<?php

namespace oPheme;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
  |--------------------------------------------------------------------------
 */

// test route printing out database tables
//Route::get( 'print-database', [
//	'uses'	 => 'oPheme\\Controllers\\PrintDatabaseController@getAll',
//	'as'	 => 'internal.print.database' ] );

// generate single use authorise code to use in oauth.client.authorise route
Route::get( '/oauth/generate-code', [
	'uses'	 => 'oPheme\\Controllers\\OAuthClientController@singleUseAuthoriseCode',
	'as'	 => 'oauth.client.singleUseAuthoriseCode' ] );

// authorise different oauth providers (twitter / instagram)
Route::get( '/oauth/{provider}/authorise', [
	'uses'	 => 'oPheme\\Controllers\\OAuthClientController@authorise',
	'as'	 => 'oauth.client.authorise' ] );

// oauth callback
Route::get( '/oauth/{provider}/callback', [
	'uses'	 => 'oPheme\\Controllers\\OAuthClientController@callback',
	'as'	 => 'oauth.client.callback' ] );

// Route to request an OAuth access token
Route::post( 'oauth/access_token', [
	'uses'	 => 'oPheme\\Controllers\\OAuthServerController@issueAccessToken',
	'as'	 => 'oauth.server.issueAccessToken' ] );

