define (
	[
		"jquery",
		"underscore",
		"backbone",
		"backbonemvc",
		"app"
		//"textplugin!/templates/page_elements/overviews/_page-loading.html"
	], function ( $,
	              _,
	              Backbone,
	              BackboneMVC,
	              app
	              //PageLoadingElementTpl
	)
	{

		"use strict";

		return BackboneMVC.Controller.extend (
			{

				name: "base",

				viewNames: [],

				// event aggregator
				dispatcher: _.extend ( {}, Backbone.Events ),

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.initialize is running for: ", this.name ] );
					}

				},

				_viewBindings: function ( action )
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController._viewBindings is running. View Names: ", this.viewNames, "Current action: ", action, "Current view: ", app.views.currentView ] );
					}

					if ( this.viewNames.length ) {

						var i, len, el;

						len = this.viewNames.length;

						for ( i = 0; i < len; i++ ) {

							/**
							 * @type { { viewName: string, action: string } }
							 */
							el = this.viewNames[ i ];

							if ( app.views.currentView.viewName !== el.viewName ) {
								continue;
							}

							if ( el.action !== action ) {
								continue;
							}

							if ( DEBUG ) {
								app.logThis ( [ "BaseController._viewBindings: Setting up View: ", el ] );
							}

							if ( this[ "_listenBindings" + el.viewName ] ) {

								this[ "_listenBindings" + el.viewName ] ();

							}
							else {

								if ( DEBUG ) {
									app.logThis ( [ "BaseController._viewBindings: Create a method (_listenBindings" + el.viewName + ") if view event bindings are needed." ] );
								}

							}

						}

					}

				},

				dispatcherListenTo: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.dispatcherListenTo is running." ] );
					}

					var f;

					f = function ( viewName )
					{

						if ( DEBUG ) {
							app.logThis ( [ "BaseController.dispatcherListenTo.viewRendered is running. View: ", viewName ] );
						}

						this.dispatcher.listenToOnce (
							app.events, "viewDestroyed", function ( viewName )
							{

								if ( DEBUG ) {
									app.logThis ( [ "BaseController.dispatcherListenTo.viewDestroyed is running. View: ", viewName ] );
								}

								this.dispatcher.stopListening ( app.events, "viewRendered", f );

							}.bind ( this )
						);

					}.bind ( this );

					this.dispatcher.listenTo ( app.events, "viewRendered", f );

				},

				// defined once, will be invoked before each action method
				beforeFilter: function ( action )
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.beforeFilter: Controller: ", this.name, "Action: ", action ] );
					}

					// show the spinner located in the header HTML
					$ ( ".hs_topBarContent .my-spinner" ).show ();

					app.startActivity("LoadingPage");

					// show the Page Loading text
					//$ ( "#content" ).html ( PageLoadingElementTpl );

				},

				// defined once, will be invoked after each action method
				afterRender: function ( action )
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.afterRender: Controller: ", this.name, "Action: ", action ] );
					}

				},

				// used with secure methods, expect true/false or Deferred Object.
				// used to decide whether access is given or not
				checkSession: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.checkSession is running. Controller: ", this.name ] );
					}

					if ( app.session.checkAuth () ) {

						if ( DEBUG ) {
							app.logThis ( [ "BaseController.checkSession: User is AUTHENTICATED." ] );
						}

						var whiteList = [ app.config.modules.invalidAccount, "login" ];

						if ( whiteList.indexOf ( this.name ) === -1 && !app.session.storage.user.get ( "is_valid" ) ) {

							if ( DEBUG ) {
								app.logThis ( [ "BaseController.checkSession: User is NOT VALID, redirecting." ] );
							}

							app.router.showIndex ();

							return false;

						}

						if ( DEBUG ) {
							app.logThis ( [ "BaseController.checkSession: User is VALID, allowing access." ] );
						}

						// save current URL
						app.hootsuite.currentURL = Backbone.history.fragment;

						// bind dispatcher events
						this.dispatcherListenTo ();

						return true;

					}

					if ( DEBUG ) {
						app.logThis ( [ "BaseController.checkSession: User is NOT AUTHENTICATED, denying access." ] );
					}

					app.router.showIndex ();

					return false;

				}

			}
		);

	}
);
