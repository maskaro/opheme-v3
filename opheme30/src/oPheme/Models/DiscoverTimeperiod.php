<?php

namespace oPheme\Models;

class DiscoverTimeperiod extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'discovertimeperiod';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'discover' => 'belongsTo',
	);
	
	/**
	 * Stops the model trying to update created_at / modified_at columns
	 * 
	 * @var boolean 
	 */
	public $timestamps = false;
		
	/**
	 * Discover relationship.
	 * @return array
	 */
	public function discover ()
	{
		return $this->{$this->relationshipTypes['discover']}( 'oPheme\Models\Discover', 'discover_id', 'id' );	
	}

}
