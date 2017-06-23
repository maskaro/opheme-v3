<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscoverdayTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discoverday', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key 
			$table->char( 'discover_id', 36 ); // Foreign Key
			$table->enum('day', array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') );
			
			// Foreign Keys (Indexing / Linking)
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_discoverday_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
			
			// Unique composite key
			$table->unique( array('discover_id','day') );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discoverday', function($table)
		{
			$table->dropForeign( 'discover_id_discoverday_foreign' );
		} );

		Schema::drop( 'discoverday' );
	}

}
