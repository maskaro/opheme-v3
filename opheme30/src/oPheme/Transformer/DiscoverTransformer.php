<?php

namespace oPheme\Transformer;

use oPheme\Models\Discover;

class DiscoverTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'discover';

	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'days',
		'keywords',
		'timeperiods',
		'authkeys',
		'users'
	];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [
//		'days',
		'keywords',
//		'timeperiods',
		'authkeys'
	];

	private function generateEmailConfirmationToken ()
	{
		return str_random( 8 );
	}

	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( Discover $discover )
	{
		$transform = [
			'id'		 => $discover->id,
			'name'		 => $discover->name,
			'latitude'	 => $discover->latitude,
			'longitude'	 => $discover->longitude,
			'radius'	 => $discover->radius,
//			'start_date' => is_null( $discover->start_date ) ? null : (string) $discover->start_date,
//			'end_date'	 => is_null( $discover->end_date ) ? null : (string) $discover->end_date,
			'running'	 => (bool) $discover->running,
			'created_at' => (string) $discover->created_at
		];

		$transform = $this->removeDisallowedItems($transform);

		// Add links on
		$transform[ 'links' ] = [
			[
				'rel'	 => 'self',
				'uri'	 => '/discovers/' . $discover->id
			],
		];
		return $transform;
	}

	/**
	 * Include Days
	 *
	 * @param Discover $discover
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeDays ( Discover $discover )
	{
		$days = $discover->discoverDay;
		return $this->collection( $days, new DiscoverDayTransformer );
	}

	/**
	 * Include Keywords
	 *
	 * @param Discover $discover
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeKeywords ( Discover $discover )
	{
		$keywords = $discover->keyword;
		return $this->collection( $keywords, new KeywordTransformer );
	}

	/**
	 * Include Timeperiods
	 *
	 * @param Discover $discover
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeTimeperiods ( Discover $discover )
	{
		$timeperiods = $discover->discoverTimeperiod;
		return $this->collection( $timeperiods, new DiscoverTimePeriodTransformer );
	}

	/**
	 * Include Authkeys
	 *
	 * @param Discover $discover
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeAuthkeys ( Discover $discover )
	{
		$authkeys = $discover->authkey;
		return $this->collection( $authkeys, new AuthKeyTransformer );

	}

	/**
	 * Include Users
	 *
	 * @param Discover $discover
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeUsers ( Discover $discover )
	{
		$users = $discover->user;
		return $this->collection( $users, new UserTransformer );
	}

}
