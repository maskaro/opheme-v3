#!/usr/bin/php -q
<?php

// TODO: test this with php /home/vagrant/Code/backend/lib/modules/DISCOVER/apiRequest.php '0a35c980-3049-4e55-ac62-74bc45528e42/DISCOVER/opheme' '/home/vagrant/Code/'

	/**
	 * Current time. Timestamp.
	 */
	$now = time();
	
	/**
	 * Backend Usage
	 */
	define('__oBACKEND__', true);
	
	/**
	 * Current Entity ID, Module, and Extra information.
	 */
	@list($id, $mod, $extra, $extra2, $extra3) = explode('/', $argv[1]); $module = strtolower($mod);
	
	$error_log = __oDIR__ . str_replace('=', '/', $extra2) . '.log';
	
	fopen($error_log, 'a');

	//get global functionality
	require_once(__oDIR__ . '/vendor/includes.inc.php');
	
	include('uuid.php');
	
	/**
	 * @var oPheme oPheme handle.
	 */
	$site = new oPheme($error_log);
	
	$site->database->queryMySQLCustom(array(
		'query' => 'SET foreign_key_checks = 0',
		'no_return' => true
	));
	
	switch ($module) {
		
		case 'discover':
//		case 'campaign':
			
			$jobId = $id; $companyId = $extra; unset($id, $extra);
	
			/**
			* Current Job full specs from Database.
			*/
			$jobCheck = $site->job->getSpecs(array('id' => $jobId));
			
			if (!empty($jobCheck)) {
				
				$job = $jobCheck[0];

//				if (intval($job['running']) === 1) {

					$user = $job['user'];

					if (intval($user['suspended']) === 0) {

//						$allowance = $site->user->refreshAllowance(true, $user['subscription'], $user['created']);
						$allowance = array(
							'jobMessageLimit' => 150
						);

						//current message count
						$currentMessageCount = $site->job->getJobMessageCount($job['id']);
						
						/* USER LIMIT CHECKS - this may terminate the script */
						include(__oDIR__ . '/backend/php/backend_checks.php');
						
						$tokens = $site->job->getJobTokens($module, $jobId);
						
						if (count($tokens)) {
							
							foreach ($tokens as $token) {

								$tokenType = $token['type'];
								$tokenId = $token['id'];

								// for instagram
								if (empty($token['token_secret'])) { $token['token_secret'] = null; }

								$site->$tokenType->startOAuth($token['token'], $token['token_secret']);

								/**
								 * Load the required Social Media apiRequest module based on Authorisation Token Type.
								 */
								include(__oDIR__ . '/backend/php/' . $tokenType . '_apiRequest.php');

							}
						
							// update last check and message count
							$site->job->setStatus(array('jobType' => $module, 'id' => $job['id'], 'last_check' => date('Y-m-d H:i:s', $now)));

						} else { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' has invalid Token IDs attached. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }

					} else { $error_message = (PHP_EOL . PHP_EOL . 'Account with ID ' . $user['id'] . ' and Email ' . $user['email'] . ' suspended. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);}

//				} else { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' suspended. Skipped.' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);  }
			
			} else { $error_message = (PHP_EOL . PHP_EOL . 'WARNING: Invalid Job ID (' . $jobId . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);  }
			
			break;
			
//		case 'msgavg':
//			
//			$tokenId = $id; $tokenType = $extra; unset($id, $extra);
//			
//			switch ($tokenType) {
//				
//				case 'twitter':
//				case 'instagram':
//					
//					/**
//					 * Current Token full specs from Database.
//					 */
//					$token = $site->{$tokenType}->getUserTokens('one', $tokenId);
//
//					if (!empty($token)) {
//					   
//						// for instagram
//						if (!isset($token['token_secret'])) { $token['token_secret'] = null; }
//					   
//						$site->{$tokenType}->startOAuth($token['token'], $token['token_secret']);
//						
//						/**
//						 * Load the required Social Media averageTimeOfMessages module based on Authorisation Token Type.
//						 */
//						include(__oDIR__ . '/backend/php/' . $tokenType . '_averageTimeOfMessages.php');
//
//					} else { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' WARNING: Invalid Token ID (' . $tokenId . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);  }
//					
//					break;
//				
//				default:
//					$error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' WARNING: Invalid Token Type (' . $tokenType . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); 
//					break;
//					
//				
//			}
//			
//			break;
			
		case 'smcheckinteraction':
			
			$tokenId = $id; $tokenType = $extra; unset($id, $extra);
			
			switch ($tokenType) {
				
				case 'twitter':
				case 'instagram':
					
					/**
					 * Current Token full specs from Database.
					 */
					$token = $site->{$tokenType}->getUserTokens('one', $tokenId);

					if (!empty($token)) {
						
						// for instagram
						if (!isset($token['token_secret'])) { $token['token_secret'] = null; }
					   
						$site->{$tokenType}->startOAuth($token['token'], $token['token_secret']);
						
						/**
						 * Load the required Social Media checkInteraction module based on Authorisation Token Type.
						 */
						include(__oDIR__ . '/backend/php/' . $tokenType . '_checkInteraction.php');

					} else { $error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' WARNING: Invalid Token ID (' . $tokenId . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log);  }
					
					break;
				
				default:
					$error_message = (PHP_EOL . PHP_EOL . ucfirst($module) . ' WARNING: Invalid Token Type (' . $tokenType . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); 
					break;
				
			}
			
			break;
			
		default:
			$error_message = (PHP_EOL . PHP_EOL . ' WARNING: Unrecognised Module (' . $module . ').' . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); 
			break;
	
	}