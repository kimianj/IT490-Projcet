//Conecting to the database
<?php
   define('DB_SERVER', 'localhos');
   define('DB_USERNAME', 'username');
   define('DB_PASSWORD', 'password');
   define('DB_DATABASE', 'database');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?> 

// making sure the creditals are correct  (ex.login.php page)
<?php
//Start a new session
session_start();
//Check the session start time is set or not
if(!isset($_SESSION['start']))
{
    //give the timer
    $_SESSION['start'] = time();
}
//check the session expiration
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) { // 1800 is for 1 hour --> can be changed later 
   
    session_unset();   
    session_destroy();  
}
//timestamp
$_SESSION['LAST_ACTIVITY'] = time(); 
?>


// log_out page manually before the session ends
<?php
   session_start();
   
   if(session_destroy()) {
      header("Location: login.php");
   }
?>
