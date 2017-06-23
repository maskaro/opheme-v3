define (
	[
		"jquery",
		"underscore",
		"backbone",
		"moment"
	], function ( $,
	              _,
	              Backbone,
	              moment )
	{

		"use strict";

		var app = {

			/**
			 * APP GLOBAL CONFIG VALUES
			 */
			config: {
				root           : "/", // The root path to run the application through.
				URL            : "/", // Base application URL
				API            : "/api.php", // Base API URL (used by models & collections)
				backendURL     : "https://backend.opheme.com",
				hootsuite      : {
					apiKey              : "27x1hcr80rk084w44kogk8kss3ickdig5ab", // hootsuite api key
					receiverPath        : "/app_receiver.html", // two-way hootsuite communication facilitator
					streamContainerClass: "hs_stream" // base class used for all views root div
				},
				modules        : {
					main          : "discovers", // module loaded by default
					invalidAccount: "social-media" // module loaded if account is not valid
				},
				smModules      : [ "twitter", "instagram" ], // social media modules
				blockUIDefaults: {
					message        : "<i class='fa fa-cog fa-2x fa-spin my-spinner'></i><br>Please wait ...",
					css            : {
						border                 : "none",
						padding                : "15px",
						background             : "none",
						"-webkit-border-radius": "10px",
						"-moz-border-radius"   : "10px",
						opacity                : 1,
						color                  : "#3498db",
						"z-index"              : 10000
					},
					overlayCSS     : {
						backgroundColor: "#ffffff",
						opacity        : 1,
						cursor         : "wait"
					},
					// fadeIn time in millis; set to 0 to disable fadeIn on block
					fadeIn         : 500,
					// fadeOut time in millis; set to 0 to disable fadeOut on unblock
					fadeOut        : 500,
					ignoreIfBlocked: false // if element is already blocked, ignore it on TRUE
					//onBlock: function() {},
					//onUnblock: function(element, options) {}
				}
			},

			/* VIEW HELPERS */

			/**
			 * Holds references to view instances
			 */
			views: {
				headerView : null, // app header
				footerVIew : null, // app footer
				currentView: null, // app current content
				instances  : [], // collection of view instances
				tour       : null, // app tour
				onTourEnd  : function ()
				{

					app.callAPI (
						{
							method       : "POST",
							endpoint     : "/_local_change_tour_status.php",
							localEndpoint: true,
							data         : {
								"tour_ended": 1,
								"opheme_id" : app.session.storage.user.get ( "id" )
							}
						}, {
							success: function ()
							{

								window.tour_ended = 1;

							}
						}
					);

				},
				onTourStart: function ()
				{

					app.callAPI (
						{
							method       : "POST",
							endpoint     : "/_local_change_tour_status.php",
							localEndpoint: true,
							data         : {
								"tour_ended": 0,
								"opheme_id" : app.session.storage.user.get ( "id" )
							}
						}, {
							success: function ()
							{

								window.tour_ended = 0;

							}
						}
					);

				}
			},

			/* HOOTSUITE HELPERS */

			/**
			 * Stores the initial location of the stream
			 */
			initialRequestedPage: null,

			/**
			 * Hootsuite specific information
			 */
			hootsuite: { // synchronised in BaseView.viewGlobalSetup()
				securityInfo     : "", // holds the query String given by hootsuite user authentication - saved in Router.showIndex()
				currentURL       : "", // records current URL for restoring next time - saved in BaseController.checkSession()
				currentDiscoverId: "", // records currently viewed discover - saved in Session.selectCurrentDiscover()
				userKey          : "" // holds user API key - saved in Session.login(), removed in Session.logoutTasks()
			},

			/* MAP HELPERS */

			/**
			 * Default setup map location
			 */
			defaultMapLocation: "Cambridge, Cambridgeshire, UK",

			/**
			 * Setup and stream Map handles and information
			 */
			maps: {
				setup : {}, // holds info relating to Discovers Setup map
				stream: {} // holds info relating to current Discover Stream map
			},

			/* ACTIVITY HELPERS */

			/**
			 * Spinner template to be used at will
			 */
			spinner: "<i class='fa fa-cog fa-spin my-spinner'></i>",

			/**
			 * Helper var - keeps track of the activity spinner handles
			 */
			activitySpinners: [],

			/**
			 * Helper var - keeps track of all ongoing activities
			 */
			activities: [],

			/**
			 * Checks whether a specific activity is in progress
			 * @param {string} name Activity name
			 * @returns {boolean} True if in progress, false otherwise
			 */
			activityRunning: function ( name )
			{

				// if name given, return the search result as bool
				if ( !!name ) {
					return (
						this.activities.indexOf ( name ) > -1
					);
				}

				// no name given?
				return false;

			},

			/**
			 * Check for activities in progress
			 * @returns {boolean} False if no activity is currently in progress, its name otherwise
			 */
			anyActivityRunning: function ()
			{

				// if any activities, return name of first one, otherwise return false
				return !!this.activities.length ? this.activities[ 0 ] : false;

			},

			/**
			 * Marks an activity as having been started and attached a spinner to wherever needed, jquery object
			 * @param {String} name Activity name
			 * @param {jQuery?} $el jQuery element to use as a reference for inserting the spinner
			 * @param {boolean?} before If true will prepend to or insert spinner, else will append to or insert
			 * @param {boolean?} insert If true will insert, else will append or prepend
			 * @returns {boolean} True if activity successfully started, false otherwise
			 */
			startActivity: function ( name, $el, before, insert )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.startActivity is running. Data: ", name, $el, before, insert ] );
				}

				if ( this.activities.indexOf ( name ) > -1 ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.startActivity: Activity is already started. Activity name: ", name ] );
					}

					return false;

				}

				this.activities.push ( name );

				if ( $el === undefined || !(
						$el instanceof $
					) || !$el.length ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.startActivity: Activity started, however there was no jQuery element given for the spinner. Element: ", $el ] );
					}

					this.activitySpinners.push ( null );

					return true;

				}

				var $spinner = $ ( this.spinner );

				$spinner.attr ( "style", "margin-left: 10px" );

				if ( !!insert ) {

					if ( !!before ) {

						$spinner.insertBefore ( $el );

					}
					else {

						$spinner.insertAfter ( $el );

					}

				}
				else {

					if ( !!before ) {

						$spinner.prependTo ( $el );

					}
					else {

						$spinner.appendTo ( $el );

					}

				}

				$spinner.show ();

				this.activitySpinners.push ( $spinner );

				return true;

			},
			
			/**
			 * Stop an activity by name
			 * @param {String} name Activity name
			 * @returns {boolean}
			 */
			stopActivity: function ( name )
			{

				var index = this.activities.indexOf ( name );

				if ( index > -1 ) {

					this.alertRemoveSpecial ( "ActivityTooLong" );

					if ( this.activitySpinners[ index ] !== null ) {

						this.activitySpinners[ index ].remove ();

					}

					this.activitySpinners.splice ( index, 1 );
					this.activities.splice ( index, 1 );

					return true;

				}

				return false;

			},
			
			/**
			 * Stops all currently running activities
			 */
			stopAllActivities: function ()
			{

				var i, len;

				len = this.activitySpinners.length;

				for ( i = 0; i < len; i++ ) {

					if ( this.activitySpinners[ i ] !== null ) {

						this.activitySpinners[ i ].remove ();

					}

				}

				this.alertRemoveSpecial ( "ActivityTooLong" );

				this.activities       = [];
				this.activitySpinners = [];

				this.stopAndRemoveAllTimers ();

			},

			/**
			 * Helper var - keeps track of Timeout and Interval timers
			 */
			activityTimers: [],

			/**
			 * Adds timer to the tracker.
			 * @param {String} name  Unique String identifier.
			 * @param {String} type  "interval" or "timeout"
			 * @param {Number} timer Timer ID as given by setTimeout/setInterval
			 * @param {Boolean=} dontCheckDuration Pass TRUE to not also create the too long timer
			 * @returns {Number} New internal app timer ID. If name and type combo already exists, it returns their ID instead.
			 */
			addTimer: function ( name, type, timer, dontCheckDuration )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.addTimer is running. Timer info: ", name, type, timer ] );
				}

				var i, id;

				for ( i in this.activityTimers ) {

					if ( this.activityTimers.hasOwnProperty ( i ) && this.activityTimers[ i ].name === name && this.activityTimers[ i ].type === type ) {

						return parseInt ( i, 10 );

					}

				}

				this.activityTimers.push (
					{
						name : name,
						type : type,
						timer: timer
					}
				);

				id = this.activityTimers.length - 1;

				if ( !dontCheckDuration ) {

					// display alert after 30 seconds informing the user
					this.activityTimers[ this.activityTimers.length - 1 ].tooLongTimer =
						setTimeout (
							function ()
							{

								if ( this.activityTimers[ id ] !== undefined && this.activityTimers[ id ].name === name ) {

									this.alertsSpecial.ActivityTooLong = {
										alert: this.showAlert (
											"This activity has been running for 30 seconds. Click on this message to refresh this Stream if it continue for much longer.", "information", null, false, function ()
											{

												// stop existing activities and timers
												app.stopAllActivities ();
												app.stopAndRemoveAllTimers ();

												// refresh current view
												app.events.trigger ( "doViewRefresh" );

											}, true
										),
										id   : id,
										name : name
									};

								}

							}.bind ( this ), 30 * 1000
						);

				}

				return this.activityTimers.length - 1;

			},

			/**
			 * Stops and removes a timer from the timer tracker by the timer's internal app ID.
			 * @param {Number} i Internal app timer ID.
			 */
			stopAndRemoveTimerByID         : function ( i )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.stopAndRemoveTimerByID is running. Requested ID: ", i ] );
				}

				if ( typeof this.activityTimers[ i ] === "object" ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.stopAndRemoveTimerByID: Removing timer... Info: ", this.activityTimers[ i ], "Tracker Info: ", this.activityTimers ] );
					}

					switch ( this.activityTimers[ i ].type ) {

						case "interval":

							if ( DEBUG ) {
								this.logThis ( [ "App.stopAndRemoveTimerByID: Clearing interval with ID: ", this.activityTimers[ i ].timer ] );
							}

							clearInterval ( this.activityTimers[ i ].timer );

							break;

						case "timeout":

							if ( DEBUG ) {
								this.logThis ( [ "App.stopAndRemoveTimerByID: Clearing timeout with ID: ", this.activityTimers[ i ].timer ] );
							}

							clearTimeout ( this.activityTimers[ i ].timer );

							break;

					}

					this.activityTimers.splice ( i, 1 );

					if ( DEBUG ) {
						this.logThis ( [ "App.stopAndRemoveTimerByID: Timer removed. Tracker Info: ", this.activityTimers ] );
					}

					return true;

				}

				if ( DEBUG ) {
					this.logThis ( [ "App.stopAndRemoveTimerByID: Timer does not exist." ] );
				}

				return false;

			},
			/**
			 * Stops and removes a timer from the timer tracker by the timer's name and type.
			 * @param {String} name Unique String identifier.
			 * @param {String} type "interval" or "timeout"
			 */
			stopAndRemoveTimerByNameAndType: function ( name, type )
			{

				return this.activityTimers.some (
					function ( timer, i )
					{

						if ( timer.name === name && timer.type === type ) {

							return this.stopAndRemoveTimerByID ( i );

						}

					}, this
				);

			},
			/**
			 * Stops and removes all timers.
			 */
			stopAndRemoveAllTimers         : function ()
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.stopAndRemoveAllTimers is running. Tracker Info: ", this.activityTimers ] );
				}

				var i;

				for ( i = this.activityTimers.length; i > 0; i-- ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.stopAndRemoveAllTimers: Removing timer with ID: ", i - 1, "And name: ", this.activityTimers[ this.activityTimers.length - 1 ].name ] );
					}

					// the activityTimers array length is changed every time a timer is removed
					this.stopAndRemoveTimerByID ( this.activityTimers.length - 1 );

				}

			},

			/* ALERT HELPERS */

			/**
			 * Helper var - keeps track of all running alerts by their content
			 */
			alertTrackerByContent: [],

			/**
			 * Helper var - keeps track of all running alerts by their handle
			 */
			alertTrackerByHandle: [],

			/**
			 * Helper var - keeps track of special, unique alert handles
			 */
			alertsSpecial: {},

			/**
			 * Removes special alert by name, if any
			 * @param {String} name Alert name
			 * @param {function=} callback Function to run once done
			 */
			alertRemoveSpecial: function ( name, callback )
			{

				if ( this.alertsSpecial[ name ] && typeof this.alertsSpecial[ name ] === "object" ) {

					this.alertsSpecial[ name ].alert.close ();

					if ( this.activityTimers[ this.alertsSpecial[ name ].id ] !== undefined && this.activityTimers[ this.alertsSpecial[ name ].id ].name === name ) {

						clearTimeout (
							this.activityTimers.splice (
								this.alertsSpecial[ name ].id, 1
							)[ 0 ]
								.tooLongTimer
						);

					}

					delete this.alertsSpecial[ name ];

				}

				if ( typeof callback === "function" ) {
					callback ();
				}

			},

			/**
			 * Returns alert index from alertsByContent if it exists based on its text, false otherwise
			 * @param {String} text Alert text to check
			 * @returns {Boolean,int} False if not found, or its app.alertTrackerByContent array index
			 */
			alertExists: function ( text )
			{

				var i;

				for ( i in this.alertTrackerByContent ) {

					if ( this.alertTrackerByContent.hasOwnProperty ( i ) && this.alertTrackerByContent[ i ] === text ) {

						return parseInt ( i, 10 );

					}

				}

				return false;

			},

			/**
			 * Remove an alert by its contents
			 * @param {String} text Alert text
			 * @returns {boolean} True if removed, false otherwise
			 */
			alertRemove: function ( text )
			{

				var i;

				for ( i in this.alertTrackerByContent ) {

					if ( this.alertTrackerByContent.hasOwnProperty ( i ) && this.alertTrackerByContent[ i ] === text ) {

						delete this.alertTrackerByHandle[ i ];
						delete this.alertTrackerByContent[ i ];

						return true;

					}

				}

				return false;

			},

			/**
			 * Display global alert
			 * @param   {String}   text     Alert body
			 * @param   {String}   type     alert, success, error, warning, information, confirm
			 * @param    {Function?} callback [Optional] Callback function for when alert finished showing
			 * @param   {Number|Boolean?}   timeout  [Optional] Timeout, seconds (float/int)
			 * @param {Function?} callbackClosed [Optional] Callback function for when alert closed down
			 * @param {boolean?} onClick [Optional] Pass true to bind the closed callback to the click event of the alert
			 * @returns {Object}     NOTY Alert handle
			 */
			showAlert: function ( text, type, callback, timeout, callbackClosed, onClick )
			{

				var index = this.alertExists ( text ),
				    n, $alerts;

				if ( this.alertTrackerByHandle.hasOwnProperty ( index ) ) {

					return this.alertTrackerByHandle[ index ];

				} // if the same alert message is already on show, stop here

				timeout = (
					typeof timeout === "number" ? timeout * 1000 : (
						timeout === undefined ? 4.5 * 1000 : false
					)
				);

				$alerts = $ ( "#alerts" );

				// create the new alert
				n = $alerts.noty (
					{
						text    : text,
						type    : type,
						timeout : timeout,
						callback: {
							onShow    : function ()
							{

								// display the alert container if it is currently hidden
								if ( $alerts.is ( ":hidden" ) ) {
									$alerts.show ();
								}

							},
							afterClose: function ()
							{

								// stop tracking this alert
								this.alertRemove ( text );

								// hide the alert container if there are no more alerts on display
								if ( $alerts.find ( "li" ).length === 0 ) {

									setTimeout ( function () { $alerts.hide (); }, 600 );

								}

								if ( typeof callbackClosed === "function" ) {

									callbackClosed ();

								}

							}.bind ( this ),
							afterShow : function ()
							{

								if ( typeof callbackClosed === "function" && onClick ) {

									$ ( this ).click ( callbackClosed );

								}

								if ( typeof callback === "function" ) {

									callback ();

								}

							}
						}
					}
				);

				// track this new alert to help prevent duplicates
				this.alertTrackerByContent.push ( text );
				this.alertTrackerByHandle.push ( n );

				return n;

			},

			/**
			 * Display a global modal.
			 * @param {String}        title       Modal title
			 * @param {String,HTMLElement,jQuery} text        Modal content
			 * @param {Function?} onShowCallback Callback to run after modal is shown
			 * @param {Function?}      yesCallback [Optional] Function to run on Yes click
			 * @param {String?}        yesLabel    [Optional] Label for Yes button, defaults to Yes. Pass "no-display" to not show it at all
			 * @param {String?}        noLabel     [Optional] Label for No button, defaults to No. Pass "no-display" to not show it at all
			 * @param {Function?}      noCallback [Optional] Function to run on No click
			 * @param {Function?}      alwaysCallback [Optional] Function to run after any button click
			 * @returns {Object}      Modal
			 */
			showModal: function ( title, text, onShowCallback, yesCallback, yesLabel, noLabel, noCallback, alwaysCallback )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.showModal is running. Data: ", title, text, yesCallback, yesLabel, noLabel, noCallback, alwaysCallback ] );
				}

				// first, close off all existing alerts
				$.noty.closeAll ();
				$.noty.clearQueue ();

				var $modal = $ ( "#oModal" );

				$modal.find ( "#oModalLabel" ).html ( title );
				$modal.find ( ".modal-body" ).empty ().append ( text );

				if ( typeof yesLabel !== "string" ) {
					yesLabel = "Yes";
				}
				$modal.find ( "#oModalYes" ).html ( yesLabel );
				$modal.find ( "#oModalYes" ).show();

				if (yesLabel === "no-display") {
					$modal.find ( "#oModalYes" ).hide();
				}

				if ( typeof noLabel !== "string" ) {
					noLabel = "No";
				}
				$modal.find ( "#oModalNo" ).html ( noLabel );
				$modal.find ( "#oModalNo" ).show();

				if (noLabel === "no-display") {
					$modal.find ( "#oModalNo" ).hide();
				}

				$modal.find ( "#oModalYes" ).off ( "click" ).on (
					"click", function ()
					{

						if ( DEBUG ) {
							this.logThis ( [ "App.showModal: Yes clicked. " ] );
						}

						if ( typeof yesCallback === "function" ) {

							if ( DEBUG ) {
								this.logThis ( [ "App.showModal: Running Yes callback. " ] );
							}

							if ( yesCallback () === false ) {

								return false;

							}

						}

						if ( typeof alwaysCallback === "function" ) {

							setTimeout (
								function ()
								{

									if ( DEBUG ) {
										this.logThis ( [ "App.showModal: Running Always callback. " ] );
									}

									alwaysCallback ();

								}.bind ( this ), 0
							);

						}

						$modal.modal ( "hide" );

					}.bind ( this )
				);

				$modal.find ( "[data-dismiss]" ).off ( "click" ).on (
					"click", function ()
					{

						$modal.modal ( "hide" );

						if ( typeof noCallback === "function" ) {

							if ( DEBUG ) {
								this.logThis ( [ "App.showModal: Running No callback. " ] );
							}

							noCallback ();

						}

						if ( typeof alwaysCallback === "function" ) {

							setTimeout (
								function ()
								{

									if ( DEBUG ) {
										this.logThis ( [ "App.showModal: Running Always callback. " ] );
									}

									alwaysCallback ();

								}.bind ( this ), 0
							);

						}

					}.bind ( this )
				);

				$modal.modal ( "show" );

				if ( typeof onShowCallback === "function" ) {

					$modal.on (
						"shown.bs.modal", function ()
						{

							if ( DEBUG ) {
								app.logThis ( [ "App.showModal: Modal is now shown, running callback ... ", onShowCallback ] );
							}

							onShowCallback ();

						}
					);

				}

				return $modal;

			},

			/* AJAX HELPERS */

			/**
			 * AJAX helper method
			 * @param {{ method: String, endpoint: String, data: Object=, cache: Boolean=, headers: Object=, async: Boolean=, localEndpoint: Boolean= }} opts Set of options ({ method (GET/POST/PUT/PATCH/DELETE), endpoint(/oauth/auth_token), data(JSON), headers(JSON) })
			 * @param {{ complete: function=, success: function=, error: function= }?} callback Set of callback functions
			 * @returns {*}
			 */
			callAPI: function ( opts, callback )
			{

				if ( DEBUG ) {
					this.logThis ( [ "app.callAPI: AJAX callAPI: ", opts, callback ] );
				}

				var data = _.extend (
					    {}, opts.data || {}, {
						    "request_method" : opts.method,
						    "request_apipath": opts.endpoint
					    }
				    ),
				    url  = opts.localEndpoint ? opts.endpoint : this.config.API;

				return $.ajax (
					{
						async      : (
							typeof opts.async !== "boolean" ? true : opts.async
						),
						// false by default, otherwise use option if available
						cache      : opts.cache || false,
						url        : url,
						dataType   : "json",
						contentType: "application/json",
						type       : "POST",
						processData: false,
						beforeSend : function ( xhr )
						{

							if ( opts.headers ) {

								var header;

								for ( header in opts.headers ) {

									if ( opts.headers.hasOwnProperty ( header ) ) {

										xhr.setRequestHeader ( header, opts.headers[ header ] );

									}

								}

							}

						},
						data       : JSON.stringify (
							{
								data: data
							}
						),
						/**
						 *
						 * @param {{error: String=, error_description: String=, error_code: number=, data: Object=, meta: Object=}} data
						 * @param textStatus
						 * @param jqXHR
						 */
						success    : function ( data, textStatus, jqXHR )
						{

							if ( DEBUG ) {
								app.logThis ( [ "app.callAPI: Ajax Success Response: ", data, textStatus, jqXHR ] );
							}

							if ( callback ) {

								// either there is data with no error, or there is no data for a successful action with no content response
								if ( (
									     data && !data.error
								     ) || !data ) {

									if ( callback && typeof callback.success === "function" ) {

										if ( data && data.data ) {

											callback.success ( data.data, data.meta || null );

										}
										else {

											callback.success ( data ); // done successfully, but no content within data.data

										}

									}

								}
								else {

									// error object = { error, error_code, error_description }

									if ( callback && typeof callback.error === "function" ) {

										callback.error ( data.error_description, data.error_code );

									}

								}

							}

						},
						error      : function ( jqXHR, textStatus, errorThrown )
						{

							if ( DEBUG ) {
								app.logThis ( [ "app.callAPI: AJAX Error Response: ", jqXHR, textStatus, errorThrown ] );
							}

							if ( callback && typeof callback.error === "function" ) {

								callback.error ( textStatus /*data*/ );

							}

						},
						complete   : function ( /*jqXHR, textStatus*/ )
						{

							if ( callback && typeof callback.complete === "function" ) {

								if ( DEBUG ) {
									app.logThis ( [ "app.callAPI: AJAX Complete is running as requested. Function: ", callback.complete ] );
								}

								callback.complete ();

							}

						}
					}
				);

			},

			/* LOGGER HELPER */

			/**
			 * Development tool, logs any arguments given to console
			 * @param {Array} args Set of arguments
			 */
			logThis: function ( args )
			{

				var time    = new Date (),
				    timeStr =
					    time.getHours () + ":" +
					    time.getMinutes () + ":" +
					    time.getSeconds () + "." +
					    time.getMilliseconds (),
				    error   = new Error (),
				    message;

				if ( error.stack ) {
					message = "\n" + error.stack.split ( "\n" )[ 2 ] + "\n\n";
				}
				else { // IE, no stack trace
					message = "";
				}

				console.log ( "Time: ", timeStr, "\n", "URL: ", window.location.href, "\n", "Arguments: ", args, "\n", "Messages: ", message );

			},

			/* TIME HELPERS */

			/**
			 * Get current epoch time
			 * @param {Boolean} millis Pass true to get milliseconds instead
			 * @returns {int} Epoch time in seconds or milliseconds
			 */
			getTime: function ( millis )
			{

				var m = moment (); // +new Date();

				return (
					millis === true ? m.valueOf () : m.unix ()
				);

			},

			/**
			 * Humanise time in seconds
			 * @param {int} sec Time in seconds
			 * @returns {String} Formatted time - seconds, minutes, hours, ...
			 */
			timeUntilHuman: function ( sec )
			{

				return moment.duration ( sec, "seconds" ).humanize ();

			},

			/* EVENT HELPERS */

			/**
			 * Events aggregator
			 */
			events: _.extend ( {}, Backbone.Events ),

			/**
			 * Stops events from doing their usual stuff
			 * @param {Object} event Event object
			 * @param {Boolean?} continuePropagation Pass true to allow propagation of event to parent listeners
			 * @param {Boolean?} continueImmediatePropagation Pass true to allow propagation of event to other listeners on the same element
			 */
			eventPreventDefault: function ( event, continuePropagation, continueImmediatePropagation )
			{

				if ( event.preventDefault ) { // W3C variant

					// stop the default browser behaviour for this event on this element
					event.preventDefault ();

					if ( !continuePropagation ) {

						// also stop all parent events from triggering
						if ( event.stopPropagation ) {
							event.stopPropagation ();
						}

					}

					if ( !continueImmediatePropagation ) {

						// also stop all other events bound to this exact same element from triggering
						if ( event.stopImmediatePropagation ) {
							event.stopImmediatePropagation ();
						}

					}

				}
				else { // IE<9 variant:

					// stop the default browser behaviour for this event on this element
					if ( event.returnValue ) {
						event.returnValue = false;
					}

					if ( !continuePropagation ) {

						// also stop all parent events from triggering
						if ( event.cancelBubble ) {
							event.cancelBubble ();
						}

					}

				}

			},

			/* OTHER HELPERS */

			/**
			 * Scroll page view to top of a certain element.
			 * @param {jQuery} $el  jQuery element
			 * @param {Function?} callback Callback function for when animation completes
			 * @param {Boolean?} dontAnimate Pass TRUE to not use animation for this
			 */
			scrollBodyToTopOf: function ( $el, callback, dontAnimate )
			{

				this.scrollElementToTopOf ( $ ( "body" ), $el, callback, 1, !dontAnimate, $ ( "#navigation-container" ).height () + 12, false );

			},

			/**
			 * Scroll page view to top of a certain element.
			 * @param {jQuery}          $container  jQuery element, container within which to scroll
			 * @param {jQuery,Number}   $el         jQuery element to scroll to or number of pixels to scroll
			 * @param {Function?}        callback   Callback function for when animation completes
			 * @param {Number?}          time        Animation duration
			 * @param {Boolean?}         animate     Animation boolean - false/null disables it, true makes it work
			 * @param {Number?}          offset      Number of pizels to be added on top of the final position, applied as a negative number
			 * @param {Boolean?}         overflowContainer Pass TRUE if $container has a fixed height with overflow set
			 */
			scrollElementToTopOf: function ( $container, $el, callback, time, animate, offset, overflowContainer )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.scrollElementToTopOf is running. Data: ", $container, $el, callback, time, animate, offset, overflowContainer ] );
				}

				if ( !(
						$container instanceof jQuery && $container.length
					) ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.scrollElementToTopOf: Container is not an object or jQuery set contains 0 elements, skipping request ..." ] );
					}

					if ( typeof callback === "function" ) {

						callback ();

					}

					return false;

				}

				if ( typeof time !== "number" ) {

					time = 1;

				}

				if ( typeof offset !== "number" ) {

					offset = 0;

				}
				else {

					offset *= -1;

				}

				var position = (
					               $el instanceof jQuery && $el.length
				               ) ? (
					                   overflowContainer ? $el[ 0 ].offsetTop : $el.offset ().top
				                   ) + offset : (
					               typeof $el === "number" ? $el + offset : offset
				               );

				if ( animate ) {

					$container.animate (
						{
							scrollTop: position
						}, time * 1000, "swing", callback
					);

				}
				else {

					$container.scrollTop ( position );

					if ( typeof callback === "function" ) {

						callback ();

					}

				}

			},

			/**
			 * Expand container to fit its children.
			 * @param   {jQuery} $container jQuery container to resize
			 * @param   {Function} callback  Function to run once complete
			 * @param   {Boolean} animate    Pass true to animate the resize
			 * @returns {Boolean}  False if container is not a jQuery element
			 */
			setHeightOfElementFromChildren: function ( $container, callback, animate )
			{

				if ( DEBUG ) {
					this.logThis ( [ "App.setHeightOfElementFromChildren is running. Data: ", $container, callback, animate ] );
				}

				if ( typeof $container !== "object" || !$container.length ) {

					if ( DEBUG ) {
						this.logThis ( [ "App.setHeightOfElementFromChildren: Container is not an object or jQuery set contains 0 elements, skipping request ..." ] );
					}

					if ( typeof callback === "function" ) {
						callback ();
					}

					return false;

				}

				var totalHeight = 0;

				$container.children ().each (
					function ()
					{
						totalHeight += $ ( this ).outerHeight ( true );
					}
				);

				if ( animate ) {

					$container.animate (
						{
							height: totalHeight
						}, 1000, "swing", callback
					);

				}
				else {

					$container.css ( "height", totalHeight );

					if ( typeof callback === "function" ) {
						callback ();
					}

				}

			}

		};

		return app;

	}
);
