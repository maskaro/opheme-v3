<?php

namespace oPheme\Transformer;

use oPheme\Models\Favorite;

class FavoriteTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'favorite';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( Favorite $favorite )
	{
		$transform = [
			'authkey_id'	 => $favorite->authkey_id,
			'message_id'	 => $favorite->socialmediaplatform_message_id,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
