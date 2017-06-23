<?php

namespace oPheme\Transformer;

use oPheme\Models\User;

class UserTransformer
	extends BaseTransformer
{

	// Overriding variable in parent
	protected $type = 'user';

	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'companies'
	];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [
	];

	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( User $user )
	{
		$transform = [
			'id'		 => $user->id,
			'email'		 => $user->email,
			'suspended'	 => (bool) $user->suspended,
			'first_name' => $user->userExtra->first_name,
			'last_name'	 => $user->userExtra->last_name,
			'phone'		 => $user->userExtra->phone,
			'last_login' => is_null( $user->userExtra->last_login ) ? null : (string) $user->userExtra->last_login,
			'created_at' => (string) $user->created_at,
		];
		
		$transform = $this->removeDisallowedItems($transform);

		// Add links on
		$transform[ 'links' ] = [
			[
				'rel'	 => 'self',
				'uri'	 => '/me',
			],
		];
		return $transform;
	}

	/**
	 * Include Company
	 *
	 * @param User $user
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeCompanies ( User $user )
	{
		$company = $user->company;

		return $this->item( $company, new CompanyTransformer );
	}

}
