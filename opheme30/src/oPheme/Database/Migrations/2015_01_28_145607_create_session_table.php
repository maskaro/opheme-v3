<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateSessionTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'sessions', function(Blueprint $t)
		{
			$t->string( 'id' )->unique();
			$t->text( 'payload' );
			$t->integer( 'last_activity' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::drop( 'sessions' );
	}

}
