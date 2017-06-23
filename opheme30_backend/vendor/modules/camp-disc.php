<?php
	
	$moduleData = array();
	
	if ($module === 'campaign') { $moduleData['cats'] = $_post['cats'] = array('----------', 'Bars', 'Clubs', 'Restaurants', 'Clothing', 'Music', 'General Shopping'); }
	else { $moduleData['lifeSpans'] = $_post['lifeSpans'] = array(__oBACKEND_MESSAGE_AGE_NEW__ => 'New Messages Only', '0' => 'All Messages'); }
	$moduleData['days'] = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

	switch ($task) {
		
		case 'process':
			
			if (isset($_SESSION[__oSESSION_USER_PARAM__]['allowance']['accountTimeLeftSeconds']) && intval($_SESSION[__oSESSION_USER_PARAM__]['allowance']['accountTimeLeftSeconds']) === 0) {
				$site->message->set($module, 'WAR', $module . '_sub_expired', 'Sorry, your account has expired. Please consider upgrading your Subscription.');
				$site->url->redirectTo('/' . $module);
				break;
			}
			
			if (!empty($_post)) {
				
				if (isset($_post['id']) && is_numeric($_post['id'])) {
					
					$do = (intval($_post['id']) > 0?'edit':'create');
					$edit = (is_numeric($_post['id']) && $_post['id'] > -1?true:false);
					
					if ($do === 'create') {
							
						$jobLimit = intval($site->user->get('allowance', $module . 'sLimit'));
						$jobCount = $site->user->get('jobs', $module . 'Count');

						if ($jobLimit > 0 && $jobLimit === $jobCount) {
							$site->url->redirectTo('/' . $module);
							$site->message->set($module, 'WAR', $module . '_' . $do, 'You have reached your ' . ucfirst($module) . ' creation limit. Please remove an existing ' . ucfirst($module) . ' or consider upgrading your subscription.');
							break;
						}

					}
					
					if ($module === 'campaign') {
						if (!empty($_FILES) && isset($_FILES['banner'])) { $_post['uploaded_banner_file'] = $_FILES['banner']; }
					}
					
					$validStatus = $site->{$module}->isValid($_post);
					
					if ($validStatus === true) {
						
						$_post['allowance'] = $site->user->get('allowance');
						$_post['user_id'] = $site->user->get('account', 'id');
					
						$site->{$module}->save($_post, $edit);
							
						if ($do === 'create') {
							$_SESSION[__oSESSION_USER_PARAM__]['jobs'][$module . 'Count']++;
							if (isset($_SESSION[__oSESSION_USER_PARAM__]['allowance'][$module . 'sLeft'])) {
								$_SESSION[__oSESSION_USER_PARAM__]['allowance'][$module . 'sLeft']--;
							}
						}

						$site->message->set($module, 'OK', $module . '_' . $do, ucfirst($module) . ' has been successfully processed.');
						$site->url->redirectTo('/' . $module);
						break;
						
					} else {
						
						for ($i = 0; $i < count($validStatus); $i++) { $site->message->set($module, 'WAR', $module . '_' . $do . '_' . $i, $validStatus[$i]); }
						
						$site->url->redirectTo('/' . $module);
						break;
						
					}
					
				}
			
			}
			
			$site->message->set($module, 'WAR', $module . '_' . $do, ucfirst($module) . ' has some missing information. Please try again.');
			
			$site->url->redirectTo('/' . $module);
			break;
		
		case 'remove':
			
			if (!empty($_post)) {
				
				if (isset($_post['id']) && is_numeric($_post['id'])) {
			
					if ($site->job->remove($module, $_post['id'])) {
						
						$_SESSION[__oSESSION_USER_PARAM__]['jobs'][$module . 'Count']--;
						if (isset($_SESSION[__oSESSION_USER_PARAM__]['allowance'][$module . 'sLeft'])) {
							$_SESSION[__oSESSION_USER_PARAM__]['allowance'][$module . 'sLeft']++;
						}

						$site->message->set($module, 'OK', $module . '_remove', ucfirst($module) . ' has been successfully removed.');
						$site->url->redirectTo('/' . $module);
						break;

					} else {

						$site->message->set($module, 'WAR', $module . '_remove', ucfirst($module) . ' has not been successfully removed. Please contact ' . __oCompanySupport__ . ' for assistance.');
						$site->url->redirectTo('/' . $module);
						break;

					}
					
				}
			
			}
			
			$site->message->set($module, 'WAR', $module . '_remove', ucfirst($module) . ' has some missing information required for removal. Please try again.');
			
			$site->url->redirectTo('/' . $module);
			break;
		
		/**
		 * /MODULE/download/JOBID/DOWNLOADTYPE
		 */
		case 'download':
			
			list($jobId, $downloadType) = explode('/', $extra);
			
			switch($downloadType) {
			
				case 'csv':
					
					// restrict access if not allowed
					if (!$site->company->hasAccessTo('csv')) { 
						$site->url->redirectTo('/' . $module);
						break;
					}
					
					$job = $site->job->getSpecs(array('jobType' => $module, 'id' => $jobId))[0];
					$messages = $site->job->getAllStoredMessages($module, $jobId, ($module === 'campaign'));
					
					if (!empty($job)) {
					
						$tempFile = sys_get_temp_dir() . '/CSV_' . ucfirst($module) . '_' . str_replace(array(' ', '\'', '"'), array('-', '', ''), $job['name']) . '_' . uniqid() . '.csv';
						$csv = new EasyCSV_Writer($tempFile);
						
						$headers = array('#', 'Task_Name', 'Source', 'Message_ID', 'From_User', 'Text', 'Sentiment', 'Klout_Score', 'At_Address', 'Created_At');
						$csv->writeRow($headers);
						$count = 1;

						foreach ($messages as $msg) {
							
							if (!empty($msg['address']) && strlen($msg['address']) > 0) { $address = $msg['address']; }
							else { 
								
								// fetch address
								$address = backend_latLngToAddress($msg['coords'][0], $msg['coords'][1]);
								
								// geocoding succeeded
								if ($address !== false) {
									// store it for future use
									$site->database->query(array(
										'name' => 'updateMessageAddress',
										'type' => 'mongo',
										'table' => 'message.' . $job['authKeyType'] . ($module === 'campaign'?'.sent':''),
										'operation' => 'update',
										'columns' => array(
											'address' => $address
										),
										'where' => array(
											'id_str' => $msg['id_str']
										)
									))->fetch();
								} else {
									$address = $msg['coords'][0] . ' | ' . $msg['coords'][1];
								}
								
							}
									
							$data = array(
								$count++,
								$job['name'],
								ucfirst($msg['smType']),
								$msg['id_str'],
								$msg['user']['screen_name'],
								str_replace(array(','), array(' '), $msg['text']),
								$msg['sentiment'],
								empty($msg['klout']['score'])?'0':$msg['klout']['score'],
								$address,
								$msg['created_at']
							);

							$csv->writeRow($data);

						}
						
						// general download headers
						//header('Content-Type: application/csv');
						header('Content-Type: text/csv');
						header('Content-Disposition: attachment; filename="' . basename($tempFile) . '"');
						header('Pragma: no-cache');
						header('Set-Cookie: fileDownload=true; path=/');
						// for IE 6
						header('Pragma: public', false);
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						
						// offer the file
						readfile($tempFile);
						
						// remove it once done
						unlink($tempFile);
						
						// stop script
						exit;
					
					}
					
					break;
				
				default:
					break;
			
			}
			
			break;
		
		default: //view
			
			$jobs = $site->job->getSpecs(array('jobType' => $module, 'userId' => $site->user->get('account', 'id')));
			
			foreach ($jobs as $key => $job) {
				unset($jobs[$key]['company_id']);
				unset($jobs[$key]['message_count_last_notification']);
				unset($jobs[$key]['since_id']);
				unset($jobs[$key]['messages_limit']);
				unset($jobs[$key]['time_limit']);
				$jobs[$key]['authKey'] = $site->job->getJobTokens($module, $jobs[$key]['id'], true);
			}
			
			$moduleData['jobs'] = $jobs;
			
			break;
		
	}
