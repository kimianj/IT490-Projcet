#!/usr/bin/php
<?php
function getDB(){
	$dbc;
	$dbc = parse_ini_file("dbLogin.ini",$process_sections=true);
	$db = new mysqli('localhost', $dbc["login"]["USER"],$dbc["login"]["PASSWORD"],$dbc["login"]["DATABASE"]);
	return $db;
}
function addOneShotToPlaylist($user, $songid){
	if($user == 0){
		return false;
	}
    $sdb = getDB();
	if($sdb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
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

$song = "6HzTK8mwweVdDRlR9nx09K";
$user = 1;

addOneShotToPlaylist($user, $song);

?>
