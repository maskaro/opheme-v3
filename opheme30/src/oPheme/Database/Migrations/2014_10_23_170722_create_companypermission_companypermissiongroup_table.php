<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateCompanypermissionCompanypermissiongroupTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'companypermission_companypermissiongroup', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'companypermission_id', 36 ); // Foreign Key
			$table->char( 'companypermissiongroup_id', 36 ); // Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing / Linking)
			$table->primary( array(
				'companypermission_id',
				'companypermissiongroup_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'companypermission_id', 'companypermission_id_idx' );
			$table->foreign( 'companypermission_id', 'companypermission_id_compperm_comppermgrp_foreign' )->references( 'id' )->on( 'companypermission' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'companypermissiongroup_id', 'companypermissiongroup_id_idx' );
			$table->foreign( 'companypermissiongroup_id', 'companypermissiongroup_id_compperm_comppermgrp_foreign' )->references( 'id' )->on( 'companypermissiongroup' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'companypermission_companypermissiongroup', function($table)
		{
			$table->dropForeign( 'companypermission_id_compperm_comppermgrp_foreign' );
			$table->dropForeign( 'companypermissiongroup_id_compperm_comppermgrp_foreign' );
		} );

		Schema::drop( 'companypermission_companypermissiongroup' );
	}

}
