<?php
	
	//limits: 	5000 requests / 60min / user
	//			5000 requests / 15min / app
	
	include('instagram_checkTokenValidity.php');

	//if everything is OK, carry on looking
	if ($code === 200) {

		// calculate distance in meters
		$distance = intval(floor($job['radius'] / 0.00062137));
		
		$lastCheckedUnix = strtotime($job['last_check']);
		
		// calculate oldest message timestamp limit
		if ($lastCheckedUnix > 0) {
			$since = $lastCheckedUnix;
		} else {
			if (intval(__oBACKEND_MESSAGE_AGE_NEW__) > 0) {
				$since = strtotime('-' . __oBACKEND_MESSAGE_AGE_NEW__, $now);
			} else {
				$since = strtotime('-7 days', $now);
			}
		}
		
		// get content
		$search_call = $site->instagram->oauth->searchMedia(
			$job['latitude'],
			$job['longitude'],
			($distance>5000?5000:$distance),
			$since,
			$now
		);
		$content_search = objectToArray($search_call);

		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $content_search['meta']['code'] . ': searchMedia()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }

		//still OK, getting messages
		if ($content_search['meta']['code'] === 200) {
			
			//klout api v2 object
			$klout = new KloutAPIv2(__oKLOUT_KEY__);

			// filter example: ("my, first" "second, thing" third fourth) => [ my, first | second, thing | third | fourth ]
			//exclusion filter
//			$filter_ex = str_getcsv($job['filter_ex'], ' '); //explode(' ', $job['filter_ex']);
			//inclusion filter
//			$filter = str_getcsv($job['filter'], ' '); //explode(' ', $job['filter']);
			
			//keep track of processed messages this session
			$countValid = 0; $countSkippedFilter = 0; $countSkippedFilterEx = 0; $countInvalidCoordinates = 0; $countTooOld = 0; $countSkippedBlacklisted = 0; $countSkippedPreferences = 0; $countSkippedMessagePerTimeLimitReached = 0; $countServiceError = 0; $countSkippedKloutScoreTooLow = 0; $countDBError = 0;
			
			do {
			
				/*** Parse and Store results ***/
				foreach ($content_search['data'] as $messageBody) {
					
					//if job message limit has been reached, skip message
					if (isset($allowance['jobMessageLimit']) && intval($allowance['jobMessageLimit']) > 0) {
						if ($currentMessageCount >= $allowance['jobMessageLimit']) {
							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' message limit reached. Stopped processing messages.' . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
							break;
						}
					}
					
					$created_at_check = $messageBody['created_time'];

					//check message timestamp - if older than job requirement, skip it
//					if ($module === 'discover') {
						if (intval(__oBACKEND_MESSAGE_AGE_NEW__) > 0) {
							$until = strtotime('+' . __oBACKEND_MESSAGE_AGE_NEW__, $created_at_check);
						} else { $until = $now; }
//					} else {
//						$until = strtotime('+' . __oBACKEND_MESSAGE_AGE_NEW__, $messageBody['created_time']);
//					}

					if ($now > $until) { $countTooOld++; continue; }

					//if message has coordinates attached, it gets processed
					if (
						isset($messageBody['location']['latitude']) && is_numeric($messageBody['location']['latitude']) &&
						(
							isset($messageBody['images']['standard_resolution']['url']) ||
							isset($messageBody['videos']['standard_resolution']['url'])
						)
					) {

						//exclusion filter
//						$stopEx = false; if (strlen($filter_ex[0]) > 0) { foreach ($filter_ex as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stopEx = true; break; } } } if ($stopEx == true) { $countSkippedFilterEx++; unset($stopEx, $keyword); continue; }

						//inclusion filter
						if (isset($job['filter'])) {
							$filter = $job['filter'];
							$stop = false; if (count ($filter) && strlen($filter[0]) > 0) { $stop = true; foreach ($filter as $keyword) { if (stripos($messageBody['caption']['text'], trim($keyword)) !== false) { $stop = false; break; } } } if ($stop == true) { $countSkippedFilter++; unset($stop, $keyword); continue; }
						}
						
						//if message isn't already in the DB, save it for future reference
						if (!$existingMessage = $site->job->messageExists($tokenType, $messageBody['id'])) {
							
							/* KLOUT INFO LOOKUP */
						
							//get twitter user id's klout id
							$kloutId = $klout->KloutIDLookupByID('ig', $messageBody['user']['id']);
							$score = '';

							if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutIDLookupByID for IgID-' . $messageBody['user']['id'] . '/IgScrN-' . $messageBody['user']['username'] . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }

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
							
							$videos = array();
							
							//if video present, add its link to the text
							if (isset($messageBody['videos']['standard_resolution']['url'])) {
								//$shortCode = $site->urlService->urlToShortCode($messageBody['videos']['standard_resolution']['url']);
//								$shortCode = null;
//								if (is_string($shortCode) && strlen($shortCode)) {
//									$url = __oCompanyBrandURL__ . '/url/' . $shortCode;
//								} else { 
									$url = $messageBody['videos']['standard_resolution']['url'];
//									if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' failed to generate URL for link (' . $messageBody['videos']['standard_resolution']['url'] . ').' . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
//								}
								$videos[] = array(
									'url' => $url,
									'image_url' => isset($messageBody['images']['standard_resolution']['url']) ? $messageBody['images']['standard_resolution']['url'] : ''
								);
							}
							
							$images = array();
							
							if (!count($videos)) {
							
								//if image present, add its link to the text
								if (isset($messageBody['images']['standard_resolution']['url'])) {
									//$shortCode = $site->urlService->urlToShortCode($messageBody['images']['standard_resolution']['url']);
	//								$shortCode = null;
	//								if (is_string($shortCode) && strlen($shortCode)) {
	//									$url = __oCompanyBrandURL__ . '/url/' . $shortCode;
	//								} else { 
										$url = $messageBody['images']['standard_resolution']['url'];
	//									if (__oDEBUG_BACKEND__) { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' failed to generate URL for link (' . $messageBody['images']['standard_resolution']['url'] . ').' . PHP_EOL . PHP_EOL); error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
	//								}
									$images[] = array('url' => $url);
								}
							
							}

							//save message coordinates
							$coords = array($messageBody['location']['latitude'], $messageBody['location']['longitude']);
							
							//get coordinates address
							$address = backend_latLngToAddress($coords[0], $coords[1]);
							if ($address === false) { $address = ''; }

							//get sentiment and attach it to message
							if (strlen($messageBody['caption']['text']) > 0) {
								$sentiment = backend_analyseSentiment($messageBody['caption']['text']);
							} else {
								$sentiment = 'none';
							}
							
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

							if ($site->job->storeMessage($token['socialmediaplatform_id'], $tokenType, $messageBody, $job['id'], 'discover') !== null) {
								$countValid++; $currentMessageCount++;
							} else { $countDBError++; }

						//otherwise, just create the link for discover
						} else {
							if ($site->job->storeMessage($token['socialmediaplatform_id'], $tokenType, $messageBody, $job['id'], 'discover', $existingMessage['_id'], null, true) !== null) {
								$countValid++; $currentMessageCount++;
							} else { $countDBError++; }
						}

					} else { $countInvalidCoordinates++; }

				}
				
				if (isset($content_search['pagination'])) {
					$search_call = $site->instagram->oauth->pagination($search_call);
					$content_search = objectToArray($search_call);
					$stopLoopNow = false;
				} else {
					$stopLoopNow = true;
				}
			
			} while($stopLoopNow === false);
			
			$error_message = (
				PHP_EOL . PHP_EOL
				. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $user['id'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ' / ' . $user['email'] . ' / ' . $user['company']['name'] . ') ' . PHP_EOL
				. 'processed ' . count($content_search['data']) . ' new INSTAGRAM messages'
				. (count($content_search['data'])?
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
					:'.' . PHP_EOL
				) . PHP_EOL
			);
			error_log($error_message, 3, $error_log);

		//otherwise, remove token due to being invalid
		} elseif (isset($content_search['meta']['error_type']) && $content_search['meta']['error_type'] === 'OAuthException') {
			$removeToken = true;
		} else {
	
			if (!__oDEBUG_BACKEND__) {
				$message = ''; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; error_log(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, 3, $error_log); }
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
			'table'		 => 'instagram.keys',
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