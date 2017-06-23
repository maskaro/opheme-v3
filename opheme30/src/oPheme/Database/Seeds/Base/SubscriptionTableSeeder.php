<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\Subscription;
use oPheme\Models\SubscriptionElement;

class SubscriptionTableSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		// All existing subscriptions are deleted !!!
		//DB::table('Subscription')->delete();
		//DB::table( 'Subscription' )->truncate();
		// Add subscription using Eloquent
		$sub		 = new Subscription;
		$sub->name	 = 'Pro';
		$sub->save();

		// Add a new Subscription Element
		$subElement				 = new SubscriptionElement;
		$subElement->name		 = 'price';
		$subElement->value_type	 = 'decimal';
		$subElement->save();

		// Add the Subscription Element to the Subscription in the pivot table (with data)
		$subElement->subscription()->attach( $sub->id, array(
			'value_decimal' => 9.99 ) );

		// Add a new Subscription Element
		$subElement				 = new SubscriptionElement;
		$subElement->name		 = 'job_limit';
		$subElement->value_type	 = 'integer';
		$subElement->save();

		// Add the Subscription Element to the Subscription in the pivot table (with data)
		$subElement->subscription()->attach( $sub->id, array(
			'value_integer' => 15 ) );
	}

}
