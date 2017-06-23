<?php

namespace oPheme\Controllers;

use oPheme\Transformer\AuthKeyTransformer;
use oPheme\Models\AuthKey;

class AuthKeyController
	extends BaseController
{	
	public function __construct()
    {
		parent::__construct();
    }

	// List
	public function adminList ( )
	{	
		list( $per_page, $current ) = $this->getPaginationDetails();
		$authKeys = AuthKey::with( 'socialMediaPlatform' )
						->take($per_page)
						->skip($current)
						->get();
		$cursor = $this->getCursor($authKeys, $per_page, $current);
		return $this->respondWithCursor( $authKeys, new AuthKeyTransformer, $cursor );
	}
	
	// List by User
	public function adminListByUser ( $userId )
	{
		$user = $this->getUser($userId);
		list( $per_page, $current ) = $this->getPaginationDetails();
		$authKeys = $user->authkey()->with( 'socialMediaPlatform' )
						->take($per_page)
						->skip($current)
						->get();
		$cursor = $this->getCursor($authKeys, $per_page, $current);
		return $this->respondWithCursor( $authKeys, new AuthKeyTransformer, $cursor );
	}
	//List by Current User
	public function meList( )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminListByUser($userId);
	}
	
	
	// Read
	public function adminRead ( $authKeyId )
	{
		$authKey = $this->getAuthKey( $authKeyId );
		return $this->respondWithItem( $authKey, new AuthKeyTransformer );
	}
	
	// Delete
	public function adminDelete ( $authKeyId )
	{
		$authKey = $this->getAuthKey( $authKeyId );
		$authKey->users()->detach();
		$authKey->forceDelete();
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// Delete All by User
	public function adminDeleteAllByUser($userId)
	{
		// get the user
		$user = $this->getUser($userId);
		// get the authkeys for that user
		$authKeys = $user->authKey()->get();
		// delete links between the user and any authkeys
		$user->authKey()->detach();
		// We need to delete any authkeys that have been detached
		// and that don't belong to another user
		$authKeys->each(function($authKey) {
			// If there is no user for the authkey
			if( $authKey->user()->get()->isEmpty() ) {
				// soft delete authkey
				$authKey->forceDelete();
			}
		});
		
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// Current User Delete All
	public function meDeleteALL (  )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminDeleteAllByUser($userId);
	}
	
	// Delete by User
	public function adminDeleteByUser($userId, $authKeyId)
	{
		// get the authkey
		$authKey = $this->getAuthKey( $authKeyId );
		
		// if the authkey doesn't exist for the user
		if( $authKey->user()->where('id', '=', $userId)->get()->isEmpty() ) {
			$this->errorNotFound('Authkey not found');
		}
		// delete links between the authkey and the user
		$authKey->user()->detach($userId);

		// We need to delete the authkey that has been detached if it doens't belong to another user
		// If there is no user for the authkey
		if( $authKey->user()->get()->isEmpty() ) {
			// soft delete authkey
			$authKey->forceDelete();
		}

		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// Current User Delete
	public function meDelete ( $authkeyId )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminDeleteByUser($userId, $authkeyId);
	}
	
	// Count by User
	public function adminCountByUser ( $userId )
	{
		$user = $this->getUser($userId);
		$count = $user->authkey()->count();
		$response = array( 'count' => $count);
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondSimpleArray( $response );
	}
	
	// Count by User
	public function meCount (  )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminCountByUser($userId);
	}
	
	// get Auth Key with error exceptions
	protected function getAuthKey ( $authKeyId )
	{
		
		// get the authkey
		$authKey = Authkey::where('id', '=', $authKeyId)->with( 'socialMediaPlatform' )->first();
		// return an error if the user isn't found
		if ( !$authKey )
		{
			$authKey = Authkey::onlyTrashed()->where('id', '=', $authKeyId)->first();
			if($authKey) {
				$this->errorGone('Authkey Gone');
			}
			$this->errorNotFound('Authkey not found');
		}
		return $authKey;
	}
}
