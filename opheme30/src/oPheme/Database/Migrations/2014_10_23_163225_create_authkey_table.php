<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateAuthkeyTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'authkey', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'socialmediaplatform_id', 36 ); // Foreign Key
			$table->string( 'screen_name', 255 );
			$table->string( 'token', 255 );
			$table->string( 'token_secret', 255 )->nullable()->default( DB::raw( 'NULL' ) );
			$table->tinyInteger( 'valid' )->unsigned()->default( 1 );
			$table->timestamp( 'last_validity_check' )->nullable()->default( DB::raw( 'NULL' ) );
			$table->timestamp( 'last_interaction_check' )->nullable()->default( DB::raw( 'NULL' ) );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking)
			$table->index( 'socialmediaplatform_id', 'socialmediaplatform_id_idx' );
			$table->foreign( 'socialmediaplatform_id', 'socialmediaplatform_id_authkey_foreign' )->references( 'id' )->on( 'socialmediaplatform' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
			
			// Unique Key
			$table->unique( array( 'socialmediaplatform_id', 'screen_name', 'token' ) );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'authkey', function($table)
		{
			$table->dropForeign( 'socialmediaplatform_id_authkey_foreign' );
		} );

		Schema::drop( 'authkey' );
	}

}
