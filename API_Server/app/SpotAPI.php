<?php
class Spotify {
    $client_id = ' 326304fb776249b09066a335fa56c207';
    $client_secret = '43a6a1810dd548d386417a57a8b32afd';
    $urlUP = 'https://api.spotify.com/v1/users/'.client_id'/playlists'
    $reURL = '172.24.227.167/homepage'
    $scope = 'user-read-private user-read-email'
    $resTyp = 'code'

    function Auth {
        $data = array (
            'client_id' => $client_id
            'reURL' => $reURL
            'scope' => $scope
            'resTyp' => $resTyp
        );

        $nURL = 'https://accounts.spotify.com/authorize?' . http_build_query( $data );
    }

<!--     function getUserPlaylist ($client) { -->
<!--         $curl = curl_init(); -->
<!--         curl_setopt ($curl, CURLOPT_URL, $urlUP); -->
<!--         curl_setopt ($curl, CURLOPT_HEADER, ) -->
<!--   --header 'Authorization: ' \ -->
<!--   --header 'Content-Type: application/json' -->
<!--     } -->

<!-- curl --request GET \ -->
<!--   --url https://api.spotify.com/v1/users/user_id/playlists \ -->
<!--   --header 'Authorization: ' \ -->
<!--   --header 'Content-Type: application/json' -->