// must be valid application key
Route::group( array( 
	'before' => 'opheme-oauth'), function()
{
	// User Routes (Me)
	Route::group(
		array(
			'before' => array(
				'opheme-oauth-owner:user',
				'opheme-oauth:basic'
			) 
		), 
	function()
	{
		// User
			//Read
			Route::get('/me', [
				'uses' => 'oPheme\\Controllers\\UserController@meRead',
				'as' => 'me.users.read' ] );
			// Get Last Login
				Route::get('/me/last_login',[
					'uses' => 'oPheme\\Controllers\\UserController@meGetLastLogin',
					'as' => 'me.users.getLastLogin' ] );
		// AuthKey
			// List		
			Route::get('/authkeys', [
					'uses' => 'oPheme\\Controllers\\AuthKeyController@meList',
					'as' => 'me.authkeys.list' ] );
			// Count
			Route::get('/authkeys/count', [
					'uses' => 'oPheme\\Controllers\\AuthKeyController@meCount',
					'as' => 'me.authkeys.count' ] );
			// Delete all - Removed route as dangerous
			//	Route::delete('/authkeys', [
			//		'uses' => 'oPheme\\Controllers\\AuthKeyController@meDeleteAll',
			//		'as' => 'me.authkeys.deleteAll' ] ); 
			// Delete specific
			Route::delete('/authkeys/{authkeyId}', [
				'uses' => 'oPheme\\Controllers\\AuthKeyController@meDelete',
				'as' => 'me.authkeys.delete' ] );
		// Discover
			// List		
			Route::get('/discovers', [
					'uses' => 'oPheme\\Controllers\\DiscoverController@meList',
					'as' => 'me.discovers.list' ] );
			// Create
			Route::post('/discovers', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@meCreate',
				'as' => 'me.discovers.create' ] );
			// Update (Full)
			Route::put('/discovers/{discoverId}', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@meUpdateFull',
				'as' => 'me.discovers.updateFull' ] );
			// Update (Partial)
			Route::patch('/discovers/{discoverId}', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@meUpdatePartial',
				'as' => 'me.discovers.updatePartial' ] );
			// Delete
			Route::delete('/discovers/{discoverId}', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@meDelete',
				'as' => 'me.discovers.delete' ] );
			// Message
				// Message List
					Route::get('/discovers/{discoverId}/messages', [
						'uses' => 'oPheme\\Controllers\\DiscoverMessageController@meListByDiscover',
						'as' => 'me.discovers.messages.list' ] );
				// Message Count
					Route::get('/discovers/{discoverId}/messages/count', [
						'uses' => 'oPheme\\Controllers\\DiscoverMessageController@meCountByDiscover',
						'as' => 'me.discovers.messages.count' ] );
				// Interaction Message List
				Route::get('/discovers/{discoverId}/interactions', [
					'uses' => 'oPheme\\Controllers\\InteractionController@meListByDiscover',
					'as' => 'me.discovers.interactions.list' ] );
				// Interaction Message Count
				Route::get('/discovers/{discoverId}/interactions/count', [
					'uses' => 'oPheme\\Controllers\\InteractionController@meCountByDiscover',
					'as' => 'me.discovers.interactions.count' ] );
		// Interaction
			// Create Reply
			Route::post('/interactions/replies', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meReply',
				'as' => 'me.interactions.reply.create' ] );
			// List Favorite
			Route::get('/interactions/favorites/{authkeyId}/{messageIds?}', [
						'uses' => 'oPheme\\Controllers\\InteractionController@meListFavorite',
						'as' => 'me.interactions.favorite.list' ] );
			// Create Favorite
			Route::post('/interactions/favorites', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meCreateFavorite',
				'as' => 'me.interactions.favorite.create' ] );
			// Delete Favorite
			Route::delete('/interactions/favorites/{authkeyId}/{messageId}', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meDeleteFavorite',
				'as' => 'me.interactions.favorite.delete' ] );
			// List Follow
			Route::get('/interactions/follows/{authkeyId}/{screenNames?}', [
						'uses' => 'oPheme\\Controllers\\InteractionController@meListFollow',
						'as' => 'me.interactions.follow.list' ] );
			// Create Follow
			Route::post('/interactions/follows', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meCreateFollow',
				'as' => 'me.interactions.follow.create' ] );
			// Delete Follow
			Route::delete('/interactions/follows/{authkeyId}/{screenName}', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meDeleteFollow',
				'as' => 'me.interactions.follow.delete' ] );
			// List Retweet
			Route::get('/interactions/retweets/{authkeyId}/{messageIds?}', [
						'uses' => 'oPheme\\Controllers\\InteractionController@meListRetweet',
						'as' => 'me.interactions.retweet.list' ] );
			// Create Retweet
			Route::post('/interactions/retweets', [
				'uses' => 'oPheme\\Controllers\\InteractionController@meCreateretweet',
				'as' => 'me.interactions.retweet.create' ] );
	});
	
	// Admin Routes
	Route::group(
		array(
			'prefix' => 'admin',
			'before' => array(
				'opheme-oauth-owner:client',
				'opheme-oauth:inhouse'
			) 
		), 
		function()
		{
		// User
			// List
			Route::get('/users', [
				'uses' => 'oPheme\\Controllers\\UserController@adminList',
				'as' => 'admin.users.list' ] );
			// Create
			Route::post('/users/{companyId?}', [
				'uses' => 'oPheme\\Controllers\\UserController@adminCreate',
				'as' => 'admin.users.create' ] );
			// Read
			Route::get('/users/{userId}', [
				'uses' => 'oPheme\\Controllers\\UserController@adminRead',
				'as' => 'admin.users.read' ] );
			// Update (Full)
			Route::put('/users/{userId}', [
				'uses' => 'oPheme\\Controllers\\UserController@adminUpdateFull',
				'as' => 'admin.users.updateFull' ] );
			// Update (Partial)
			Route::patch('/users/{userId}', [
				'uses' => 'oPheme\\Controllers\\UserController@adminUpdatePartial',
				'as' => 'admin.users.updatePartial' ] );
			// Delete
			Route::delete('/users/{userId}', [
				'uses' => 'oPheme\\Controllers\\UserController@adminDelete',
				'as' => 'admin.users.delete' ] );
			// Set Last Login
			Route::post('/users/{userId}/last_login',[
				'uses' => 'oPheme\\Controllers\\UserController@adminSetLastLogin',
				'as' => 'admin.users.setLastLogin' ] );
			//Authkeys
				// List authkeys
				Route::get('/users/{userId}/authkeys', [
					'uses' => 'oPheme\\Controllers\\AuthKeyController@adminListByUser',
					'as' => 'admin.users.authkeys.list' ] );
				// Count authkeys
				Route::get('/users/{userId}/authkeys/count', [
						'uses' => 'oPheme\\Controllers\\AuthKeyController@adminCountByUser',
						'as' => 'admin.users.authkeys.count' ] );
				// Delete all authkeys
				Route::delete('/users/{userId}/authkeys', [
					'uses' => 'oPheme\\Controllers\\AuthKeyController@adminDeleteAllByUser',
					'as' => 'admin.users.authkeys.deleteAll' ] );
				// Delete specific authkey
				Route::delete('/users/{userId}/authkeys/{authkeyId}', [
					'uses' => 'oPheme\\Controllers\\AuthKeyController@adminDeleteByUser',
					'as' => 'admin.users.authkeys.delete' ] );
			// Discovers
				// List discovers
				Route::get('/users/{userId}/discovers', [
					'uses' => 'oPheme\\Controllers\\DiscoverController@adminListByUser',
					'as' => 'admin.users.discovers.list' ] );
				// Create
				Route::post('/users/{userId}/discovers', [
					'uses' => 'oPheme\\Controllers\\DiscoverController@adminCreateByUser',
					'as' => 'admin.users.discovers.create' ] );
				// Update (Full)
				Route::put('/users/{userId}/discovers/{discoverId}', [
					'uses' => 'oPheme\\Controllers\\DiscoverController@adminUpdateFullByUser',
					'as' => 'admin.users.discovers.updateFull' ] );
				// Update (Partial)
				Route::patch('/users/{userId}/discovers/{discoverId}', [
					'uses' => 'oPheme\\Controllers\\DiscoverController@adminUpdatePartialByUser',
					'as' => 'admin.users.discovers.updatePartial' ] );
		// Company
			// List
			Route::get('/companies', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminList',
				'as' => 'admin.companies.list' ] );
			// Create
			Route::post('/companies', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminCreate',
				'as' => 'admin.companies.create' ] );
			// Read
			Route::get('/companies/{companyId}', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminRead',
				'as' => 'admin.companies.read' ] );
			// Update (Full)
			Route::put('/companies/{companyId}', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminUpdateFull',
				'as' => 'admin.companies.updateFull' ] );
			// Update (Partial)
			Route::patch('/companies/{companyId}', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminUpdatePartial',
				'as' => 'admin.companies.updatePartial' ] );
			// Delete
			Route::delete('/companies/{companyId}', [
				'uses' => 'oPheme\\Controllers\\CompanyController@adminDelete',
				'as' => 'admin.companies.delete' ] );
		// AuthKey
			// List
			Route::get('/authkeys', [
				'uses' => 'oPheme\\Controllers\\AuthKeyController@adminList',
				'as' => 'admin.authkeys.list' ] );
			// Read
			Route::get('/authkeys/{authkeyId}', [
				'uses' => 'oPheme\\Controllers\\AuthKeyController@adminRead',
				'as' => 'admin.authkeys.read' ] );
			// Delete
			Route::delete('/authkeys/{authkeyId}', [
				'uses' => 'oPheme\\Controllers\\AuthKeyController@adminDelete',
				'as' => 'admin.authkeys.delete' ] );
		// Discover
			// List
			Route::get('/discovers', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@adminList',
				'as' => 'admin.discovers.list' ] );
			// Read
			Route::get('/discovers/{discoverId}', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@adminRead',
				'as' => 'admin.discovers.read' ] );
			// Delete
			Route::delete('/discovers/{discoverId}', [
				'uses' => 'oPheme\\Controllers\\DiscoverController@adminDelete',
				'as' => 'admin.discovers.delete' ] );
			// Message
				// Message List
				Route::get('/discovers/{discoverId}/messages', [
					'uses' => 'oPheme\\Controllers\\DiscoverMessageController@adminListByDiscover',
					'as' => 'admin.discovers.messages.list' ] );
				// Message Count
					Route::get('/discovers/{discoverId}/messages/count', [
							'uses' => 'oPheme\\Controllers\\DiscoverMessageController@adminCountByDiscover',
							'as' => 'admin.discovers.messages.count' ] );
				// Interaction Message List
				Route::get('/discovers/{discoverId}/interactions', [
					'uses' => 'oPheme\\Controllers\\InteractionController@adminListByDiscover',
					'as' => 'admin.discovers.interactions.list' ] );
				// Interaction Message Count
				Route::get('/discovers/{discoverId}/interactions/count', [
					'uses' => 'oPheme\\Controllers\\InteractionController@adminCountByDiscover',
					'as' => 'admin.discovers.interactions.count' ] );
		}
	);	
} );

// Unknown page
App::missing( function()
{
	// Return a 404 json error
	return new JsonResponse([
						'error' => "not_found",
						'error_code' => "404",
						'error_description' => "Resource Not Found"
				],
				404
		);
} );
