<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("testRabbitMQ.ini","dmzExchange");

$searchQuery = $_POST['searching'];

$request = array();
$request['type'] = "search";

$request['title'] = $searchArray;

$response = $client->send_request($request);



//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
print_r($response);
//see if you can grab the responce array
$returnCode = $response["returnCode"];
//echo $returnCode;

switch($returnCode){
	case 1:
		$resultCodes = $response["songs"];
		echo "<script>
		sessionStorage.setItem('results',", $resultCodes, ");
        	let results = sessionStorage.getItem(results);
        	</script>";
        	header("Location: /search.html");
			exit();

		break;
	case 2:
		echo "Something went wrong.";
			exit();
		break;
	default:
		echo "Something went wrong";
}


echo "\n\n";

echo $argv[0]." END".PHP_EOL;
?>
