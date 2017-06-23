<?php

namespace oPheme\Models;

class DiscoverDay extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'discoverday';
	
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
	 * The list of validation rules
	 * @var array $validationRules 
	 */
	protected $validationRules = array(
		'day' => [ 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday' ],
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
