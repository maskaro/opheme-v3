<?php

namespace oPheme\Classes\Traits;

use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;
use Illuminate\Support\Facades\Config;

trait DisallowedItemsTrait
{
	/**
	 * Removes disallowed items from the array
	 * @param array $items
	 * @return array $items
	 */
	protected function removeDisallowedItems($items)
	{
		// Get the disallowed items
		if ( !empty( $this->getDisallowedItems() ) ) {
			$disallowedItems = $this->getDisallowedItems();
			// Go through the disallowedItems array
			foreach ( $disallowedItems as $disallowedItem )	{
				// remove the disallowedItems from the writeableItems array
				unset( $items[ $disallowedItem ] );
			}
		}
		return $items;
	}
	
	/**
	 * 	Set what items are disallowed
	 */
	protected function getDisallowedItems ()
	{
		// get disallowed based on scope
		$disallowedItemsByScope = $this->getDisallowedItemsByScope();
		// get disallowed based on client
		$disallowedItemsByClient = $this->getDisallowedItemsByClient();

		// merge the 2 disallowed arrays together and unique them
		$disallowedItems = array_unique(array_merge($disallowedItemsByScope, $disallowedItemsByClient));
		return $disallowedItems;
	}
	
	/**
	 * 	Set what items are disallowed based on scope
	 */
	protected function getDisallowedItemsByScope ()
	{
		// Check disallowed items baised on the scope
		foreach ( Authorizer::getScopes() as $scope )
		{
			// If the config for the scope is null, the scope has full access,
			// therfore the client has full access
			if ( !$scopeDisallowedItemsByScope[] = 
					Config::get( 'transformer_writehelper.disallowed_items.' . $this->type . '.scope.' . $scope->getId() ) )
			{
				return array();
			}
		}

		// if there is only one array no need to intersect the arrays
		if ( count( $scopeDisallowedItemsByScope ) == 1 )
		{
			return $scopeDisallowedItemsByScope[0];
		}
		
		// Only keep the disallowed items that is in every scope the client has
		return call_user_func_array( 'array_intersect', $scopeDisallowedItemsByScope );
	}
	/**
	 * 	Set what items are disallowed based on client id
	 */
	protected function getDisallowedItemsByClient ()
	{
		// Check disallowed items based on client id
		$clientId = Authorizer::getClientId();
		if ( !$clientDisallowedItems[] = 
				Config::get( 'transformer.disallowed_items.' . $this->type . '.client.' . $clientId ) )
		{
			return array();
		}
		
		return $clientDisallowedItems[0];
	}
}