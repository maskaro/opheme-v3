<?php 

namespace oPheme\Transformer;

use League\Fractal\TransformerAbstract;
use oPheme\Classes\Traits\DisallowedItemsTrait;
use oPheme\Exception\InternalErrorException;

abstract class BaseTransformer extends TransformerAbstract
{
	use DisallowedItemsTrait;
	
	// Used by DisallowedItemsTrait
	protected $type;

	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [ ];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [ ];
	
	/**
	 *  Constructor checks if the available and default includes are allow
	 *  based on the client scope and user permissions
	 */
	public function __construct ()
	{
		if( !isset($this->type) ) {
			throw new InternalErrorException("type in transformer not set");
		}
		$this->availableIncludes = $this->checkIncludeAllowed( $this->availableIncludes );
		$this->defaultIncludes	 = $this->checkIncludeAllowed( $this->defaultIncludes );
	}
	
	/**
     * Checker for array of includes in Transformers
     *
     * @param array $includes Array to be checked
     *
     * @return array
     */
	protected function checkIncludeAllowed($includes)
    {
		// Check permissions for includes
        foreach($includes as $key => $include) {
			if(!$this->isIncludeAllowed($include)) {
				unset($includes[$key]);
			}
		}
		
		return $includes;
    }
	
	// This can be overridden on children
	protected function isIncludeAllowed($include)
	{
		return true;
	}
}

