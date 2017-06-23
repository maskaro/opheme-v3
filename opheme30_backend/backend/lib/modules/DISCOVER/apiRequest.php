#!/usr/bin/php -q
<?php

	/**
	 * Base oPheme directory
	 */
	define('__oDIR__', $argv[2]);
	
	//backend functionality
	require_once(__oDIR__ . '/backend/php/backend.php');
	
	exit;