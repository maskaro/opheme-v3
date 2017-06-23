<?php
	
	/**
	 * Production status. Also change the __oCIo__ constant in OPHEME/vendor/constants.php
	 */
	define('__oLIVE__', true);

	/**
	 * @var array MySQL Database Configuration details and Tables.
	 */
	$__oMyDB__ = array(
		'db.connection.user' => 'homestead',
		'db.connection.pass' => 'secret',
		'db.connection.host' => '127.0.0.1',
		'db.connection.port' => 3307,
		'db.connection.db' => 'opheme30' . (__oLIVE__?'':'_ci'),
		'db.tables' => array(
//			'app.campaign.jobs' => 'campaigns',
//			'app.discover.jobs' => 'discover',
			'app.jobs' => 'discover',
			'app.jobs.users' => 'discover_user',
			'app.jobs.filters.link' => 'discover_keyword',
			'app.jobs.filters' => 'keyword',
			'app.jobs.messages' => 'discovermessage',
			'app.jobs.tokens' => 'authkey_discover',
			'app.subscription.limits' => 'subscription_limits',
			'app.companies' => 'company',
//			'email.notification.history' => 'email_notification_history',
			'instagram.keys' => 'authkey',
//			'logs.operations' => 'logs_operations',
			'socialMedia.interactions' => 'interaction',
			'socialMedia.interactions.messages' => 'interactionmessage',
			'socialMedia.platforms' => 'socialmediaplatform',
//			'twitter.campaign.blacklist' => 'twitter_campaign_marketing_blacklist',
//			'twitter.campaign.preferences' => 'twitter_campaign_marketing_preferences',
			'twitter.keys' => 'authkey',
			'app.users' => 'user',
			'app.users.extra' => 'userextra'
		)
	);
	
	/**
	 * @var array MongoDB Database Configuration details and Tables.
	 */
	$__oMoDB__ = array(
		'db.connection.port' => 26017,
		'db.connection.host' => '127.0.0.1',
		'db.tables' => array(
			'message.twitter' => array(
				'db' => 'messages' . (__oLIVE__?'':'_ci') ,
				'coll' => 'message'
			),
			'message.twitter.sent' => array(
				'db' => 'messages' . (__oLIVE__?'':'_ci') ,
				'coll' => 'message'
			),
			'message.instagram' => array(
				'db' => 'messages' . (__oLIVE__?'':'_ci') ,
				'coll' => 'message'
			),
			'message.instagram.sent' => array(
				'db' => 'messages' . (__oLIVE__?'':'_ci') ,
				'coll' => 'message'
			)
		)
	);
	
	define('__oBACKEND_MYSQL_USE_SSH__', true);
	
	if (__oBACKEND_MYSQL_USE_SSH__ === true) {
		
		define('__oBACKEND_SSH_HOST__', '192.168.20.10');
		define('__oBACKEND_SSH_USER__', 'vagrant');
		define('__oBACKEND_SSH_MYSQL_REMOTE_PORT__', 3306);
		define('__oBACKEND_SSH_MYSQL_LOCAL_PORT__', $__oMyDB__['db.connection.port']);
		define('__oBACKEND_SSH_MONGO_REMOTE_PORT__', 27017);
		define('__oBACKEND_SSH_MONGO_LOCAL_PORT__', $__oMoDB__['db.connection.port']);
		define('__oBACKEND_SSH_KEY__', '/home/vagrant/.ssh/id_rsa_vagrant');
		define('__oBACKEND_SSH_LOG__', '/home/vagrant/ssh_tunnel.log');
		
		// start ssh tunnel
		// ssh vagrant@192.168.20.10 -i /home/vagrant/.ssh/id_rsa_vagrant -o StrictHostKeyChecking=no
		shell_exec('ssh -f -L 127.0.0.1:' . __oBACKEND_SSH_MYSQL_LOCAL_PORT__ . ':127.0.0.1:' . __oBACKEND_SSH_MYSQL_REMOTE_PORT__ . ' ' . __oBACKEND_SSH_USER__ . '@' . __oBACKEND_SSH_HOST__ . ' -i ' . __oBACKEND_SSH_KEY__ . ' -o StrictHostKeyChecking=no >> ' . __oBACKEND_SSH_LOG__ . ' 2>> ' . __oBACKEND_SSH_LOG__ . ' sleep 60');
		shell_exec('ssh -f -L 127.0.0.1:' . __oBACKEND_SSH_MONGO_LOCAL_PORT__ . ':127.0.0.1:' . __oBACKEND_SSH_MONGO_REMOTE_PORT__ . ' ' . __oBACKEND_SSH_USER__ . '@' . __oBACKEND_SSH_HOST__ . ' -i ' . __oBACKEND_SSH_KEY__ . ' -o StrictHostKeyChecking=no >> ' . __oBACKEND_SSH_LOG__ . ' 2>> ' . __oBACKEND_SSH_LOG__ . ' sleep 60');
		
	}
	
	/**
	 * Backend run frequency. Must match the OPHEME/backend/etc/settings.conf value. Seconds. +5 latency seconds.
	 */
	define('__oBACKEND_RUN_FREQUENCY__', '30 seconds');
	
	/**
	 * Controls day of the week to send out weekly email.
	 */
	define('__oEMAIL_NOTIFICATION_WEEKLY_DAY__', 'Monday');
	
	/**
	 * Controls the age of Messages that are kept when analysed by the backend modules.
	 */
	define('__oBACKEND_MESSAGE_AGE_NEW__', '5 minutes');
	
	/**
	 * Controls the minimum Klout score for Campaign module response.
	 */
	define('__oBACKEND_CAMPAIGN_REPLY_KLOUT_LOWER_LIMIT__', 30);
	
	/**
	 * Controls the frequency of Social Media token validity checks.
	 */
	define('__oBACKEND_SM_TOKEN_VALIDITY_CHECK__', '4 hours');
	
	/**
	 * Controls the frequency of Social Media interaction checks.
	 */
	define('__oBACKEND_SM_INTERACTION_CHECK__', '30 seconds');
	
	/**
	 * Controls the maximum age of the messages to check
	 */
	define('__oBACKEND_SM_AVG_TIME_CHECK__', '7 days');
	
	/**
	 * Controls the maximum age of the messages to check
	 */
	define('__oBACKEND_SM_AVG_TIME_MSG_AGE__', '30 days');
	
	/**
	 * Controls backend debugging output.
	 */
	define('__oDEBUG_BACKEND__', true);
	
	/**
	 * Controls backend operations debugging output.
	 */
	define('__oDEBUG_OPS__', true);
	
	/**
	 * Regular Session name.
	 */
	if (__oLIVE__) { define('__oSESSION_NAME__', '__oSecret_Sessions__'); }
	else { define('__oSESSION_NAME__', '__oSecret_ci_Sessions__'); }
	
	/**
	 * Session User context parameter.
	 */
	define('__oSESSION_USER_PARAM__', 'user');
	
	/**
	 * Vendor default module location
	 */
	define('__oMOD_DEFAULT__', 'dashboard');
	
	/**
	 * System Twitter application Consumer Key.
	 */
	define('__oTWITTER_CONSUMER_KEY__', 'ixsSi0R6alETD4hsZjq6YkZA2');
