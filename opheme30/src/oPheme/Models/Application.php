<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Application extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'application';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'userPreference' => 'hasMany',
		'applicationKey' => 'hasMany',
		'user' => 'belongsToMany',
	);
	
	/**
	 * UserPreference relationship.
	 * @return array
	 */
	public function userPreference ()
	{
		return $this->{$this->relationshipTypes['userPreference']}( 'oPheme\Models\UserPreference', 'application_id' );
	}
	
	/**
	 * ApplicationKey relationship.
	 * @return array
	 */
	public function applicationKey ()
	{
		return $this->{$this->relationshipTypes['applicationKey']}( 'oPheme\Models\ApplicationKey', 'application_id' );
	}
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'application_user', 'application_id', 'user_id' )->withPivot('user_api_key')->withTimestamps();	
	}

}
