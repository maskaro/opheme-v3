<?php
	
	/**
	 * Request format:
	 * 
	 *		/ajax/[MODULE]/[ACTION]
	 * 
	 *		[DATA] comes from POST
	 * 
	 * Example requests:
	 * 
	 *		/ajax/twitter/follow
	 *		/ajax/job/getNewMessages
	 *		/ajax/job/pause
	 *		/ajax/twitter/reply
	 */
	
	/**
	 * @var boolean Check request data is correct.
	 */
	$continue = (
		isset($checkAjaxRequest[$task][$extra])
		?
		$checkAjaxRequest[$task][$extra]
		:
		false
	);
	
	//ajax request
	$ajax = (isset($_server['HTTP_X_REQUESTED_WITH']) && $_server['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	
	//AJAX request, and valid module, and $extra info has been supplied
	if ($ajax && $continue) {
		
		/**
		 * @var string JSON message that will be echoed to javascript.
		 */
		$message = $site->{$task}->{$extra}($_post, true);

		//return message after encoding
		echo json_encode($message);
		
		//send output to browser
		if (__oBUFFER__) { echo ob_get_clean(); }
		
		//stop this PHP script
		exit;
	
	}
	
	$site->url->redirectTo('/dashboard');