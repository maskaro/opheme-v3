<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateDiscoverKeywordTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'discover_keyword', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'discover_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'keyword_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'discover_id',
				'keyword_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'discover_id', 'discover_id_idx' );
			$table->foreign( 'discover_id', 'discover_id_discover_keyword_foreign' )->references( 'id' )->on( 'discover' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'keyword_id', 'keyword_id' );
			$table->foreign( 'keyword_id', 'keyword_id_discover_keyword_foreign' )->references( 'id' )->on( 'keyword' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'discover_keyword', function($table)
		{
			$table->dropForeign( 'discover_id_discover_keyword_foreign' );
			$table->dropForeign( 'keyword_id_discover_keyword_foreign' );
		} );

		Schema::drop( 'discover_keyword' );
	}

}
