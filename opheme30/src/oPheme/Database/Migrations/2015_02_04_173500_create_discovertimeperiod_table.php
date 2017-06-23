<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscovertimeperiodTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discovertimeperiod', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key 
			$table->char( 'discover_id', 36 ); // Foreign Key
			$table->time( 'start' );
			$table->time( 'end' );
			
			// Foreign Keys (Indexing / Linking)
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_discovertimeperiod_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			// Unique composite key
			$table->unique( array('discover_id', 'start', 'end') );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discovertimeperiod', function($table)
		{
			$table->dropForeign( 'discover_id_discovertimeperiod_foreign' );
		} );

		Schema::drop( 'discovertimeperiod' );
	}

}
