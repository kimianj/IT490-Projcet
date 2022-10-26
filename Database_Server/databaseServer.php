#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
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
      		return array("returnCode" => '1', 'message'=>"Correct credentials.");
      }
      if ($success == false){
      		return array("returnCode" => '2', 'message'=>"Incorrect credentials.");
      }
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","Q");

$server->process_requests('requestProcessor');
exit();
?>

