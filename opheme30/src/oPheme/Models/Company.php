<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Company extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'company';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'user' => 'hasMany',
		'team' => 'hasMany',
		'subscription' => 'belongsTo',
		'autoReplyAuthKey' => 'hasMany',
		'registrationToken' => 'hasMany',
		'companyPermission' => 'hasMany',
	);
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'company_id', 'id' );
	}
	
	/**
	 * Team relationship.
	 * @return array
	 */
	public function team ()
	{
		return $this->{$this->relationshipTypes['team']}( 'oPheme\Models\Team', 'company_id', 'id' );
	}

	/**
	 * Subscription relationship.
	 * @return array
	 */
	public function subscription ()
	{
		return $this->{$this->relationshipTypes['subscription']}( 'oPheme\Models\Subscription', 'subscription_id' );
	}
	
	/**
	 * AutoReplyAuthKey relationship.
	 * @return array
	 */
	public function autoReplyAuthKey ()
	{
		return $this->{$this->relationshipTypes['autoReplyAuthKey']}( 'oPheme\Models\AutoReplyAuthKey', 'company_id' );
	}
	
	/**
	 * RegistrationToken relationship.
	 * @return array
	 */
	public function registrationToken ()
	{
		return $this->{$this->relationshipTypes['registrationToken']}( 'oPheme\Models\RegistrationToken', 'company_id' );
	}
	
	/**
	 * CompanyPermission relationship.
	 * @return array
	 */
	public function companyPermission ()
	{
		return $this->{$this->relationshipTypes['companyPermission']}( 'oPheme\Models\CompanyPermission', 'company_id' );
	}
}
