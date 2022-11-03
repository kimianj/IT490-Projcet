<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("testRabbitMQ.ini","speak");

$username = $_POST['username'];
$password= $_POST['password'];

$request = array();
$request['type'] = "Login"; //change hard coded variables to text box inputs (username/password)

$request['username'] = $username;
$request['password'] = $password;
$request['message'] = $msg;

$response = $client->send_request($request);



//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
print_r($response);
//see if you can grab the responce array
$returnCode = $response["returnCode"];
//echo $returnCode;

switch($returnCode){
	case 1:
		$sessionId = $response["sessionId"];
		echo "<script>
		sessionStorage.setItem('id',", $sessionId, ");
        	let info = sessionStorage.getItem(id);
        	</script>";
        	header("Location: /homepage.html");
			exit();

		break;
	case 2:
		echo "Incorrect Username or Password";
			header("Location: /index.html");
			exit();
		break;
	default:
		echo "Something went wrong";
}


echo "\n\n";

echo $argv[0]." END".PHP_EOL;
?>
