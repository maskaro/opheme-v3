<?php

namespace oPheme\Classes\WriteHelpers;

use ReflectionClass;
use oPheme\Classes\Traits\DisallowedItemsTrait;
use oPheme\Exception\InternalErrorException;
use oPheme\Exception\MissingParameterException;
use oPheme\Exception\UnknownParameterException;

class BaseWriteHelper
{
	use DisallowedItemsTrait;
	
	// Used by DisallowedItemsTrait
	protected $type;
	
	public $external;
	public $internal;
	
	public function __construct ($requestAction = null)
	{
		if( !isset($this->type) ) {
			throw new InternalErrorException("type in write helper not set");
		}
		
		if( is_null($requestAction)) {
			throw new InternalErrorException("requestAction in write helper not set");
		}
		
		$this->cleanItems($requestAction, 'external');
		$this->cleanItems($requestAction, 'internal');
	}
	
	/**
	 * 
	 * @param string $requestAction
	 * @param string $itemType
	 */
	private function cleanItems( $requestAction, $itemType)
	{
		$requestActionsAllowed = array('create', 'update');
		if( !in_array( $requestAction, $requestActionsAllowed ) ) {
			throw new InternalErrorException("Invalid Request Action '$requestAction'");
		}
		if( !($itemType == 'external' || $itemType == 'internal') ) {
			throw new InternalErrorException("Invalid Item Type '$itemType'");
		}
		
		// if this item type isn't set create an empty array
		if( !isset( $this->{$itemType} ) ) {
			$this->{$itemType} = array();
		}
		
		// check the dissallowed items basied on client and scope permissions
		$this->{$itemType} = $this->removeDisallowedItems($this->{$itemType});

		foreach ( $this->{$itemType} as $name => &$detailsArray) {
			// remove any items that aren't for this $requestAction
			if( !$detailsArray[$requestAction] ) {
				unset( $this->{$itemType}[$name] );
			}
			// remove the $requestActionsAllowed values from the items
			foreach( $requestActionsAllowed as $requestAction) {
				unset( $detailsArray[$requestAction] );
			}
		}
	}
	
	/**
	 * Check if there is any missing items
	 * 
	 * @param array $jsonData
	 */
	public function checkMissingItems( $jsonData )
	{
		// check if there are any differences in the external config and given json data
		if ( $missingItems = array_diff_key($this->external, $jsonData) ) {
			// for any missing items check if they are actually required
			foreach( $missingItems as $key => $missingItem) {
				
				if( !isset( $missingItem['required'] ) ) {
					// if the missing item's required parameter is missing
					throw new InternalErrorException("Missing required parameter for {$key} in writehelper config");
				}
				if( $missingItem['required'] === true) {
					// if the missing item is required
					// returns an error
					throw new MissingParameterException($key);
				} elseif( $missingItem['required'] === false ) {
					// if the missing item isn't required
					unset( $this->external[$key] );
				} else {
					// if the missing item's required parameter isn't true or false
					throw new InternalErrorException("Incorrect value for {$key}'s required parameter in writehelper config");
				}
			}
		}
	}
	
	/**
	 * Check if there is any unknown items
	 * 
	 * @param array $jsonData
	 */
	public function checkUnknownItems( $jsonData )
	{
		if ( $unknownItems = array_diff_key($jsonData, $this->external) ) {
			// returns an error passing the first unknown item
			throw new UnknownParameterException( array_keys($unknownItems)[0] );
		}
	}
	
