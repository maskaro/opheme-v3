<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class AuthKey extends BaseModel
{
	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'authkey';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'socialMediaPlatform' => 'belongsTo',
		'user' => 'belongsToMany',
		'userFollow' => 'hasMany',
		'autoReplyAuthKey' => 'hasMany',
		'discover' => 'belongsToMany',
		'favorite' => 'hasMany',
		'follow' => 'hasMany',
		'retweet' => 'hasMany',
	);
	
	/**
	 * SocialMediaPlatform relationship.
	 * @return array
	 */
	public function socialMediaPlatform ()
	{
		return $this->{$this->relationshipTypes['socialMediaPlatform']}( 'oPheme\Models\SocialMediaPlatform', 'socialmediaplatform_id' );
	}
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'authkey_user', 'authkey_id', 'user_id' )->withTimestamps();
	}
	
	/**
	 * UserFollow relationship.
	 * @return array
	 */
	public function userFollow ()
	{
		return $this->{$this->relationshipTypes['userFollow']}( 'oPheme\Models\UserFollow', 'authkey_id' );
	}
	
	/**
	 * AutoReplyAuthKey relationship.
	 * @return array
	 */
	public function autoReplyAuthKey ()
	{
		return $this->{$this->relationshipTypes['autoReplyAuthKey']}( 'oPheme\Models\AutoReplyAuthKey', 'authkey_id' );
	}
	
	/**
	 * Discover relationship.
	 * @return array
	 */
	public function discover ()
	{
		return $this->{$this->relationshipTypes['discover']}( 'oPheme\Models\Discover', 'authkey_discover', 'authkey_id', 'discover_id' )->withTimestamps();
	}
	
	/**
	 * Favorite relationship.
	 * @return array
	 */
	public function favorite ()
	{
		return $this->{$this->relationshipTypes['favorite']}( 'oPheme\Models\Favorite', 'authkey_id');
	}
	
	/**
	 * Follow relationship.
	 * @return array
	 */
	public function follow ()
	{
		return $this->{$this->relationshipTypes['follow']}( 'oPheme\Models\Follow', 'authkey_id');
	}
	
	/**
	 * Retweet relationship.
	 * @return array
	 */
	public function retweet ()
	{
		return $this->{$this->relationshipTypes['retweet']}( 'oPheme\Models\Follow', 'authkey_id');
	}
}
