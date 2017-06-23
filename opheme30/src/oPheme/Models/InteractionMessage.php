<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class InteractionMessage extends BaseMysqlMoloquentModel
{
	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'interactionmessage';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'interaction' => 'belongsTo',
		'socialMediaPlatform' => 'belongsTo',
		'message' => 'belongsTo',
	);
	
	/**
	 * Interaction relationship.
	 * @return array
	 */
	public function interaction ()
	{
		return $this->{$this->relationshipTypes['interaction']}( 'oPheme\Models\Interaction', 'interaction_id' );
	}

	/**
	 * SocialMediaPlatform relationship.
	 * @return array
	 */
	public function socialMediaPlatform ()
	{
		return $this->{$this->relationshipTypes['socialMediaPlatform']}( 'oPheme\Models\SocialMediaPlatform', 'socialmediaplatform_id' );
	}
	
	/**
	 * Message relationship.
	 * @return array
	 */
	public function message ()
	{
		return $this->{$this->relationshipTypes['message']}( 'oPheme\Models\Message', 'backend_message_id', '_id' );
	}
}