	/**
	 * Add the values to the class variable "external" and Validates them
	 * 
	 * @param type $selfEloquentObject
	 * @param array $jsonData
	 * @return void
	 */
	public function addValuesAndValidate($selfEloquentObject, $jsonData)
	{
		$grouped = array();
		$groupedMultiple = array();
		foreach($this->external as $name => &$config) {
			// if there is data given
			if( isset( $jsonData[$name] ) ) {
				if( isset( $config['multiple'] ) ) {
					// If this is a multiple item

					// Produce the values array
					$usedUniqueRef = array();
					foreach( $jsonData[$name]['data'] as $KeyValuePair ) {
						do {
							$uniqueRef = str_random(5);
						} while( in_array( $uniqueRef, $usedUniqueRef, true ) );
						
						$usedUniqueRef[] = $uniqueRef;
						foreach( $KeyValuePair as $key => $value ) {
							${$key}[$uniqueRef] = $value;
						}
					}
					
					// Add the values array to the class variable "external"
					$outerRelationship = $config['database']['relationship'];
					foreach( $config['multiple'] as $key => $data ) {
						// Add data to the class variable "external"
						if( !isset(${$key}) ) {
							$config['multiple'][$key]['values'] = array();
						} else {
							$config['multiple'][$key]['values'] = ${$key};
						}
						
						// Group the relationships together
						$base = $config['multiple'][$key];
						if( isset( $base['database']['relationship'] ) ) {
							$innerRelationship = $base['database']['relationship'];
							$groupedMultiple[$outerRelationship][$innerRelationship][$key] = $base;
						}
						else {
							$groupedMultiple[$outerRelationship]['self'][$key] = $base;
						}
					}
				} else {
					// If this is a singlar item
					
					// Add data to the class variable "external"
					$config['value'] = $jsonData[$name];
					
					// Group the relationships together
					if( isset( $config['database']['relationship'] ) ) {
						$relationship = $config['database']['relationship'];
						$grouped[$relationship][$name] = $config;
					} else {
						$grouped['self'][$name] = $config;
					}
				}
			} else {
				// unset an items that weren't given (for patch routes)
				unset($this->external[$name]);
			}
		}
		
		// non multiple fields group
		foreach($grouped as $relationship => $items) {
			if($relationship == 'self') {
				foreach($items as $itemName => $itemData) {
					$selfEloquentObject->validate( array( $itemName => $itemData['value'] ) );
				}
			} else {
				$class = get_class($selfEloquentObject->$relationship()->getRelated());
				$object = new $class();
				foreach($items as $itemName => $itemData) {
					$object->validate( array( $itemName => $itemData['value'] ) );
				}
			}
		}
		
		// multiple fields group
		foreach($groupedMultiple as $outerRelationship => $innerRelationshipData) {
			// get the relationship to the main object
			$class = get_class($selfEloquentObject->$outerRelationship()->getRelated());
			$object = new $class();
			foreach($innerRelationshipData as $innerRelationship => $items) {
				if($innerRelationship == 'self') {
					foreach($items as $itemName => $itemData) {
						foreach( $itemData['values'] as $value) {
							$object->validate( array( $itemName => $value ) );
						}
					}
				} else {
					// get the relationship to the inner object
					$innerClass = get_class($object->$innerRelationship()->getRelated());
					$innerObject = new $innerClass();
					foreach( $itemData['values'] as $value) {
						$innerObject->validate( array( $itemName => $value ) );
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * Executes the external callbacks
	 */
	public function executeExternalCallbacks ()
	{
		$this->executeCallbacks( 'external' );
	}
	
	/**
	 * 
	 * Executes the internal callbacks
	 */
	public function executeInternalCallbacks ()
	{
		$this->executeCallbacks( 'internal' );
	}
	
	/**
	 * 
	 * @param type $itemType
	 */
	public function executeCallbacks( $itemType )
	{
		if( !($itemType == 'external' || $itemType == 'internal') ) {
			throw new InternalErrorException("Invalid Item Type '$itemType'");
		}
		
		foreach($this->{$itemType} as &$config) {
			if( isset( $config['multiple'] ) ) {
				foreach( $config['multiple'] as &$field) {
					if( isset( $field['callback'] ) ) {
						// update the value to be set using the callback
						foreach( $field['values'] as &$value ) {
							$value = $this->{'callback_' . $field['callback']}($value);
						}
						unset( $config['callback'] );
					}
				}
			} else {
				if( isset( $config['callback'] ) ) {
					// update the value to be set using the callback
					$config['value'] = $this->{'callback_' . $config['callback']}($config['value']);
					unset( $config['callback'] );
				}
			}
		}
	}
	
	/**
	 * 
	 * @param type $eloquentObject
	 */
	public function executeCreate($eloquentObject)
	{
		$grouped = array();
		$groupedMultiple = array();
		foreach( array($this->external, $this->internal) as $itemGroups ) {
			foreach( $itemGroups as  $config) {
				if( isset( $config['multiple'] ) ) {
					// If this is a multiple item
					$outerRelationship = $config['database']['relationship'];
					foreach( $config['multiple'] as $base ) {
						// Group the relationships together
						$field = $base['database']['field'];
						if( isset( $base['database']['relationship'] ) ) {
							$innerRelationship = $base['database']['relationship'];
						} else {
							$innerRelationship = "self";
						}
						
						foreach($base['values'] as $uniqueRef => $value) {
							$groupedMultiple[$outerRelationship][$uniqueRef][$innerRelationship][$field] = $value;
						}
					}
				} else {
					// If this is a singlar item
					$field = $config['database']['field'];
					$value = $config['value'];
					// Group the relationships together
					if( isset( $config['database']['relationship'] ) ) {
						$relationship = $config['database']['relationship'];
						$grouped[$relationship][$field] = $value;
					} else {
						$grouped['self'][$field] = $value;
					}
				}
			}
		}

		foreach($grouped as $relationship => $items) {
			if($relationship == 'self') {
				foreach($items as $field => $value) {
					$eloquentObject->$field = $value;
				}
				$eloquentObject->save();
			} else {
				$class = get_class($eloquentObject->$relationship()->getRelated());
				$object = new $class($items);

				$eloquentObject->$relationship()->save($object);
			}
		}
		
		$eloquentObjectClassShortName = (new ReflectionClass($eloquentObject))->getShortName();
		foreach($groupedMultiple as $outerRelationship => $itemSets) {
			$class = get_class($eloquentObject->$outerRelationship()->getRelated());
			$eloquentRelationshipType = $eloquentObject->relationshipTypes[$outerRelationship];
			foreach($itemSets as $itemSet) {
				foreach($itemSet as $innerRelationship => $items) {
					if($innerRelationship == 'self') {
						switch($eloquentRelationshipType) {
							case "belongsTo":
							case "hasMany":
								$object = new $class($items);
								$object->{lcfirst($eloquentObjectClassShortName)}()->associate($eloquentObject);
								$object->save();
							break;
							case "belongsToMany":
								$object = $class::firstOrCreate($items);
								$object->{lcfirst($eloquentObjectClassShortName)}()->attach( $eloquentObject->id );
							break;
							default:
								throw new InternalErrorException("'{$eloquentRelationshipType}' eloquentRelationshipType not set in 'executeCreate' function");
						}
						
					} else {
						throw new InternalErrorException("Code for groupedMultiple with innerRelationship not yet created in 'executeCreate' function");
//						$innerClass = get_class($object->$innerRelationship()->getRelated());
//						$innerObject = new $innerClass($items);
//						$innerObject->$innerRelationship()->save($innerObject);
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * @param type $eloquentObject
	 */
	public function executeUpdate($eloquentObject)
	{
		$groupedMultiple = array();
		foreach( array($this->external, $this->internal) as $itemGroups ) {
			foreach( $itemGroups as $config) {
				if( isset( $config['multiple'] ) ) {
					// If this is a multiple item
					$outerRelationship = $config['database']['relationship'];
					foreach( $config['multiple'] as $base ) {
						// Group the relationships together
						$field = $base['database']['field'];
						if( isset( $base['database']['relationship'] ) ) {
							$innerRelationship = $base['database']['relationship'];
						} else {
							$innerRelationship = "self";
						}
						
						if( empty($base['values']) ) {
							$groupedMultiple[$outerRelationship] = array();
						} else {
							foreach($base['values'] as $uniqueRef => $value) {
								$groupedMultiple[$outerRelationship][$uniqueRef][$innerRelationship][$field] = $value;
							}
						}
					}
				} else {
					// If this is a singlar item
					$field = $config['database']['field'];
					$value = $config['value'];
					// Group the relationships together
					if( isset( $config['database']['relationship'] ) ) {
						$relationship = $config['database']['relationship'];
						$eloquentObject->$relationship->$field = $value;
					} else {
						$eloquentObject->$field = $value;
					}
				}
			}
		}
		$eloquentObject->push();

		$eloquentObjectClassShortName = (new ReflectionClass($eloquentObject))->getShortName();
		foreach($groupedMultiple as $outerRelationship => $itemSets) {
			$class = get_class($eloquentObject->$outerRelationship()->getRelated());
			$eloquentRelationshipType = $eloquentObject->relationshipTypes[$outerRelationship];
			
			switch($eloquentRelationshipType) {
				case "belongsTo":
				case "hasMany":
					$associations = $eloquentObject->{$outerRelationship}()->get();
					foreach($associations as $association) {
						$association->delete();
					}
					break;
				case "belongsToMany":
					$eloquentObject->{$outerRelationship}()->detach();
					break;
				default:
					throw new InternalErrorException("'{$eloquentRelationshipType}' eloquentRelationshipType not set in 'executeCreate' function");
			}
			
			foreach($itemSets as $itemSet) {
				foreach($itemSet as $innerRelationship => $items) {
					if($innerRelationship == 'self') {
						switch($eloquentRelationshipType) {
							case "belongsTo":
							case "hasMany":
								$object = new $class($items);
								$object->{lcfirst($eloquentObjectClassShortName)}()->associate($eloquentObject);
								$object->save();
							break;
							case "belongsToMany":
								$object = $class::firstOrCreate($items);
								$object->{lcfirst($eloquentObjectClassShortName)}()->attach( $eloquentObject->id );
							break;
							default:
								throw new InternalErrorException("'{$eloquentRelationshipType}' eloquentRelationshipType not set in 'executeCreate' function");
						}
						
					} else {
						throw new InternalErrorException("Code for groupedMultiple with innerRelationship not yet created in 'executeCreate' function");
//						$innerClass = get_class($object->$innerRelationship()->getRelated());
//						$innerObject = new $innerClass($items);
//						$innerObject->$innerRelationship()->save($innerObject);
					}
				}
			}
		}
	}
}
