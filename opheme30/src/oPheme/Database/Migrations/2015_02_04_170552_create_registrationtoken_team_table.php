<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateRegistrationtokenTeamTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'registrationtoken_team', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'registrationtoken_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'team_id', 36 ); // Primary (Composite) Foreign Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'registrationtoken_id',
				'team_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'registrationtoken_id', 'registrationtoken_id_idx' );
			$table->foreign( 'registrationtoken_id', 'registrationtoken_id_registrationtoken_team_foreign' )->references( 'id' )->on( 'registrationtoken' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'team_id', 'team_id_idx' );
			$table->foreign( 'team_id', 'team_id_registrationtoken_team_foreign' )->references( 'id' )->on( 'team' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'registrationtoken_team', function($table)
		{
			$table->dropForeign( 'registrationtoken_id_registrationtoken_team_foreign' );
			$table->dropForeign( 'team_id_registrationtoken_team_foreign' );
		} );

		Schema::drop( 'registrationtoken_team' );
	}

}
