<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscoverautoreplyTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discoverautoreply', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'discover_id', 36 ); // Primary Foreign Key
			$table->string( 'message', 255 );
			$table->string( 'url', 255 )->nullable();
			$table->double( 'minimum_kluat_score', 6, 2 )->nullable();
			$table->integer( 'hourly_limit' )->default( 6 );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Primary Key (Indexing)
			$table->primary( 'discover_id' );

			// Foreign Keys (Indexing / Linking)
			$table->foreign( 'discover_id', 'discover_id_discoverautoreply_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discoverautoreply', function($table)
		{
			$table->dropForeign( 'discover_id_discoverautoreply_foreign' );
		} );

		Schema::drop( 'discoverautoreply' );
	}

}
