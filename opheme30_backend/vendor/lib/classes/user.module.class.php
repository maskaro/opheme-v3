<?php

/**
 * Deals with USER context
 */
class User
{

	/**
	 * Link to Database module.
	 * @var Database
	 */
	private $database	 = null;

	/**
	 * Link to Login module.
	 * @var Login
	 */
//	private $login		 = null;

	/**
	 * Link to Company module.
	 * @var Company
	 */
//	private $company	 = null;

	/**
	 * User information from $_SESSION. Internal use. Contains [account, allowance, login, modules]
	 * @var array
	 */
	private $userInfo			 = null;

	/**
	 * $_SESSION parameters name for user information
	 * @var string
	 */
	private $userSessionParam	 = 'user';

	/**
	 * Instantiate Class.
	 */
	public function __construct ( &$database )
	{

		$this->database	 = & $database;
//		$this->login	 = & $login;
//		$this->company	 = & $company;

		$this->userSessionParam = __oSESSION_USER_PARAM__;
	}

	/**
	 * Refreshes internal userInfo from $_SESSION, Module access, and allowance information.
	 */
	public function refreshUserInfo ()
	{

		$this->userInfo = $_SESSION[ $this->userSessionParam ];
		$this->refreshModules();
		$this->refreshAllowance();
	}

	/**
	 * Checks user context for $module access.
	 * @param string $module Module to check.
	 * @return boolean TRUE if allowed access, FALSE otherwise.
	 */
	public function hasAccessTo ( $module )
	{
		if ( !is_array( $this->userInfo[ 'modules' ] ) )
		{
			$this->userInfo[ 'modules' ] = array();
		}
		return in_array( strtolower( $module ), $this->userInfo[ 'modules' ] );
	}

	/**
	 * Check wheter User ID is a company representative.
	 * @param integer $userId Database User ID.
	 * @return boolean TRUE if user is a representative, FALSE otherwise.
	 */
	public function isRepresentative ( $company, $userId )
	{

		$return = false;

		$data = $this->database->query( array(
				'name'		 => 'getCompany',
				'type'		 => 'mysql',
				'operation'	 => 'select',
				'table'		 => 'company',
				'where'		 => array(
					'company_id' => $company
				)
			) )->fetch()[ 'getCompany' ];

		if ( $data )
		{
			$return = is_string( stristr( $data[ 'user_id' ], $userId ) );
		}

		return $return;
	}

	/**
	 * Returns user [$category, $part] data.<br><br>
	 * Available <b>[$category, $part]</b> combinations and their type:<br>
	 * <b>[account, <br>&#09;(id, firstname, lastname, email, business_type, business_www, phone, home_location, email_notification_frequency, email_notification_last_timestamp, subscription, last_login, created, from_company_id, [login, (string)])<br>] - string<br>
	 * [allowance, <br>&#09;(name, price, accountTimeLimit, accountTimeLeftSeconds, discoversLimit,<br>
	 * &#09;discoversLeft, campaignsLimit, campaignsLeft, jobMessageLimit, jobTimeLimit)<br>] - mixed<br>
	 * [authorised, (twitter)] - boolean<br>
	 * [modules, (`module`)] - string</b><br>
	 * @param string $category Data category found within User context.
	 * @param string [Optional]<br>$part Data part found within $category.
	 * @return mixed String/Integer, or Array with data, NULL if [$category, $part] doesn't exist.
	 */
	public function get ( $category, $part = null )
	{
		$return = null;
		if ( $part )
		{
			$return = (isset( $this->userInfo[ $category ][ $part ] ) ? $this->userInfo[ $category ][ $part ] : null);
		}
		else
		{
			$return = (isset( $this->userInfo[ $category ] ) ? $this->userInfo[ $category ] : null);
		}
		return $return;
	}

