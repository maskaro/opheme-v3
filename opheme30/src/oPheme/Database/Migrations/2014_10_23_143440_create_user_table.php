<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'user', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'company_id', 36 ); // Foreign Key
			$table->string( 'password', 60 );
			$table->string( 'email', 255 );
			$table->tinyInteger( 'suspended' )->unsigned()->default( 0 );
			$table->rememberToken();
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking) 
			$table->index( 'company_id', 'company_id_idx' );
			$table->foreign( 'company_id', 'company_id_user_foreign' )->references( 'id' )->on( 'company' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			// Unique Keys (Indexing / No Duplicates)
			$table->unique( 'email' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'user', function($table)
		{
			$table->dropForeign( 'company_id_user_foreign' );
		} );

		Schema::drop( 'user' );
	}

}
