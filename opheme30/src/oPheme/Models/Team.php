<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Team extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'team';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'user' => 'belongsToMany',
		'company' => 'belongsTo',
		'discover' => 'belongsToMany',
		'registrationToken' => 'belongsToMany',
	);
	
	/**
	 * User relationship.
	 * @return array
	 */
	public function user ()
	{
		return $this->{$this->relationshipTypes['user']}( 'oPheme\Models\User', 'team_user', 'team_id', 'user_id' )->withTimestamps();
	}

	/**
	 * Company relationship.
	 * @return array
	 */
	public function company ()
	{
		return $this->{$this->relationshipTypes['company']}( 'oPheme\Models\Company', 'company_id', 'id' );
	}
	
	/**
	 * Discover relationship.
	 * @return array
	 */
	public function discover ()
	{
		return $this->{$this->relationshipTypes['discover']}( 'oPheme\Models\Discover', 'discover_team', 'team_id', 'discover_id' )->withTimestamps();
	}
	
	/**
	 * RegistrationToken relationship.
	 * @return array
	 */
	public function registrationToken ()
	{
		return $this->{$this->relationshipTypes['registrationToken']}( 'oPheme\Models\RegistrationTokens', 'registrationtoken_team', 'team_id', 'registrationtoken_id' )->withTimestamps();
	}

}
