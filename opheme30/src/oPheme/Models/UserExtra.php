<?php

namespace oPheme\Models;

class UserExtra	extends BaseModel
{	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'userextra';

	/**
	 * Table Primary Key override
	 * @var string 
	 */
	protected $primaryKey = 'user_id';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'user' => 'belongsTo',
	);
	
	/**
	 * Stops the model trying to update created_at / modified_at columns
	 * 
	 * @var boolean 
	 */
	public $timestamps = false;
	
	// everytime anything in userextra is update, user timestamp updated
	protected $touches = array('user');

	/**
	 * The list of validation rules
	 * @var array $validationRules 
	 */
	protected $validationRules = array(
		'first_name' => [ 'string' ],
		'last_name' => [ 'string' ],
		'phone' => [ 'string' ],
	);
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'user_id' );
	}

	public function getIsActiveAttribute ()
	{
		return ( $this->email_confirmation_token === null );
	}

}
