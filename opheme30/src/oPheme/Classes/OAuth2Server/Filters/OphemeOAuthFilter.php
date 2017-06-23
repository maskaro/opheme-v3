<?php

namespace oPheme\Classes\OAuth2Server\Filters;

use oPheme\Exception\MissingScopeException;
use LucaDegasperi\OAuth2Server\Authorizer;
use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;

class OphemeOAuthFilter extends OAuthFilter
{
    /**
     * The Authorizer instance
     * @var LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * Whether or not to check the http headers only for an access token
     * @var bool
     */
    protected $httpHeadersOnly = false;

    /**
     * The scopes to check for
     * @var array
     */
    protected $scopes = [];

    /**
     * @param Authorizer $authorizer
     * @param bool $httpHeadersOnly
     */
    public function __construct(Authorizer $authorizer, $httpHeadersOnly = false)
    {
        parent::__construct($authorizer, $httpHeadersOnly);
    }
	
    /**
     * Validate the scopes
     * @throws \League\OAuth2\Server\Exception\InvalidScopeException
     */
    public function validateScopes()
    {
        foreach($this->scopes as $scope) {
            if(!$this->authorizer->hasScope($scope)) {
                throw new MissingScopeException($scope);
            }
        }
    }
}
