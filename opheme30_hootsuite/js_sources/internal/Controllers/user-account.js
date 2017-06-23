define (
	[
		"jquery",
		"underscore",
		"backbone",
		"app",
		"Models/user",
		"Controllers/base",
		"Views/pages/user-account"
	], function ( $,
	              _,
	              Backbone,
	              app,
	              UserModel,
	              BaseController,
	              UserAccountView )
	{

		"use strict";

		return BaseController.extend (
			{

				name: "user-account",

				viewNames: [
					{
						action  : "view",
						viewName: "UserAccountView"
					}
				],

				user_view: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountController.user_view is running." ] );
					}

					// reset currentDiscover
					app.session.resetCurrentDiscover();

					var deferred = new $.Deferred ();

					(
						new UserAccountView ()
					).show (
						function ()
						{
							deferred.resolve ();
						}
					);

					return deferred;

				},

				_listenBindingsUserAccountView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountController._listenBindingsUserAccountView is running" ] );
					}

					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doUpdateUser", this._update );

				},

				_update: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserAccountController._update is running." ] );
					}

					if ( !app.startActivity ( "UserAccountController._update", $ ( "div.panel-title h3" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "UserAccountController._update: Action already in progress, skipping request ... " ] );
						}

						return false;

					}

					var $submitButtons = $ ( "input[type=submit]" ),
					    formData, passData;

					$submitButtons.attr ( "disabled", "disabled" );

					formData = {
						first_name: $ ( "input[name='first-name']" ).val (),
						last_name : $ ( "input[name='last-name']" ).val ()
						//phone: $("input[name='phone']").val()
					};
					passData = $ ( "#password-new" ).val ();

					if ( passData.length ) {
						formData = _.extend (
							{}, formData, {
								password: passData
							}
						);
					}

					Backbone.sync (
						"patch", new UserModel (
							{
								id: app.session.storage.user.get ( "id" )
							}
						), {
							attrs   : formData,
							success : function ( user )
							{

								if ( DEBUG ) {
									app.logThis ( [ "UserAccountController.user_update.success: ", user ] );
								}

								app.session.updateSessionUser ( user );

								(
									new UserAccountView ()
								).formChanged = false;

								app.showAlert ( "Your details have been updated.", "success" );

								app.events.trigger ( "doViewRefresh" );

							},
							error   : function ( model, error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "UserAccountController.user_update.error: ", error, "Model: ", model ] );
								}

								app.showAlert ( "Your details have not been saved. Reason: " + error + ".", "error" );

							},
							complete: function ()
							{

								$submitButtons.removeAttr ( "disabled" );

								app.stopActivity ( "UserAccountController._update" );

							}
						}
					);

				}

			}
		);

	}
);
