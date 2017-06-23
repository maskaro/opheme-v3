<?php
	
	if (strtotime('+' . __oBACKEND_SM_AVG_TIME_CHECK__, $token['last_checked_messages']) < $now) {
		
		//get followers
		$content = objectToArray($site->instagram->oauth->getUserFollower());
		
		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $content['meta']['code'] . ': getUserFollower()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }
		
		if ($content['meta']['code'] === 200 && !empty($content['data'])) {
			
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
			
			foreach ($content['data'] as $user) {
				
				//get follower messages
				$messages = objectToArray($site->instagram->oauth->getUserMedia($user['id']));
				
				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $content['meta']['code'] . ': getUserMedia()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }
				
				if ($content['meta']['code'] === 200 && !empty($messages['data'])) {
					
					foreach ($messages['data'] as $msg) {
						if ($msg['created_time'] >= $checkCreationTime) {
							//format time
							$msg['created_at'] = date('D M j G:i:s T Y', $message['created_time']);
							//extract time from message created_at field
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
			
			trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - successfully calculated average Follower Message time (' . $averageStr . ').' . PHP_EOL . PHP_EOL);
			
		}
		
	} else { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - not yet time to check (' . date('Y-m-d H:i:s', $token['last_checked_messages']) . '). Skipped' . PHP_EOL . PHP_EOL); }