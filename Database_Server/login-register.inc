<?php

require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');
require_once('getDatabase.inc');

function createNewUser($username, $email, $password){
	$password = password_hash($password, PASSWORD_BCRYPT);
	$db = getDB();
	if($db->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	$q1 = "SELECT userid from users where (username='$username' or email='$username')";
	$result = $db->query($q1);
    if($result->num_rows > 0){
    	echo "User already exists.".PHP_EOL;
    	return false;
    }
    $q2 = "INSERT INTO users (username, email, password, hasSpotify) VALUES ('$username','$email','$password',0)";
	$result = $db->query($q2);
	if($result){
		echo "Created new user.".PHP_EOL;
		return true;
	}
	return false;
}

function updateUser_Spotify($email, $token){
	$db = getDB();
	if($db->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return 0;
	}
	$q1 = "SELECT userid from users where (email='$email')";
	$result = $db->query($q1);
	$result2;
    if($result->num_rows > 0){
    	$q2 = "update users set hasSpotify = 1, token = '$token' where email = '$email'";
    	$result2 = $db->query($q2);
    }
    else{
    	$q2 = "insert into users (email, hasSpotify, token) values ('$email', 1, '$token')";
    	$result2 = $db->query($q2);
    }
	if($result2){
		$result3 = $db->query($q1); 
		if($result3){
			return $result3->fetch_row()[0];
		}
	}
	return 0;
}

function doLogin($username,$password)
{
    // lookup username in database
    // check password
    $mydb = getDB();
    if($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return 0;
	}
    $query = "SELECT userid, username, password from users where (username='$username' or email='$username')";
    $result = $mydb->query($query);
    if($result->num_rows > 0){
    	if(password_verify($password, $result->fetch_row()[2])){
    		echo "Found user ".$result->fetch_row()[0].".".PHP_EOL;
    		return $result->fetch_row()[0];
    	}
    	else{
    		echo "Incorrect password.".PHP_EOL;
    		return 0;
    	}
    }
    else{
    	echo "Incorrect username/email.".PHP_EOL;
    	return 0;
    }
    //return false if not valid
}

?>
