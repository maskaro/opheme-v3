<?php

if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') !== 'XMLHttpRequest') {
	echo '{ "error": "no_ajax", "error_description": "AJAX must be used to access this resource." }';
	exit;
}

if (empty($_POST['request_method']) || empty($_POST['request_apipath'])) {
	echo '{ "error": "empty_request", "error_description": "This request is invalid. Both \"request_method\" and \"request_apipath\" must be set." }';
	exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
	echo '{ "error": "invalid_request", "error_description": "This request is invalid. The AJAX request method must be POST." }';
	exit;
}

// include all sorts of goodies
require 'api_client_settings.php';
require 'api_functions.php';

// by default the request will come on behalf of the user
$client_scope = false;

// request options
$request_method = strtoupper($_POST['request_method']); // GET, POST, ...
$request_endpoint = htmlentities(htmlspecialchars($_POST['request_apipath'])); // /users/1 ..
unset($_POST['request_method'], $_POST['request_apipath']); // get rid of those from the post data

$request_data = [];

// needed for LOGIN/REGISTER/REFRESH_{CLIENT,APPLICATION}TOKEN
if (!empty($_POST['client_credentials']) && $_POST['client_credentials'] === 'yes') {
	$request_data['client_id'] = $client_id;
	$request_data['client_secret'] = $client_secret;
	unset($_POST['client_credentials']);
}

$options = []; // options for curl request

// request on behalf of client/application
if (!empty($_POST['client_scope']) && $_POST['client_scope'] === 'yes') {
	$client_scope = true; unset($_POST['client_scope']);
	include $client_key_file;
	$options['curl_headers'] = [
		'Authorization' => $client_key
	];
}

$request_data = array_merge($request_data, $_POST); // save the remaining post data for sending to the api

switch ($request_method) {
	case 'GET':
	case 'POST':
		$options['curl_opts'][] = [ 'option' => CURLOPT_POST, 'value' => true ];
	case 'PUT':
	case 'PATCH':
	case 'DELETE':
		$options = array_merge($options, array(
			'request_data' => $request_data,
			'request_endpoint' => $request_endpoint,
			'request_method' => $request_method,
			'api_location' => $api_location
		));
		$response = curlGenericRequest( $options );
		
		// if within client scope, check for error requiring new client key
		if ($client_scope && !empty($response['error']) && in_array($response['error'], [ 'access_denied', 'invalid_request' ]) ) {
			if (curlClientCredentialsRequest()) {
				$options['curl_headers'] = [
					'Authorization' => $client_key
				];
				// re-run the initial request in an attempt to get the data
				$response = curlGenericRequest( $options );
			}
		}
		
		// final response from server
		header('Content-Type: application/json');
		echo json_encode($response);
	
		break;
	default:
		echo '{ "error": "unknown_request_method", "error_description": "Unknown request method. Accepted: GET, POST, PUT, PATCH, DELETE." }';
}

exit;