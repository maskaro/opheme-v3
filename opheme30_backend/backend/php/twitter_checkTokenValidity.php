<?php

	if (strtotime('+' . __oBACKEND_SM_TOKEN_VALIDITY_CHECK__, strtotime($token['last_validity_check'])) < $now) {
	
		/* check client credentials - only 15 calls of this type per 15min window */
		$content_verify = objectToArray($site->twitter->oauth->get('account/verify_credentials'));
		
		if (__oDEBUG_BACKEND__) { 
			$message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url;
			if (isset($content_verify['errors'])) {
				foreach($content_verify['errors'] as $error) {
					$message .= ' / ' . print_r($error, true);
				}
			}
			error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log);
		}
		
		$code = $site->twitter->oauth->http_code;
		if ($code === 401) { $removeToken = true; }
		else {
			$site->database->query(array(
				'name' => 'updateTokenCheckTime',
				'type' => 'mysql',
				'table' => 'twitter.keys',
				'operation' => 'update',
				'columns' => array(
					'last_validity_check' => date('Y-m-d H:i:s', $now)
				),
				'where' => array(
					'id' => $tokenId
				)
			))->fetch();
		}
		
	} else { $code = 200; }