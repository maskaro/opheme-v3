<?php

namespace oPheme\Controllers;

use Carbon;
use Exception;
use Illuminate\Support\Facades\Input;
use Instagram\Instagram;
use oPheme\Transformer\InteractionMessageTransformer;
use oPheme\Transformer\FavoriteTransformer;
use oPheme\Transformer\FollowTransformer;
use oPheme\Transformer\RetweetTransformer;
use oPheme\Classes\Traits\DiscoverTrait;
use oPheme\Models\Traits\ValidationTrait;
use oPheme\Models\AuthKey;
use oPheme\Models\Discover;
use oPheme\Models\DiscoverMessage;
use oPheme\Models\Favorite;
use oPheme\Models\Follow;
use oPheme\Models\Message;
use oPheme\Models\Interaction;
use oPheme\Models\InteractionMessage;
use oPheme\Models\Retweet;
use oPheme\Models\SocialMediaPlatform;

use Thujohn\Twitter\Facades\Twitter as Twitter;

class InteractionController extends BaseController
{
	use ValidationTrait, DiscoverTrait;
	
	public function __construct()
    {
		parent::__construct();
    }
	
	
	// List By Discover
	public function adminListByDiscover ( $discoverId )
	{
		$before = Input::get('before', '9999-12-31 23:59:59');
		$after = Input::get('after', '0000-01-01 00:00:00');
		
		// Validate the input
		$this->validationRules = array( 'before' => array('date_format:Y-m-d H:i:s'), 'after' => array('date_format:Y-m-d H:i:s') );
		$this->validate(array('before' => $before, 'after' => $after));
		
		// Uses strtotime to normalise the given date (example 2015-06-1015:00:00 - will filter correctly)
		$before = date("Y-m-d H:i:s",  strtotime($before));
		$after = date("Y-m-d H:i:s",  strtotime($after));
		
		
		list( $per_page, $current ) = $this->getPaginationDetails();
		
		$interactionMessages = InteractionMessage::with('socialMediaPlatform')
								->with('message')
								->with('interaction')
								->with('interaction.discoverMessage')
								->whereHas('interaction.discoverMessage', function($q) use ($discoverId)
								{
									$q->where('discover_id', '=', $discoverId);

								})
								// it needs to be created_at because the UI check will be happening in real time
								// and the Instagram messages will normally be processed a bit later than their original post time
								->whereBetween('created_at', array($after, $before))
								->orderBy('message_datestamp')
								->take($per_page)
								->skip($current)
								->get();

		$cursor = $this->getCursor($interactionMessages, $per_page, $current);
		return $this->respondWithCursor( $interactionMessages, new InteractionMessageTransformer, $cursor );
	}
	
