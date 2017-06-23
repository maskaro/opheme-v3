<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTeamUserTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'team_user', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'team_id', 36 ); // 
			$table->char( 'user_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'team_id',
				'user_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'team_id', 'team_id_idx' );
			$table->foreign( 'team_id', 'team_id_team_user_foreign' )->references( 'id' )->on( 'team' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'user_id', 'user_id_idx' );
			$table->foreign( 'user_id', 'user_id_team_user_foreign' )->references( 'id' )->on( 'user' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'team_user', function($table)
		{
			$table->dropForeign( 'team_id_team_user_foreign' );
			$table->dropForeign( 'user_id_team_user_foreign' );
		} );

		Schema::drop( 'team_user' );
	}

}
