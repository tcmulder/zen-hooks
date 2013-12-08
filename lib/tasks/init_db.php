<?php
function create_db($db_creds){
	// connect to mysql
	$link = mysql_connect('localhost', 'admin', 'iUu5xkAt/v8=');
	if($link) {
		// create the database
		$db = mysql_select_db($db_creds['name'], $link);

		// if the database doesn't exist already
		if (!$db) {
			mysql_query('CREATE DATABASE IF NOT EXISTS ' . $db_creds['name'], $link);
			mysql_query('GRANT USAGE ON *.* TO '. $db_creds['user'] . '@localhost IDENTIFIED BY \'' . $db_creds['pass'] . '\'', $link);
			mysql_query('GRANT ALL PRIVILEGES ON ' . $db_creds['name'] . '.* TO ' . $db_creds['user'] . '@localhost', $link);
			mysql_query("FLUSH PRIVILEGES", $link);
		}
	}
	mysql_close($link);
	print_r($db_creds);
}