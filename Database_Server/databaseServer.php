#!/usr/bin/php
<?php
require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');
require_once('getDatabase.inc');
require_once('login-register.inc');
require_once('spotifyFunctions.inc');


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
    /*
    case "Spotify login":
    	$email = $request['email'];
    	$token = $request['token'];
    	$u = updateUser_Spotify($email, $token);
    	if ($u != 0){
      		return array("returnCode" => '200', 'message'=>"All good!", "email" => $email);
      	}
      	return array("returnCode" => '3', 'message'=>"Something went wrong.");
	*/
	case "playlist_add_song":
		$r = addToPlaylist($request['sessionId'], $request['songId']);
		if($r){
			return array("returnCode" => '1', 'message'=>"Song added to Playlist.");
		}
		return array("returnCode" => '2', 'message'=>"Uh oh, something went wrong.");
	case "playlist_retrieve":
		$s = retrievePlaylist($request['sessionId']);
		if($s){
			return array("returnCode" => '1', 'songs' => $s);
		}
		return array("returnCode" => '2', 'message'=>"Uh oh, something went wrong.");
	case "songRegister":
		registerSongs($request['songs']);
		return array("returnCode" => '1');
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","Q");

$server->process_requests('requestProcessor');
exit();
?>

