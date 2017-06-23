<?php

class Job
{

	/**
	 * Link to Database module.
	 * @var Database
	 */
	private $database = null;

	/**
	 * Link to Campaign module.
	 * @var Campaign
	 */
	public $campaign = null;

	/**
	 * Link to Discover module.
	 * @var Discover
	 */
	public $discover = null;

	/**
	 * Link to UrlService module.
	 * @var UrlService
	 */
	public $urlService = null;

	/**
	 * Link to Twitter module.
	 * @var Twitter
	 */
	public $twitter = null;

	/**
	 * Link to Instagra module.
	 * @var Instagram
	 */
	public $instagram = null;

	/**
	 * Link to Company module.
	 * @var Company
	 */
	public $company = null;

	/**
	 * Instantiate Class.
	 */
	public function __construct ( &$database, &$campaign, &$discover, &$urlService, &$twitter, &$instagram, &$company )
	{

		$this->database		 = & $database;
		$this->campaign		 = & $campaign;
		$this->discover		 = & $discover;
		$this->urlService	 = & $urlService;
		$this->twitter		 = & $twitter;
		$this->instagram	 = & $instagram;
		$this->company		 = & $company;
	}

	/**
	 * Create a share link for a job.
	 * @param array $details Request details. ['jobType', 'id', 'messagesCount'(, 'toSM', 'smType', 'smId', 'message')]
	 * @param boolean $ajax TRUE if this is an AJAX request.
	 * @return array [(boolean) 'status', (null|string) 'link']
	 */
//	public function share ( $details, $ajax = false )
//	{
//
//		$return = array(
//			'status' => false,
//			'link'	 => null
//		);
//
//		if ( isset( $details[ 'jobType' ], $details[ 'id' ], $details[ 'messagesCount' ] ) )
//		{
//
//			$url		 = __oCompanyBrandURL__ . '/url/share/' . $details[ 'jobType' ] . '/' . $details[ 'id' ];
//			$shortUrl	 = $this->urlService->urlToShortCode( $url );
//
//			if ( is_string( $shortUrl ) )
//			{
//
//				$save = $this->setShare( $details[ 'jobType' ], $details[ 'id' ], $details[ 'messagesCount' ], $this->shareExists( $details[ 'jobType' ], $details[ 'id' ] ) );
//
//				if ( $save )
//				{
//
//					$return[ 'status' ]	 = true;
//					$return[ 'link' ]	 = __oCompanyBrandURL__ . '/url/' . $shortUrl;
//
//					if ( isset( $details[ 'toSM' ], $details[ 'smType' ], $details[ 'smId' ], $details[ 'message' ] ) )
//					{
//
//						global $availableSMModules; //check if requested SM module exists
//						if ( in_array( $details[ 'smType' ], $availableSMModules ) )
//						{
//
//							$return[ 'message' ] = $details[ 'message' ] . ' ' . $return[ 'link' ];
//							$return[ 'status' ]	 = $this->{$details[ 'smType' ]}->postStatus( $return[ 'message' ], $details[ 'smId' ] );
//						}
//					}
//				}
//			}
//		}
//
//		return $return;
//	}

	/**
	 * Create a share link for a job.
	 * @param array $details Request details. ['jobType', 'id']
	 * @param boolean $ajax TRUE if this is an AJAX request.
	 * @return boolean TRUE if successful, FALSE otherwise.
	 */
//	public function unShare ( $details, $ajax = false )
//	{
//
//		if ( $this->shareExists( $details[ 'jobType' ], $details[ 'id' ] ) )
//		{
//
//			return $this->setStatus( array(
//					'jobType'	 => $details[ 'jobType' ],
//					'id'		 => $details[ 'id' ],
//					'shared'	 => 0 ) ) && (boolean) $this->database->query( array(
//					'name'		 => 'removeShare',
//					'type'		 => 'mysql',
//					'table'		 => 'app.jobs.share',
//					'operation'	 => 'delete',
//					'where'		 => array(
//						'jobType'	 => $details[ 'jobType' ],
//						'jobId'		 => $details[ 'id' ]
//					)
//				) )->fetch()[ 'removeShare' ];
//		}
//	}

	/**
	 * Check if job has already been shared.
	 * @param string $jobType 'discover', 'campaign'
	 * @param integer $id Job ID.
	 * @return boolean TRUE if successful, FALSE otherwise.
	 */
//	public function shareExists ( $jobType, $id )
//	{
//
//		return (boolean) $this->database->query( array(
//				'name'		 => 'countShare',
//				'type'		 => 'mysql',
//				'table'		 => 'app.jobs.share',
//				'operation'	 => 'count',
//				'where'		 => array(
//					'jobType'	 => $jobType,
//					'jobId'		 => $id
//				)
//			) )->fetch()[ 'countShare' ][ 'count' ];
//	}

