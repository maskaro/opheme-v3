<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateAutoreplyauthkeyTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'autoreplyauthkey', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'authkey_id', 36 ); // Foreign Key
			$table->char( 'company_id', 36 ); // Foreign Key
			$table->char( 'user_id', 36 ); // Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking)
			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_autoreplyauthkey_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'company_id', 'company_id_idx' );
			$table->foreign( 'company_id', 'company_id_autoreplyauthkey_foreign' )->references( 'id' )->on( 'company' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_autoreplyauthkey_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'autoreplyauthkey', function($table)
		{
			$table->dropForeign( 'authkey_id_autoreplyauthkey_foreign' );
			$table->dropForeign( 'company_id_autoreplyauthkey_foreign' );
			$table->dropForeign( 'user_id_autoreplyauthkey_foreign' );
		} );

		Schema::drop( 'autoreplyauthkey' );
	}

}
