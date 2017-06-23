<?php

	if (strtotime('+' . __oBACKEND_SM_INTERACTION_CHECK__, strtotime($token['last_interaction_check'])) < $now) {
		
		// update check timestamp
		$site->database->query(array(
			'name' => 'updateInteractionCheckTime',
			'type' => 'mysql',
			'table' => 'twitter.keys',
			'operation' => 'update',
			'columns' => array(
				'last_interaction_check' => date('Y-m-d H:i:s', $now)
			),
			'where' => array(
				'id' => $tokenId
			)
		))->fetch();
		
		// fetch interaction information for current token owner
//		$data = array(
//			'name' => 'getInteractions',
//			'type' => 'mysql',
//			'operation' => 'select',
//			'table' => 'socialMedia.interactions.messages',
//			'where' => array(
//				'social_media_platform_id' => $tokenId
//			)
//		);
//		$interactionResults = $site->database->query($data)->fetch()['getInteractions'];

		$interactionResults = $site->database->queryMySQLCustom(array(
			'query' => 'SELECT * FROM interactionmessage WHERE NOT created_at <=> updated_at AND socialmediaplatform_id = "' . $token['socialmediaplatform_id'] . '"'
		))->fetch()['custom'];
		
		if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - Interactions: ' . print_r( $interactionResults, true) . '.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }
		
		// if any interaction info available
		if (is_array($interactionResults) && count($interactionResults)) {
			
			/**
			 * Check response to follows.
			 */
			
			// go through all interactions
//			foreach ($interactionResults as $iRes) {
//				
//				if ($iRes['type'] !== 'follow_out') { continue; }
//				if (intval($iRes['done']) === 1) { continue; }
//		
//				$params = array(
//					'stringify_ids' => true,
//					'screen_name' => $token['screen_name'],
//					'cursor' => -1,
//					'count' => 5000 //max Twitter allows is 5000
//				);
//
//				// get followers for current token owner
//				$content = objectToArray($site->twitter->oauth->get('followers/ids', $params));
//
//				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content['errors'])) { foreach($content['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
//
//				if ($site->twitter->oauth->http_code === 200 && !empty($content['ids'])) {
//					
//					// look at all users following this token owner
//					foreach ($content['ids'] as $userId) {
//						
//						/**
//						 * Check follow status.
//						 */
//						
//						// match interactions with current user id that has not yet followed token owner
//						if ($iRes['sm_user_id'] == $userId) {
//
//							// update interactions to show current user is following token owner
//							$data1 = array(
//								'name' => 'addFollowInfo',
//								'type' => 'mysql',
//								'operation' => 'insert',
//								'table' => 'socialMedia.interaction',
//								'columns' => array(
//									'opheme_user_id' => $token['user_id'],
//									'sm_user_id' => $iRes['sm_user_id'],
//									'sm_user_screen_name' => $iRes['sm_user_screen_name'],
//									'type' => 'follow_in',
//									'done' => 1,
//									'authKeyId' => $token['id'],
//									'authKeyType' => 'twitter',
//									'added_at' => $now
//								)
//							);
//							$site->database->query($data1)->fetch();
//							
//							$data2 = array(
//								'name' => 'updateFollowInfo',
//								'type' => 'mysql',
//								'operation' => 'update',
//								'table' => 'socialMedia.interaction',
//								'columns' => array(
//									'done' => 1,
//								),
//								'where' => array(
//									'opheme_user_id' => $token['user_id'],
//									'sm_user_id' => $iRes['sm_user_id'],
//									'type' => 'follow_out',
//									'authKeyId' => $token['id'],
//									'authKeyType' => 'twitter'
//								)
//							);
//							$site->database->query($data2)->fetch();
//
//						}
//
//					}
//
//				}
//				
//			}
//			
			/**
			 * Check response to reply status.
			 */
			
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . PHP_EOL . 'Looking at Interaction: ' . print_r($iRes, true) . PHP_EOL . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }
				
				// already processed, carry on
				if ($iRes['created_at'] !== $iRes['updated_at']) { continue; }

				$paramsMsgs = array(
					'user_id' => $iRes['socialmediaplatform_user_id'],
					'include_entities' => false,
					'trim_user' => true,
					'include_rts' => true,
					'exclude_replies' => false,
					'count' => 200 //max Twitter allows is 200
				);

				// get all follower messages
				$messagesMsgs = objectToArray($site->twitter->oauth->get('statuses/user_timeline', $paramsMsgs));

				if (__oDEBUG_BACKEND__) { $message = 'INTERACTIONS FOR ' . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messagesMsgs['errors'])) { foreach($messagesMsgs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }

				if ($site->twitter->oauth->http_code === 200 && !empty($messagesMsgs)) {

					foreach ($messagesMsgs as $msg) {

						// match interaction to current message id that has not yet been detected as a response to any of the token owner's messages
						if ($iRes['socialmediaplatform_message_id'] == $msg['in_reply_to_status_id_str']) {
							
							$discoverMessageId = $site->database->query([
								'name' => 'getMessageId',
								'type' => 'mysql',
								'operation' => 'select',
								'table' => 'socialMedia.interactions',
								'columns' => array( 'discovermessage_id' ),
								'where' => array(
									'id' => $iRes['interaction_id']
								)
							])->fetch()['getMessageId']['discovermessage_id'];
							
							$jobId = $site->database->query([
								'name' => 'getJobId',
								'type' => 'mysql',
								'operation' => 'select',
								'table' => 'app.jobs.messages',
								'columns' => array( 'discover_id' ),
								'where' => array(
									'id' => $discoverMessageId
								)
							])->fetch()['getJobId']['discover_id'];

							//klout api v2 object
							$klout = new KloutAPIv2(__oKLOUT_KEY__);
							
							//get sentiment and attach it to message
							$sentiment = backend_analyseSentiment($msg['text']);
							
							$kloutId = $klout->KloutIDLookupByID('tw', $msg['user']['id_str']);
							$score = "";

							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $jobId . ' - ' . PHP_EOL . 'Klout API reply to (KloutIDLookupByID for TwID-' . $msg['user']['id_str'] . (isset($msg['user']['screen_name']) ? '/TwScrN-' . $msg['user']['screen_name'] : '') . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }

							//if user has a klout id
							if (isset($kloutId)) { //get all the data for later use

								//get their klout score
								$score = floatval($klout->KloutScore($kloutId));

								if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $jobId . ' - ' . PHP_EOL . 'Klout API reply to (KloutScore for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }

							}
							
							$msgCreatedAt = date('Y-m-d H:i:s', strtotime($msg['created_at']));
							
							$msg['opheme_backend'] = array(
								'images' => [],
								'videos' => [],
								'coords' => array(
									'latitude' => 0,
									'longitude' => 0
								),
								'sentiment' => $sentiment,
								'klout_score' => $score,
								'address' => '',
								'social_media_platform' => $tokenType,
								'created_timestamp' => $msgCreatedAt
							);
							
							// store message in mongo and get its ID
							$mongoId = $site->job->storeMessage($token['socialmediaplatform_id'], $tokenType, $msg, $jobId, 'discover', null, true);
							
							if ($mongoId) {
								
								$nowDate = date('Y-m-d H:i:s', $now);
								
								$data1 = array(
									'name' => 'addReply',
									'type' => 'mysql',
									'operation' => 'insert',
									'table' => 'socialMedia.interactions.messages',
									'columns' => array(
										'id' => (string) Uuid::uuid4(),
										'interaction_id' => $iRes['interaction_id'],
										'backend_message_id' => $mongoId,
										'socialmediaplatform_id' => $token['socialmediaplatform_id'],
										'socialmediaplatform_message_id' => $msg['id_str'],
										'socialmediaplatform_user_id' => $msg['user']['id_str'],
										'message_datestamp' => $msgCreatedAt,
										'created_at' => $nowDate,
										'updated_at' => $nowDate
									)
								);
								$site->database->query($data1)->fetch();
								
								// touch parent interaction
								$data2 = array(
									'name' => 'updateReplyInfo',
									'type' => 'mysql',
									'operation' => 'update',
									'table' => 'socialMedia.interactions.messages',
									'columns' => array(
										'updated_at' => $nowDate
									),
									'where' => array(
										'id' => $iRes['id']
									)
								);
								$site->database->query($data2)->fetch();
								
							}

						}

					}

				}

				unset($messagesMsgs);

			}
			
			/**
			 * Check if messages were favourited.
			 */

			// go through all interactions
