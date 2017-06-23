<?php

namespace oPheme\Database\Seeds\Development;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\Company;
use oPheme\Models\Subscription;
use Faker;

class CompanyTableSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();
		
		$faker = Faker\Factory::create( 'en_GB' ); // create an English faker
		
		$totalToSeed = 10;
		$this->command->info($totalToSeed . ' fake companies being seeded, Please wait');
		
		for ( $i = 1; $i <= $totalToSeed; $i++ )
		{
			// add fake company using Eloquent
			$company					 = new Company;
			$company->subscription_id	 = Subscription::where( "name", "=", "Pro" )->first()->id; // Standard Subscription
			$company->name				 = $faker->company;
			$company->location			 = $faker->address;
			$company->url				 = $faker->optional(0.7)->url; // 30% chance of NULL (70% chance of values)
			$company->phone				 = $faker->phoneNumber;
			$company->save();
		}
	}

}
