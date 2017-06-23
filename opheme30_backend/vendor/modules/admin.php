<?php

	include 'reseller-admin.php';
	
	switch ($task) {
		
		case 'company':
			
			$action = isset($_post['action'])?$_post['action']:null;
			$id = isset($_post['id']) && is_numeric($_post['id'])?intval($_post['id']):null;
			
			$operationInfo = '<strong>Company - ' . ucfirst($action) . '</strong>' . ($id?' on Company ID <strong>' . $id . '</strong>':'');
			$success = 'Successfully executed ' . $operationInfo . '.';
			$failed = 'Failed to execute ' . $operationInfo . '.';
			$error = true; $addonMsg = '';
			
			if (!empty($_post)) {
				
				if ($id !== 0) {
				
					switch ($action) {

						case 'create':

							if (!empty($_post['users'])) {
								$users = explode(',', rtrim(strtolower($_post['users']), ', ')); $rep = '';
								foreach ($users as $email) {
									$usr = $site->user->getUsersBy('email', $email);
									if (count($usr)) { $usrId = $usr[0]['id'] . ','; } else { $usrId = ''; }
									$rep .= $usrId;
								}
								$repr = rtrim($rep, ',');
								if (!$repr) { $repr = '-1'; }
								unset($users, $email, $usr, $usrId);
							} else { $repr = '-1'; }

							$id = rtrim(strtolower($_post['compId']));
							$modules = explode(',', rtrim(strtolower($_post['modules']), ', '));
							for ($i = 0; $i < count($modules); $i++) { if (!in_array($modules[$i], $availableModules)) { unset($modules[$i]); } }

							$result = $site->company->create($id, $repr, implode(',', $modules));

							if ($result) { $error = false; }

							break;

						case 'edit-modules':
							
							$modules = explode(',', rtrim(strtolower($_post['modules'])));
							for ($i = 0; $i < count($modules); $i++) { if (!in_array($modules[$i], $availableModules)) { unset($modules[$i]); } }

							$result = $site->company->setAvailableModules($id, implode(',', $modules));

							if (is_int($result)) { $error = false; }

							break;
							
						case 'edit-representative':
							
							if (!empty($_post['users'])) {
								$users = explode(',', rtrim(strtolower($_post['users']), ', '));
								$rep = '';
								foreach ($users as $email) {
									$usr = $site->user->getUsersBy('email', $email);
									if (count($usr)) { $usrId = $usr[0]['id'] . ','; } else { $usrId = ''; }
									$rep .= $usrId;
								}
								$repr = rtrim($rep, ',');
								if (!$repr) { $repr = '-1'; }
								unset($users, $email, $usr, $usrId);
							} else { $repr = '-1'; }

							$result = $site->company->setRepresentatives($id, $repr);

							if (is_int($result)) { $error = false; }

							break;

						case 'delete':

							$result = $site->company->remove($id);

							if ($result) { $error = false; }

							break;

						default:
							break;

					}
				
				} else {
					$addonMsg = 'This operation is restricted. Please try again.';
					$error = true;
				}
				
			} else {
				$addonMsg = 'There is some missing information required. Please try again.';
				$error = true;
			}
			
			$msg = ($error?$failed:$success) . $addonMsg;
			$logMsg = $module . ' / ' . $task . ' / ' . $msg;
			
			$site->log->logOperation($site->user->get('account', 'id'), $logMsg, $error);
			$site->message->set($module, $error?'WAR':'OK', 'company_' . $action, $msg);
			
			$site->url->redirectTo('/' . $module);
			break;
		
		default:
			
			if (empty($_post)) {

				/* System Loads */

				$loadsLine = str_replace('  ', ' ', getLastLine(__oLOGS__ . '/load.log'));
				$memoryLine = getLastLine(__oLOGS__ . '/memory.log');
				$cpuCount = file_get_contents(__oLOGS__ . '/cpu.count');

				list($loadsTime, $one, $five, $onefive) = explode(' ', $loadsLine);

				$loads = array(
					'lastCheck' => date('l jS \of F Y h:i:s A', $loadsTime),
					'one' => getLoadPercentage($cpuCount, $one),
					'five' => getLoadPercentage($cpuCount, $five),
					'onefive' => getLoadPercentage($cpuCount, $onefive)
				); unset($loadsTime, $one, $five, $oneFive);

				list($memTime, $total, $used, $free) = explode(' ', $memoryLine);
				$memory = array(
					'lastCheck' => date('l jS \of F Y h:i:s A', $memTime),
					'total' => array(
						'mb' => getMemoryMB($total),
						'gb' => getMemoryGB($total)
					),
					'used' => array(
						'mb' => getMemoryMB($used),
						'gb' => getMemoryGB($used)
					),
					'free' => array(
						'mb' => getMemoryMB($free),
						'gb' => getMemoryGB($free)
					)
				); unset($memTime, $total, $used, $free);

				$moduleData['system'] = array(
					'loads' => $loads,
					'memory' => $memory
				);

				/* Overall Module Status */
				
				$counts = array();
				foreach ($availableJModules as $jModule) {
					$counts[$jModule]['message_count'] = 0; $counts[$jModule]['job_count'] = 0;
				}
				
				for ($i = 0; $i < count($users); $i++) {
					foreach ($availableJModules as $jModule) {
						foreach ($users[$i]['jobs'][$jModule] as $job) {
							$counts[$jModule]['message_count'] += intval($job['message_count']);
							$counts[$jModule]['job_count']++;
						}
					}
				}
				$moduleData['system']['job_stats'] = $counts;

				/* Companies */

				$companies = $site->company->getCompany();

				//remove opheme from the list
				unset($companies[0]);

				for ($i = 1; $i <= count($companies); $i++) {
					$usrArr = explode(',', $companies[$i]['user_id']); $representatives = '';
					foreach ($usrArr as $usrId) {
						if ($usrId === '-1') { continue; }
						$user = $site->user->getUsersBy('id', $usrId)[0];
						$representatives .= $user['email'] . ',';
					}
					$repr = substr($representatives, 0, -1);
					if (!$repr) { $repr = ''; }
					$companies[$i]['representatives'] = $repr;
				}

				$moduleData['companies'] = $companies;

				$moduleData['availableModules'] = implode(', ', $availableModules);
			
			}
			
			break;
		
	}
	
	
	
	