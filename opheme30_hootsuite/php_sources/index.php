<?php

	$secret = 'oph3m3!_s3cr3t_e34ahck25ol12'; // defined in App configuration at hootsuite.com/developers

	@$user_id   = $_REQUEST['i'];
	@$timestamp = $_REQUEST['ts'];
	@$token     = $_REQUEST['token'];
	
	if (hash("sha512", $user_id . $timestamp . $secret) === $token) {
		
		//include ('_local_fetch_user_api.php');
		
		include ('index.html'); exit;
	}

	header('HTTP/1.1 401 Unauthorized', true, 401);

	echo '<html><body><script>window.close();</script>You may now close this window.</body></html>';
	
	exit;
