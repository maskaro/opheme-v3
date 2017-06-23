<?php

namespace oPheme\Controllers;

use Illuminate\Support\Facades\Mail;
use oPheme\Classes\WriteHelpers\UserWriteHelper;
use oPheme\Transformer\UserTransformer;
use oPheme\Models\Company;
use oPheme\Models\User;

class UserController
	extends BaseController
{
	public function __construct()
    {
		parent::__construct();		
    }

	// List
	public function adminList ()
	{
		list( $per_page, $current ) = $this->getPaginationDetails();
		$users = User::with( 'userExtra' )
						->take($per_page)
						->skip($current)
						->get();
		$cursor = $this->getCursor($users, $per_page, $current);
		return $this->respondWithCursor( $users, new UserTransformer, $cursor );
	}

	// Create
	public function adminCreate ( $companyId = null )
	{
		$requestAction = 'create';
		// get an instance of the user transformer
		$userWriteHelper = new UserWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check if there are any missing items
		$userWriteHelper->checkMissingItems( $jsonData );
		// check if there are any unknown items
		$userWriteHelper->checkUnknownItems( $jsonData );
		
		// create a new instance of the user class
		$user = new User;
		// validate the data
		$userWriteHelper->addValuesAndValidate($user,  $jsonData );
		
		// executes the external callbacks
		$userWriteHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$userWriteHelper->executeInternalCallbacks();
		
		// add data passed into method
		// If no company was passed in, add user to opheme company
		if( is_null( $companyId ) ) {
			$companyId = Company::where( 'name', '=', 'Opheme Limited' )->first()->id;
		}
		
		// Add any extra internal data items
		$userWriteHelper->internal['company_id'] = [
			'value' => $companyId,
			'database' => ['field' => 'company_id']
		];
		
		// do the creation
		$userWriteHelper->executeCreate($user);
		
		// get the user details
		$user = $this->getUser($user->id);
		
		
		// send email - pawel add data into below array (eg. firstName => $user->userExtra->first_name )
		Mail::send( 'emails.pages.welcome', 
					array(
						'firstName' => $user->userExtra->first_name,
						'lastName'	=> $user->userExtra->last_name,
					), 
					function($message) use ($user)
		{
			$message->to( $user->email, $user->userExtra->first_name . " " . $user->userExtra->last_name )
				->subject( 'Testing' );
		} );
		
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('201')->respondWithItem( $user, new UserTransformer );
	}

	// Read
	public function adminRead ( $userId )
	{
		// get the user details
		$user = $this->getUser($userId);
		return $this->respondWithItem( $user, new UserTransformer );
	}
	// Read by Current User
	public function meRead ( )
	{
		$userId = $this->getCurrentUserId();
		return $this->adminRead($userId);
	}
	
	// Update (PUT)
	public function adminUpdateFull ( $userId )
	{
		$requestAction = 'update';
		// get an instance of the user transformer
		$userWriteHelper = new UserWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check if there are any missing items
		$userWriteHelper->checkMissingItems( $jsonData );
		// check if there are any unknown items
		$userWriteHelper->checkUnknownItems( $jsonData );
		
		// get the user details
		$user = $this->getUser($userId);
		// exclude the current users id from the email unique validation check
		$user->excludeIdFromEmailValidation( $userId );
		// validate the data
		$userWriteHelper->addValuesAndValidate($user,  $jsonData );
		
		// executes the external callbacks
		$userWriteHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$userWriteHelper->executeInternalCallbacks();
		
		// do the update
		$userWriteHelper->executeUpdate($user);
		
		// get the user details
		$user = $this->getUser($user->id);
		
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondWithItem( $user, new UserTransformer );
	}
	
	// Update (PATCH)
	public function adminUpdatePartial ( $userId )
	{
		$requestAction = 'update';
		// get an instance of the user transformer
		$userWriteHelper = new UserWriteHelper($requestAction);
		
		// get the data wanting to update
		$jsonData = $this->getJsonData();
		
		// check if there are any unknown items
		$userWriteHelper->checkUnknownItems( $jsonData );
		
		// get the user details
		$user = $this->getUser($userId);
		// exclude the current users id from the email unique validation check
		$user->excludeIdFromEmailValidation( $userId );
		// validate the data
		$userWriteHelper->addValuesAndValidate($user,  $jsonData );
		
		// executes the external callbacks
		$userWriteHelper->executeExternalCallbacks();
		// executes the internal callbacks
		$userWriteHelper->executeInternalCallbacks();
		
		// do the update
		$userWriteHelper->executeUpdate($user);
		
		// get the user details
		$user = $this->getUser($user->id);
		
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondWithItem( $user, new UserTransformer );
	}

	// Delete
	public function adminDelete ( $userId )
	{
		// get the user details
		$user = $this->getUser( $userId );
		
		$user->delete();
		// return a code 204 header and no content
		return $this->setHttpStatusCode('204')->respondWithEmpty();
	}
	
	// get last login
	public function meGetLastLogin ( )
	{
		$user = $this->getCurrentUser();
		$lastLogin = is_null( $user->userExtra->last_login ) ? null : (string) $user->userExtra->last_login;
		$response = array( 'last_login' => $lastLogin);
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondSimpleArray( $response );
	}
	
	// set last login
	public function adminSetLastLogin ( $userId )
	{
		$user = $this->getUser($userId);
		$user->setLastLogin();
		$response = array( 'last_login' => (string)$user->userExtra->last_login);
		return $this->setHttpStatusCode('200')->respondSimpleArray( $response );
	}
	
	protected function getUser ( $userId )
	{
		// get the user details
		$user = User::with( 'userExtra' )->where( 'id', '=', $userId )->first();
		// return an error if the user isn't found
		if ( !$user )
		{
			$user = User::onlyTrashed()->where( 'id', '=', $userId )->first();
			if($user) {
				$this->errorGone('User Gone');
			}
			$this->errorNotFound('User not found');
		}
		return $user;
	}
}
