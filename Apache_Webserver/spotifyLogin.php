<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","speak");
$client2 = new rabbitMQClient("testRabbitMQ.ini","DMZ");

if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "Login"; //change hard coded variables to text box inputs (username/password)
$username = $_POST['username'];
$password= $_POST['password'];
$request['username'] = $username;
$request['password'] = $password;
$request['message'] = $msg;
$response = $client->send_request($request);

$request2 = array();
$request2['type'] = "authLink";

//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
print_r($response);
//see if you can grab the responce array
$returnCode = $response["returnCode"];
//echo $returnCode;
$authLink = $responce2['authLink']

switch($returnCode){
	case 1:
		$sessionId = $response["sessionId"];
		echo "<script>
		sessionStorage.setItem('id',", $sessionId, ");
        	let info = sessionStorage.getItem(id);
        	</script>";

		break;
case 2:
		echo "Incorrect Username or Password";
			header("Location: /index.html");
			exit();
		break;
	default:
		echo "Something went wrong";
}



try {
    $oauth = new OAuth("clientId","clientSecret",OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
    $oauth->setToken("access_token","access_token_secret");

    $oauth->fetch("http://http://172.24.227.167/homepage.html");

    $response_info = $oauth->getLastResponseInfo();
    header("Content-Type: {$response_info["content_type"]}");
    echo $oauth->getLastResponse();
} catch(OAuthException $E) {
    echo "Exception caught!\n";
    echo "Response: ". $E->lastResponse . "\n";
}
	

echo "\n\n";

echo $argv[0]." END".PHP_EOL;
?>
