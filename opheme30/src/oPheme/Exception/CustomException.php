<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class CustomException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode;

    /**
     * {@inheritdoc}
     */
    public $errorType;
	
	/**
     * {@inheritdoc}
     */
	public $errorCode;
	
	/**
     * {@inheritdoc}
     */
	public $redirectUri;

    /**
     * {@inheritdoc}
     */
	public function __construct($message = 'An error occured', 
								$errorType = 'generic_error', 
								$errorCode = 100, 
								$httpStatusCode = 400,
								$redirectUri = null)
    {
		$this->errorType = $errorType;
		$this->errorCode = $errorCode;
		$this->httpStatusCode = $httpStatusCode;
		$this->redirectUri = $redirectUri;
		// Construct the parent
        parent::__construct($message);
    }
}
