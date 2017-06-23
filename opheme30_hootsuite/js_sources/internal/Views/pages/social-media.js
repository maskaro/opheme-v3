define (
	[
		"jquery",
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/overviews/_social-media.html",
		"textplugin!/templates/pages_full/social-media.html"
	], function ( $,
	              _,
	              app,
	              BaseView,
	              SocialMediaOverviewElementTpl,
	              SocialMediaPageTpl )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "SocialMediaView",

				viewPublicName: "Social Media Accounts",

				viewDefaultLocation: "social-media/view",

				eventList: [
					"doBuildAuthorisation",
					"doDestroyAuthorisation",
					"doCheckHandleStatus"
				],

				controllerName: "social-media",

				smType: null,

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.initialize is running." ] );
					}

					return this;

				},

				dispatcherListenTo: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.dispatcherListenTo is running." ] );
					}

					(
						new BaseView ()
					).dispatcherListenTo.apply ( this );

					// overwrite existing doViewRefresh event function
					this.dispatcher.stopListening ( app.events, "doViewRefresh" );

					this.dispatcher.listenTo (
						app.events, "doViewRefresh", function ()
						{

							if ( DEBUG ) {
								app.logThis ( [ "SocialMediaView.dispatcherListenTo.doViewRefresh is running." ] );
							}

							app.session.getSMHandles (
								{
									complete: function ()
									{

										// refresh the formChanged state
										this.formChanged = false;

										// re-render page
										this.show ();

									}.bind ( this )
								}
							);

						}.bind ( this )
					);

				},

				destructor: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.destructor is running." ] );
					}

					this.smType = null;

					(
						new BaseView ()
					).destructor.apply ( this );

				},

				events: {
					"click button[data-deauthorise]": "sm-deauthorise",
					"click a[data-authorise]"       : "sm-authorise"
				},

				"sm-deauthorise": function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.sm-deauthorise is running." ] );
					}

					app.eventPreventDefault ( evt );

					if ( app.activityRunning ( "SocialMediaView.viewSetup.doBuildAuthorisation.button" ) ) {

						app.logThis ( [ "SocialMediaView.sm-deauthorise: Activity already in progress. Skipping request ..." ] );

						app.showAlert ( "Please finish the previous authorisation attempt before attempting to Deauthorise an existing Social Media Account.", "warning" );

						return false;

					}

					var $el = $ ( evt.target );

					app.startActivity ( "SocialMediaView.sm-deauthorise", $ ( "div.panel-title h3" ) );

					app.showModal (
						"Deauthorise @" + $el.data ( "handle" ) + " - " + $el.data ( "type" ).toCamelCase ( true ) + " Account", "Are you sure you want to deauthorise @" + $el.data ( "handle" ) + "?",
						null,
						function ()
						{

							$el.attr ( "disabled", "disabled" );

							setTimeout (
								function ()
								{

									this.dispatcher.trigger ( "doDestroyAuthorisation", evt.target );

								}.bind ( this ), 0
							);

						}.bind ( this ),
						null, null, null, function ()
						{

							app.stopActivity ( "SocialMediaView.sm-deauthorise" );

						}
					);

				},

				"sm-authorise": function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.sm-authorise is running. Data: ", evt ] );
					}

					var $anchor = $ ( evt.target ),
					    handle  = $anchor.data ( "handle" );

					this.smType = $anchor.data ( "type" );

					$anchor.attr ( "disabled", "disabled" );

					this.dispatcher.trigger (
						"doBuildAuthorisation", function ( location, expires_in )
						{

							if ( !location ) {

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaView.sm-authorise.doBuildAuthorisation.button: Location came back null, code failed. " ] );
								}

								app.showAlert ( "Sorry, something seems to have gone wrong on our end. Please try again. Message: " + expires_in, "warning" );

								app.eventPreventDefault ( evt );

								$anchor.removeAttr ( "disabled" );

								return false;

							}

							if ( !app.startActivity ( "SocialMediaView.sm-authorise.doBuildAuthorisation.button", $ ( "div.panel-title h3" ) ) ) {

								if ( DEBUG ) {
									app.logThis ( [ "SocialMediaView.sm-authorise.doBuildAuthorisation.button: Action already in progress, skipping request ..." ] );
								}

								app.showAlert ( "Please finish the previous authorisation attempt before starting a new one.", "warning" );

								app.eventPreventDefault ( evt );

								$anchor.removeAttr ( "disabled" );

								return false;

							}

							location = location.replace ( "__smType__", this.smType );
							location += "&handle=" + handle;

							if ( DEBUG ) {
								app.logThis ( [ "SocialMediaView.sm-authorise.doBuildAuthorisation.button: Action started, monitoring ..." ] );
							}

							// check the result of the authorisation every few seconds
							app.addTimer (
								"button-" + this.smType, "interval",
								setInterval (
									function ()
									{

										this.dispatcher.trigger ( "doCheckHandleStatus", $anchor );

									}.bind ( this ), 2.5 * 1000
								)
							);

							// button self expiry
							app.addTimer (
								"authButtons", "timeout",
								setTimeout (
									function ()
									{

										if ( DEBUG ) {
											app.logThis ( [ "SocialMediaView.sm-authorise.doBuildAuthorisation.button.timeout: Code expired, refreshing ... " ] );
										}

										app.events.trigger ( "doViewRefresh" );

									}.bind ( this ), expires_in * 1000
								)
							);

							$anchor.attr ( "href", location );

						}.bind ( this )
					);

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( SocialMediaPageTpl );

					subTemplates.push (
						{
							"renderSocialMediaAccounts": _.template ( SocialMediaOverviewElementTpl )
						}
					);

					data = {
						data: {
							user          : app.session.storage.user.toJSON (),
							twitterHandles: app.session.storage.twitterHandles,
							smHandles     : app.session.storage.smHandles,
							smModules     : app.config.smModules
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "SocialMediaView.render: Data: ", data ] );
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
						app.logThis ( [ "SocialMediaView.viewSetup is running." ] );
					}

					// trigger view refresh if no activity in progress
					var fn = function ()
					{

						if ( !app.anyActivityRunning () ) {

							app.session.getSMHandles (
								{
									complete: function ()
									{

										// Render inside the page wrapper
										$ ( "#content" ).empty ().append ( this.render ().$el );

										// rebind events for newly generated page elements
										this.delegateEvents ( this.events );

									}.bind ( this )
								}
							);

						}
					}.bind ( this );

					// make this view refresh every 5 seconds to pick up any changes that might have been made on the server from a third party
					app.addTimer (
						"refreshSocialMediaView", "interval",
						setInterval ( fn, 15 * 1000 ), true
					);

					/* BOOTSTRAP TOUR CODE */

					app.views.tour = new Tour (
						{
							onStart: app.views.onTourStart,
							onEnd  : app.views.onTourEnd,
							steps  : [
								{
									orphan : true,
									title  : "Welcome to Opheme Wizard!",
									content: "Hi there! Well done signing up for Opheme, this walkthrough will show you how to use Opheme in just a few easy steps. Click on the Next button below and we will get started."

								},
								{
									element  : "#authorisation-status .panel-heading",
									placement: "bottom",
									title    : "Social Media Accounts",
									content  : "First, you need to authorise at least one Social Media Account, such as Twitter or Instagram."
								},
								{
									element  : "#hootsuite-accounts",
									placement: "bottom",
									title    : "Social Media Accounts",
									content  : "You can do this by clicking on the “Authorise” button located either next to one of the social media accounts that you’re currently using on Hootsuite, if you would like to use it with Opheme, or one of the buttons found at the bottom of this page."
								},
								{
									element  : "#new-sma",
									placement: "top",
									title    : "Add New Account",
									content  : "Upon clicking one of these buttons you will be taken to the respective Social Media provider’s authorisation page where you will be asked to allow Opheme (that’s us!) to use your account on your behalf (no funny business, Opheme will only ever do what you instruct us while using the app). Then, just click the 'Authorise App' button and, after a few seconds, you will find yourself back here where you are free to repeat these last few steps as many times as you like. Please click Next when ready."
								},
								{
									element  : "#discoverList",
									placement: "bottom",
									title    : "Navigation Bar",
									content  : "Excellent! Now you can see a bar at the top of your stream containing a dropdown and several buttons. Let’s have a look at what each of them does."
								},
								{
									element  : "#discoverList",
									placement: "bottom",
									title    : "Choose Discover",
									content  : "Choose Discover is a dropdown menu - let’s call it the Discover dropdown -, which allows you to see all of the Discovers you create while using our app. You can choose to view their posts streams by clicking on the menu and picking one to view. At the moment you don’t have any Discovers, we’ll get to that in a moment."
								},
								{
									element  : "#discoverControls",
									placement: "bottom",
									title    : "New Discover",
									content  : "The 'New Discover' <i class='fa fa-lg fa-plus'></i> button allows you to create new Discover, we’ll be doing that shortly."
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "Discovers List",
									content  : "The 'Home' <i class='fa fa-lg fa-home'></i> button takes you to a list of all your Discovers (similar to the dropdown menu) – let’s call this Discover List. There you are able to manage each Discover – pause/start, edit, or send them to oblivion a.k.a. remove I from your account!"
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "Settings",
									content  : "The 'Settings' <i class='fa fa-lg fa-gear'></i> button is another dropdown menu that gives you access to other parts of the app like ..."
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "Settings - continued",
									content  : "Social Media Accounts (current page),<br />Account Settings (change your first/last names or your Opheme account password),<br />Logout (disconnect from your Opheme account, allowing you to log into another account or create a brand new one)."
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "More",
									content  : "The 'More' <i class='fa fa-lg fa-ellipsis-v'></i> button is the final one available here and it gives you two options ..."
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "More - continued",
									content  : "Take the Tour – clicking his on any stream will start that stream’s part of the Tour (this walkthrough you’re now reading!) should you need a refresher."
								},
								{
									element  : ".hs_topBarControls",
									placement: "bottom",
									title    : "More - continued",
									content  : "Contact Us – clicking this will open your default Email client and start a new message directed at us – support@opheme.com. Use this whenever you need to get in touch regarding any issues, ideas, or requests you might have relevant to our Hootsuite App."
								},
								{
									orphan : true,
									title  : "Create New Discover",
									content: "Now let’s take a look at how quickly we can create a new Discover! Just click Next and you will be taken to the Discover Setup screen.",
									onNext : function ()
									{
										app.router.navigate (
											"discovers/view-setup", {
												trigger: true,
												replace: false
											}
										);
									}
								},
								{
									element: "",
									title  : "",
									content: ""
								}
							]
						}
					);

					if ( !window.tour_ended ) {

						// Initialize the tour
						app.views.tour.init ();

						// make sure tour always starts from 0
						app.views.tour.goTo ( 0 );

						// Start the tour
						app.views.tour.start ();

					}

				}

			}
		);

	}
);
