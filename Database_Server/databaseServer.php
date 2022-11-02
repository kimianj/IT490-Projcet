#!/usr/bin/php
<?php
require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');
require_once('getDatabase.inc');
require_once('login-register.inc');
/*
function getDB(){
	$dbc;
	$dbc = parse_ini_file("dbLogin.ini",$process_sections=true);
	$db = new mysqli('localhost', $dbc["login"]["USER"],$dbc["login"]["PASSWORD"],$dbc["login"]["DATABASE"]);
	return $db;
}
function createNewUser($username, $email, $password){
	$password = password_hash($password, PASSWORD_BCRYPT);
	$db = getDB();
	$q1 = "SELECT userid from users where (username='$username' or email='$username')";
	$result = $db->query($q1);
    if($result->num_rows > 0){
    	echo "User already exists.".PHP_EOL;
    	return false;
    }
    $q2 = "INSERT INTO users (username, email, password) VALUES ('$username','$email','$password')";
	$result = $db->query($q2);
	if($result){
		echo "Created new user.".PHP_EOL;
		return true;
	}
	return false;
}

function doLogin($username,$password)
{
    // lookup username in database
    // check password
    $mydb = getDB();
    $query = "SELECT userid, username, password from users where (username='$username' or email='$username')";
    $result = $mydb->query($query);
    if($result->num_rows > 0){
    	if(password_verify($password, $result->fetch_row()[2])){
    		echo "Found user.".PHP_EOL;
    		return true;
    	}
    	else{
    		echo "Incorrect password.".PHP_EOL;
    		return false;
    	}
    }
    else{
    	echo "Incorrect username/email.".PHP_EOL;
    	return false;
    }
    //return false if not valid
}*/
function makeNewSession($uid){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return 0;
	}
	$newID = false;
	$ID = 0;
	while(!$newID){
		$ID = rand(1,getrandmax());
		$q1 = "SELECT sessionid from sessions where sessionid='$ID'";
		$result = $sdb->query($q1);
    	if($result->num_rows == 0){
    		$newID = true;
    	}
	}
	$q2 = "INSERT INTO sessions (sessionid, userid) VALUES ('$ID', '$uid')";
	$result = $sdb->query($q2);
	if($result)
		return $ID;
	else
		return 0;
}
function makeNewSession_Spotify($email){
	$db = getDB();
	if($db->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return 0;
	}
	$q1 = "SELECT userid from users where (email='$email')";
	$result = $db->query($q1);
	if($result->num_rows > 0){
		return makeNewSession($result->fetch_row()[0]);
	}
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "Login":
    	echo "Searching for user...".PHP_EOL;
      $u = doLogin($request['username'],$request['password']);
      if ($u != 0){
      		$ID = makeNewSession($u);
      		if($ID == 0){
      			return array("returnCode" => '3', 'message'=>"Something went wrong with inserting the Session ID.");
      		}
      		echo "Successful login".PHP_EOL;
      		$a = array("returnCode" => '1', 'message'=>"Correct credentials.", "sessionId" => $ID);
      		var_dump($a);
      		return array("returnCode" => '1', 'message'=>"Correct credentials.", "sessionId" => $ID);
      }
      if ($u == 0){
      		return array("returnCode" => '2', 'message'=>"Incorrect credentials.");
      }
    case "validate_session":
      return validateSession($request['sessionId']);
    case "Spotify login":
    	$email = $request['email'];
    	$token = $request['token'];
    	$u = updateUser_Spotify($email, $token);
    	if ($u != 0){
      		return array("returnCode" => '200', 'message'=>"All good!", "email" => $email);
      	}
      	return array("returnCode" => '3', 'message'=>"Something went wrong.");
  }
  //return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","Q");

$server->process_requests('requestProcessor');
exit();
?>

