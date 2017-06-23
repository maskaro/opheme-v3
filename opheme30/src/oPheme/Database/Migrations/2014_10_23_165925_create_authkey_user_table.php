<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateAuthkeyUserTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'authkey_user', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'authkey_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'user_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			//Primary Key (Indexing)
			$table->primary( array(
				'user_id',
				'authkey_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_authkey_user_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_authkey_user_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'authkey_user', function($table)
		{
			$table->dropForeign( 'authkey_id_authkey_user_foreign' );
			$table->dropForeign( 'user_id_authkey_user_foreign' );
		} );

		Schema::drop( 'authkey_user' );
	}

}
