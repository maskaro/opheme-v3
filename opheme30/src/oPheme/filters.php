<?php

namespace oPheme;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\URL;

/*
  |--------------------------------------------------------------------------
  | Application & Route Filters
  |--------------------------------------------------------------------------
  |
  | Below you will find the "before" and "after" events for the application
  | which may be used to do any work before or after a request into your
  | application. Here you may also register your custom route filters.
  |
 */

App::before( function($request)
{
	//
} );


App::after( function($request, $response)
{
	//
} );

/*
  |--------------------------------------------------------------------------
  | Authentication Filters
  |--------------------------------------------------------------------------
  |
  | The following filters are used to verify that the user of the current
  | session is logged into this application. The "basic" filter easily
  | integrates HTTP Basic authentication for quick, simple checking.
  |
 */

Route::filter( 'auth', function()
{
	if ( Auth::guest() )
	{
		if ( Request::ajax() )
		{
			return Response::make( 'Unauthorized', 401 );
		}
		else
		{
//			return Redirect::guest( URL::route( 'auth.login.view' ) );
		}
	}
} );


Route::filter( 'auth.basic', function()
{
	return Auth::basic();
} );

/*
  |--------------------------------------------------------------------------
  | Guest Filter
  |--------------------------------------------------------------------------
  |
  | The "guest" filter is the counterpart of the authentication filters as
  | it simply checks that the current user is not logged in. A redirect
  | response will be issued if they are, which you may freely change.
  |
 */

Route::filter( 'guest', function()
{
	if ( Auth::check() )
	{
//		return Redirect::route( 'user.dashboard' );
	}
} );

/*
  |--------------------------------------------------------------------------
  | CSRF Protection Filter
  |--------------------------------------------------------------------------
  |
  | The CSRF filter is responsible for protecting your application against
  | cross-site request forgery attacks. If this special token in a user
  | session does not match the one given in this request, we'll bail.
  |
 */

Route::filter( 'csrf', function()
{
	if ( Session::token() != Input::get( '_token' ) )
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
} );

/*
  |--------------------------------------------------------------------------
  | User account details check Filter
  |--------------------------------------------------------------------------
  |
  | Checks whether current authenticated user has filled in all their details
  | and has authorised with at least one social media account provider.
  |
 */

/*Route::filter( 'user.account.valid', function()
{
	$user = Auth::user();
	if ( !$user->is_valid || !$user->has_auth_keys )
	{
		return Redirect::route( 'user.account.view' )->with( 'warning', 'Please fill in your account profile.' );
	}
	else
	{
		//return Redirect::route( 'user.dashboard' );
	}
} );*/