	/**
	 * Refreshes internal information on user module allowance.
	 * @param boolean $return [Optional]<br>If TRUE, returns an allowance array.
	 * @param integer $subId [Optional]<br>Subscription ID from Database. Overrides internal subId if present.
	 * @param datetime $accountCreated [Optional]<br>This is only required from backend. If this is set, information about Jobs Left is omitted.
	 * @return mixed If $return is TRUE, then returns array. Otherwise, TRUE on success, FALSE on failure.
	 */
	public function refreshAllowance ( $return = false, $subId = null, $accountCreated = null )
	{

		if ( $subId === null && isset( $_SESSION[ $this->userSessionParam ][ 'account' ][ 'subscription' ] ) )
		{
			$subId = $_SESSION[ $this->userSessionParam ][ 'account' ][ 'subscription' ];
		}

		if ( $subId !== null )
		{
			$data = $this->database->query( array(
					'name'		 => 'getAllowance',
					'type'		 => 'mysql',
					'operation'	 => 'select',
					'table'		 => 'app.subscription.limits',
					'columns'	 => array(
						'name',
						'messages_limit'	 => 'jobMessageLimit',
						'time_limit'		 => 'jobTimeLimit',
						'discover_job_limit' => 'discoversLimit',
						'campaign_job_limit' => 'campaignsLimit',
						'price',
						'account_time_limit' => 'accountTimeLimit'
					),
					'where'		 => array(
						'id' => $subId
					),
					'limit'		 => '1'
				) )->fetch()[ 'getAllowance' ];
		}

		if ( $data )
		{

			if ( intval( $data[ 'accountTimeLimit' ] ) > 0 )
			{
				$created = ($accountCreated ? $accountCreated : $_SESSION[ $this->userSessionParam ][ 'account' ][ 'created' ]);
				$now	 = time();
				$until	 = strtotime( '+' . $data[ 'accountTimeLimit' ], strtotime( $created ) );
				$diff	 = $until - $now;
				if ( $diff < 0 )
				{
					$diff = 0;
				}
				$data[ 'accountTimeLeftSeconds' ] = $diff;
			}
			else
			{
				unset( $data[ 'accountTimeLimit' ] );
			}

			if ( intval( $data[ 'jobMessageLimit' ] ) === 0 )
			{
				unset( $data[ 'jobMessageLimit' ] );
			}
			if ( intval( $data[ 'jobTimeLimit' ] ) === 0 )
			{
				unset( $data[ 'jobTimeLimit' ] );
			}

			//this is a request from backend most likely
			if ( !$accountCreated )
			{

				if ( intval( $data[ 'discoversLimit' ] ) > 0 )
				{
					$data[ 'discoversLeft' ] = $data[ 'discoversLimit' ] - $_SESSION[ $this->userSessionParam ][ 'jobs' ][ 'discoverCount' ];
				}
				else
				{
					unset( $data[ 'discoversLimit' ] );
				}

				if ( intval( $data[ 'campaignsLimit' ] ) > 0 )
				{
					$data[ 'campaignsLeft' ] = $data[ 'campaignsLimit' ] - $_SESSION[ $this->userSessionParam ][ 'jobs' ][ 'campaignCount' ];
				}
				else
				{
					unset( $data[ 'campaignsLimit' ] );
				}
			}

			$this->userInfo[ 'allowance' ] = $data;
		}
		else
		{
			$this->userInfo[ 'allowance' ] = null;
		}

		if ( $return )
		{
			return $this->userInfo[ 'allowance' ];
		}

		return (boolean) $this->userInfo[ 'allowance' ];
	}

