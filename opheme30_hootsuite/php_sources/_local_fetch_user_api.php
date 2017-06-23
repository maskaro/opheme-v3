<?php

	include ('_local_common.php');

	$query = "select api_key, tour_ended from opheme_api_keys where hootsuite_id = $user_id";
	$querykey = 'KEY' . md5($query);

	$api_key = '';
	$tour_ended = 0;

	if (!$result = $m->get($querykey)) {

		try {
		
			if ($result = $db->query($query)) {

				$res = $result->fetch_assoc();

				$api_key = $res["api_key"];

				if (strlen($api_key)) {

					$api_key = mc_encrypt($api_key, $cryptSecret);
					$tour_ended = $res["tour_ended"];

				}

			}

		} catch(Exception $e) {

            echo '{ "error": "mysql_error", "error_description": "PHP Error. Error: ' . $e->getMessage() . '" }';

		}
		
	} else {
		
		$api_key = $result;
		
	}

	include ('_local_common_end.php');
