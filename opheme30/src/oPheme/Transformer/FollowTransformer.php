<?php

namespace oPheme\Transformer;

use oPheme\Models\Follow;

class FollowTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'follow';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( Follow $follow )
	{
		$transform = [
			'authkey_id'	 => $follow->authkey_id,
			'screen_name'	 => $follow->screen_name,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
