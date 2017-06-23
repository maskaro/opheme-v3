<?php

namespace oPheme\Models\Traits;

use Rhumsaa\Uuid\Uuid;

trait UuidTrait
{	
	/**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
 
        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
		static::creating(function ($model) {
            $key = $model->getKeyName();
			// Added check for array, as system can have primary composite keys
			if( is_array( $key ) ) {
				foreach( $key as $part) {
					if (empty($model->$part)) {
						$model->$part = (string) $model->generateNewUuid();
					}
				}
			} else {
				if (empty($model->$key)) {
					$model->$key = (string) $model->generateNewUuid();
				}
			}
        });
    }
 
    /**
     * Get a new version 4 (random) UUID.
     *
     * @return \Rhumsaa\Uuid\Uuid
     */
    public function generateNewUuid()
    {
        return Uuid::uuid4();
    }
}
