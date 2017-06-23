<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\SocialMediaPlatform;

class SocialMediaPlatformTableSeeder extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		//DB::table('AuthKeyType')->delete();
		//DB::table( 'AuthKeyType' )->truncate();

		$tokenType1			 = new SocialMediaPlatform;
		$tokenType1->name	 = 'twitter';
		$tokenType1->save();

		$tokenType2			 = new SocialMediaPlatform;
		$tokenType2->name	 = 'instagram';
		$tokenType2->save();
	}

}
