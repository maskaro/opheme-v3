<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class UnknownParameterException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unknown_parameter';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 204;

    /**
     * {@inheritdoc}
     */

    public function __construct($parameter)
    {
        parent::__construct(
            sprintf(
                'The request has an unknown parameter. Check the "%s" parameter.',
                $parameter
            )
        );
    }
}
