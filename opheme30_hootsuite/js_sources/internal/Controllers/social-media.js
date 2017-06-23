define (
	[
		"jquery",
		"app",
		"Controllers/base",
		"Views/pages/social-media"
	], function ( $,
	              app,
	              BaseController,
	              SocialMediaView )
	{

		"use strict";

		return BaseController.extend (
			{

				name: "social-media",

				viewNames: [
					{
						action  : "view",
						viewName: "SocialMediaView"
					}
				],

				user_view: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaController.user_view is running." ] );
					}

					// reset currentDiscover
					app.session.resetCurrentDiscover();

					var deferred = new $.Deferred (),
						//fetchedTwitterHandles = false,
						//fetchedInstagramHandles = false,
						goAheadFn = function () {

							(
								new SocialMediaView ()
							).show (
								function ()
								{

									deferred.resolve ();

								}
							);

						};

					hsp.getTwitterAccounts (
						function ( handles )
						{

							if ( DEBUG ) {
								app.logThis ( [ "SocialMediaController.user_view.Hootsuite.getTwitterAccounts: Twitter Handles: ", handles ] );
							}

							app.session.storage.twitterHandles = handles;

							//fetchedTwitterHandles = true;

							//if (fetchedTwitterHandles && fetchedInstagramHandles) {

								goAheadFn();

							//}

						}
					);

					//hsp.getInstagramAccounts (
					//	function ( handles )
					//	{
					//
					//		if ( DEBUG ) {
					//			app.logThis ( [ "SocialMediaController.user_view.Hootsuite.getInstagramAccounts: Instagram Handles: ", handles ] );
					//		}
					//
					//		app.session.storage.InstagramHandles = handles;
					//
					//		fetchedInstagramHandles = true;
					//
					//		if (fetchedTwitterHandles && fetchedInstagramHandles) {
					//
					//			goAheadFn();
					//
					//		}
					//
					//	}
					//);

					return deferred;

				},

				_listenBindingsSocialMediaView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaController._listenBindingsSocialMediaView is running." ] );
					}

					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doBuildAuthorisation", this[ "_build-authorisationLink" ] );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doDestroyAuthorisation", this[ "_destroy-authorisation" ] );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doCheckHandleStatus", this[ "_check-handle-status" ] );

				},

				// returns authorisation link for buttons
				"_build-authorisationLink": function ( callback )
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaController._build-authorisationLink is running." ] );
					}

					if ( !app.startActivity ( "SocialMediaController._build-authorisationLink", $ ( "div.panel-title h3" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "SocialMediaController._build-authorisationLink: Action already in progress, skipping request ... " ] );
						}

						return false;

					}

					app.callAPI (
						{
							method  : "GET",
							endpoint: "/oauth/generate-code",
							data    : {
								"client_scope": "yes"
							},
							async   : false
						}, {
							/**
							 * @param {{ authorise_code: string, expires_in: number }} data
							 * @returns {boolean}
							 */
							success : function ( data )
							{

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaController._build-authorisationLink.success is running. Code: ", data ] );
								}

								if ( !data || !(
										data.authorise_code
									) ) {

									if ( DEBUG ) {
										app.logThis ( [ "SocialMediaController._build-authorisationLink.success: Something went wrong, there is no Code, stopping ..." ] );
									}

									if ( typeof callback === "function" ) {

										callback ( null, "No authorisation code supplied by the server." );

									}

									return false;

								}

								var code        = data.authorise_code,
								    callbackURL =
									    window.location.protocol + "//" +
									    window.location.hostname + "/social-media/callback?" +
									    app.hootsuite.securityInfo,
								    location    =
									    app.config.backendURL + "/oauth/__smType__/authorise?" +
									    "callback=" + encodeURIComponent ( callbackURL ) +
									    "&user_id=" + app.session.storage.user.get ( "id" ) +
									    "&authorise_code=" + code;

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaController._build-authorisationLink: Built variables - Callback: ", callbackURL, " Next Location: ", location ] );
								}

								if ( typeof callback === "function" ) {

									if ( DEBUG ) {
										app.logThis ( [ "SocialMediaController._build-authorisationLink.success: Running callback as requested ..." ] );
									}

									callback ( location, data.expires_in );

								}

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaController._build-authorisationLink.error is running. Error: ", error ] );
								}

								if ( typeof callback === "function" ) {

									app.logThis ( [ "SocialMediaController._build-authorisationLink.error: Running callback as requested ..." ] );

									callback ( null, error );

								}

							},
							complete: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaController._build-authorisationLink.complete is running." ] );
								}

								app.stopActivity ( "SocialMediaController._build-authorisationLink" );

							}
						}
					);

				},

				// $anchor
				"_check-handle-status": function ( $anchor )
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaController._check-handle-status is running. Anchor object: ", $anchor ] );
					}

					var smType = $anchor.data ( "type" ); // social media type

					app.session.getSMHandles (
						{
							success: function ( handles, count, existingHandles, existingCount )
							{

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaController._check-handle-status.getSMHandles.success is running. Count: ", count ] );
								}

								// more handles on server than here
								if ( existingCount < count ) {

									if ( DEBUG ) {
										app.logThis ( [ "SocialMediaController._check-handle-status.getSMHandles.success: New handle detected ..." ] );
									}

									//app.stopAndRemoveTimerByNameAndType ( "button-" + smType, "interval" );
									//
									//app.stopActivity ( "SocialMediaView.viewSetup.doBuildAuthorisation.button" );

									app.showAlert ( "You have authorised the use of your " + smType.toCamelCase ( true ) + " account.", "success" );

									app.session.storage.smHandlesCount = count;

									app.events.trigger ( "doViewRefresh" );

									return;

								}

								var i, j, k, len1, len2, len3, inHandleSet, existingHandleSet, inHandle, existingHandle, smModule;

								// check handles individually
								// if there is anything to count
								if ( count && app.session.storage.smHandlesCount ) {

									len1 = app.config.smModules.length;

									// go through all social media modules
									for ( i = 0; i < len1; i++ ) {

										smModule = app.config.smModules[ i ];

										existingHandleSet = existingHandles[ smModule ];
										inHandleSet       = handles[ smModule ];

										// if the social media module has any handles
										if ( existingHandleSet.constructor === Array && inHandleSet.constructor === Array ) {

											len2 = existingHandleSet.length;
											len3 = inHandleSet.length;

											// go through each in turn
											for ( j = 0; j < len2; j++ ) {

												existingHandle = existingHandleSet[ j ];

												for ( k = 0; k < len3; k++ ) {

													inHandle = inHandleSet[ k ];

													// handle from server is already here, but is newer
													if ( inHandle.screen_name === existingHandle.screen_name && inHandle.updated_at !== existingHandle.updated_at ) {

														if ( DEBUG ) {
															app.logThis ( [ "SocialMediaController._check-handle-status.getSMHandles.success: Existing handle is newer on server ... Existing Handle:", existingHandle, "Server Handle", inHandle ] );
														}

														app.showAlert ( "You have authorised the use of your " + smType.toCamelCase ( true ) + " account.", "success" );

														// refresh view, we're done
														app.events.trigger ( "doViewRefresh" );

													}

												}

											}

										}

									}

								}

							}

						}
					);

				},

				"_destroy-authorisation": function ( target )
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaController._destroy-authorisation is running." ] );
					}

					// the destroy authorisation button
					var $el = $ ( target ),
					    id, smModule, handle;

					if ( !app.startActivity ( "SocialMediaController._destroy-authorisation", $el, true, true ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "SocialMediaController._destroy-authorisation: Action already in progress, skipping request ... " ] );
						}

						return false;

					}

					$el.attr ( "disabled", "disabled" );

					id       = $el.data ( "id" );
					smModule = $el.data ( "type" );
					handle   = $el.data ( "handle" );

					app.callAPI (
						{
							method  : "DELETE",
							endpoint: "/authkeys/" + id,
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success : function ()
							{

								app.showAlert ( "You have removed the use of your " + smModule.toCamelCase ( true ) + " account @" + handle + ".", "success" );

								app.session.storage.smHandlesCount--;

								app.events.trigger ( "doViewRefresh" );

							},
							error   : function ( error )
							{

								app.showAlert ( "Something went wrong. Message: " + error + ".", "success" );

								$el.removeAttr ( "disabled" );

							},
							complete: function ()
							{

								app.stopActivity ( "SocialMediaController._destroy-authorisation" );

							}
						}
					);

				}

			}
		);

	}
);
