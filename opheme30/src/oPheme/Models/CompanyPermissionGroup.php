<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CompanyPermissionGroup extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'companypermissiongroup';
	
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
		'companyPermissionGroup' => 'belongsToMany',
	);
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'companypermissiongroup_user', 'companypermissiongroup_id', 'user_id' )->withTimestamps();
	}
	
	/**
	 * CompanyPermission relationship.
	 * @return array
	 */
	public function companyPermission ()
	{
		return $this->{$this->relationshipTypes['companyPermission']}( 'oPheme\Models\CompanyPermission', 'companypermission_companypermissiongroup', 'companypermissiongroup_id', 'companypermission_id' )->withTimestamps();	
	}

}
