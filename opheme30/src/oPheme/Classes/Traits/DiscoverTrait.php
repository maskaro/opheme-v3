<?php

namespace oPheme\Classes\Traits;

use oPheme\Exception\GoneException;
use oPheme\Exception\NotFoundException;

trait DiscoverTrait
{
	// Checks that the passed discoverId blongs to the user
	private function checkUserDiscover( $user, $discoverId )
	{
		$discoverCount = $user->discover()
						->where('id', '=', $discoverId)
						->count();
		if($discoverCount < 1) {
			$discoverCount = $user->discover()
						->onlyTrashed()
						->where('id', '=', $discoverId)
						->count();
			if($discoverCount > 0) {
				throw new GoneException('Discover Gone');
			}
			throw new NotFoundException('Discover not found');
		}
	}
}
