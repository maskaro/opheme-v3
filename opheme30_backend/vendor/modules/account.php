<?php

	switch ($task) {
		
		case 'save-details':

			if (strlen($_post['new-password']) > 0) { //if anything is provided, attempt to validate password
				if (strlen($_post['old-password']) === 0) {
					$site->message->set('account', 'WAR', 'old_password_missing', 'In order to change your account password, please provide your current one.');
					break;
				}
				if ($_post['new-password'] !== $_post['confirm-password']) {
					$site->message->set('account', 'WAR', 'password_mismatch', 'New and confirm Passwords do not match.');
					break;
				}
			}
			
			$result = $site->user->changeAccountDetails($_post, $site->user->get('account', 'id'));

			if ($result === true) {
				
				//get new data and store in session
				$data = array(
					'firstname' => $_post['first-name'],
					'lastname' => $_post['last-name'],
					'phone' => $_post['phone'],
					'business_type' => $_post['business-type'],
					'business_www' => $_post['business-www'],
					'home_location' => $_post['home-location']
				);
				$_SESSION[__oSESSION_USER_PARAM__]['account'] = array_merge($_SESSION[__oSESSION_USER_PARAM__]['account'], $data);
				$_SESSION[__oSESSION_USER_PARAM__]['account']['allSet'] = true;

				$site->message->set('account', 'OK', 'success', 'Account information has been successfully saved.');
				
				if (strlen($_post['new-password']) > 0) {
					$site->url->redirectTo('/logout');
					$site->message->set('account', 'WAR', 'password_changed', 'Password has been successfully changed. Please login using your new password.');
					break;
				}
				
				//refresh internal user info
				$site->user->refreshUserInfo();
				
			} elseif ($result === 1) {
				$site->message->set('account', 'WAR', 'details_missing', 'Your current password is incorrect. Please try again.');
			} elseif ($result === 2) {
				$site->message->set('account', 'WAR', 'details_missing', 'There is missing data in your Details form. Please try again.');
			} else {
				$site->message->set('account', 'WAR', 'database', 'Failed to complete request, database issue. Please report submit a report at ' . __oCompanySupport__ . ' if the problem persists.');
			}
			
			$site->url->redirectTo('/account');
			break;
			
		case 'save-emailNotification':
			
			if (strlen($_post['email-frequency']) > 0 && array_key_exists($_post['email-frequency'], $availableEmailNotificationFrequency)) {
				if (
					$site->user->setStatus(
						array(
							'id' => $site->user->get('account', 'id'),
							'email_notification_frequency' => $_post['email-frequency']
						)
					)
				) {
					//refresh internal user info
					$site->user->refreshUserInfo();
					$site->message->set('account', 'OK', 'success', 'Email Notification Frequency has been successfully changed.');
				}
			} else {
				$site->message->set('account', 'WAR', 'details_missing', 'There is missing data in your Email Notification Frequency form. Please try again.');
			}
			
			$site->url->redirectTo('/dashboard');
			
			break;
		
		default:
			break;
		
	}