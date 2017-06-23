<?php

namespace oPheme\Models\Traits;

use Illuminate\Support\Facades\Validator;
use oPheme\Exception\ValidationException;

trait ValidationTrait
{
/**
	 * The list of validation rules
	 * @var array $validationRules 
	 */
	protected $validationRules = array();
	
    public function validate($data)
    {
        // make a new validator object
        $validator = Validator::make($data, $this->validationRules);

        // check for failure
        if ($validator->fails())
        {
            // set errors and return false
            $this->errorValidation($validator);
        }
        // validation pass
    }
	
	/**
	 * Throws a Validation Exception
	 */
	protected function errorValidation ( $validator )
	{
		// build the validation error to throw the exception with
		// get the failed validation error array
		$failedValidation = $validator->failed();
		// get the first item that gave an error and the validion it failed array
		list($failedValidationItem, $failedValidationItemErrorArray) = each($failedValidation);
		// get the validation method that the item failed and details of the validation method array
		list($failedValidationItemError, $failedValidationItemErrorDetailsArray) = each($failedValidationItemErrorArray);

		// set the error type and error message and throw an exception
		$type =  strtolower($failedValidationItem)."_".strtolower($failedValidationItemError);
		$message = $validator->messages()->first($failedValidationItem);
			
		throw new ValidationException( $type, $message );
	}
}