	/**
	 * Cache a job's parsed message count.
	 * @param string $jobType 'discover', 'campaign'
	 * @param integer $id Job ID.
	 * @param integer $count Messages Count.
	 * @param boolean $exists Does this share already exist?
	 * @return boolean TRUE if successful, FALSE otherwise.
	 */
//	public function setShare ( $jobType, $id, $count, $exists = false )
//	{
//
//		$data = array(
//			'name'		 => 'setShare',
//			'type'		 => 'mysql',
//			'table'		 => 'app.jobs.share',
//			'operation'	 => $exists ? 'update' : 'insert',
//			'columns'	 => array(
//				'messagesCount' => $count
//			)
//		);
//
//		if ( $exists )
//		{ //update
//			$data[ 'where' ] = array(
//				'jobType'	 => $jobType,
//				'jobId'		 => $id
//			);
//			$updStatus		 = true;
//		}
//		else
//		{ //insert
//			$data[ 'columns' ][ 'jobType' ]	 = $jobType;
//			$data[ 'columns' ][ 'jobId' ]	 = $id;
//			$updStatus						 = $this->setStatus( array(
//				'jobType'	 => $jobType,
//				'id'		 => $id,
//				'shared'	 => 1 ) );
//		}
//
//		return $updStatus && is_numeric( $this->database->query( $data )->fetch()[ 'setShare' ] );
//	}

	/**
	 * Retrieve a cached job's messages count.
	 * @param string $jobType 'discover', 'campaign'
	 * @param integer $id Job ID.
	 * @return mixed Integer if successfull, NULL otherwise.
	 */
//	public function getShare ( $jobType, $id )
//	{
//
//		return $this->database->query( array(
//				'name'		 => 'getShare',
//				'type'		 => 'mysql',
//				'table'		 => 'app.jobs.share',
//				'operation'	 => 'select',
//				'columns'	 => array(
//					'messagesCount'
//				),
//				'where'		 => array(
//					'jobType'	 => $jobType,
//					'jobId'		 => $id
//				)
//			) )->fetch()[ 'getShare' ][ 'messagesCount' ];
//	}

	/**
	 * Change job status.
	 * @param array $details Details array. ['id', 'otherColumn1', 'otherColumnN']. if 'id' is omitted, all jobs are updated. If 'user_id' is given, only those user ID jobs are updated. Must have ['jobType'] as either 'campaign' or 'discover'<br>If '_where_' array given, will be used to filter jobs.
	 * @param boolean $ajax [Optional]<br>If requested through AJAX.
	 * @return boolean TRUE on success, FALSE otherwise
	 */
	public function setStatus ( $details, $ajax = false )
	{

		if ( !empty( $details[ 'jobType' ] ) )
		{

			$data = array(
				'name'		 => 'changeStatus',
				'type'		 => 'mysql',
				'operation'	 => 'update',
				'table'		 => 'app.jobs' )
			;

			if ( isset( $details[ 'id' ] ) )
			{
				$data[ 'where' ] = array(
					'id' => $details[ 'id' ]
				);
			}
//			elseif ( isset( $details[ 'user_id' ] ) )
//			{
//				$data[ 'where' ] = array(
//					'user_id' => $details[ 'user_id' ]
//				);
//			}

			if ( isset( $details[ '_where_' ] ) )
			{
				if ( !isset( $data[ 'where' ] ) )
				{
					$data[ 'where' ] = array();
				}
				foreach ( $details[ '_where_' ] as $key => $value )
				{
					$data[ 'where' ][ $key ] = $value;
				}
			}

			foreach ( $details as $column => $value )
			{
				if ( $column === '_where_' || $column === 'id' || $column === 'user_id' || $column === 'jobType' || ($ajax && $column !== 'suspended') )
				{
					continue;
				} $data[ 'columns' ][ $column ] = $value;
			}

			if ( is_numeric( $this->database->query( $data )->fetch()[ 'changeStatus' ] ) )
			{
				$return = true;
			}
			else
			{
				$return = false;
			}
		}
		else
		{
			$return = false;
		}

		return $return;
	}

