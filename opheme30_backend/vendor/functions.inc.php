<?php

/**
 * Returns company name from URL: discover.oPheme.Com => opheme
 * @param string $domain URL containing domain name.
 */
function getCompanyID ( $domain )
{
	$domain = strtolower( $domain );
	//more than 1 dot means there are one or more subdomains
	while ( substr_count( $domain, '.' ) > 1 )
	{
		$domain = substr( $domain, strpos( $domain, '.' ) + 1 );
	}
	return substr( $domain, 0, strpos( $domain, '.' ) );
}

/**
 * Convert an object to an array.
 * @param object|array $d An object or array to convert.
 * @return array Object as Array.
 */
function objectToArray ( $d )
{
	if ( is_object( $d ) )
	{
		$d = get_object_vars( $d );
	}
	if ( is_array( $d ) )
	{
		return array_map( __FUNCTION__, $d );
	}
	else
	{
		return $d;
	}
}

/**
 * check whether an array is associative or not
 * returns TRUE or FALSE
 */
function is_assoc_array ( $array )
{
	$return = null;
	if ( !is_array( $array ) )
	{
		$return = false;
	}
	else
	{
		$return = (bool) count( array_filter( array_keys( $array ), 'is_string' ) );
	}
	return $return;
}

/**
 * 
 * @param type $str
 * @return type
 */
function map_entities_quotes ( $str )
{
	return htmlentities( $str, ENT_QUOTES );
}

/**
 * Recursively goes through a multi level array and applies array_map to everything.
 * @param type $callback
 * @param type $array
 * @return type
 */
function array_map_deep ( $callback, $array )
{
	$new = array();
	foreach ( $array as $key => $val )
	{
		if ( is_array( $val ) )
		{
			$new[ $key ] = array_map_deep( $callback, $val );
		}
		else
		{
			$new[ $key ] = call_user_func( $callback, $val );
		}
	}
	return $new;
}

/**
 * Converts seconds to weeks, days, hours, minutes, seconds.
 * @param integer $secs Number of seconds to convert.
 * @return string Formatted string.
 */
function secs_to_h ( $secs )
{

	// specifically handle zero
	if ( $secs == 0 )
	{
		$return = '0 seconds';
	}
	else
	{

		$units = array(
			"week"	 => 7 * 24 * 3600,
			"day"	 => 24 * 3600,
			"hour"	 => 3600,
			//"minute" =>        60,
			//"second" =>         1,
		);

		$s = '';

		foreach ( $units as $name => $divisor )
		{
			$quot = intval( $secs / $divisor );
			if ( $quot )
			{
				$s .= "$quot $name";
				$s .= (abs( $quot ) > 1 ? 's' : '') . ', ';
				$secs -= $quot * $divisor;
			}
		}

		$return = substr( $s, 0, -2 );
	}

	return $return;
}

/**
 * Custom error handler.
 * @param integer $errno Error number.
 * @param string $errstr Error message.
 * @param string $errfile Error file.
 * @param integer $errline Error line.
 * @return boolean TRUE to stop PHP's default error handler from executing.
 */
function myErrorHandler ( $errno, $errstr, $errfile, $errline )
{

	if ( !(error_reporting() & $errno) )
	{
		return;
	} // This error code is not included in error_reporting

	$type = null;

	switch ( $errno )
	{

		case E_COMPILE_ERROR:
		case E_ERROR:
		case E_USER_ERROR:
			$message = "<b>MyPHP ERROR</b> [$errno] $errstr<br />\nFatal error on line $errline in file $errfile.";
			$type	 = 'ERR';
			break;

		case E_COMPILE_WARNING:
		case E_WARNING:
		case E_USER_WARNING:
			$message = "<b>MyPHP WARNING</b> [$errno] $errstr<br />\nWarning on line $errline in file $errfile.";
			$type	 = 'WAR';
			break;

		case E_NOTICE:
		case E_USER_NOTICE:
			$message = "<b>MyPHP NOTICE</b> [$errno] $errstr<br />\nNotice on line $errline in file $errfile.";
			$type	 = 'INFO';
			break;

		default:
			$message = "Unknown error type: [$errno] $errstr<br />\nError on line $errline in file $errfile";
			$type	 = 'ERR';
			break;
	}

	//set message for framework to handle
	$msg = new Message();
	$msg->set( 'system', $type, $errfile, $message );
	unset( $msg );

	/* Don't execute PHP internal error handler */
	return true;
}

