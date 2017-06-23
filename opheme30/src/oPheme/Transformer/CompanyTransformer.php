<?php

namespace oPheme\Transformer;

use oPheme\Models\Company;

class CompanyTransformer
	extends BaseTransformer
{

	// Overriding variable in parent
	protected $type = 'company';
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'users'
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
	public function transform ( Company $company )
	{
		return [
			'id'			 => $company->id,
			'name'			 => $company->name,
			'location'		 => $company->location,
			'url'			 => $company->url,
			'phone'		 => $company->phone,
			'created_at'	 => (string) $company->created_at,
			// Always have links allowed
			 'links'  => [
				[
					'rel'	 => 'self',
					'uri'	 => '/companies',
				],
			],
		];
	}

	/**
	 * Include Users
	 *
	 * @param Company $company
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeUsers ( Company $company )
	{
		$users = $company->users;

		return $this->collection( $users, new UserTransformer );
	}
	
	// TODO: Permission check on Client (OAuth application) and User 
	protected function isIncludeAllowed ( $include )
	{
		return true;
	}

}
