<?php

function curlClientCredentialsRequest () {
	
	global $api_location, $client_id, $client_secret;
	
	$client_request_data = [
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'client_credentials',
		'scope' => 'basic,inhouse'
	];
	$client_options = [
		'request_data' => $client_request_data,
		'request_endpoint' => '/oauth/access_token',
		'request_method' => 'POST',
		'api_location' => $api_location
	];
	
	$response = curlGenericRequest( $client_options );
	
	if (!empty($response['access_token']) && storeClientCredentials($response['access_token'])) {
		return true;
	}
	
	return false;
	
}

function storeClientCredentials ( $token ) {
	global $client_key_file, $client_key;
	$client_key = $token;
	return (bool) file_put_contents($client_key_file, '<?php $client_key = "' . $token . '";');
}

// options: [ curl_opts([opt, val],..), request_data, request_endpoint, request_method, api_location ]
function curlGenericRequest( $options ) {
	
	$curl = curl_init();
	
	$request_data = http_build_query($options['request_data']);
	
	curl_setopt_array( $curl, array(
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_POSTFIELDS => $request_data,
		//CURLOPT_HEADER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $options['api_location'] . $options['request_endpoint'],
		CURLOPT_CUSTOMREQUEST => $options['request_method']
	));
	
	if (!empty($options['curl_opts']) && is_array($options['curl_opts'])) {
		foreach ($options['curl_opts'] as $opt) {
			curl_setopt( $curl, $opt['option'], $opt['value']);
		}
	}

	foreach ( getallheaders() as $key => $value ) {
		if ( stristr($key, 'content') && ( stristr($key, 'type') || stristr($key, 'length') ) ) continue;
		$headers[] = $key . ': ' . $value;
	}
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = 'Content-Length: ' . strlen($request_data);
	if (!empty($options['curl_headers']) && is_array($options['curl_headers'])) {
		foreach ($options['curl_headers'] as $key => $value) {
			foreach ($headers as $index => $v) {
				if (stristr($v, $key)) {
					unset($headers[$index]);
				}
			}
			$header = $key . ': ' . $value;
			$headers[] = $header;
		}
	}
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

	$result = curl_exec($curl);
	curl_close($curl);

	if(!$result) {
		$result = '{ "error": "curl_error", "error_description": "Curl error: ' . curl_error($curl) . '. Error code: ' . curl_errno($curl) . '." }';
	}

	return objectToArray(json_decode($result));
	
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}