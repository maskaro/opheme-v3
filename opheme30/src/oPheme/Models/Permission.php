<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Permission extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permission';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'permissionGroup' => 'belongsTo',
		'companyPermission' => 'hasMany',
	);
	
	/**
	 * PermissionGroup relationship.
	 * @return array
	 */
	public function permissionGroup ()
	{
		return $this->{$this->relationshipTypes['permissionGroup']}( 'oPheme\Models\PermissionGroup', 'permissiongroup_id' );
	}
	
	/**
	 * CompanyPermission relationship.
	 * @return array
	 */
	public function companyPermission ()
	{
		return $this->{$this->relationshipTypes['companyPermission']}( 'oPheme\Models\CompanyPermission', 'permission_id' );
	}

}
