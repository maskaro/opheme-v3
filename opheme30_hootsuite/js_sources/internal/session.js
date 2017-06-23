define (
	[
		"underscore",
		"backbone",
		"app",
		"Models/user",
		"Models/api_info",
		"moment"
	], function ( _,
	              Backbone,
	              app,
	              UserModel,
	              APIInfoModel,
	              moment )
	{

		"use strict";

		return function ()
		{

			var self            = this,

			    // Initialize with negative/empty defaults. These will be overriden after the initial checkAuth
			    defaults        = {
				    user           : new UserModel ( {} ),
				    smHandles: {},
				    apiInfo  : new APIInfoModel ( {} ),
				    discovers: [],
				    currentDiscover: null,
				    twitterHandles : [] // hootsuite user authorised twitter handles
			    },

			    // STORAGE - does not work with Hootsuite
			    //		var namespace = $;//.initNamespaceStorage("hootsuite_app");
			    //		if ("localStorage" in window && window["localStorage"] !== null) {
			    //
			    //			self.storageContainers = {
			    //				"local": namespace.localStorage,
			    //				"session": namespace.sessionStorage
			    //			};
			    //
			    //		} else {
			    //
			    //			self.storageContainers = {
			    //				"local": namespace.cookieStorage,
			    //				"session": namespace.cookieStorage
			    //			};
			    //
			    //		}

			    // data - api_key, user_id
			    storeApiKey     = function ( data, callback )
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.storeApiKey is running. Data: ", data, "Callback: ", callback ] );
				    }

				    app.callAPI (
					    {
						    method       : "POST",
						    endpoint: "/_local_store_user_api.php",
						    localEndpoint: true,
						    data         : {
							    "api_key"     : data.api_key,
							    "opheme_id": data.user_id,
							    "hootsuite_id": window.hootsuite_user_id
						    }
					    }, {
						    success : function ( response )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.storeApiKey.success: ", response ] );
							    }

							    if ( callback && callback.success )
							    {
								    callback.success ( response );
							    }

						    },
						    error  : function ( error )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.storeApiKey.error is running. Error: ", error ] );
							    }

							    if ( callback && callback.error )
							    {
								    callback.error ( error );
							    }

						    },
						    complete: function ()
						    {

							    if ( callback && callback.complete )
							    {

								    if ( DEBUG )
								    {
									    app.logThis ( [ "Session.storeApiKey.complete is running." ] );
								    }

								    callback.complete ();

							    }

						    }
					    }
				    );

			    },

			    removeApiKey    = function ( callback )
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.removeApiKey is running. Callback: ", callback ] );
				    }

				    app.callAPI (
					    {
						    method       : "POST",
						    endpoint: "/_local_remove_user_api.php",
						    localEndpoint: true,
						    data         : {
							    "api_key"     : self.storage.apiInfo.get ( "access_token" ),
							    "hootsuite_id": window.hootsuite_user_id
						    }
					    }, {
						    success : function ( response )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.removeApiKey.success: ", response ] );
							    }

							    if ( callback && callback.success )
							    {
								    callback.success ( response );
							    }

						    },
						    error  : function ( error )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.removeApiKey.error is running. Error: ", error ] );
							    }

							    if ( callback && callback.error )
							    {
								    callback.error ( error );
							    }

						    },
						    complete: function ()
						    {

							    if ( callback && callback.complete )
							    {

								    if ( DEBUG )
								    {
									    app.logThis ( [ "Session.removeApiKey.complete is running." ] );
								    }

								    callback.complete ();

							    }

						    }
					    }
				    );

			    },

			    updateLastLogin = function ()
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.updateLastLogin is running." ] );
				    }

				    app.callAPI (
					    {
						    method  : "POST",
						    endpoint: "/admin/users/" + app.session.storage.user.get ( "id" ) + "/last_login",
						    data    : {
							    client_scope: "yes"
						    }
					    }, {
						    /**
						     * @param {{ last_login }} data
						     */
						    success: function ( data )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.updateLastLogin.success is running. Data: ", data ] );
							    }

						    },
						    error  : function ( error )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.updateLastLogin.error is running. Error: ", error ] );
							    }

						    }
					    }
				    );

			    },

			    //getLastLogin = function ()
			    //{
			    //
			    //  if ( DEBUG ) {
			    //    app.logThis ( [ "Session.getLastLogin is running." ] );
			    //  }
			    //
			    //  app.callAPI (
			    //    {
			    //	    method  : "GET",
			    //	    endpoint: "/me/last_login",
			    //	    headers: {
			    //		    "Authorization": self.storage.apiInfo.get ( "access_token" )
			    //	    }
			    //    }, {
			    //	    /**
			    //	     * @param {{ last_login }} data
			    //	     */
			    //	    success: function ( data )
			    //	    {
			    //
			    //		    if ( DEBUG ) {
			    //			    app.logThis ( [ "Session.getLastLogin.success is running. Data: ", data ] );
			    //		    }
			    //
			    //		    var lastLogin;
			    //
			    //		    if (data.last_login) {
			    //
			    //			    lastLogin = moment ( moment.utc ( data.last_login ).toDate () );
			    //
			    //		    } else {
			    //
			    //			    lastLogin = moment();
			    //
			    //		    }
			    //
			    //		    app.session.storage.user.set (
			    //			    {
			    //				    "last_login"     : lastLogin.format ( "YYYY-MM-DD HH:mm:ss" ),
			    //				    "last_login_unix": lastLogin.unix ()
			    //			    }
			    //		    );
			    //
			    //		    updateLastLogin();
			    //
			    //	    },
			    //	    error  : function ( error )
			    //	    {
			    //
			    //		    if ( DEBUG ) {
			    //			    app.logThis ( [ "Session.getLastLogin.error is running. Error: ", error ] );
			    //		    }
			    //
			    //	    }
			    //    }
			    //  );
			    //
			    //},

			    getUserMe       = function ( callback )
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.getUserMe is running." ] );
				    }

				    app.callAPI (
					    {
						    method  : "GET",
						    endpoint: "/me",
						    headers : {
							    "Authorization": self.storage.apiInfo.get ( "access_token" )
						    }
					    }, {
						    success : function ( user )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.getUserMe.success is running. User: ", user ] );
							    }

							    if ( !user || !user.id )
							    {

								    if ( DEBUG )
								    {
									    app.logThis ( [ "Session.getUserMe.success.error: Something went wrong server side." ] );
								    }

								    if ( callback && callback.error )
								    {
									    callback.error ( "Server went Bye-Bye." );
								    }

								    return false;

							    }

							    var usr = new UserModel (
								    {
									    id: user.id
								    }
							    );

							    storeApiKey (
								    {
									    api_key: self.storage.apiInfo.get ( "access_token" ),
									    user_id: user.id
								    }
							    );

							    usr.fetch (
								    {
									    success : function ( model )
									    {

										    if ( DEBUG )
										    {
											    app.logThis ( [ "Session.getUserMe.fetch.success is running. Data: ", model ] );
										    }

										    self.storage.user = model;

										    // get user last login time and then update it on the server
										    //getLastLogin ();

										    var lastLogin = moment ( moment.utc ( model.last_login ).toDate () ).unix ();

										    self.storage.user.set ( "last_login_unix", lastLogin );

										    updateLastLogin ();

										    // update hootsuite stream subtitle with account email
										    hsp.updatePlacementSubtitle ( model.get ( "email" ).substring ( 0, 35 ) );

										    self.getSMHandles (
											    {
												    success : function ( handles )
												    {

													    if ( DEBUG )
													    {
														    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success is running. Handles: ", handles ] );
													    }

													    self.getDiscovers (
														    {
															    success : function ( discovers )
															    {

																    if ( DEBUG )
																    {
																	    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success.getDiscovers.success is running. Discovers: ", discovers ] );
																    }

																    //self.trigger ( "change:logged_in" );

																    app.callAPI (
																	    {
																		    method       : "POST",
																		    endpoint: "/_local_fetch_user_tour_status.php",
																		    localEndpoint: true,
																		    data         : {
																			    "opheme_id": app.session.storage.user.get ( "id" )
																		    }
																	    }, {
																		    success: function ( data )
																		    {

																			    if ( DEBUG )
																			    {
																				    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success.getDiscovers.success.fetchTourStatus.success is running. Data: ", data ] );
																			    }

																			    window.tour_ended = parseInt ( data.tour_ended, 10 );

																			    if ( callback && callback.success )
																			    {
																				    callback.success ( model, handles, discovers, data.tour_ended );
																			    }

																		    },
																		    error  : function ( error )
																		    {

																			    if ( DEBUG )
																			    {
																				    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success.getDiscovers.success.fetchTourStatus.error is running. Error: ", error ] );
																			    }

																			    if ( callback && callback.error )
																			    {
																				    callback.error ( error );
																			    }

																		    }
																	    }
																    );

															    },
															    error  : function ( error )
															    {

																    if ( DEBUG )
																    {
																	    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success.getDiscovers.error is running. Error: ", error ] );
																    }

																    if ( callback && callback.error )
																    {
																	    callback.error ( error );
																    }

															    },
															    complete: function ()
															    {

																    if ( callback && callback.complete )
																    {

																	    if ( DEBUG )
																	    {
																		    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.success.getDiscovers.complete is running." ] );
																	    }

																	    callback.complete ();

																    }

															    }
														    }
													    );

												    },
												    error  : function ( error )
												    {

													    if ( DEBUG )
													    {
														    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.error is running. Error: ", error ] );
													    }

													    if ( callback && callback.error )
													    {
														    callback.error ( error );
													    }

												    },
												    complete: function ()
												    {

													    if ( callback && callback.complete )
													    {

														    if ( DEBUG )
														    {
															    app.logThis ( [ "Session.getUserMe.fetch.success.getSMHandles.complete is running." ] );
														    }

														    callback.complete ();

													    }

												    }
											    }
										    );

									    },
									    error  : function ( model, response )
									    {

										    if ( DEBUG )
										    {
											    app.logThis ( [ "Session.getUserMe.fetch.success is running. Data: ", model, response ] );
										    }

										    if ( callback && callback.error )
										    {

											    callback.error ( response );

										    }

									    },
									    complete: function ( /*model, response*/ )
									    {

										    if ( callback && callback.complete )
										    {

											    if ( DEBUG )
											    {
												    app.logThis ( [ "Session.getUserMe.fetch.complete is running." ] );
											    }

											    callback.complete ();

										    }

									    }
								    }
							    );

						    },
						    error  : function ( error )
						    {

							    if ( DEBUG )
							    {
								    app.logThis ( [ "Session.getUserMe.error is running. Error: ", error ] );
							    }

							    if ( callback && callback.error )
							    {
								    callback.error ( error );
							    }

						    },
						    complete: function ()
						    {

							    if ( callback && callback.complete )
							    {

								    if ( DEBUG )
								    {
									    app.logThis ( [ "Session.getUserMe.complete is running." ] );
								    }

								    callback.complete ();

							    }

						    }
					    }
				    );

			    },

			    resetToDefault  = function ()
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.resetToDefault is running." ] );
				    }

				    var prop;

				    self.storage = {};

				    for ( prop in defaults )
				    {

					    if ( defaults.hasOwnProperty ( prop ) )
					    {

						    self.storage[ prop ] = defaults[ prop ];

					    }

				    }

			    },

			    logoutTasks     = function ( callback )
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.logoutTasks is running." ] );
				    }

				    resetToDefault ();

				    window.opheme_user_key = app.hootsuite.userKey = "";

				    self.resetCurrentDiscover ();

				    hsp.updatePlacementSubtitle ( "Opheme" );

				    //self.trigger ( "change:logged_in" );

				    if ( callback )
				    {

					    if ( typeof callback.success === "function" )
					    {

						    callback.success ();

					    }

					    if ( typeof callback.complete === "function" )
					    {

						    callback.complete ();

					    }

				    }

			    },

			    initialise      = function ()
			    {

				    if ( DEBUG )
				    {
					    app.logThis ( [ "Session.initialize is running." ] );
				    }

				    resetToDefault ();

			    },
			    i, len, smModule;

			// populate defaults with social media modules
			len = app.config.smModules.length;

			for ( i = 0; i < len; i++ )
			{

				smModule = app.config.smModules[ i ];

				defaults.smHandles[ smModule ] = [];

			}

			self.storage = null;

			// Update user attributes after recieving API response
			self.updateSessionUser = function ( userData )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.updateSessionUser is running." ] );
				}

				self.storage.user.set ( userData );

			};
			/*
			 * Check for session from API
			 * The API will parse client cookies using its secret token
			 * and return a user object if authenticated
			 */
			self.checkAuth = function ()
			{

				if ( self.storage.user.get ( "id" ) )
				{ // all good

					if ( DEBUG )
					{
						app.logThis ( [ "Session.checkAuth.success: User is authenticated." ] );
					}

					return true;

				}

				// not logged in

				if ( DEBUG )
				{
					app.logThis ( [ "Session.checkAuth.success: User is NOT authenticated." ] );
				}

				return false;

			};

			self.loginWithKey = function ( callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.loginWithKey is running. API Info: ", self.storage.apiInfo ] );
				}

				getUserMe (
					callback
					//{
					//	success : function ( user, smHandles, discovers )
					//	{
					//
					//		if ( DEBUG ) {
					//			app.logThis ( [ "Session.loginWithKey.success is running. User: ", user ] );
					//		}
					//
					//		self.trigger ( "change:logged_in" );
					//
					//		// hootsuite - not needed, using per-stream hsp.save/getData methods
					//		//storeApiKey (
					//		//	{
					//		//		api_key: self.storage.apiInfo.get ( "access_token" ),
					//		//		user_id: user.get ( "id" )
					//		//	}
					//		//);
					//
					//		if ( callback && callback.success ) {
					//			callback.success ( user, smHandles, discovers );
					//		}
					//
					//	},
					//	error   : function ( error )
					//	{
					//
					//		if ( DEBUG ) {
					//			app.logThis ( [ "Session.loginWithKey.error is running. Error: ", error ] );
					//		}
					//
					//		if ( callback && callback.error ) {
					//			callback.error ( error );
					//		}
					//
					//	},
					//	complete: function ()
					//	{
					//
					//		if ( callback && callback.complete ) {
					//
					//			if ( DEBUG ) {
					//				app.logThis ( [ "Session.loginWithKey.complete is running." ] );
					//			}
					//
					//			callback.complete ();
					//
					//		}
					//
					//	}
					//}
				);

			};

			// opts { username, password }
			self.login = function ( data, callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.login is running. Data: ", data ] );
				}

				app.callAPI (
					{
						data    : {
							username            : data.username,
							password: data.password,
							grant_type: "password_forever",
							"client_credentials": "yes"
						},
						method: "POST",
						endpoint: "/oauth/access_token"
					}, {
						success : function ( api_data )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.login.success is running. Data: ", api_data ] );
							}

							self.storage.apiInfo = new APIInfoModel ( api_data );

							// save user key for later
							window.opheme_user_key = app.hootsuite.userKey = api_data.access_token;

							self.loginWithKey ( callback );

						},
						error  : function ( error )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.login.error is running. Error: ", error ] );
							}

							if ( callback && callback.error )
							{
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete )
							{

								if ( DEBUG )
								{
									app.logThis ( [ "Session.storeApiKey.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			self.logout = function ( callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.logout is running." ] );
				}

				//logoutTasks ( callback );

				removeApiKey (
					{
						success : function ( response )
						{

							if ( DEBUG ) {
								app.logThis ( [ "Session.logout.success: API key removed, finalising cleanup. Response: ", response ] );
							}

							logoutTasks ();

							if ( callback && callback.success ) {
								callback.success ( response );
							}

						},
						error   : function ( error )
						{

							if ( DEBUG ) {
								app.logThis ( [ "Session.logout.error: API key could not be removed. Error: ", error ] );
							}

							if ( callback && callback.error ) {
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete ) {

								if ( DEBUG ) {
									app.logThis ( [ "Session.logout.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			self.register = function ( data, callback )
			{

				var usr = new UserModel ();

				usr.save (
					data, {
						success : function ( model )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.register.success is running. User:  ", model ] );
							}

							var loginData = {
								username: data.email,
								password: data.password
							};

							self.login (
								loginData, {
									success : function ( user )
									{

										if ( DEBUG )
										{
											app.logThis ( [ "Session.register.success.login.success is running. User: ", user ] );
										}

										if ( callback && callback.success )
										{
											callback.success ( user );
										}

									},
									error  : function ( error )
									{

										if ( DEBUG )
										{
											app.logThis ( [ "Session.register.success.login.error is running. Error: ", error ] );
										}

										if ( callback && callback.error )
										{
											callback.error ( error );
										}

									},
									complete: function ()
									{

										if ( callback && callback.complete )
										{

											if ( DEBUG )
											{
												app.logThis ( [ "Session.register.success.login.complete is running." ] );
											}

											callback.complete ();

										}

									}

								}
							);

						},
						error  : function ( model, error )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.register.error is running. Error: ", error, "Model: ", model ] );
							}

							if ( callback && callback.error )
							{
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete )
							{

								if ( DEBUG )
								{
									app.logThis ( [ "Session.register.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			/**
			 * Gives back handles in the format {twitter: [], instagram: []} and their count, and the existing handles in the same format and their count
			 * @param {{success: function?, error: function?, complete: function?}} callback Runs with result data
			 */
			self.getSMHandles = function ( callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.getSMHandles is running." ] );
				}

				if ( !self.storage.apiInfo.get ( "access_token" ) )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.getSMHandles.error: There is no User API key available." ] );
					}

					if ( callback && callback.error )
					{
						callback.error ( [ "There is no User API key available. Please login." ] );
					}

					return;

				}

				app.callAPI (
					{
						method  : "GET",
						endpoint: "/authkeys",
						headers : {
							"Authorization": self.storage.apiInfo.get ( "access_token" )
						}
					}, {
						/**
						 * @param {{ social_media_platform_name: string }[]} handles
						 */
						success : function ( handles )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getSMHandles.success is running. Handles: ", handles ] );
							}

							var handlesFinal = {
								twitter  : [],
								instagram: []
							}, existingHandles, existingCount;

							existingCount               = self.storage.smHandlesCount;
							self.storage.smHandlesCount = handles.length;

							self.updateSessionUser (
								{
									"is_valid": false
								}
							);

							len = handles.length;

							if ( len )
							{

								for ( i = 0; i < len; i++ )
								{

									handlesFinal[ handles[ i ].social_media_platform_name ].push ( handles[ i ] );

								}

								self.updateSessionUser (
									{
										"is_valid": true
									}
								);

							}

							existingHandles = self.storage.smHandles;

							// potential updates
							self.storage.smHandles = handlesFinal;

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getSMHandles.success: Done processing, here's the result: ", "handles", handles, "handlesFinal", handlesFinal, "len", len, "existingHandles", existingHandles, "existingCount", existingCount ] );
							}

							if ( callback && callback.success )
							{
								callback.success ( handlesFinal, len, existingHandles, existingCount );
							}

						},
						error  : function ( error )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getSMHandles.error is running. Error: ", error ] );
							}

							if ( callback && callback.error )
							{
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete )
							{

								if ( DEBUG )
								{
									app.logThis ( [ "Session.getSMHandles.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			self.getSMHandlesCount = function ( callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.getSMHandlesCount is running." ] );
				}

				if ( !self.storage.apiInfo.get ( "access_token" ) )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.getSMHandlesCount.error: There is no User API key available." ] );
					}

					if ( callback && callback.error )
					{
						callback.error ( [ "There is no User API key available. Please login." ] );
					}

					return;

				}

				app.callAPI (
					{
						method  : "GET",
						endpoint: "/authkeys/count",
						headers : {
							"Authorization": self.storage.apiInfo.get ( "access_token" )
						}
					}, {
						success : function ( count )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getSMHandlesCount.success is running. Count: ", count ] );
							}

							if ( callback && callback.success )
							{
								callback.success ( count.count );
							}

						},
						error  : function ( error )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getSMHandlesCount.error is running. Error: ", error ] );
							}

							if ( callback && callback.error )
							{
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete )
							{

								if ( DEBUG )
								{
									app.logThis ( [ "Session.getSMHandlesCount.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			self.getDiscovers = function ( callback )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.getDiscovers is running." ] );
				}

				if ( !self.storage.apiInfo )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.getDiscovers.error: There is no User API key available." ] );
					}

					if ( callback && callback.error )
					{
						callback.error ( [ "There is no User API key available. Please login." ] );
					}

					return;

				}

				app.callAPI (
					{
						method  : "GET",
						endpoint: "/discovers",
						headers : {
							"Authorization": self.storage.apiInfo.get ( "access_token" )
						}
					}, {
						success : function ( discovers )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getDiscovers.success is running. Discovers: ", discovers ] );
							}

							// if only one discover, maintain the expected data structure
							if ( !(
									discovers instanceof Array
								) )
							{
								discovers = [ discovers ];
							}

							self.storage.discovers = discovers;

							if ( callback && callback.success )
							{
								callback.success ( discovers );
							}

						},
						error  : function ( error )
						{

							if ( DEBUG )
							{
								app.logThis ( [ "Session.getDiscovers.error is running. Error: ", error ] );
							}

							if ( callback && callback.error )
							{
								callback.error ( error );
							}

						},
						complete: function ()
						{

							if ( callback && callback.complete )
							{

								if ( DEBUG )
								{
									app.logThis ( [ "Session.getDiscovers.complete is running." ] );
								}

								callback.complete ();

							}

						}
					}
				);

			};

			self.selectCurrentDiscover = function ( id )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.selectCurrentDiscover is running. ID: ", id, "Discovers: ", app.session.storage.discovers ] );
				}

				var discover;

				len = self.storage.discovers.length;

				for ( i = 0; i < len; i++ )
				{

					discover = self.storage.discovers[ i ];

					if ( discover.id === id )
					{

						self.storage.currentDiscover = discover;

						app.hootsuite.currentDiscoverId = id;

						break;

					}

				}

			};

			self.resetCurrentDiscover = function ()
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.resetCurrentDiscover is running." ] );
				}

				self.storage.currentDiscover = null;

				app.hootsuite.currentDiscoverId = "";

			};

			self.removeDiscoverLocal = function ( id )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.removeDiscoverLocal is running." ] );
				}

				if ( !id )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.removeDiscoverLocal: No ID given, skipping request ..." ] );
					}

					return false;

				}

				var discover;

				len = self.storage.discovers.length;

				for ( i = 0; i < len; i++ )
				{

					discover = self.storage.discovers[ i ];

					if ( id === discover.id )
					{

						self.storage.discovers.splice ( i, 1 );

						break;

					}

				}

			};

			self.addDiscoverLocal = function ( discoverData )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.addDiscoverLocal is running. Data: ", discoverData ] );
				}

				if ( !discoverData )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.addDiscoverLocal: No Discover given, skipping request ..." ] );
					}

					return false;

				}

				self.storage.discovers.push ( discoverData );

			};

			self.changeDiscoverLocal = function ( id, data )
			{

				if ( DEBUG )
				{
					app.logThis ( [ "Session.changeDiscoverLocal is running. ID: ", id ] );
				}

				if ( !id )
				{

					if ( DEBUG )
					{
						app.logThis ( [ "Session.changeDiscoverLocal: No Discover ID given, skipping request ..." ] );
					}

					return false;

				}

				var discover;

				len = self.storage.discovers.length;

				for ( i = 0; i < len; i++ )
				{

					discover = self.storage.discovers[ i ];

					if ( id === discover.id )
					{

						_ ( discover ).extend ( data );

					}

				}

			};

			// add events
			_.extend ( self, Backbone.Events );

			//self.on (
			//	"change:logged_in", function ()
			//	{
			//
			//		if ( DEBUG ) {
			//			app.logThis ( [ "Session: User Login Status Changed. Running app.router.showIndex()." ] );
			//		}
			//
			//		app.router.showIndex ();
			//
			//	}, self
			//);

			initialise ();

		};

	}
);
