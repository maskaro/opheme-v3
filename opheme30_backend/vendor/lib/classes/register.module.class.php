<?php

	class Register {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Link to Token module.
		 * @var Token 
		 */
		public $token = null;
		/**
		 * Link to Company module.
		 * @var Company 
		 */
		public $company = null;
		/**
		 * Link to Crypt module.
		 * @var Crypt
		 */
		public $crypt = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database, &$token, &$company, &$crypt) {
			
			$this->database =& $database;
			$this->token =& $token;
			$this->company =& $company;
			$this->crypt =& $crypt;
		
		}
		
		/**
		 * Activate current a user's account.
		 * @param mixed $thing Integer if User's ID, String if it's a Code.
		 * @return boolean TRUE on success, FALSE otherwise.
		 */
		public function activateAccount($thing) {
			
			$data = array(
				'name' => 'activateAccount',
				'type' => 'mysql',
				'table' => 'user.accounts',
				'operation' => 'update',
				'columns' => array(
					'code' => '0'
				)
			);
			
			if (is_numeric($thing) && intval($thing) > 0) {
				$data['where']['id'] = $thing;
			} elseif (is_string($thing)) {
				$data['where']['code'] = $thing;
			}
		
			return (boolean) $this->database->query($data)->fetch()['activateAccount'];
			
		}
		
		/**
		 * Checks for $email in database.
		 * @param string $email User email to check.
		 * @return boolean TRUE if it exists, FALSE otherwise.
		 */
		public function emailExists($email) {
			
			return (boolean) $this->database->query(array(
				'name' => 'countEmails',
				'type' => 'mysql',
				'operation' => 'count',
				'table' => 'user.accounts',
				'where' => array(
					'email' => $email
				)
			))->fetch()['countEmails']['count'];
			
		}
		
		/**
		 * Creates a new account.
		 * @param string $firstName User first name.
		 * @param string $email User email.
		 * @param string $pass User password. If $token is omitted, it is used as the $token.
		 * @param boolean $selfReg Self registration?
		 * @return mixed Code String if successful ('1' for Token based accounts), FALSE if DB went off, or:<br>
		 * 1 - This email address is already registered.
		 */
		public function newAccount($firstName, $email, $pass, $selfReg = false) {
			
			if ($this->emailExists($email)) { $return = 1; }
			else {
				
				if ($selfReg) {
					$code = uniqid('o', true);
				} else { $code = '1'; }
				
				$password = $this->crypt->hashPassword($pass);
				$return = (boolean) $this->database->query(array(
					'name' => 'registerNewAccount',
					'type' => 'mysql',
					'operation' => 'insert',
					'table' => 'user.accounts',
					'columns' => array(
						'password' => $password['hash'],
						'salt' => $password['salt'],
						'email' => $email,
						'firstname' => $firstName,
						'code' => $code,
						'from_company_id' => ($selfReg?$this->company->getCompany(__oCompanyID__)[0]['id']:$this->token->getCompanyID($password['pass'])),
						'registered_at' => date('Y-m-d H:i:s')
					)
				))->fetch();
				
				if ($return) { $return = $code; }
					
			}
			
			return $return;
			
		}
		
	}