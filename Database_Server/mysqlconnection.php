#!/usr/bin/php
<?php

$mydb = new mysqli('172.24.92.156', 'testUser','12345','db490');

if($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}



?>
