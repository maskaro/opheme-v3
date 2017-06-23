<?php

	class Company {
		
		/**
		 * Link to Database module.
		 * @var Database
		 */
		private $database = null;
		/**
		 * Company ID.
		 * @var string 
		 */
		private $company = null;
		/**
		 * Company Info.
		 * @var array
		 */
		private $companyInfo = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$database) {
			
			$this->database =& $database;
			
			$this->company = __oCompanyID__;
			$this->getAvailableModules();
			
		}
		
		/**
		 * Load company modules into internal array.
		 */
		private function getAvailableModules() {
			
			$this->companyInfo['modules'] = explode(',', $this->database->query(array(
				'name' => 'getAllowedModules',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'company',
				'columns' => array(
					'modules'
				),
				'where' => array(
					'company_id' => $this->company
				)
			))->fetch()['getAllowedModules']['modules']);
			
		}
		
		/**
		 * Set company modules into Database.
		 * @param integer $id Company database ID.
		 * @param string $moduleStr String of modules, separated by commas.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function setAvailableModules($id, $moduleStr) {
			
			return $this->database->query(array(
				'name' => 'setAllowedModules',
				'type' => 'mysql',
				'operation' => 'update',
				'table' => 'company',
				'columns' => array(
					'modules' => $moduleStr
				),
				'where' => array(
					'id' => $id
				)
			))->fetch()['setAllowedModules'];
			
		}
		
		/**
		 * Set company representative into Database.
		 * @param integer $id Company database ID.
		 * @param string $reprStr String of User IDs, separated by commas.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function setRepresentatives($id, $reprStr) {
			
			return $this->database->query(array(
				'name' => 'setRepresentatives',
				'type' => 'mysql',
				'operation' => 'update',
				'table' => 'company',
				'columns' => array(
					'user_id' => $reprStr
				),
				'where' => array(
					'id' => $id
				)
			))->fetch()['setRepresentatives'];
			
		}
		
		/**
		 * Set company modules into Database.
		 * @param string $compId Company identifier.
		 * @param string $repr String of User ID to act as reprentatives, separated by commas.
		 * @param string $modules List of allowed modules, separated by commas.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function create($compId, $repr, $modules) {
			
			return (boolean) $this->database->query(array(
				'name' => 'create',
				'type' => 'mysql',
				'operation' => 'insert',
				'table' => 'company',
				'columns' => array(
					'company_id' => $compId,
					'user_id' => $repr,
					'modules' => $modules
				)
			))->fetch()['create'];
			
		}
		
		/**
		 * Set company modules into Database.
		 * @param integer $id Company database ID.
		 * @param string $moduleStr String of modules, separated by commas.
		 * @return boolean TRUE if successful, FALSE otherwise.
		 */
		public function remove($id) {
			
			return (boolean) $this->database->query(array(
				'name' => 'remove',
				'type' => 'mysql',
				'operation' => 'delete',
				'table' => 'company',
				'where' => array(
					'id' => $id
				)
			))->fetch()['remove'];
			
		}
		
		/**
		 * Get data about Company.
		 * @param string|int $info [Optional]<br>Company string identifier ('opheme'). Company ID if integer.
		 * @return array Company data. Array with 0 or more companies.
		 */
		public function getCompany($info = null) {
			
			$data = array(
				'name' => 'getCompany',
				'type' => 'mysql',
				'operation' => 'select',
				'table' => 'company'
			);
			
			if (is_numeric($info)) {
				$data['where'] = array(
					'id' => $info
				);
			} elseif (is_string($info)) {
				$data['where'] = array(
					'company_id' => $info
				);
			}
			
			$result = $this->database->query($data)->fetch()['getCompany'];
			
			if ($result) {
				if (is_assoc_array($result)) { $return = array($result); }
				else { $return = $result; }
			} else { $return = array(); }
			
			return $return;
			
		}
		
		/**
		 * Checks Company for $module access.
		 * @param string $module Module to check.
		 * @return boolean TRUE if allowed access, FALSE otherwise.
		 */
		public function hasAccessTo($module) {
			
			return in_array($module, $this->companyInfo['modules']);
			
		}
		
		/**
		 * Retrieves an array of available company modules.
		 * @return array Associative array of available company modules. ['module' => true]
		 */
		public function getModules() {
			
			$modules = array();
			foreach ($this->companyInfo['modules'] as $mod) { $modules[$mod] = true; }
			
			return $modules;
			
		}
		
	}