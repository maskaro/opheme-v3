<?php
	
	//always redirect to Account when logic is done
	$site->url->redirectTo('/account');
	
	switch ($task) {
	
		case 'twitter':
			
			//get oauth token from twitter
			$oauth_token = (empty($_get['oauth_token'])?null:$_get['oauth_token']);
			
			//if the oauth_token is old redirect to the connect page.
			if ($oauth_token && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] !== $oauth_token) {

				$site->message->set('callback', 'OK', 'twitter_OAuth', 'Current Twitter OAuth Token is old. Please try again.');
				break;

			} else {
				
				switch ($extra) {
					
					case 'authorise-request':
						
						//if (!empty($_post['token-name'])) {
							
							//$_SESSION['twitter-token-name'] = $_post['token-name'];
							//$_SESSION['twitter-token-name'] = '';
						
							//create TwitterOAuth object with client credentials.
							$site->twitter->startOAuth();
							
							//callback link
							$request_callback = __oOAUTH_CALLBACK__ . '/twitter/save-token';
							
							//get temporary credentials.
							$request_token = $site->twitter->oauth->getRequestToken($request_callback);

							//save temporary credentials to session.
							$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
							$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

							//if last connection failed don't display authorization link.
							switch ($site->twitter->oauth->http_code) {

								case 200:

									//build authorize URL
									$url = $site->twitter->oauth->getAuthorizeURL($token);

									//redirect user to Twitter.
									$site->url->redirectTo($url);
									break;

								default:

									//save HTTP status for error dialog on connnect page.
									$site->message->set('callback', 'WAR', 'twitter_OAuth', 'Failed to obtain authorization from Twitter. Please try again. OAuth Error Code: ' . $site->twitter->oauth->http_code . '.');
									break;

							}
							
						//} else { $site->message->set('callback', 'WAR', 'twitter_OAuth', 'You must specify an Account Name for a new Twitter Authorisation request. Please try again.'); }
						
						break;
					
					case 'save-token':
						
						//misusage
						if (!isset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret'], $_get['oauth_verifier']/*, $_SESSION['twitter-token-name']*/)) {
							$site->message->set('callback', 'WAR', 'twitter_OAuth', 'There is no information about Twitter OAuth Token.');
							break;
						}

						//create TwitteroAuth object with app key/secret and token key/secret from default phase
						$site->twitter->startOAuth($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

						//request access tokens from twitter
						$oauth_verifier = $_get['oauth_verifier'];
						$access_token = $site->twitter->oauth->getAccessToken($oauth_verifier);

						//if HTTP response is 200 continue otherwise send to connect page to retry
						if ($site->twitter->oauth->http_code === 200) {

							//remove no longer needed request tokens
							unset($_SESSION['oauth_token']);
							unset($_SESSION['oauth_token_secret']);
							
							$tokenName = null;//$_SESSION['twitter-token-name']; unset($_SESSION['twitter-token-name']);
							
							$result = $site->twitter->saveToken($site->user->get('account', 'id'), $tokenName, $access_token);
							
							//the user has been verified and the access tokens have been saved for future use
							if ($result) {
								
								$totalTokens = 0;
								foreach ($availableSMModules as $smModule) {
									$totalTokens += $site->{$smModule}->countTokens('user', $site->user->get('account', 'id'));
								}
								
								if ($totalTokens === 1) {
									foreach ($availableJModules as $jModule) {
										$jobs = $site->job->getSpecs(array('jobType' => $jModule, 'userId' => $site->user->get('account', 'id')));
										foreach ($jobs as $key => $job) {
											$data = array(
												'name' => 'addAuthKeys',
												'type' => 'mysql',
												'table' => 'app.jobs.tokens',
												'operation' => 'insert',
												'columns' => array(
													'user_id' => $site->user->get('account', 'id'),
													'job_type' => $jModule,
													'job_id' => $job['id'],
													'token_type' => 'twitter',
													'token_id' => $result
												)
											);
											$site->database->query($data)->fetch()['addAuthKeys'];
										}
									}
								}
								
								$addonMsg = '';
								
								$emailData = array(
									'email' => array (
										'template' => 'twitter_linked_account_success',
										'subject' => 'You have successfully linked your Twitter Account to your ' . __oCompanyBrand__ . ' Account!',
										'description' => 'Twitter Account link to your ' . __oCompanyBrand__ . ' Account confirmation.',
										//'tokenName' => $tokenName,
										'tokenName' => $site->twitter->getUserTokens('one', $result, true)['screen_name'],
										'email' => $site->user->get('account', 'email')
									)
								);

								//attempt to send confirmation email
								if (!$site->email->sendEmail($emailData)) {
									$addonMsg = ', however, Email confirmation could not be sent';
								}
								
								//refresh tokens
								$_SESSION[__oSESSION_USER_PARAM__]['authorisation']['twitter'] = $site->twitter->getUserTokens('all', $site->user->get('account', 'id'), true);
								
								//successfully linked account
								$site->message->set('callback', 'OK', 'twitter_linked_account', 'Successfully linked Twitter account' . $addonMsg . '.');
								
							} elseif ($result === null) {
								$site->message->set('callback', 'WAR', 'twitter_token_exists', 'Sorry, but you have already linked this Twitter Account with ' . __oCompanyBrand__ . '.');
							} else {
								$site->message->set('callback', 'WAR', 'database_access', 'Database error.');
							}

						} else {
							//save HTTP status for error dialog on connnect page.
							$site->message->set('callback', 'WAR', 'twitter_OAuth', 'Failed to obtain authorization from Twitter. Please try again. OAuth Error Code: ' . $site->twitter->oauth->http_code . '.');
						}
						
						break;
					
					default:
						$site->message->set('callback', 'WAR', 'twitter', 'No action requested. Nothing to do.');
						break;
					
				}

			}
			
			break;
			
		case 'instagram':
			
			switch ($extra) {
					
				case 'authorise-request':

					//if (!empty($_post['token-name'])) {

						//$_SESSION['instagram-token-name'] = $_post['token-name'];
						//$_SESSION['instagram-token-name'] = '';

						//create instagramOAuth object
						$site->instagram->startOAuth();

						//build authorize URL
						$url = $site->instagram->oauth->getLoginUrl(array('basic', 'relationships', 'likes', 'comments'));

						//redirect user to Instagram
						$site->url->redirectTo($url);

					//} else { $site->message->set('callback', 'WAR', 'instagram_OAuth', 'You must specify an Account Name for a new Instagram Authorisation request. Please try again.'); }

					break;
					
				case 'save-token':

					//misusage
					if (!isset($_get['code'])) {
						$site->message->set('callback', 'WAR', 'instagram_OAuth', 'There is no information about Instagram OAuth Token.');
						break;
					}
					
					//create instagramOAuth object
					$site->instagram->startOAuth();
					
					// Grab OAuth callback code
					$code = $_get['code'];
					
					// get the token
					$data = $site->instagram->oauth->getOAuthToken($code);

					//if auth succeeded
					if(!empty($data->user->username)) {

						// store token
						$result = $site->instagram->saveToken($site->user->get('account', 'id'), $data->user->username, $data->access_token);

						//the user has been verified and the access tokens have been saved for future use
						if ($result) {

							$totalTokens = 0;
							foreach ($availableSMModules as $smModule) {
								$totalTokens += $site->{$smModule}->countTokens('user', $site->user->get('account', 'id'));
							}

							if ($totalTokens === 1) {
								foreach ($availableJModules as $jModule) {
									$jobs = $site->job->getSpecs(array('jobType' => $jModule, 'userId' => $site->user->get('account', 'id')));
									foreach ($jobs as $key => $job) {
										$data = array(
											'name' => 'addAuthKeys',
											'type' => 'mysql',
											'table' => 'app.jobs.tokens',
											'operation' => 'insert',
											'columns' => array(
												'user_id' => $site->user->get('account', 'id'),
												'job_type' => $jModule,
												'job_id' => $job['id'],
												'token_type' => 'instagram',
												'token_id' => $result
											)
										);
										$site->database->query($data)->fetch()['addAuthKeys'];
									}
								}
							}

							$addonMsg = '';

							$emailData = array(
								'email' => array (
									'template' => 'instagram_linked_account_success',
									'subject' => 'You have successfully linked your Instagram Account to your ' . __oCompanyBrand__ . ' Account!',
									'description' => 'Instagram Account link to your ' . __oCompanyBrand__ . ' Account confirmation.',
									//'tokenName' => $tokenName,
									'tokenName' => $data->user->username,
									'email' => $site->user->get('account', 'email')
								)
							);

							//attempt to send confirmation email
							if (!$site->email->sendEmail($emailData)) {
								$addonMsg = ', however, Email confirmation could not be sent';
							}

							//refresh tokens
							$_SESSION[__oSESSION_USER_PARAM__]['authorisation']['instagram'] = $site->instagram->getUserTokens('all', $site->user->get('account', 'id'), true);

							//successfully linked account
							$site->message->set('callback', 'OK', 'instagram_linked_account', 'Successfully linked Instagram account' . $addonMsg . '.');

						} elseif ($result === null) {
							$site->message->set('callback', 'WAR', 'instagram_token_exists', 'Sorry, but you have already linked this Instagram Account with ' . __oCompanyBrand__ . '.');
						} else {
							$site->message->set('callback', 'WAR', 'database_access', 'Database error.');
						}

					} else {
						//save HTTP status for error dialog on connnect page.
						$site->message->set('callback', 'WAR', 'instagram_OAuth', 'Failed to obtain authorization from Instagram. Please try again.');
					}

					break;

				default:
					$site->message->set('callback', 'WAR', 'instagram', 'No action requested. Nothing to do.');
					break;

			}
			
			break;
			
		default:
			break;
		
	}