	/**
	 * Checks whether the system has already stored this message.
	 * @param string $tokenType Social media module name.
	 * @param string $id Message ID.
	 * @return array|null Mongo message is exists, NULL otherwise.
	 */
	public function messageExists ( $tokenType, $id )
	{

		/* return (boolean) intval($this->database->query(array(
		  'name' => 'countMessages',
		  'type' => 'mongo',
		  'table' => 'message.' . $messageType,
		  'operation' => 'count',
		  'where' => array(
		  'id_str' => $id
		  )
		  ))->fetch()['countMessages']['count']); */

		return $this->database->query( array(
					'name'		 => 'getMessageIfAny',
					'type'		 => 'mongo',
					'table'		 => 'message.' . $tokenType,
					'operation'	 => 'select',
					'where'		 => array(
						'opheme_backend.social_media_type' => $tokenType,
						'id' => $id
					),
					'howMany' => 'one'
				) )->fetch()[ 'getMessageIfAny' ];
	}

	/**
	 * Checks whether Campaign has already sent a message to this Social Media user.
	 * @param string $tokenType SM token type.
	 * @param string $userId Social Media User ID.
	 * @param integer $jobId Database Job ID.
	 */
//	public function messageToUserExists ( $tokenType, $userId, $jobId )
//	{

		/* return (boolean) intval($this->database->query(array(
		  'name' => 'countMessages',
		  'type' => 'mongo',
		  'table' => 'job.campaign.sent',
		  'operation' => 'count',
		  'where' => array(
		  'user_id' => $userId,
		  'campaign_id' => $jobId,
		  'smType' => $tokenType,
		  'smId' => $tokenId
		  )
		  ))->fetch()['countMessages']['count']); */

//		return (boolean) intval( $this->database->query( array(
//					'name'		 => 'countMessages',
//					'type'		 => 'mysql',
//					'table'		 => 'app.jobs.messages',
//					'operation'	 => 'count',
//					'where'		 => array(
//						'job_id'	 => $jobId,
//						'job_type'	 => 'campaign',
//						'token_type' => $tokenType,
//						'user_id'	 => $userId
//					)
//				) )->fetch()[ 'countMessages' ][ 'count' ] );
//	}

	/**
	 * Checks whether a Social Media user has been blacklisted in the Database.
	 * @param string $type Social media module.
	 * @param string $screen_name Twitter User screen name.
	 * @return boolean TRUE if blacklisted, FALSE otherwise.
	 */
//	public function userIsBlacklisted ( $type, $screen_name )
//	{
//
//		return (boolean) $this->database->query( array(
//				'name'		 => 'isBlacklisted',
//				'type'		 => 'mysql',
//				'table'		 => $type . '.campaign.blacklist',
//				'operation'	 => 'count',
//				'where'		 => array(
//					'screen_name' => $screen_name
//				)
//			) )->fetch()[ 'isBlacklisted' ][ 'count' ];
//	}

	/**
	 * Checks whether a Social Media user has saved Campaign preferences in the Database.
	 * @param string $type Social media module.
	 * @param string $screen_name Twitter User screen name.
	 * @param string $pref Preference category.
	 * @return boolean TRUE if it is a preferenced category, FALSE otherwise.
	 */
//	public function userIsPreferenced ( $type, $screen_name, $pref )
//	{
//
//		$return = false;
//
//		if ( in_array( $pref, $this->getPreferences( $type, $screen_name ) ) )
//		{
//			$return = true;
//		}
//
//		return $return;
//	}

	/**
	 * Get number of current job messages.
	 * @param int $jobId Job ID.
	 * @return int Message count.
	 */
	public function getJobMessageCount ( $jobId )
	{
		return intval( $this->database->query( array(
				'name'		 => 'getMessageCount',
				'type'		 => 'mysql',
				'table'		 => 'app.jobs.messages',
				'operation'	 => 'count',
				'where'		 => array(
					'discover_id'	 => $jobId
//					'job_type'	 => $jobType
				)
			) )->fetch()[ 'getMessageCount' ][ 'count' ] );
	}

