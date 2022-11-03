#!/usr/bin/php
<?php
require_once('secret.inc');
require_once('getHostInfo.inc');
require_once('path.inc');
require_once('rabbitMQLib.inc');

    function Auth() {
    $reURL = '172.24.227.167/homepage';
    $scope = 'user-read-private user-read-email';
    $resTyp = 'code';

        $data = array (
            'client_id' => $client_id,
            'reURL' => $reURL,
            'scope' => $scope,
            'resTyp' => $resTyp
        );

        $authLink = 'https://accounts.spotify.com/authorize?' . http_build_query( $data );
        return $authLink;
    }
    function getAuthCode() {
        <?php if(isset($_GET['code']) && isset($_SESSION['spotAcc'] ) ) {
            $data = array(
                'reURL' => $reURL,
                'gType' => 'authCode',
                'code' => $_GET['code'],
            );

            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_POST, 1 );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Basic ' . base64_encode( $client_id . ':' . $client_secret ) ) );

            $response = curl_exec($curl);
            curl_close( $curl );
            return $response;

            if ( isset( $response->accToken ) ) {
                header('Location: '. $response -> accToken)
            }
        }
    }

    function getUserData() {
        <?php if ( isset( $_GET['access'] ) ) {
            $curl = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.spotify.com/v1/me' );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json', 'Authorization: Bearer ' . $_GET['access'] ) );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            $profile = curl_exec($curl)
            curl_close($curl);

            echo '<pre>';
            var_dump( $profile );
            echo '</pre>';

            //figure out what needs to be returned
        }
    }

   function getUserPlaylist ($client) {
        if ($profile -> id) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, 'https://api.spotify.com/v1/users/' . $profile -> id . '/playlists');
            curl_setopt( $curl, CURLOPT_HTTPGET, 1 );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $_GET['access'] ) );

            $playlists = curl_exec($curl);
            curl_close($curl);

            echo '<pre>';
            var_dump( $playlists );
            echo '</pre>';

            //figure out what needs to be returned
        }
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
      return Auth();
      break;
    case "authCode":
        return getAuthCode();
        break;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","D");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
