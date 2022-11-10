<?php

require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');
require_once('getDatabase.inc');

function getUserFromSession($session){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return 0;
	}
	$q1 = "SELECT userid from users where sessionid='$session'";
	$result = $db->query($q1);
    if($result->num_rows == 0){
    	echo "Invalid session ID.".PHP_EOL;
    	return 0;
    }
    $res = $result->fetch_row();
    return $res[0];
}

function addToPlaylist($session, $songID){
	$user = getUserFromSession($session);
	if($user == 0){
		return false;
	}
    $q1 = "insert into playlists (userid, songid) values ('$user', '$songid')";
    $result = $db->query($q1);
	if($result){
		echo "Added to playlist successfully.".PHP_EOL;
		return true;
	}
	return false;
}

function retrievePlaylist($session){
	$user = getUserFromSession($session);
	if($user == 0){
		return false;
	}
	$q1 = "select songid from playlists where userid='$user'";
    $result = $db->query($q1);
	if($result){
		$songs = [];
		$j = $result->num_rows;
		for($i = 0; $i < $j; $i++){
			$songs[$i] = $result->fetch_row()[0];
		}
		return $songs;
	}
	return false;
	
	
}

?>
