<?php

	class Log {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {
			
			$this->database =& $database;
		
		}
		
		/**
		 * Logs an action in the database for later viewing.
		 * @param string $where Which form category. ('submits')
		 * @param string $type What type of form. ('contact')
		 * @param string $who User IP.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function logForm($where, $type, $who) {
			
			return (boolean) $this->database->query(array(
				'name' => 'logForm',
				'type' => 'mysql',
				'operation' => 'insert',
				'table' => 'logs.forms.' . $where,
				'columns' => array(
					'user_id' => $who,
					'form_type' => $type
				)
			))->fetch()['logForm'];

		}
		
		/**
		 * Logs an action in the database for later viewing.
		 * @param integer $userId Who performed the $action.
		 * @param string $action The message to be logged.
		 * @param boolean $error [Optional] Type of message. Set to TRUE if it's a failed action. Defaults to FALSE.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function logOperation($userId, $action, $error = false) {
			
			return (boolean) $this->database->query(array(
				'name' => 'logOperation',
				'type' => 'mysql',
				'operation' => 'insert',
				'table' => 'logs.operations',
				'columns' => array(
					'user_id' => $userId,
					'action' => $action,
					'error' => intval($error)
				)
			))->fetch()['logOperation'];

		}
		
	}