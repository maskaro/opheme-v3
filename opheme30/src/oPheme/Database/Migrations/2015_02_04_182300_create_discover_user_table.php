<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscoverUserTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discover_user', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'discover_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'user_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'discover_id',
				'user_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_discover_user_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'user_id', 'user_id' );
			$table->foreign( 'user_id', 'user_id_discover_user_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discover_user', function($table)
		{
			$table->dropForeign( 'discover_id_discover_user_foreign' );
			$table->dropForeign( 'user_id_discover_user_foreign' );
		} );

		Schema::drop( 'discover_user' );
	}

}