	/**
	 * Stores a message in the database. If only $message is given, it only stores it in the database without further action.
	 * @param string $smPlatformId Platform ID
	 * @param string $smType Social media module.
	 * @param array $message Message array.
	 * @param string|integer $jobId [Optional]<br>Job Database ID.
	 * @param string $module [Optional]<br>Module name.
	 * @param string $mongoId [Optional]<br>Message Mongo ID
	 * @param boolean $isSent [Optional]<br>Has this tween been sent by a user?
	 * @param boolean $linkOnly [Optional]<br>If TRUE, only creates a link between a message ID and a job ID without storing the actual message.
	 * @return boolean|string TRUE if successful, FALSE otherwise. Can return a MongoID string if $isSent is true and $linkOnly is false.
	 */
	public function storeMessage ( $smPlatformId, $smType, $message, $jobId = null, $module = null, $mongoId = null, $isSent = null, $linkOnly = null )
	{

		$return = true;

		//make sure timezone is correct
		if ( isset( $message[ 'created_at' ] ) )
		{
			$message[ 'created_at' ] = date( 'D M j G:i:s T Y', strtotime( $message[ 'created_at' ] ) );
		}
		else
		{
			$message[ 'created_at' ] = date( 'D M j G:i:s T Y', $message[ 'created_time' ] );
		}

		if ( !$linkOnly )
		{
			
			if (intval($this->database->query( [
					'name'		 => 'checkExists',
					'type'		 => 'mongo',
					'table'		 => 'message.' . $smType . ($isSent ? '.sent' : ''),
					'operation'	 => 'count',
					'where'	 => array(
						'id'		 => isset($message[ 'id_str' ]) ? $message[ 'id_str' ] : $message[ 'id' ],
						'opheme_backend.social_media_platform' => $smType
					)
				] )->fetch()[ 'checkExists' ]['count']) === 0) 
			{
			
				$mongoId = $this->database->query( array(
						'name'		 => 'insertMessage',
						'type'		 => 'mongo',
						'table'		 => 'message.' . $smType . ($isSent ? '.sent' : ''),
						'operation'	 => 'insert',
						'columns'	 => $message,
						'id' => true
					) )->fetch()[ 'insertMessage' ];

				$return = $mongoId;
			
			} else {

				$return = (string) $this->database->query( [
					'name'		 => 'getMessage',
					'type'		 => 'mongo',
					'table'		 => 'message.' . $smType . ($isSent ? '.sent' : ''),
					'operation'	 => 'select',
					'where'	 => array(
						'id'		 => isset($message[ 'id_str' ]) ? $message[ 'id_str' ] : $message[ 'id' ],
						'opheme_backend.social_media_platform' => $smType
					),
					'howMany' => 'one'
				] )->fetch()[ 'getMessage' ]['_id'];

			}
			
		}

		//if job linking is required
		if ( !empty( $jobId ) && !empty( $module ) && $mongoId )
		{

			//link message with a job
			if ( !$isSent ) {
				
				// job does not already have this exact message linked
				if (intval($this->database->query( [
						'name'		 => 'checkExists',
						'type'		 => 'mysql',
						'table'		 => 'app.jobs.messages',
						'operation'	 => 'count',
						'where'	 => array(
							'discover_id'		 => $jobId,
							'socialmediaplatform_id' => $smPlatformId,
							'socialmediaplatform_message_id'	 => isset($message[ 'id_str' ]) ? $message[ 'id_str' ] : $message[ 'id' ],
						)
					] )->fetch()[ 'checkExists' ]['count']) == 0) {
				
						$date = date('Y-m-d H:i:s');

						$return = $this->database->query( array(
								'name'		 => 'insertMessageLink',
								'type'		 => 'mysql',
								'table'		 => 'app.jobs.messages',
								'operation'	 => 'insert',
								'columns'	 => array(
									'id' => (string) Uuid::uuid4(),
									'discover_id'		 => $jobId,
									'backend_message_id' => $mongoId,
									'socialmediaplatform_id' => $smPlatformId,
									'socialmediaplatform_message_id'	 => isset($message[ 'id_str' ]) ? $message[ 'id_str' ] : $message[ 'id' ],
									'message_datestamp'	 => $message['opheme_backend'][ 'created_timestamp' ],
									'created_at'	 => $date,
									'updated_at'	 => $date
								)
							) )->fetch()[ 'insertMessageLink' ];
				
					}
			}
//			else
//			{
//
//				$return = (boolean) $this->database->query( array(
//						'name'		 => 'insertMessageLinkSent',
//						'type'		 => 'mysql',
//						'table'		 => 'app.jobs.messages',
//						'operation'	 => 'insert',
//						'columns'	 => array(
//							'job_type'		 => $module,
//							'job_id'		 => $jobId,
//							'message_id'	 => $message[ 'id' ],
//							'user_id'		 => $message[ 'user' ][ 'id' ],
//							'created_mongo'	 => $message[ 'created_mongo' ],
//							'token_type'	 => $smType
//						)
//					) )->fetch()[ 'insertMessageLinkSent' ];
//			}
		}

		return $return;
	}

