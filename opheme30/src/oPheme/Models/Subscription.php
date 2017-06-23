<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Subscription extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'subscription';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'company' => 'hasMany',
		'subscriptionElement' => 'belongsToMany',
	);
	
	public function company ()
	{
		return $this->{$this->relationshipTypes['company']}( 'oPheme\Models\Company', 'subscription_id' );
	}
	
	/**
	 * SubscriptionElement relationship.
	 * @return array
	 */
	public function subscriptionElement ()
	{
		return $this->{$this->relationshipTypes['subscriptionElement']}( 'oPheme\Models\SubscriptionElement', 'subscription_subscriptionelement', 'subscription_id', 'subscriptionelement_id' )->withPivot('value_integer','value_double','value_string')->withTimestamps();
	}

}
