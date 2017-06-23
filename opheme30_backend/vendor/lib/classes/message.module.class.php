<?php

	class Message {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database = null) {
			
			$this->database =& $database;
			
		}
		
		/**
		 * Returns a specific message from Session.
		 * @param string $module Module name.
		 * @param string $type Message type. ['OK', 'ERR', 'WAR', 'INFO']
		 * @param string $id Message ID.
		 * @return string Message.
		 */
		public function getOne($module, $type, $id) {
			$message = null;
			if (isset($_SESSION['messages'][$module][$type][$id])) { 
				$message = $_SESSION['messages'][$module][$type][$id];
				unset($_SESSION['messages'][$module][$type][$id]);
			}
			return $message;
		
		}
		
		/**
		 * Returns all messages from Session.
		 * 
		 * @param string $module [Optional]<br>Only messages from $module will be returned.
		 * @param string $type [Optional] Only messages of $type will be returned.<br>
		 *		OK - success messages<br>ERR - error messages<br>WAR - warning messages<br>INFO - informational messages<br>CRITICAL - database failure messages
		 * @return array Messages.
		 */
		public function getAll($module = null, $type = null) {
			
			//get all database errors, if any
			$db_err = !empty($this->database)?$this->database->getErrorMessages():null;
			if ($db_err) { $_SESSION['messages']['database']['CRITICAL'] = $db_err; unset($db_err); }
			
			/**
			 * Slim Framework message integration.
			 */
			if (!empty($_SESSION['slim.flash']) && is_array($_SESSION['slim.flash'])) {
				foreach ($_SESSION['slim.flash'] as $noticeType => $noticeMessage) {
					switch($noticeType) {
						case 'success':
							$_SESSION['messages']['billing']['OK'][] = $noticeMessage;
							break;
						case 'info':
							$_SESSION['messages']['billing']['INFO'][] = $noticeMessage;
							break;
						case 'warning':
							$_SESSION['messages']['billing']['WAR'][] = $noticeMessage;
							break;
						case 'error':
							$_SESSION['messages']['billing']['ERR'][] = $noticeMessage;
							break;
						default:
							break;
					}
				}
				unset($_SESSION['slim.flash']);
			}
			
			/**
			 * Messages array - will contain all messages from $module of $type, if given, otherwise all.
			 */
			$messages_return = null;
			
			if (isset($_SESSION['messages']) && count($_SESSION['messages'])) {
				if ($module) {
					if ($type) {
						if (isset($_SESSION['messages'][$module][$type])) {
							$messages_return = $_SESSION['messages'][$module][$type];
							unset($_SESSION['messages'][$module][$type]);
							if (count($_SESSION['messages'][$module]) === 0) { unset($_SESSION['messages'][$module]); }
						}
					} else {
						if (isset($_SESSION['messages'][$module])) {
							$messages_return = $_SESSION['messages'][$module];
							unset($_SESSION['messages'][$module]);
						}
					}
				} else {
					if ($type) {
						foreach ($_SESSION['messages'] as $moduleName => $messages) {
							if (isset($messages[$type])) {
								$messages_return[$moduleName] = $messages[$type];
								unset($_SESSION['messages'][$moduleName][$type]);
								if (count($_SESSION['messages'][$moduleName]) === 0) { unset($_SESSION['messages'][$moduleName]); }
							}
						}
					} else {
						$messages_return = $_SESSION['messages'];
						unset($_SESSION['messages']);
					}
				}
				if (count($_SESSION['messages']) === 0) { unset($_SESSION['messages']); }
			}
			
			return $messages_return;
			
		}
		
		/**
		 * Sets a message in Session.
		 * @param string $module Module name.
		 * @param string $type Message type. ['OK', 'ERR', 'WAR', 'INFO', 'CRITICAL']
		 * @param string $id Message ID.
		 * @param string $content Message body.
		 */
		public function set($module, $type, $id, $content) {
			if (!in_array($type, array('OK', 'ERR', 'WAR', 'INFO', 'CRITICAL'))) { return trigger_error('Incorrect usage of ' . __CLASS__ . '->' . __METHOD__, E_ERROR); }
			$_SESSION['messages'][$module][$type][$id] = $content;
		}

	}
	