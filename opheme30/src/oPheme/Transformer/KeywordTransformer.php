<?php

namespace oPheme\Transformer;

use oPheme\Models\Keyword;

class KeywordTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'keyword';
	
	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( Keyword $keyword )
	{
		$transform = [
			'keyword' => $keyword->keyword,
		];

		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
