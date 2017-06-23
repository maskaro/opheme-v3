<?php
	
	if (strtotime('+' . __oBACKEND_SM_AVG_TIME_CHECK__, $token['last_checked_messages']) < $now) {
	
		$params = array(
			'stringify_ids' => true,
			'screen_name' => $token['screen_name'],
			'cursor' => -1,
			'count' => 5000 //max Twitter allows is 5000
		);
		
		//get followers
		$content = objectToArray($site->twitter->oauth->get('followers/ids', $params));
		
		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content['errors'])) { foreach($content['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $messagePHP_EOL . PHP_EOL); }
		
		if ($site->twitter->oauth->http_code === 200 && !empty($content['ids'])) {
			
			//update check timestamp
			$site->database->query(array(
				'name' => 'updateTokenCheckTime',
				'type' => 'mysql',
				'table' => $tokenType . '.keys',
				'operation' => 'update',
				'columns' => array(
					'last_checked_messages' => $now
				),
				'where' => array(
					'id' => $tokenId
				)
			))->fetch();
			
			$checkCreationTime = strtotime('-' . __oBACKEND_SM_AVG_TIME_MSG_AGE__, $now);
			$avg = 0; $cnt = 0;
			
			foreach ($content['ids'] as $userId) {
				
				$params = array(
					'user_id' => $userId,
					'include_entities' => false,
					'trim_user' => true,
					'include_rts' => true,
					'exclude_replies' => true, //get only user initiated tweets for a true user message time average
					'count' => 200 //max Twitter allows is 200
				);
				
				//get follower messages
				$messages = objectToArray($site->twitter->oauth->get('statuses/user_timeline', $params));
				
				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messages['errors'])) { foreach($messages['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }
				
				if ($site->twitter->oauth->http_code === 200 && !empty($messages)) {
					
					foreach ($messages as $msg) {
						if (strtotime($msg['created_at']) >= $checkCreationTime) {
							//extract time from tweet created_at field
							$tmp = explode(':', explode(' ', $msg['created_at'])[3]);
							//calculate number of seconds of HH:MM:SS and add it to the stack
							$avg += strtotime($tmp[0] . ' hours ' . $tmp[1] . ' minutes', 0);
							//increase message counter
							$cnt++;
						}
					}
					
				}
				
				unset($messages);
				
			}
			
			$average = floor($avg / $cnt);
			$averageStr = gmdate('H:i:s', $average);
			
			// update average message time
			$site->database->query(array(
				'name' => 'updateTokenMsgTime',
				'type' => 'mysql',
				'table' => $tokenType . '.keys',
				'operation' => 'update',
				'columns' => array(
					'average_message_time_of_followers' => $averageStr
				),
				'where' => array(
					'id' => $tokenId
				)
			))->fetch();
			
			trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - successfully calculated average Follower Tweet time (' . $averageStr . ').' . PHP_EOL . PHP_EOL);
			
		}
		
	} else { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - not yet time to check (' . date('Y-m-d H:i:s', $token['last_checked_messages']) . '). Skipped' . PHP_EOL . PHP_EOL); }