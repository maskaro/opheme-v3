<?php

namespace oPheme\Models;

use Jenssegers\Mongodb\Model as Moloquent;
use oPheme\Models\Traits\ValidationTrait;
use oPheme\Models\Traits\CompositeKeyTrait;

class BaseMongoMoloquentModel extends Moloquent
{
	use ValidationTrait, CompositeKeyTrait;
	
	/**
	 * Moloquent specific connection details
	 */
	protected $connection = 'mongodb';
	
}
