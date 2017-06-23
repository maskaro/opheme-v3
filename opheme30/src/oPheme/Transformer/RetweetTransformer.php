<?php

namespace oPheme\Transformer;

use oPheme\Models\Retweet;

class RetweetTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'retweet';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( Retweet $favorite )
	{
		$transform = [
			'authkey_id'	 => $favorite->authkey_id,
			'message_id'	 => $favorite->socialmediaplatform_message_id,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
