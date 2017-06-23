<?php

namespace oPheme\Models;

use Carbon\Carbon;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User	extends BaseModel
	implements UserInterface, RemindableInterface
{

	use UserTrait,
	 RemindableTrait,
	 SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'company' => 'belongsTo',
		'userExtra' => 'hasOne',
		'authKey' => 'belongsToMany',
		'team' => 'belongsToMany',
		'userFollow' => 'hasMany',
		'autoReplyAuthKey' => 'hasMany',
		'companyPermissionGroup' => 'belongsToMany',
		'discover' => 'belongsToMany',
		'userPreference' => 'belongsToMany',
	);
	
	/**
	 * The list of validation rules
	 * @var array $validationRules 
	 */
	protected $validationRules = array(
		'email' => [ 'email', 'unique:user,email' ],
		'suspended' => [ 'boolean' ],
	);
	
	/**
	 * Excludes the row with the given id from the email unique validation
	 * allowing the current user to pass their id in
	 * @param id $userId
	 */
	public function excludeIdFromEmailValidation($userId)
	{
		$this->validationRules['email'] = [ 'sometimes', 'required', 'email', 'unique:user,email,'.$userId ] ;
	}
	/**
	 * Company relationship.
	 * @return array
	 */
	public function company ()
	{
		return $this->{$this->relationshipTypes['company']}( 'oPheme\Models\Company', 'company_id' );
	}
	
	/**
	 * UserExtra relationship.
	 * @return array
	 */
	public function userExtra ()
	{
		return $this->{$this->relationshipTypes['userExtra']}( 'oPheme\Models\UserExtra', 'user_id' );
	}

	/**
	 * AuthKey relationship.
	 * @return array
	 */
	public function authKey ()
	{
		return $this->{$this->relationshipTypes['authKey']}( 'oPheme\Models\AuthKey', 'authkey_user', 'user_id', 'authkey_id' )->withTimestamps();
	}
	
	/**
	 * Team relationship.
	 * @return array
	 */
	public function team ()
	{
		return $this->{$this->relationshipTypes['team']}( 'oPheme\Models\Team', 'team_user', 'user_id', 'team_id' );
	}
	
	/**
	 * UserFollow relationship.
	 * @return array
	 */
	public function userFollow ()
	{
		return $this->{$this->relationshipTypes['userFollow']}( 'oPheme\Models\UserFollow', 'user_id' );
	}
	
	/**
	 * AutoReplyAuthKey relationship.
	 * @return array
	 */
	public function autoReplyAuthKey ()
	{
		return $this->{$this->relationshipTypes['autoReplyAuthKey']}( 'oPheme\Models\AutoReplyAuthKey', 'user_id' );
	}
	
	/**
	 * CompanyPermissionGroup relationship.
	 * @return array
	 */
	public function companyPermissionGroup ()
	{
		return $this->{$this->relationshipTypes['companyPermissionGroup']}( 'oPheme\Models\CompanyPermissionGroup', 'companypermissiongroup_user', 'user_id', 'companypermissiongroup_id' )->withTimestamps();
	}
	
	/**
	 * Discover relationship.
	 * @return array
	 */
	public function discover ()
	{
		return $this->{$this->relationshipTypes['discover']}( 'oPheme\Models\Discover', 'discover_user', 'user_id', 'discover_id' )->withTimestamps();
	}
	
	/**
	 * UserPreference relationship.
	 * @return array
	 */
	public function userPreference ()
	{
		return $this->{$this->relationshipTypes['userPreference']}( 'oPheme\Models\UserPreference', 'user_userpreference', 'user_id', 'userpreference_id' )->withPivot('value_integer','value_double','value_string')->withTimestamps();
	}
	
	public function setLastLogin()
	{
		$this->userExtra->last_login = Carbon::now();
		$this->userExtra->save();
	}
	public function getHasAuthKeysAttribute ()
	{
		return ( $this->authKey->count() > 0 );
	}

	public function getIsAdminAttribute ()
	{
		return true;
	}

	public function getIsResellerAttribute ()
	{
		return true;
	}

	public function getIsSuspendedAttribute ()
	{
		return ( $this->userExtra->first()->suspended == 1 );
	}

}