	/**
	 * Refresh user allowed modules based on $_SESSION parameters. Stores data in $user->userInfo as well as returning it.
	 * @return array Module array.
	 */
	public function refreshModules ()
	{

		$modules = array();

		if ( $_SESSION[ __oSESSION_USER_PARAM__ ][ 'account' ][ 'allSet' ] && $this->hasAuthorised() )
		{
			if ( isset( $_SESSION[ __oSESSION_USER_PARAM__ ][ 'allowance' ][ 'discoversLimit' ] ) )
			{
				$modules[] = 'discover';
			}
			if ( isset( $_SESSION[ __oSESSION_USER_PARAM__ ][ 'allowance' ][ 'campaignsLimit' ] ) )
			{
				$modules[] = 'campaign';
			}
			global $freeCompanyModules;
			foreach ( $freeCompanyModules as $mod )
			{
				$modules[] = $mod;
			}
			$companyModules = $this->company->getModules();
			foreach ( $companyModules as $mod => $status )
			{
				if ( in_array( $mod, array(
						'admin',
						'reseller' ) ) )
				{
					continue;
				}
				$modules[] = $mod;
			}
		}
		if ( $_SESSION[ __oSESSION_USER_PARAM__ ][ 'representative' ] )
		{
			if ( $this->company->hasAccessTo( 'admin' ) )
			{
				$modules[] = 'admin';
			}
			if ( $this->company->hasAccessTo( 'reseller' ) )
			{
				$modules[] = 'reseller';
			}
		}

		$this->userInfo[ 'modules' ] = $modules;

		return $modules;
	}

	/**
	 * Checks user context for authorisation status.
	 * @return boolean TRUE if has at least one authorisation, FALSE otherwise.
	 */
	public function hasAuthorised ()
	{

		global $availableSMModules;

		$return = false;

		foreach ( $availableSMModules as $smModule )
		{
			if ( count( $this->userInfo[ 'authorisation' ][ $smModule ] ) )
			{
				$return = true;
				break;
			}
		}

		return $return;
	}

	/**
	 * Change user account details.
	 * @param array $details Array containg new user account details as received by $_POST.<br>
	 * $details['first-name'], $details['last-name'], $details['phone'], $details['business-type'], $details['business-www'], $details['old-password']
	 * @param string $userId User database ID.
	 * @return mixed TRUE if successful, FALSE if not all data is present, NULL if database failed, 1 if current password is incorrect.<br>
	 */
	public function changeAccountDetails ( Array $details, $userId )
	{

		$return = 2;

		if ( isset( $details[ 'first-name' ], $details[ 'last-name' ], $details[ 'phone' ], $details[ 'business-type' ], $details[ 'business-www' ], $details[ 'home-location' ] ) )
		{

			$password = $this->database->query( array(
					'name'		 => 'getPasswordInfo',
					'type'		 => 'mysql',
					'operation'	 => 'select',
					'table'		 => 'user.accounts',
					'columns'	 => array(
						'password' => 'pass',
						'salt'
					),
					'where'		 => array(
						'id' => $userId
					),
					'limit'		 => '1'
				) )->fetch()[ 'getPasswordInfo' ];

			if ( $password )
			{

				$checkPassword = $this->login->register->crypt->hashPassword( $details[ 'old-password' ], $password[ 'salt' ] );

				//if new password is provided and old one checks out, include it in the changes
				if ( isset( $details[ 'new-password' ] ) && strlen( $details[ 'new-password' ] ) > 0 )
				{
					if ($checkPassword[ 'hash' ] !== $password[ 'pass' ]) {
						return 1;
					}
					$new_password = $this->login->register->crypt->hashPassword( $details[ 'new-password' ], $password[ 'salt' ] )[ 'hash' ];
				}
				else
				{ //otherwise, keep old password
					$new_password = $password[ 'pass' ];
				}

				$return = $this->database->query( array(
						'name'		 => 'changeDetails',
						'type'		 => 'mysql',
						'operation'	 => 'update',
						'table'		 => 'user.accounts',
						'columns'	 => array(
							'firstname'		 => $details[ 'first-name' ],
							'lastname'		 => $details[ 'last-name' ],
							'phone'			 => $details[ 'phone' ],
							'business_type'	 => $details[ 'business-type' ],
							'business_www'	 => $details[ 'business-www' ],
							'home_location'	 => $details[ 'home-location' ],
							'password'		 => $new_password,
						),
						'where'		 => array(
							'id' => $userId
						)
					) )->fetch()[ 'changeDetails' ];

				if ( $return === 0 )
				{
					$return = true;
				} //generally happens if all fields have exactly the same values as what already is in the database
				else
				{
					$return = (boolean) $return;
				}
			}
			else
			{
				$return = null;
			}
		}

		return $return;
	}

