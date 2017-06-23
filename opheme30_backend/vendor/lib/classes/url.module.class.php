<?php

	class URL {
		
		private $uri_data = array();
		private $uri_redirect = null;
		private $uri_redirect_flag = false;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct() {
			
			$this->parseURI();
			
		}
		
		private function parseURI() {
			
			$this->uri_data['module'] = $this->filter('module');
			$this->uri_data['task'] = $this->filter('task');
			$this->uri_data['extra'] = $this->filter('extra');
			
		}
		
		private function filter($param) {
			return filter_input(INPUT_GET, strtolower($param), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		
		/**
		 * Return URI parameter value.
		 * @param string $param What URI parameter to return. Accepted params: module, task, extra.
		 * @return mixed Parameter value, FALSE if failed to parse URI, NULL if there is none.
		*/
		public function fetch($param) {
			
			return $this->uri_data[$param];
			
		}
		
		/**
		 * Sets redirect if $uri given, else gets current redirect URI.
		 * @param string $uri [Optional]<br>URI to set redirect to.
		 * @return string [Only if no $uri given]<br>URI string.
		*/
		public function redirectTo($uri = null) {
			
			if ($uri) { 
				$this->uri_redirect = $uri;
				$this->uri_redirect_flag = true;
			} else {
				$uri = $this->uri_redirect;
				$this->uri_redirect = null;
				$this->uri_redirect_flag = false;
				return $uri;
			}
			
		}
		
		/**
		 * Check Redirect status.
		 * @return boolean TRUE if Redirect has been set, FALSE otherwise.
		 */
		public function issetRedirect() {
			return $this->uri_redirect_flag;
		}
		
		/**
		 * Clear Redirect, if any.
		 */
		public function clearRedirect() {
			$this->uri_redirect = null;
			$this->uri_redirect_flag = false;
		}
		
	}