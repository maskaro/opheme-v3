<?php

	class Discover {
		
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
		 * Checks whether Discover details are correct.
		 * @param array $details Discover $_POST info.
		 * @return boolean TRUE on valid, FALSE otherwise.
		 */
		public function isValid($details) {

			$return = array();
			
			//discover name
			if (empty($details['name'])) {
				$return[] = 'Discover must have a name.';
			}
			
			global $availableSMModules; $count = 0;
			foreach ($availableSMModules as $sModule) { if (!empty($details['authKey_' . $sModule])) { $count++; } }
			if ($count === 0) {
				$return[] = 'Discover must have at least one Social Media Account bound to it.';
			}
			
			if (!isset($details['messageLifeSpanLimit']) || (!array_key_exists($details['messageLifeSpanLimit'], $details['lifeSpans']))) {
				$return[] = 'Discover must have a Message Type, please choose one.';
			}

			//discover filter
			if (!empty($details['filter'])) {
				if (strlen($details['filter']) < 1) {
					$return[] = 'Discover keyword should have a minimum of 1 character.';
				}
			}

			if (!empty($details['filter_ex'])) {
				$filter_ex = explode(' ', $details['filter_ex']);
				foreach ($filter_ex as $keyword) {
					if (strlen($keyword) < 1) {
						$return[] = 'Discover exclusion keywords should have a minimum of 1 character.';
					}
				}
			}

			//at least one week day selected
			//if (empty($details['days']) || count($details['days']) == 0) {
				//$return[] = 'You must select at least one week day.';
			//}

			if (!empty($details['time_start']) && strlen($details['time_start']) > 0) {
				if (!isTimeValid($details['time_start'])) {
					$return[] = 'Discover must have a valid start time.';
				}
				if (empty($details['time_end']) || strlen($details['time_end']) == 0) {
					$return[] = 'Discover must have a valid end time.';
				}
			}

			if (!empty($details['time_end']) && strlen($details['time_end']) > 0) {
				if (!isTimeValid($details['time_end'])) {
					$return[] = 'Discover must have a valid end time.';
				}
				if (empty($details['time_start']) || strlen($details['time_start']) == 0) {
					$return[] = 'Discover must have a valid start time.';
				}
			}

			if (!empty($details['date_start']) && strlen($details['date_start']) > 0) {
				if (!isDateValid($details['date_start'])) {
					$return[] = 'Discover must have a valid start date.';
				}
				if (empty($details['date_end']) || strlen($details['date_end']) == 0) {
					$return[] = 'Discover must have a valid end date.';
				}
			}

			if (!empty($details['date_end']) && strlen($details['date_end']) > 0) {
				if (!isDateValid($details['date_end'])) {
					$return[] = 'Discover must have a valid end date.';
				}
				if (empty($details['date_start']) || strlen($details['date_start']) == 0) {
					$return[] = 'Discover must have a valid start date.';
				}
			}

			if (!empty($details['time_start']) && !empty($details['time_end']) && strlen($details['time_start']) > 0 && strlen($details['time_end']) > 0) {
				if (date('H:i', strtotime($details['time_start'])) > date('H:i', strtotime($details['time_end']))) {
					$return[] = 'Start time cannot be after the end time.';
				}
			}

			if (!empty($details['date_start']) && !empty($details['date_end']) && strlen($details['date_start']) > 0 && strlen($details['date_end']) > 0) {
				if (date('Y-m-d', strtotime($details['date_start'])) > date('Y-m-d', strtotime($details['date_end']))) {
					$return[] = 'Start date cannot be after the end date.';
				}
			}

			if (!empty($details['date_start']) && strlen($details['date_start']) > 0) {
				if (date('Y-m-d', strtotime(time())) > date('Y-m-d', strtotime($details['date_start']))) {
					$return[] = 'Start date cannot be in the past.';
				}
			}

			if (!empty($details['date_end']) && strlen($details['date_end']) > 0) {
				if (date('Y-m-d', strtotime(time())) > date('Y-m-d', strtotime($details['date_end']))) {
					$return[] = 'End date cannot be in the past.';
				}
			}

			//latitude and longitude have to be numeric
			if (empty($details['centre_lat']) || empty($details['centre_lng']) || !is_numeric($details['centre_lat']) || !is_numeric($details['centre_lng'])) {
				$return[] = 'A Location must be set.';
			}
			
			//max miles
			if (empty($details['radius']) || !is_numeric($details['radius']) || ((floatval($details['radius']) - 10) > 0) || floatval($details['radius']) < 0) {
				$return[] = 'Radius must be between 0.1 and 10 miles.';
			}
			
			if (count($return) === 0) { $return = true; }
			
			return $return;
			
		}
		
		/**
		 * Create Discover in database.
		 * @param array $details Discover info array.
		 * @return boolean TRUE on success, FALSE otherwise.
		 */
		public function save(Array $details, $edit = false) {
			
			$jobTimeLimit = (!empty($details['allowance']['jobTimeLimit'])?$details['allowance']['jobTimeLimit']:'0 hours');
			
			$data = array(
				'name' => 'saveDiscover',
				'type' => 'mysql',
				'table' => 'app.discover.jobs',
				'columns' => array(
					'user_id' => $details['user_id'],
					'name' => $details['name'],
					'filter' => $details['filter'],
					'filter_ex' => $details['filter_ex'],
					'centre_lat' => $details['centre_lat'],
					'centre_lng' => $details['centre_lng'],
					'radius' => $details['radius'],
					'messages_limit' => $details['allowance']['jobMessageLimit'],
					'time_limit' => $jobTimeLimit,
					'messageLifeSpanLimit' => $details['messageLifeSpanLimit']
				),
				'id' => true
			);
			
			if (!empty($details['date_start']) && strlen($details['date_start']) > 0) { $data['columns']['start_date'] = $details['date_start']; }
			if (!empty($details['date_end']) && strlen($details['date_end']) > 0) { $data['columns']['end_date'] = $details['date_end']; }
			if (!empty($details['time_start']) && strlen($details['time_start']) > 0) { $data['columns']['start_time'] = $details['time_start']; }
			if (!empty($details['time_end']) && strlen($details['time_end']) > 0) { $data['columns']['end_time'] = $details['time_end']; }
			if (!empty($details['days'])) { $data['columns']['weekdays'] = implode(',', $details['days']); }
			
			if (empty($data['columns']['start_date'])) { $data['columns']['start_date'] = '0000-00-00'; }
			if (empty($data['columns']['end_date'])) { $data['columns']['end_date'] = '0000-00-00'; }
			if (empty($data['columns']['start_time'])) { $data['columns']['start_time'] = '00:00:00'; }
			if (empty($data['columns']['end_time'])) { $data['columns']['end_time'] = '00:00:00'; }
			if (empty($data['columns']['weekdays'])) { $data['columns']['weekdays'] = ''; }
			
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
				
			}
			
			$result = $this->database->query($data)->fetch()['saveDiscover'];
			
			global $availableSMModules; $count = 0;
			foreach ($availableSMModules as $sModule) { if (!empty($details['authKey_' . $sModule])) { $count++; } }
			
			if ($result || $count) {
				
				$id = ($data['operation'] === 'insert'?intval($result):$details['id']);
				$jobType = 'discover';
				
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