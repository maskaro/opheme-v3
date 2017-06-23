<?php

	switch ($task) {
		
		case 'process':
			
			$action = isset($_post['action'])?$_post['action']:null;
			$id = isset($_post['id']) && is_numeric($_post['id'])?intval($_post['id']):null;
				
			$operationInfo = '<strong>' . ucfirst($extra) . ' - ' . ucfirst($action) . '</strong>' . ($id?' on ' . ucfirst($extra) . ' ID <strong>' . $id . '</strong>':'');
			$success = 'Successfully executed ' . $operationInfo . '.';
			$failed = 'Failed to execute ' . $operationInfo . '.';
			$error = true; $addonMsg = '';
			
			if (!empty($_post) && $extra ) {
				
				switch ($extra) {

					case 'campaign':
					case 'discover':
						
						switch ($action) {
						
							case 'suspend':
								$result = $site->job->setStatus(array('jobType' => $extra, 'id' => $id, 'suspended' => 1));
								break;
							
							case 'resume':
								$result = $site->job->setStatus(array('jobType' => $extra, 'id' => $id, 'suspended' => 0));
								break;
							
							case 'delete':
								$result = $site->job->remove($extra, $id);
								break;
							
							default:
								$result = -1;
								break;
						
						}
						
						break;
						
					case 'user':

						switch ($action) {
						
							case 'suspend':
								$result = $site->user->setStatus(array('id' => $id, 'suspended' => 1));
								break;
							
							case 'resume':
								$result = $site->user->setStatus(array('id' => $id, 'suspended' => 0));
								break;
							
							case 'resetTrial':
								$result = $site->user->setStatus(array('id' => $id, 'created' => date('Y-m-d H:i:s', time())));
								break;
							
							case 'resetPassword':
								
								$usr = $site->user->getUsersBy('id', $id)[0];
								$password = $site->login->resetPassword($usr['email']);

								if (is_string($password)) {

									//if company has access to emailing capabilities
									if ($site->company->hasAccessTo('email')) {

										$emailData = array(
											'email' => array (
												'template' => 'password_reset_success',
												'subject' => 'You have successfully reset your ' . __oCompanyBrand__ . ' Account password!',
												'description' => 'Password reset confirmation of your ' . __oCompanyBrand__ . ' Account.',
												'email' => $usr['email'],
												'password' => $password,
												'loginUrl' => __oCompanyBrandURL__ . '/login'
											)
										);

										//attempt to send confirmation email
										if (!$site->email->sendEmail($emailData)) {
											$result = -6;
										} else {
											$result = true;
										}

									} else {
										$result = 11;
									}

								} elseif ($password === false) { $result = false; }
								
								break;
							
							case 'activate':
								$result = $site->user->setStatus(array('id' => $id, 'code' => '0'));
								break;
							
							case 'changeSub':
								$allowance = $site->user->refreshAllowance(true, $_post['sub_id']);
								$allowance['jobMessageLimit'] = (isset($allowance['jobMessageLimit'])?$allowance['jobMessageLimit']:0);
								$allowance['jobTimeLimit'] = (isset($allowance['jobTimeLimit'])?$allowance['jobTimeLimit']:'0 hours');
								foreach ($availableJModules as $mod) {
									$jobs = $site->job->getSpecs(array('jobType' => $mod, 'userId' => $id));
									foreach ($jobs as $jb) { 
										$site->job->setStatus(array(
											'jobType' => $mod,
											'id' => $jb['id'],
											'messages_limit' => $allowance['jobMessageLimit'],
											'time_limit' => $allowance['jobTimeLimit']
										));
									}
								}
								$result = $site->user->setStatus(array('id' => $id, 'subscription' => $_post['sub_id']));
								break;
							
							case 'delete':
								foreach ($availableJModules as $mod) {
									$jobs = $site->job->getSpecs(array('jobType' => $mod, 'userId' => $id));
									foreach ($jobs as $jb) { 
										$site->job->remove($mod, $jb['id']);
									}
								}
								$site->token->removeForUser($site->user->getUsersBy('id', $id)[0]['email']);
								$result = $site->user->remove($id);
								if ($result) { $site->user->removeSessions($id); }
								break;
							
							default:
								$result = -1;
								break;
						
						}
						
						if ($result === true) {
							$result = 10;
						} elseif ($result === false) {
							$result = 9;
						}
						
						break;
						
					case 'token':
						
						switch ($action) {

							case 'create':

								if (isset($_post['email'])) {
									
									if (!$site->register->emailExists($_post['email'])) {

										$result = $site->token->create($_post['email'], $site->company->getCompany(__oCompanyID__)[0]['id']);

										if (is_string($result)) {
											
											if ($site->register->newAccount('', $_post['email'], $result)) {

												//if company has access to emailing capabilities
												if ($site->company->hasAccessTo('email')) {

													$emailData = array(
														'email' => array (
															'template' => 'registration_success',
															'subject' => 'Your ' . __oCompanyBrand__ . ' Account has been Created!',
															'description' => __oCompanyBrand__ . ' Account creation confirmation.',
															'regToken' => $result,
															'email' => $_post['email'],
															'loginUrl' => __oCompanyBrandURL__ . '/login'
														)
													);

													//attempt to send confirmation email
													if (!$site->email->sendEmail($emailData)) {
														$result = -2;
													} else {
														$result = 10;
													}

												} else {
													$result = 10;
												}
												
											} else {
												$result = -5;
											}

										} elseif ($result === 1) {
											$result = -4;
										} else {
											$result = 9;
										}
										
									} else {
										$result = -3;
									}

								} else {
									$result = -1;
								}

								break;

							case 'delete':
								$result = $site->token->remove($id);
								break;

							default:
								$result = -1;
								break;

						}
						
						break;

					default:
						$result = -1;
						break;

				}
				
				switch ($result) {
					
					case 10:
						$addonMsg = '';
						$error = false;
						break;
					
					case -1:
						$addonMsg = ' Missing information. Please try again.';
						break;
					
					case -2:
						$addonMsg = ' However, Email confirmation could not be sent to Client. Please contact ' . __oCompanySupport__ . ' for assistance.';
						break;
					
					case -3:
						$addonMsg = ' Client Email already exists on the System. Please try again.';
						break;
					
					case -4:
						$addonMsg = ' Invalid Client Email. Please try again.';
						break;
					
					case -5:
						$addonMsg = ' However, the System failed to create new Client Account.';
						break;
					
					case -6:
						$addonMsg = ' However, Email confirmation could not be sent. The new password is: <strong>' . $password . '</strong>.';
						break;
					
					case 11:
						$addonMsg = ' The new password is: <strong>' . $password . '</strong>.';
						$error = false;
						break;
					
					default:
						$addonMsg = ' Database exception. Please contact ' . __oCompanySupport__ . ' for assistance.';
						break;
					
				}
			
			} else {
				$addonMsg = 'There is some missing information required. Please try again.';
				$error = true;
			}
			
			$msg = ($error?$failed:$success) . $addonMsg;
			$logMsg = $module . ' / ' . $extra . ' / ' . $msg;
			
			$site->log->logOperation($site->user->get('account', 'id'), $logMsg, $error);
			$site->message->set($module, ($error?'WAR':'OK'), $extra . '_' . $action, $msg);
			
			$site->url->redirectTo('/' . $module);
			break;
		
		default: //view
			
			if (empty($_post)) {
			
				$id = $site->company->getCompany(__oCompanyID__)[0]['id'];

				if ($module === 'admin') { $by = null; $tokens = $site->token->get(null, null); }
				else { $by = 'from_company_id'; $tokens = $site->token->get(null, $id); }

				$users = $site->user->getUsersBy($by, $id);

				for ($i = 0; $i < count($users); $i++) {
					
					foreach ($availableJModules as $jModule) {
						
						$jobs = $site->job->getSpecs(array('jobType' => $jModule, 'userId' => $users[$i]['id']));
						
						for($j = 0; $j < count($jobs); $j++) {
							
							$jobs[$j]['message_count'] = $site->job->getJobMessageCount($jModule, $jobs[$j]['id']);
							
						}
						
						$users[$i]['jobs'][$jModule] = $jobs;
						
					}

				}

				$subs = $site->getSubs();

				$moduleData['users'] = $users;
				$moduleData['subs'] = $subs;
				$moduleData['tokens'] = $tokens;
			
			}
			
			break;
		
	}