<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\Permission;
use oPheme\Models\PermissionGroup;

class PermissionTableSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		// add user using Eloquent
		$permissionGroup		 = new PermissionGroup;
		$permissionGroup->name	 = 'Discover';
		$permissionGroup->save();
		
		$permission						 = new Permission;
		$permission->permissiongroup_id	 = $permissionGroup->id;
		$permission->name				 = 'View';
		$permission->save();
		
		$permission						 = new Permission;
		$permission->permissiongroup_id	 = $permissionGroup->id;
		$permission->name				 = 'Create';
		$permission->save();
		
		$permission						 = new Permission;
		$permission->permissiongroup_id	 = $permissionGroup->id;
		$permission->name				 = 'Edit';
		$permission->save();
		
		$permission						 = new Permission;
		$permission->permissiongroup_id	 = $permissionGroup->id;
		$permission->name				 = 'Delete';
		$permission->save();
	}

}
