define (
	[
		"jquery",
		"app",
		"Controllers/base",
		"Views/pages/main-stream"
	], function ( $,
	              app,
	              BaseController,
	              MainStreamView )
	{

		"use strict";

		return BaseController.extend (
			{

				name: "main-stream",

				viewNames: [
					{
						action: "view", viewName: "MainStreamView"
					}
				],

				deferredObject: null,

				user_view: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamController.user_view is running." ] );
					}

					var discover = app.session.storage.currentDiscover;

					if ( discover ) {

						if ( DEBUG ) {
							app.logThis ( [ "MainStreamController.user_view: Discover has been set: ", discover ] );
						}

						this.deferredObject = new $.Deferred ();

						this.deferredObject.done (
							function ()
							{
								(
									new MainStreamView ()
								).show ();
							}
						);

						setTimeout (
							function ()
							{
								this._getUserFollowsForDiscover ();
							}.bind ( this ), 0
						);

						return this.deferredObject;

					}

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamController.user_view: Discover has NOT been set, sending User back to Discovers list." ] );
					}

					return app.router.navigate (
						"discovers/view", {
							trigger: true,
							replace: true
						}
					);

				},

				_listenBindingsMainStreamView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamController._listenBindingsMainStreamView is running." ] );
					}

					//this.dispatcher.listenTo(app.views.currentView.dispatcher, "doSomething", this["_process-something"]);

				},

				/**
				 * Retrieves list of user follows from server for current discover.
				 * @private
				 */
				_getUserFollowsForDiscover: function ()
				{

					var discover = app.session.storage.currentDiscover,
					    authkeys = discover.authkeys.data,
					    i, len   = authkeys.length;

					// set a watcher
					this.deferredObject.progress (
						function ( number )
						{

							// all done now, proceed with page setup
							if ( number === len ) {

								this.deferredObject.resolve ();

							}

						}.bind ( this )
					);

					// start the server calls
					for ( i = 0; i < len; i++ ) {

						// one authkey at a time
						this._fetchUserFollowsForAuthKeyId ( authkeys[ i ], i );

					}

				},

				/**
				 * Retrieves user follows for one authkey.
				 * @param {object} authkey Authkey to check
				 * @param {int} arrayId Array position of the authkey element
				 * @private
				 */
				_fetchUserFollowsForAuthKeyId: function ( authkey, arrayId )
				{

					app.callAPI (
						{
							method  : "GET",
							endpoint: "/interactions/follows/" + authkey.id,
							data    : {
								count: 200
							},
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success : function ( follows )
							{

								if ( DEBUG ) {
									app.logThis ( [ "MainStreamController._fetchUserFollowsForAuthKeyId.success is running. Data: ", authkey, arrayId ] );
								}

								var userFollows = authkey.userFollows = {},
								    i, len = follows.length;

								// go through all follows, if any
								for ( i = 0; i < len; i++ ) {

									// make them easily accessible
									userFollows[ follows[ i ].screen_name ] = true;

								}

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "MainStreamController._fetchUserFollowsForAuthKeyId.error is running. Message: ", error ] );
								}

							},
							complete: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "MainStreamController._fetchUserFollowsForAuthKeyId.complete is running." ] );
								}

								// notify the object that current job is done
								this.deferredObject.notify ( arrayId + 1 );

							}.bind ( this )
						}
					);

				}

			}
		);

	}
);
