<?php

	include 'api_client_settings.php';
	include 'api_functions.php';

	try {

		$m = new Memcache();
		$m->pconnect('localhost', 11211);

		$db = mysqli_connect('localhost', 'hootsuite', 'hootsuite135!', 'hootsuite');

		if ($db->connect_errno) {
			die('Failed to connect to MySQL. Error: ' . $db->connect_errno);
		}

	} catch (Exception $e) {
		
		echo '{ "error": "service_connection_error", "error_description": "PHP Error. Error: ' . $e->getMessage() . '" }';
		exit;
		
	}
