<?php

	class oPheme {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		public $database = null;
		/**
		 * Link to UrlService module.
		 * @var UrlService
		 */
		public $urlService = null;
		/**
		 * Link to Message module.
		 * @var Message
		 */
		public $message = null;
		/**
		 * Link to URL module.
		 * @var URL
		 */
		public $url = null;
		/**
		 * Link to Crypt module.
		 * @var Crypt 
		 */
		public $crypt = null;
		/**
		 * Link to User module.
		 * @var User
		 */
		public $user = null;
		/**
		 * Link to Company module.
		 * @var Company
		 */
		public $company = null;
		/**
		 * Link to Login module.
		 * @var Login
		 */
		public $login = null;
		/**
		 * Link to Register module.
		 * @var Register
		 */
		public $register = null;
		/**
		 * Link to Token module.
		 * @var Token
		 */
		public $token = null;
		/**
		 * Link to Session module.
		 * @var Session
		 */
		public $session = null;
		/**
		 * Link to Discover module.
		 * @var Discover
		 */
		public $discover = null;
		/**
		 * Link to Campaign module.
		 * @var Campaign
		 */
		public $campaign = null;
		/**
		 * Link to Job module.
		 * @var Job
		 */
		public $job = null;
		/**
		 * Link to Callback module.
		 * @var Callback
		 */
		public $callback = null;
		/**
		 * Link to Smarty module.
		 * @var Smarty
		 */
		public $smarty = null;
		/**
		 * Link to Email module.
		 * @var Email
		 */
		public $email = null;
		/**
		 * Link to Log module.
		 * @var Log
		 */
		public $log = null;
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
		 * Link to SocialMedia module.
		 * @var SocialMedia
		 */
		public $socialMedia = null;
		
		public function __construct($error_log) {
			
			global $__oMyDB__, $__oMoDB__;
			
			$this->database = new Database($__oMyDB__, $__oMoDB__, $error_log);
			$this->urlService = new UrlService($this->database);
//			$this->message = new Message($this->database);
//			$this->smarty = new Smarty();
//			$this->log = new Log($this->database);
//			$this->url = new URL();
//			$this->crypt = new Crypt();
//			$this->session = new Session($this->database);
//			$this->company = new Company($this->database);
//			$this->token = new Token($this->database);
//			$this->register = new Register($this->database, $this->token, $this->company, $this->crypt);
//			$this->login = new Login($this->database, $this->register);
//			$this->user = new User($this->database);//, $this->login, $this->company);
			$this->twitter = new Twitter($this->database);
			$this->instagram = new Instagram($this->database);
			$this->socialMedia = new SocialMedia($this->database, $this->twitter, $this->instagram);
			$this->discover = new Discover($this->database, $this->twitter, $this->instagram);
//			$this->campaign = new Campaign($this->database, $this->twitter, $this->instagram);
			$this->job = new Job($this->database, $this->discover, $this->campaign, $this->urlService, $this->twitter, $this->instagram, $this->company);
//			$this->callback = new Callback($this->database, $this->twitter, $this->instagram);
//			$this->email = new Email($this->smarty);
			
		}
		
		/**
		 * Check whether $module exists.
		 * 
		 * @param string $module App module.
		 * @return boolean
		 */
		public function isModule($module) {
			return is_file(__oMOD__ . '/' . $module . '.php');
		}
		
		/**
		 * Returns the absolute path of requested $module, if exists, otherwise returns __oMOD_DEFAULT__ path.
		 * 
		 * @param string $module App module.
		 * @return string File literal location.
		 */
		public function getModuleToLoad($module) {
			$module_return = __oMOD__ . '/' . $module . '.php';
			return $module_return;
		}
		
		/**
		 * Calculates Days, Hours, Minutes, and Seconds from $inputSeconds seconds.<br><br>
		 * http://stackoverflow.com/a/8273826
		 * @param integer $inputSeconds Seconds.
		 * @return array Number of Days, Hours, Minutes, Seconds.
		 */
		public function secondsToTime($inputSeconds) {

			$secondsInAMinute = 60;
			$secondsInAnHour  = 60 * $secondsInAMinute;
			$secondsInADay    = 24 * $secondsInAnHour;

			// extract days
			$days = floor($inputSeconds / $secondsInADay);

			// extract hours
			$hourSeconds = $inputSeconds % $secondsInADay;
			$hours = floor($hourSeconds / $secondsInAnHour);

			// extract minutes
			$minuteSeconds = $hourSeconds % $secondsInAnHour;
			$minutes = floor($minuteSeconds / $secondsInAMinute);

			// extract the remaining seconds
			$remainingSeconds = $minuteSeconds % $secondsInAMinute;
			$seconds = ceil($remainingSeconds);

			// return the final array
			$obj = array(
				'days' => (int) $days,
				'hours' => (int) $hours,
				'minutes' => (int) $minutes,
				'seconds' => (int) $seconds,
			);
			
			return $obj;
			
		}
		
		/**
		 * Get all system subscriptions.
		 * @return array Subs array.
		 */
		public function getSubs() {
			
			return $this->database->query(array(
				'name' => 'getSubs',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'app.subscription.limits'
			))->fetch()['getSubs'];
			
		}
		
	}