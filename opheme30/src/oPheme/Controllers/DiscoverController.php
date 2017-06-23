<?php

namespace oPheme\Controllers;

use oPheme\Transformer\DiscoverTransformer;
use oPheme\Models\Discover;
use oPheme\Classes\WriteHelpers\DiscoverWriteHelper;
use oPheme\Classes\Traits\DiscoverTrait;

class DiscoverController
	extends BaseController
{	
	use DiscoverTrait;
	
	public function __construct()
    {
		parent::__construct();
    }

	// List
	public function adminList ( )
	{
		list( $per_page, $current ) = $this->getPaginationDetails();
		$discovers = Discover::take($per_page)
						->skip($current)
						->get();
		$cursor = $this->getCursor($discovers, $per_page, $current);
		return $this->respondWithCursor( $discovers, new DiscoverTransformer, $cursor );
	}
	
	// List by User
	public function adminListByUser ( $userId )
	{	
		$user = $this->getUser($userId);
		list( $per_page, $current ) = $this->getPaginationDetails();
		$discovers = $user->discover()
						->take($per_page)
						->skip($current)
						->get();
		$cursor = $this->getCursor($discovers, $per_page, $current);
		return $this->respondWithCursor( $discovers, new DiscoverTransformer, $cursor );
		
	}
	
	// List by Current User
	public function meList( )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminListByUser($userId);
	}
	
	// Create
	public function adminCreateByUser ( $userId )
	{
		$user = $this->getUser($userId);
		
		$requestAction = 'create';
		// get an instance of the write helper
		$writeHelper = new DiscoverWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		$authKeys = $this->checkRetrieveAuthKeys($user, $jsonData);
		// unset the authkeys entry so to not error as an unknown item
		unset($jsonData['authkeys']);
		
		// check if there are any missing items
		$writeHelper->checkMissingItems( $jsonData );
		// check if there are any unknown items
		$writeHelper->checkUnknownItems( $jsonData );
		
		// create a new instance of the user class
		$discover = new Discover;
		// validate the data
		$writeHelper->addValuesAndValidate($discover,  $jsonData );
		
		// executes the external callbacks
		$writeHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$writeHelper->executeInternalCallbacks();
		
		// do the creation
		$writeHelper->executeCreate($discover);
		
		// Attach the authKeys
		$discover->authKey()->attach($authKeys);
		
		// Attach the user
		$discover->user()->attach($user);
		
		// get the discover details
		$discover = $this->getDiscover($discover->id);
		
		// return a code 201 header and pass back the updated item
		return $this->setHttpStatusCode('201')->respondWithItem( $discover, new DiscoverTransformer );
	}
	
	// Create by Current User
	public function meCreate( )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminCreateByUser($userId);
	}
	
	// Read
	public function adminRead ( $discoverId )
	{
		$discover = $this->getDiscover( $discoverId );
		return $this->respondWithItem( $discover, new DiscoverTransformer );
	}
	
	// Update (PUT)
	public function adminUpdateFullByUser ( $userId, $discoverId )
	{
		$user = $this->getUser($userId);
		$requestAction = 'update';
		// get an instance of the write helper
		$writeHelper = new DiscoverWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		$authKeys = $this->checkRetrieveAuthKeys($user, $jsonData);
		// unset the authkeys entry so to not error as an unknown item
		unset($jsonData['authkeys']);
	
		// check if there are any missing items
		$writeHelper->checkMissingItems( $jsonData );
		// check if there are any unknown items
		$writeHelper->checkUnknownItems( $jsonData );
		
		$discover = $this->getDiscover($discoverId);
		
		// validate the data
		$writeHelper->addValuesAndValidate($discover,  $jsonData );
		
		// executes the external callbacks
		$writeHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$writeHelper->executeInternalCallbacks();
		
		// do the update
		$writeHelper->executeUpdate($discover);
		
		// Detach all the existing authKeys
		$discover->authKey()->detach();
		// Attach the authKeys
		$discover->authKey()->attach($authKeys);
		
		// get the discover details
		$discover = $this->getDiscover($discover->id);
		
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondWithItem( $discover, new DiscoverTransformer );
	}
	
	// Update (PUT) by Current User
	public function meUpdateFull ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminUpdateFullByUser( $user->id, $discoverId );	
	}
	
	// Update (PATCH)
	public function adminUpdatePartialByUser ( $userId, $discoverId )
	{
		$user = $this->getUser($userId);
		$requestAction = 'update';
		// get an instance of the write helper
		$writeHelper = new DiscoverWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		if( isset( $jsonData['authkeys'] ) ) {
			$authKeys = $this->checkRetrieveAuthKeys($user, $jsonData);
			// unset the authkeys entry so to not error as an unknown item
			unset($jsonData['authkeys']);
		}
		
		// check if there are any unknown items
		$writeHelper->checkUnknownItems( $jsonData );
		
		$discover = $this->getDiscover($discoverId);
		
		// validate the data
		$writeHelper->addValuesAndValidate($discover,  $jsonData );
		
		// executes the external callbacks
		$writeHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$writeHelper->executeInternalCallbacks();
		
		// do the update
		$writeHelper->executeUpdate($discover);
		
		// If authkey were giving in the patch
		if( isset( $authKeys ) ) {
			// Detach all the existing authKeys
			$discover->authKey()->detach();
			// Attach the authKeys
			$discover->authKey()->attach($authKeys);
		}
		
		// get the discover details
		$discover = $this->getDiscover($discover->id);
		
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondWithItem( $discover, new DiscoverTransformer );
	}
	
	// Update (PATCH) by Current User
	public function meUpdatePartial ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminUpdatePartialByUser( $user->id, $discoverId );
	}
	
	// Delete
	public function adminDelete ( $discoverId )
	{
		$discover = $this->getDiscover( $discoverId );
		$discover->Delete();
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// Delete by Current User
	public function meDelete ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminDelete( $discoverId );
	}

	// get Discover with error exceptions
	private function getDiscover ( $discoverId )
	{
		// get the discover
		$discover = Discover::where('id', '=', $discoverId)->first();
		// return an error if the user isn't found
		if ( !$discover )
		{
			$discover = Discover::onlyTrashed()->where('id', '=', $discoverId)->first();
			if($discover) {
				$this->errorGone('Discover Gone');
			}
			$this->errorNotFound('Discover not found');
		}
		return $discover;
	}
	
	// get the AuthKeys out of the json data, checking the authkeys belong to a user
	private function checkRetrieveAuthKeys($user, $jsonData)
	{
		// get the authkeys to be used to attach to the discover after creation
		if( !isset( $jsonData['authkeys']['data'] ) || empty( $jsonData['authkeys']['data'] ) ) {
			$this->errorWrongArgs("Missing AuthKeys");
		}
		$authKeys = array();
		foreach( $jsonData['authkeys']['data'] as $key) {
			if( !isset( $key['id'] ) ) {
				$this->errorWrongArgs("Missing AuthKey Id");
			}

			$authKey = $user->authKey()->where('id', '=', $key['id'])->first();
			if ( !$authKey )
			{
				$authKey = $user->authKey()->onlyTrashed()->where('id', '=', $key['id'])->first();
				if($authKey) {
					$this->errorGone('Authkey Gone');
				}
				$this->errorNotFound('Authkey not found');
			}
			$authKeys[] = $authKey->id;
		}
		
		return $authKeys;
	}
}
