<?php

namespace oPheme\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use oPheme\Models\Traits\ValidationTrait;
use oPheme\Models\Traits\UuidTrait;
use oPheme\Models\Traits\CompositeKeyTrait;

class BaseModel	Extends Eloquent
{
	use ValidationTrait, UuidTrait, CompositeKeyTrait;

	/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
	
	
}
