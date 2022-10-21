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
    if($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
    $query = "SELECT userid, username from users where username='$username' and password='$password'";
    $result = $mydb->query($query);
    if($result->num_rows > 0){
    	return true;
    }
    else{
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
    	$foundUser = doLogin($request['username'],$request['password']);
    	if($foundUser){
    		sendLoginConfirmation();
    	}
    	else{
    		sendLoginDeclination();
    	}
      	return $foundUser;
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

function sendLoginConfirmation(){
    $client = new rabbitMQClient("testRabbitMQ.ini", "speak");
    $msg = "test message";

    $request = array();
    $request['type'] = "validate_session";
    $request['message'] = $msg;
    $response = $client->send_request($request);
    //$response = $client->publish($request);

    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
}
function sendLoginDeclination(){
    $client = new rabbitMQClient("testRabbitMQ.ini", "speak");
    $msg = "test message";

    $request = array();
    $request['type'] = "invalid_session";
    $request['message'] = $msg;
    $response = $client->send_request($request);
    //$response = $client->publish($request);

    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
}

$server = new rabbitMQServer("testRabbitMQ.ini","listen");

$server->process_requests('requestProcessor');
exit();
?>

