<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionSubscriptionelementTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'subscription_subscriptionelement', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'subscription_id', 36 ); // Primary (Composite) Foreign Key
			$table->char( 'subscriptionelement_id', 36 ); // Primary (Composite) Foreign Key
			$table->integer( 'value_integer' )->nullable()->default(DB::raw('NULL'));
			$table->decimal( 'value_decimal', 8, 2 )->nullable()->default(DB::raw('NULL'));
			$table->string( 'value_string', 255 )->nullable()->default(DB::raw('NULL'));
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();

			// Composite Primary Key (Indexing)
			$table->primary( array(
				'subscription_id',
				'subscriptionelement_id' ), '' ); // pass no name as composite primary keys don't require one
			// Foreign Keys (Indexing / Linking)
			$table->index( 'subscription_id', 'subscription_id_idx' );
			$table->foreign( 'subscription_id', 'subscription_id_subscription_subscriptionelement_foreign' )->references( 'id' )->on( 'subscription' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'subscriptionelement_id', 'subscriptionelement_id_idx' );
			$table->foreign( 'subscriptionelement_id', 'subscriptionelement_id_subscription_subscriptionelement_foreign' )->references( 'id' )->on( 'subscriptionelement' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'subscription_subscriptionelement', function($table)
		{
			$table->dropForeign( 'subscription_id_subscription_subscriptionelement_foreign' );
			$table->dropForeign( 'subscriptionelement_id_subscription_subscriptionelement_foreign' );
		} );

		Schema::drop( 'subscription_subscriptionelement' );
	}

}
