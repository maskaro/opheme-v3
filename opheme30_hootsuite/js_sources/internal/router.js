define (
	[
		"underscore",
		"backbone",
		"backbonemvc",
		"app",
		"Models/api_info"
	], function ( _,
	              Backbone,
	              BackboneMVC,
	              app,
	              APIInfoModel )
	{

		"use strict";

		return BackboneMVC.Router.extend (
			{

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "Router.initialize is running." ] );
					}

				},

				routes: {
					"": "showIndex"
				},

				showIndex: function ()
				{

					var currentRequest = Backbone.history.fragment.split ( "?" );

					// remember the stream location
					if ( !app.initialRequestedPage ) {

						// save initial page
						app.initialRequestedPage = currentRequest[ 0 ];

						// hootsuite is giving info for PHP, save it for later
						app.hootsuite.securityInfo = currentRequest[ 1 ];

						// get rid of the hootsuite security info from URL
						this.navigate(app.initialRequestedPage);

					}

					//// set initial requested page to currentURL, if there
					//if (app.hootsuite.currentURL.length) {
					//
					//	app.initialRequestedPage = app.hootsuite.currentURL;
					//
					//}

					if ( DEBUG ) {
						app.logThis ( [ "Router.showIndex is running. Current Request: ", currentRequest ] );
					}

					// hootsuite
					if ( currentRequest[ 0 ] !== "login/exit" && currentRequest[ 0 ] !== "login/view" && /* window.opheme_user_key && window.opheme_user_key.length && */ !app.session.storage.user.get ( "id" ) ) {

						hsp.getData (
							function ( data )
							{

								if ( DEBUG ) {
									app.logThis ( [ "Router.showIndex.hsp.getData: Stream saved data:", data ] );
								}

								if (data === null || !data.userKey || !data.userKey.length) {

									if ( DEBUG ) {
										app.logThis ( [ "Router.showIndex.hsp.getData: No stream data available. Sending to login/view." ] );
									}

									app.router.navigate (
										"login/view", {
											trigger: true,
											replace: true
										}
									);

									return;

								}

								app.hootsuite = data;

								if ( data.userKey ) {

									if ( DEBUG ) {
										app.logThis ( [ "Router.showIndex: API key detected. Attempting login..." ] );
									}

									// create api info model
									app.session.storage.apiInfo = new APIInfoModel (
										{
											"access_token": data.userKey
										}
									);

									app.session.loginWithKey (
										{
											success: function ( user, smHandles, discovers )
											{

												if ( DEBUG ) {
													app.logThis ( [ "Router.showIndex: Login successful. Data: ", user, smHandles, discovers ] );
												}

												if ( data.currentURL ) {

													if ( DEBUG ) {
														app.logThis ( [ "Router.showIndex: Sending user to: ", data.currentURL ] );
													}

													if (data.currentDiscoverId && data.currentDiscoverId.length) {

														app.session.selectCurrentDiscover(data.currentDiscoverId);

													}

													app.router.navigate (
														data.currentURL, {
															trigger: true,
															replace: true
														}
													);

												}
												else {

													if ( DEBUG ) {
														app.logThis ( [ "Router.showIndex: Sending user to: ", app.initialRequestedPage ] );
													}

													app.router.navigate (
														app.initialRequestedPage, {
															trigger: true,
															replace: false
														}
													);

												}

											},
											error  : function ( error )
											{

												if ( DEBUG ) {
													app.logThis ( [ "Router.showIndex: Login error: ", error, ". Redirecting to: login/exit." ] );
												}

												app.showAlert ( "Failed to login. Reason: " + error, "error" );

												app.router.navigate (
													"login/exit", {
														trigger: true,
														replace: true
													}
												);

											}
										}
									);

								}

							}
						);

					}
					else {

						if ( app.session.storage.user.get ( "id" ) ) {

							if ( DEBUG ) {
								app.logThis ( [ "Router.showIndex: User is AUTHENTICATED." ] );
							}

							if ( app.session.storage.user.get ( "is_valid" ) === true ) {

								if ( DEBUG ) {
									app.logThis ( [ "Router.showIndex: User is VALID." ] );
								}

								if ( app.hootsuite.currentURL ) {

									if ( DEBUG ) {
										app.logThis ( [ "Router.showIndex: Sending user to: ", app.hootsuite.currentURL ] );
									}

									app.router.navigate (
										app.hootsuite.currentURL, {
											trigger: true,
											replace: true
										}
									);

								} else {

									if ( DEBUG ) {
										app.logThis ( [ "Router.showIndex: Sending to " + app.initialRequestedPage ] );
									}

									app.router.navigate (
										app.initialRequestedPage, {
											trigger: true,
											replace: false
										}
									);

								}

							}
							else {

								if ( DEBUG ) {
									app.logThis ( [ "Router.showIndex: User is NOT VALID, sending to " + app.config.modules.invalidAccount + "/view." ] );
								}

								app.router.navigate (
									app.config.modules.invalidAccount + "/view", {
										trigger: true,
										replace: false
									}
								);

							}

						}
						else {

							if ( DEBUG ) {
								app.logThis ( [ "Router.showIndex: User is NOT AUTHENTICATED. Sending to login/view." ] );
							}

							app.router.navigate (
								"login/view", {
									trigger: true,
									replace: true
								}
							);

						}

					}

				},

				"404": function ()
				{

					var path = (
						this.current ()
					).fragment.join ( "/" );

					if ( DEBUG ) {
						app.logThis ( [ "Router.404: ", path, " not found. Sending User to Error 404." ] );
					}

					app.router.navigate (
						"error/404/" + path, {
							trigger: true,
							replace: true
						}
					);

				},

				// http://stackoverflow.com/a/16191880
				current: function ()
				{

					var Router   = this,
					    fragment = Backbone.history.fragment,
					    routes   = _.pairs ( Router.routes ),
					    route    = null,
					    params   = null,
					    matched;

					matched = _.find (
						routes, function ( handler )
						{
							route = _.isRegExp ( handler[ 0 ] ) ? handler[ 0 ] : Router._routeToRegExp ( handler[ 0 ] );
							return route.test ( fragment );
						}
					);

					if ( matched ) {

						// NEW: Extracts the params using the internal
						// function _extractParameters
						params = Router._extractParameters ( route, fragment );
						route  = matched[ 1 ];

					}

					return {
						route   : route,
						fragment: fragment.split ( "/" ),
						params  : params
					};

				}

			}
		);

	}
);
