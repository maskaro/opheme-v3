<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateCompanypermissiongroupUserTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'companypermissiongroup_user', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns

			$table->char( 'companypermissiongroup_id', 36 ); // Foreign Key
			$table->char( 'user_id', 36 ); // Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();


			// Composite Primary Key (Indexing / Linking)
			$table->primary( array(
				'companypermissiongroup_id',
				'user_id' ), '' ); // pass no name as composite primary keys don't require one
			//
			// Foreign Keys (Indexing / Linking)
			$table->index( 'companypermissiongroup_id', 'companypermissiongroup_id_idx' );
			$table->foreign( 'companypermissiongroup_id', 'companypermissiongroup_id_companypermissiongroup_user_foreign' )->references( 'id' )->on( 'companypermissiongroup' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_companypermissiongroup_user_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'companypermissiongroup_user', function($table)
		{
			$table->dropForeign( 'companypermissiongroup_id_companypermissiongroup_user_foreign' );
			$table->dropForeign( 'user_id_companypermissiongroup_user_foreign' );
		} );

		Schema::drop( 'companypermissiongroup_user' );
	}

}
