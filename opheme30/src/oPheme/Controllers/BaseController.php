<?php

namespace oPheme\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use League\Fractal\Pagination\Cursor;
use oPheme\Classes\Traits\UserTrait;

class BaseController
	extends ApiController
{
	use UserTrait;
	
	protected function getPaginationDetails() {
		$cursorInput = Input::get( 'cursor' );
		$numberInput = Input::get( 'number' );
		$current	 = isset( $cursorInput ) ? (int) base64_decode( $cursorInput ) : 0;
		$per_page	 = isset( $numberInput ) ? (int) $numberInput : Config::get('settings.lazy_loading_per_page');

		if ( $per_page > Config::get('settings.lazy_loading_max_per_page') )
		{
			$this->errorWrongArgs( "Number cannot be greater than ".Config::get('settings.lazy_loading_max_per_page') );
		}
		
		return array( $per_page, $current );
	}
	
	protected function getCursor($collection, $per_page, $current)
	{
		$count = $collection->count();
		
		$next		 = ( $count < $per_page ) ? base64_encode( (string) ($current + $count) ) : base64_encode( (string) ($current + $per_page) );
		$prev		 = (($current - $per_page) > 0 ) ? base64_encode( (string) ($current - $per_page) ) : null;
		$cursor		 = new Cursor( $current, $prev, $next, $count );
		
		return $cursor;
	}
	
	protected function getJsonData()
	{
		if(! Request::isJson()) {
			$this->errorWrongArgs("Request not JSON");
		}
		
		$jsonData = Input::json()->get('data');
		if ( !$jsonData ) {
			$this->errorWrongArgs("Missing JSON Data");
		}
		return $jsonData;
	}	
}
