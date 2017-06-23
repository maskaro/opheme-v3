<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserfollowTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'userfollow', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'user_id', 36 ); // Foreign Key
			$table->char( 'authkey_id', 36 ); // Foreign Key
			$table->string( 'followed_user_handle', 255 );
			$table->tinyInteger( 'followed_back' )->unsigned()->default( 0 );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			//Primary Key (Indexing)
			// Composite Primary Key (Indexing / Linking)
			$table->primary( array(
				'user_id',
				'authkey_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_userfollow_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_userfollow_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'userfollow', function($table)
		{
			$table->dropForeign( 'user_id_userfollow_foreign' );
			$table->dropForeign( 'authkey_id_userfollow_foreign' );
		} );

		Schema::drop( 'userfollow' );
	}

}
