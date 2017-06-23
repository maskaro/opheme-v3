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

    $_POST = $data['data'];

    if (empty($_POST['opheme_id'])) {
        echo '{ "error": "empty_request", "error_description": "This request is invalid. \"opheme_id\" must be set." }';
        exit;
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        echo '{ "error": "invalid_request_method", "error_description": "This request is invalid. The AJAX request method must be POST." }';
        exit;
    }

	include ('_local_common.php');

	$opheme_id = $_POST['opheme_id'];

	$query = "select tour_ended from opheme_api_keys where opheme_id = '$opheme_id'";
	$querykey = 'KEY' . md5($query);

	$tour_ended = 0;

	if (!$result = $m->get($querykey)) {

		try {
		
			if ($result = $db->query($query)) {

				$res = $result->fetch_assoc();

				if ($res["tour_ended"]) {

					$tour_ended = $res["tour_ended"];

				}

			}

		} catch(Exception $e) {

            echo '{ "error": "mysql_error", "error_description": "PHP Error. Error: ' . $e->getMessage() . '" }';

		}
		
	} else {
		
		$tour_ended = $result;
		
	}

	echo '{ "tour_ended": "' . $tour_ended . '" }';

	include ('_local_common_end.php');
