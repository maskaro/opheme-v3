<?php

	class Callback {
		
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
		
	}