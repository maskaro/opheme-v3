<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Interaction extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'interaction';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'discoverMessage' => 'belongsTo',
		'interactionMessage' => 'hasMany',
	);
	
	/**
	 * DiscoverMessage relationship.
	 * @return array
	 */
	public function discoverMessage ()
	{
		return $this->{$this->relationshipTypes['discoverMessage']}( 'oPheme\Models\DiscoverMessage', 'discovermessage_id' );
	}
	
	/**
	 * InteractionMessage relationship.
	 * @return array
	 */
	public function interactionMessage ()
	{
		return $this->{$this->relationshipTypes['interactionMessage']}( 'oPheme\Models\InteractionMessage', 'interaction_id' );
	}

}
