<?php

require_once('get_database.inc');

function createNewUser($username, $email, $password){
	$password = password_hash($password, PASSWORD_BCRYPT);
	$db = getDB();
	$q1 = "SELECT userid from users where (username='$username' or email='$username')";
	$result = $db->query($q1);
    if($result->num_rows > 0){
    	echo "User already exists.".PHP_EOL;
    	return false;
    }
    $q2 = "INSERT INTO users (username, email, password) VALUES ('$username','$email','$password')";
	$result = $db->query($q2);
	if($result){
		echo "Created new user.".PHP_EOL;
		return true;
	}
	return false;
}

function doLogin($username,$password)
{
    // lookup username in database
    // check password
    $mydb = getDB();
    /*
    if($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return false;
	}
	*/
    $query = "SELECT userid, username, password from users where (username='$username' or email='$username')";
    $result = $mydb->query($query);
    if($result->num_rows > 0){
    	if(password_verify($password, $result->fetch_row()[2])){
    		echo "Found user.".PHP_EOL;
    		return true;
    	}
    	else{
    		echo "Incorrect password.".PHP_EOL;
    		return false;
    	}
    }
    else{
    	echo "Incorrect username/email.".PHP_EOL;
    	return false;
    }
    //return false if not valid
}

?>
