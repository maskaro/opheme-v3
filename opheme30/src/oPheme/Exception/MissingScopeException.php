<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class MissingScopeException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 403;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'missing_scope';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 201;

    /**
     * {@inheritdoc}
     */

    public function __construct($parameter)
    {
        parent::__construct(
            sprintf(
                'The access token is missing a required scope. Check the "%s" scope.',
                $parameter
            )
        );
    }
}
