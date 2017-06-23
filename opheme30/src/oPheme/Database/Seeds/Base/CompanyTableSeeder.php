<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\Company;
use oPheme\Models\Subscription;

class CompanyTableSeeder extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		// All existing companies are deleted !!!
		//DB::table('Company')->delete();
		//DB::table( 'Company' )->truncate();
		
		// Get the subscription id
		$subscription = Subscription::where( "name", "=", "Pro" )->first();
		
		// add company using Eloquent
		$company1					 = new Company;
		$company1->subscription_id	 = $subscription->id;
		$company1->name				 = 'Opheme Limited';
		$company1->location			 = '19 Pakenham Close, Cambridge, Cambridgeshire, CB4 1PW, United Kingdom';
		$company1->url				 = 'http://opheme.com';
		$company1->phone			 = '1234567890';
		$company1->save();

//		$company2					 = new Company;
//		$company2->subscription_id	 = 1;
//		$company2->name				 = 'Social Media Frontiers Limited SLA';
//		$company2->location			 = 'Somewhere on Earth';
//		$company2->url				 = 'http://twadar.net';
//		$company2->phone			 = '1234567890';
//		$company2->save();
	}

}
