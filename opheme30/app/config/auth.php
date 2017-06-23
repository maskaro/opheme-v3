<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Authentication Driver
	|--------------------------------------------------------------------------
	|
	| This option controls the authentication driver that will be utilized.
	| This driver manages the retrieval and authentication of the users
	| attempting to get access to protected areas of your application.
	|
	| Supported: "database", "eloquent"
	|
	*/

	'driver' => 'eloquent',

	/*
	|--------------------------------------------------------------------------
	| Authentication Model
	|--------------------------------------------------------------------------
	|
	| When using the "Eloquent" authentication driver, we need to know which
	| Eloquent model should be used to retrieve your users. Of course, it
	| is often just the "User" model but you may use whatever you like.
	|
	*/

	'model' => 'oPheme\Models\User',

	/*
	|--------------------------------------------------------------------------
	| Authentication Table
	|--------------------------------------------------------------------------
	|
	| When using the "Database" authentication driver, we need to know which
	| table should be used to retrieve your users. We have chosen a basic
	| default value but you may easily change it to any table you like.
	|
	*/

	'table' => 'user',

	/*
	|--------------------------------------------------------------------------
	| Password Reminder Settings
	|--------------------------------------------------------------------------
	|
	| Here you may set the settings for password reminders, including a view
	| that should be used as your password reminder e-mail. You will also
	| be able to set the name of the table that holds the reset tokens.
	|
	| The "expire" time is the number of minutes that the reminder should be
	| considered valid. This security feature keeps tokens short-lived so
	| they have less time to be guessed. You may change this as needed.
	|
	*/

	'reminder' => array(

		'email' => 'emails.auth.reminder',

		'table' => 'userreset',

		'expire' => 60,

	),
	
	'providers' => array(
		
		'twitter' => array(
			
//			Razvan's dev
//			'identifier'    => '5eE3dRoJRh15S8TH6XVm5t6yq',
//			'secret'        => 'FLXcPNwQipcIGooIvAxXlqLvLACNbhOasMz0zpznJTlyHIpG3b',
			
//			Razvan's live
			'identifier'	=> 'ixsSi0R6alETD4hsZjq6YkZA2',
			'secret'		=> 'wwCR1Sf1zzsKC0oAbL3J0uT6MAaRScB9vQT6gaMZtSBl8E6x5s',
			
//			Lewis' dev
//			'identifier'	=> 'sNDE0Kx3tZEqAofBEGn25ycpL',
//			'secret'		=> '7vP8IHsiGTGj2LUs0IJ4m0qCfzbC3MbOwv2jioI2Gy9TS9JdFw',
			
			'callback_uri'  => 'https://backend.opheme.com/oauth/twitter/callback'
		
		),
		
		'instagram' => array(
			
//			'identifier'    => 'b698bef81550462594378f337526c285',
//			'secret'        => '83ff88bc4873403ea86bab2c33486809',
//			'callback_uri'  => 'https://backend.opheme.com/oauth/instagram/callback',
			'identifier'    => '064f3934e99f4497a2b281779158d42f',
			'secret'        => 'b13aeae61823486186f9a0018d2da118',
			'callback_uri'  => 'https://backend.opheme.com/oauth/instagram/callback',
			'scope'			=> array('basic', 'comments', 'relationships', 'likes')
		)
		
	)

);
