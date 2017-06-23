<?php

	$whitelist = ['hootsuite', 'opheme'];

	foreach ($whitelist as $allowed) {
		if (stristr($_SERVER['SERVER_NAME'], $allowed) === false) {
			echo '{ "error": "unauthorised_request", "error_description": "This request does not originate from a trusted source." }';
			exit;
		}
	}

	if (stristr(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH'), 'XMLHttpRequest') === false) {
		echo '{ "error": "no_ajax", "error_description": "AJAX must be used to access this resource." }';
		exit;
	}

	if (stristr(filter_input(INPUT_SERVER, 'CONTENT_TYPE'), 'application/json') === false) {
		echo '{ "error": "no_json", "error_description": "This resource requires JSON data." }';
		exit;
	}

	$data = json_decode(file_get_contents('php://input'), true);

	if (empty($data['data'])) {
		echo '{ "error": "invalid_json", "error_description": "This resource requires valid JSON data. All data must be wrapped around by { \"data\": { JSON_data } }. " }';
		exit;
	}

	if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        echo '{ "error": "invalid_request_method", "error_description": "This request is invalid. The AJAX request method must be POST." }';
        exit;
    }

	$_POST = $data['data'];

	if (empty($_POST['api_key']) || empty($_POST['hootsuite_id'])) {
		echo '{ "error": "empty_request", "error_description": "This request is invalid. Both \"api_key\" and \"hootsuite_id\" must be set and have a value/length higher than 0." }';
		exit;
	}

	include ('_local_common.php');

	$apiKey = $db->real_escape_string(mc_decrypt($_POST['api_key'], $cryptSecret));
	$hootsuiteId = $db->real_escape_string($_POST['hootsuite_id']);

	$query = "delete from opheme_api_keys where api_key='$apiKey' and hootsuite_id=$hootsuiteId";

	try {
		if ($result = $db->query($query)) {
			echo '{ "success": "success" }';
		} else {
			echo '{ "error": "mysql_error", "error_description": "The MySQL query failed. Error: ' . $db->error . '" }';
		}
	} catch(Exception $e) {
		echo '{ "error": "mysql_error", "error_description": "MySQL Error. Error: ' . $e->getMessage() . '" }';
	}

	include ('_local_common_end.php');

	exit;
