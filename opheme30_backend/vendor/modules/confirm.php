<?php

	$site->url->redirectTo('/login');
		
	switch ($task) {

		case 'account':

			if (!empty($extra)) {

				if ($site->register->activateAccount($extra)) {

					$addonMsg = '';

					$emailData = array(
						'email' => array (
							'template' => 'activation_success',
							'subject' => 'You have successfully activated your ' . __oCompanyBrand__ . ' Account!',
							'description' => __oCompanyBrand__ . 'Account activation confirmation.',
							'email' => $_post['email'],
							'loginUrl' => __oCompanyBrandURL__ . '/login'
						)
					);

					$site->email->sendEmail($emailData);

					$site->message->set('confirm', 'OK', 'success', 'Account has been successfully activated, you may now login.');

				} else {

					$site->message->set('confirm', 'WAR', 'fail', 'Account has not been activated, incorrect activation code. Please try again. If this issue persists, please get in touch at ' . __oCompanySupport__ . '.');

				}

			}

			break;

		default:
			break;

	}