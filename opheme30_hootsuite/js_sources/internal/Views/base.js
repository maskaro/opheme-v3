define (
	[
		"require",
		"jquery",
		"underscore",
		"backbone",
		"app",
		"Views/fragments/header",
		"textplugin!/templates/page_elements/common/_footer.html"
	], function ( require,
	              $,
	              _,
	              Backbone,
	              app,
	              HeaderFragmentView,
	              FooterElementTpl )
	{

		"use strict";

		return Backbone.View.extend (
			{

				viewName: "BaseView", // the OOP class name of current view

				viewDefaultLocation: "controller/action", // default url for this view

				viewPublicName: "", // view name to show to the public

				className: app.config.hootsuite.streamContainerClass, // class name of the div element

				eventList: null, // list of events the view will be using - should be the ones other entities listen in on, such as the associated controller

				controllerName: null, // the controller name of current view

				controllerAction: "view", // default controller action for current view

				constructor: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.constructor is running." ] );
					}

					if ( this.viewName === "BaseView" ) { // BaseView should never be instantiated and this field needs overriding

						if ( DEBUG ) {
							app.logThis ( [ "BaseView.constructor: This View MUST have a name, currently " + this.viewName + ", and the Base View should not be instantiated directly, mistake?. Current View: ", this ] );
						}

					}

					// start off with formChanged state as false
					this.formChanged = false;

					if ( app.views.instances[ this.viewName ] ) {

						var inst = app.views.instances[ this.viewName ];

						// singleton
						if ( DEBUG ) {
							app.logThis ( [ "BaseView.constructor: Instance of " + this.viewName + " already exists. Instance: ", inst ] );
						}

						return inst;

					}

					// add to existing instances
					app.views.instances[ this.viewName ] = this;

					// Call the original constructor
					Backbone.View.apply ( this );

				},

				destructor: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.destructor: Override me for Personal Cleanup." ] );
					}

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.destructor: Stopping all activities ..." ] );
					}

					app.stopAllActivities ();

					if ( this.eventList && this.eventList instanceof Array && this.eventList.length ) {

						if ( DEBUG ) {
							app.logThis ( [ "BaseView.destructor: Unbinding all events ..." ] );
						}

						var i, len, eventName;

						len = this.eventList.length;

						for ( i = 0; i < len; i++ ) {

							eventName = this.eventList[ i ];

							if ( DEBUG ) {
								app.logThis ( [ "BaseView.destructor: Unbinding event (" + eventName + ") ..." ] );
							}

							this.dispatcher.off ( eventName );

						}

					}

				},

				// event aggregator, link between view and controller
				dispatcher: _.extend ( {}, Backbone.Events ),

				dispatcherListenTo: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.dispatcherListenTo is running." ] );
					}

					// handle current view refresh requests
					this.dispatcher.listenTo (
						app.events, "doViewRefresh", function ()
						{

							if ( DEBUG ) {
								app.logThis ( [ "BaseView.dispatcherListenTo: View Refresh for " + app.views.currentView.viewName + " has been requested. View: ", app.views.currentView ] );
							}

							// refresh the formChanged state
							app.views.currentView.formChanged = false;

							// re-render page
							app.views.currentView.show ();

						}
					);

					// handle current view after-render setup
					this.dispatcher.listenTo (
						app.events, "viewRendered", function ( viewName )
						{

							if ( DEBUG ) {
								app.logThis ( [ "BaseView.dispatcherListenTo: " + viewName + " View has been Rendered. View: ", app.views.currentView ] );
								app.logThis ( [ "BaseView.dispatcherListenTo: Passing control to BaseView.viewsGlobalSetup." ] );
							}

							// always run the global setup for each view
							app.views.currentView.viewGlobalSetup ();

							if ( app.views.currentView.viewSetup ) {

								if ( DEBUG ) {
									app.logThis ( [ "BaseView.dispatcherListenTo: Passing control to " + viewName + " Personal Setup." ] );
								}

								app.views.currentView.viewSetup ();

							}
							else {

								if ( DEBUG ) {
									app.logThis ( [ "BaseView.dispatcherListenTo: Create a " + viewName + ".viewSetup() method if you need Personal Setup done on " + viewName + "." ] );
								}

							}

						}
					);

					// handle current view after-deletion setup
					this.dispatcher.listenTo (
						app.events, "viewDestroyed", function ( viewName )
						{

							if ( DEBUG ) {
								app.logThis ( [ "BaseView.viewDestroyed: " + viewName + " View has been Destroyed." ] );
							}

							if ( this.destructor ) {

								if ( DEBUG ) {
									app.logThis ( [ "BaseView.viewDestroyed: Passing control to " + viewName + ".destructor." ] );
								}

								this.destructor ();

							}
							else {

								if ( DEBUG ) {
									app.logThis ( [ "BaseView.viewDestroyed: Create a " + viewName + ".destructor() method if you need Personal Cleanup done on " + viewName + "." ] );
								}

							}

						}.bind ( this )
					);

				},

				runControllerViewBindings: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.runControllerViewBindings for " + this.viewName + " is running. Controller name: ", this.controllerName ] );
					}

					if ( this.controllerName ) {

						var Controller = require ( "Controllers/" + this.controllerName ),
						    ctrl       = new Controller ();

						if ( DEBUG ) {
							app.logThis ( [ "BaseView.runControllerViewBindings: Controller instance: ", ctrl ] );
						}

						if ( ctrl._viewBindings ) {

							ctrl._viewBindings ( this.controllerAction );

						}

					}

				},

				viewGlobalSetup: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.viewsGlobalSetup for " + this.viewName + " is running." ] );
					}

					// initialise tooltips
					$ ( "[data-toggle='tooltip']" ).tooltip (
						{
							container: "body"
						}
					);

					// Initialise popovers
					$ ( "[data-toggle='popover']" ).popover ();

					// Hide confirm email popover when user clicks anywhere on the website
					$ ( "body" ).on (
						"click", function ( e )
						{

							var $target = $ ( e.target );

							//only buttons
							if ( $target.data ( "toggle" ) !== "popover" && $target.parents ( ".popover.in" ).length === 0 ) {

								$ ( "[data-toggle='popover']" ).popover ( "hide" );

							}

							// top settings button
							if ( !$target.hasClass ( "btn-settings" ) && !$target.hasClass ( "hs_topBarControlsBtnCustom" ) && !$target.hasClass ( "fa-gear" ) && !$target.hasClass ( "hs_topBarDropdown" ) ) {

								$ ( ".hs_topBarControlsBtnCustom.btn-settings.active" ).click ();

							}

							// top settings button
							if ( !$target.hasClass ( "btn-more" ) && !$target.hasClass ( "hs_topBarControlsBtnCustom" ) && !$target.hasClass ( "fa-ellipsis-v" ) && !$target.hasClass ( "hs_topBarDropdown" ) ) {

								$ ( ".hs_topBarControlsBtnCustom.btn-more.active" ).click ();

							}

						}
					);

					// bind parsley to any forms on page
					var $forms = $ ( "form" ),
					    formElements, formChangedActions;

					if ( $forms.length ) {

						formElements       = $forms.find ( "input:not([type=button], [type=submit], [type=reset]), textarea, select, input[type=hidden], :hidden" ).not ( "div, label, br" );
						formChangedActions = function ()
						{

							this.formChanged = true;

						}.bind ( this );

						formElements.keyup (
							function ( evt )
							{

								var k = evt.keyCode || evt.which;

								if ( k !== 13 ) { // no return key

									formChangedActions ();

								}

							}
						);

						formElements.change (
							function ()
							{

								formChangedActions ();

							}
						);

						$forms.submit (
							function ( evt )
							{

								if ( !app.views.currentView.formChanged ) {

									if ( DEBUG ) {
										app.logThis ( [ "BaseView.viewsGlobalSetup: Form inside " + app.views.currentView.viewName + " was submitted without changes. Form: ", this ] );
									}

									app.eventPreventDefault ( evt );

									app.showAlert ( "Please make some changes to the form before attempting to submit it!", "warning" );

									return false;

								}

							}
						);

						$forms.parsley ();

					}

					// hide the page loading spinner
					$ ( ".hs_topBarContent .my-spinner" ).hide ();

					app.stopActivity ( "LoadingPage" );

					try {

						// synchronise stream data with hootsuite
						hsp.saveData ( app.hootsuite );

					}
					catch ( e ) {

						hsp.saveData ( app.hootsuite );

					}

				},

				// Close and unbind any existing page view
				destroyCurrentView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.destroyCurrentView is running. Unbinding events and removing previous view (" + app.views.currentView.viewName + ") from DOM: ", app.views.currentView ] );
					}

					// destroy the view
					app.views.currentView.undelegateEvents ();
					app.views.currentView.remove ();

					app.events.trigger ( "viewDestroyed", app.views.currentView.viewName );

					app.alertRemoveSpecial ( "ActivityTooLong" );

					// remove all listen bindings and event callbacks
					app.views.currentView.dispatcher.stopListening ();

					app.views.currentView = null;

				},

				show: function ( callback )
				{

					if ( DEBUG ) {
						app.logThis ( [ "BaseView.show for " + this.viewName + " is running. Rendering View: ", this ] );
					}

					if ( app.session.storage.user.get ( "id" ) && (
						                                              app.router.current ()
					                                              ).fragment[ 0 ] === "login" ) {

						return app.router.showIndex ();

					}

					// Close and unbind any existing page view
					if ( app.views.currentView ) {

						this.destroyCurrentView ();

					}

					// clear out any existing tooltips
					$ ( "body div[role='tooltip']" ).remove ();

					// generate header, only create once
					if ( !app.views.headerView ) {
						app.views.headerView = new HeaderFragmentView (); // create the view
					}

					// destroy the header view
					app.views.headerView.undelegateEvents ();
					app.views.headerView.remove ();

					// regenerate header view
					$ ( "#header" ).empty ().append ( app.views.headerView.render ().$el );

					// re-delegate header events
					app.views.headerView.delegateEvents ( app.views.headerView.events );

					// generate footer only once ever
					if ( !app.views.footerView ) {

						// create the view
						app.views.footerView = _.template ( FooterElementTpl );

						// generate the view
						$ ( "#footer" ).html ( app.views.footerView () );

					}

					// Render inside the page wrapper
					$ ( "#content" ).empty ().append ( this.render ().$el );

					// Re-delegate events (unbound when closed)
					this.delegateEvents ( this.events );

					// make dispatcher listen
					this.dispatcherListenTo ();

					// set the stream title to current view public name
					//if ( this.viewPublicName.length ) {
					//
					//	// hootsuite has a limit of 35 characters for the stream title
					//	hsp.updatePlacementSubtitle ( this.viewPublicName.substring ( 0, 35 ) );
					//
					//}

					// Establish the requested view into scope
					app.views.currentView = this;

					// make associated controlelr dispatcher
					this.runControllerViewBindings ();

					// trigger viewRendered event
					app.events.trigger ( "viewRendered", this.viewName );

					// set history location
					app.router.navigate ( this.viewDefaultLocation );

					// if requested, signal the caller that it is done
					if ( callback && typeof callback === "function" ) {
						callback ();
					}

					return this;

				}

			}
		);

	}
);
