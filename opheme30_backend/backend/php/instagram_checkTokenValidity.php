<?php

	if (strtotime('+' . __oBACKEND_SM_TOKEN_VALIDITY_CHECK__, strtotime($token['last_validity_check'])) < $now) {
	
		/* check client credentials - only 15 calls of this type per 15min window */
		$content_verify = objectToArray($site->instagram->oauth->getUser());
		
		if (__oDEBUG_BACKEND__) { 
			$message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $content_verify['meta']['code'] . ': getUser()';
			if ($content_verify['meta']['code'] > 200) {
				$message .= ' / ' . $content_verify['meta']['error_type'] . ' - ' . $content_verify['meta']['error_message'];
			}
			error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log);
		}
		
		$code = $content_verify['meta']['code'];
		if (isset($content_search['meta']['error_type']) && $content_search['meta']['error_type'] === 'OAuthException') { $removeToken = true; }
		else {
			$site->database->query(array(
				'name' => 'updateTokenCheckTime',
				'type' => 'mysql',
				'table' => 'instagram.keys',
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