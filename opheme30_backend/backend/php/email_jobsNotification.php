<?php
	
	/**
	 * Running checks.
	 */
	
	// get current datetime info
	//$date = getdate();
	// form ideal running time and get the integer value of it
	//$runningTimeCheck = intval($date['hours'] . $date['minutes'] . $date['seconds']);
	// only run this if it's between exactly between midnight and 30 seconds past
	//if ($runningTimeCheck > 30) { exit; }
	
	if (time() - strtotime("today midnight") > 30) { exit; }

	/**
	 * Base oPheme directory.
	 */
	define('__oDIR__', $argv[1]);
	
	//get global functionality
	require_once(__oDIR__ . '/vendor/includes.inc.php');
	
	/**
	 * @var oPheme oPheme handle.
	 */
	$site = new oPheme();
	
	// current timestamp
	$currentTimestamp = time();
	
	// field separator in the database table columns
	$separator = '^';
	
	// get all system users
	$users = $site->user->getUsersBy();
	
	// go through each user
	foreach ($users as $user) {
			
		// jobs array with type, name, and new messages
		$jobsList = array();

		// jobs email control var
		$sendEmailJobs = false;

		// go through each job of all the available types
		foreach ($availableJModules as $jModule) {

			// get job details
			$allJobsOfType = $site->job->getSpecs(array('jobType' => $jModule, 'userId' => $user['id']));

			// if there are any jobs of this type
			if (count($allJobsOfType)) {

				// set up jobsList array for current module
				$jobsList[$jModule] = array();

				// go through each job of current type
				foreach ($allJobsOfType as $jobTmp) {
					
					$jobTmp['message_count'] = $site->job->getJobMessageCount($jModule, $jobTmp['id']);

					// number of new messages
					$newMsgs = $jobTmp['message_count'] - $jobTmp['message_count_last_notification'];

					// check if there are any new messages
					if ($newMsgs > 0) {

						// add to jobsList array
						$jobsList[$jModule][] = array(
							'name' => $jobTmp['name'],
							'messages' => $newMsgs
						);

						// set new last message count
						$site->database->query(array(
							'name' => 'setMessageCount',
							'type' => 'mysql',
							'table' => 'app.' . $jModule . '.jobs',
							'operation' => 'update',
							'columns' => array(
								'message_count_last_notification' => $jobTmp['message_count']
							),
							'where' => array(
								'id' => $jobTmp['id']
							)
						))->fetch();

						// set email flag
						$sendEmailJobs = true;

					} // end newMsgs if

				} // end job foreach

			} // end count if

		} // end module foreach
		
		// control report saving
		$saveReport = false;
		
		// compile discover history info
		if (isset($jobsList['discover'])) {
			$discNamesTmp = ''; $discMsgsTmp = '';
			foreach ($jobsList['discover'] as $disc) { $discNamesTmp .= $disc['name'] . $separator; $discMsgsTmp .= $disc['messages'] . $separator; }
			$discNames = rtrim($discNamesTmp, $separator); $discMsgs = rtrim($discMsgsTmp, $separator);
			unset($discNamesTmp, $discMsgsTmp);
			$saveReport = true;
		} else { $discNames = ''; $discMsgs = ''; }

		// compile campaign history info
		if (isset($jobsList['campaign'])) {
			$campNamesTmp = ''; $campMsgsTmp = '';
			foreach ($jobsList['campaign'] as $camp) { $campNamesTmp .= $camp['name'] . $separator; $campMsgsTmp .= $camp['messages'] . $separator; }
			$campNames = rtrim($campNamesTmp, $separator); $campMsgs = rtrim($campMsgsTmp, $separator);
			unset($campNamesTmp, $campMsgsTmp);
			$saveReport = true;
		} else { $campNames = ''; $campMsgs = ''; }
		
		// check whether the reports needs to be stored
		if ($saveReport) {
			
			// save daily report
			$site->database->query(array(
				'name' => 'insertEmailNotification',
				'type' => 'mysql',
				'table' => 'email.notification.history',
				'operation' => 'insert',
				'columns' => array(
					'user_id' => $user['id'],
					'discover_names' => $discNames,
					'discover_messages' => $discMsgs,
					'campaign_names' => $campNames,
					'campaign_messages' => $campMsgs,
					'sent_date' => date('Y-m-d')
				)
			))->fetch();
			
		}
		
		// interaction email control vars
		$sendEmailInteraction = false;
		$sendEmailFollow = false;
		$sendEmailReply = false;
		$sendEmailFavourited = false;
		
		/**
		 * Also get Social Media interaction status for this.
		 */
		$followInfo = $site->socialMedia->getInteraction($user['id'], null, 'follow_in', $user['email_notification_last_timestamp']);
		$replyInfo = $site->socialMedia->getInteraction($user['id'], null, 'message_in', $user['email_notification_last_timestamp']);
		$favouritedInfo = $site->socialMedia->getInteraction($user['id'], null, 'message_out', $user['email_notification_last_timestamp']);
		
		// interactions available
		if (count($followInfo)) {
			$sendEmailFollow = true; $sendEmailInteraction = true;
		}
		if (count($replyInfo)) {
			$sendEmailReply = true; $sendEmailInteraction = true;
		}
		if (count($favouritedInfo)) {
			foreach ($favouritedInfo as $fav) {
				if ($fav['favourited'] > 0) {
					$sendEmailFavourited = true; $sendEmailInteraction = true;
					break;
				}
			}
		}
		
		// get number of days since last notification
		$daysSince = intval(intval($currentTimestamp - $user['email_notification_last_timestamp']) / (3600*24));

		// check whether an updates email needs to be sent
		if (
			$daysSince >= intval($user['email_notification_frequency']) &&	// user frequency preference timeout		AND
			intval($user['email_notification_frequency']) !== 0 &&			// user frequency is set to at least 1 Day	AND
			($sendEmailJobs === true || $sendEmailInteraction === true) &&	// updates email needs to be sent out		AND
			$date['weekday'] !== __oEMAIL_NOTIFICATION_WEEKLY_DAY__			// it's not the same day as the weekly updates email
		) {

			// get user company details
			$companyId = $site->company->getCompany($user['from_company_id'])[0]['company_id'];
			// company files location
			$companyFiles = __oDIR__ . '/rebrands/' . $companyId;
			// get company brand
			$companyBrand = file_get_contents($companyFiles . '/words/brand_name.inc');
			// get company domain
			$companyDomain = file_get_contents($companyFiles . '/words/domain_name.inc');
			// get company brand url
			$companyBrandURL = 'http' . (__oSSL_ENABLED__ === true?'s':'') . '://' . (__oApp__ === true?'app':'portal') . '.' . $companyDomain;
			
			// set email data
			$emailData = array(
				'email' => array (
					'template' => 'jobs_updates_notification',
					'subject' => 'Activity report for your ' . $companyBrand . ' tasks',
					'description' => 'Activity report for your ' . $companyBrand . ' tasks',
					'firstName' => $user['firstname'],
					'email' => $user['email'],
					'loginUrl' => $companyBrandURL . '/login',
					'brand' => $companyBrand
				)
			);
			
			if ($sendEmailJobs === true) {
				$emailData['email']['jobs'] = $jobsList;
			}
			
			if ($sendEmailFollow === true) {
				$emailData['email']['followInfo'] = $followInfo;
			}
			
			if ($sendEmailReply === true) {
				$emailData['email']['replyInfo'] = $replyInfo;
			}
			
			if ($sendEmailFavourited === true) {
				$emailData['email']['favouritedInfo'] = $favouritedInfo;
			}

			// send email
			if ($site->email->sendEmail($emailData)) {

				// set new last message count
				$site->database->query(array(
					'name' => 'updateEmailTimestamp',
					'type' => 'mysql',
					'table' => 'user.accounts',
					'operation' => 'update',
					'columns' => array(
						'email_notification_last_timestamp' => $currentTimestamp
					),
					'where' => array(
						'id' => $user['id']
					)
				))->fetch();

			}
			
		}
		
		// weekly email reports
		include(__oDIR__ . '/backend/php/email_jobsNotification_weekly.php');
		
	}