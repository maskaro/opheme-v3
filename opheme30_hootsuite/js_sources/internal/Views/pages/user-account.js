define (
	[
		"jquery",
		"underscore",
		"bootstraptour",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/forms/_account-settings.html",
		"textplugin!/templates/pages_full/account-settings.html"
	], function ( $,
	              _,
	              BootstrapTour,
	              app,
	              BaseView,
	              UserAccountFormElementTpl,
	              UserAccountPageTpl )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "UserAccountView",

				viewPublicName: "Account Settings",

				viewDefaultLocation: "user-account/view",

				eventList: [
					"doUpdateUser"
				],

				controllerName: "user-account",

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountView.initialize is running." ] );
					}

					return this;

				},

				events: {
					"submit #account-settings-form": "user-save-account"
				},

				"user-save-account": function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountView.user-save-account is running." ] );
					}

					app.eventPreventDefault ( evt );

					if ( $ ( "#account-settings-form" ).parsley ().isValid () ) {

						this.dispatcher.trigger ( "doUpdateUser" );

					}
					else {

						app.showAlert ( "Please deal with the red form fields!", "error" );

					}

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( UserAccountPageTpl );

					subTemplates.push (
						{
							"renderAccountSettings": _.template ( UserAccountFormElementTpl )
						}
					);

					data = {
						data: {
							user: app.session.storage.user.toJSON ()
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountView.render: Data: ", data ] );
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
						app.logThis ( [ "UserAccountView.viewSetup is running." ] );
					}

					$ ( "#password-new" ).on (
						"keypress change keyup", function ()
						{

							var changed = false,
							    $el     = $ ( this ),
							    $accountSettingsForm;

							if ( $el.val ().length ) {

								if ( !$el.is ( "[data-parsley-required]" ) ) {

									$ ( "#password-old, #password-confirm, #password-new" ).attr ( "data-parsley-required", "true" );
									changed = true;

								}

							}
							else {

								if ( $el.is ( "[data-parsley-required]" ) ) {

									$ ( "#password-old, #password-confirm, #password-new" ).removeAttr ( "data-parsley-required" );
									changed = true;

								}

							}

							if ( changed ) {

								$accountSettingsForm = $ ( "#account-settings-form" );

								// destroy ParsleyForm instance
								$accountSettingsForm.parsley ().destroy ();

								// bind parsley
								$accountSettingsForm.parsley ();

							}

						}
					);

					var $passwordContainer = $ ( "#password-container" );

					$passwordContainer.on (
						"hide.bs.collapse", function ()
						{

							$ ( "#passwordToggle" ).text ( "Change Password" );
							$ ( "#password-old, #password-confirm, #password-new" ).val ( "" );

						}
					);

					$passwordContainer.on (
						"show.bs.collapse", function ()
						{

							$ ( "#passwordToggle" ).text ( "Cancel" );

						}
					);

				}

			}
		);

	}
);
