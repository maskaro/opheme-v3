<?php

	switch($task) {
		
		case 'check':
			
			if (empty($_post['email']) || empty($_post['password'])) {
				$site->message->set('login', 'WAR', 'empty_fields', 'Please make sure you fill in both Email and Password fields.');
				break;
			}
			
			if(!filter_var($_post['email'], FILTER_VALIDATE_EMAIL)) {
				$site->message->set('login', 'WAR', 'invalid_email', 'Invalid E-Mail Address.');
				break;
			}
			
			$data = $site->login->generate($_post['email'], $_post['password']);
			
			//success
			if (is_array($data)) {
				
				//first login - activate account, and remove login token
				if ($data['account']['code'] === '1') {
					$site->register->activateAccount($data['account']['id']);
					$site->token->remove($_post['password']);
				}
				
				//set jobs info
				$data['jobs']['discoverCount'] = $site->job->count('discover', $data['account']['id']);
				$data['jobs']['campaignCount'] = $site->job->count('campaign', $data['account']['id']);
				
				//all details set boolean
				$allSet = true;
				foreach ($data['account'] as $value) { if (!strlen($value)) { $allSet = false; break; } }
				$data['account']['allSet'] = $allSet;
				
				$data['timestamp'] = time();
				
				$site->log->logOperation($data['account']['id'], 'login / check / Successful from IP <' . $site->user->getIP() . '>.');
				
				//regenerate session ID before setting any new data to it, and delete old one
				session_regenerate_id(true);
				
				$_SESSION[__oSESSION_USER_PARAM__] = $data; unset($data);
				
				$authorised = false;
				foreach ($availableSMModules as $smModule) {
					if ($site->{$smModule}->countTokens('user', $_SESSION[__oSESSION_USER_PARAM__]['account']['id'])) {
						$authorised = true; break;
					}
				}
				
				if (!$authorised || !$allSet) {
					$site->url->redirectTo('/account');
				} else {
					$site->url->redirectTo('/' . __oMOD_DEFAULT__);
				}
				
				break;
				
			//fail
			} else {
				
				switch ($data) {
					
					case 1:
						$site->message->set('login', 'WAR', 'account_suspended', 'Account has been suspended. For more information please contact us.');
						break;
					
					case 2:
						$site->message->set('login', 'WAR', 'login_too_many_attempts', 'Too many logins. Please try again in 2 hours.');
						break;
					
					case 3:
						$site->message->set('login', 'WAR', 'login_invalid_details', 'Incorrect email or password.');
						break;
					
					case 4:
						$site->message->set('login', 'WAR', 'login_inactive', 'You must first Activate your account before logging in. Please also check your SPAM folder if you have not yet received your activation Email. If this issue persists, please get in touch at ' . __oCompanySupport__ . ' for assitance.');
						break;
					
					default:
						break;
					
				}
				
			}
			
			$site->url->redirectTo('/login');
			
			break;
		
		case 'reset-password':
			
			if(empty($_post['email']) || !filter_var($_post['email'], FILTER_VALIDATE_EMAIL)) {
				$site->message->set('login', 'WAR', 'invalid_email', 'Invalid E-Mail Address.');
				break;
			}
			
			$password = $site->login->resetPassword($_post['email']);

			if (is_string($password)) {
				
				$addonMsg = '';
					
				$emailData = array(
					'email' => array (
						'template' => 'password_reset_success',
						'subject' => 'You have successfully reset your ' . __oCompanyBrand__ . ' Account password!',
						'description' => 'Password reset confirmation of your ' . __oCompanyBrand__ . ' Account.',
						'email' => $_post['email'],
						'password' => $password,
						'loginUrl' => __oCompanyBrandURL__ . '/login'
					)
				);

				//attempt to send confirmation email
				if (!$site->email->sendEmail($emailData)) {
					$addonMsg = ' However, Email confirmation could not be sent. Your new Password is: <br><strong>' . $password . '</strong>';
				} else {
					$addonMsg = ' You will receive an Email within a few minutes containing your new Password.';
				}
				
				$site->log->logOperation(-1, 'login / reset-password / Successful from IP <' . $site->user->getIP() . '> with email <' . $_post['email'] . '>.');
				
				$site->message->set('login', 'OK', 'reset_password_success', 'You have successfully reset your Account\'s Password! ' . $addonMsg);

			} elseif ($password === false) {
				$site->message->set('login', 'WAR', 'reset_password_database', 'This email does not exist on our System. If this issue persists, please get in touch at ' . __oCompanySupport__ . ' for assitance.');
			}
			
			$site->url->redirectTo('/login');
			
			break;
		
		default:
			break;
		
	}