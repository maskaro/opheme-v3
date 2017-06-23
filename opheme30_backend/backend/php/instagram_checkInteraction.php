<?php

	if (strtotime('+' . __oBACKEND_SM_INTERACTION_CHECK__, strtotime($token['last_interaction_check'])) < $now) {
		
		// update check timestamp
		$site->database->query(array(
			'name' => 'updateInteractionCheckTime',
			'type' => 'mysql',
			'table' => 'instagram.keys',
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
//				//get followers
//				$content = objectToArray($site->instagram->oauth->getUserFollower());
//
//				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $content['meta']['code'] . ': getUserFollower()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } $error_message = (PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }
//		
//				if ($content['meta']['code'] === 200 && !empty($content['data'])) {
//					
//					// look at all users following this token owner
//					foreach ($content['data'] as $user) {
//						
//						/**
//						 * Check follow status.
//						 */
//						
//						// match interactions with current user id that has not yet followed token owner
//						if ($iRes['sm_user_id'] == $user['id']) {
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
//									'authKeyId' => $token['id'],
//									'authKeyType' => 'instagram',
//									'added_at' => time()
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
//									'authKeyType' => 'instagram'
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
			
			/**
			 * Check response to reply status.
			 */
			
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . PHP_EOL . 'Looking at Interaction: ' . print_r($iRes, true) . PHP_EOL . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }
				
				// already processed, carry on
				if ($iRes['created_at'] !== $iRes['updated_at']) { continue; }

				$search_call = $site->instagram->oauth->getMediaComments(
					$iRes['socialmediaplatform_message_id']
				);
				$content_search = objectToArray($search_call);

				if (__oDEBUG_BACKEND__) { $message = 'INTERACTIONS FOR ' . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messagesMsgs['errors'])) { foreach($messagesMsgs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } $error_message = (PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }

				if ($site->twitter->oauth->http_code === 200 && !empty($content_search['data'])) {

					foreach ($content_search['data'] as $msg) {

						// match interaction to current message id that has not yet been detected as a response to any of the token owner's messages
						if ($msg['from']['id'] == $iRes['socialmediaplatform_user_screen_name'] && stristr($msg['text'], $token['screen_name']) !== false) {
							
							//get sentiment and attach it to message
							$sentiment = backend_analyseSentiment($msg['text']);
							
							$msgCreatedAt = date('Y-m-d H:i:s', $msg['created_time']);
							
							$msg['opheme_backend'] = array(
								'images' => [],
								'videos' => [],
								'coords' => array(
									'latitude' => 0,
									'longitude' => 0
								),
								'sentiment' => $sentiment,
								'klout_score' => '',
								'address' => '',
								'social_media_platform' => $tokenType,
								'created_timestamp' => $msgCreatedAt
							);
							
							$msg['user'] = array(
								'id' =>	$msg['from']['id'],
								'id_str' => $msg['from']['id'],
								'screen_name' => $msg['from']['username'],
								'profile_image_url' => $msg['from']['profile_picture']
							);
							
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
										'socialmediaplatform_message_id' => $msg['id'],
										'socialmediaplatform_user_id' => $msg['user']['id'],
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

			/*
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if ($iRes['type'] !== 'message_out') { continue; }
				if (intval($iRes['done']) === 1) { continue; }
			
				$paramsFavs = array(
					'user_id' => $iRes['sm_user_id'],
					'include_entities' => false,
					'count' => 200 //max Twitter allows is 200
				);

				// get all follower messages
				$messagesFavs = objectToArray($site->instagram->oauth->get('favorites/list', $paramsFavs));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->instagram->oauth->http_code . ': ' . $site->instagram->oauth->url; if (isset($messagesFavs['errors'])) { foreach($messagesFavs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } $error_message = (PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }

				if ($site->instagram->oauth->http_code === 200 && !empty($messagesFavs)) {

					foreach ($messagesFavs as $msg) {

						// match interaction to current message id that has not yet been detected as a favourite of any of the token owner's messages
						if ($iRes['reply_message_id'] == $msg['id_str']) {

							// update interaction to reflect current user has favourited one of token owner's messages
							$data = array(
								'name' => 'updateFavouritedInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'favourited_at' => $now
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'authKeyId' => $token['id'],
									'authKeyType' => 'instagram',
									'reply_message_id' => $iRes['reply_message_id']
								)
							);
							$site->database->query($data)->fetch();

						}

					}

				}

				unset($messagesFavs);
				
			}
			*/
			
		}
		
	} else {
		$error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - Not yet time to Check Interaction.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
	}