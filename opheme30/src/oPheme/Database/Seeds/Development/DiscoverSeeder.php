<?php

namespace oPheme\Database\Seeds\Development;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use oPheme\Models\Discover;
use oPheme\Models\DiscoverDay;
use oPheme\Models\Keyword;
use oPheme\Models\DiscoverTimeperiod;
use oPheme\Models\User;

class DiscoverSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();
		
		$keyword1 = new Keyword;
		$keyword1->keyword = "book";
		$keyword1->save();
		
		$keyword2 = new Keyword;
		$keyword2->keyword = "vodka";
		$keyword2->save();
		
		$user = User::where('email', 'lewisf2001uk@googlemail.com')->firstOrFail();
		
		$dayNames = array(
			"monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"
		);
		
		$discover = new Discover;
		$discover->name	= "Cambridge, Any Books?";
		$discover->latitude = "52.210332";
		$discover->longitude = "0.116040";
		$discover->radius = "10";
		$discover->start_date = "2015-04-01 12:00:00"; 
		$discover->end_date = "2015-04-08 12:00:00";
		$discover->save();
		$discover->keyword()->attach( $keyword1->id );
		$discover->user()->attach( $user->id );
		
		
		$authkeys = $user->authKey()->get();
		foreach($authkeys as $authkey) {
			$discover->authKey()->attach( $authkey->id );
		}
		
		foreach($dayNames as $dayName) {
			$day = new DiscoverDay;
			$day->day = $dayName;
			$day->discover()->associate($discover);
			$day->save();
		}
		$period = new DiscoverTimeperiod;
		$period->start = "00:00:00";
		$period->end = "23:59:59";
		$period->discover()->associate($discover);
		$period->save();
		
		$discover = new Discover;
		$discover->name	= "London, Any Vodka?";
		$discover->latitude = "51.505474";
		$discover->longitude = "-0.075328";
		$discover->radius = "10";
		$discover->start_date = "2015-04-01 12:00:00"; 
		$discover->end_date = "2015-04-08 12:00:00";
		$discover->save();
		$discover->keyword()->attach( $keyword2->id );
		$discover->user()->attach( $user->id );
		
		$user = User::where('email', 'lewisf2001uk@googlemail.com')->firstOrFail();
		$authkeys = $user->authKey()->get();
		foreach($authkeys as $authkey) {
			$discover->authKey()->attach( $authkey->id );
		}
		
		foreach($dayNames as $dayName) {
			$day = new DiscoverDay;
			$day->day = $dayName;
			$day->discover()->associate($discover);
			$day->save();
		}
		$period = new DiscoverTimeperiod;
		$period->start = "00:00:00";
		$period->end = "11:59:59";
		$period->discover()->associate($discover);
		$period->save();
	}

}
