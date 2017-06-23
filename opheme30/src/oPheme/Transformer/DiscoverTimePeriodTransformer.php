<?php

namespace oPheme\Transformer;

use oPheme\Models\DiscoverTimeperiod;

class DiscoverTimePeriodTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'discovertimeperiod';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( DiscoverTimeperiod $timeperiod )
	{
		$transform = [
			'start'	 => $timeperiod->start,
			'end'	 => $timeperiod->end,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
