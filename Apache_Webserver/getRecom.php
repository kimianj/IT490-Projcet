<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$query = $_POST['query']; //make sure this is extablshed as POST request in HTML

$client = new rabbitMQClient("testRabbitMQ.ini, "dmzExchange");

$request = array();
$request['type'] = "recomendations";
$request['query'] = $query;

$responce = $client->send_request($request);
$recomendations = $response["results"];

header("http.www.changeme.exe"); //change this to search result site
echo $recomendations; //update this to organize data in pretty format, perhaps HTML side?
exit();
?>
