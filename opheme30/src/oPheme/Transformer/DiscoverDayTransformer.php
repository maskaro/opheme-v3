<?php

namespace oPheme\Transformer;

use oPheme\Models\DiscoverDay;

class DiscoverDayTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'discoverday';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( DiscoverDay $day )
	{
		$transform = [
			'day' => $day->day,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
