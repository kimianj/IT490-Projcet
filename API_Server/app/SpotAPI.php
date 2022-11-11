#!/usr/bin/php
<?php
require_once('secret.inc');
require_once('getHostInfo.inc');
require_once('path.inc');
require_once('rabbitMQLib.inc');

    function Auth() {
    $reURL = 'http://172.24.147.251/homePage.html';
    $scope = 'user-read-private user-read-email';
    $resTyp = 'code';

        $data = array (
            'client_id' => getClientID(),
            'redirect_uri' => $reURL,
            'scope' => $scope,
            'response_type' => $resTyp
        );

        $authLink = 'https://accounts.spotify.com/authorize?' . http_build_query( $data );
        echo $authLink;
        return array("authLink" => $authLink);
    }
    function getAccTok() {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_POST, 1 );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Basic ' . base64_encode( $client_id . ':' . $client_secret ) ) );

            $response = curl_exec($curl);
            curl_close( $curl );
            return array("response" => $response);

            if ( isset( $response->accToken ) ) {
                header('Location: '. $response -> accToken);
            }
    }

    function search($title) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.spotify.com/v1/search?q='.urlencode($title).'&type=track');
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json', 'Authorization: Bearer ' . $_GET['access'] ) );

         $response = curl_exec($curl);
         curl_close( $curl );
         return array("response" => $response);
    }

    function getRecomendation($tracks) {
    	$trackList = "";
    	for ($i = 0; $i<count($tracks); $i+=1) {
    		if (i > 0) {
    			$trackList = $trackList.","; 
    		}
    		$trackList = $trackList.$tracks[$i]; 
    	}
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.spotify.com/v1/recommendations?seed_tracks='.urlencode($trackList).'&market=US');
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json', 'Authorization: Bearer ' . $_GET['access'] ) );

        $response = curl_exec($curl);
        curl_close( $curl );
        return array("response" => $response);
    }
    
    function artistEvents($artist) {

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://concerts-artists-events-tracker.p.rapidapi.com/artist?name=".urlencode($artist),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"X-RapidAPI-Host: concerts-artists-events-tracker.p.rapidapi.com",
			"X-RapidAPI-Key: 413f8728ffmsh3d4c4e519c045abp10714bjsn818deccfc438"
		],
	]);

	$response = curl_exec($curl);
	curl_close($curl);
	return array("response" => $response);
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
    case "AccTok":
        return getAccTok();
        break;
    case "search":
        return search($request["title"]);
        break;
    case "recommendations":
        return getRecomendation($request["tracks"]);
    case "events": 
    	return artistEvents($request["name"]); 
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","D");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
