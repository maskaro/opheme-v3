<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistrationToken extends BaseModel
{
	use SoftDeletingTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'registrationtoken';
	
	/**
	 * allow mass assignment of object
	 */
	protected $guarded = array();
	
	/**
	 * The array to define the relationships to other models
	 * @var array 
	 */
	public $relationshipTypes = array(
		'company' => 'belongsTo',
		'team' => 'belongsToMany',
	);
	
	
	/**
	 * Company relationship.
	 * @return array
	 */
	public function company ()
	{
		return $this->{$this->relationshipTypes['company']}( 'oPheme\Models\Company', 'company_id' );
	}
	
	/**
	 * Team relationship.
	 * @return array
	 */
	public function team ()
	{
		return $this->{$this->relationshipTypes['team']}( 'oPheme\Models\Team', 'registrationtoken_team', 'registrationtoken_id', 'team_id' )->withTimestamps();
	}

}
