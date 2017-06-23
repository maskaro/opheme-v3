<?php

namespace oPheme\Classes\OAuth2Server;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use League\OAuth2\Server\Exception\OAuthException;
use LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider;
use oPheme\Classes\OAuth2Server\Filters\OphemeOAuthFilter;
use oPheme\Classes\OAuth2Server\Filters\OphemeOAuthOwnerFilter;
use oPheme\Exception\OphemeException;

class OphemeOAuth2ServerServiceProvider extends OAuth2ServerServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->bootFilters();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
		$this->registerErrorHandlers();
        $this->registerFilterBindings();
    }
	
    /**
     * Register the Filters to the IoC container because some filters need additional parameters
     * @return void
     */
    public function registerFilterBindings()
    {
		$this->app->bindShared('oPheme\Classes\OAuth2\Filters\OphemeOAuthOwnerFilter', function ($app) {
            return new OphemeOAuthOwnerFilter($app['oauth2-server.authorizer']);
        });
        $this->app->bindShared('oPheme\Classes\OAuth2\Filters\OphemeOAuthFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');
            return new OphemeOAuthFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });
    }

    /**
     * Boot the filters
     * @return void
     */
    private function bootFilters()
    {
		$this->app['router']->filter('opheme-oauth', 'oPheme\Classes\OAuth2Server\Filters\OphemeOAuthFilter');
		$this->app['router']->filter('opheme-oauth-owner', 'oPheme\Classes\OAuth2Server\Filters\OphemeOAuthOwnerFilter');
	}
	
	/**
     * Register the OAuth error handlers
     * @return void
     */
    private function registerErrorHandlers()
    {
		// Grab exception if class is OphemeException
        $this->app->error(function(OphemeException $e) {
            if($e->shouldRedirect()) {
                return new RedirectResponse($e->getRedirectUri($e->getCode()));
            } else {
                return new JsonResponse([
                                'error' => $e->errorType,
								'error_code' => $e->getCode(),
                                'error_description' => $e->getMessage()
                        ],
                        $e->httpStatusCode,
                        $e->getHttpHeaders()
                );
            }
        });
		
		// Grab exception if class is OAuthException
		$this->app->error(function(OAuthException $e) {
            if($e->shouldRedirect()) {
                return new RedirectResponse($e->getRedirectUri($this->getOAuthExceptionErrorCode($e->errorType)));
            } else {
                return new JsonResponse([
                                'error' => $e->errorType,
								'error_code' => $this->getOAuthExceptionErrorCode($e->errorType),
                                'error_description' => $e->getMessage()
                        ],
                        $e->httpStatusCode,
                        $e->getHttpHeaders()
                );
            }
        });
    }
	
	private function getOAuthExceptionErrorCode($errorType) {
		return Config::get('oauth2errorcode.'.$errorType);
	}
}
