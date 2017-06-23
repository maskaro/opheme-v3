<?php

namespace oPheme\Models;

use Jenssegers\Eloquent\Model as Moloquent;
use oPheme\Models\Traits\ValidationTrait;
use oPheme\Models\Traits\UuidTrait;
use oPheme\Models\Traits\CompositeKeyTrait;

class BaseMysqlMoloquentModel extends Moloquent
{
	use ValidationTrait, UuidTrait, CompositeKeyTrait;
	
	/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

	/**
	 * Moloquent specific connection details
	 */
	protected $connection = 'mysql';
	
}
