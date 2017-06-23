<?php

namespace oPheme\Classes\OAuth;

use Illuminate\Support\ServiceProvider;
use oPheme\Classes\OAuth\OAuth1\Server\Twitter;
use oPheme\Classes\OAuth\OAuth2\Client\Instagram;

class OAuthServiceProvider
	extends ServiceProvider
{

	/**
	 * Inject the providers into the Manager class
	 *
	 * @return void
	 */
	public function boot ()
	{
		$this->app[ 'auth.providers.manager' ]->set( 'twitter', $this->app[ 'auth.providers.twitter' ] );
		$this->app[ 'auth.providers.manager' ]->set( 'instagram', $this->app[ 'auth.providers.instagram' ] );
	}

	/**
	 * Register each authentication provider
	 *
	 * @return void
	 */
	public function register ()
	{
		$this->registerManager();
		$this->registerTwitterAuthenticator();
		$this->registerInstagramAuthenticator();
	}

	/**
	 * Register the Manager class
	 *
	 * @return void
	 */
	public function registerManager ()
	{

		$this->app->bindShared('auth.providers.manager', function($app)
		{
			return new Manager;
		} );

		$this->app->bind( 'oPheme\Classes\OAuth\Manager', function($app)
		{
			return $app[ 'auth.providers.manager' ];
		} );
	}

	/**
	 * Register Twitter Authenticator
	 *
	 * @return void
	 */
	public function registerTwitterAuthenticator ()
	{

		$this->app->bindShared('auth.providers.twitter', function($app)
		{
			return new Twitter( array(
				'identifier'	 => $app[ 'config' ]->get( 'auth.providers.twitter.identifier' ),
				'secret'		 => $app[ 'config' ]->get( 'auth.providers.twitter.secret' ),
				'callback_uri'	 => $app[ 'config' ]->get( 'auth.providers.twitter.callback_uri' )
				) );
		} );
	}

	/**
	 * Register Instagram Authenticator
	 *
	 * @return void
	 */
	public function registerInstagramAuthenticator ()
	{
		$this->app->bindShared('auth.providers.instagram', function($app)
		{
			$instagram = new Instagram( array(
				'clientId'		 => $app[ 'config' ]->get( 'auth.providers.instagram.identifier' ),
				'clientSecret'	 => $app[ 'config' ]->get( 'auth.providers.instagram.secret' ),
				'redirectUri'	 => $app[ 'config' ]->get( 'auth.providers.instagram.callback_uri' ),
				'scopes'		 => $app[ 'config' ]->get( 'auth.providers.instagram.scope')
				) );
			return $instagram;
		} );
	}
}
