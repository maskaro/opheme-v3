<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UserPreference extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'userpreference';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'user' => 'belongsToMany',
	);
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'user_userpreference', 'userpreference_id', 'user_id' )->withPivot('value_integer','value_double','value_string')->withTimestamps();	
	}

}
