<?php

	class SocialMedia {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Link to Twitter module.
		 * @var Twitter
		 */
		public $twitter = null;
		/**
		 * Link to Instagram module.
		 * @var Instagram
		 */
		public $instagram = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database, &$twitter, &$instagram) {
			
			$this->database =& $database;
			$this->twitter =& $twitter;
			$this->instagram =& $instagram;
		
		}
		
		/**
		 * Follow Twitter user ID. Mostly used by AJAX.
		 * @param array Details array.
		 * @param boolean $ajax [Optional]<br>If requested through AJAX.
		 * @return boolean|string TRUE on success, FALSE if database failed, String if Twitter gave an error containing information.
		 */
		public function follow($details, $ajax = false) {
			
			return $this->{$details['smType']}->follow($details, $ajax);
			
		}
		
		/**
		 * Reply to Twitter message of user ID. Mostly used by AJAX.
		 * @param array Details array.
		 * @param boolean $ajax [Optional]<br>If requested through AJAX.
		 * @return boolean|string TRUE on success, String if Twitter gave an error containing information.
		 */
		public function sendReply($details, $ajax = false) {
			
			if ($ajax) {
				
				//if (!isset($details['fromInteraction'])) { $message = '@' . $details['usn'] . ' ' . $details['message']; }
				//else { 
					$message = $details['message'];
				//}
				
				$message = preg_replace('/\s+/', ' ', trim($message));
				
				if (isset($details['mId'])) { $mId = $details['mId']; } else { $mId = null; }
				
				$return = $this->{$details['smType']}->postStatus($message, $details['smId'], $mId, true);
				
				if (isset($return['id_str'])) {
					
					if (isValidTimeStamp($details['origMsgDate'])) { $origTime = $details['origMsgDate']; }
					else { $origTime = strtotime($details['origMsgDate']); }
					
					$this->database->query(array(
						'name' => 'addMessage',
						'type' => 'mysql',
						'operation' => 'insert',
						'table' => 'socialMedia.interaction',
						'columns' => array(
							'opheme_user_id' => $details['oUserId'],
							'sm_user_id' => $details['uId'],
							'sm_user_screen_name' => $details['usn'],
							'type' => 'message_out',
							'message' => $message,
							'message_id' => $return['id_str'],
							'message_added_at' => time(),
							'original_message' => $details['origMsg'],
							'original_message_id' => $mId,
							'original_message_added_at' => $origTime,
							'authKeyId' => $details['smId'],
							'authKeyType' => $details['smType'],
							'added_at' => time()
						)
					))->fetch();
					
					$return = true;
					
				}
				
				return $return;
			
			}
			
		}
		
		/**
		 * Get information on who $userID followed and who followed back.
		 * @param integer $userId Database USER_ID.
		 * @param string $smType Social Media module.
		 * @param string $type What kind of information - follow / message / favourite
		 * @param integer $since Timestamp used to filter out records.
		 * @param string $sortBy DB field to sort by.
		 * @param string $sortType Sort type. asc|desc
		 * @param integer $limit Limit number of results.
		 * @param string $smUid Social Media User ID.
		 * @return array Array containing 0 or more interactions.
		 */
		public function getInteraction($userId, $smType = null, $type = null, $since = 0, $sortBy = 'added_at', $sortType = 'desc', $limit = 0, $smUid = null) {
			
			if (is_assoc_array($userId)) { // AJAX
				$array = $userId;
				$userId = $array['userId'];
				$smType = isset($array['smType'])?$array['smType']:null;
				$type = isset($array['type'])?$array['type']:null;
				$since = isset($array['since'])?$array['since']:0;
				$sortBy = isset($array['sortBy'])?$array['sortBy']:'added_at';
				$sortType = isset($array['sortType'])?$array['sortType']:'desc';
				$limit = isset($array['limit'])?$array['limit']:0;
				$smUid = isset($array['smUid'])?$array['smUid']:null;
			}
			
			$data = array(
				'name' => 'getInteraction',
				'type' => 'mysql',
				'table' => 'socialMedia.interaction',
				'operation' => 'select',
				'where' => array(
					'opheme_user_id' => $userId,
					'added_at' => array(
						'operator' => '>=',
						'data' => $since
					)
				),
				'order' => array(
					$sortBy => $sortType
				)
			);
			if ($limit) { $data['limit'] = $limit; }
			
			if (!empty($type)) {
				$data['where']['type'] = array(
					'operator' => 'like',
					'data' => $type . '%'
				);
			}
			
			if (!empty($smType)) {
				$data['where']['authKeyType'] = $smType;
			}
			
			if (!empty($smUid)) {
				$data['where']['sm_user_id'] = $smUid;
			}
			
			$results = $this->database->query($data)->fetch()['getInteraction'];
			
			if (is_array($results)) {
				if (is_assoc_array($results)) { $results = array($results); }
			} else { $results = array(); }
			
			return $results;
			
		}
		
	}