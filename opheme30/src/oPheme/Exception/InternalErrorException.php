<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class InternalErrorException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 500;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'internal_error';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 500;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Internal error')
    {
        parent::__construct($message);
    }
}
