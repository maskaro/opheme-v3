<?php

	//http://api.twitter.com/1.1/search/tweets.json?q=%20&count=100&geocode=lat,lng,rad&since_id=last_id
	//limits: 	180 requests / 15min / user
	//			450 requests / 15min / app
	
	include('twitter_checkTokenValidity.php');

	//if everything is OK, carry on looking
	if ($code === 200) {

		//search for messages
		$params = array(
			'q' => 				rawurlencode(' '),
			'count' => 			'100',
			'geocode' =>		$job['latitude'] . ',' . $job['longitude'] . ',' . $job['radius'] . 'mi',
			'since_id' =>		$job['since_id'],
			'result_type' => 	'recent',
			'include_entities' => true
		);

		//get content - multi-layered array
		$content_search = objectToArray($site->twitter->oauth->get('search/tweets', $params));

		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content_search['errors'])) { foreach($content_search['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }

		//still OK, getting messages
		if ($site->twitter->oauth->http_code === 200) {
			
			//klout api v2 object
			$klout = new KloutAPIv2(__oKLOUT_KEY__);

			// filter example: ("my, first" "second, thing" third fourth) => [ my, first | second, thing | third | fourth ]
			//exclusion filter
//			$filter_ex = str_getcsv($job['filter_ex'], ' '); //explode(' ', $job['filter_ex']);
			//inclusion filter
//			$filter = str_getcsv($job['filter'], ' '); //explode(' ', $job['filter']);
			
			//keep track of processed messages this session
			$countValid = 0; $countSkippedFilter = 0; $countSkippedFilterEx = 0; $countInvalidCoordinates = 0; $countTooOld = 0; $countSkippedBlacklisted = 0; $countSkippedPreferences = 0; $countSkippedMessagePerTimeLimitReached = 0; $countServiceError = 0; $countSkippedKloutScoreTooLow = 0; $countDBError = 0;
			
			if (!is_array( $content_search['statuses'])) {
				$content_search['statuses'] = array();
			}
			
			/*** Parse and Store results ***/
			foreach ($content_search['statuses'] as $messageBody) {
				
				//if job message limit has been reached, skip message
				if (isset($allowance['jobMessageLimit']) && intval($allowance['jobMessageLimit']) > 0) {
					if ($currentMessageCount >= $allowance['jobMessageLimit']) {
						if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' message limit reached. Stopped processing messages.' . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
						break;
					}
				}
				
				$created_at_check = strtotime($messageBody['created_at']);

				//check message timestamp - if older than job requirement, skip it
				if (intval(__oBACKEND_MESSAGE_AGE_NEW__) > 0) {
					$until = strtotime('+' . __oBACKEND_MESSAGE_AGE_NEW__, $created_at_check);
				} else { $until = $now; }
				if ($now > $until) { $countTooOld++; continue; }

				//if message has coordinates attached, it gets processed
				if (isset($messageBody['geo']['coordinates']) || isset($messageBody['coordinates']['coordinates'])) {

					//exclusion filter
//					$stopEx = false; if (strlen($filter_ex[0]) > 0) { foreach ($filter_ex as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stopEx = true; break; } } } if ($stopEx == true) { $countSkippedFilterEx++; unset($stopEx, $keyword); continue; }

					//inclusion filter
					if (isset($job['filter'])) {
						$filter = $job['filter'];
						$stop = false; if (count($filter) && strlen($filter[0]) > 0) { $stop = true; foreach ($filter as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stop = false; break; } } } if ($stop == true) { $countSkippedFilter++; unset($stop, $keyword); continue; }
					}
					
					//if message isn't already in the DB, save it for future reference
					if (!$existingMessage = $site->job->messageExists($tokenType, $messageBody['id_str'])) {
						
						//save message coordinates
						$coords = (
							isset($messageBody['geo']['coordinates'])?
								$messageBody['geo']['coordinates']:
								$messageBody['coordinates']['coordinates']
						);
						
						//get coordinates address
						$address = backend_latLngToAddress($coords[0], $coords[1]);
						if ($address === false) { 
							$address = '';
						}
						
						//get sentiment and attach it to message
						$sentiment = backend_analyseSentiment($messageBody['text']);
						
						/* KLOUT INFO LOOKUP */
						
						//get twitter user id's klout id
						$kloutId = $klout->KloutIDLookupByID('tw', $messageBody['user']['id_str']);
						$score = '';
						
						if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutIDLookupByID for TwID-' . $messageBody['user']['id_str'] . '/TwScrN-' . $messageBody['user']['screen_name'] . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
						
						//if user has a klout id
						if (isset($kloutId)) { //get all the data for later use
							
							//get their klout score
							$score = floatval($klout->KloutScore($kloutId));
							
							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutScore for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
							
							//get their klout topics of influence
//							$messageBody['klout']['topics'] = json_decode($klout->KloutUserTopics($kloutId), true);
							
//							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutUserTopics for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
							
							//get their klout list of influencers and influencees
//							$kloutInfluence = json_decode($klout->KloutUserInfluence($kloutId), true);
//							$messageBody['klout']['influencers'] = $kloutInfluence['myInfluencers'];
//							$messageBody['klout']['influencees'] = $kloutInfluence['myInfluencees'];
							
//							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutUserInfluence for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
							
						}
						
						$images = array();
						$ext = array('png', 'jpg', 'jpeg', 'gif');
						$urls = null;
						
						if (isset($messageBody['entities']['url'], $messageBody['entities']['url']['urls'])) {
							$urls = $messageBody['entities']['url']['urls'];
						} elseif (isset($messageBody['entities']['urls'])) {
							$urls = $messageBody['entities']['urls'];
						}
						
						if ($urls) {
							
							for ($i = 0, $len = count($urls); $i < $len ; $i++) {
								
								if (!isset($urls['expanded_url'])) { continue; }
								
								if ( strlen($urls['expanded_url']) < 1 || ! in_array(pathinfo( $urls['expanded_url'] )['extension'], $ext) ) { continue; }
								
								$images[] = array(
									'url' => $urls['expanded_url']
								);
								
							}
							
						}
						
						$videos = array();
						
						$messageBody['opheme_backend'] = array(
							'images' => $images,
							'videos' => $videos,
							'coords' => array(
								'latitude' => $coords[0],
								'longitude' => $coords[1]
							),
							'sentiment' => $sentiment,
							'klout_score' => $score,
							'address' => $address,
							'social_media_platform' => $tokenType,
							'created_timestamp' => date('Y-m-d H:i:s', $created_at_check)
						);
						
//						if ($module === 'discover') {
							if ($site->job->storeMessage($token['socialmediaplatform_id'], $tokenType, $messageBody, $job['id'], 'discover') !== null) {
								$countValid++; $currentMessageCount++;
							} else { $countDBError++; }
//						} else {
//							$site->job->storeMessage($tokenType, $messageBody);
//						}
						
					//otherwise, just create the link for discover
					} else {
						$messageBody['opheme_backend']['created_timestamp'] = date('Y-m-d H:i:s', $created_at_check);
						if ($site->job->storeMessage($token['socialmediaplatform_id'], $tokenType, $messageBody, $job['id'], 'discover', $existingMessage['_id'], null, true) !== null) {
							$countValid++; $currentMessageCount++;
						} else { $countDBError++; }
					}

				} else { $countInvalidCoordinates++; }

			}
			
			$error_message = (
				PHP_EOL . PHP_EOL
				. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $user['id'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ' / ' . $user['email'] . ' / ' . $user['company']['name'] . ') ' . PHP_EOL
				. 'processed ' . count($content_search['statuses']) . ' new TWITTER messages'
				. (count($content_search['statuses'])?
					', of which: ' . PHP_EOL
					. ($countValid?'    ' . $countValid . ' were valid ' . PHP_EOL:'')
					. ($countServiceError?'    ' . $countServiceError . ' gave Service Errors ' . PHP_EOL:'')
					. ($countDBError?'    ' . $countDBError . ' gave Database Errors ' . PHP_EOL:'')
					. ($countInvalidCoordinates?'    ' . $countInvalidCoordinates . ' had invalid coordinates ' . PHP_EOL:'')
					. ($countTooOld?'    ' . $countTooOld . ' were too old ' . PHP_EOL:'')
					. ($countSkippedFilter?'    ' . $countSkippedFilter . ' were skipped because Filter (' . implode(' || ', $job['filter']) . ') had no matches ' . PHP_EOL:'')
//					. ($countSkippedFilterEx?'    ' . $countSkippedFilterEx . ' were skipped because FilterEx (' . $job['filter_ex'] . ') had at least a match ' . PHP_EOL:'')
//					. ($countSkippedMessagePerTimeLimitReached?'    ' . $countSkippedMessagePerTimeLimitReached . ' were skipped because Hourly Limit (' . $job['hourly_limit'] . ') was reached ' . PHP_EOL:'')
//					. ($countSkippedBlacklisted?'    ' . $countSkippedBlacklisted . ' were blacklisted' . PHP_EOL:'')
//					. ($countSkippedPreferences?'    ' . $countSkippedPreferences . ' had preferences and none were matched' . PHP_EOL:'')
					. ($countSkippedKloutScoreTooLow?'    ' . $countSkippedKloutScoreTooLow . ' were skipped because of too low Klout score (<' . __oBACKEND_CAMPAIGN_REPLY_KLOUT_LOWER_LIMIT__ . ') [Campaign]' . PHP_EOL:'')
					:'.' . PHP_EOL
				) . PHP_EOL
			);
			error_log($error_message, 3, $error_log);

			//store last message id for future calls
			if (isset($content_search['search_metadata'])) {
				$site->job->setStatus(array('jobType' => $module, 'id' => $job['id'], 'since_id' => $content_search['search_metadata']['max_id_str'], 'updated_at' => date('Y-m-d H:i:s', $now)));
			}

		//otherwise, remove token due to being invalid
		} elseif ($site->twitter->oauth->http_code === 401) {
			$removeToken = true;
		} else {
	
			if (!__oDEBUG_BACKEND__) {
				$message = ''; if (isset($content_search['errors'])) { foreach($content_search['errors'] as $error) { $message .= ' / ' . print_r($error, true); } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
				$error_message = (
					PHP_EOL . PHP_EOL
					. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $user['id'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ' - ' . $user['email'] . ') '
					. 'processed 0 new messages.' . (strlen($message)?' Service Error: (' . $message . ').':'') .
					PHP_EOL . PHP_EOL
				);
				error_log($error_message, 3, $error_log);
			}
		
		}
		
	}
	
	//invalid token
	if (isset($removeToken) && $removeToken === true) {
		
		$site->database->query( array(
			'name'		 => 'makeTokenInvalid',
			'type'		 => 'mysql',
			'table'		 => 'twitter.keys',
			'operation'	 => 'update',
			'columns' => array(
				'valid' => 0,
				'updated_at' => date('Y-m-d H:i:s', $now)
			),
			'where'		 => array(
				'id' => $tokenId
			)
		) )->fetch()[ 'makeTokenInvalid' ];
		
		$error_message = (PHP_EOL . PHP_EOL . 'Authentication Token from ' . ucfirst($tokenType) . ' with ID ' . $tokenId . ' was removed due to being invalid. Skipped.' . PHP_EOL . PHP_EOL);
		error_log($error_message, 3, $error_log); 

	}