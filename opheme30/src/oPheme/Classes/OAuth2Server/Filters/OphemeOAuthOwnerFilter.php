<?php

namespace oPheme\Classes\OAuth2Server\Filters;

use oPheme\Exception\IncorrectResourceOwnerTypeException;
use LucaDegasperi\OAuth2Server\Authorizer;
use LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;

class OphemeOAuthOwnerFilter extends OAuthOwnerFilter
{
    /**
     * The Authorizer instance
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * @param Authorizer $authorizer
     */
    public function __construct(Authorizer $authorizer)
    {
        parent::__construct($authorizer);
    }

    /**
     * The main filter method
     * @internal param mixed $route, mixed $request, mixed $owners,...
     * @return null
     * @throws \League\OAuth2\Server\Exception\AccessDeniedException
     */
    public function filter()
    {
        if (func_num_args() > 2) {
            $allowedOwnerTypes = array_slice(func_get_args(), 2);
			$currentOwnerType = $this->authorizer->getResourceOwnerType();
            if (!in_array($currentOwnerType, $allowedOwnerTypes)) {
                throw new IncorrectResourceOwnerTypeException($currentOwnerType, implode(", ",$allowedOwnerTypes));
            }
        }
        return null;
    }
}
