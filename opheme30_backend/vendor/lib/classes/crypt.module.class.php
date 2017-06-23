<?php

	class Crypt {
		
		//session related
		private $algorithm;
		private $mode;
		private $randomSource;
		private $ivSize;
		
		//password related
		private $passAlgo = 'sha512';

		public $cleartext;
		public $ciphertext;
		public $iv;
		public $key;

		/**
		 * Instantiate Class.
		 */
		public function __construct($algorithm = MCRYPT_BLOWFISH, $mode = MCRYPT_MODE_CBC, $random_source = MCRYPT_DEV_URANDOM) {
			$this->algorithm = $algorithm;
			$this->mode = $mode;
			$this->randomSource = $random_source;
			$this->ivSize = mcrypt_get_iv_size($this->algorithm, $this->mode);
		}

		public function generateIv() {
			$this->iv = mcrypt_create_iv($this->ivSize, $this->randomSource);
		}

		public function encrypt() {
			if (strlen($this->iv) === $this->ivSize) {
				$this->ciphertext = mcrypt_encrypt($this->algorithm, $this->key, $this->cleartext, $this->mode, $this->iv);
			}
		}

		public function decrypt() {
			if (strlen($this->iv) === $this->ivSize) {
				$this->cleartext = mcrypt_decrypt($this->algorithm, $this->key, $this->ciphertext, $this->mode, $this->iv);
			}
		}
		
		/**
		 * Hash a given password string.
		 * @param string $password [Optional]<br>Password string. If omitted, a new one is generated.
		 * @param string $salt [Optional]<br>Salt. If omitted, a new one is generated.
		 * @return assoc_array ['pass', 'hash', 'salt']
		 */
		public function hashPassword($password = null, $salt = null) {
			if (!$password) { $password = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); }
			if (!$salt) { $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); }
			$password_hash = hash($this->passAlgo, $password . $salt);
			for($round = 0; $round < 65536; $round++) { $password_hash = hash($this->passAlgo, $password_hash . $salt); }
			return array('pass' => $password, 'hash' => $password_hash, 'salt' => $salt);
		}
		
		/**
		 * Hash a given string.
		 * @param string $string String.
		 * @return string String hash.
		 */
		public function hash($string) {
			return hash($this->passAlgo, $string);
		}
		
	}