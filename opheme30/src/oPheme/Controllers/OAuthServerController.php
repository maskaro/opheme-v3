<?php

namespace oPheme\Controllers;

use Illuminate\Support\Facades\Response;
use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;

class OAuthServerController
	extends BaseController
{		
	/**
	 * Issue an access token
	 *
	 * @return access token
	 */
	public function issueAccessToken ( )
	{
		return Response::json(Authorizer::issueAccessToken());
	}
}
