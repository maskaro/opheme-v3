<?php

return array(
	/*
	  |--------------------------------------------------------------------------
	  | Transformers and WriteHelpers
	  |--------------------------------------------------------------------------
	  |
	  | This file is for storing the transformers and write helpers configurations
	  |
	 */
	
	/**
	 * Disallowed items basied on the transformer type, scopes, client id
	 */
	'disallowed_items' => [
		'user' => [
			'scope' => [
				'basic'		 => [
					'email'
				],
//				'inhouse'	 => [
//					'email'
//				],
			],
			'client' => [
				//Add items that aren't allowed baised on the client (example below)
//				'opheme_hootsuite' => [
//					'firstname',
//				],
				'opheme_hootsuite' => [
					'email_confirmation_token'
				],
			],
		],
	],
);
