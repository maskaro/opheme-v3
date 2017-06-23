<?php

namespace oPheme\Transformer;

use oPheme\Models\AuthKey;

class AuthKeyTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'authkey';
	
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
	];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [
	];

	/**
	 * 	Preforms the parent's constructor
	 */
	public function __construct ()
	{
		parent::__construct();
	}

	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( AuthKey $authKey )
	{
		$transform = [
			'id'							 => $authKey->id,
			'social_media_platform_name'	 => $authKey->socialMediaPlatform->name,
			'screen_name'					 => $authKey->screen_name,
			'valid'							 => (bool) $authKey->valid,
			'created_at'					 => (string) $authKey->created_at,
			'updated_at'					 => (string) $authKey->updated_at
		];

		$transform = $this->removeDisallowedItems($transform);

		// Add links on
		$transform[ 'links' ] = [
			[
				'rel'	 => 'self',
				'uri'	 => '/authkeys/'.$authKey->id,
			],
		];
		return $transform;
	}
}
