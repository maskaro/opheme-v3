<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OAuthServerSeeder
	extends Seeder
{

	public function run ()
	{
		// Not using Eloquent for this seeder

		$datetime = Carbon::now();

		// Seed Clients (opheme_hootsuite / opheme_webapp)
		$clients = [
			[
				'id'		 => 'opheme_hootsuite',
				'secret'	 => 'RZSdzQOqIcXVMlKJsszRLKy6Uv4M5gOOmWQPf04A',
				'name'		 => 'Opheme Hootsuite',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'id'		 => 'opheme_webapp',
				'secret'	 => 'jSw8M7pKg715I5aI9eLIGVUC8mWMFHPBsY03ljzu',
				'name'		 => 'Opheme Web Application',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
		];

		DB::table( 'oauth_clients' )->insert( $clients );

		// Seed Grants
		$grants = [
			[
				'id'		 => 'password',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'id'		 => 'password_forever',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'id'		 => 'client_credentials',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'id'		 => 'refresh_token',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
		];

		DB::table( 'oauth_grants' )->insert( $grants );

		// Seed Clients_to_Grants (allow opheme_hootsuite and opheme_webapp 
		// access to password and client_credentials grants)
		$clientGrants = [
			[
				'client_id'	 => 'opheme_hootsuite',
				'grant_id'	 => 'password_forever',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_hootsuite',
				'grant_id'	 => 'client_credentials',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_webapp',
				'grant_id'	 => 'password',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_webapp',
				'grant_id'	 => 'refresh_token',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_webapp',
				'grant_id'	 => 'client_credentials',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
		];

		DB::table( 'oauth_client_grants' )->insert( $clientGrants );

		// Seed Scopes
		$scopes = [
			[
				'id'			 => 'basic',
				'description'	 => 'Basic Scope - To read any non sensitive data',
				'created_at'	 => $datetime,
				'updated_at'	 => $datetime,
			],
			[
				'id'			 => 'inhouse',
				'description'	 => 'In House Built Applications Scope - All access pass',
				'created_at'	 => $datetime,
				'updated_at'	 => $datetime,
			],
		];

		DB::table( 'oauth_scopes' )->insert( $scopes );

		// Seed Clients_to_Scopes (allow opheme_hootsuite and opheme_webapp 
		// access to basic and inhouse scopes)

		$clientScopes = [
			[
				'client_id'	 => 'opheme_hootsuite',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_hootsuite',
				'scope_id'	 => 'inhouse',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_webapp',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'client_id'	 => 'opheme_webapp',
				'scope_id'	 => 'inhouse',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
		];

		DB::table( 'oauth_client_scopes' )->insert( $clientScopes );
		
		// Seed Grants_to_Scopes (allow client_credentials grant 
		// access to basic and inhouse scopes, and password grant
		// access to basic)
		
		$grantScopes = [
			[
				'grant_id'	 => 'client_credentials',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'grant_id'	 => 'client_credentials',
				'scope_id'	 => 'inhouse',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'grant_id'	 => 'password',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'grant_id'	 => 'password_forever',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
			[
				'grant_id'	 => 'refresh_token',
				'scope_id'	 => 'basic',
				'created_at' => $datetime,
				'updated_at' => $datetime,
			],
		];

		DB::table( 'oauth_grant_scopes' )->insert( $grantScopes );
	}

}
