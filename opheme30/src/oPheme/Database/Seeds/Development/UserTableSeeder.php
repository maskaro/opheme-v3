<?php

namespace oPheme\Database\Seeds\Development;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use oPheme\Models\Company;
use oPheme\Models\User;
use oPheme\Models\UserExtra;
use Faker;

class UserTableSeeder
	extends Seeder
{

	public function run ()
	{

		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		$faker = Faker\Factory::create( 'en_GB' ); // create an English faker

		

		$totalToSeed = 100;
		$this->command->info( $totalToSeed . ' fake users being seeded, Please wait' );

		for ( $i = 1; $i <= $totalToSeed; $i++ )
		{
			// Progress bar
			if ( $i % ($totalToSeed / 10) == 0 && $i !== 0 )
			{
				$this->command->info( ($i / $totalToSeed) * 100 . "%" ); // Show pecentage completion
			}

			// add fake user using Eloquent
			$user = new User;
			if ( mt_rand( 1, 100 ) <= 70 ) // 70% chance of user being a single user (assigned to the opheme company)
			{
				$user->company_id = Company::where( 'name', '=', 'Opheme Limited' )->first()->id;
			}
			else
			{
				// get the total companies that aren't opheme limited
				$companyCount = Company::where( 'name', '!=', 'Opheme Limited' )->count();
				// get 1 company that isn't opheme limited with an offset between 0 and the count (-1)  [Any of the generated companies (Not 'Opheme Limited')]
				$user->company_id = Company::where( 'name', '!=', 'Opheme Limited' )->limit( 1 )->skip( mt_rand( 0, $companyCount - 1 ) )->first()->id;
			}
			$user->email		 = $faker->unique()->safeEmail;
			$user->password		 = Hash::make( $faker->password );
			$user->suspended	 = $faker->boolean( $chanceOfGettingTrue = 10 ) ? 1 : 0; // 10% chance of being suspended
			$user->save();

			$userExtra				 = new UserExtra;
			$userExtra->user_id		 = $user->id;
			$userExtra->first_name	 = $faker->optional( $weight					 = 0.9 )->firstName; // 10% chance of NULL (90% chance of values)
			$userExtra->last_name	 = $faker->optional( $weight					 = 0.9 )->lastName; // 10% chance of NULL (90% chance of values)
			$userExtra->phone		 = $faker->optional( $weight					 = 0.9 )->phoneNumber; // 10% chance of NULL (90% chance of values)
			if ( $user->company_id == 1 ) // Only send an email confirmation if user set themself up, not if they were added by a company
			{
				if ( $faker->boolean( $chanceOfGettingTrue = 10 ) ) // 10% chance the user hasn't verified their email
				{
					$userExtra->email_confirmation_token = $faker->regexify( '[a-z0-9]{8}' ); // create an 8 long string with a-z and 0-9
				}
			}
			else
			{
				$userExtra->email_confirmation_token = NULL;
			}

			$userExtra->last_login = $faker->optional( 0.9 )->dateTimeThisYear; // 10% chance of NULL (90% chance of values)

			$userExtra->save();
		}
	}

}
