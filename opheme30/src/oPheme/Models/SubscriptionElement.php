<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SubscriptionElement extends BaseModel
{
	Use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'subscriptionelement';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'subscription' => 'belongsToMany',
	);
	
	/**
	 * Subscription relationship.
	 * @return array
	 */
	public function subscription ()
	{
		return $this->{$this->relationshipTypes['subscription']}( 'oPheme\Models\Subscription', 'subscription_subscriptionelement', 'subscriptionelement_id', 'subscription_id' )->withPivot('value_integer','value_double','value_string')->withTimestamps();
	}

}