/**
 * Validates time strings 00:00[:00] -> 23:59[:00].
 * @param string $time Time string.
 * @return boolean TRUE if valid, FALSE otherwise.
 */
function isTimeValid ( $time )
{
	return (is_object( DateTime::createFromFormat( 'H:i', $time ) ) || is_object( DateTime::createFromFormat( 'H:i:s', $time ) ));
}

/**
 * Validates date strings m/d/Y or Y-m-d.
 * @param string $date Date string.
 * @return boolean TRUE if valid, FALSE otherwise.
 */
function isDateValid ( $date )
{
	return (is_object( DateTime::createFromFormat( 'm/d/Y', $date ) ) || is_object( DateTime::createFromFormat( 'Y-m-d', $date ) ));
}

function getLoadPercentage ( $cpu_count, $load )
{
	return floor( $load * 100 / $cpu_count );
}

function getMemoryMB ( $memory )
{
	return floor( ($memory / 1024) * 100 ) / 100;
}

function getMemoryGB ( $memory )
{
	return floor( ($memory / 1024 / 1024) * 100 ) / 100;
}

/**
 * Gets last line in a file.
 * @param string $file File $location['
 * @return string Last line in the file.
 */
function getLastLine ( $file )
{

	$line = '';

	$f		 = fopen( $file, 'r' );
	$cursor	 = -1;

	fseek( $f, $cursor, SEEK_END );
	$char = fgetc( $f );

	/**
	 * Trim trailing newline chars of the file
	 */
	while ( $char === "\n" || $char === "\r" )
	{
		fseek( $f, $cursor--, SEEK_END );
		$char = fgetc( $f );
	}

	/**
	 * Read until the start of file or first newline char
	 */
	while ( $char !== false && $char !== "\n" && $char !== "\r" )
	{
		/**
		 * Prepend the new char
		 */
		$line	 = $char . $line;
		fseek( $f, $cursor--, SEEK_END );
		$char	 = fgetc( $f );
	}

	return $line;
}

/**
 * Gets current day of the week.
 * @return string Current Day of the week.
 */
function getDayOfWeek ()
{

	$pTimezone = 'Europe/London';

	$userDateTimeZone	 = new DateTimeZone( $pTimezone );
	$UserDateTime		 = new DateTime( "now", $userDateTimeZone );

	$offsetSeconds = $UserDateTime->getOffset();

	return gmdate( "l", time() + $offsetSeconds );
}

/**
 * Get the sentiment of a string.
 * @param string $text Text to analyse.
 * @return string Sentiment of $text. Can be 'neutral', 'positive', or 'negative'.
 */
function backend_analyseSentiment ( $text )
{

	$jtext = 'txt=' . $text;

	$ch = curl_init( 'http://sentiment.vivekn.com/api/text/' );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $jtext );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		//'Content-Type: application/json',
		'Accept: application/json, text/javascript',
		'Content-Length: ' . strlen( $jtext ),
		'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
	) );

	$result = objectToArray( json_decode( curl_exec( $ch ) ) );

	if ( __oDEBUG_BACKEND__ )
	{
		trigger_error( PHP_EOL . PHP_EOL . 'Sentiment Analysis Result for (' . $text . '):' . print_r( $result, true ) );
	}

	return strtolower( $result[ 'result' ][ 'sentiment' ] );
}

/**
 * Get the Address of a set of coordinates.
 * @param string $lat Latitude.
 * @param string $lng Longitude.
 * @return string|boolean Address on success, FALSE on failure.
 */
