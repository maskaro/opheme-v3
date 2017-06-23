define (
	[
		"jquery",
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/forms/_login.html",
		"textplugin!/templates/page_elements/forms/_register.html",
		//"textplugin!/templates/page_elements/forms/_forgot-password.html",
		"textplugin!/templates/page_elements/simple/_terms.html",
		"textplugin!/templates/pages_full/login.html"
	], function ( $,
	              _,
	              app,
	              BaseView,
	              LoginElementTpl,
	              RegisterElementTpl,
	              //ForgotPasswordElementTpl,
	              TermsElementTpl,
	              LoginPageTpl )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "LoginView",

				viewPublicName: "Login / Register",

				viewDefaultLocation: "login/view",

				eventList: [
					"doLogin",
					"doForgot",
					"doRegister"
				],

				controllerName: "login",

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView.initialize is running." ] );
					}

					return this;

				},

				events: {
					"submit #login-form"   : "_doLogin",
					"submit #forgot-form"  : "_doForgot",
					"submit #register-form": "_doRegister"
					//"click #forgot-panel-header": "_forgotToggle"
				},

				_doLogin: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView._doLogin is running." ] );
					}

					app.eventPreventDefault ( evt );

					if ( $ ( "#login-form" ).parsley ().isValid () ) {

						this.dispatcher.trigger ( "doLogin" );

					}
					else {

						// Invalid clientside validations thru parsley
						if ( DEBUG ) {
							app.logThis ( [ "LoginView._doLogin: Did not pass client side validation" ] );
						}

						app.showAlert ( "Please enter your email address and password!", "error" );

					}

				},

				//_doForgot: function ( evt )
				//{
				//
				//	if ( DEBUG ) {
				//		app.logThis ( [ "LoginView._doForgot is running." ] );
				//	}
				//
				//	app.eventPreventDefault ( evt );
				//
				//	if ( $ ( "#forgot-form" ).parsley ().isValid () ) {
				//
				//		this.dispatcher.trigger ( "doForgot" );
				//
				//	}
				//	else {
				//
				//		// Invalid client-side validations through parsley
				//		if ( DEBUG ) {
				//			app.logThis ( [ "LoginView._doForgot: Did not pass client side validation" ] );
				//		}
				//
				//		app.showAlert ( "Please enter your email address!", "error" );
				//
				//	}
				//
				//},

				_doRegister: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView._doRegister is running." ] );
					}

					app.eventPreventDefault ( evt );

					if ( $ ( "#register-form" ).parsley ().isValid () ) {

						this.dispatcher.trigger ( "doRegister" );

					}
					else {

						// Invalid clientside validations thru parsley
						if ( DEBUG ) {
							app.logThis ( [ "LoginView._doRegister: Did not pass client side validation" ] );
						}

						app.showAlert ( "Please make sure all the form fields have been filled in!", "error" );

					}

				},

				//_forgotToggle: function ( evt )
				//{
				//
				//	if ( DEBUG ) {
				//		app.logThis ( [ "LoginView._forgotToggle is running." ] );
				//	}
				//
				//	var i,
				//	    el = $ ( evt.target );
				//
				//	for ( i = 0; i < 3; i++ ) {
				//		el = el.next ().slideToggle ( "slow" );
				//	}
				//
				//	$ ( ".flipper" ).animate (
				//		{
				//			height: "470px"
				//		},
				//		"fast"
				//	);
				//},

				// Allow enter press to trigger login
				_onPasswordKeyup: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView._onPasswordKeyup is running." ] );
					}

					evt = app.eventPreventDefault ( evt );

					var k = evt.keyCode || evt.which;

					if ( k === 13 && $ ( "#login-form" ).find ( "input[name=password]" ).val () !== "" ) {

						this._doLogin ( evt );

					}

				},

				// Allow enter press to trigger signup
				_onConfirmPasswordKeyup: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView._onConfirmPasswordKeyUp is running." ] );
					}

					evt = app.eventPreventDefault ( evt );

					var k = evt.keyCode || evt.which;

					if ( k === 13 && $ ( "#register-form" ).find ( "input[name=password_confirm]" ).val () !== "" ) {

						this._doRegister ( evt );

					}

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( LoginPageTpl );

					subTemplates.push (
						{
							"renderLogin": _.template ( LoginElementTpl )
						}
					);
					subTemplates.push (
						{
							"renderRegister": _.template ( RegisterElementTpl )
						}
					);
					//subTemplates.push (
					//	{
					//		"renderForgot": _.template ( ForgotPasswordElementTpl )
					//	}
					//);
					subTemplates.push (
						{
							"renderTerms": _.template ( TermsElementTpl )
						}
					);

					data = {};

					if ( DEBUG ) {
						app.logThis ( [ "LoginView.render: Data: ", data ] );
					}

					_.each (
						subTemplates, function ( template )
						{
							data = _.extend ( data, template );
						}
					);

					this.$el.html ( this.template ( data ) );

					return this;

				},

				viewSetup: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "LoginView.viewSetup is running." ] );
					}

					$ ( "#confirmEmail" )
						.bind (
						"click", function ()
						{
							$ ( this ).popover ( "hide" );
						}
					)
						.bind (
						"cut copy paste contextmenu", function ( evt )
						{
							app.eventPreventDefault ( evt );
							$ ( this ).popover ( "show" );
						}
					);

					// assume they do not have an account if Login has been displayed
					var $registerPanel = $ ( "div[href='#register-panel']" ),
					    $termsContent  = $ ( "#terms-content" );

					$registerPanel.click ();
					app.scrollBodyToTopOf ( $registerPanel );

					$ ( "#terms-button" ).click (
						function ( evt )
						{

							app.eventPreventDefault ( evt );

							$termsContent.slideDown (
								400, function ()
								{

									app.scrollBodyToTopOf ( $termsContent );

								}
							);

						}
					);

					$ ( "#back-tc" ).click (
						function ()
						{

							app.scrollBodyToTopOf (
								$registerPanel, function ()
								{

									$termsContent.slideUp ( 400 );

								}
							);

						}
					);

				}

			}
		);

	}
);
