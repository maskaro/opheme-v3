<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscovermessageTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discovermessage', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'discover_id', 36 ); // Foreign Key
			$table->char( 'backend_message_id', 36 );
			$table->char( 'socialmediaplatform_id', 36 ); // Foreign Key
			$table->string( 'socialmediaplatform_message_id', 255 );
			$table->dateTime( 'message_datestamp' );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking)
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_discovermessage_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'socialmediaplatform_id', 'socialmediaplatform_id_idx' );
			$table->foreign( 'socialmediaplatform_id', 'socialmediaplatform_id_discovermessage_foreign' )->references( 'id' )->on( 'socialmediaplatform' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		
			// Index for faster searching
			$table->index( 'socialmediaplatform_message_id', 'socialmediaplatform_message_id_idx' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discovermessage', function($table)
		{
			$table->dropForeign( 'discover_id_discovermessage_foreign' );
			$table->dropForeign( 'socialmediaplatform_id_discovermessage_foreign' );
		} );

		Schema::drop( 'discovermessage' );
	}

}
