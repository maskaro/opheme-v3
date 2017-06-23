<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateInteractionmessageTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'interactionmessage', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'interaction_id', 36 ); // Foreign Key
			$table->char( 'backend_message_id', 36 );
			$table->char( 'socialmediaplatform_id', 36 ); // Foreign Key
			$table->string( 'socialmediaplatform_message_id', 255 )->nullable()->default(DB::raw('NULL'));
			$table->string( 'socialmediaplatform_user_id', 255 );
			$table->string( 'socialmediaplatform_user_screen_name', 255 );
			$table->dateTime( 'message_datestamp' );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking)
			$table->index( 'interaction_id', 'interaction_id_idx' );
			$table->foreign( 'interaction_id', 'interaction_id_interactionmessage_foreign' )->references( 'id' )->on( 'interaction' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		
			$table->index( 'socialmediaplatform_id', 'socialmediaplatform_id_idx' );
			$table->foreign( 'socialmediaplatform_id', 'socialmediaplatform_id_interactionmessage_foreign' )->references( 'id' )->on( 'socialmediaplatform' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
			
			// Index for faster searching
			$table->index( 'socialmediaplatform_message_id', 'socialmediaplatform_message_id_idx' );
			$table->index( 'backend_message_id', 'backend_message_id_idx' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'interactionmessage', function($table)
		{
			$table->dropForeign( 'interaction_id_interactionmessage_foreign' );
			$table->dropForeign( 'socialmediaplatform_id_interactionmessage_foreign' );
		} );

		Schema::drop( 'interactionmessage' );
	}

}
