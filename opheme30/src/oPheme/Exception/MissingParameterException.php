<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class MissingParameterException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'missing_parameter';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 203;

    /**
     * {@inheritdoc}
     */

    public function __construct($parameter)
    {
        parent::__construct(
            sprintf(
                'The request is missing a required parameter. Check the "%s" parameter.',
                $parameter
            )
        );
    }
}
