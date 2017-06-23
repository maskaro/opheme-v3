<?php

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

function curlClientCredentialsRequest () {
	
	global $api_location, $client_id, $client_secret;
	
	$client_request_data = [
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'client_credentials',
		'scope' => 'basic,inhouse'
	];
	$headers = [
		'Content-Type' => 'application/x-www-form-urlencoded'
	];
	$client_options = [
		'request_data' => http_build_query($client_request_data),
		'request_endpoint' => '/oauth/access_token',
		'request_method' => 'POST',
		'curl_headers' => $headers,
		'api_location' => $api_location
	];
	
	$response = curlGenericRequest( $client_options );
	
	if (!empty($response['access_token']) && storeClientCredentials($response['access_token'])) {
		return true;
	} else {
	    logAPIRequest($client_options, $headers, $response);
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
	
	curl_setopt_array( $curl, array(
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_POSTFIELDS => $options['request_data'],
		CURLOPT_SSL_VERIFYPEER => false,
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
		if ( stristr($key, 'host') || stristr($key, 'content') && ( stristr($key, 'type') || stristr($key, 'length') ) ) continue;
		// encrypt key
		if ( stristr($key, 'authorization') ) {
			global $cryptSecret;
			$value = mc_decrypt($value, $cryptSecret);
		}
		$headers[] = $key . ': ' . $value;
	}
	$headers[] = 'Content-Length: ' . strlen($options['request_data']);
	global $api_host;
	$headers[] = 'Host: ' . $api_host;
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
	
	if (DEBUG) logAPIRequest($options, $headers, $result);

	if(!$result && curl_errno($curl) > 0) {
		$result = '{ "error": "curl_error", "error_description": "Curl error: ' . curl_error($curl) . '. Error code: ' . curl_errno($curl) . '." }';
		if (!DEBUG) logAPIRequest($options, $headers, $result);
	}
	
	curl_close($curl);

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

function parseBoolsAndNumbers ($obj) {
	if (is_object($obj)) {
		$obj = objectToArray($obj);
	}
	if (is_array($obj)) {
		foreach ($obj as $key => $value) {
			$obj[$key] = ($key === 'phone')?$value:call_user_func(__FUNCTION__, $value);
		}
		return $obj;
	} else {
		if (is_numeric($obj)) return filter_var($obj, FILTER_VALIDATE_INT);
		if (isBool($obj)) return filter_var($obj, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		return $obj;
	}
	return null;
}

function isBool($string) {
    $string = strtolower($string);
    return (in_array($string, array('true', 'false')));
}

/**
 * Write API request to file.
 * @param array $options  Request array.
 * @param string $response Response string.
 */
function logAPIRequest($options, $headers, $response) {
	
	$content = 'Date and Time: ' . date('m/d/Y h:i:s a', time()) . PHP_EOL;
	$content .= 'Options: ' . print_r($options, true);
	$content .= 'Headers: ' . print_r($headers, true);
	$content .= 'Response: ' . $response . PHP_EOL;
	$content .= PHP_EOL;
	
	file_put_contents_force('../api_requests.log', $content, FILE_APPEND | LOCK_EX);
	
}

function file_put_contents_force() {
	$args = func_get_args();
	$path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $args[0]);
	$parts = explode(DIRECTORY_SEPARATOR, $path);
	array_pop($parts);
	$directory =  '';
	foreach($parts as $part):
		$check_path = $directory.$part;
			if( is_dir($check_path.DIRECTORY_SEPARATOR) === FALSE) {
				mkdir($check_path, 0755);
			}
			$directory = $check_path.DIRECTORY_SEPARATOR;
	endforeach;     
	call_user_func_array('file_put_contents',$args);
}

// http://www.warpconduit.net/2013/04/14/highly-secure-data-encryption-decryption-made-easy-with-php-mcrypt-rijndael-256-and-cbc/
$cryptFn = MCRYPT_BLOWFISH; // MCRYPT_RIJNDAEL_256
// Encrypt Function
function mc_encrypt($encrypt, $key){
	global $cryptFn;
    $encrypt = serialize($encrypt);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($cryptFn, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
    $key = pack('H*', $key);
    $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
    $passcrypt = mcrypt_encrypt($cryptFn, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
    $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
    return $encoded;
}

// Decrypt Function
function mc_decrypt($decrypt, $key){
	global $cryptFn;
    $decrypt = explode('|', $decrypt.'|');
    $decoded = base64_decode($decrypt[0]);
    $iv = base64_decode($decrypt[1]);
    if(strlen($iv)!==mcrypt_get_iv_size($cryptFn, MCRYPT_MODE_CBC)){ return false; }
    $key = pack('H*', $key);
    $decrypted = trim(mcrypt_decrypt($cryptFn, $key, $decoded, MCRYPT_MODE_CBC, $iv));
    $mac = substr($decrypted, -64);
    $decrypted = substr($decrypted, 0, -64);
    $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
    if($calcmac!==$mac){ return false; }
    $decrypted = unserialize($decrypted);
    return $decrypted;
}