	/**
	 * Retrieve a Message from database based on ID.
	 * @param string $type Social media module message table name. ('twitter')
	 * @param integer|string $id Message ID.
	 * @param boolean $isSent Was this sent to a user?
	 * @return mixed NULL if
	 */
//	public function getMessage ( $type, $id, $isSent = false )
//	{
//
//		$fetch = array(
//			'name'		 => 'getMessage',
//			'type'		 => 'mongo',
//			'table'		 => 'message.' . $type . ($isSent ? '.sent' : ''),
//			'operation'	 => 'select',
//			'where'		 => array(
//				'id' => $id
//			),
//			'howMany'	 => 'one'
//		);
//
//		return $this->database->query( $fetch )->fetch()[ 'getMessage' ];
//	}

	/**
	 * Return an array of user preferences.
	 * @param string $type Social media module.
	 * @param string $screen_name Twitter screen name.
	 * @return Array Array of user preferences. Empty if user has none or does not exist.
	 */
//	public function getPreferences ( $type, $screen_name )
//	{
//
//		$prefs = $this->database->query( array(
//				'name'		 => 'getPreferences',
//				'type'		 => 'mysql',
//				'table'		 => $type . '.campaign.preferences',
//				'operation'	 => 'select',
//				'columns'	 => array(
//					'preferences' => 'prefs'
//				),
//				'where'		 => array(
//					'screen_name' => $screen_name
//				)
//			) )->fetch()[ 'getPreferences' ];
//
//		if ( isset( $prefs[ 'prefs' ] ) )
//		{
//
//			$return = explode( ',', $prefs[ 'prefs' ] );
//		}
//		else
//		{
//			$return = array();
//		}
//
//		return $return;
//	}

	/**
	 * Get new job messages.
	 * @param array $details Details array. Pass on ['sent'] to retrieve sent messages. Must contain ['jobType', 'smType']
	 * @param boolean $ajax [Optional]<br>If requested through AJAX.
	 * @return array Array with new messages, otherwise empty array.
	 */
//	public function getNewMessages ( $details, $ajax = false )
//	{
//
//		if ( isset( $details[ 'id' ], $details[ 'jobType' ] ) )
//		{
//
//			$return = array();
//
//			$id = $details[ 'id' ];
//
//			if ( isset( $details[ 'maxItems' ] ) )
//			{
//				$maxItems = intval( $details[ 'maxItems' ] );
//			}
//			else
//			{
//				$maxItems = 0;
//			}
//
//			$data = array(
//				'name'		 => 'getMessages',
//				'type'		 => 'mysql',
//				'table'		 => 'app.jobs.messages',
//				'operation'	 => 'select',
//				'columns'	 => array(
//					'message_id',
//					'token_type',
//					'id'
//				),
//				'where'		 => array(
//					'job_type'	 => $details[ 'jobType' ],
//					'job_id'	 => $id
//				),
//				'limit'		 => $maxItems,
//				'order'		 => array(
//					'created_mongo' => 'desc'
//				)
//			);
//
//			if ( !empty( $details[ 'fetchedIds' ] ) && ( $fetchedIds = isValidJson( $details[ 'fetchedIds' ] ) ) )
//			{
//				if ( count( $fetchedIds ) )
//				{
//					$data[ 'where' ][ 'id' ] = array(
//						'operator'	 => 'NOT IN',
//						'data'		 => '(' . implode( ',', $fetchedIds ) . ')',
//						'as_is'		 => true
//					);
//				}
//			}
//
//			$jobMessages = $this->database->query( $data )->fetch()[ 'getMessages' ];
//			if ( is_assoc_array( $jobMessages ) )
//			{
//				$jobMessages = array(
//					$jobMessages );
//			}
//
//			if ( is_array( $jobMessages ) )
//			{
//
//				foreach ( $jobMessages as $jobMessage )
//				{
//
//					$smType = $jobMessage[ 'token_type' ];
//
//					$fetch = array(
//						'name'		 => 'getMessage',
//						'type'		 => 'mongo',
//						'table'		 => 'message.' . $smType . (isset( $details[ 'sent' ] ) && filter_var( $details[ 'sent' ], FILTER_VALIDATE_BOOLEAN ) ? '.sent' : ''),
//						'operation'	 => 'select',
//						'columns'	 => array(
//							'id',
//							'user',
//							'text',
//							'_o_text',
//							'created_at',
//							'coords',
//							'smType',
//							'smId',
//							'created_mongo'
//						),
//						'where'		 => array(
//							'id_str' => $jobMessage[ 'message_id' ]
//						),
//						'howMany'	 => 'one'
//					);
//
//					//sentiment access
//					if ( $this->company->hasAccessTo( 'sentiment-analysis' ) )
//					{
//						$sentiment				 = true;
//						$fetch[ 'columns' ][]	 = 'sentiment';
//					}
//					//klout access
//					if ( $this->company->hasAccessTo( 'klout' ) && $smType !== 'instagram' )
//					{
//						$klout					 = true;
//						$fetch[ 'columns' ][]	 = 'klout';
//					}
//
//					$message = $this->database->query( $fetch )->fetch()[ 'getMessage' ];
//
//					if ( is_assoc_array( $message ) && isset( $message[ 'coords' ], $message[ 'user' ] ) )
//					{
//
//						$searchFor	 = array(
//							'"',
//							'\'' );
//						$replaceWith = array(
//							'&quot;',
//							'&apos;' );
//
//						$msg = array(
//							'id'		 => $message[ 'id_str' ],
//							'backend_id' => $jobMessage[ 'id' ],
//							'user'		 => array(
//								'profile_image_url'	 => $message[ 'user' ][ 'profile_image_url' ],
//								'screen_name'		 => $message[ 'user' ][ 'screen_name' ],
//								'id'				 => $message[ 'user' ][ 'id_str' ]
//							),
//							'text'		 => str_replace( $searchFor, $replaceWith, $message[ 'text' ] ),
//							'_o_text'	 => $message[ '_o_text' ],
//							'created_at' => $message[ 'created_at' ],
//							'timestamp'	 => $message[ 'created_mongo' ],
//							'coords'	 => $message[ 'coords' ],
//							'smType'	 => $smType,
//							'smId'		 => $message[ 'smId' ]
//						);
//
//						if ( isset( $sentiment ) )
//						{
//							$msg[ 'sentiment' ] = $message[ 'sentiment' ];
//						}
//
//						if ( isset( $klout ) )
//						{
//							$msg[ 'user' ][ 'klout' ] = array(
//								'score' => isset( $message[ 'klout' ][ 'score' ] ) ? $message[ 'klout' ][ 'score' ] : 0
//							);
//						}
//
//						$return[] = $msg;
//					}
//				}
//			}
//
//			if ( $ajax )
//			{
//
//				$total = $this->getJobMessageCount( $details[ 'jobType' ], $id );
//
//				$final = array(
//					'msgs'	 => $return,
//					'total'	 => $total );
//			}
//			else
//			{
//				$final = $return;
//			}
//		}
//		else
//		{
//			$final = array();
//		}
//
//		return $final;
//	}

