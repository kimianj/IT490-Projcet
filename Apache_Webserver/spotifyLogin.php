#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$client2 = new rabbitMQClient("testRabbitMQ.ini","dmzExchange");

if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request2 = array();
$request2['type'] = "authLink";
$request2['message'] = $msg;
$response = $client2->send_request($request2);

$authLink = $response['authLink'];

if($authLink != null){
	header("Location: ".$authLink);
	exit();
}

else{
	echo "The message is empty";
}


try {
    $oauth = new OAuth("clientId","clientSecret",OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
    $oauth->setToken("access_token","access_token_secret");

    $oauth->fetch("http://172.24.227.167/spotifyLogin.php");
    //fectch from the homepage

    $response_info = $oauth->getLastResponseInfo();
    header("Content-Type: {$response_info["content_type"]}");
    echo $oauth->getLastResponse();
} catch(OAuthException $E) {
    echo "Exception caught!\n";
    echo "Response: ". $E->lastResponse . "\n";
}



	

?>
