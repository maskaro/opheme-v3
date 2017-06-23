<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CompanyPermission extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'companypermission';
	
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
		'companyPermissionGroup' => 'belongsToMany',
		'permission' => 'belongsTo',
	);
	
	/**
	 * Company relationship.
	 * @return array
	 */
	public function company ()
	{
		return $this->{$this->relationshipTypes['company']}( 'oPheme\Models\Company', 'company_id' );
	}
	
	/**
	 * CompanyPermissionGroup relationship.
	 * @return array
	 */
	public function companyPermissionGroup ()
	{
		return $this->{$this->relationshipTypes['companyPermissionGroup']}( 'oPheme\Models\CompanyPermissionGroup', 'companypermission_companypermissiongroup', 'companypermission_id', 'companypermissiongroup_id' )->withTimestamps();		
	}
	
	/**
	 * Permission relationship.
	 * @return array
	 */
	public function permission ()
	{
		return $this->{$this->relationshipTypes['permission']}( 'oPheme\Models\Permission', 'permission_id' );
	}

}