	/**
	 * Change user status.
	 * @param array $details Details array. ['id'] is required.
	 * @param boolean $ajax [Optional]<br>If requested through AJAX.
	 * @return boolean TRUE on success, FALSE otherwise
	 */
	public function setStatus ( $details, $ajax = false )
	{

		$data = array(
			'name'		 => 'changeStatus',
			'type'		 => 'mysql',
			'operation'	 => 'update',
			'table'		 => 'user.accounts',
			'where'		 => array(
				'id' => $details[ 'id' ]
			)
		);

		foreach ( $details as $column => $value )
		{
			if ( $column === 'id' )
			{
				continue;
			} $data[ 'columns' ][ $column ] = $value;
		}

		return (boolean) $this->database->query( $data )->fetch()[ 'changeStatus' ];
	}

	/**
	 * Remove User.
	 * @param integer $id User ID.
	 * @return boolean TRUE on success, FALSE otherwise.
	 */
	public function remove ( $id )
	{

		return (boolean) $this->database->query( array(
				'name'		 => 'removeUser',
				'type'		 => 'mysql',
				'table'		 => 'user.accounts',
				'operation'	 => 'delete',
				'where'		 => array(
					'id' => $id
				)
			) )->fetch()[ 'removeUser' ];
	}

	/**
	 * Remove User session data.
	 * @param integer $id User ID.
	 * @return boolean TRUE on success, FALSE otherwise.
	 */
	public function removeSessions ( $id )
	{

		return (boolean) $this->database->query( array(
				'name'		 => 'removeUserSessions',
				'type'		 => 'mysql',
				'table'		 => 'user.login.sessions',
				'operation'	 => 'delete',
				'where'		 => array(
					'user_id' => $id
				)
			) )->fetch()[ 'removeUserSessions' ];
	}

	/**
	 * Get user information from Database.
	 * @param integer $field [Optional]<br>Database field by which to select users. If omitted, all users are retrieved.
	 * @param integer $info [Optional]<br>Information relevant to the chosen $field. Required of $field is given.
	 * @return array Array of 0 or more users.
	 */
	public function getUsersBy ( $field = null, $info = null )
	{

		$data = array(
			'name'		 => 'getUsers',
			'type'		 => 'mysql',
			'operation'	 => 'select',
			'table'		 => 'user.accounts',
			'order'		 => array(
				'email' => 'asc'
			)
		);

		if ( $field )
		{

			$data[ 'where' ] = array(
				$field => $info
			);
		}

		$return = $this->database->query( $data )->fetch()[ 'getUsers' ];

		if ( $return )
		{

			if ( is_assoc_array( $return ) )
			{
				$return = array(
					$return );
			}

			for ( $i = 0; $i < count( $return ); $i++ )
			{
				unset( $return[ $i ][ 'password' ] );
				unset( $return[ $i ][ 'salt' ] );
			}
		}
		else
		{
			$return = array();
		}

		return $return;
	}

	/**
	 * Get user IP.
	 * @return string IP address.
	 */
	public function getIP ()
	{
		foreach ( array(
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR' ) as $key )
		{
			if ( array_key_exists( $key, $_SERVER ) === true )
			{
				foreach ( array_map( 'trim', explode( ',', $_SERVER[ $key ] ) ) as $ip )
				{
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false )
					{
						return $ip;
					}
				}
			}
		}
	}

}
