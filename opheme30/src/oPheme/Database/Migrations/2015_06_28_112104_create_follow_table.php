<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateFollowTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'follow', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'authkey_id', 36 ); // Primary (Composite) Foreign Key
			$table->string( 'screen_name', 255 ); // Primary (Composite)

			// Foreign Keys (Indexing / Linking)
			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_follow_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			// Index for faster searching
			$table->primary( array('authkey_id', 'screen_name') );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table( 'follow', function($table)
		{
			$table->dropForeign( 'authkey_id_follow_foreign' );
		} );

		Schema::drop( 'follow' );
	}

}
