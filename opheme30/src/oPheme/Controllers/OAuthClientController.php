<?php

namespace oPheme\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Util\RedirectUri;
use oPheme\Classes\OAuth\Manager;
use oPheme\Exception\AuthorisationFailedException;
use oPheme\Exception\BadRequestException;
use oPheme\Exception\OphemeException;
use oPheme\Exception\InternalErrorException;
use oPheme\Exception\NotFoundException;
use oPheme\Models\AuthKey;
use oPheme\Models\SocialMediaPlatform;
use oPheme\Models\SingleUseAuthoriseCode;

class OAuthClientController
	extends BaseController
{
	/**
	 * The Provider Manager instance
	 *
	 * @param oPheme\OAuth\Manager
	 */
	protected $oAuthManager;
	
	protected $callback;

	/**
	 * Create a new instance of the OAuthController
	 *
	 * @return void
	 */
	public function __construct (Manager $oAuthManager)
	{	
		parent::__construct();
		$this->registerErrorHandlers();
		$this->beforeFilter('opheme-oauth', array( 'only' =>
			array( 'singleUseAuthoriseCode' ) ) );
		$this->beforeFilter('opheme-oauth:basic', array( 'only' =>
			array( 'singleUseAuthoriseCode' ) ) );
		$this->beforeFilter('opheme-oauth-owner:client', array( 'only' =>
			array( 'singleUseAuthoriseCode' ) ) );
		$this->oAuthManager = $oAuthManager;
	}
	
	/**
	 * Retrieve a single use token for authorising
	 * 
	 * @return string
	 */
	public function singleUseAuthoriseCode()
	{
		do {
			$code = str_random(40);
			$count = SingleUseAuthoriseCode::where('code', '=', $code)->count();
		} while ( $count > 0 );
		
		// Create a new single use authorise code in the database
		$singleUseAuthoriseCode = new SingleUseAuthoriseCode;
		$singleUseAuthoriseCode->code = $code;
		$singleUseAuthoriseCode->expire_time = strtotime("+2 minute");
		$singleUseAuthoriseCode->save();
		
		$expires_in = $singleUseAuthoriseCode->expire_time - time();
		if($expires_in < 0) {
			$expires_in = 0;
		}
		$responseArray = array("data" => 
			array(
				"authorise_code" => $singleUseAuthoriseCode->code,
				"expires_in" => $expires_in
			)
		);

		return $this->respondWithArray( $responseArray );		
	}
	
	/**
	 * Authorise an authentication request
	 *
	 * @return Redirect
	 */
	public function authorise ( $providerName )
	{
		$callback = $this->getInput( 'callback' );
		$this->callback = $this->addScheme($callback);

		$parsedUrl = parse_url($this->callback);
		// check callback is reachable
		if ( !checkdnsrr( $parsedUrl['host'] , "ANY") ) {
			$this->callback = null;
			throw new BadRequestException( "Invalid callback" );
		}

		Session::put('callback', $this->callback);

		$userId = $this->getInput( 'user_id' );

		// get the user details
		$user = $this->getUser($userId);
		Session::put('user', $user);

		// check the single use authorise code
		$code = $this->getInput( 'authorise_code' );
		$singleUseAuthoriseCode = SingleUseAuthoriseCode::where( 'code', '=', $code )
					->where( 'expire_time', '>', time() );
		// see if the code was found
		if($singleUseAuthoriseCode->count() != 1) {
			throw new NotFoundException( "authorise_code not found or expired" );
		}
		// remove the authorised code
		$singleUseAuthoriseCode->delete();

		$provider = $this->oAuthManager->get( $providerName );
		switch ( $providerName )
		{
			// oauth1
			case 'twitter':
				return $this->authoriseOAuth1( $provider );

			// oauth2
			case 'instagram':
				return $this->authoriseOAuth2( $provider );

			default:
				throw new BadRequestException( "The ' {$providerName} ' Social Media Platform has not been configured yet." );
		}
	}

	/**
	 * Receive the callback from the authentication provider
	 *
	 * @return Redirect
	 */
	public function callback ( $providerName )
	{
		$this->callback = Session::pull( 'callback' );
		$provider = $this->oAuthManager->get( $providerName );

		switch ( $providerName ) {
			// oauth1
			case 'twitter':
				if( Input::has('denied') ) {
					throw new AuthorisationFailedException();
				}
				$token = $provider->getTokenCredentials(
					Session::pull( 'credentials' ), Input::get( 'oauth_token' ), Input::get( 'oauth_verifier' )
				);
				$token_public	 = $token->getIdentifier();
				$token_secret	 = $token->getSecret();
				break;

			// oauth2
			case 'instagram':
				if( Input::has('error') ) {
					throw new AuthorisationFailedException( Input::get('error_description') );
				}
				$token = $provider->getAccessToken( 'authorization_code', [
					'code' => Input::get( 'code' )
					] );

				$token_public	 = $token->accessToken;
				$token_secret	 = null;
				break;

			default:
				throw new InternalErrorException("Unknown Social Media Platform.");
		}

		$providerUser = $provider->getUserDetails( $token );

		$authKeyData = array(
			"socialmediaplatform_id" => SocialMediaPlatform::where( 'name', $providerName )->first()->id,
			"screen_name" => $providerUser->nickname,
		);

		$user = Session::pull( 'user' );
		$authKey = AuthKey::firstOrNew($authKeyData);
		if( !$authKey->exists ) {
			$authKey->token = $token_public;
			$authKey->token_secret = $token_secret;
			$authKey->save();
			$authKey->user()->save( $user );
		} else {
			$authKey->token = $token_public; // set the new data
			$authKey->token_secret = $token_secret; // set the new data
			$authKey->valid = 1;
			$authKey->last_validity_check = null;
			$authKey->save();
			if( !$authKey->user->contains( $user->id ) ) {
				$authKey->user()->save( $user );
			}
		}

		return new RedirectResponse(
			RedirectUri::make(
				$this->callback,
				[
					'response' => json_encode( array('data' => array('status' => 'success') ) )
				]
			)
		);
	}
	
	private function getInput($field)
	{
		$input = Input::get( $field );
		if ( !$input ) {
			throw new BadRequestException( "Missing {$field}" );
		}
		return $input;
	}
	
	private function addScheme($url, $scheme = 'http://')
	{
		return parse_url($url, PHP_URL_SCHEME) === null ?
		$scheme . $url : $url;
	}
	
	private function getOAuthExceptionErrorCode($errorType)
	{
		return Config::get('oauth2errorcode.'.$errorType);
	}
	
	private function authoriseOAuth1 ( $provider )
	{
		$credentials = $provider->getTemporaryCredentials();
		Session::put( 'credentials', $credentials );
		Session::save();

		return $provider->authorize( $credentials );
	}

	private function authoriseOAuth2 ( $provider )
	{
		return Redirect::away( $provider->getAuthorizationUrl() );
	}
	
	private function registerErrorHandlers()
	{
		// Grab exception if class is OphemeException
		App::error(function(OphemeException $e) {
			$dataArray = array(
				'status' => 'fail',
				'error' =>  $e->errorType,
				'error_code' => $e->getCode(),
				'error_description' =>  $e->getMessage()
			);
			if( isset( $this->callback ) ) {
				return new RedirectResponse(
					RedirectUri::make(
						$this->callback,
						[
							'response' => json_encode( $dataArray )
						]
					)
				);
            } elseif ( Request::server('HTTP_REFERER') !== null ) {
				$parsedReferer = parse_url(Request::server('HTTP_REFERER'));
				$referer = $parsedReferer['scheme'] 
							. "://" 
							. $parsedReferer['host']
							. $parsedReferer['path'];
				return new RedirectResponse(
					RedirectUri::make(
						$referer,
						[
							'response' => json_encode( $dataArray )
						]
					)
				);
			} else {
				return new JsonResponse(
						$dataArray,
                        $e->httpStatusCode,
                        $e->getHttpHeaders()
                );
            }
        });
		
		// Grab exception if class is OAuthException
		App::error(function(OAuthException $e) {
			$dataArray = array(
				'status' => 'fail',
				'error' =>  $e->errorType,
				'error_code' => $this->getOAuthExceptionErrorCode($e->errorType),
				'error_description' =>  $e->getMessage()
			);
            if( isset( $this->callback ) ) {
				return new RedirectResponse(
					RedirectUri::make(
						$this->callback,
						[
							'response' => json_encode( $dataArray )
						]
					)
				);
             } elseif ( Request::server('HTTP_REFERER') !== null ) {
				$parsedReferer = parse_url(Request::server('HTTP_REFERER'));
				$referer = $parsedReferer['scheme'] 
							. "://" 
							. $parsedReferer['host']
							. $parsedReferer['path'];
				return new RedirectResponse(
					RedirectUri::make(
						$referer,
						[
							'response' => json_encode( $dataArray )
						]
					)
				);
			} else {
				return new JsonResponse(
						$dataArray,
                        $e->httpStatusCode,
                        $e->getHttpHeaders()
                );
            }
        });
	}
}
