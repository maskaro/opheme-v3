<?php

	$site->url->redirectTo('/login?register');

	 if (!empty($_post)) {
		
		switch ($task) {
			
			case 'process':
				
				/*if (empty($_SESSION['captcha_code'])) {
					$site->message->set('register', 'WAR', 'no_captcha', 'SOmething went wrong on our end regarding the Captcha code. Please try again later.');
					break;
				}
				
				if(strcasecmp($_SESSION['captcha_code'], $_post['captcha_code']) != 0) {
					$site->message->set('register', 'WAR', 'no_token', 'Incorrect Captcha code.');
					break;
				}*/
				
				if(empty($_post['email']) || !filter_var($_post['email'], FILTER_VALIDATE_EMAIL)) {
					$site->message->set('register', 'WAR', 'email_invalid', 'Invalid E-Mail Address.');
					break;
				}
				
				$_SESSION['formData']['email'] = $_post['email'];
				
				if (empty($_post['firstName'])) {
					$site->message->set('register', 'WAR', 'no_fname', 'Please enter your First Name so we can better personalise your experience..');
					break;
				}
				
				$_SESSION['formData']['firstName'] = $_post['firstName'];
				
				if (empty($_post['password'])) {
					$site->message->set('register', 'WAR', 'no_password', 'Please enter a password.');
					break;
				}

				if ($_post['password'] !== $_post['confirm-password']) {
					$site->message->set('register', 'WAR', 'password_mismatch', 'Passwords do not match.');
					break;
				}

				if (empty($_post['terms'])) {
					$site->message->set('register', 'WAR', 'no_terms', 'Please read and agree to our Terms & Conditions.');
					break;
				}
				
				$_SESSION['formData']['terms'] = $_post['terms'];

				/*if (!empty($_post['token'])) {
					/* PURGE OLD TOKENS BEFORE CHECKING ANYTHING *
					$site->token->purgeOld(3);
					if (!$site->token->isValid($_post['token'], $_post['email'])) {
						$site->message->set('register', 'WAR', 'token_invalid', 'Invalid Token. Please enter your Secret Token correctly (check the Token Email you received). Tokens expire after 3 days.');
						break;
					} else {
						$token = $_post['token'];
					}
				} else {
					$token = null;
				}*/
				
				$registration = $site->register->newAccount($_post['firstName'], $_post['email'], $_post['password'], true);

				if (is_int($registration)) {
					
					switch ($registration) {
						
						case 1:
							$site->message->set('register', 'WAR', 'email_exists', 'E-Mail Address has already been registered. We suggest using the <strong>Forgot Password</strong> feature located below.');
							unset($_SESSION['formData']);
							$site->url->redirectTo('/login');
							break;
						
						default:
							break;
						
					}

					break;

				} elseif (is_string($registration)) {

					//if (!empty($token)) { $site->token->remove($token); }
					
					$addonMsg = ''; unset($_SESSION['formData']);
					
					$emailData = array(
						'email' => array (
							'template' => 'registration_success_self',
							'subject' => 'You have successfully created an Account with ' . __oCompanyBrand__ . '!',
							'description' => __oCompanyBrand__ . 'Account creation confirmation.',
							'email' => $_post['email'],
							'confirmUrl' => __oCompanyBrandURL__ . '/confirm/account/' . $registration
						)
					);

					//attempt to send confirmation email
					if (!$site->email->sendEmail($emailData)) {
						$addonMsg = ' Sadly, an Email containing your account activation instructions could not be sent. Please get in touch at ' . __oCompanySupport__ . ' for assitance.';
					} else {
						$addonMsg = ' You will now receive an Email within a few minutes containing activation instructions you will need to follow. This will allow you to enjoy your new ' . __oCompanyBrand__ . ' account.';
					}
					
					$site->message->set('register', 'OK', 'success', 'Account has been successfully created.' . $addonMsg);
					$site->url->redirectTo('/login');

				} else {

					$site->message->set('register', 'WAR', 'database', 'Could not create Account due to our Database taking a short break. Please try again later. If this issue persists, please get in touch at ' . __oCompanySupport__ . ' for assitance.');
					$site->url->redirectTo('/login');

				}
				
				break;
			 
			default:
				break;
			 
		}
    
    }