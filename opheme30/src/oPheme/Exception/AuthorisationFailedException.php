<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class AuthorisationFailedException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'authorisation_failed';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 206;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Authorisation failed')
    {
        parent::__construct($message);
    }
}
