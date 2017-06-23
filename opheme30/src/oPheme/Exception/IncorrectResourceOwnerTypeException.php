<?php

namespace oPheme\Exception;

use oPheme\Exception\OphemeException;

/**
 * Exception class
 */
class IncorrectResourceOwnerTypeException extends OphemeException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 403;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'incorrect_resource_owner_type';
	
	/**
     * {@inheritdoc}
     */
	public $errorCode = 202;
	
    /**
     * {@inheritdoc}
     */
    public function __construct($currentOwnerType, $allowedOwnerTypes)
    {
        parent::__construct(
			sprintf(
                'The access token is an incorrect resource owner type. Current type: "%s", Allowed types: "%s"',
                $currentOwnerType,
				$allowedOwnerTypes
            )
		);
    }
}
