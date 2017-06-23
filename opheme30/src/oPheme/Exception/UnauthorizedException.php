<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class UnauthorizedException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 401;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unauthorized';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 401;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Unauthorized')
    {
        parent::__construct($message);
    }
}
