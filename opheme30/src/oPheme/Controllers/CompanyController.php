<?php

namespace oPheme\Controllers;

use oPheme\Transformer\CompanyTransformer;
use oPheme\Models\Company;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use League\Fractal\Pagination\Cursor;

class CompanyController
	extends ApiController
{
	// List
	public function adminList ()
	{
		$cursorInput = Input::get( 'cursor' );
		$numberInput = Input::get( 'number' );
		$current	 = isset( $cursorInput ) ? (int) base64_decode( $cursorInput ) : 0;
		$per_page	 = isset( $numberInput ) ? (int) $numberInput : Config::get('settings.lazy_loading_per_page');

		if ( $per_page > 100 )
		{
			return $this->errorWrongArgs( "Number cannot be greater than 100" );
		}

		$companies = Company::limit( $per_page )
			->skip( $current )
			->get();
		
		$count = $companies->count();
		
		$next		 = ( $count < $per_page ) ? base64_encode( (string) ($current + $count) ) : base64_encode( (string) ($current + $per_page) );
		$prev		 = (($current - $per_page) > 0 ) ? base64_encode( (string) ($current - $per_page) ) : null;
		$cursor		 = new Cursor( $current, $prev, $next, $count );

		return $this->respondWithCursor( $companies, new CompanyTransformer, $cursor );
	}

	// Create
	public function adminCreate ()
	{
		
	}

	//Read
	public function adminRead ( $companyId )
	{
		$company = Company::find( $companyId );
		
		if ( !$company )
		{
			return $this->errorNotFound('Company not found');
		}
		
		return $this->respondWithItem( $company,  new CompanyTransformer );
	}

	// Update (PUT)
	public function adminUpdateFull ( $companyId )
	{
		
	}
	
	// Update (PATCH)
	public function adminUpdatePartial ( $companyId )
	{
		
	}

	// Delete
	public function adminDelete ( $companyId )
	{
		
	}

}
