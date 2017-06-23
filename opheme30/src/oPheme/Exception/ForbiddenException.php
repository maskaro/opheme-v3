<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class ForbiddenException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 403;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'forbidden';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 403;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Forbidden')
    {
        parent::__construct($message);
    }
}
