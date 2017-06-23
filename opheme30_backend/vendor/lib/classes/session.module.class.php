<?php

	/* Deals with Sessions within USER context */
	
	class Session {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		
		private $saltUnpacked = '6a6173686429282a2664753632333769386575696825245e266636333871376f723875323138377465387177796432333432333435';
		private $salt = null;
		private $key = null;
		private $alg = MCRYPT_BLOWFISH;
		private $algMode = MCRYPT_MODE_CBC;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {
			
			$this->database =& $database;
			
			// set our custom session functions.
			session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
			
			// This line prevents unexpected effects when using objects as save handlers.
			register_shutdown_function('session_write_close');
			
			$this->salt = pack('H*', $this->saltUnpacked);
			
		}
		
		/**
		 * Starts session with name $session_name. If $regenerate is true, it also regenerates session id.
		 * Set $secure to TRUE if using SSL.
		 * 
		 * @param string $session_name Starts session with this name.
		 * @param boolean $regenerate If TRUE, it also regenerates session id.
		 * @param boolean $secure Set to TRUE if using SSL.
		 */
		public function startSession($session_name, $regenerate = false, $secure = false) {
			
			// Make sure the session cookie is not accessable via javascript.
			$httponly = true;
		  
			// Hash algorithm to use for the sessionid. (use hash_algos() to get a list of available hashes.)
			$session_hash = 'sha512';
		  
			// Check if hash is available
			if (in_array($session_hash, hash_algos())) {
				// Set the has function.
				ini_set('session.hash_function', $session_hash);
			}
			// How many bits per character of the hash.
			// The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
			ini_set('session.hash_bits_per_character', 5);
		  
			// Force the session to only use cookies, not URL variables.
			ini_set('session.use_only_cookies', 1);
		  
			// Get session cookie parameters 
			$cookieParams = session_get_cookie_params(); 
			// Set the parameters
			session_set_cookie_params($cookieParams['lifetime'], $cookieParams['path'], '.' . __oCompanyDomain__, $secure, $httponly); 
			// Change the session name 
			session_name($session_name);
			// Now we cat start the session
			session_start();
			
			// This line regenerates the session and delete the old one. 
			// It also generates a new encryption key in the database. 
			if ($regenerate) { session_regenerate_id(); }
			
		}
		
		public function open() {
			return true;
		}
		
		public function close() {
			return true;
		}
		
		public function read($id) {
			
			//grab key
			$this->grabKey($id);
			//get encrypted data
			$data = $this->database->query(array(
				'name' => 'getSessionData',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'user.login.sessions',
				'columns' => array(
					'data'
				),
				'where' => array(
					'id' => $id
				),
				'limit' => '1'
			))->fetch()['getSessionData'];
			//return decrypted data
			return $this->decrypt($data['data']);
			
		}
		
		public function write($id, $data) {
			
			//grab key
			$this->grabKey($id);
			//encrypt the data
			$data_enc = $this->encrypt($data);
			//current Epoch time
			$time = time();
			
			return (boolean) $this->database->query(array(
				'name' => 'writeSessionData',
				'type' => 'mysql',
				'operation' => 'replace',
				'table' => 'user.login.sessions',
				'columns' => array(
					'id' => $id,
					'set_time' => $time,
					'data' => $data_enc,
					'session_key' => $this->key,
					'user_id' => (isset($_SESSION[__oSESSION_USER_PARAM__]['account'])?$_SESSION[__oSESSION_USER_PARAM__]['account']['id']:-1)
				)
			))->fetch()['writeSessionData'];
			
		}
		
		private function grabKey($id) {
			
			$key = $this->database->query(array(
				'name' => 'getSessionKey',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'user.login.sessions',
				'columns' => array(
					'session_key'
				),
				'where' => array(
					'id' => $id
				),
				'limit' => '1'
			))->fetch()['getSessionKey'];
			
			if ($key && isset($key['session_key']) && strlen($key['session_key']) === 32) {
				$this->key = $key['session_key'];
			} else {
				$this->key = $this->genKey();
			}
			
		}
		
		private function hashKey($key) {
			return $this->cutKey(hash('sha256', $this->salt . $key . $this->salt));
		}
		
		private function genKey() {
			return $this->cutKey(hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true)));
		}
		
		private function cutKey($key, $length = 32) {
			return substr($key, 0, $length);
		}
		
		public function destroy($id) {
			
			$this->key = null;
			
			return (boolean) $this->database->query(array(
				'name' => 'deleteSessionData',
				'type' => 'mysql',
				'operation' => 'delete',
				'table' => 'user.login.sessions',
				'where' => array(
					'id' => $id
				)
			))->fetch()['deleteSessionData'];
			
		}
		
		public function gc($max) {
			
			$old = time() - $max;
			
			return (boolean) $this->database->query(array(
				'name' => 'deleteOldSessionData',
				'type' => 'mysql',
				'operation' => 'delete',
				'table' => 'user.login.sessions',
				'where' => array(
					'set_time' => array(
						'operator' => '<',
						'data' => $old
					)
				)
			))->fetch()['deleteOldSessionData'];
			
		}
		
		private function encrypt($data) {
			
			$crypt = new Crypt($this->alg, $this->algMode);
			
			$crypt->key = $this->hashKey($this->key);
			$crypt->cleartext = $data;
			$crypt->generateIv();
			$crypt->encrypt();

			$iv = $crypt->iv;
			$data_encrypted = $crypt->ciphertext;
			$data_encoded = base64_encode($iv . $data_encrypted);
			
			unset($crypt);
			
			return $data_encoded;
		}
		
		private function decrypt($data) {
			
			$data_decoded = base64_decode($data);
			
			$iv_size = mcrypt_get_iv_size($this->alg, $this->algMode);
			$iv = substr($data_decoded, 0, $iv_size);
			$ciphertext = substr($data_decoded, $iv_size);

			$crypt = new Crypt($this->alg, $this->algMode);
			$crypt->key = $this->hashKey($this->key);
			$crypt->iv = $iv;
			$crypt->ciphertext = $ciphertext;
			$crypt->decrypt();
			
			$data_decrypted = $crypt->cleartext;
			
			unset($crypt);
			
			return $data_decrypted;
			
		}
		
	}