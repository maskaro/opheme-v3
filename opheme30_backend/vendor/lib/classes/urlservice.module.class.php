<?php

	/**
	 * Based on work done at http://www.sitepoint.com/building-your-own-url-shortener/ by Alex Fraundorf.
	 */
	class UrlService {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		
		/**
		 * Characters available in the short Code creation process.
		 * @var string 
		 */
		private static $chars = '123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
		
		/**
		 * Controls extra check of URL validity by attempting to connect to it.
		 * @var boolean
		 */
		private static $checkUrlExists = false;

		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {

			$this->database =& $database;
			
		}
		
		/**
		 * Creates short Code from long URL.
		 * @param string $url Long URL to be shortened
		 * @return integer|string|null Short Code if successful, Null if DB failure, Integer as follows otherwise: 
		 * 1 - No URL was supplied.
		 * 2 - URL does not have a valid format.
		 * 3 - URL does not appear to exist.
		 */
		public function urlToShortCode($url) {
			
			if (!empty($url)) {

				if ($this->validateUrlFormat($url) !== false) {

					if (self::$checkUrlExists && !$this->verifyUrlExists($url)) {
						
						$return = 3;
						
					} else {
						
						$return = $this->urlExistsInDb($url);

						if ($return === false) {
							
							$return = $this->createShortCode($url);
						}
						
					}
					
				} else { $return = 2; }
				
			} else { $return = 1; }

			return $return;
			
		}
		
		/**
		 * Validate Long URL format.
		 * @param string $url Long URL to check.
		 * @return boolean|string Filtered URL string on success, FALSE otherwise.
		 */
		protected function validateUrlFormat($url) {
			return $url;
			//$pattern = '`^(((?:https?|ftps?):)?(\/\/))?(([-a-z0-9@_:]+)\.)+[a-z0-9+]{2,12}((\/[-a-z0-9@_?&%,\"=\':.]+)+)?`i';
			//preg_match($pattern, $url, $matches);
			//return (!empty($matches) && strlen($matches[0]) === strlen($url));
			//return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
		}
		
		/**
		 * Verify Long URL existence by attempting a connection to it using CURL.
		 * @param string $url Long URL to check.
		 * @return boolean TRUE if it exists, FALSE otherwise.
		 */
		protected function verifyUrlExists($url) {
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch); unset($ch);

			return (!empty($response) && $response != 404);
			
		}
		
		/**
		 * Check if a Long URL already exists in database and return its short Code if it does.
		 * @param string $url Long URL to check.
		 * @return mixed FALSE if it does not exist, string containing the associated short Code if it does.
		 */
		protected function urlExistsInDb($url) {
			
			$result = $this->database->query(array(
				'name' => 'getURL',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'app.urls',
				'columns' => array(
					'short_code'
				),
				'where' => array(
					'long_url' => $url
				)
			))->fetch()['getURL'];
			
			return (empty($result)) ? false : $result['short_code'];
			
		}
		
		/**
		 * Create short Code code from long URL.
		 * @param string $url Long URL string.
		 * @return string|null NULL on failure, otherwise Short Code string.
		 */
		protected function createShortCode($url) {
			
			$id = $this->insertUrlInDb($url);
			$shortCode = $this->convertIntToShortCode($id);
			$this->insertShortCodeInDb($id, $shortCode);
			
			return $shortCode;
			
		}

		/**
		 * Insert long URL into database.
		 * @param string $url Long URL.
		 * @return id ID of the new row in the Database.
		 */
		protected function insertUrlInDb($url) {
			
			$result = $this->database->query(array(
				'name' => 'storeURL',
				'type' => 'mysql',
				'operation' => 'insert',
				'table' => 'app.urls',
				'columns' => array(
					'long_url' => $url
				),
				'id' => true
			))->fetch()['storeURL'];

			return $result;
		}
		
		/**
		 * Convert an integer (Database row ID of Long URL) to a short Code.
		 * @param integer $id Row ID for Long URL.
		 * @return string|null Short Code, NULL if $id is not valid or less than 0.
		 */
		protected function convertIntToShortCode($id) {
			
			$id = ((is_numeric($id) && ($id > -1))?intval($id):-1);
			
			if ($id > -1) {

				$length = strlen(self::$chars);
				$code = '';
				
				while ($id > $length - 1) {
					//determine what the value of the next higher character in the short code should be and prepend
					$code = self::$chars[intval(fmod($id, $length))] . $code;
					//reset $id to remaining value to be converted
					$id = floor($id / $length);
				}

				//remaining value of $id is less than the length of self::$chars
				$code = self::$chars[intval($id)] . $code;
				
			} else { $code = null; }

			return $code;
		}
		
		/**
		 * 
		 * @param type $id
		 * @param type $code
		 * @return boolean|null TRUE on success, FALSE on failure, NULL if one parameter is missing.
		 */
		protected function insertShortCodeInDb($id, $code) {
			
			if (isset($id, $code)) {
				
				$return = (boolean) $this->database->query(array(
					'name' => 'storeShortURL',
					'type' => 'mysql',
					'operation' => 'update',
					'table' => 'app.urls',
					'columns' => array(
						'short_code' => $code
					),
					'where' => array(
						'id' => $id
					)
				))->fetch()['storeShortURL'];
			
			} else { $return = null; }

			return $return;
		}
		
		/**
		 * Converts short Code to Long URL.
		 * @param string $code Short Code.
		 * @param boolean $increment Increment conversion counter.
		 * @return string|integer Long URL on success, Integer as follow on failure:
		 * 1 - No short code was supplied.
		 * 2 - Short code does not have a valid format.
		 * 3 - Short code does not appear to exist.
		 */
		public function shortCodeToUrl($code, $increment = true) {
			
			if (!empty($code)) {
				
				if ($this->validateShortCode($code) === 1) {
					
					$urlRow = $this->getUrlFromDb($code);
					
					if (!empty($urlRow)) {
						
						if ($increment === true) {
							$this->incrementCounter($urlRow['id']);
						}
						
						$return = $urlRow['long_url'];
						
					} else { $return = 3; }
				
				} else { $return = 2; }
				
			} else { $return = 1; }

			return $return;
			
		}
		
		/**
		 * Validate short Code.
		 * @param string $code Code to validate.
		 * @return integer|false 1 if Valid, 0 if is not, FALSE if an error occurred.
		 */
		protected function validateShortCode($code) {
			return preg_match('|[' . self::$chars . ']+|', $code);
		}
		
		/**
		 * Retrieve Long URL and its ID from database based on its short Code.
		 * @param string $code Short code.
		 * @return mixed Assoc array on success, NULL otherwise.
		 */
		protected function getUrlFromDb($code) {
			
			return $this->database->query(array(
				'name' => 'getURL',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'app.urls',
				'columns' => array(
					'id',
					'long_url'
				),
				'where' => array(
					'short_code' => $code
				)
			))->fetch()['getURL'];
			
		}
		
		/**
		 * Increment conversion counter for an URL ID.
		 * @param integer $id URL database ID.
		 */
		protected function incrementCounter($id) {
			
			$counter = $this->database->query(array(
				'name' => 'getCounter',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'app.urls',
				'columns' => array(
					'counter'
				),
				'where' => array(
					'id' => $id
				)
			))->fetch()['getCounter']['counter'];
			
			return (isset($counter) && (boolean) $this->database->query(array(
				'name' => 'incrementCounter',
				'type' => 'mysql',
				'operation' => 'update',
				'table' => 'app.urls',
				'columns' => array(
					'counter' => $counter + 1
				),
				'where' => array(
					'id' => $id
				)
			))->fetch()['incrementCounter']);
			
		}
		
	}