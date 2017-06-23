<?php

namespace oPheme;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

// current authenticated user
if ( Auth::check() )
{
	$user		 = Auth::user();
	$userExtra	 = $user->extra;
	View::share( 'user', Auth::user() );
	View::share( 'userExtra', $userExtra );
	View::share( 'userIsValid', $userExtra->is_valid && $user->has_auth_keys );
	View::share( 'userIsAdmin', $user->is_admin );
	View::share( 'userIsReseller', $user->is_reseller );
	unset( $user, $userExtra );
}