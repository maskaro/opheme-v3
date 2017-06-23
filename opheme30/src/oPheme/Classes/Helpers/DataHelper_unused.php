<?php

namespace oPheme\Classes\Helpers;

use Illuminate\Support\Facades\Facade;

class DataHelper_unused
	extends Facade
{

	protected static $storage = array();

	protected static function getFacadeAccessor ()
	{
		return 'datahelper';
	}

	/**
	 * Store data for future use in current app instance.
	 * @param string $key Dot notation (structured) for the data, eg: 'user.extra'.
	 * @param mixed $data Any type of data.
	 */
	public static function store ( $key, $data )
	{
		self::$storage = array_add( self::$storage, $key, $data );
	}

	/**
	 * Retrieve data from storage.
	 * @param string $key Dot notation (structured) for the data, eg: 'user.extra'.
	 * @return mixed Data from storage, NULL if it does not exist.
	 */
	public static function get ( $key )
	{
		return array_get( self::$storage, $key, null );
	}
	
	/**
	 * Retrieve and remove data from storage.
	 * @param string $key Dot notation (structured) for the data, eg: 'user.extra'.
	 * @return mixed Data from storage, NULL if it does not exist.
	 */
	public static function getAndForget ( $key )
	{
		return array_pull( self::$storage, $key, null );
	}

	/**
	 * Check storage for particular data.
	 * @param string $key Dot notation (structured) for the data, eg: 'user.extra'.
	 * @return boolean TRUE if exists, FALSE otherwise.
	 */
	public static function exists ( $key )
	{
		return !is_null( array_get( self::$storage, $key, null ) );
	}
	
	/**
	 * Remove data from storage.
	 * @param string $key Dot notation (structured) for the data, eg: 'user.extra'.
	 */
	public static function remove($key) {
		array_forget(self::$storage, $key);
	}

}