	/**
	 * Retrieves all stored messages for a job.
	 * @param string $jobType Job type - discover or campaign.
	 * @param int $jobId ID of the job.
	 * @param boolean $sent TRUE if campaign, FALSE otherwise. Defaults to FALSE.
	 * @return array|null Array of one or more messages, NULL if job type or ID are omitted.
	 */
//	public function getAllStoredMessages ( $jobType = null, $jobId = null, $sent = false )
//	{
//
//		$return = null;
//
//		if ( $jobType && $jobId )
//		{
//
//			$jobMessages = $this->database->query( array(
//					'name'		 => 'getMessages',
//					'type'		 => 'mysql',
//					'table'		 => 'app.jobs.messages',
//					'operation'	 => 'select',
//					'columns'	 => array(
//						'message_id',
//						'token_type'
//					),
//					'where'		 => array(
//						'job_type'	 => $jobType,
//						'job_id'	 => $jobId
//					),
//					'order'		 => array(
//						'created_mongo' => 'desc'
//					)
//				) )->fetch()[ 'getMessages' ];
//
//			if ( is_array( $jobMessages ) )
//			{
//
//				$return = array();
//
//				foreach ( $jobMessages as $jobMessage )
//				{
//
//					$fetch = array(
//						'name'		 => 'getMessage',
//						'type'		 => 'mongo',
//						'table'		 => 'message.' . $jobMessage[ 'token_type' ] . (filter_var( $sent, FILTER_VALIDATE_BOOLEAN ) ? '.sent' : ''),
//						'operation'	 => 'select',
//						'columns'	 => array(
//							'id_str',
//							'user',
//							'text',
//							'_o_text',
//							'created_at',
//							'coords',
//							'address',
//							'smType',
//						),
//						'where'		 => array(
//							'id_str' => $jobMessage[ 'message_id' ]
//						),
//						'howMany'	 => 'one'
//					);
//
//					//sentiment access
//					if ( $this->company->hasAccessTo( 'sentiment-analysis' ) )
//					{
//						$sentiment				 = true;
//						$fetch[ 'columns' ][]	 = 'sentiment';
//					}
//					//klout access
//					if ( $this->company->hasAccessTo( 'klout' ) && $smType !== 'instagram' )
//					{
//						$klout					 = true;
//						$fetch[ 'columns' ][]	 = 'klout';
//					}
//
//					$message = $this->database->query( $fetch )->fetch()[ 'getMessage' ];
//
//					if ( is_assoc_array( $message ) && isset( $message[ 'coords' ], $message[ 'user' ] ) )
//					{
//
//						$return[] = $message;
//					}
//				}
//			}
//		}
//
//		return $return;
//	}

