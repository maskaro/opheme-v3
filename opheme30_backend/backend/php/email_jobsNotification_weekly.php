<?php
		
	// check if it's time to send the history email
	if ($date['weekday'] === __oEMAIL_NOTIFICATION_WEEKLY_DAY__) {

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
		
		// assume nothing new happened this past week
		$miss = true;
		
		$emailsSent = $site->database->query(array(
			'name' => 'getEmailHistory',
			'type' => 'mysql',
			'table' => 'email.notification.history',
			'operation' => 'select',
			'columns' => array(
				'discover_messages',
				'campaign_messages'
			),
			'where' => array(
				'user_id' => $user['id'],
				'sent_date' => array(
					'operator' => '>=',
					'data' => date('Y-m-d', strtotime('-7 days'))
				)
			)
		))->fetch()['getEmailHistory'];

		// if there are any updates - there should always be
		if ($emailsSent) {

			if (is_assoc_array($emailsSent)) { $emailsSent = array($emailsSent); }

			// jobs list
			$jobsList = array();

			// go through each email update
			foreach ($emailsSent as $email) {
				// go through all available modules
				foreach ($availableJModules as $jModule) {
					// start total messages calculation at 0
					if (!isset($jobsList[$jModule]['messages'])) { $jobsList[$jModule]['messages'] = 0; }
					// extract module jobs messages
					$msgs = explode($separator, $email[$jModule . '_messages']);
					// calculate total number of messages for this module type
					foreach ($msgs as $msgs_count) { $jobsList[$jModule]['messages'] += intval($msgs_count); }
				}
			}
			
			// by default assume there are no updates
			$sendEmailJobs = false;

			// check message count, if at least one module has more than 0, then send out the updates email, else send the miss email
			foreach ($availableJModules as $jModule) {
				if ($jobsList[$jModule]['messages'] > 0) { $miss = false; $sendEmailJobs = true; } /* at least one module has some updates */
				else { unset($jobsList[$jModule]); }
			}

		// nothing new happened this past week
		}
		
		// interaction email control vars
		$sendEmailFollow = false;
		$sendEmailReply = false;
		$sendEmailFavourited = false;
		
		/**
		 * Also get Social Media interaction status for this.
		 */
		$followInfo = $site->socialMedia->getInteraction($user['id'], null, 'follow_in', strtotime('-7 days', $date[0]));
		$replyInfo = $site->socialMedia->getInteraction($user['id'], null, 'message_in', strtotime('-7 days', $date[0]));
		$favouritedInfo = $site->socialMedia->getInteraction($user['id'], null, 'message_out', strtotime('-7 days', $date[0]));
		
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
		
		// set email data
		$emailData = array(
			'email' => array (
				'subject' => $companyBrand . ' weekly report',
				'description' => 'Weekly report from ' . $companyBrand . '.',
				'firstName' => $user['firstname'],
				'email' => $user['email'],
				'loginUrl' => $companyBrandURL . '/login',
				'brand' => $companyBrand
			)
		);
		
		// miss email
		if ($miss === true) {
			
			// ask for miss template
			$emailData['email']['template'] = 'jobs_updates_notification_weekly_miss';
		
		// updates email
		} else {
			
			// ask for updates email
			$emailData['email']['template'] = 'jobs_updates_notification_weekly';
			
			if ($sendEmailJobs) {
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
			
		}

		// send email
		$site->email->sendEmail($emailData);

	}