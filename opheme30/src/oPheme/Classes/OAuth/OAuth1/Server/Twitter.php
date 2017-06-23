<?php

namespace oPheme\Classes\OAuth\OAuth1\Server;

use League\OAuth1\Client\Server\Twitter as TwitterLeague;
use Illuminate\Support\Facades\Input;

class Twitter extends TwitterLeague
{
    /**
     * {@inheritDoc}
     */
    public function urlAuthorization()
    {
		if( Input::has('handle') ) {
			$handle = Input::get('handle');
			return "https://api.twitter.com/oauth/authenticate?screen_name=$handle";
		}
        return 'https://api.twitter.com/oauth/authenticate';
    }
}