	/**
	 * Checks if $timeFrame message limit has been exceeded.
	 * @param string $timeFrame Time frame to check against.
	 * @param integer $limit Limit to check against.
	 * @param integer|string $jobId Job ID.
	 * @param string $module Job type.
	 * @param boolean $isSent [Optional]<br>Has this tween been sent to a user?
	 * @return boolean TRUE if exceeded, FALSE otherwise.
	 */
//	public function timeFrameLimitExceeded ( $timeFrame, $limit, $jobId, $module, $isSent = null )
//	{
//
//		$time = strtotime( '-' . $timeFrame, time() );
//
//		$count = $this->database->query( array(
//				'name'		 => 'getMessageCount',
//				'type'		 => 'mongo',
//				'table'		 => 'job.' . $module . ($isSent ? '.sent' : ''),
//				'operation'	 => 'count',
//				'where'		 => array(
//					'created_mongo'	 => array(
//						'$gt' => $time
//					),
//					$module . '_id'	 => $jobId
//				)
//			) )->fetch()[ 'getMessageCount' ][ 'count' ];
//
//		return ($count > $limit);
//	}

	/**
	 * Remove job.
	 * @param string $module System module.
	 * @param integer $id Job ID.
	 * @return boolean TRUE on success, FALSE otherwise.
	 */
//	public function remove ( $module, $id )
//	{
//
//		return (boolean) $this->database->query( array(
//				'name'		 => 'removeJob',
//				'type'		 => 'mysql',
//				'table'		 => 'app.' . $module . '.jobs',
//				'operation'	 => 'delete',
//				'where'		 => array(
//					'id' => $id
//				)
//			) )->fetch()[ 'removeJob' ] &&
//			(boolean) $this->database->query( array(
//				'name'		 => 'removeJob',
//				'type'		 => 'mysql',
//				'table'		 => 'app.jobs.messages',
//				'operation'	 => 'delete',
//				'where'		 => array(
//					'job_id'	 => $id,
//					'job_type'	 => $module
//				)
//			) )->fetch()[ 'removeJob' ];
//	}

	/**
	 * Counts existing $userId jobs.
	 * @param string $module System module.
	 * @param type $userId [Optional]<br>Database USER_ID. All are counted if omitted.
	 * @param boolean $ajax [Optional]<br>If requested through AJAX.
	 * @return integer USER_ID job count.
	 */
//	public function count ( $module, $userId = false, $ajax = false )
//	{
//
//		$data = array(
//			'name'		 => 'countJobs',
//			'type'		 => 'mysql',
//			'operation'	 => 'count',
//			'table'		 => 'app.' . $module . '.jobs',
//		);
//
//		if ( $userId )
//		{
//
//			$data[ 'where' ] = array(
//				'user_id' => $userId
//			);
//		}
//
//		return $this->database->query( $data )->fetch()[ 'countJobs' ][ 'count' ];
//	}

