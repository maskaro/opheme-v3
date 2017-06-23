<?php

namespace oPheme\Classes\Traits;

use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;
use oPheme\Models\User;
use oPheme\Exception\GoneException;
use oPheme\Exception\NotFoundException;

trait UserTrait
{
	protected function getCurrentUserId()
	{
		$userId = Authorizer::getResourceOwnerId();
		return $userId;
	}
	
	protected function getCurrentUser()
	{
		return $this->getUser( $this->getCurrentUserId() );
	}
	
	protected function getUser ( $userId )
	{
		// get the user details
		$user = User::where( 'id', '=', $userId )->first();
		// return an error if the user isn't found
		if ( !$user )
		{
			$user = User::onlyTrashed()->where( 'id', '=', $userId )->first();
			if($user) {
				throw new GoneException('User Gone');
			}
			throw new NotFoundException('User not found');
		}
		return $user;
	}
}