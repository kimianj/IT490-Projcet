#!/usr/bin/php
<?php
function getDB(){
	$dbc;
	$dbc = parse_ini_file("dbLogin.ini",$process_sections=true);
	$db = new mysqli('localhost', $dbc["login"]["USER"],$dbc["login"]["PASSWORD"],$dbc["login"]["DATABASE"]);
	return $db;
}
function retrievePlaylistRecents($user){
	$sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
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

$user = 1;

$playlist = retrievePlaylistRecents($user);

var_dump($playlist);

?>
