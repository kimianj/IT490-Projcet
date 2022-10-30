#!/usr/bin/php
<?php
require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    // lookup username in database
    // check password
    $mydb = new mysqli('localhost', 'testUser','12345','db490');
    /*
    if($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	*/
    $query = "SELECT userid, username from users where username='$username' and password='$password'";
    $result = $mydb->query($query);
    if($result->num_rows > 0){
    	echo "Found user".PHP_EOL;
    	return true;
    }
    else{
    	echo "Could not find user".PHP_EOL;
    	return false;
    }
    //return false if not valid
}
function makeNewSession(){
	$sdb = new mysqli('localhost', 'testUser','12345','db490');
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
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
	$q2 = "INSERT INTO sessions (sessionid) VALUES ('$ID')";
	$result = $sdb->query($q2);
	if($result)
		return $ID;
	else
		return 0;
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
      $success = doLogin($request['username'],$request['password']);
      if ($success == true){
      		$ID = makeNewSession();
      		if($ID == 0){
      			return array("returnCode" => '3', 'message'=>"Something went wrong with inserting the Session ID.");
      		}
      		return array("returnCode" => '1', 'message'=>"Correct credentials.", "sessionId" => $ID);
      }
      if ($success == false){
      		return array("returnCode" => '2', 'message'=>"Incorrect credentials.");
      }
    case "validate_session":
      return validateSession($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","Q");

$server->process_requests('requestProcessor');
exit();
?>

