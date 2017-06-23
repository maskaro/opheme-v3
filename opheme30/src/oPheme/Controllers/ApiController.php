<?php

namespace oPheme\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Manager;
use League\Fractal\Pagination\CursorInterface;
use oPheme\Exception\CustomException;
use oPheme\Exception\ForbiddenException;
use oPheme\Exception\InternalErrorException;
use oPheme\Exception\NotFoundException;
use oPheme\Exception\UnauthorizedException;
use oPheme\Exception\BadRequestException;
use oPheme\Exception\MissingParameterException;
use oPheme\Exception\UnknownParameterException;
use oPheme\Exception\GoneException;
use oPheme\Exception\SocialMediaException;
use oPheme\Exception\UnsupportedSocialMediaException;

class ApiController
	extends Controller
{

	protected $httpStatusCode = 200;

	public function __construct ( )
	{
		$this->fractal = new Manager;

		// Are we going to try and include embedded data?
		$this->fractal->parseIncludes( explode( ',', Input::get( 'include' ) ) );
	}

	/**
	 * Getter for httpStatusCode
	 *
	 * @return mixed
	 */
	public function getHttpStatusCode ()
	{
		return $this->httpStatusCode;
	}

	/**
	 * Setter for httpStatusCode
	 *
	 * @param int $httpStatusCode Value to set
	 *
	 * @return self
	 */
	public function setHttpStatusCode ( $httpStatusCode )
	{
		$this->httpStatusCode = $httpStatusCode;
		return $this;
	}

	protected function respondWithItem ( $item, $callback )
	{
		$resource = new Item( $item, $callback );

		$rootScope = $this->fractal->createData( $resource );

		return $this->respondWithArray( $rootScope->toArray() );
	}

	protected function respondWithCollection ( $collection, $callback )
	{
		$resource = new Collection( $collection, $callback );

		$rootScope = $this->fractal->createData( $resource );

		return $this->respondWithArray( $rootScope->toArray() );
	}
	
	protected function respondWithCursor($collection, $callback, CursorInterface $cursor)
    {
        $resource = new Collection($collection, $callback);
        $resource->setCursor($cursor);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }
	
	protected function respondSimpleArray(array $array)
	{
		//wrap simple array in a data element
		$response = array( 'data' => $array );
		return $this->respondWithArray($response);
	}
	
	protected function respondWithEmpty()
	{
		return $this->respondWithArray(array());
	}

	protected function respondWithArray ( array $array, array $headers = [ ] )
	{
		$response = Response::json( $array, $this->httpStatusCode, $headers );

		return $response;
	}

	/**
	 * Throws a Custom Exception
	 */
	protected function respondWithError ( $message = 'An error occured', 
											$errorType = 'generic_error', 
											$errorCode = 100, 
											$httpStatusCode = 400,
											$redirectUri = null)
	{
		$exception = new CustomException ( $message, $errorType, $errorCode, $httpStatusCode, $redirectUri );
		if ( $httpStatusCode === 200 )
		{
			trigger_error(
				"You better have a really good reason for erroring on a 200...", E_USER_WARNING
			);
		}
		
		throw $exception;
	}

	/**
	 * Throws a Forbidden Exception
	 */
	protected function errorForbidden ( $message = 'Forbidden' )
	{
		throw new ForbiddenException( $message );
	}

	/**
	 * Throws a Internal Error Exception
	 */
	protected function errorInternalError ( $message = 'Internal Error' )
	{
		throw new InternalErrorException( $message );
	}

	/**
	 * Throws a Not Found Exception
	 */
	protected function errorNotFound ( $message = 'Resource Not Found' )
	{
		throw new NotFoundException( $message );
	}

	/**
	 * Throws an Unauthorized Exception
	 */
	protected function errorUnauthorized ( $message = 'Unauthorized' )
	{
		throw new UnauthorizedException( $message );
	}

	/**
	 * Throws an Bad Request Exception
	 */
	protected function errorWrongArgs ( $message = 'Bad Request' )
	{
		throw new BadRequestException( $message );
	}
	
	/**
	 * Throws a Gone Exception
	 * @param string $message
	 * @throws GoneException
	 */
	protected function errorGone ( $message = "Resource Gone")
	{
		throw new GoneException( $message );
	}
	
	/**
	 * Throws a Missing Parameter Exception
	 */
	protected function errorMissingParameter ( $parameter )
	{
		throw new MissingParameterException( $parameter );
	}
	
	/**
	 * Throws a Unknown Parameter Exception
	 */
	protected function errorUnknownParameter ( $parameter )
	{
		throw new UnknownParameterException( $parameter );
	}
	
	/**
	 * Throws a Social Media Exception
	 */
	protected function errorSocialMedia ( $message = 'Social media error' )
	{
		throw new SocialMediaException( $message );
	}
	
	/**
	 * Throws a Unsupported Social Media Exception
	 */
	protected function errorUnsupportedSocialMedia ( $message = 'Unsupported social media')
	{
		throw new UnsupportedSocialMediaException( $message );
	}
}
