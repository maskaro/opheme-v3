<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreatePermissionTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'permission', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'permissiongroup_id', 36 ); // Foreign Key
			$table->string( 'name', 255 );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();
			
			// Foreign Keys (Indexing / Linking)
			$table->index( 'permissiongroup_id', 'permissiongroup_id_idx' );
			$table->foreign( 'permissiongroup_id', 'permissiongroup_id_permission_foreign' )->references( 'id' )->on( 'permissiongroup' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'permission', function($table)
		{
			$table->dropForeign( 'permissiongroup_id_permission_foreign' );
		} );
		
		Schema::drop( 'permission' );
	}

}
