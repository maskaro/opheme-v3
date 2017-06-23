<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PermissionGroup extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissiongroup';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'permission' => 'hasMany',
	);
	
	/**
	 * Permission relationship.
	 * @return array
	 */
	public function permission ()
	{
		return $this->{$this->relationshipTypes['permission']}( 'oPheme\Models\Permission', 'permissiongroup_id' );
	}

}
