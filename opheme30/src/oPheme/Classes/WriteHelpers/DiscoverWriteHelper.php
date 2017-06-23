<?php

namespace oPheme\Classes\WriteHelpers;

class DiscoverWriteHelper
	extends BaseWriteHelper
{

	// Overriding variable in parent
	protected $type = 'discover';

	/**
	 * 	Preforms the parent's constructor
	 */
	public function __construct ($requestAction)
	{
		$this->external = [
			'running'			 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database'	 => [ 'field' => 'running' ]
			],
			'name'			 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => true,
				'database'	 => [ 'field' => 'name' ]
			],
			'latitude'		 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => true,
				'database'	 => [ 'field' => 'latitude' ]
			],
			'longitude'		 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => true,
				'database'	 => [ 'field' => 'longitude' ]
			],
			'radius'		 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => true,
				'database'	 => [ 'field' => 'radius' ]
			],
			'start_date'	 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database'	 => [ 'field' => 'start_date' ]
			],
			'end_date'		 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database'	 => [ 'field' => 'end_date' ]
			],
			'days'			 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database' => [ 'relationship' => 'discoverDay'],
				'multiple'	 => [
					'day' => [
						'database' => [ 'field' => 'day' ]
					]
				],
			],
			'timeperiods'	 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database' => [ 'relationship' => 'discoverTimeperiod' ],
				'multiple'	 => [
					'start'	 => [
						'database' => [ 'field' => 'start' ]
					],
					'end'	 => [
						'database' => [ 'field' => 'end' ]
					],
				]
			],
			'keywords'		 => [
				'create'	 => true,
				'update'	 => true,
				'required'	 => false,
				'database' => [ 'relationship' => 'keyword' ],
				'multiple'	 => [
					'keyword' => [
						'database' => [ 'field' => 'keyword' ]
					]
				],
			],
		];

		parent::__construct($requestAction);
	}
}
