<?php

	class Campaign {
		
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
		 * Checks whether Campaign details are correct.
		 * @param array $details Campaign $_POST info.
		 * @return boolean TRUE on valid, FALSE otherwise.
		 */
		public function isValid($details) {

			$return = array();
			
			//discover name
			if (empty($details['name'])) {
				$return[] = 'Campaign must have a Name.';
			}
			
			global $availableSMModules; $count = 0;
			foreach ($availableSMModules as $sModule) { if (!empty($details['authKey_' . $sModule])) { $count++; } }
			if ($count === 0) {
				$return[] = 'Discover must have at least one Social Media Account bound to it.';
			}
			
			/*if (empty($details['category']) || ($details['category'] === '----------') || (!in_array($details['category'], $details['cats']))) {
				$return[] = 'Campaign must have a Category, please choose one.';
			}*/
			
			//numeric radius
			if (empty($details['hourly_limit']) || !is_numeric($details['hourly_limit'])) {
				$return[] = 'Campaign response Hourly Limit must be set and a valid whole number.';
			}

			//max responses
			$responses = 6;
			if ($details['hourly_limit'] < 1 || $details['hourly_limit'] > $responses) {
				$return[] = 'Campaign response Hourly Limit must be between 1 and ' . $responses . ' Responses.';
			}

			//discover filter
			if (!empty($details['filter'])) {
				if (strlen($details['filter']) < 1) {
					$return[] = 'Campaign keyword should have a minimum of 1 character.';
				}
			}

			if (!empty($details['filter_ex'])) {
				$filter_ex = explode(' ', $details['filter_ex']);
				foreach ($filter_ex as $keyword) {
					if (strlen($keyword) < 3) {
						$return[] = 'Campaign exclusion keywords should have a minimum of 3 characters.';
					}
				}
			}
			
			if (empty($details['text']) || strlen($details['text']) < 5) {
				$return[] = 'Campaign must have a message, minimum of 5 characters.';
			}

			if (empty($details['response_text']) || strlen($details['response_text']) < 10 || strlen($details['response_text']) > 110 || stripos($details['response_text'], '%r') === false || stripos($details['response_text'], '%c') === false) {
				$return[] = 'Campaign must have a Tweet response message, between 10 and 110 characters, and MUST contain both %r and %c.';
			}

			if (is_uploaded_file($details['uploaded_banner_file']['tmp_name'])) {
				if ($details['uploaded_banner_file']['error'] != UPLOAD_ERR_OK) {
					$return[] = 'Campaign banner could not be uploaded. Please report submit a report at ' . __oCompanySupport__ . '. PHP upload error code: <strong>' . $details['uploaded_banner_file']['error'] . '</strong>.';
				}
				$allowedExts = array('png', 'jpg', 'jpeg'); $allowedTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
				$temp = explode('.', $details['uploaded_banner_file']['name']); $extension = strtolower(end($temp));
				$type = exif_imagetype($details['uploaded_banner_file']['tmp_name']);
				$max_size = intval(ini_get('upload_max_filesize')) * 1024 * 1024;
				if (!(in_array($type, $allowedTypes) && ($details['uploaded_banner_file']['size'] <= $max_size) && in_array($extension, $allowedExts))) {
					$return[] = 'Campaign banner could not be uploaded. Allowed image types: ' . implode(',', $allowedExts) . '. Maximum file size: ' . ini_get('upload_max_filesize') . '.';
					return false;
				}
			}

			//at least one week day selected
			if (empty($details['days']) || count($details['days']) == 0) {
				$return[] = 'You must select at least one week day.';
			}

			if (empty($details['time_start']) || !isTimeValid($details['time_start'])) {
				$return[] = 'Campaign must have a valid start time.';
			}
			if (empty($details['time_end']) || strlen($details['time_end']) == 0) {
				$return[] = 'Campaign must have a valid end time.';
			}
			
			if (empty($details['date_start']) || !isDateValid($details['date_start'])) {
				$return[] = 'Campaign must have a valid start date.';
			}
			if (empty($details['date_end']) || !isDateValid($details['date_end'])) {
				$return[] = 'Campaign must have a valid end date.';
			}

			if (date('H:i', strtotime($details['time_start'])) > date('H:i', strtotime($details['time_end']))) {
				$return[] = 'Start time cannot be after the end time.';
			}

			if (date('Y-m-d', strtotime($details['date_start'])) > date('Y-m-d', strtotime($details['date_end']))) {
				$return[] = 'Start date cannot be after the end date.';
			}

			if (date('Y-m-d', strtotime(time())) > date('Y-m-d', strtotime($details['date_start']))) {
				$return[] = 'Start date cannot be in the past.';
			}

			if (date('Y-m-d', strtotime(time())) > date('Y-m-d', strtotime($details['date_end']))) {
				$return[] = 'End date cannot be in the past.';
			}

			//latitude and longitude have to be numeric
			if (empty($details['centre_lat']) || empty($details['centre_lng']) || !is_numeric($details['centre_lat']) || !is_numeric($details['centre_lng'])) {
				$return[] = 'A Location must be set.';
			}

			//max miles
			if (empty($details['radius']) || !is_numeric($details['radius']) || ((floatval($details['radius']) - 1) > 0) || floatval($details['radius']) < 0) {
				$return[] = 'Radius must be between 0.1 and 1 miles.';
			}
			
			if (count($return) === 0) { $return = true; }
			
			return $return;
			
		}
		
		/**
		 * Create Campaign in database.
		 * @param array $details Campaign info array.
		 * @return boolean TRUE on success, FALSE otherwise.
		 */
		public function save(Array $details, $edit = false) {
			
			$days = implode(',', $details['days']);
			
			$removeThese = array('script', 'js', 'javascript', 'document');
			$text = str_ireplace($removeThese, '', $details['text']);
			$response_text = str_ireplace($removeThese, '', $details['response_text']);
			
			$jobTimeLimit = (!empty($details['allowance']['jobTimeLimit'])?$details['allowance']['jobTimeLimit']:'0 hours');
			
			$data = array(
				'name' => 'saveCampaign',
				'type' => 'mysql',
				'table' => 'app.campaign.jobs',
				'columns' => array(
					'user_id' => $details['user_id'],
					'name' => $details['name'],
					'category' => '',//$details['category'],
					'hourly_limit' => $details['hourly_limit'],
					'text' => $text,
					'response_text' => $response_text,
					'filter' => $details['filter'],
					'filter_ex' => $details['filter_ex'],
					'centre_lat' => $details['centre_lat'],
					'centre_lng' => $details['centre_lng'],
					'radius' => $details['radius'],
					'weekdays' => $days,
					'start_time' => $details['time_start'],
					'end_time' => $details['time_end'],
					'start_date' => $details['date_start'],
					'end_date' => $details['date_end'],
					'messages_limit' => $details['allowance']['jobMessageLimit'],
					'time_limit' => $jobTimeLimit
				),
				'id' => true
			);
			
			if (is_uploaded_file($details['uploaded_banner_file']['tmp_name'])) {
				$data['columns']['banner'] = base64_encode(file_get_contents($details['uploaded_banner_file']['tmp_name']));
				$data['columns']['banner_type'] = $details['uploaded_banner_file']['type'];
			}
			
			if ($edit) {
				
				$data['operation'] = 'update';
				$data['where'] = array(
					'id' => $details['id']
				);
				
			} else {
				
				$data['operation'] = 'insert';
				$data['columns']['since_id'] = 0;
				$data['columns']['suspended'] = 0;
				$data['columns']['company_id'] = __oCompanyID__;
				
				if (empty($data['columns']['banner'])) {
					
					$data['columns']['banner'] = '';
					$data['columns']['banner_type'] = '';
					
				}
						
			}
			
			$result = $this->database->query($data)->fetch()['saveCampaign'];
			
			global $availableSMModules; $count = 0;
			foreach ($availableSMModules as $sModule) { if (!empty($details['authKey_' . $sModule])) { $count++; } }
			
			if ($result || $count) {
				
				$id = ($data['operation'] === 'insert'?intval($result):$details['id']);
				$jobType = 'campaign';
				
				// remove all current tokens
				$data = array(
					'name' => 'addAuthKeys',
					'type' => 'mysql',
					'table' => 'app.jobs.tokens',
					'operation' => 'delete',
					'where' => array(
						'job_type' => $jobType,
						'job_id' => $id
					)
				);
				$this->database->query($data)->fetch();
				
				foreach ($availableSMModules as $sModule) {
				
					if (empty($details['authKey_' . $sModule])) { continue; }
					
					// add new tokens
					foreach ($details['authKey_' . $sModule] as $tokenInfo) {

						list($tokenId, $tokenType) = explode(',', $tokenInfo);

						$data = array(
							'name' => 'addAuthKeys',
							'type' => 'mysql',
							'table' => 'app.jobs.tokens',
							'operation' => 'insert',
							'columns' => array(
								'user_id' => $details['user_id'],
								'job_type' => $jobType,
								'job_id' => $id,
								'token_type' => $tokenType,
								'token_id' => $tokenId
							)
						);

						$result = $this->database->query($data)->fetch()['addAuthKeys'];

					}
				
				}
				
			}
			
			return $result;
			
		}
		
	}