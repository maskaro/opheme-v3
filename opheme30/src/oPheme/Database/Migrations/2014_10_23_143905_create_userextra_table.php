<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserextraTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'userextra', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'user_id', 36 ); // Primary Foreign Key
			$table->string( 'first_name', 128 )->nullable();
			$table->string( 'last_name', 128 )->nullable();
			$table->string( 'phone', 20 )->nullable();
			$table->string( 'email_confirmation_token', 255 )->nullable();
			$table->timestamp( 'last_login' )->nullable()->default( DB::raw( 'NULL' ) );

			//Primary Key (Indexing)
			$table->primary( 'user_id' );

			// Foreign Keys (Indexing / Linking)
			$table->foreign( 'user_id', 'user_id_userextra_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'userextra', function($table)
		{
			$table->dropForeign( 'user_id_userextra_foreign' );
		} );

		Schema::drop( 'userextra' );
	}

}