	/**
	 * Get jobs info.
	 * @param array $details [Optional]<br>Details array. ['id', 'userId']. Must contain ['jobType']
	 * @param boolean $ajax [Optional]<br>If requested through AJAX.
	 * @return array Job array.
	 */
	public function getSpecs ( $details = null, $ajax = false )
	{

//		if ( !empty( $details[ 'jobType' ] ) )
//		{

			$data = array(
				'name'		 => 'getJobs',
				'type'		 => 'mysql',
				'operation'	 => 'select',
				'table'		 => 'app.jobs',
				'where' => array(
					'deleted_at' => array(
						'operator' => 'is',
						'data' => null
					)
				)
			);

			if ( isset( $details[ 'id' ] ) )
			{

				$data[ 'where' ] = array(
					'id' => $details[ 'id' ]
				);
			}
//			elseif ( isset( $details[ 'userId' ] ) )
//			{
//
//				$data[ 'where' ] = array(
//					'user_id' => $details[ 'userId' ]
//				);
//			}

			$result = $this->database->query( $data )->fetch()[ 'getJobs' ];

			if ( $result )
			{
				if ( is_assoc_array( $result ) )
				{
					$result = array( $result );
				}
			}
			else
			{
				$result = array();
			}
			
			if (!isset($details['simple'])) {
			
				for ($i = 0, $len = count($result); $i < $len; $i++) {

					$job = $result[$i];
					
					$dataFilterLink = array(
						'name'		 => 'getFilterLink',
						'type'		 => 'mysql',
						'operation'	 => 'select',
						'table'		 => 'app.jobs.filters.link',
						'where' => array(
							'discover_id' => $job['id']
						)
					);
					
					$resultFilterLink = $this->database->query( $dataFilterLink )->fetch()[ 'getFilterLink' ];
					
					if ($resultFilterLink) {
						
						$dataFilters = array(
							'name'		 => 'getFilters',
							'type'		 => 'mysql',
							'operation'	 => 'select',
							'table'		 => 'app.jobs.filters',
							'where' => array(
								'id' => $resultFilterLink['keyword_id']
							)
						);
					
						$resultFilters = $this->database->query( $dataFilters )->fetch()[ 'getFilters' ];
						
						if ($resultFilters) {
							
							$result[$i]['filter'] = [];
							
							if (is_assoc_array($resultFilters)) {
								$resultFilters = array($resultFilters);
							}
							
							foreach ($resultFilters as $filter) {
								
								$result[$i]['filter'][] = $filter['keyword'];
								
							}
							
						}
						
					} 

					$dataMidUser = array(
						'name'		 => 'getUserForJob',
						'type'		 => 'mysql',
						'operation'	 => 'select',
						'table'		 => 'app.jobs.users',
						'where' => array(
							'discover_id' => $job['id']
						)
					);

					$resultMidUser = $this->database->query( $dataMidUser )->fetch()[ 'getUserForJob' ];

					$dataUser = array(
						'name'		 => 'getUser',
						'type'		 => 'mysql',
						'operation'	 => 'select',
						'table'		 => 'app.users',
						'where' => array(
							'id' => $resultMidUser['user_id']
						)
					);

					$user = $this->database->query( $dataUser )->fetch()[ 'getUser' ];
					
					$dataUserExtra = array(
						'name'		 => 'getUserExtra',
						'type'		 => 'mysql',
						'operation'	 => 'select',
						'table'		 => 'app.users.extra',
						'where' => array(
							'user_id' => $user['id']
						)
					);
					
					$userExtra = $this->database->query( $dataUserExtra )->fetch()[ 'getUserExtra' ];
					
					$dataCompany = array(
						'name'		 => 'getCompany',
						'type'		 => 'mysql',
						'operation'	 => 'select',
						'table'		 => 'app.companies',
						'where' => array(
							'id' => $user['company_id']
						)
					);
					
					$company = $this->database->query( $dataCompany )->fetch()[ 'getCompany' ];
					
					$user = array_merge($user, $userExtra, array ( 'company' => $company ));
					
					unset($user['password'], $user['remember_token'], $user['user_id']);
					
					$result[$i]['user'] = $user;

				}
			
			}
			
//		}
//		else
//		{
//			$result = array();
//		}

		return $result;
	}

	/**
	 * Get tokens for job.
	 * @param string $jobType Job type. discover, campaign
	 * @param int $jobId Job id.
	 * @param boolean $forUi Dont get info not meant for UI.
	 * @return array Array with 0 or more token infos.
	 */
	public function getJobTokens ( $jobType, $jobId, $forUi = false )
	{

		$fetch = array(
			'name'		 => 'getTokens',
			'type'		 => 'mysql',
			'table'		 => 'app.jobs.tokens',
			'operation'	 => 'select',
			'columns'	 => array(
				'authkey_id'
			),
			'where'		 => array(
				'discover_id' => $jobId
			)
		);

		$result = $this->database->query( $fetch )->fetch()[ 'getTokens' ];

		$return = array();

		if ( $result )
		{
			if ( is_assoc_array( $result ) )
			{
				$result = array(
					$result );
			}
			foreach ( $result as $tokenInfo )
			{
				$token = $this->twitter->getUserTokens( 'one', $tokenInfo[ 'authkey_id' ], $forUi );
				if ( count( $token ) )
				{
					$token[ 'type' ] = 'twitter';
					$return[]		 = $token;
				}
				else {
					$token = $this->instagram->getUserTokens( 'one', $tokenInfo[ 'authkey_id' ], $forUi );
					$token[ 'type' ] = 'instagram';
					$return[]		 = $token;
				}
			}
		}

		return $return;
	}

}
