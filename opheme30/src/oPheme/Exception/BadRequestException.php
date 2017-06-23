<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class BadRequestException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'bad_request';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 400;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Bad request')
    {
        parent::__construct($message);
    }
}
