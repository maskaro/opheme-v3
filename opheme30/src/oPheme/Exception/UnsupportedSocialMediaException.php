<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class UnsupportedSocialMediaException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unsupported_social_media';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 208;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Unsupported social media')
    {
        parent::__construct($message);
    }
}
