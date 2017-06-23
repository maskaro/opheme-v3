<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateAuthkeyDiscoverTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'authkey_discover', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'authkey_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'discover_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'authkey_id',
				'discover_id',
				 ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_authkey_discover_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
			
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_authkey_discover_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'authkey_discover', function($table)
		{
			$table->dropForeign( 'authkey_id_authkey_discover_foreign' );
			$table->dropForeign( 'discover_id_authkey_discover_foreign' );
		} );

		Schema::drop( 'authkey_discover' );
	}

}
