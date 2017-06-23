<?php

namespace oPheme\Models;


class Message extends BaseMongoMoloquentModel
{		
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'message';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * Stops the model trying to update created_at / updated_at columns
	 * 
	 * @var boolean 
	 */
	public $timestamps = false;
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'discoverMessage' => 'hasMany',
	);
	
	/**
	 * DiscoverMessage relationship.
	 * @return array
	 */
	public function discoverMessage ()
	{
		return $this->{$this->relationshipTypes['discoverMessage']}( 'oPheme\Models\DiscoverMessage', 'backend_message_id', '_id' );
	}
	
	/**
	 * Overriding laravel eloquent function to not do anything special with created_at / updated_at fields
	 * http://laravel.com/docs/4.2/eloquent#date-mutators
	 */
	
	public function getDates()
	{
		// Do nothing.
		return array();
	}
}
