<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserUserpreferenceTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'user_userpreference', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'user_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'userpreference_id', 36 ); // Primary (Composite) Foreign Key
			$table->integer( 'value_integer' )->nullable()->default(DB::raw('NULL'));
			$table->decimal( 'value_decimal', 8, 2 )->nullable()->default(DB::raw('NULL'));
			$table->string( 'value_string', 255 )->nullable()->default(DB::raw('NULL'));
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'user_id',
				'userpreference_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_user_userpreference_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'userpreference_id', 'userpreference_id_idx' );
			$table->foreign( 'userpreference_id', 'userpreference_id_user_userpreference_foreign' )->references( 'id' )->on( 'userpreference' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'user_userpreference', function($table)
		{
			$table->dropForeign( 'user_id_user_userpreference_foreign' );
			$table->dropForeign( 'userpreference_id_user_userpreference_foreign' );
		} );

		Schema::drop( 'user_userpreference' );
	}

}
