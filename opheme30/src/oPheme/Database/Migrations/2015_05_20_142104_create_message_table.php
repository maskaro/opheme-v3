<?php

use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration {

	/**
	 * The name of the database connection to use.
	 *
	 * @var string
	 */
	protected $connection = 'mongodb';
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection($this->connection)->create('message', function($collection)
		{
			$collection->index('_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection($this->connection)->drop( 'message' );
	}

}
