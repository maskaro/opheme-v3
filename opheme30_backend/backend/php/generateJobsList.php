#!/usr/bin/php -q
<?php
	
// TODO: test this with php /home/vagrant/Code/backend/php/generateJobsList.php "/home/vagrant/Code/" "/home/vagrant/jobs.txt"

	/**
	 * Base oPheme directory.
	 */
	define('__oDIR__', $argv[1]);
	
	/**
	 * Jobs file to write to.
	 */
	define('__oJOBS_FILE__', $argv[2]);
	
	//get global functionality
	require_once(__oDIR__ . '/vendor/includes.inc.php');
	
	/**
	 * @var oPheme oPheme handle.
	 */
	$site = new oPheme();
	
	/**
	 * Output text.
	 */
	$text = '';
	
	//id/MODULE/COMPANY PHP_EOL
//	foreach ($availableJModules as $mod) {
		foreach ($site->job->getSpecs(array('simple' => true)) as $job) {
			if (intval($job['running']) === 1) {
//				$text .= $job['id'] . '!' . strtoupper($mod) . '!' . $job['company_id'] . PHP_EOL;
				$text .= $job['id'] . '/DISCOVER/opheme' . PHP_EOL;
			}
		}
//	}
	
	/**
	 * Calculate average time of messages and Check Social Media for Interaction stats.
	 */
	
	//id!MODULE!TYPE/DAYS_REFRESH/DAYS_CHECK PHP_EOL
	foreach ($availableSMModules as $mod) {
		foreach ($site->{$mod}->getUserTokens('all', null, true) as $token) {
//			$text .= $token['id'] . '/MSGAVG/' . $mod . PHP_EOL;
			$text .= $token['id'] . '/SMCHECKINTERACTION/' . $mod . PHP_EOL;
		}
	}

	//write to file
	file_put_contents(__oJOBS_FILE__, $text);
	
	exit;