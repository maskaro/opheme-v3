<?php

	class Instagram {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Link to OAuth module.
		 * @var OAuth
		 */
		public $oauth = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {
			
			$this->database =& $database;
		
		}
		
		/**
		 * Initiate OAuth connection.
		 * If $token and $token_secret are given, initiates an OAuth connection to Instagram on user's bahalf.
		 * Otherwise, does it on the application's behalf.
		 * @param string $token [Optional] User access token.
		 * @param string $tokenSecret NOT USED.
		 */
		public function startOAuth($token = null, $tokenSecret = null) {
			
			//callback link
			$callback = __oOAUTH_INSTAGRAM_CALLBACK__;
			
			$this->oauth = new InstagramOAuth(array(
				'apiKey'      => __oINSTAGRAM_CONSUMER_KEY__,
				'apiSecret'   => __oINSTAGRAM_CONSUMER_SECRET__,
				'apiCallback' => $callback
			));
				
			if ($token) {
				
				$this->oauth->setAccessToken($token);
				
			}
			
		}
		
		/**
		 * Checks whether Instagram key exists in database for current user.
		 * @param integer $userId Database USER ID.
		 * @param array $token
		 * @return boolean TRUE if exists, FALSE otherwise.
		 */
		public function isTokenByValue($userId, $token) {
			
			return (boolean) $this->database->query(array(
				'name' => 'isInstagramKey',
				'type' => 'mysql',
				'operation' => 'count',
				'table' => 'instagram.keys',
				'where' => array(
					'user_id' => $userId,
					'token' => $token,
				)
			))->fetch()['isInstagramKey']['count'];
			
		}
		
		/**
		 * Save Instagram access token to Database.
		 * @param integer $userId Database USER ID.
		 * @param string $tokenName Token assigned name.
		 * @param array $token  Instagram access token comprised of [oauth_token, oauth_token_secret]
		 * @return mixed Token ID if successful, FALSE on database error, NULL if key already exists.
		 */
		public function saveToken($userId, $tokenName, $token) {
			
			$return = null;
			
			if (!$this->isTokenByValue($userId, $token)) {
				
				$return = $this->database->query(array(
					'name' => 'insertInstagramKey',
					'type' => 'mysql',
					'operation' => 'insert',
					'table' => 'instagram.keys',
					'columns' => array(
						'user_id' => $userId,
						'screen_name' => $tokenName,
						'name' => $tokenName,
						'token' => $token
					),
					'id' => true
				))->fetch()['insertInstagramKey'];
				
			}
			
			return $return;
			
		}
		
		/**
		 * Count Instagram access tokens in Database.
		 * @param string $type Which tokens? ('all', 'user')
		 * @param integer $userId [Optional]<br>Only required when $type is 'user'.
		 * @return mixed INTEGER count value, NULL if $type was misgiven.
		 */
		public function countTokens($type, $userId = null) {
			
			switch ($type) {
				
				case 'user':
					$result = $this->database->query(array(
						'name' => 'countInstagramKeys',
						'type' => 'mysql',
						'operation' => 'count',
						'table' => 'instagram.keys',
						'where' => array(
							'user_id' => $userId
						)
					))->fetch()['countInstagramKeys'];
					break;
				
				case 'all':
					$result = $this->database->query(array(
						'name' => 'countInstagramKeys',
						'type' => 'mysql',
						'operation' => 'count',
						'table' => 'instagram.keys'
						)
					)->fetch()['countInstagramKeys'];
					break;
				
				default:
					$result['count'] = null;
					break;
				
			}
			
			return intval($result['count']);
			
		}
		
		/**
		 * Get User Instagram access tokens from Database.
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
				'name' => 'getInstagramKeys',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'instagram.keys'
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
			
			$result = $this->database->query($dataQ)->fetch()['getInstagramKeys'];
			
			//get Instagram authorisation tokens
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
				if (is_assoc_array($data)) { unset($data['token']); }
				else {
					for ($i = 0; $i < count($data); $i++) {
						unset($data[$i]['token']);
					}
				}
			}
			
			return $data;
			
		}
		
		/**
		 * Remove Instagram access tokens from Database.
		 * @param string $all All tokens? (TRUE, FALSE)
		 * @param integer $id If $all is TRUE, this is USER_ID. If $all is FALSE, this is $TOKEN_ID
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function removeToken($all = false, $id) {
			
			return (boolean) $result = $this->database->query(array(
				'name' => 'deleteInstagramUserKeys',
				'type' => 'mysql',
				'operation' => 'delete',
				'table' => 'instagram.keys',
				'where' => array(
					($all?'user_':'') . 'id' => $id
				)
			))->fetch()['deleteInstagramUserKeys'];
			
		}
		
		/**
		 * Follow Instagram user ID. Mostly used by AJAX.
		 * @param array Details array.
		 * @param boolean $ajax [Optional]<br>If requested through AJAX.
		 * @return boolean|string TRUE on success, FALSE if database failed, String if Instagram gave an error containing information.
		 */
		public function follow($details, $ajax = false) {
			
			if ($ajax) {
				
				$return = false;
				
				$userToFollow = strval($details['userId']);
				$userScreenName = $details['screen_name'];
				$userCurrent = $details['oUserId'];
				$authKeyId = $details['authKeyId'];
				$token = $this->getUserTokens('one', $authKeyId);
				
				$this->startOAuth($token['token']);
				$content = objectToArray($this->oauth->modifyRelationship('follow', $userToFollow));
				
				$message = '';
				if ($content['meta']['code'] > 200) {
					$message = 'Task: friendships/create / Error Code: ' . $content['meta']['code'];
					$message .= ' / Type: ' . $content['meta']['error_type'] . PHP_EOL;
					$message .= ' / Message: ' . $content['meta']['error_message'] . PHP_EOL;
					$message .= ' / Parameters: ' . print_r($details, true);
				}
				if (__oDEBUG_OPS__) { trigger_error($message); }

				if ($content['meta']['code'] === 200) {
					
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
							'authKeyType' => 'instagram',
							'added_at' => time()
						)
					))->fetch()['addFollow'];
					
					//any database errors are caught at a global level, so this would most likely mean that after following once through the app
					//the user decided to unfollow via Instagram, and then tried refollowing via the app, which makes the database not insert a new row
					//because another one exists exactly like it, but Instagram will still create the Follow relationship successfully, the issue is only
					//on our side
					if (!$return) { $return = true; }
					
				} else {
					$return = $message;
				}

				return $return;
				
			}
			
		}
		
		/**
		 * Post a status to Instagram account.
		 * @param string $status Message.
		 * @param integer $tokenId Token ID.
		 * @param string $messageId Post this status as a comment to this message ID.
		 * @param boolean $getReply Change return from boolean to an array containing the reply from Instagram.
		 * @return mixed TRUE if successful, string with Instagram message otherwise. Or response array from Instagram if $getReply is true.<br>
		 *		<pre>{<br>"meta":{<br>&#09;"code":200<br>&#09;},<br>"data":{<br>&#09;"created_time":"1408035159",<br>&#09;"text":"@littleman_smith That looks amazing!",<br>&#09;"from":{<br>&#09;&#09;"username":"maskaro",<br>&#09;&#09;"profile_picture":"http:\/\/images.ak.instagram.com\/profiles\/anonymousUser.jpg",<br>&#09;&#09;"id":"473352",<br>&#09;&#09;"full_name":""},<br>&#09;"id":"786978412579487292"<br>&#09;}<br>}</pre>
		 */
		public function postStatus($status, $tokenId, $messageId = null, $getReply = false) {
			
			$token = $this->getUserTokens('one', $tokenId);
			$this->startOAuth($token['token']);
			
			$content = objectToArray($this->oauth->addMediaComment($messageId, $status));
			
			$message = 'Instagram - Task: statuses/update / ';
			if ($content['meta']['code'] > 200) {
				$message .= 'Error Code: ' . $content['meta']['code'];
				$message .= ' / Type: ' . $content['meta']['error_type'] . PHP_EOL;
				$message .= ' / Message: ' . $content['meta']['error_message'] . PHP_EOL;
				$message .= ' / Parameters: MID-' . $messageId . '/Status-' . $status;
			} else { $message .= 'Success'; }
			//if (__oDEBUG_OPS__) { 
				trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); trigger_error(PHP_EOL . PHP_EOL . 'Content Reply from Instagram: ' . print_r($content, true) . PHP_EOL . PHP_EOL); 
			//}
			
			if ($content['meta']['code'] === 200) {
				if ($getReply === true) {
					$content['data']['id_str'] = $content['data']['id'];
					$return = $content['data'];
				} else {
					$return = true;
				}
			} else {
				$return = $message;
			}
			
			return $return;
			
		}
		
	}