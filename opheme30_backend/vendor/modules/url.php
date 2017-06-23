<?php

	$redirectAway = true;

	switch ($task) {
		
		// => /url/share/discover/123
		case 'share':
			
			if (!empty($extra)) {
			
				@list($jobType, $jobId) = explode('/', $extra);
				
				if (isset($jobType, $jobId) && in_array($jobType, $availableJModules)) {
					
					$messagesCount = $site->job->getShare($jobType, $jobId);
					
					if (!empty($messagesCount)) {
						
						$jobCheck = $site->job->getSpecs(array('jobType' => $jobType, 'id' => $jobId));
						
						if (!empty($jobCheck[0]) && intval($jobCheck[0]['shared'])) {
							
							$job = $jobCheck[0];
							
							/**
							 * Only Twitter supports posting new statuses.
							 */
							$job['authKey'] = $site->job->getJobTokens($jobType, $job['id'], true);
							foreach ($job['authKey'] as $authKey) {
								if ($authKey['type'] === 'twitter') {
									$token = $authKey;
								}
							}
							
							$usr = $site->user->getUsersBy('id', $job['user_id'])[0];
							
							//get messages
							$fetch = array('jobType' => $jobType, 'id' => $jobId, 'maxItems' => $messagesCount, 'lastId' => '0');
							if ($jobType === 'campaign') { $fetch['sent'] = true; }
							$messages = json_encode($site->job->getNewMessages($fetch));
							
							//enable this when enabling automatic job sharing
							//$messages = json_encode($site->job->getAllStoredMessages($jobType, $jobId, ($jobType === 'campaign')));

							$moduleData = array(
								'jobType' => ucfirst($jobType),
								'smType' => 'Twitter',
								'userName' => $usr['firstname'] . ' ' . $usr['lastname'],
								'screen_name' => $token['screen_name'],
								'mapMessages' => $messages,
								'loggedIn' => $loggedIn,
								'loadTpl' => 'job_share_link'
							);

							unset($job, $usr, $jobId, $jobType, $token, $messages, $messagesCount, $fetch);

							$redirectAway = false;
						
						}
					
					}
					
				}
				
			}
			
			break;
		
		case 'campaign':
			
			if (!empty($extra)) {
			
				@list($jobId, $smModule, $messageId) = explode('/', $extra);
				
				if (isset($jobId, $smModule, $messageId) && strlen($jobId) > 0 && strlen($smModule) > 0 && strlen($messageId) > 0) {
					
					if (in_array($smModule, $availableSMModules)) {
					
						$msg = $site->job->getMessage($smModule, $messageId);
						$job = $site->job->getSpecs(array('jobType' => $task, 'id' => $jobId));
						
						if (count($job)) {
							
							$job = $job[0];
							$usr = $site->user->getUsersBy('id', $job['user_id'])[0];
							$prefs = $site->job->getPreferences($smModule, $msg['user']['screen_name']);
							$blacklisted = $site->job->userIsBlacklisted($smModule, $msg['user']['screen_name']);

							if (isset($msg, $job, $usr, $prefs, $blacklisted)) {

								$cats = array('Bars', 'Clubs', 'Restaurants', 'Clothing', 'Music', 'General Shopping');

								$moduleData = array(
									'smModule' => $smModule,
									'msg' => $msg,
									'job' => $job,
									'user' => $usr,
									'prefs' => $prefs,
									'blacklisted' => $blacklisted,
									'cats' => $cats,
									'url' => '/url/campaign/' . $extra,
									'loggedIn' => $loggedIn,
									'loadTpl' => 'campaign_message'
								);

								unset($msg, $job, $usr, $prefs, $blacklisted, $cats);

								$redirectAway = false;

							}
							
						}
						
					}
					
				}
						
			}
			
			break;
			
		/*case 'preferences':
			
			if (isset($_post['screen_name'], $_post['smModule']) && in_array($_post['smModule'], $availableSMModules)) {
			
				if (isset($_post['preferences']) && count($_post['preferences']) > 0) {

					if (isset($_post['password']) && strlen($_post['password']) >= 8) {

						if (isset($_post['prefs_exist'])) {

							$user = $site->database->query(array(
								'name' => 'getUser',
								'type' => 'mysql',
								'table' => $_post['smModule'] . '.campaign.preferences',
								'operation' => 'select',
								'where' => array(
									'screen_name' => $_post['screen_name']
								)
							))->fetch()['getUser'];
							
							//check password
							$pass = $site->crypt->hashPassword($_post['password'], $user['salt']);
							
							if ($user['password'] === $pass['hash']) {
								
								$site->database->query(array(
									'name' => 'savePrefs',
									'type' => 'mysql',
									'table' => $_post['smModule'] . '.campaign.preferences',
									'operation' => 'update',
									'columns' => array(
										'preferences' => implode(',', $_post['preferences'])
									),
									'where' => array(
										'screen_name' => $_post['screen_name']
									)
								))->fetch()['savePrefs'];
								
								$msgOk = 'Successfully updated Marketing Preferences for @' . $_post['screen_name'] . '.';
								
							} else { $msg = 'Marketing Preferences not saved. Incorrect password for current ' . ucfirst($_post['smModule']) . ' Preferences.'; }

						} else {

							if (isset($_post['confirm-password']) && $_post['confirm-password'] === $_post['password']) {
								
								$pass = $site->crypt->hashPassword($_post['password']);
								
								$site->database->query(array(
									'name' => 'savePrefs',
									'type' => 'mysql',
									'table' => $_post['smModule'] . '.campaign.preferences',
									'operation' => 'insert',
									'columns' => array(
										'screen_name' => $_post['screen_name'],
										'preferences' => implode(',', $_post['preferences']),
										'password' => $pass['hash'],
										'salt' => $pass['salt']
									)
								))->fetch()['savePrefs'];
								
								$msgOk = 'Successfully saved Marketing Preferences for @' . $_post['screen_name'] . '.';
								
							} else { $msg = 'Marketing Preferences not saved. Passwords do not match. Please make sure you enter the same password in both Password fields.'; }

						}

					} else { $msg = 'Marketing Preferences not saved. Please fill in the Password field, at least 8 characters, and try again.'; }

				} else { $msg = 'Marketing Preferences not saved. Please select at least one Marketing Category and try again.'; }
				
			} else { $msg = 'Marketing Preferences not saved. There is some missing information. Please try again.'; }
			
			if (isset($msgOk)) {
				$site->message->set($module, 'OK', $module . '_preferences', $msgOk);
			} else {
				$site->message->set($module, 'WAR', $module . '_preferences', $msg);
			}
			
			$redirectAway = false;
			$site->url->redirectTo($_post['url']);
			
			break;
		*/
		case 'blacklist':
			
			if (isset($_post['screen_name'], $_post['smModule']) && in_array($_post['smModule'], $availableSMModules)) {

				/*if (isset($_post['prefs_exist'])) {
					
					if (isset($_post['password']) && strlen($_post['password']) >= 8) {
						
						$user = $site->database->query(array(
							'name' => 'getUser',
							'type' => 'mysql',
							'table' => $_post['smModule'] . '.campaign.preferences',
							'operation' => 'select',
							'columns' => array(
								'password' => 'pass',
								'salt'
							),
							'where' => array(
								'screen_name' => $_post['screen_name']
							)
						))->fetch()['getUser'];
						
						//check password
						$pass = $site->crypt->hashPassword($_post['password'], $user['salt']);

						if ($user['pass'] === $pass['hash']) { $saveBlacklist = true; }
						else {	
							$msg = 'Blacklist Preference not saved. Incorrect password for current ' . ucfirst($_post['smModule']) . ' Account.';
							$saveBlacklist = false;
						}
						
					} else { 
						$msg = 'Blacklist Preference not saved. Please fill in the Password field, at least 8 characters, and try again.';
						$saveBlacklist = false;
					}
					
				} else {*/ $saveBlacklist = true; //}

				if ($saveBlacklist === true) {
					
					$data = array(
						'name' => 'blck',
						'type' => 'mysql',
						'table' => $_post['smModule'] . '.campaign.blacklist'
					);
					
					$words = (isset($_post['blacklist'])?'added @' . $_post['screen_name'] . ' to':'removed @' . $_post['screen_name'] . ' from');
					
					$info = array(
						'screen_name' => $_post['screen_name']
					);
					
					if (isset($_post['blacklist'])) {
						$data['operation'] = 'insert';
						$data['columns'] = $info;
					} else {
						$data['operation'] = 'delete';
						$data['where'] = $info;
					}

					$site->database->query($data)->fetch()['blck'];

					$msgOk = 'Successfully ' . $words . ' Blacklist.';

				}
				
			} else { $msg = 'Blacklist Preference not saved. There is some missing information. Please try again.'; }
			
			if (isset($msgOk)) {
				$site->message->set($module, 'OK', $module . '_blacklist', $msgOk);
			} else {
				$site->message->set($module, 'WAR', $module . '_blacklist', $msg);
			}
			
			$redirectAway = false;
			$site->url->redirectTo($_post['url']);
			
			break;
		
		default:
			
			$url = $site->urlService->shortCodeToUrl($task);
			
			if (is_string($url)) {
				
				$site->url->redirectTo($url);
				$redirectAway = false;
				
			}
			
			break;
		
	}
	
	if ($redirectAway === true) {
		if ($loggedIn) { 
			$site->url->redirectTo('/' . __oMOD_DEFAULT__);
		} else {
			$site->url->redirectTo('http://www.' . __oCompanyDomain__);
		}
	}