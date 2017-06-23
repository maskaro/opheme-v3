<?php

namespace oPheme\Models;

class Follow extends BaseModel
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'follow';
	
	/**
	 * Table Primary Key override
	 * @var string 
	 */
	protected $primaryKey = array('authkey_id', 'screen_name');
	
	/**
	 * Stops the model trying to update created_at / modified_at columns
	 * 
	 * @var boolean 
	 */
	public $timestamps = false;
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'authKey' => 'belongsTo',
	);
	
	/**
	 * AuthKey relationship.
	 * @return array
	 */
	public function authKey ()
	{
		return $this->{$this->relationshipTypes['authKey']}( 'oPheme\Models\AuthKey', 'authkey_id' );
	}
}
