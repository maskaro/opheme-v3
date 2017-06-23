<?php

	// TODO: the $error_log in this file isn't working, meant to get a value from the global space

	class Database {
		
		private $debug = false;
		
		private $errorLog = '';
		
		private $mysqlTables = null;
		private $mysqlOpt = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET CHARACTER SET utf8', PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::MYSQL_ATTR_FOUND_ROWS => true);
		private $mysqlConn = null;
		private $mysqlQueries = null;
		private $mysqlResults = null;
		private $mysqlCustomQueries = 0;
		
		private $mongoTables = null;
		private $mongoConn = null;
		private $mongoQueries = null;
		private $mongoResults = null;
		
		private $request = null;
		private $exampleRequest = array(
			'name' => 'query_name_identifier',
			'type' => 'mysql | mongo',
			'database' => 'databaseName - Not used for MongoDB, Optional for MySQL.',
			'operation' => 'insert | replace | update | delete | select | count',
			'table' => 'tableIdentifier',
			'columns' => array(
				'notice1' => 'Not for COUNT or DELETE operations.',
				'notice2' => 'Omit if you want all columns for SELECT.',
				'notice3' => 'SELECT will treat `value1..N` as column Aliases.',
				'columnName1' => 'value1',
				'columnNameN' => 'valueN'
			),
			'where' => array(
				'notice1' => 'Optional except for DELETE operations.',
				'notice2' => 'operator - Comparison sign.',
				'notice3' => 'link - logical link to previous column, only from 2nd column (and, or) - Only for MySQL',
				'columnName1' => array(
					'operator' => '= | > | < | ...',
					'data' => 'filterValue1'
				),
				'columnNameN' => array(
					'operator' => '= | > | < | ...',
					'data' => 'filterValueN',
					'link' => 'and | or'
				)
			),
			'order' => array(
				'columnName1' => 'asc | desc',
				'columnNameN' => 'asc | desc'
			),
			'limit' => '1 | 0,10 - for MongoDB this is valid only for SELECT operations.',
			'id' => 'No value needed - Only for MySQL',
			'upsert' => array(
				'notice' => 'Only for MongoDB UPDATE operations.',
				'boolean'
			),
			'replace' => array(
				'notice' => 'Only for MongoDB UPDATE operations.',
				'boolean'
			),
			'howMany' => array(
				'notice' => 'Only for MongoDB SELECT operations.',
				'one | all'
			)
		);
		
		private $errors = null;
		
		public function __construct(Array &$my_db, Array &$mo_db, $error_log) {
			
			$this->debug = __oDEBUG_OPS__;
			
			$this->errorLog = $error_log;
			
			$this->mysqlTables = $my_db['db.tables'];
			$this->mongoTables = $mo_db['db.tables'];
			
			try {
				
				$this->mysqlConn = new PDO("mysql:host=" . $my_db['db.connection.host'] . ";port=" . $my_db['db.connection.port'] . ";dbname=" . $my_db['db.connection.db'] . ";charset=utf8", $my_db['db.connection.user'], $my_db['db.connection.pass'], $this->mysqlOpt);
				$this->mysqlConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->mysqlConn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				
				if ($this->debug) {
					MongoLog::setModule( MongoLog::IO );
					MongoLog::setLevel( MongoLog::ALL );
				}
				
				$this->mongoConn = new MongoClient("mongodb://" . $mo_db["db.connection.host"] . ":" . $mo_db["db.connection.port"]);
				
			} catch(Exception $ex) { $this->setErrorMessage('Database - Code: ' . $ex->getCode() . ' | Message: ' . $ex->getMessage()); }
			
		}
		
		public function __destruct() {
			
			$this->queryMySQLCustom(array(
				'query' => 'SET foreign_key_checks = 1',
				'no_return' => true
			))->fetch();
			
			$this->disconnect();
			
		}
		
		private function disconnect() {
			
			$this->mysqlConn = null;
			$this->mongoConn = null;
			
		}
		
		/**
		 * Sets $ex as an error message.
		 */
		public function setErrorMessage($ex) {
			
			if (!$this->errors) { $this->errors = array(); }
			
			$errorMessage = 'Failed to run query! Reason <b>' . $ex . '</b>. Please submit a report at ' . __oCompanySupport__ . ' if this error persists.';
			$this->errors[] = $errorMessage;
			
			if ($this->debug) { $error_log = $this->errorLog; $error_message = (PHP_EOL . PHP_EOL . 'Database Exception: ' . $errorMessage . PHP_EOL . PHP_EOL); error_log($error_message, 3, $error_log); }
			//if (__oDEBUG_BACKEND__) { $error_log = $this->errorLog; $error_message = ('Database Exception: ' . $errorMessage . '.'); error_log($error_message, 3, $error_log); }
			
		}
		
		/**
		 * Returns all error messages. Resets all errors after return.
		 */
		public function getErrorMessages() {
			
			$errors = $this->errors;
			
			$this->resetErrorMessages();
			
			return $errors;
			
		}
		
		/**
		 * Reset any errors that might have occurred recently.
		 */
		public function resetErrorMessages() { $this->errors = null; }
		
		/**
		 * Available MySQL tables:<br>
		 * 'app.campaign.jobs'
		 * 'app.discover.jobs'
		 * 'app.jobs.messages'
		 * 'app.jobs.share'
		 * 'app.jobs.tokens'
		 * 'app.subscription.limits'
		 * 'app.urls'
		 * 'company'
		 * 'company.modules'
		 * 'email.notification.history'
		 * 'instagram.keys'
		 * 'logs.forms.submits'
		 * 'logs.operations'
		 * 'twitter.campaign.blacklist'
		 * 'twitter.campaign.preferences'
		 * 'socialMedia.interaction'
		 * 'twitter.keys'
		 * 'user.accounts'
		 * 'user.login.attempts'
		 * 'user.login.sessions'
		 * 'user.modules'
		 * 'user.registration.tokens'
		 * 
		 * Available MongoDB tables:<br>
		 * 'job.campaign'
		 * 'job.campaign.sent'
		 * 'job.discover'
		 * 'message.twitter'
		 * 'message.twitter.sent'
		 * 'ui.job.campaign.timestamps'
		 * 'ui.job.discover.timestamps'
		 * 
		 * @param array $request array(<br>
		 *	&#09;'name' => 'query_name_identifier',<br>
		 *	&#09;'type' => 'mysql | mongo',<br>
		 *	&#09;'database' => 'databaseName', //Optional for MySQL //Not for MongoDB.<br>
		 *	&#09;'operation' => 'insert | replace | update | delete | select | count',<br>
		 *	&#09;'table' => 'tableIdentifier',<br>
		 *	&#09;'columns' => array(
		 *		//Omit if you want all columns for SELECT.
		 *		//SELECT will treat `value1..N` as column Aliases.
		 *		//NOT for COUNT or DELETE.<br>
		 *		&#09;&#09;'columnName1' => 'value1',<br>
		 *		&#09;&#09;'columnNameN' => 'valueN'<br>
		 *	&#09;),<br>
		 *	&#09;'where' => array( //Optional except for DELETE operations. //operator - Comparison sign. //condition - logical link to previous column, only from 2nd column (and, or)<br>
		 *		&#09;&#09;'columnName1' => array(<br>
		 *			&#09;&#09;&#09;'operator' => '= | > | < | ...',<br>
		 *			&#09;&#09;&#09;'data' => 'filterValue1'<br>
		 *		&#09;&#09;),<br>
		 *		&#09;&#09;'columnName2' => array(<br>
		 *			&#09;&#09;&#09;'operator' => '= | > | < | ...',<br>
		 *			&#09;&#09;&#09;'data' => 'filterValueN',<br>
		 *			&#09;&#09;&#09;'condition' => 'and | or' [optional, assumed 'and'] //Only for MySQL<br>
		 *		&#09;&#09;)<br>
		 *		&#09;&#09;'columnNameN' => array(<br>
		 *			&#09;&#09;&#09;'operator' => '= | > | < | ...',<br>
		 *			&#09;&#09;&#09;'data' => 'filterValueN',<br>
		 *			&#09;&#09;&#09;'as_is' => true [optional, passes data without filtering/escaping] //Only for MySQL<br>
		 *		&#09;&#09;)<br>
		 *	&#09;),<br>
		 *	&#09;OR<br>
		 *	&#09;'where' => array( //Optional except for DELETE operations. //Assumes = operator and AND link<br>
		 *		&#09;&#09;'columnName1' => 'value1',<br>
		 *		&#09;&#09;'columnNameN' => 'valueN'<br>
		 *	&#09;),<br>
		 *	&#09;'order' => array(<br>
		 *		&#09;&#09;'columnName1' => 'asc | desc',<br>
		 *		&#09;&#09;'columnNameN' => 'asc | desc'<br>
		 *	&#09;),<br>
		 *	&#09;'limit' => '1 | 0,10', //Only for MySQL.<br>
		 *	&#09;'limit' => '1', //Only for MongoDB SELECT operations.<br>
		 *	&#09;'id' => true, //Optionally returns the ID of the INSERT operation.<br>
		 *	&#09;'upsert' => boolean, //Only for MongoDB UPDATE operations.<br>
		 *	&#09;'replace' => boolean, //Only for MongoDB UPDATE operations.<br>
		 *	&#09;'howMany' => 'one | all' //Only for MongoDB SELECT operations.<br>
		 * );<br>
		 * <br>
		 */
		public function query($request = null) {
			
			if (
				$request === null ||
				!is_array($request) ||
				!isset($request['name']) ||
				!isset($request['type']) ||
				(
					$request['type'] !== 'mysql' &&
					$request['type'] !== 'mongo'
				) ||
				!isset($request['operation']) ||
				(
					$request['operation'] !== 'insert' &&
					$request['operation'] !== 'replace' &&
					$request['operation'] !== 'update' &&
					$request['operation'] !== 'delete' &&
					$request['operation'] !== 'select' &&
					$request['operation'] !== 'count'
				) ||
				!isset($request['table']) ||
				(
					$request['type'] === 'mongo' &&
					(
						isset($request['database']) ||
						!isset($this->mongoTables[$request['table']]) ||
						$request['operation'] === 'replace' ||
						(
							$request['operation'] === 'select' &&
							(
								!isset($request['howMany']) ||
								(
									$request['howMany'] !== 'all' &&
									$request['howMany'] !== 'one'
								)
							)
						)
					)
				) ||
				(
					$request['type'] === 'mysql' &&
					(
						!isset($this->mysqlTables[$request['table']]) ||
						(
							(
								$request['operation'] === 'replace'
							) && (
								!isset($request['columns']) ||
								!is_assoc_array($request['columns'])
							)
						) ||
						(
							isset($request['order']) &&
							(
								!is_assoc_array($request['order']) ||
								count($request['order']) === 0
							)
						)
					)
				) ||
				(
					(
						$request['operation'] === 'insert' ||
						$request['operation'] === 'update'
					) &&
					(
						!isset($request['columns']) ||
						!is_assoc_array($request['columns'])
					)
				) ||
				(
					$request['operation'] === 'select' &&
					isset($request['columns']) &&
					!is_array($request['columns'])
				) ||
				(
					$request['operation'] === 'delete' &&
					!isset($request['where'])
				) ||
				(
					(
						$request['operation'] === 'delete' ||
						$request['operation'] === 'count'
					) &&
					isset($request['columns'])
				) ||
				(
					isset($request['where']) &&
					(
						!is_array($request['where']) ||
						count($request['where']) === 0
					)
				)
			) {
				
				$this->setErrorMessage(
					'This Query is incomplete or incorrect.' .
					' <br>Your Query: '. print_r($request, true) .
					' <br>Usage: ' . print_r($this->exampleRequest, true)
				);
				
			} else {
			
				$this->request = $request; unset($request);
				$this->{$this->request['type'] . ucfirst($this->request['operation'])}();
			
			}
			
			if ($this->debug) { $error_log = $this->errorLog; $error_message = (PHP_EOL . '<br>Database Request: <pre>' . print_r($this->request, true) . '</pre>'); error_log($error_message, 3, $error_log); }
			
			return $this;
			
		}
		
		/**
		 * Runs any MySQL query.
		 * 
		 * Available MySQL tables:
		 * 
		 * 'app.campaign.jobs'
		 * 'app.discover.jobs'
		 * 'app.jobs.messages'
		 * 'app.jobs.share'
		 * 'app.jobs.tokens'
		 * 'app.subscription.limits'
		 * 'app.urls'
		 * 'company'
		 * 'company.modules'
		 * 'email.notification.history'
		 * 'instagram.keys'
		 * 'logs.forms.submits'
		 * 'logs.operations'
		 * 'twitter.campaign.blacklist'
		 * 'twitter.campaign.preferences'
		 * 'socialMedia.interaction'
		 * 'twitter.keys'
		 * 'user.accounts'
		 * 'user.login.attempts'
		 * 'user.login.sessions'
		 * 'user.modules'
		 * 'user.registration.tokens'
		 * 
		 * Example:
		 * 
		 * array(
		 *	<br>&#09;'query' => 'SELECT * FROM `table.one` WHERE `num_one` &gt; :num1 AND `num_two` &lt; :num2',
		 *	<br>&#09;array(
		 *		<br>&#09;&#09;':num1' => 5,
		 *		<br>&#09;&#09;':num2' => 10
		 *	<br>&#09;)
		 * <br>)
		 * @param assoc_array $request ['query' => string, 'params' => array]<br>.
		 */
		public function queryMySQLCustom($request) {
			
			if ($this->debug) { $error_log = $this->errorLog; $error_message = ('<br>Database Request: ' . print_r($request, true)); error_log($error_message, 3, $error_log); }
			
			$name = ($this->mysqlCustomQueries > 0)?'custom_' . ($this->mysqlCustomQueries++):'custom';
			
			if ($this->debug) { $queryStart = microtime(true); }

			try {
					
				$stmt = $this->mysqlConn->prepare($request['query']);
				
				if (!isset($request['params']) || !is_assoc_array( $request['params'] )) {
					$request['params'] = [];
				}
				
				$stmt->execute($request['params']);
//				var_dump($request);
//				var_dump(isset($request['no_return']));
				if (!isset($request['no_return'])) {
			
					$this->mysqlResults[$name] = $stmt->fetchAll();

				} else {
					
					$this->mysqlResults[$name] = true;
					
				}

			} catch(PDOException $ex) {

				$errorMessage = 'Query Name: ' . $name . ' | Query: ' . $request['query'] . ' | ' . print_r($request['params'], true) . ' | Exception: ' . $ex->getMessage();
				$this->setErrorMessage($errorMessage);

			}
			
			if ($this->debug) {
				
				$error_log = $this->errorLog;
				
				$queryEnd = microtime(true);

				$error_message = ('<br>MySQL Query: <pre>' . print_r($request['query'], true) . '</pre>'); error_log($error_message, 3, $error_log);
				$error_message = ('<br>MySQL Query Result: <pre>' . print_r($this->mysqlResults['custom'], true) . '</pre>'); error_log($error_message, 3, $error_log);

				$timeTaken = calcMicroTimeDiff($queryStart, $queryEnd, 10);
				$error_message = ('<br>MySQL Query Execution Time: ' . $timeTaken . '<br>'); error_log($error_message, 3, $error_log);

			}
			
			return $this;
			
		}
		
		/**
		 * Runs queries and returns results.
		 * @return array Unless an error occurs, results will look like:<br>
		 * 	<br>
		 *	INSERT Operation returns:<br>
		 *		&#09;Number of affected rows.<br>
		 * <br>
		 *	REPLACE Operation returns:<br>
		 *		&#09;Number of affected rows.<br>
		 * <br>
		 *	UPDATE Operation returns:<br>
		 *		&#09;Number of affected rows.<br>
		 * 	<br>
		 *	DELETE Operation returns:<br>
		 *		&#09;Number of affected rows.<br>
		 * 	<br>
		 *	SELECT Operation returns:<br>
		 *		&#09;Result set OR NULL if none found.<br>
		 * <br>
		 *	COUNT Operation returns:<br>
		 *		&#09;['count'] number >= 0<br>
		 * <br>
		 * Actual return array:<br>
		 * <br>
		 *	$results = array(<br>
		 *		&#09;'mysql' => array(<br>
		 *			&#09;&#09;'query_name1' => 'result_set_array | null',<br>
		 *			&#09;&#09;'query_nameN' => 'result_set_array | null'<br>
		 *		&#09;),<br>
		 *		&#09;'mongo' => array(<br>
		 *			&#09;&#09;'query_name1' => 'result_set_array | null',<br>
		 *			&#09;&#09;'query_nameN' => 'result_set_array | null'<br>
		 *		&#09;),<br>
		 *	);<br>
		 * <br>
		 * If only one type of operation is requested (mysql or mongo), then the return array looks like:<br>
		 * $results = array(<br>
		 *		&#09;'query_name1' => 'result_set_array | null'<br>
		 *		&#09;'query_nameN' => 'result_set_array | null'<br>
		 *	);<br>
		 * <br><br>
		 * If the operation has come through queryMySQLCustom($request), results will be available under the name 'custom' ('custom_0..N' for more than one custom query per fetch), so:<br>
		 * $results = array(<br>
		 *		&#09;'mysql' => array(<br>
		 *			&#09;&#09;'custom' => 'result_set_array | false'<br>
		 *		&#09;)<br>
		 * )<br><br>
		 * OR<br><br>
		 * $results = array(<br>
		 *		&#09;'custom' => 'result_set_array | false'<br>
		 *	);<br>
		*/
		public function fetch() {
			
			$this->runQueries();
			
			$results = null;
			$mysqlResultsCount = boolval(count($this->mysqlResults));
			$mongoResultsCount = boolval(count($this->mongoResults));
			
			if ($mysqlResultsCount && $mongoResultsCount) {
				$results['mysql'] = $this->mysqlResults;
				$results['mongo'] = $this->mongoResults;
			} elseif ($mysqlResultsCount) {
				$results = $this->mysqlResults;
			} elseif ($mongoResultsCount) {
				$results = $this->mongoResults;
			}
			
			$this->mysqlResults = null;
			$this->mongoResults = null;
			$this->mysqlCustomQueries = 0;
			
			return $results;
			
		}
		
		private function runQueries() {
			
			if (count($this->mysqlQueries) > 0) {
				
				$this->mysqlResults = array();
				
				while ($query = array_pop($this->mysqlQueries)) {
					
					if ($this->debug) { $queryStart = microtime(true); }
					
					try {
					
						$stmt = $this->mysqlConn->prepare($query['query']);
						$stmt->execute($query['params']);
						
						if ($this->debug) { $queryEnd =	microtime(true); }

						$rowCount = intval($stmt->rowCount());
						$this->mysqlResults[$query['name']] = (
							in_array($query['type'], array('select', 'count'))
							?
							(
								$rowCount > 1
								?
								$stmt->fetchAll()
								:
								(
									$rowCount === 1
									?
									$stmt->fetch()
									:
									null
								)
							)
							:
							(
								(
									$query['type'] === 'insert' &&
									isset($query['id'])
								)
								?
								$this->mysqlConn->lastInsertId()
								:
								$rowCount
							)
						);

						if ($this->debug) {

							$error_log = $this->errorLog;
							
							$error_message = (PHP_EOL . '<br>MySQL Query: <pre>' . print_r($query, true) . '</pre>'); error_log($error_message, 3, $error_log);
							$error_message = (PHP_EOL . '<br>MySQL Query Result: <pre>' . print_r($this->mysqlResults[$query['name']], true) . '</pre>'); error_log($error_message, 3, $error_log);

							$timeTaken = calcMicroTimeDiff($queryStart, $queryEnd, 10);
							$error_message = (PHP_EOL . '<br>MySQL Query Execution Time: ' . $timeTaken . '<br>'); error_log($error_message, 3, $error_log);

						}
						
					} catch(PDOException $ex) {
						
						$this->mysqlResults[$query['name']] = null;
						
						$errorMessage = 'Query Name: ' . $query['name'] . ' | Query: ' . $query['query'] . ' | ' . print_r($query['params'], true) . ' | Exception: ' . $ex->getMessage();
						$this->setErrorMessage($errorMessage);
						
					}
					
				}
				
			}
			
			if (count($this->mongoQueries) > 0) {
				
				$this->mongoResults = array();
				
				while ($query = array_pop($this->mongoQueries)) {
					
					try {
						
						$db = $this->mongoConn->{$query['db']};
						$coll = $db->{$query['coll']};

						if ($this->debug) { $queryStart = microtime(true); }
						
						switch($query['type']) {
						
							case 'insert':
								$query['data']['_id'] = new MongoId();
								$_res = $coll->insert($query['data']);
								if (isset($query['id'])) {
									$this->mongoResults[$query['name']] = (string) $query['data']['_id'];
								} else {
									$this->mongoResults[$query['name']] = $_res;
								}
								break;
							case 'update':
								if (!isset($query['multiple'])) { $query['multiple'] = false; }
								if (!isset($query['upsert'])) { $query['upsert'] = false; }
								if (!empty($query['replace'])) { $result = $coll->update($query['query'], $query['data'], array('upsert' => $query['upsert'], 'w' => 1, 'multi' => $query['multiple'])); }
								else { $result = $coll->update($query['query'], array('$set' => $query['data']), array('upsert' => $query['upsert'], 'w' => 1, 'multi' => $query['multiple'])); }
								$this->mongoResults[$query['name']] = intval($result['n']);
								break;
							case 'delete':
								$result = $coll->remove($query['query'], array('w' => 1));
								$this->mongoResults[$query['name']] = intval($result['n']);
								break;
							case 'count':
								$this->mongoResults[$query['name']]['count'] = intval($coll->count($query['query']));
								break;
							case 'select':
								switch($query['howMany']) {
									case 'one':
										$this->mongoResults[$query['name']] = $coll->findOne($query['query'], $query['fields']);
										break;
									case 'all':
										$cursor = $coll->find($query['query'], $query['fields']);
										if ($cursor->count() === 0) { $this->mongoResults[$query['name']] = null; }
										else {
											$cursor->limit($query['limit'])->sort($query['sort']);
											$this->mongoResults[$query['name']] = array();
											while ($cursor->hasNext()) {
												$this->mongoResults[$query['name']][] = $cursor->getNext();
											}
										}
										break;
									default:
										break;
								}
								break;
							default:
								break;
							
						}

						if ($this->debug) { $queryEnd = microtime(true); }
						
						$lastError = $db->lastError();
						
						if ($this->debug) { 

							$error_log = $this->errorLog;
							
							$error_message = (PHP_EOL . '<br>MongoDB Query: ' . print_r($query, true)); error_log($error_message, 3, $error_log);
							$error_message = (PHP_EOL . '<br>MongoDB Operation Status: ' . print_r($lastError, true)); error_log($error_message, 3, $error_log);
							$error_message = (PHP_EOL . '<br>MongoDB Operation Result: ' . print_r($this->mongoResults[$query['name']], true)); error_log($error_message, 3, $error_log);

							$timeTaken = calcMicroTimeDiff($queryStart, $queryEnd, 10);
							$error_message = (PHP_EOL . '<br>MongoDB Query Execution Time: ' . $timeTaken . '<br>'); error_log($error_message, 3, $error_log);

						} else {
							if ($lastError['err']) { 
								
								$this->setErrorMessage(PHP_EOL . '<br>MongoDB Operation Error: ' . $lastError['err'] . '.'); 
								
								$this->mongoResults[$query['name']] = null;
								
							}
						}
						
					} catch(MongoException $ex) {
						
						$this->mongoResults[$query['name']] = null;
						
						$errorMessage = PHP_EOL . 'Code: ' . $ex->getCode() . '. Message: ' . $ex->getMessage() . '.';
						$this->setErrorMessage($errorMessage);
						
					}
					
				}
				
			}
			
		}
		
		private function mysqlInsert($type = 'INSERT') {
			
			$query = $type . ' INTO ' . (isset($this->request['database'])?'`' . $this->request['database'] . '`.':'') . '`' . $this->mysqlTables[$this->request['table']] . '`';
			$columns = ''; $values = ''; $query_params = array();
			foreach ($this->request['columns'] as $column => $value) { 
				$columns .= '`' . $column . '`, ';
				$values .= ':insert_' . $column . ', ';
				$query_params[':insert_' . $column] = $value;
			}
			$query .=  ' (' . rtrim($columns, ', ') . ') VALUES (' . rtrim($values, ', ') . ')';
			
			$this->mysqlQueries[] = array(
				'name' => $this->request['name'],
				'type' => strtolower($type),
				'query' => $query,
				'params' => $query_params,
				'id' => isset($this->request['id'])
			);
			
		}
		
		private function mysqlReplace() {
			
			$this->mysqlInsert('REPLACE');
			
		}
		
		private function mysqlUpdate() {
			
			$query = 'UPDATE ' . (isset($this->request['database'])?'`' . $this->request['database'] . '`.':'') . '`' . $this->mysqlTables[$this->request['table']] . '` SET ';
			$query_params = array();
			foreach ($this->request['columns'] as $column => $value) {
				$query .= '`' . $column . '` = :update_' . $column . ', ';
				$query_params[':update_' . $column] = $value;
			}
			$query = rtrim($query, ', ');
			if (isset($this->request['where'])) {
				$query .= ' WHERE '; $whereCount = count($this->request['where']); $current = 0;
				foreach ($this->request['where'] as $column => $value) {
					if (is_assoc_array($value)) {
						if ($whereCount > 1 && ($current++) > 0) {
							if (isset($value['condition']) && in_array($value['condition'], array('and', 'or'), true)) {
								$condition = ' ' . strtoupper($value['condition']) . ' ';
							} else { $condition = ' AND '; }
						} else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` ' . $value['operator'] . ' ';
						if (!empty($value['as_is'])) {
							$query .= $value['data'];
						} else {
							$query .= ':where_' . $column;
							$query_params[':where_' . $column] = $value['data'];
						}
					} else {
						if ($whereCount > 1 && ($current++) > 0) { $condition = ' AND '; }
						else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` = :where_' . $column;
						$query_params[':where_' . $column] = $value;
					}
				}
			}
			
			if (isset($this->request['limit'])) {
				$query .= ' LIMIT ' . $this->request['limit'];
			}
			
			$this->mysqlQueries[] = array(
				'name' => $this->request['name'],
				'type' => 'update',
				'query' => $query,
				'params' => $query_params
			);
			
		}
		
		private function mysqlDelete() {
			
			$query = 'DELETE FROM ' . (isset($this->request['database'])?'`' . $this->request['database'] . '`.':'') . '`' . $this->mysqlTables[$this->request['table']] .'` WHERE ';
			$query_params = array(); $whereCount = count($this->request['where']);  $current = 0;
			foreach ($this->request['where'] as $column => $value) {
				if (is_assoc_array($value)) {
					if ($whereCount > 1 && ($current++) > 0) {
						if (isset($value['condition']) && in_array($value['condition'], array('and', 'or'), true)) {
							$condition = ' ' . strtoupper($value['condition']) . ' ';
						} else { $condition = ' AND '; }
					} else { $condition = ''; }
					$query .=  $condition . '`' . $column . '` ' . $value['operator'] . ' ';
					if (!empty($value['as_is'])) {
						$query .= $value['data'];
					} else {
						$query .= ':where_' . $column;
						$query_params[':where_' . $column] = $value['data'];
					}
				} else {
					if ($whereCount > 1 && ($current++) > 0) { $condition = ' AND '; }
					else { $condition = ''; }
					$query .=  $condition . '`' . $column . '` = :where_' . $column;
					$query_params[':where_' . $column] = $value;
				}
			}
			
			if (isset($this->request['limit'])) {
				$query .= ' LIMIT ' . $this->request['limit'];
			}
			
			$this->mysqlQueries[] = array(
				'name' => $this->request['name'],
				'type' => 'delete',
				'query' => $query,
				'params' => $query_params
			);
			
		}
		
		private function mysqlSelect() {
			
			$query = 'SELECT ';
			if (isset($this->request['columns'])) {
				$columns = '';
				foreach ($this->request['columns'] as $column => $value) { 
					if (is_string($column)) { //if $column is a string, means $column is the column name and $value is its alias
						$columns .= '`' . $column . '` as `' . $value . '`, ';
					} else { //otherwise $value is the column name
						$columns .= '`' . $value . '`, ';
					}
				}
				$query .= rtrim($columns, ', ');
			} else {
				$query .= '*';
			}
			
			$query .=  ' FROM ' . (isset($this->request['database'])?'`' . $this->request['database'] . '`.':'') . '`' . $this->mysqlTables[$this->request['table']] . '`';
			$query_params = array();
			
			if (isset($this->request['where'])) {
				$query .= ' WHERE '; $whereCount = count($this->request['where']); $current = 0;
				foreach ($this->request['where'] as $column => $value) {
					if (is_assoc_array($value)) {
						if ($whereCount > 1 && ($current++) > 0) {
							if (isset($value['condition']) && in_array($value['condition'], array('and', 'or'), true)) {
								$condition = ' ' . strtoupper($value['condition']) . ' ';
							} else { $condition = ' AND '; }
						} else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` ' . $value['operator'] . ' ';
						if (!empty($value['as_is'])) {
							$query .= $value['data'];
						} else {
							$query .= ':where_' . $column;
							$query_params[':where_' . $column] = $value['data'];
						}
					} else {
						if ($whereCount > 1 && ($current++) > 0) { $condition = ' AND '; }
						else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` = :where_' . $column;
						$query_params[':where_' . $column] = $value;
					}
				}
			}
			
			if (isset($this->request['order'])) {
				$query .= ' ORDER BY ';
				foreach ($this->request['order'] as $column => $value) {
					$query .= '`' . $column . '` ' . strtoupper($value) . ', ';
				}
				$query = rtrim($query, ', ');
			}
			
			if (isset($this->request['limit'])) {
				$query .= ' LIMIT ' . $this->request['limit'];
			}
			
			$this->mysqlQueries[] = array(
				'name' => $this->request['name'],
				'type' => 'select',
				'query' => $query,
				'params' => $query_params
			);
			
		}
		
		private function mysqlCount() {
			
			$query = 'SELECT count(*) as count FROM ' . (isset($this->request['database'])?'`' . $this->request['database'] . '`.':'') . '`' . $this->mysqlTables[$this->request['table']] . '`';
			$query_params = array();
			
			if (isset($this->request['where'])) {
				$query .= ' WHERE '; $whereCount = count($this->request['where']); $current = 0;
				foreach ($this->request['where'] as $column => $value) {
					if (is_assoc_array($value)) {
						if ($whereCount > 1 && ($current++) > 0) {
							if (isset($value['condition']) && in_array($value['condition'], array('and', 'or'), true)) {
								$condition = ' ' . strtoupper($value['condition']) . ' ';
							} else { $condition = ' AND '; }
						} else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` ' . $value['operator'] . ' ';
						if (!empty($value['as_is'])) {
							$query .= $value['data'];
						} else {
							$query .= ':where_' . $column;
							$query_params[':where_' . $column] = $value['data'];
						}
					} else {
						if ($whereCount > 1 && ($current++) > 0) { $condition = ' AND '; }
						else { $condition = ''; }
						$query .=  $condition . '`' . $column . '` = :where_' . $column;
						$query_params[':where_' . $column] = $value;
					}
				}
			}
			
			$this->mysqlQueries[] = array(
				'name' => $this->request['name'],
				'type' => 'count',
				'query' => $query,
				'params' => $query_params
			);
			
		}
		
		private function mongoInsert() {
			
			$this->request['columns'] = array_map_deep('strval', $this->request['columns']);
			
			$this->mongoQueries[] = array(
				'name' => $this->request['name'],
				'db' => $this->mongoTables[$this->request['table']]['db'],
				'coll' => $this->mongoTables[$this->request['table']]['coll'],
				'type' => 'insert',
				'data' => $this->request['columns'],
				'id' => isset($this->request['id'])
			);
			
		}
		
		private function mongoUpdate() {
			
			$query = array();
			if (isset($this->request['where'])) {
				foreach ($this->request['where'] as $column => $value) {
					if (is_array($value)) { $query[$column] = array_map_deep('strval', $value); }
					else { $query[$column] = strval($value); }
				}
			}
			
			$this->request['columns'] = array_map_deep('strval', $this->request['columns']);
			
			$this->mongoQueries[] = array(
				'name' => $this->request['name'],
				'db' => $this->mongoTables[$this->request['table']]['db'],
				'coll' => $this->mongoTables[$this->request['table']]['coll'],
				'type' => 'update',
				'query' => $query,
				'data' => $this->request['columns'],
				'replace' => (isset($this->request['replace'])?boolval($this->request['upsert']):false),
				'upsert' => (isset($this->request['upsert'])?boolval($this->request['upsert']):false)
			);
			
		}
		
		private function mongoDelete() {
			
			$query = array();
			foreach ($this->request['where'] as $column => $value){
				if (is_array($value)) { $query[$column] = array_map_deep('strval', $value); }
				else { $query[$column] = strval($value); }
			}
				
			$this->mongoQueries[] = array(
				'name' => $this->request['name'],
				'db' => $this->mongoTables[$this->request['table']]['db'],
				'coll' => $this->mongoTables[$this->request['table']]['coll'],
				'type' => 'delete',
				'query' => $query
			);
			
		}
		
		private function mongoSelect() {
			
			$query = array();
			if (isset($this->request['where'])) {
				foreach ($this->request['where'] as $column => $value) {
					if (is_array($value)) { $query[$column] = array_map_deep('strval', $value); }
					else { $query[$column] = strval($value); }
				}
			}
			
			$fields = array();
			if (isset($this->request['columns'])) {
				foreach ($this->request['columns'] as $column) {
					$fields[$column] = true;
				}
			}
			
			$sort = array();
			if (isset($this->request['order'])) {
				foreach ($this->request['order'] as $column => $value) {
					if ($value === 'desc') { $sort[$column] = -1; }
					else { $sort[$column] = 1; }
				}
			}
			
			$this->mongoQueries[] = array(
				'name' => $this->request['name'],
				'db' => $this->mongoTables[$this->request['table']]['db'],
				'coll' => $this->mongoTables[$this->request['table']]['coll'],
				'type' => 'select',
				'query' => $query,
				'fields' => $fields,
				'sort' => $sort,
				'limit' => (isset($this->request['limit'])?$this->request['limit']:0),
				'howMany' => $this->request['howMany']
			);
			
		}
		
		private function mongoCount() {
			
			$query = array();
			if (isset($this->request['where'])) {
				foreach ($this->request['where'] as $column => $value) {
					if (is_array($value)) { $query[$column] = array_map_deep('strval', $value); }
					else { $query[$column] = strval($value); }
				}
			}
			
			$this->mongoQueries[] = array(
				'name' => $this->request['name'],
				'db' => $this->mongoTables[$this->request['table']]['db'],
				'coll' => $this->mongoTables[$this->request['table']]['coll'],
				'type' => 'count',
				'query' => $query
			);
			
		}
		
	}