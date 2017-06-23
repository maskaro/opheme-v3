<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class GoneException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 410;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'gone';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 410;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Resource Gone')
    {
        parent::__construct($message);
    }
}
