<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Discover extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'discover';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'team' => 'belongsToMany',
		'user' => 'belongsToMany',
		'discoverAutoReply' => 'hasOne',
		'discoverMessage' => 'hasMany',
		'keyword' => 'belongsToMany',
		'discoverDay' => 'hasMany',
		'discoverTimeperiod' => 'hasMany',
		'authKey' => 'belongsToMany',
		'interaction' => 'hasMany'
	);
	
	/**
	 * The list of validation rules
	 * @var array $validationRules 
	 */
	protected $validationRules = array(
		'latitude' => [ 'numeric', 'between:-90,90' ],
		'longitude' => [ 'numeric', 'between:-180,180' ],
		'radius' => [ 'numeric', 'between:0.1,10' ],
		'start_date' => [ 'date_format:Y-m-d H:i:s' ],
		'end_date' => [ 'date_format:Y-m-d H:i:s' ],
	);
	
	/**
	 * Team relationship.
	 * @return array
	 */
	public function team ()
	{
		return $this->{$this->relationshipTypes['team']}( 'oPheme\Models\Team', 'discover_team', 'discover_id', 'team_id' )->withTimestamps();
	}
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'discover_user', 'discover_id', 'user_id' )->withTimestamps();
	}

	/**
	 * DiscoverAutoReply relationship.
	 * @return array
	 */
	public function discoverAutoReply ()
	{
		return $this->{$this->relationshipTypes['discoverAutoReply']}( 'oPheme\Models\DiscoverAutoReply', 'discover_id' );
	}
	
	/**
	 * DiscoverMessage relationship.
	 * @return array
	 */
	public function discoverMessage ()
	{
		return $this->{$this->relationshipTypes['discoverMessage']}( 'oPheme\Models\DiscoverMessage', 'discover_id' );
	}
	
	/**
	 * Keyword relationship.
	 * @return array
	 */
	public function keyword ()
	{
		return $this->{$this->relationshipTypes['keyword']}( 'oPheme\Models\Keyword', 'discover_keyword', 'discover_id', 'keyword_id' )->withTimestamps();
	}
	
	/**
	 * DiscoverDay relationship.
	 * @return array
	 */
	public function discoverDay ()
	{
		return $this->{$this->relationshipTypes['discoverDay']}( 'oPheme\Models\DiscoverDay', 'discover_id' );
	}
	
	/**
	 * DiscoverTimeperiod relationship.
	 * @return array
	 */
	public function discoverTimeperiod ()
	{
		return $this->{$this->relationshipTypes['discoverTimeperiod']}( 'oPheme\Models\DiscoverTimeperiod', 'discover_id' );
	}
	
	/**
	 * AuthKey relationship.
	 * @return array
	 */
	public function authKey ()
	{
		return $this->{$this->relationshipTypes['authKey']}( 'oPheme\Models\AuthKey', 'authkey_discover', 'discover_id', 'authkey_id' )->withTimestamps();	
	}
	
	/**
	 * Interaction relationship.
	 * @return array
	 */
	public function interaction ()
	{
		return $this->{$this->relationshipTypes['interaction']}( 'oPheme\Models\Interaction', 'discover_id');	
	}
}
