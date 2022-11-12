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
	$result = $sdb->query($q1);
    if($result->num_rows == 0){
    	echo "Invalid session ID.".PHP_EOL;
    	return 0;
    }
    $res = $result->fetch_row();
    return $res[0];
}

function addToPlaylist($session, $songid){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	$user = getUserFromSession($session);
	if($user == 0){
		return false;
	}
	
	$q1 = "select songid from playlists where userid='$user' and songid='$songid'";
    $result = $sdb->query($q1);
    
    if($result->num_rows > 0){
    	echo "Song is already in playlist for this user.".PHP_EOL;
    	return false;
    }
    
    $q2 = "insert into playlists (userid, songid) values ('$user', '$songid')";
    $result = $sdb->query($q2);
	if($result){
		echo "Added to playlist successfully.".PHP_EOL;
		return true;
	}
	echo "Could not add to playlist.".PHP_EOL;
	return false;
}

function retrievePlaylist($session){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	$user = getUserFromSession($session);
	if($user == 0){
		return false;
	}
	$q1 = "select songid from playlists where userid='$user'";
    $result = $sdb->query($q1);
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

function retrievePlaylistRecents($session){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	$user = getUserFromSession($session);
	if($user == 0){
		return false;
	}
	$q1 = "select songid, time_added from playlists where userid='$user' order by time_added desc limit 3";
    $result = $sdb->query($q1);
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

function registerSongs($songs){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	foreach($songs as $song){
		$id = $song['id'];
		$title = $song['title'];
		$artistid = $song['artistid'];
		$artistname = $song['artistname'];
		$art = $song['art300'];
		$q1 = "select songid from songinfo where songid='$id'";
    		$result = $sdb->query($q1);
    		if($result->num_rows > 0) {continue;}
    		$q2 = "insert into songinfo (songid, songname, artistid, artistname, artlink300) values ('$id','$title','$artistid','$artistname','$art')";
    		$result = $sdb->query($q2);
	}
	return true;
}

function getSongsByID($ids){
	
}

?>
