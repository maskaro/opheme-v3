<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class ValidationException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
	 * 
	 * Set in constructor
     */
    public $errorType;
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 205;

    /**
     * {@inheritdoc}
     */

    public function __construct($error_type, $message = 'Validation Error' )
    {
		$this->errorType = sprintf('validation_%s', $error_type);
        parent::__construct($message);
    }
}
