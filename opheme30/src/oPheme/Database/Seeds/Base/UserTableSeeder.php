<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use oPheme\Models\Company;
use oPheme\Models\User;
use oPheme\Models\UserExtra;

class UserTableSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		// All existing users are deleted !!!
		//DB::table('User')->delete();
		//DB::table( 'User' )->truncate();
		// add user using Eloquent
		
		// Get the subscription id
		$company = Company::where( "name", "=", "Opheme Limited" )->first();
		
		$user				 = new User;
		$user->company_id	 = $company->id;
		$user->email		 = 'maskaro@gmail.com';
		$user->password		 = Hash::make( 'maskarosf14022006' );
		$user->suspended	 = 0;
		$user->save();

		$userExtra							 = new UserExtra;
		$userExtra->first_name				 = 'Razvan-Ioan';
		$userExtra->last_name				 = 'Dinita';
		$userExtra->phone					 = '07549953728';
		$userExtra->email_confirmation_token = null;
		$userExtra->user()->associate($user);
		$userExtra->save();
		
		$user				 = new User;
		$user->company_id	 = $company->id;
		$user->email		 = 'lewisf2001uk@googlemail.com';
		$user->password		 = Hash::make( 'password' );
		$user->suspended	 = 0;
		$user->save();

		$userExtra							 = new UserExtra;
		$userExtra->first_name				 = 'Lewis';
		$userExtra->last_name				 = 'Freeman';
		$userExtra->phone					 = '07885529292';
		$userExtra->email_confirmation_token = null;
		$userExtra->user()->associate($user);
		$userExtra->save();
		
		// alternativ to eloquent we can also use direct database-methods
		/*
		  User::create(array(
		  'username'  => 'admin',
		  'password'  => Hash::make('password'),
		  'email'     => 'admin@localhost'
		  ));
		 */
	}

}