//	define('__oTWITTER_CONSUMER_KEY__', 'sNDE0Kx3tZEqAofBEGn25ycpL'); // lewis
	
	/**
	 * System Twitter application Consumer Secret.
	 */
	define('__oTWITTER_CONSUMER_SECRET__', 'wwCR1Sf1zzsKC0oAbL3J0uT6MAaRScB9vQT6gaMZtSBl8E6x5s');
//	define('__oTWITTER_CONSUMER_SECRET__', '7vP8IHsiGTGj2LUs0IJ4m0qCfzbC3MbOwv2jioI2Gy9TS9JdFw'); // lewis
	
	if (__oLIVE__ === true) {
		
		/**
		* System Instagram application Client Key.
		*/
	   define('__oINSTAGRAM_CONSUMER_KEY__', '064f3934e99f4497a2b281779158d42f');
	   /**
		* System Instagram application Client Secret.
		*/
	   define('__oINSTAGRAM_CONSUMER_SECRET__', 'b13aeae61823486186f9a0018d2da118');
		
	} else {
	
		/**
		 * System Instagram application Client Key.
		 */
		define('__oINSTAGRAM_CONSUMER_KEY__', 'dfdfeda2aca3486b99996507bc3a7857');
		/**
		 * System Instagram application Client Secret.
		 */
		define('__oINSTAGRAM_CONSUMER_SECRET__', 'd7373f57d42b42ffba7d2edd8bd3b834');
	
	}
	
	/**
	 * System Klout application Key
	 */
	define('__oKLOUT_KEY__', 'ruqph6qvqzxfkure3zpnuavg');
	/**
	 * System Klout application Shared Secret
	 */
	define('__oKLOUT_SHARED_SECRET__', 'dCF5bKdm99');
	
	/**
	 * Google Maps v3 API Key
	 */
	define('__oGMAPS_API_KEY__', 'AIzaSyDRmiZvB4SUycET2FUbLP0CRRLTx3agaPQ');
	
	/**
	 * Mapquest API key
	 */
	define('__oMAPQUEST_API_KEY__', 'Fmjtd%7Cluur256tl9%2Crl%3Do5-9w72qy');
	
	/**
	 * Geonames API username
	 */
	define('__oGEONAMES_USERNAME__', 'maskaro');
	
	/**
	 * Twitter Callback
	 */
	define('__oOAUTH_TWITTER_CALLBACK__', 'https://backend.opheme.com/oauth/twitter/callback');
	
	/**
	 * Instagram Callback
	 */
	define('__oOAUTH_INSTAGRAM_CALLBACK__', 'https://backend.opheme.com/oauth/instagram/callback');
	
	/**
	 * Available Social Media modules
	 */
	$availableSMModules = array('twitter', 'instagram');
	
	/**
	 * Available Jobs modules
	 */
	$availableJModules = array('discover', 'campaign');
	
	/**
	 * Available Email Notification frequency. Days => Display
	 */
	$availableEmailNotificationFrequency = array('1' => '24 Hours', '2' => '48 Hours', '7' => '1 Week');