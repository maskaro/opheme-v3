<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class SocialMediaException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'social_media';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 207;

    /**
     * {@inheritdoc}
     */

    public function __construct($message = 'Social media error')
    {
        parent::__construct($message);
    }
}
