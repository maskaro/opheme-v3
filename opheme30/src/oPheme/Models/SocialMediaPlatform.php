<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SocialMediaPlatform extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'socialmediaplatform';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'authKey' => 'hasMany',
		'discoverMessage' => 'hasMany',
		'interactionMessage' => 'hasMany',
	);
	
	/**
	 * Fetch tokens associated with current SocialMediaPlatform.
	 * 
	 * @return Eloquent
	 */
	public function authKey ()
	{
		return $this->{$this->relationshipTypes['authKey']}( 'oPheme\Models\AuthKey', 'socialmediaplatform_id' );
	}
	
	/**
	 * DiscoverMessage relationship.
	 * @return array
	 */
	public function discoverMessage ()
	{
		return $this->{$this->relationshipTypes['discoverMessage']}( 'oPheme\Models\DiscoverMessage', 'socialmediaplatform_id' );
	}
	
	/**
	 * InteractionMessage relationship.
	 * @return array
	 */
	public function interactionMessage ()
	{
		return $this->{$this->relationshipTypes['interactionMessage']}( 'oPheme\Models\InteractionMessage', 'socialmediaplatform_id' );		
	}

}
