define (
	[
		"jquery",
		"underscore",
		"app",
		"Controllers/base",
		"Views/pages/login"
	], function ( $,
	              _,
	              app,
	              BaseController,
	              LoginView )
	{

		"use strict";

		return BaseController.extend (
			{

				name: "login",

				viewNames: [
					{
						action  : "view",
						viewName: "LoginView"
					}
				],

				view: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginController.view is running." ] );
					}

					// bind dispatcher events
					this.dispatcherListenTo ();

					var deferred = new $.Deferred ();

					(
						new LoginView ()
					).show (
						function ()
						{
							deferred.resolve ();
						}
					);

					return deferred;

				},

				_listenBindingsLoginView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginController._listenBindingsLoginView is running." ] );
					}

					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doLogin", this[ "_process-login" ] );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doForgot", this[ "_process-forgotPassword" ] );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doRegister", this[ "_process-register" ] );

				},

				"_process-login": function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginController.process-login is running." ] );
					}

					var $loginForm = $ ( "#login-form" ),
					    data;

					if ( !app.startActivity ( "LoginController.process-login", $loginForm.closest ( "div.panel.panel-default" ).find ( "div.panel-title" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "LoginController.process-login: Already in progress, skipping request ..." ] );
						}

						return false;

					}

					$loginForm.find ( "input[type=submit]" ).attr ( "disabled", "disabled" );

					data = {
						username: $loginForm.find ( "input[name='email']" ).val (),
						password: $loginForm.find ( "input[name='password']" ).val (),
						remember: $loginForm.find ( "input[name='remember']" ).is ( ":checked" )
					};

					app.session.login (
						data, {
							success : function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-login.success: User is logged in." ] );
								}

								app.router.showIndex ();

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-login.error: Reason: ", error ] );
								}

								app.showAlert ( "Something went wrong! Message: " + error, "error" );

								$loginForm.find ( "input[type='submit']" ).removeAttr ( "disabled" );

							},
							complete: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-login.complete is running." ] );
								}

								app.stopActivity ( "LoginController.process-login" );

							}
						}
					);

				},

				//"_process-forgotPassword": function ()
				//{
				//
				//	if ( DEBUG ) {
				//		app.logThis ( [ "LoginController.process-forgotPassword is running." ] );
				//	}
				//
				//	// stuff
				//
				//},

				"_process-register": function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginController.process-register is running." ] );
					}

					var $registerForm = $ ( "#register-form" ),
					    formData, defaults;

					if ( !app.startActivity ( "LoginController.process-register", $registerForm.closest ( "div.panel.panel-default" ).find ( "div.panel-title" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "LoginController.process-register: Already in progress, skipping request ..." ] );
						}

						return false;

					}

					$registerForm.find ( "input[type='submit']" ).attr ( "disabled", "disabled" );

					formData = {
						email     : $registerForm.find ( "input[name='email']" ).val (),
						password  : $registerForm.find ( "input[name='password']" ).val (),
						first_name: $registerForm.find ( "input[name='first_name']" ).val ()
					};
					defaults = {
						suspended: false,
						last_name: "",
						phone    : ""
					};

					formData = _.extend ( {}, defaults, formData );

					app.session.register (
						formData, {
							success : function ( user )
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-register.success: ", user ] );
								}

								app.showAlert ( "Welcome, " + user.get ( "first_name" ) + "!", "success" );

								app.router.showIndex ();

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-register.error: ", error ] );
								}

								app.showAlert ( "Something went wrong! Message: " + error, "error" );

								$registerForm.find ( "input[type=submit]" ).removeAttr ( "disabled" );

							},
							complete: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.process-register is running." ] );
								}

								app.stopActivity ( "LoginController.process-register" );

							}
						}
					);

				},

				exit: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginController.exit is running." ] );
					}

					app.session.logout (
						{
							success: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.exit.success: Logout successful, reloading app." ] );
								}

								app.showAlert ( "Logout successful", "success" );

								app.router.showIndex ();

							},
							error  : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "LoginController.exit.error: Logout unsuccessful. Reason: ", error ] );
								}

								app.showAlert ( "Logout unsuccessful. Reason: " + error, "error" );

							}
						}
					);

				}

			}
		);

	}
);