//			foreach ($interactionResults as $iRes) {
//				
//				if ($iRes['type'] !== 'message_out') { continue; }
//				if (intval($iRes['favourited']) === 1) { continue; }
//			
//				$paramsFavs = array(
//					'user_id' => $iRes['sm_user_id'],
//					'include_entities' => false,
//					'count' => 200 //max Twitter allows is 200
//				);
//
//				// get all follower messages
//				$messagesFavs = objectToArray($site->twitter->oauth->get('favorites/list', $paramsFavs));
//
//				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messagesFavs['errors'])) { foreach($messagesFavs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
//
//				if ($site->twitter->oauth->http_code === 200 && !empty($messagesFavs)) {
//
//					foreach ($messagesFavs as $msg) {
//
//						// match interaction to current message id that has not yet been detected as a favourite of any of the token owner's messages
//						if ($iRes['message_id'] == $msg['id_str']) {
//
//							// update interaction to reflect current user has favourited one of token owner's messages
//							$data1 = array(
//								'name' => 'addFavouritedInfo',
//								'type' => 'mysql',
//								'operation' => 'insert',
//								'table' => 'socialMedia.interaction',
//								'columns' => array(
//									'opheme_user_id' => $token['user_id'],
//									'sm_user_id' => $iRes['sm_user_id'],
//									'sm_user_screen_name' => $iRes['sm_user_screen_name'],
//									'type' => 'favourite_in',
//									'original_message' => $iRes['original_message'],
//									'original_message_id' => $iRes['original_message_id'],
//									'original_message_added_at' => $iRes['original_message_added_at'],
//									'message' => $iRes['message'],
//									'message_id' => $iRes['message_id'],
//									'message_added_at' => $iRes['message_added_at'],
//									'done' => 1,
//									'favourited' => 1,
//									'authKeyId' => $token['id'],
//									'authKeyType' => 'twitter',
//									'added_at' => $now
//								)
//							);
//							$site->database->query($data1)->fetch();
//							
//							$data2 = array(
//								'name' => 'updateFavouritedInfo',
//								'type' => 'mysql',
//								'operation' => 'update',
//								'table' => 'socialMedia.interaction',
//								'columns' => array(
//									'favourited' => 1
//								),
//								'where' => array(
//									'opheme_user_id' => $token['user_id'],
//									'sm_user_id' => $iRes['sm_user_id'],
//									'type' => 'message_out',
//									'message_id' => $iRes['message_id'],
//									'authKeyId' => $token['id'],
//									'authKeyType' => 'twitter',
//								)
//							);
//							$site->database->query($data2)->fetch();
//
//						}
//
//					}
//
//				}
//
//				unset($messagesFavs);
//				
//			}

		}
		
	} else {
		$error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - Not yet time to Check Interaction.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
	}