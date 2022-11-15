#!/usr/bin/php
<?php

$mydb = new mysqli('localhost', 'root','Kirokoko2210!!','IT490');

if($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}

echo "Database connection successful!".PHP_EOL;



?>
