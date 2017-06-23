<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;

class DevelopmentDatabaseSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run ()
	{

		Eloquent::unguard();

		// Disable Foreign key check for this connection before running seeders
		//DB::statement( 'SET FOREIGN_KEY_CHECKS=0;' );
		
		// Base Seeds
		$this->call( 'oPheme\\Database\\Seeds\\Base\\SubscriptionTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\CompanyTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\UserTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\SocialMediaPlatformTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\AuthKeyTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\PermissionTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Base\\OAuthServerSeeder' );
		
		// Development Seeds
		$this->call( 'oPheme\\Database\\Seeds\\Development\\CompanyTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Development\\UserTableSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Development\\DiscoverSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Development\\DiscoverMessageSeeder' );
		$this->call( 'oPheme\\Database\\Seeds\\Development\\InteractionMessageSeeder' );
		
		// Re-enable Foreign key checks
		//DB::statement( 'SET FOREIGN_KEY_CHECKS=1;' );
	}

}
