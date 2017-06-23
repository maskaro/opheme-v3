<?php

namespace oPheme\Transformer;

use oPheme\Models\SocialMediaPlatform;

class SocialMediaPlatformTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'socialmediaplatform';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( SocialMediaPlatform $socialmediaplatform )
	{
		$transform = [
			'socialmediaplatform' => $socialmediaplatform->name,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
