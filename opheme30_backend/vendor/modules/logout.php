<?php
	
	//remove session stored data
	unset($_SESSION[__oSESSION_USER_PARAM__]);
	
	// Delete the actual cookie.
	//$params = session_get_cookie_params();
	//setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	
	//destroy session
	//session_destroy();

	//$site->message->set('logout', 'OK', 'logout_success', 'You have successfully been logged out of your ' . __oCompanyBrand__ . ' Account.');
	$site->url->redirectTo('/login');