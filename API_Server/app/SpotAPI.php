#!/usr/bin/php
<?php
require_once('secret.php');
require_once('getHostInfo.inc');
require_once('path.inc');
require_once('rabbitMQLib.inc');

class Spotify {

    function Auth () {
    $reURL = '172.24.227.167/homepage'

        $data = array (
            'client_id' => $client_id
            'reURL' => $reURL
            'scope' => $scope
            'resTyp' => $resTyp
        );

        $authLink = 'https://accounts.spotify.com/authorize?' . http_build_query( $data );
    }

 /*   function getUserPlaylist ($client) {
    $urlUP = 'https://api.spotify.com/v1/users/'.client_id'/playlists'
    $scope = 'user-read-private user-read-email'
    $resTyp = 'code'

        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $urlUP);
        curl_setopt ($curl, CURLOPT_HEADER, )
  --header 'Authorization: ' \
  --header 'Content-Type: application/json'
    }

curl --request GET \
  --url https://api.spotify.com/v1/users/user_id/playlists \
  --header 'Authorization: ' \
  --header 'Content-Type: application/json' */
}

function requestProcessor($request) {
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "authLink":
      return Auth;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","D");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();