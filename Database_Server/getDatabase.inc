<?php

require_once('path.inc');
require_once('getHostInfo.inc');
require_once('rabbitMQLib.inc');
function getDB(){
	$dbc;
	$dbc = parse_ini_file("dbLogin.ini",$process_sections=true);
	$db = new mysqli('localhost', $dbc["login"]["USER"],$dbc["login"]["PASSWORD"],$dbc["login"]["DATABASE"]);
	return $db;
}

?>
