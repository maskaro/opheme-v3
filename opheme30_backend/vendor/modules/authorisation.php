<?php

	//if ($task) { 
		$site->url->redirectTo('/account');
	//}
		
	switch($extra) {
		
		case 'remove':
			
			if (isset($_post['token-id']) && in_array($task, $availableSMModules)) {
				
				$tokenRemove = $site->{$task}->getUserTokens('one', $_post['token-id'], true);
				
				if ($site->{$task}->removeToken(false, $_post['token-id'])) {
					
					$data = array(
						'name' => 'deleteJobSMKeys',
						'type' => 'mysql',
						'operation' => 'delete',
						'table' => 'app.jobs.tokens',
						'where' => array(
							'token_type' => $task,
							'token_id' => $_post['token-id']
						)
					);
					$site->database->query($data)->fetch();
					
					$tokenName = $tokenRemove['screen_name'];
							
					$addonMsg = '';

					$emailData = array(
						'email' => array (
							'template' => $task . '_delinked_account_success',
							'subject' => 'You have successfully delinked your ' . ucfirst($task) . ' Account from your ' . __oCompanyBrand__ . ' Account!',
							'description' => ucfirst($task) . ' Account delink from your ' . __oCompanyBrand__ . ' Account confirmation.',
							'tokenName' => $tokenName,
							'email' => $site->user->get('account', 'email')
						)
					);

					//attempt to send confirmation email
					if (!$site->email->sendEmail($emailData)) {
						$addonMsg = ', however, Email confirmation could not be sent';
					}

					//refresh tokens
					$_SESSION[__oSESSION_USER_PARAM__]['authorisation'][$task] = $site->{$task}->getUserTokens('all', $site->user->get('account', 'id'), true);
					
					if (count($_SESSION[__oSESSION_USER_PARAM__]['authorisation'][$task]) === 1) {
						
						$token = $_SESSION[__oSESSION_USER_PARAM__]['authorisation'][$task][0];
						
						foreach ($availableJModules as $jModule) {
							
							$site->database->query(array(
								'name' => 'updateJobToken',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'app.jobs.tokens',
								'columns' => array(
									'token_id' => $token['id']
								),
								'where' => array(
									'job_type' => $jModule,
									'token_type' => $task,
									'user_id' => $site->user->get('account', 'id')
								)
							))->fetch();
							
						}
						
					}

					$site->message->set('authorisation', 'OK', $task . '_remove', 'Authorisation token has been successfully removed' . $addonMsg . '.');
					break;

				}

				$site->message->set('authorisation', 'WAR', $task . '_remove', 'Authorisation token could not be removed. Please contact ' . __oCompanySupport__ . ' for assistance.');
				break;
				
			}
			
			$site->message->set('authorisation', 'WAR', $task . '_remove', 'No Authorisation Token selected. Please try again.');
			break;
		
		default:
			
			$site->message->set('authorisation', 'WAR', $task . '_' . $extra, 'Unrecognised action (' . $extra . '). Please try again.');
			break;
		
	}