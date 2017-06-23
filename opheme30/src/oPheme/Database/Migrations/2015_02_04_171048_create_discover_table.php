<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscoverTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discover', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->string( 'name', 255 );
			$table->double( 'latitude', 10, 6 );
			$table->double( 'longitude', 10, 6 );
			$table->double( 'radius', 8, 3 );
			$table->dateTime( 'start_date' )->nullable();
			$table->dateTime( 'end_date' )->nullable();
			$table->tinyInteger( 'running' )->unsigned()->default( 1 ); // Razvan
			$table->dateTime( 'last_check' )->nullable(); // Razvan backend support
			$table->string( 'since_id' )->default( '0' ); // Razvan backend support
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::drop( 'discover' );
	}

}