function backend_latLngToAddress ( $lat, $lng )
{

	//$url = 'http://open.mapquestapi.com/geocoding/v1/reverse?location=' . $lat . ',' . $lng . '&key=' . __oMAPQUEST_API_KEY__;
	$url	 = 'http://api.geonames.org/findNearbyPostalCodesJSON?lat=' . $lat . '&lng=' . $lng . '&radius=0.1&style=FULL&username=' . __oGEONAMES_USERNAME__;
	$result	 = objectToArray( json_decode( get_web_page( $url ) ) );

	if ( __oDEBUG_OPS__ )
	{
		trigger_error( PHP_EOL . PHP_EOL . 'Coordinates to Address result (' . $lat . ',' . $lng . '):' . print_r( $result, true ) . PHP_EOL . PHP_EOL );
	}

	/* if (isset($result['results'][0]['locations'][0])) {
	  $location = $result['results'][0]['locations'][0];
	  $address = $location['street'] . ' | ' . $location['adminArea5'] . ' | ' . $location['postalCode'] . ' | ' . $location['adminArea4'] . ' | ' . $location['adminArea3'] . ' | ' . $location['adminArea1'];
	  } */
	if ( isset( $result[ 'postalCodes' ][ 0 ] ) )
	{
		$location	 = $result[ 'postalCodes' ][ 0 ];
		$address	 = $location[ 'placeName' ] . (isset( $location[ 'adminName3' ] ) ? ' | ' . $location[ 'adminName3' ] : '') . ' | ' . $location[ 'postalCode' ] . ' | ' . $location[ 'adminName1' ] . ' | ' . $location[ 'countryCode' ];
	}
	else
	{
		$address = false;
	}

	return $address;
}

/**
 * Get output from a web url service.
 * @param string $url URL to contact.
 * @return string Response.
 */
function get_web_page ( $url )
{

	$options = array(
		CURLOPT_RETURNTRANSFER	 => true, // return web page
		CURLOPT_HEADER			 => false, // don't return headers
		CURLOPT_FOLLOWLOCATION	 => true, // follow redirects
		CURLOPT_ENCODING		 => "", // handle compressed
		CURLOPT_USERAGENT		 => "oPheme", // who am i
		CURLOPT_AUTOREFERER		 => true, // set referer on redirect
		CURLOPT_CONNECTTIMEOUT	 => 120, // timeout on connect
		CURLOPT_TIMEOUT			 => 120, // timeout on response
		CURLOPT_MAXREDIRS		 => 10 ); // stop after 10 redirects

	$ch			 = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content	 = curl_exec( $ch );
	$err		 = curl_errno( $ch );
	$errmsg		 = curl_error( $ch );
	$header		 = curl_getinfo( $ch );
	$httpCode	 = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

	curl_close( $ch );

	$header [ 'http_code' ]	 = $httpCode;
	$header [ 'errno' ]		 = $err;
	$header [ 'errmsg' ]		 = $errmsg;
	$header [ 'content' ]		 = $content;
	return $header [ 'content' ];
}

/**
 * Calculate difference between two microtime(true) stamps.
 * @param float $start Start time.
 * @param float $end End time.
 * @param int $precision Controls number of decimals.
 * @return string Microseconds string representation as 0.123.
 */
function calcMicroTimeDiff ( $start, $end, $precision = 3 )
{

	$diff	 = $end - $start;
	$sec	 = intval( $diff );
	$micro	 = $diff - $sec;
	return intval( date( '%s', mktime( 0, 0, $sec ) ) ) . str_replace( '0.', '.', sprintf( '%.' . $precision . 'f', $micro ) );
}

/**
 * Checks whether a string is a valid unix timestamp.
 * @param string/integer $timestamp Unix timestamp.
 * @return boolean True or False.
 */
function isValidTimeStamp ( $timestamp )
{
	return ((string) (int) $timestamp === $timestamp) && ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
}

/**
 * Checks whether a string is valid JSON.
 * @param string $string JSON string.
 * @return boolean True of False.
 */
function isValidJson ( $string )
{
	$json = json_decode( $string );
	return (json_last_error() === JSON_ERROR_NONE?$json:false);
}
