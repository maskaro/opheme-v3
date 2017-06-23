<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class NotFoundException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 404;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'not_found';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 404;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Not found')
    {
        parent::__construct($message);
    }
}
