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

	if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    	echo '{ "error": "invalid_request_method", "error_description": "This request is invalid. The AJAX request method must be POST." }';
    	exit;
    }

	$data = json_decode(file_get_contents('php://input'), true);

	if (empty($data['data'])) {
		echo '{ "error": "invalid_json", "error_description": "This resource requires valid JSON data. All data must be wrapped around by { \"data\": { JSON_data } }. " }';
		exit;
	}

	$_POST = $data['data'];

	if (!isset($_POST['tour_ended']) || empty($_POST['opheme_id'])) {
		echo '{ "error": "empty_request", "error_description": "This request is invalid. Both \"tour_ended\" and \"opheme_id\" must be set." }';
		exit;
	}

	include ('_local_common.php');

	$tourEnded = $db->real_escape_string($_POST['tour_ended']);
	$ophemeId = $db->real_escape_string($_POST['opheme_id']);

	$query = "update opheme_api_keys set tour_ended = '$tourEnded' where opheme_id='$ophemeId'";

    try {
        if (!$result = $db->query($query)) {
            echo '{ "error": "mysql_error", "error_description": "The MySQL query failed. Error: ' . $db->error . '" }';
        } else {
            echo '{ "success": "success" }';
        }
    } catch(Exception $e) {
        echo '{ "error": "mysql_error", "error_description": "PHP Error. Error: ' . $e->getMessage() . '" }';
    }

	include ('_local_common_end.php');

	exit;
