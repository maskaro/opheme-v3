<?php

	$msgInfo = PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $user['id'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ' / ' . $user['email'] . ' / ' . $user['company']['name'] . ')';
	
	/**
	 * User allowance checks.
	 */
	
	//check account time limit
	if (isset($allowance['accountTimeLimit']) && intval($allowance['accountTimeLimit']) > 0) {
		$until = strtotime('+' . $allowance['accountTimeLimit'], strtotime($user['created']));
		if ($now >= $until) {
			$error_message = ($msgInfo . ' has an associated User Account with ID ' . $user['id'] . ' whose Trial period has Expired. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
			exit;
		}
	}
	
	//check account job TIME LIMIT
	$continue = false;
	if (isset($allowance['jobTimeLimit']) && intval($allowance['jobTimeLimit']) > 0) {
		$until = strtotime('+' . $allowance['jobTimeLimit'], strtotime($job['created_at']));
		if ($until >= $now) { $continue = true; }
	} else { $continue = true; }
	if ($continue === false) {
		$error_message = ($msgInfo . ' time limit expired. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
		exit;
	}
	
	//if message limit has been reached, don't run job
	if (isset($allowance['jobMessageLimit']) && intval($allowance['jobMessageLimit']) > 0) {
		if ($currentMessageCount >= $allowance['jobMessageLimit']) {
			$error_message = ($msgInfo . ' message limit reached. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
			exit;
		}
	}
	
	/**
	 * Job checks.
	 */
	
	//check DATE
//	$job['start_date'] = explode(' ', $job['start_date'])[0];
//	$job['end_date'] = explode(' ', $job['end_date'])[0];
//	
//	if (strlen($job['start_date']) && strlen($job['end_date'])) {
//		$continue = false;
//		$start = strtotime($job['start_date']);
//		$end = strtotime($job['end_date']);
//		$today = strtotime(date('Y-m-d'));
//		if ($today >= $start && $today <= $end) { $continue = true; }
//		if ($continue === false) { 
//			$error_message = ($msgInfo . ' running Dates are not current. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
//			exit;
//		}
//	}
//	
//	//check WEEKDAY
//	if (strlen($job['weekdays']) > 0) {
//		$continue = false;
//		$weekdays = explode(',', $job['weekdays']);
//		$today = getDayOfWeek();
//		foreach ($weekdays as $day) { if ($day === $today) { $continue = true; break; } }
//		if ($continue === false) { 
//			$error_message = ($msgInfo . ' running Weekdays has none that match Today (' . $today . '). Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
//			exit;
//		}
//	}
//	
//	//check TIMEs
//	if ($job['start_time'] !== '00:00:00' || $job['end_time'] !== '00:00:00') {
//		$continue = false;
//		$reference_time = date('H:i');
//		$absolute = strtotime($reference_time);
//		if (
//			strtotime($job['start_time'], $absolute) <= $absolute
//			&&
//			strtotime($job['end_time'], $absolute) >= $absolute
//		) { $continue = true; }
//		if ($continue === false) {
//			$error_message = ($msgInfo . ' running Times are not yet current. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);
//			exit;
//		}
//	}