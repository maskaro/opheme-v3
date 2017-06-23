<?php

	class Login {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Link to Register module.
		 * @var Register
		 */
		public $register = null;
		
		/**
		 * $_SESSION parameters name for user information.
		 * @var String
		 */
		private $userSessionParam;
		/**
		 * Time span to check login against. Hours.
		 * @var int 
		 */
		private $loginTimeSpan = 2;
		/**
		 * Login attempts limit. Prevents brute forcing logins.
		 * @var int 
		 */
		private $loginAttemptsLimit = 5;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database, &$register) {
			
			$this->database =& $database;
			$this->register =& $register;
			$this->userSessionParam = __oSESSION_USER_PARAM__;
		
		}
		
		/**
		 * Checks current login status and validity based on $_SESSION['user']['login']['string'].
		 * @return boolean|integer TRUE if authentically logged in, FALSE if login is invalid, 1 if timeout period has passed.
		 */
		public function isValid() {
			
			$return = false;
			
			// Check if required session variables are set
			if(isset($_SESSION[$this->userSessionParam]['login']['string'], $_SESSION[$this->userSessionParam]['timestamp'])) {
				
				if (time() - $_SESSION[$this->userSessionParam]['timestamp'] < __oUSER_INACTIVITY_TIMEOUT__) {

					$email = $_SESSION[$this->userSessionParam]['account']['email'];
					$loginString = $_SESSION[$this->userSessionParam]['login']['string'];
					$userBrowser = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');

					$password = $this->database->query(array(
						'name' => 'getUserPass',
						'type' => 'mysql',
						'operation' => 'select',
						'table' => 'user.accounts',
						'columns' => array(
							'password' => 'pass'
						),
						'where' => array(
							'email' => $email
						),
						'limit' => '1'
					))->fetch()['getUserPass'];

					if ($password) {
						$loginCheck = $this->register->crypt->hash($password['pass'] . $userBrowser);
						if($loginCheck === $loginString) { $return = true; }
					}
				
				} else { $return = 1; }

			}
			
			return $return;
			
		}
		
		/**
		 * Updates last login time of $userID.
		 * @param integer $userId User database id.
		 * @return boolean TRUE on success, FALSE otherwise.
		 */
		private function updateLoginTime($userId) {
		
			return (boolean) $this->database->query(array(
				'name' => 'updateLoginTime',
				'type' => 'mysql',
				'table' => 'user.accounts',
				'operation' => 'update',
				'columns' => array(
					'last_login' => date('Y-m-d H:i:s', time())
				),
				'where' => array(
					'id' => $userId
				)
			))->fetch()['updateLoginTime'];

		}
		
		/**
		 * Creates LOGIN information.
		 * @param string $email Email supplied by user.
		 * @param string $pass Password supplied by user.
		 * @return mixed User account array from DB if successful, or an Integer:<br>
		 * 1 - Account has been suspended. For more information please contact us.
		 * 2 - Too many logins. Please try again in 2 hours.
		 * 3 - Invalid or incorrect email or password.
		 * 4 - Must activate.
		 */
		public function generate($email, $pass) {
			
			//assume invalid from the start
			$return = 3;
			
			$userInfo = $this->database->query(array(
				'name' => 'getUserInfo',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'user.accounts',
				'where' => array(
					'email' => $email
				),
				'limit' => '1'
			))->fetch()['getUserInfo'];
			
			if($userInfo) {
				
				//account suspended
				if (boolval($userInfo['suspended'])) { $return = 1; }
				//account not activated
				elseif ($userInfo['code'] !== '0' && $userInfo['code'] !== '1') { $return = 4; }
				//stop processing logins for account if brute force is attempted
				elseif ($this->checkBrute($userInfo['email']) === true) { $return  = 2; }
				//otherwise
				else {
					
					//hash user password
					$check_password = $this->register->crypt->hashPassword($pass, $userInfo['salt']);
					
					//password is OK
					if($check_password['hash'] === $userInfo['password']) {
						
						$return = array();
						
						$return['previous_interaction_check'] = strtotime($userInfo['last_login']);
						$this->updateLoginTime($userInfo['id']);

						$user_browser = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'); // Get the user-agent string of the user.
						$return['login']['string'] = hash('sha512', $userInfo['password'] . $user_browser);

						unset($userInfo['password']);
						unset($userInfo['salt']);

						$return['account'] = $userInfo;
						
					//otherwise record failed login attempt
					} else {
						
						$this->database->query(array(
							'name' => 'recordLoginAttempts',
							'type' => 'mysql',
							'table' => 'user.login.attempts',
							'operation' => 'insert',
							'columns' => array(
								'email' => $userInfo['email'],
								'time' => time()
							)
						))->fetch();

					}

				}

			}

			return $return;
			
		}
		
		/**
		 * Checks for brute force attempts.
		 * @param string $email User email to check for.
		 * @return boolean TRUE if brute force detected, FALSE otherwise.
		 */
		private function checkBrute($email) {
			$return = false;
			if ($this->getAttempts($email) > $this->loginAttemptsLimit) { $return = true; }
			return $return;
		}
		
		/**
		 * Get login attempts from past $hours hours.
		 * @param integer $hours Time span to check.
		 * @param string $email User email to check for.
		 * @return integer Number of recent logins.
		 */
		public function getAttempts($email) {
		
			//all login attempts are counted from the past 2 hours.
			$valid_attempts = time() - ($this->loginTimeSpan * 60 * 60);
			
			return $this->database->query(array(
				'name' => 'countAttempts',
				'type' => 'mysql',
				'operation' => 'count',
				'table' => 'user.login.attempts',
				'where' => array(
					'email' => $email,
					'time' => array(
						'operator' => '>',
						'data' => $valid_attempts
					)
				)
			))->fetch()['countAttempts']['count'];

		}
		
		/**
		 * Generate a new password for account with $email email.
		 * @param string $email Account email.
		 * @return mixed New password string, or FALSE if $email doesnt exist.
		 */
		public function resetPassword($email) {
			
			$return = false;
			
			if ($this->register->emailExists($email)) {
				
				$newPass = $this->register->crypt->hashPassword();
				
				$result = (boolean) $this->database->query(array(
					'name' => 'updatePassword',
					'type' => 'mysql',
					'operation' => 'update',
					'table' => 'user.accounts',
					'columns' => array(
						'password' => $newPass['hash'],
						'salt' => $newPass['salt']
					),
					'where' => array(
						'email' => $email
					)
				))->fetch()['updatePassword'];
				
				if ($result) { $return = $newPass['pass']; }
				
			}
			
			return $return;

		}
		
	}