<?php

	class Twitter {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Link to Twitter OAuth module.
		 * @var TwitterOAuth
		 */
		public $oauth = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {
			
			$this->database =& $database;
		
		}
		
		/**
		 * Initiate Twitter OAuth connection.
		 * If $token and $token_secret are given, initiates an OAuth connection to Twitter on user's bahalf.
		 * Otherwise, does it on the application's behalf.
		 */
		public function startOAuth($token = null, $token_secret = null) {
			
			if ($token && $token_secret) {
				/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
				$this->oauth = new TwitterOAuth(__oTWITTER_CONSUMER_KEY__, __oTWITTER_CONSUMER_SECRET__, $token, $token_secret);
			} else {
				/* Create TwitterOAuth object with client credentials. */
				$this->oauth = new TwitterOAuth(__oTWITTER_CONSUMER_KEY__, __oTWITTER_CONSUMER_SECRET__);
			}
			
		}
		
		/**
		 * Checks whether Twitter key exists in database for current user.
		 * @param integer $userId Database USER ID.
		 * @param array $token ['oauth_token', 'oauth_token_secret']
		 * @return boolean TRUE if exists, FALSE otherwise.
		 */
		public function isTokenByValue($userId, $token) {
			
			return (boolean) $this->database->query(array(
				'name' => 'isTwitterKey',
				'type' => 'mysql',
				'operation' => 'count',
				'table' => 'twitter.keys',
				'where' => array(
					'user_id' => $userId,
					'token' => $token['oauth_token'],
					'token_secret' => $token['oauth_token_secret']
				)
			))->fetch()['isTwitterKey']['count'];
			
		}
		
		/**
		 * Save Twitter access token to Database.
		 * @param integer $userId Database USER ID.
		 * @param string $tokenName Token assigned name.
		 * @param array $token  Twitter access token comprised of [oauth_token, oauth_token_secret]
		 * @return mixed Token ID if successful, FALSE on database error, NULL if key already exists.
		 */
		public function saveToken($userId, $tokenName, $token) {
			
			$return = null;
			
			if (!$this->isTokenByValue($userId, $token)) {
				
				$screenName = $this->getTokenScreenName($token['oauth_token'], $token['oauth_token_secret']);
				if (empty($tokenName)) { $tokenName = $screenName; }
				
				$return = $this->database->query(array(
					'name' => 'insertTwitterKey',
					'type' => 'mysql',
					'operation' => 'insert',
					'table' => 'twitter.keys',
					'columns' => array(
						'user_id' => $userId,
						'screen_name' => $screenName,
						'name' => $tokenName,
						'token' => $token['oauth_token'],
						'token_secret' => $token['oauth_token_secret']
					),
					'id' => true
				))->fetch()['insertTwitterKey'];
				
			}
			
			return $return;
			
		}
		
		/**
		 * Count Twitter access tokens in Database.
		 * @param string $type Which tokens? ('all', 'user')
		 * @param integer $userId [Optional]<br>Only required when $type is 'user'.
		 * @return mixed INTEGER count value, NULL if $type was misgiven.
		 */
		public function countTokens($type, $userId = null) {
			
			switch ($type) {
				
				case 'user':
					$result = $this->database->query(array(
						'name' => 'countTwitterKeys',
						'type' => 'mysql',
						'operation' => 'count',
						'table' => 'twitter.keys',
						'where' => array(
							'user_id' => $userId
						)
					))->fetch()['countTwitterKeys'];
					break;
				
				case 'all':
					$result = $this->database->query(array(
						'name' => 'countTwitterKeys',
						'type' => 'mysql',
						'operation' => 'count',
						'table' => 'twitter.keys'
						)
					)->fetch()['countTwitterKeys'];
					break;
				
				default:
					$result['count'] = null;
					break;
				
			}
			
			return intval($result['count']);
			
		}
		
		/**
		 * Get User Twitter access tokens from Database.
		 * @param string $type How many tokens? ('all', 'one')
		 * @param integer $id [Optional]<br>$id Token ID for $type 'one', User Id for $type 'all', or NULL for all system tokens.
		 * @param boolean $forUi [Optional]<br>If TRUE, 'token' and 'token_secret' fields will not be available.
		 * @return mixed Array containing token info, can contain 0 or more tokens if $type is 'all'. If $type is 'one', then an associative array containing the token will be returned, if found. Otherwise, an empty array.
		 */
		public function getUserTokens($type, $id = null, $forUi = false) {
			
			$dataY = array(
				'name' => 'getPlatformId',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'socialMedia.platforms',
				'columns' => array(
					'id'
				),
				'where' => array(
					'name' => strtolower( get_class() )
				)
			);
			
			$resultY = $this->database->query($dataY)->fetch()['getPlatformId'];
			
			$dataQ = array(
				'name' => 'getTwitterKeys',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'twitter.keys'
			);
			
			switch ($type) {
				case 'all':
					if (is_numeric($id)) {
						$dataQ['where'] = array(
							'user_id' => $id
						);
					}
					break;
				case 'one':
					$dataQ['where'] = array(
							'id' => $id
						);
					break;
				default:
					$dataQ['where'] = array();
					break;
			}
			
			$dataQ['where']['socialmediaplatform_id'] = $resultY['id'];
			
			$result = $this->database->query($dataQ)->fetch()['getTwitterKeys'];
			
			//get Twitter authorisation tokens
			$data = (
				is_assoc_array($result)
				?
				(
					$type === 'all'
					?
					array($result)
					:
					$result
				)
				:
				(
					is_array($result)
					?
					$result
					:
					array()
				)
			);
			
			if ($forUi) {
				if (is_assoc_array($data)) { unset($data['token']); unset($data['token_secret']); }
				else {
					for ($i = 0; $i < count($data); $i++) {
						unset($data[$i]['token']); unset($data[$i]['token_secret']);
					}
				}
			}
			
			return $data;
			
		}
		
		/**
		 * Remove Twitter access tokens from Database.
		 * @param string $all All tokens? (TRUE, FALSE)
		 * @param integer $id If $all is TRUE, this is USER_ID. If $all is FALSE, this is $TOKEN_ID
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function removeToken($all, $id) {
			
			return (boolean) $result = $this->database->query(array(
				'name' => 'deleteTwitterUserKeys',
				'type' => 'mysql',
				'operation' => 'delete',
				'table' => 'twitter.keys',
				'where' => array(
					($all?'user_':'') . 'id' => $id
				)
			))->fetch()['deleteTwitterUserKeys'];
			
		}
		
		/**
		 * Follow Twitter user ID. Mostly used by AJAX.
		 * @param array Details array.
		 * @param boolean $ajax [Optional]<br>If requested through AJAX.
		 * @return boolean|string TRUE on success, FALSE if database failed, String if Twitter gave an error containing information.
		 */
		public function follow($details, $ajax = false) {
			
			if ($ajax) {
				
				$return = false;
				
				$userToFollow = strval($details['userId']);
				$userScreenName = $details['screen_name'];
				$userCurrent = $details['oUserId'];
				$authKeyId = $details['authKeyId'];
				$token = $this->getUserTokens('one', $authKeyId);
				$params = array('user_id' => $userToFollow, 'follow' => true);
				
				$this->startOAuth($token['token'], $token['token_secret']);
				$content = objectToArray($this->oauth->post('friendships/create', $params));
				
				$message = '';
				if (isset($content['errors'])) {
					$message = 'Task: friendships/create / Error Code: ' . $this->oauth->http_code;
					foreach($content['errors'] as $error) {
						$message .= ' / Message: ' . print_r($error, true) . PHP_EOL;
					}
					$message .= ' / Parameters: ' . print_r($details, true);
				}
				if (__oDEBUG_OPS__) { trigger_error($message); }

				if ($this->oauth->http_code === 200) {
					
					$exists = (boolean) $this->database->query(array(
						'name' => 'checkFollow',
						'type' => 'mysql',
						'operation' => 'count',
						'table' => 'socialMedia.interaction',
						'where' => array(
							'opheme_user_id' => $userCurrent,
							'sm_user_id' => $userToFollow,
							'type' => 'follow_out',
							'authKeyId' => $authKeyId,
							'authKeyType' => 'twitter'
						)
					))->fetch()['checkFollow']['count'];
					
					if (!$exists) {
					
						$return = (boolean) $this->database->query(array(
							'name' => 'addFollow',
							'type' => 'mysql',
							'operation' => 'insert',
							'table' => 'socialMedia.interaction',
							'columns' => array(
								'opheme_user_id' => $userCurrent,
								'sm_user_id' => $userToFollow,
								'sm_user_screen_name' => $userScreenName,
								'type' => 'follow_out',
								'authKeyId' => $authKeyId,
								'authKeyType' => 'twitter',
								'added_at' => time()
							)
						))->fetch()['addFollow'];
					
					}
					
					//any database errors are caught at a global level, so this would most likely mean that after following once through the app
					//the user decided to unfollow via Twitter, and then tried refollowing via the app, which makes the database not insert a new row
					//because another one exists exactly like it, but Twitter will still create the Follow relationship successfully, the issue is only
					//on our side
					if (!$return) { $return = "Already following this person's account"; }
					
				} else {
					$return = $message;
				}

				return $return;
				
			}
			
		}
		
		/**
		 * Get screen name for twitter Token.
		 * @param string $token Twitter public token.
		 * @param string $tokenSecret Twitter secret token.
		 * @return string Screen name on success, empty string on failure.
		 */
		public function getTokenScreenName($token, $tokenSecret) {
				
			$this->startOAuth($token, $tokenSecret);
			$content = objectToArray($this->oauth->post('account/settings', array()));
			
			if (__oDEBUG_OPS__) {
				$message = '';
				if (isset($content['errors'])) {
					$message = 'Task: account/settings / Error Code: ' . $this->oauth->http_code;
					foreach($content['errors'] as $error) {
						$message .= ' / Message: ' . print_r($error, true) . PHP_EOL;
					}
				}
				trigger_error($message);
			}

			if ($this->oauth->http_code === 200) {
					
				$return = $content['screen_name'];

			} else {
				$return = 'Unknown';
			}
			
			return $return;
			
		}
		
		/**
		 * Post a status to Twitter account.
		 * @param string $status Message.
		 * @param integer $tokenId Token ID.
		 * @param string $messageId [Optional] If given, it will post this status as a reply to this message ID.
		 * @param boolean $getReply Change return from boolean to an array containing the reply from Twitter.
		 * @return mixed TRUE if successful, string with Twitter message otherwise. Returns full reply array from Twitter if $getReply is true.
		 */
		public function postStatus($status, $tokenId, $messageId = null, $getReply = false) {
			
			$token = $this->getUserTokens('one', $tokenId);
			$this->startOAuth($token['token'], $token['token_secret']);
			
			$params = array('status' => $status, 'trim_user' => 1);
			if ($messageId) { $params['in_reply_to_status_id'] = $messageId; }
			$content = objectToArray($this->oauth->post('statuses/update', $params));
			
			$message = 'Twitter - Task: statuses/update / ';
			if (isset($content['errors'])) {
				$message .= 'Error Code: ' . $this->oauth->http_code;
				foreach($content['errors'] as $error) {
					$message .= ' / Message: ' . print_r($error, true) . PHP_EOL;
				}
			} else { $message .= 'Success'; }
			if (__oDEBUG_OPS__) { trigger_error($message); trigger_error(PHP_EOL . 'Content Reply from Twitter: ' . print_r($content, true)); }
			
			if ($this->oauth->http_code === 200) {
				if ($getReply === true) {
					$return = $content;
				} else {
					$return = true;
				}
			} else {
				$return = $message;
			}
			
			return $return;
			
		}
		
	}