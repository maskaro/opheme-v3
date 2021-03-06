<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateFavoriteTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'favorite', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'authkey_id', 36 ); // Primary (Composite) Foreign Key
			$table->string( 'socialmediaplatform_message_id', 255 ); // Primary (Composite)

			// Foreign Keys (Indexing / Linking)
			$table->index( 'authkey_id', 'authkey_id_idx' );
			$table->foreign( 'authkey_id', 'authkey_id_favorite_foreign' )->references( 'id' )->on( 'authkey' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			// Index for faster searching
			$table->primary( array('authkey_id', 'socialmediaplatform_message_id') );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table( 'favorite', function($table)
		{
			$table->dropForeign( 'authkey_id_favorite_foreign' );
		} );

		Schema::drop( 'favorite' );
	}

}
