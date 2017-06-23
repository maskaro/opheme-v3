<?php

namespace oPheme\Classes\WriteHelpers;

use Illuminate\Support\Facades\Hash;

class UserWriteHelper extends BaseWriteHelper
{
	// Overriding variable in parent
	protected $type = 'user';
	
	/**
	 * 	Preforms the parent's constructor
	 */
	public function __construct ( $requestAction )
	{
		$this->external = [
			'email'		 => [ 
				'create' => true,
				'update' => true,
				'required'	 => true,
				'database'	 => ['field' => 'email']
			],
			'password'		 => [ 
				'create' => true,
				'update' => true,
				'required'	 => true,
				'database'	 => ['field' => 'password'],
				'callback' => 'password'
			],
			'suspended'	 => [ 
				'create' => true,
				'update' => true,
				'required'	 => true,
				'database' => ['field' => 'suspended']
			],
			'first_name'	 => [ 
				'create' => true,
				'update' => true,
				'required'	 => true,
				'database' => ['relationship' => 'userExtra', 'field' => 'first_name']
			],
			'last_name'	 => [ 
				'create' => true,
				'update' => true,
				'required'	 => false,
				'database' => ['relationship' => 'userExtra', 'field' => 'last_name']
			],
			'phone'		 => [ 
				'create' => true,
				'update' => true,
				'required'	 => false,
				'database' => ['relationship' => 'userExtra', 'field' => 'phone'] 
			],
		];
		
		$this->internal = [
			'email_confirmation_token' => [ 
				'create' => true,
				'update' => false,
				'value' => $this->generateEmailConfirmationToken(),
				'database' => ['relationship' => 'userExtra', 'field' => 'email_confirmation_token'] 
			],
		];
		
		parent::__construct( $requestAction );
	}
	
	private function generateEmailConfirmationToken()
	{
		return str_random(8);
	}
	
	protected function callback_password($value)
	{
		return Hash::make( $value );
	}
}
