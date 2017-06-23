<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateCompanyTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'company', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'subscription_id', 36 ); // Foreign Key
			$table->string( 'name', 255 );
			$table->string( 'location', 255 );
			$table->string( 'url', 255 )->nullable();
			$table->string( 'phone', 20 );
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys (Indexing / Linking)
			$table->index( 'subscription_id', 'subscription_id_idx' );
			$table->foreign( 'subscription_id', 'subscription_id_company_foreign' )->references( 'id' )->on( 'subscription' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'company', function($table)
		{
			$table->dropForeign( 'subscription_id_company_foreign' );
		} );

		Schema::drop( 'company' );
	}

}
