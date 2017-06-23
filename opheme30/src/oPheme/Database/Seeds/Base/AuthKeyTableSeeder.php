<?php

namespace oPheme\Database\Seeds\Base;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\AuthKey;
use oPheme\Models\User;
use oPheme\Models\SocialMediaPlatform;

class AuthKeyTableSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();

		//DB::table('AuthKey')->delete();
		//DB::table( 'AuthKey' )->truncate();
		
		//User 1
		$user = User::where('email', 'maskaro@gmail.com')->firstOrFail();
		
		$token							 = new AuthKey;
		$token->socialmediaplatform_id	 = SocialMediaPlatform::where("name", "=", "twitter")->first()->id;
		$token->screen_name			 = 'rdinita';
		$token->token					 = '15894713-x8ykiwW0C6XwXRtkkYRD36VRttVbK5URhVv6KxpwI';
		$token->token_secret			 = '5BgHRnSZkDwfbn8Zs17ExKnWypaKfYsrRk18nUHMtQ';
		$token->save();
		$user->authKey()->attach( $token->id );
		
		$token							 = new AuthKey;
		$token->socialmediaplatform_id	 = SocialMediaPlatform::where("name", "=", "instagram")->first()->id;
		$token->screen_name			 = 'maskaro';
		$token->token					 = '473352.dfdfeda.54e05182a6fd4906a43f30befd0ac840';
		$token->token_secret			 = null;
		$token->save();
		$user->authKey()->attach( $token->id );
		
		
		// User 2
		$user = User::where('email', 'lewisf2001uk@googlemail.com')->firstOrFail();
		
		$token							 = new AuthKey;
		$token->socialmediaplatform_id	 = SocialMediaPlatform::where("name", "=", "twitter")->first()->id;
		$token->screen_name				 = 'lewisf2001uk';
		$token->token					 = '423681650-bb5OwTqr0UzZrjLdHCUCgSEY7Pb4zRLDl4Y8PBHr';
//		$token->token					 = '423681650-uf9Q0RYRkPDBAy5cx00Ht8zzClRmAF9zw7bTXgaO'; old
		$token->token_secret			 = 'Kp9sdqXY7Lw1Y3aeVo8A90oqAz7PwkTfGrKIin1cKe2v7';
		//$token->token_secret			 = 'gYx9YkMgMDrOIhkkgjerxXvOTiCGYhGBeSLwQFrh1aFDC'; old
		$token->save();
		$user->authKey()->attach( $token->id );
		
		$token							 = new AuthKey;
		$token->socialmediaplatform_id	 = SocialMediaPlatform::where("name", "=", "instagram")->first()->id;
		$token->screen_name				 = 'lewisf2001uk';
		$token->token					 = '2029415346.b698bef.d3a715d02dd241b9aa44c6864b6f72cd';
		$token->token_secret			 = null;
		$token->save();
		$user->authKey()->attach( $token->id );
	}

}