	// List By Discover (By Current User)
	public function meListByDiscover ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminListByDiscover( $discoverId );
	}
	
	// Count By Discover
	public function adminCountByDiscover ( $discoverId )
	{
		$before = Input::get('before', '9999-12-31 23:59:59');
		$after = Input::get('after', '0000-01-01 00:00:00');
		
		// Validate the input
		$this->validationRules = array( 'before' => array('date_format:Y-m-d H:i:s'), 'after' => array('date_format:Y-m-d H:i:s') );
		$this->validate(array('before' => $before, 'after' => $after));
		
		// Uses strtotime to normalise the given date (example 2015-06-1015:00:00 - will filter correctly)
		$before = date("Y-m-d H:i:s",  strtotime($before));
		$after = date("Y-m-d H:i:s",  strtotime($after));
		
		$count = InteractionMessage::whereHas('interaction.discoverMessage', function($q) use ($discoverId)
					{
						$q->where('discover_id', '=', $discoverId);

					})
					// it needs to be created_at because the UI check will be happening in real time
					// and the Instagram messages will normally be processed a bit later than their original post time
					->whereBetween('created_at', array($after, $before))
					->count();
								
		$response = array( 'count' => $count);
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondSimpleArray( $response );
	}
	
	// Count By Discover (By Current User)
	public function meCountByDiscover ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminCountByDiscover( $discoverId );
	}
	
	// Reply
	public function meReply (  )
	{
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check the data given
		if ( isset($jsonData['discover_id']) XOR isset($jsonData['interaction_id']) ) {
			
			$this->validationRules =
				array(
					'authkey_id' => 'required',
					'inreplyto_message_id' => 'required_with:discover_id',
					'message' => 'required|max:140'
				);
			$this->validate( $jsonData );
			
			// depending on what data was given changes the way the function works
			if( isset($jsonData['discover_id']) ) {
				$responseType = 'discover';
				$discoverId = $jsonData['discover_id'];
			} elseif( isset($jsonData['interaction_id']) ) {
				$responseType = 'interaction';
				$interaction  = Interaction::where('id', '=', $jsonData['interaction_id'])->first();
				// return an error if the interaction isn't found
				if ( !$interaction )
				{
					$interaction = Interaction::onlyTrashed()->where('id', '=', $jsonData['interaction_id'])->first();
					if($interaction) {
						$this->errorGone('Interaction Gone');
					}
					$this->errorNotFound('Interaction not found');
				}
				$discoverId = $interaction->discoverMessage()->first()->discover_id;
			}
			
		} else {
			$this->respondWithError ( $message = 'Require only one of discover_id or interaction_id', 
											$errorType = 'parameter_error', 
											$errorCode = 301, 
											$httpStatusCode = 400);
		}

		// get the discover
		$discover = Discover::where('id', '=', $discoverId)->first();
		// return an error if the discover isn't found
		if ( !$discover )
		{
			$discover = Discover::onlyTrashed()->where('id', '=', $jsonData['discover_id'])->first();
			if($discover) {
				$this->errorGone('Discover Gone');
			}
			$this->errorNotFound('Discover not found');
		}
				
		// checks for authkey belonging to discover
		if(
			! AuthKey::where('id', '=', $jsonData['authkey_id'])
						->whereHas('discover', function($q) use ($discoverId)
							{
								$q->where('id', '=', $discoverId);
							})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in discover');
		}
		
		// get the current user
		$user = $this->getCurrentUser();
		// checks for discover belonging to user
		$this->checkUserDiscover( $user, $discoverId );
				
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($jsonData)
								{
									$q->where('id', '=', $jsonData['authkey_id']);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		} else {
			// get the social media platform from the auth key
			$socialMediaPlatform = SocialMediaPlatform::whereHas('authkey', function($q) use ($jsonData)
								{
									$q->where('id', '=', $jsonData['authkey_id']);
								})->first();
		}
		
		// checks for discover or interaction belonging to user
		// checks that "inreplyto_message_id" within interaction or discover
		switch($responseType){
			case 'discover':
				
				//checks that "inreplyto_message_id" within discover
				if(
					! $discover->whereHas('discoverMessage', function($q) use ($jsonData, $socialMediaPlatform)
										{
											$q->where('socialmediaplatform_id', '=', $socialMediaPlatform->id)
											->where('socialmediaplatform_message_id', '=', $jsonData['inreplyto_message_id']);
										})->count() > 0
				) {
					$this->errorNotFound('inreplyto_message not found');
				}
				
				break;
			case 'interaction':
				if( isset($jsonData['inreplyto_message_id']) ) {
					//checks that "inreplyto_message_id" within interaction
					if(
						! Interaction::where('id', '=', $jsonData['interaction_id'])->whereHas('interactionMessage', function($q) use ($jsonData, $socialMediaPlatform)
											{
												$q->where('socialmediaplatform_id', '=', $socialMediaPlatform->id)
												->where('socialmediaplatform_message_id', '=', $jsonData['inreplyto_message_id']);
											})->count() > 0
					) {
						$this->errorNotFound('inreplyto_message not found');
					}
				} else {
					// If a inreplyto message id hasn't been given.
					// The original discover message of the interaction will be use for the reply
					$jsonData['inreplyto_message_id'] =	$interaction->discoverMessage->socialmediaplatform_message_id;
				}
				break;
		}
		
		// get the authkey details
		
		$authKey = AuthKey::where('id', '=', $jsonData['authkey_id'])->first();

		// send reply to twitter / instagram
		switch($socialMediaPlatform->name){
			case 'twitter':
				$config = [
					'token'		 => $authKey->token,
					'secret'	 => $authKey->token_secret,
				];
				// Reconfig to update the authenticated user
				Twitter::reconfig($config);
				
				// Tweet
				try {
					$response = Twitter::postTweet(['status' => $jsonData['message'], 'in_reply_to_status_id' => $jsonData['inreplyto_message_id'], 'format' => 'json']);
				} catch ( Exception $e ) {
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			case 'instagram':
				$instagram = new Instagram($authKey->token);
				$currentUser = $instagram->getCurrentUser();
				// add comment
				try {
					$response = $currentUser->addMediaComment($jsonData['inreplyto_message_id'], $jsonData['message']);
				} catch ( Exception $e ) {
					$this->errorSocialMedia($e->getMessage());
				}

				// manually build the response for the data to be added to the database, 
				// as Instagram don't give comment Id's back when sending a reply
				$response = array();
				$response['text'] = $jsonData['message'];
				$response['created_time'] = Carbon::now()->timestamp;
				$response['from']= $currentUser->getData();
				$response = json_encode($response);
				break;
			default:
				$this->errorUnsupportedSocialMedia();
				break;
		}
		
		// record sent reply in database
		$carbonCurrentTime = Carbon::now();
		$currentTime = $carbonCurrentTime->toDateTimeString(); 
		
		// Save the message
		$message = Message::firstOrCreate( json_decode( $response , true) );
		$message->opheme_backend = array('social_media_platform' => $socialMediaPlatform->name,
										'created_timestamp' => $currentTime,
										'sentiment' => '',
										'klout_score' => '');
		$message->save();
		
		// Do specific things for different requests
		switch($responseType){
			case 'discover':
				// Get the discover message id to link the interaction to
				$discoverMessage = DiscoverMessage::where('socialmediaplatform_id', '=', $socialMediaPlatform->id)
								->where('socialmediaplatform_message_id', '=', $jsonData['inreplyto_message_id'])
								->first();
									
				// Create a new interaction
				$interaction = new Interaction;
				$interaction->discoverMessage()->associate($discoverMessage);
				$interaction->save();
				
				break;
			case 'interaction':
				// None Required Currently
				break;
		}
		// Save details into interactionmessage table
		$interactionMessage = new InteractionMessage;
		$interactionMessage->backend_message_id = $message->_id;
		switch ($socialMediaPlatform->name) {
			case 'twitter':
				$interactionMessage->socialmediaplatform_message_id = $message->id;
				$interactionMessage->socialmediaplatform_user_id = $message->user->id;
				$interactionMessage->socialmediaplatform_user_screen_name = $message->user->screen_name;
				break;
			case 'instagram':
				// no need to specify null
//				$interactionMessage->socialmediaplatform_message_id = null;
				$interactionMessage->socialmediaplatform_user_id = $message->from->id;
				$interactionMessage->socialmediaplatform_user_screen_name = $message->from->username;
				break;
			default:
				break;
		}
		$interactionMessage->message_datestamp = $message->opheme_backend['created_timestamp'];
		$interactionMessage->interaction()->associate($interaction);
		$interactionMessage->socialMediaPlatform()->associate($socialMediaPlatform);
		$interactionMessage->save();

		return $this->respondWithItem( $interactionMessage, new InteractionMessageTransformer );
	}
	
	// List Favorites
	public function meListFavorite ( $authkeyId, $messageIds = false)
	{
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($authkeyId)
								{
									$q->where('id', '=', $authkeyId);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		list( $per_page, $current ) = $this->getPaginationDetails();
		
		$favoritesQuery = Favorite::where('authkey_id', '=', $authkeyId);
		
		if( $messageIds ) {
			$favoritesQuery->where(function($q) use($messageIds)
			{
				$messageIdsArray = explode( ',', $messageIds );
				foreach($messageIdsArray as $messageId) {
					$q->orWhere('socialmediaplatform_message_id', '=', trim($messageId));
				}
				
			});
		}
			
		$favorites = $favoritesQuery->take($per_page)
			->skip($current)
			->get();
		
		$cursor = $this->getCursor($favorites, $per_page, $current);
		return $this->respondWithCursor( $favorites, new FavoriteTransformer, $cursor );
	}
	
	// Create Favorite
	public function meCreateFavorite (  )
	{
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check the data given
		$this->validationRules =
				array(
					'authkey_id' => 'required',
					'message_id' => 'required',
				);
		$this->validate( $jsonData );
			
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($jsonData)
								{
									$q->where('id', '=', $jsonData['authkey_id']);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// check favorite isn't already in the database
		if(
			Favorite::where('authkey_id', '=', $jsonData['authkey_id'])
					->where('socialmediaplatform_message_id', '=', $jsonData['message_id'])
					->count() > 0
		) {
			$this->errorSocialMedia('[139] You have already favorited this status.');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $jsonData['authkey_id'])->first();
								
		// send reply to twitter / instagram
		switch($authKey->socialMediaPlatform->name){
			case 'twitter':
				$config = [
					'token'		 => $authKey->token,
					'secret'	 => $authKey->token_secret,
				];
				// Reconfig to update the authenticated user
				Twitter::reconfig($config);
				
				// Tweet
				try {
					Twitter::postFavorite(['id' => $jsonData['message_id'], 'format' => 'json']);
				} catch ( Exception $e ) {
					// add to database if
					if($e->getMessage() == "[139] You have already favorited this status.") {
						$favorite = new Favorite();
						$favorite->authkey_id = $jsonData['authkey_id'];
						$favorite->socialmediaplatform_message_id = $jsonData['message_id'];
						$favorite->save();
					}
								
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			case 'instagram':
				$instagram = new Instagram($authKey->token);
				$currentUser = $instagram->getCurrentUser();
				// add comment
				try {
					$response = $currentUser->addLike($jsonData['message_id']);
				} catch ( Exception $e ) {
					// add to database if
//					if($e->getMessage() == "XXXXX") {
//						$favorite = new Favorite();
//						$favorite->authkey_id = $jsonData['authkey_id'];
//						$favorite->socialmediaplatform_message_id = $jsonData['message_id'];
//						$favorite->save();
//					}
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			default:
				$this->errorUnsupportedSocialMedia();
				break;
		}	
		
		// Record favorite in database
		$favorite = new Favorite();
		$favorite->authkey_id = $jsonData['authkey_id'];
		$favorite->socialmediaplatform_message_id = $jsonData['message_id'];
		$favorite->save();
		
		return $this->respondWithItem( $favorite, new FavoriteTransformer );
	}
	
	// Delete Favorites
	public function meDeleteFavorite ( $authkeyId, $messageId )
	{
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($authkeyId)
								{
									$q->where('id', '=', $authkeyId);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// Check the favorite exist in our database
		$favorite = Favorite::where('authkey_id', '=', $authkeyId)
			->where('socialmediaplatform_message_id', '=', trim($messageId))
			->first();
		
		if( !$favorite ) {
			$this->errorNotFound('Favorite not found');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $authkeyId)->first();
		
		// send reply to twitter / instagram
		switch($authKey->socialMediaPlatform->name){
			case 'twitter':
				$config = [
					'token'		 => $authKey->token,
					'secret'	 => $authKey->token_secret,
				];
				// Reconfig to update the authenticated user
				Twitter::reconfig($config);
				
				// Tweet
				try {
					Twitter::destroyFavorite(['id' => $messageId, 'format' => 'json']);
				} catch ( Exception $e ) {
					// add to database if
					if($e->getMessage() == "[144] No status found with that ID.") {
						$favorite->Delete();
					}
								
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			case 'instagram':
				$instagram = new Instagram($authKey->token);
				$currentUser = $instagram->getCurrentUser();
				// add comment
				try {
					$response = $currentUser->deleteLike($messageId);
				} catch ( Exception $e ) {
					// add to database if
//					if($e->getMessage() == "XXXXX") {
//						$favorite->Delete();
//					}
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			default:
				$this->errorUnsupportedSocialMedia();
				break;
		}
		
		$favorite->Delete();
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// List Follow
	public function meListFollow ( $authkeyId, $screenNames = false )
	{
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($authkeyId)
								{
									$q->where('id', '=', $authkeyId);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		list( $per_page, $current ) = $this->getPaginationDetails();
		
		$followsQuery = Follow::where('authkey_id', '=', $authkeyId);
		
		if( $screenNames ) {
			$followsQuery->where(function($q) use($screenNames)
			{
				$screenNamesArray = explode( ',', $screenNames );
				foreach($screenNamesArray as $screenName) {
					$q->orWhere('screen_name', '=', trim($screenName));
				}
				
			});
		}
			
		$follows = $followsQuery->take($per_page)
			->skip($current)
			->get();
		
		$cursor = $this->getCursor($follows, $per_page, $current);
		return $this->respondWithCursor( $follows, new FollowTransformer, $cursor );
	}
	
	
	// Create Follow
	public function meCreateFollow (  )
	{
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check the data given
		$this->validationRules =
				array(
					'authkey_id' => 'required',
					'screen_name' => 'required',
				);
		$this->validate( $jsonData );
			
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($jsonData)
								{
									$q->where('id', '=', $jsonData['authkey_id']);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// check favorite isn't already in the database
		if(
			Follow::where('authkey_id', '=', $jsonData['authkey_id'])
					->where('screen_name', '=', $jsonData['screen_name'])
					->count() > 0
		) {
			$this->errorSocialMedia('You are already following this user.');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $jsonData['authkey_id'])->first();
								
		// send reply to twitter / instagram
		switch($authKey->socialMediaPlatform->name){
			case 'twitter':
				$config = [
					'token'		 => $authKey->token,
					'secret'	 => $authKey->token_secret,
				];
				// Reconfig to update the authenticated user
				Twitter::reconfig($config);
				
				// Tweet
				try {
					Twitter::postFollow(['screen_name' => $jsonData['screen_name'], 'format' => 'json']);
				} catch ( Exception $e ) {
//					 add to database if
//					if($e->getMessage() == "XXXXXX") {
//						$follow = new Follow();
//						$follow->authkey_id = $jsonData['authkey_id'];
//						$follow->screen_name = $jsonData['screen_name'];
//						$follow->save();
//					}
							
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			case 'instagram':
				$instagram = new Instagram($authKey->token);
				$currentUser = $instagram->getCurrentUser();
				// add comment
				try {
					$user = $instagram->getUserByUsername($jsonData['screen_name']);
					$currentUser->follow($user);
				} catch ( Exception $e ) {
					// add to database if
//					if($e->getMessage() == "XXXXX") {
//						$follow = new Follow();
//						$follow->authkey_id = $jsonData['authkey_id'];
//						$follow->screen_name = $jsonData['screen_name'];
//						$follow->save();
//					}
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			default:
				$this->errorUnsupportedSocialMedia();
				break;
		}	
		// Record favorite in database
		$follow = new Follow();
		$follow->authkey_id = $jsonData['authkey_id'];
		$follow->screen_name = $jsonData['screen_name'];
		$follow->save();
		
		return $this->respondWithItem( $follow, new FollowTransformer );
	}
	
	// Delete Follow
	public function meDeleteFollow ( $authkeyId, $screenName )
	{
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($authkeyId)
								{
									$q->where('id', '=', $authkeyId);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// Check the favorite exist in our database
		$follow = Follow::where('authkey_id', '=', $authkeyId)
			->where('screen_name', '=', trim($screenName))
			->first();
		
		if( !$follow ) {
			$this->errorNotFound('Follow not found');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $authkeyId)->first();
		
		// send reply to twitter / instagram
		switch($authKey->socialMediaPlatform->name){
			case 'twitter':
				$config = [
					'token'		 => $authKey->token,
					'secret'	 => $authKey->token_secret,
				];
				// Reconfig to update the authenticated user
				Twitter::reconfig($config);
				
				// Tweet
				try {
					Twitter::postUnfollow(['screen_name' => $screenName, 'format' => 'json']);
				} catch ( Exception $e ) {
					// add to database if
//					if($e->getMessage() == "XXXXX") {
//						$favorite->Delete();
//					}
								
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			case 'instagram':
				$instagram = new Instagram($authKey->token);
				$currentUser = $instagram->getCurrentUser();
				// add comment
				try {
					$user = $instagram->getUserByUsername($screenName);
					$currentUser->unFollow($user);
				} catch ( Exception $e ) {
					// add to database if
//					if($e->getMessage() == "XXXXX") {
//						$favorite->Delete();
//					}
					$this->errorSocialMedia($e->getMessage());
				}
				break;
			default:
				$this->errorUnsupportedSocialMedia();
				break;
		}
		
		$follow->Delete();
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// List Retweet
	public function meListRetweet ( $authkeyId, $messageIds = false)
	{
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($authkeyId)
								{
									$q->where('id', '=', $authkeyId);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $authkeyId)->first();
		
		// error if this is not a twitter authkey (as this is a twitter only endpoint)
		if($authKey->socialMediaPlatform->name !== 'twitter') {
			$this->errorUnsupportedSocialMedia();
		}
		
		list( $per_page, $current ) = $this->getPaginationDetails();
		
		$retweetsQuery = Retweet::where('authkey_id', '=', $authkeyId);
		
		if( $messageIds ) {
			$retweetsQuery->where(function($q) use($messageIds)
			{
				$messageIdsArray = explode( ',', $messageIds );
				foreach($messageIdsArray as $messageId) {
					$q->orWhere('socialmediaplatform_message_id', '=', trim($messageId));
				}
				
			});
		}
			
		$retweets = $retweetsQuery->take($per_page)
			->skip($current)
			->get();
		
		$cursor = $this->getCursor($retweets, $per_page, $current);
		return $this->respondWithCursor( $retweets, new RetweetTransformer, $cursor );
	}
	
	// Create Retweet
	public function meCreateRetweet(  )
	{
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check the data given
		$this->validationRules =
				array(
					'authkey_id' => 'required',
					'message_id' => 'required',
				);
		$this->validate( $jsonData );
			
		// get the current user
		$user = $this->getCurrentUser();
		
		// checks for authkey belonging to user
		if(
			! $user->whereHas('authkey', function($q) use ($jsonData)
								{
									$q->where('id', '=', $jsonData['authkey_id']);
								})->count() > 0
		) {
			$this->errorNotFound('Authkey not found in user');
		}
		
		// get the authkey details
		$authKey = AuthKey::with('socialMediaPlatform')->where('id', '=', $jsonData['authkey_id'])->first();
		
		// error if this is not a twitter authkey (as this is a twitter only endpoint)
		if($authKey->socialMediaPlatform->name !== 'twitter') {
			$this->errorUnsupportedSocialMedia();
		}
		
		// check the retweet isn't already in the database
		if(
			Retweet::where('authkey_id', '=', $jsonData['authkey_id'])
					->where('socialmediaplatform_message_id', '=', $jsonData['message_id'])
					->count() > 0
		) {
			$this->errorSocialMedia('[327] You have already retweeted this tweet.');
		}

		$config = [
			'token'		 => $authKey->token,
			'secret'	 => $authKey->token_secret,
		];
		// Reconfig to update the authenticated user
		Twitter::reconfig($config);

		// Tweet
		try {
			Twitter::postRt($jsonData['message_id'] , [ 'format' => 'json']);
		} catch ( Exception $e ) {
			// add to database if
						if($e->getMessage() == "[327] You have already retweeted this tweet.") {
							$retweet = new Retweet();
							$retweet->authkey_id = $jsonData['authkey_id'];
							$retweet->socialmediaplatform_message_id = $jsonData['message_id'];
							$retweet->save();
						}
			$this->errorSocialMedia($e->getMessage());
		}
	
		// Record favorite in database
		$retweet = new Retweet();
		$retweet->authkey_id = $jsonData['authkey_id'];
		$retweet->socialmediaplatform_message_id = $jsonData['message_id'];
		$retweet->save();
		
		return $this->respondWithItem( $retweet, new RetweetTransformer );
	}
}
