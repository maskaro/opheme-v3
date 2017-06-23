define (
	[
		"jquery",
		"underscore",
		"app",
		"moment",
		"textplugin!/templates/page_elements/forms/_stream-reply.html",
		"infinity",
		"omspiderfier",
		"markerclusterer",
		"gmapsmaplabel"
	], function ( $,
	              _,
	              app,
	              moment,
	              ReplyElementTpl,
	              InfinityList )
	{

		"use strict";

		google.maps.visualRefresh = true;

		return function ( $el, options )
		{

			/**
			 * Default options
			 * @type {{omap: {api: string, gmaps: {options: {map_centre: {lat: number, lng: number}, zoom: number, mapTypeId: (google.maps.MapTypeId|number|string), panControl: boolean, zoomControl: boolean, mapTypeControl: boolean, mapTypeControlOptions: {style: (google.maps.MapTypeControlStyle|number|string), position: (google.maps.ControlPosition|number|string)}, scaleControl: boolean, streetViewControl: boolean, overviewMapControl: boolean}, mc_options: {gridSize: number, maxZoom: number, batchSize: number}, oms_options: {markersWontMove: boolean, markersWontHide: boolean, keepSpiderfied: boolean, nearbyDistance: number, circleSpiralSwitchover: number, legWeight: number, legColours: {usual: {HYBRID: string, SATELLITE: string, TERRAIN: string, ROADMAP: string}, highlighted: {HYBRID: string, SATELLITE: string, TERRAIN: string, ROADMAP: string}}}}, noOfMarkers: number, uniqueMarkers: boolean}, messagesContainerQueryString: string, stopOnMessageLimit: boolean, timeout: number, max_refresh_items: number, max_queue_items: number, display_freq: number, iconShapes: {instagram: string, twitter: string}, iconColours: {instagram: {none: string, negative: string, neutral: string, positive: string}, twitter: {none: string, negative: string, neutral: string, positive: string}}, ageThresholdOpacity: {10: number, 20: number, 30: number, 40: number, 50: number, 60: number, old: number}}}
			 * @private
			 */
			var _defaults             =
			    {
				    omap                        : { // map related options
					    api          : "gmaps", // must be declared, defaults to "gmaps"
					    gmaps        : {
						    options          : { // map specific options, must consult with API
							    map_centre           : { // custom container, defines map centre coords
								    lat: 52.2100,
								    lng: 0.1300
							    },
							    zoom                 : 8,
							    mapTypeId            : google.maps.MapTypeId.ROADMAP,
							    panControl           : false,
							    zoomControl          : true,
							    mapTypeControl       : true,
							    mapTypeControlOptions: {
								    style   : google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
								    position: google.maps.ControlPosition.TOP_CENTER
							    },
							    scaleControl         : false,
							    streetViewControl    : false,
							    overviewMapControl   : false
						    },
						    mc_options       : { // google maps marker clusterer options
							    gridSize     : 20,
							    maxZoom      : 14,
							    batchSize    : 100,
							    batchSizeIE  : 100,
							    ignoreHidden : true, // don't include hidden markers when drawing clusters
							    averageCenter: true // place overlay at the centre of the cluster
						    },
						    // google maps overlapping marker spiderfier options
						    oms_options      : {
							    // saves memory if markers don't need to be moved
							    markersWontMove       : true,
							    // saves memory if markers don't need to be hidden/shown
							    markersWontHide       : false,
							    // keep markers spiderfied after one is clicked
							    keepSpiderfied        : true,
							    // pixel distance to qualify a marker as overlapping with another
							    nearbyDistance        : 30,
							    // minimum number of markers to be fanned out into a spiral
							    circleSpiralSwitchover: 5,
							    // connecting lines thickness
							    legWeight             : 1.5,
							    // connecting lines colours
							    legColours            : {
								    usual      : {
									    HYBRID   : "#fff",
									    SATELLITE: "#fff",
									    TERRAIN  : "#444",
									    ROADMAP  : "#444"
								    },
								    highlighted: {
									    HYBRID   : "#f00",
									    SATELLITE: "#f00",
									    TERRAIN  : "#f00",
									    ROADMAP  : "#f00"
								    }
							    }
						    },
						    // meters / pixel        1      2      3     4     5     6     7    8    9    10  11  12  13  14 15  16    17    18    19    20   21     22     23
						    zoomLevels_MperPx: [ 0, 21282, 16355, 10064, 5540, 2909, 1485, 752, 378, 190, 95, 48, 24, 12, 6, 3, 1.48, 0.74, 0.37, 0.19, 0.1, 0.05, 0.025, 0.013 ]
					    },
					    // max number of markers on map
					    noOfMarkers  : 300,
					    // same location markers - set true to only drop one pin on the map for one unique location
					    uniqueMarkers: false
				    },
				    // DOM query string for messages list container
				    messagesContainerQueryString: "#messages",
				    // stop message and marker processing if message limit is hit
				    stopOnMessageLimit          : false,
				    // check timeout in ms
				    timeout                     : 10000,
				    // max number of new messages at a time from server
				    max_refresh_items           : 10,
				    // max number of messages from queue to be displayed at a time
				    max_queue_items             : 1000,
				    // message display frequency in ms
				    display_freq                : 5000,
				    // ms time delay until a message is marked as unread in the UI
				    newMessageUnreadDelay       : 0,
				    // map pin icons
				    iconShapes                  : {
					    "instagram": "M 26.688,0L 5.313,0 C 2.391,0,0,2.391,0,5.313l0,21.375 c0,2.922, 2.391,5.313, 5.313,5.313l 21.375,0 c 2.922,0, 5.313-2.391, 5.313-5.313L 32,5.313 C 32,2.391, 29.609,0, 26.688,0z M 10.244,14l 11.512,0 c 0.218,0.627, 0.338,1.3, 0.338,2c0,3.36-2.734,6.094-6.094,6.094c-3.36,0-6.094-2.734-6.094-6.094 C 9.906,15.3, 10.025,14.627, 10.244,14z M 28,14.002L 28,22 l0,4 c0,1.1-0.9,2-2,2L 6,28 c-1.1,0-2-0.9-2-2l0-4 L 4,14.002 L 4,14 l 3.128,0 c-0.145,0.644-0.222,1.313-0.222,2c0,5.014, 4.079,9.094, 9.094,9.094c 5.014,0, 9.094-4.079, 9.094-9.094 c0-0.687-0.077-1.356-0.222-2L 28,14 L 28,14.002 z M 28,7c0,0.55-0.45,1-1,1l-2,0 c-0.55,0-1-0.45-1-1L 24,5 c0-0.55, 0.45-1, 1-1l 2,0 c 0.55,0, 1,0.45, 1,1L 28,7 z",
					    "twitter"  : "M 32,6.076c-1.177,0.522-2.443,0.875-3.771,1.034c 1.355-0.813, 2.396-2.099, 2.887-3.632 c-1.269,0.752-2.674,1.299-4.169,1.593c-1.198-1.276-2.904-2.073-4.792-2.073c-3.626,0-6.565,2.939-6.565,6.565 c0,0.515, 0.058,1.016, 0.17,1.496c-5.456-0.274-10.294-2.888-13.532-6.86c-0.565,0.97-0.889,2.097-0.889,3.301 c0,2.278, 1.159,4.287, 2.921,5.465c-1.076-0.034-2.088-0.329-2.974-0.821c-0.001,0.027-0.001,0.055-0.001,0.083 c0,3.181, 2.263,5.834, 5.266,6.438c-0.551,0.15-1.131,0.23-1.73,0.23c-0.423,0-0.834-0.041-1.235-0.118 c 0.836,2.608, 3.26,4.506, 6.133,4.559c-2.247,1.761-5.078,2.81-8.154,2.81c-0.53,0-1.052-0.031-1.566-0.092 c 2.905,1.863, 6.356,2.95, 10.064,2.95c 12.076,0, 18.679-10.004, 18.679-18.68c0-0.285-0.006-0.568-0.019-0.849 C 30.007,8.548, 31.12,7.392, 32,6.076z",
					    "highlight": "m1.25,35.18401l35.52306,0l10.97655,-33.74676l10.97734,33.74676l35.52306,0l-28.73936,20.85617l10.9783,33.74672l-28.73933,-20.85716l-28.73854,20.85716l10.97746,-33.74672l-28.73853,-20.85617l0,0z"
				    },
				    // map pin icon sentiment colours
				    iconColours                 : {
					    "instagram": {
						    "none"    : "#8B4513",
						    "negative": "red",
						    "neutral" : "blue",
						    "positive": "green"
					    },
					    "twitter"  : {
						    "none"    : "#1E90FF",
						    "negative": "red",
						    "neutral" : "blue",
						    "positive": "green"
					    },
					    "highlight": "yellow"
				    },
				    // up to threshold has opacity
				    ageThresholdOpacity         : {
					    "10": 1,
					    "20": 0.85,
					    "30": 0.70,
					    "40": 0.55,
					    "50": 0.50,
					    "60": 0.45,
					    old : 0.35
				    },
				    // reply form message text limit
				    messageReplyCharacterLimit  : {
					    twitter  : 140,
					    instagram: 500
				    },
				    // jquery selector for load older messages button(s)
				    loadMoreButtonSelector      : "#load-more, #load-more-map"
			    },

			    /**
			     * Opheme reference
			     */
			    _self                 = this, // oPheme reference

			    /**
			     * Internal function tracker
			     * @type {Object}
			     * @private
			     */
			    _fn                   = {},

			    /**
			     * Internal tracking of things
			     * @private
			     */
			    _internal             = {
				    // map handle
				    map_handle                    : null,
				    // map bounds
				    map_bounds                    : null,
				    // map marker clusterer handle
				    map_mc_handle                 : null,
				    // map spiderfier handle
				    map_oms_handle                : null,
				    // keep track of markers
				    map_markers                   : [],
				    // keep track of temporary markers waiting to be added to map
				    map_temp_markers              : [],
				    // custom container in which to display the map
				    map_container                 : null,
				    // job messages json array
				    messages_data                 : {
					    messages: []
				    },
				    //messages                  : [],
				    // job messages temporary json array
				    messages_temp                 : [],
				    // job old messages temporary json array
				    messages_old_temp             : [],
				    // job messages compiled html
				    compiledMessages              : [],
				    // job messages total count
				    total_message_count           : 0,
				    // ajax timer handles
				    timers                        : {
					    // job timer
					    job         : null,
					    // queue timer
					    queue       : null,
					    // interactions timer
					    interactions: null,
					    // retweets timer
					    retweets    : null,
					    // favourites timer
					    favourites  : null,
					    // follows timer
					    follows     : null
				    },
				    // date to be used as a default
				    default_check_date            : "1970-01-01 00:00:00",
				    // keep a references to the newest message timestamp of the last batch of older messages
				    newest_older_message_timestamp: "1970-01-01 00:00:00",
				    // ajax last check timestamps
				    last_checks                   : {
					    // job last check
					    job         : "1970-01-01 00:00:00",
					    // interactions last check
					    interactions: "1970-01-01 00:00:00",
					    // retweets last check
					    retweets    : "1970-01-01 00:00:00",
					    // favouries last check
					    favourites  : "1970-01-01 00:00:00",
					    // follows last check
					    follows     : "1970-01-01 00:00:00"
				    },
				    // API call cursors
				    cursors                       : {
					    // job messages cursor
					    messages: null
				    },
				    // underscore compiled template
				    compiledTpl                   : null,
				    // current list in view
				    currentListView               : "messages",
				    // previous list scroll position
				    previousListScrollPosition    : 0,
				    // list view types
				    allowedListTypes              : [ "messages", "interactions" ],
				    // holds references to objects required to keep track of current highlighted marker and message
				    highlights                    : {
					    // reference to highlighted html message
					    message   : null,
					    // reference to highlighted marker
					    marker    : null,
					    // reference to highlighted marker's icon
					    markerIcon: null,
					    // reference to highlighted marker's z-index
					    zIndex    : null
				    },
				    // handle on all map spider pools visuals
				    omsSpiderPools                : [],
				    // $ references for watched elements
				    scrollWatchList               : [],
				    // timeout ID
				    scrollWatchBuffer             : null,
				    // first time the stream was populated with messages
				    firstTime                     : true,
				    // keep track of how many images have been processed
				    processedImages               : 0
			    },

			    /**
			     * Public settings
			     * @type {{smHandles: Object[], template: Object}}
			     * @private
			     */
			    _settings             = {
				    smHandles: null, // given from the outside
				    template : null // given from the outside
			    },

			    /**
			     * Block messages container with a default No Messages message.
			     */
			    showNoMessagesBlockUI = function ()
			    {

				    // default message
				    $ ( _settings.messagesContainerQueryString ).block (
					    _.extend (
						    {}, app.config.blockUIDefaults, {
							    message: "There is no content to view at the moment. <br>You can safely move away from this page, we will continue looking and let you know when there is.",
							    css    : _.extend (
								    {}, app.config.blockUIDefaults.css, {
									    "width": "50%"
								    }
							    )
						    }
					    )
				    );

			    },

			    /**
			     * Unblock messages container.
			     */
			    hideMessagesBlockUI   = function ()
			    {

				    $ ( _settings.messagesContainerQueryString ).unblock ();

			    },

			    /**
			     * Initial Opheme setup
			     * @returns {*} Null if no social media handles or message template given
			     * @private
			     */
			    _init                 = function ()
			    {

				    // add custom settings to defaults, overriding as necessary
				    _settings = _.extend ( {}, _defaults, _settings, options );

				    if ( app.views.currentView.viewName !== "MainStreamView" ) {

					    _internal.doNotSetupMap = true;

				    }
				    else {

					    if ( !_settings.smHandles || !_settings.template ) {

						    if ( DEBUG ) {
							    app.logThis ( [ "oPheme._init: WARNING: For Streams, Social Media Handles ('smHandles' option) AND Message Template ('template' option) MUST be given. Given options: ", options ] );
						    }

						    return null;

					    }

				    }

				    if ( !(
						    $el instanceof jQuery
					    ) ) {

					    if ( DEBUG ) {
						    app.logThis ( [ "oPheme._init: Map element given is not a valid jQuery element. Given value: ", $el ] );
					    }

					    return null;

				    }

				    // jquery element reference
				    _internal.map_container = $el;

				    // stream only setup
				    if ( !_internal.doNotSetupMap ) {

					    if ( _settings.template ) {
						    _internal.compiledTpl = _.template ( _settings.template );
					    }

					    InfinityList.config.PAGE_TO_SCREEN_RATIO = 7; // number of elements per infinity page
					    InfinityList.config.SCROLL_THROTTLE = 25; // ms response time to scroll

					    _fn.initialiseListView ();

					    _fn.hootsuiteSetup ();

					    // default message
					    showNoMessagesBlockUI ();

					    $ ( _settings.loadMoreButtonSelector ).find ( "a" ).click ( _fn.fetchOlderMessages );

				    }

			    },

			    // custom scroll plugin name
			    scrollPluginName      = "scrolledIntoView",

			    // custom scroll plugin settings
			    scrollPluginSettings  =
			    {
				    // runs when the element is scrolled into view
				    scrolledin     : function ( /*event*/ )
				    {

					    var $elem        = this,
					        messageIndex = this.data ( "messageIndex" ),
					        messageData  = _internal.messages_data.messages[ messageIndex ];

					    if ( DEBUG ) {
						    app.logThis ( [ "oPheme.scrollPluginSettings.scrolledin is running. Element: ", $elem ] );
					    }

					    // message has interactions, but current view is messages
					    if ( messageData.interactions && _internal.currentListView !== "interactions" ) {

						    if ( DEBUG ) {
							    app.logThis ( [ "oPheme.scrollPluginSettings.scrolledin: Not marking element as old since it still has unseen interactions, skipping ... Element: ", $elem ] );
						    }

						    return;

					    }

					    // add delay to marking message as read
					    setTimeout (
						    function ()
						    {

							    var $elements = $elem.find ( "a.hs_isNewMessage, .hs_comment.hs_isNewMessage" ),
							        i, len, actualPosition;

							    // mark item as old
							    $elements.addClass ( "hs_isOldMessage" );
							    $elem.addClass ( "hs_isOldMessage" );

							    // also mark the raw message as old
							    messageData.isNew = false;

							    // check interactions, if any
							    if ( messageData.interactions ) {

								    len = messageData.interactions.messages.length;

								    for ( i = 0; i < len; i++ ) {

									    messageData.interactions.messages[ i ].isNew = false;

								    }

							    }

							    // calculate actual position of the message since messages are reversed when displayed
							    //actualPosition = _internal.total_message_count - 1 - messageIndex;
							    actualPosition = messageIndex;

							    _fn.processRawMessage ( messageData, true, actualPosition );

							    // update counts
							    _fn.updateVisualCounts ( -1, $elem.find ( ".hs_comment" ).length * -1 );

							    setTimeout (
								    function ()
								    {

									    $elements.removeClass ( "hs_isNewMessage" );
									    $elem.removeClass ( "hs_isNewMessage" );

								    }, 500
							    );

						    }, _settings.newMessageUnreadDelay
					    );

				    },
				    // runs when element is scrolled out of view
				    //scrolledout    : null,
				    // in ms
				    throttleTimeout: 0
			    },

			    /**
			     * Sets up existing stream map markers and messages
			     */
			    reInitialiseStream    = function ()
			    {

				    if ( DEBUG ) {
					    app.logThis ( [ "oPheme.reInitialiseStream is running." ] );
				    }

				    // save current noOfMarkers limit
				    var noOfMarkersTemp = _settings.omap.noOfMarkers;

				    // change current limit to something big -> 32768 - 1 -> so that all current messages are parsed at once
				    _settings.omap.noOfMarkers = Math.pow ( 2, 15 ) - 1;

				    // parse existing messages
				    _fn.parseMessages ();

				    // restore limit
				    _settings.omap.noOfMarkers = noOfMarkersTemp;

			    };

			/**
			 * Gets the current list view
			 * @returns {string} Current list view - messages / interactions
			 */
			_self.getCurrentListView = function ()
			{

				return _internal.currentListView;

			};

			/**
			 * Retrieves a duplicate set of internal message and marker data
			 * @returns {{messages: Array.<JSON>}}
			 */
			_self.getCompiledMessagesAndMarkers = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.getCompiledMessagesAndMarkers is running." ] );
				}

				return {
					messages: _internal.messages_data.messages.slice ( 0 )
				};

			};

			/**
			 * Creates the list view
			 */
			_fn.initialiseListView = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.initialiseListView is running." ] );
				}

				if ( !!app.maps.stream.listView ) {

					// empty the list
					app.maps.stream.listView.remove ();

				}

				var $container = $ ( _settings.messagesContainerQueryString );

				app.maps.stream.listView = new InfinityList.ListView (
					$container, {
						lazyFn      : _fn.doPageSetup,
						scrollParent: $container
					}
				);

				// monitor scroll events
				$container.scroll (
					function ( event )
					{

						if ( !_internal.scrollWatchBuffer ) {

							_internal.scrollWatchBuffer = setTimeout (
								function ()
								{

									if ( DEBUG ) {
										app.logThis ( [ "oPheme.initialiseListView.$container.scroll.setTimeout is running." ] );
									}

									_fn.checkListItemsInView ( event );

									_internal.scrollWatchBuffer = null;

								}, scrollPluginSettings.throttleTimeout
							);

						}

					}
				);

			};

			// Custom $ plugin for monitoring elements on scroll to check whether they are in view
			$.fn[ scrollPluginName ] = function ( options )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme." + scrollPluginName + " is running. Elements: ", this ] );
				}

				options = $.extend ( {}, scrollPluginSettings, options );

				this.each (
					function ()
					{

						var $elem    = $ ( this ),
						    instance = $.data ( this, scrollPluginName );

						if ( DEBUG ) {
							app.logThis ( [ "oPheme." + scrollPluginName + ": Binding to: ", $elem, "Instance data: ", instance ] );
						}

						// if already initialised, just change options
						if ( instance ) {
							instance.options = options;
						}
						else {
							// otherwise, save the instance data
							instance = $.data ( this, scrollPluginName, _fn.scrollPluginMonitor ( $elem, options ) );
							$elem.on (
								"remove", $.proxy (
									function ()
									{

										// remove data when element is destroyed
										$.removeData ( this, scrollPluginName );
										_fn.scrollPluginUnMonitor ( instance );

									}, this
								)
							);
						}
					}
				);

				return this;
			};

			// add element to scroll watch list
			_fn.scrollPluginMonitor = function ( $element, options )
			{

				var item = { $element: $element, options: options, invp: false };

				_internal.scrollWatchList.push ( item );

				return item;

			};

			// remove element from scroll watch list
			_fn.scrollPluginUnMonitor = function ( item )
			{

				var i,
				    len = _internal.scrollWatchList.length;

				for ( i = 0; i < len; i++ ) {

					if ( _internal.scrollWatchList[ i ] === item ) {

						_internal.scrollWatchList.splice ( i, 1 );

						item.$element = null;

						return;

					}

				}

			};

			// check all watched items for scroll position changes
			_fn.checkListItemsInView = function ( event )
			{

				var $container = $ ( _settings.messagesContainerQueryString );

				$.each (
					_internal.scrollWatchList, function ()
					{

						//noinspection JSPotentiallyInvalidUsageOfThis
						if ( this.invp ) { // only run this if element is not in viewport

							return;

						}

						if ( _fn.inViewTestElement ( this.$element, $container.height (), $container.scrollTop () ) ) {

							//if ( !this.invp ) {

							//noinspection JSPotentiallyInvalidUsageOfThis
							this.invp = true;

							if ( typeof this.options.scrolledin === "function" ) {

								this.options.scrolledin.call ( this.$element, event );

							}

							//this.$element.trigger ( "scrolledin", event );

							//}

						}
						//else if ( this.invp ) {
						//
						//	this.invp = false;
						//
						//	if ( typeof this.options.scrolledout === "function" ) {
						//
						//		this.options.scrolledout.call ( this.$element, event );
						//
						//	}
						//
						//	//this.$element.trigger ( "scrolledout", event );
						//
						//}

					}
				);

			};

			/**
			 * Checks whether an element is within container's viewport
			 * @param {jQuery} $elem Element to check
			 * @param {number} containerHeight Height of container element
			 * @param {number} containerScrollTop Current scroll position of the container
			 * @returns {boolean} True if in view, false otherwise
			 */
			_fn.inViewTestElement = function ( $elem, containerHeight, containerScrollTop )
			{

				var elemTop    = $elem.position ().top,
				    elemBottom = elemTop + containerHeight;

				return (
					(
						elemTop >= 0 /* -50 */ && elemTop <= containerHeight / 1.5
					) ||
					(
						elemBottom <= containerHeight && containerScrollTop <= 0 // 50
					)
				);

			};

			/**
			 * Callback for when list view page is brought into view
			 */
			_fn.doPageSetup = function ()
			{

				var $page = $ ( this );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.doPageSetup is running. Element: ", $page ] );
				}

				// after message setup
				_fn.messagesAfterSetup ( $page );

			};

			/**
			 * Hootsuite specific setup for MainStreamView
			 */
			_fn.hootsuiteSetup = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.hootsuiteSetup is running." ] );
				}

			};

			// empty the map element, ready to re-use
			/**
			 * Empty the map element, ready to re-use
			 * @returns {jQuery} Map element
			 */
			_self.clearMapElement = function ()
			{

				return _internal.map_container.empty ();

			};

			/**
			 * Generic map setup, will point to map_customAPI()
			 * @param {Object} info Data object
			 * @returns {*} Map handle, output from chosen function
			 */
			_self.map = function ( info )
			{

				if ( !info || !info.api ) { // default api

					info.api = _settings.omap.api;

				}
				else {

					_settings.omap.api = info.api;

				}

				var mapHandle = _fn[ "map_" + info.api ] ( info );

				if ( !_internal.doNotSetupMap ) {

					// existing map data
					if ( app.session.storage.currentDiscover.mapData ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.map: Existing map data detected. Reinitialising stream ... Data: ", app.session.storage.currentDiscover.mapData ] );
						}

						_internal.messages_data = app.session.storage.currentDiscover.mapData.messages_data;

						_internal.cursors = app.session.storage.currentDiscover.mapData.cursors;

						_internal.total_message_count = _internal.messages_data.messages.length;
						_internal.messages_temp       = _internal.messages_data.messages.slice ( 0 );

						// TODO: is this if statement really necessary?
						//if ( app.session.storage.currentDiscover.mapData.last_checks !== undefined ) {
						_internal.last_checks = app.session.storage.currentDiscover.mapData.last_checks;
						//}

						reInitialiseStream ();

					}
					else {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.map: No existing map data detected. Creating links ..." ] );
						}

						// link up map data
						app.session.storage.currentDiscover.mapData = {
							messages_data: _internal.messages_data,
							last_checks  : _internal.last_checks,
							cursors      : _internal.cursors
						};

					}

				}
				else {

					delete _internal.doNotSetupMap;

				}

				return mapHandle;

			};

			/**
			 * Generic add marker, will point to map_customAPI_addMarker()
			 * @param {Object} info Data object
			 * @returns {*} Output from chosen function
			 */
			_fn.map_addMarker = function ( info )
			{

				return _fn[ "map_" + _settings.omap.api + "_addMarker" ] ( info );

			};

			/**
			 * Generic add markers to managers, will point to map_customAPI_addMarkersToManagers()
			 * @param {Object=} info Data object
			 * @returns {*} Output from chosen function
			 */
			_fn.map_addMarkersToManagers = function ( info )
			{

				return _fn[ "map_" + _settings.omap.api + "_addMarkersToManagers" ] ( info );

			};

			/**
			 * Generic clear marker, will point to map_customAPI_clearMarker()
			 * @param {Object} info Data object
			 * @returns {*} Output from chosen function
			 */
			_fn.map_clearMarker = function ( info )
			{

				return _fn[ "map_" + _settings.omap.api + "_clearMarker" ] ( info );

			};

			/**
			 * Generic highlights and unhighlights marker and message, will point to map_customAPI_highlightMarkerMessage()
			 * @param {google.maps.Marker=} marker Map marker. If null, it will unhighlight marker and message
			 * @param {jQuery=} $message List $ message
			 */
			_self.map_highlightMarkerMessage = function ( marker, $message )
			{

				return _fn[ "map_" + _settings.omap.api + "_highlightMarkerMessage" ] ( marker, $message );

			};

			/* GOOGLE MAPS SPECIFIC METHODS */

			/**
			 * Google maps init method
			 * @param {Object} options Data object to override defaults, as necessary
			 * @returns {google.maps.Map,null} Map handle if created, null if error occurred
			 */
			_fn.map_gmaps = function ( options )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps is running. Options: ", options ] );
				}

				var g = _settings.omap.gmaps,
				    mti;

				if ( typeof options === "object" ) {

					_ ( g ).extend ( options );

				}

				try {

					// create centre of GMaps view
					g.options.center = new google.maps.LatLng ( g.options.map_centre.lat, g.options.map_centre.lng );

					// initialise map
					_internal.map_handle = new google.maps.Map ( _internal.map_container[ 0 ], g.options );

					// stream only setup
					if ( !_internal.doNotSetupMap ) {

						// create the map bounds
						_internal.map_bounds = new google.maps.LatLngBounds ();

						// map listeners
						google.maps.event.addListener ( _internal.map_handle, "idle", _fn.map_gmaps_onIdle );
						google.maps.event.addListener ( _internal.map_handle, "click", _fn.map_gmaps_onClick );

						// initialise MC
						_internal.map_mc_handle = new MarkerClusterer ( _internal.map_handle, [], g.mc_options );

						// MC listeners
						//google.maps.event.addListener ( _internal.map_mc_handle, "click", _fn.map_gmaps_MCClick );

						// initialise OMS
						_internal.map_oms_handle = new OverlappingMarkerSpiderfier ( _internal.map_handle, g.oms_options );

						mti = google.maps.MapTypeId;

						_internal.map_oms_handle.legColors.usual[ mti.HYBRID ]          = g.oms_options.legColours.usual.HYBRID;
						_internal.map_oms_handle.legColors.usual[ mti.SATELLITE ]       = g.oms_options.legColours.usual.SATELLITE;
						_internal.map_oms_handle.legColors.usual[ mti.TERRAIN ]         = g.oms_options.legColours.usual.TERRAIN;
						_internal.map_oms_handle.legColors.usual[ mti.ROADMAP ]         = g.oms_options.legColours.usual.ROADMAP;
						_internal.map_oms_handle.legColors.highlighted[ mti.HYBRID ]    = g.oms_options.legColours.usual.HYBRID;
						_internal.map_oms_handle.legColors.highlighted[ mti.SATELLITE ] = g.oms_options.legColours.usual.SATELLITE;
						_internal.map_oms_handle.legColors.highlighted[ mti.TERRAIN ]   = g.oms_options.legColours.usual.TERRAIN;
						_internal.map_oms_handle.legColors.highlighted[ mti.ROADMAP ]   = g.oms_options.legColours.usual.ROADMAP;

						// global OMS marker click listener
						_internal.map_oms_handle.addListener ( "click", _fn.map_gmaps_markerClick );

						// global OMS marker spiderfy listener
						_internal.map_oms_handle.addListener ( "spiderfy", _fn.map_gmaps_markerSpiderfied );

						// global OMS marker unspiderfy listener
						_internal.map_oms_handle.addListener ( "unspiderfy", _fn.map_gmaps_markerUnSpiderfied );

					}

				}
				catch ( e ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.map_gmaps: Error: ", e ] );
					}

				}

				// return handle for further manipulation
				return _internal.map_handle;

			};

			/**
			 * To be run when the map has triggered its idle event.
			 */
			//_fn.map_gmaps_MCClick = function ( cluster )
			//{
			//
			//	if ( DEBUG ) {
			//		app.logThis ( [ "oPheme.map_gmaps_MCClick is running. Cluster: ", cluster, "Cluster Markers: ", cluster.getMarkers () ] );
			//	}
			//
			//	//_fn.map_gmaps_onIdle();
			//
			//};

			/**
			 * To be run when the map has triggered its click event.
			 */
			_fn.map_gmaps_onClick = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_onClick is running." ] );
				}

				// unhighlight everything when map is clicked
				_fn.map_gmaps_highlightMarkerMessage ( null );

			};

			/**
			 * To be run when the map has triggered its idle event.
			 */
			_fn.map_gmaps_onIdle = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_onIdle is running." ] );
				}

				// run spiderfier specific method to calculate spider pools
				setTimeout ( _fn.map_gmaps_OMSVisualiseSpiderPools, 0 );

			};

			/**
			 * Go through all markers which are to be spiderfied on click and draw a circle at the centre of each pool
			 */
			_fn.map_gmaps_OMSVisualiseSpiderPools = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_OMSVisualiseSpiderPools is running." ] );
				}

				// get all markers in view that will be spiderfied on click
				var markersOMS = _internal.map_oms_handle.markersNearAnyOtherMarker (),
				    spiderfyingPositions, mToPx, pxDistanceToCheck, currentZoom, generateBounds;

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_OMSVisualiseSpiderPools: Markers to be spiderfied: ", markersOMS ] );
				}

				if ( markersOMS.length ) {

					spiderfyingPositions = [];
					pxDistanceToCheck    = _settings.omap.gmaps.oms_options.nearbyDistance;
					currentZoom          = _internal.map_handle.getZoom ();
					mToPx                = _settings.omap.gmaps.zoomLevels_MperPx[ currentZoom ];
					generateBounds       = function ( markers )
					{

						// stop if the array is empty
						if ( !(
								markers instanceof Array && markers.length
							) ) {

							return;

						}

						var nextMarkers    = [],
						    gmBounds       = new google.maps.LatLngBounds (),
						    marker         = markers.pop (),
						    markerPosition = marker.getPosition (),
						    currentMarkers = [ marker ],
						    markersLength_ = markers.length,
						    compareMarker, i, pxDistance, mDistance, markerCount;

						// add current marker to bounds
						gmBounds.extend ( markerPosition );
						markerCount        = 1;

						for ( i = 0; i < markersLength_; i++ ) {

							compareMarker = markers[ i ];

							// get meter distance
							mDistance = google.maps.geometry.spherical.computeDistanceBetween ( markerPosition, compareMarker.getPosition () );

							// calculate map pixel distance
							pxDistance = mDistance / mToPx;

							// next marker in line is close to current marker
							if ( pxDistance < pxDistanceToCheck ) {

								// add next marker in line to
								gmBounds.extend ( compareMarker.getPosition () );

								// keep track of marker count for current bounds
								markerCount++;

								// keep track of marker reference
								currentMarkers.push ( compareMarker );

							}
							else {

								// add marker to next set to be checked
								nextMarkers.push ( compareMarker );

							}

						}

						// save current bounds
						spiderfyingPositions.push (
							{
								bounds     : gmBounds,
								markerCount: markerCount,
								markers    : currentMarkers
							}
						);

						// recursively process next set of markers
						generateBounds ( nextMarkers );

					};

					// generate the map bounds
					generateBounds ( markersOMS );

					// visualise the bounds centres
					_fn.map_gmaps_OMSSpiderPoolsVisualsSetup ( spiderfyingPositions );

				}
				else {

					// remove all existing pool visuals, if necessary
					_fn.map_gmaps_OMSClearPools ();

				}

			};

			/**
			 * Remove all existing pool visuals
			 */
			_fn.map_gmaps_OMSClearPools = function ()
			{

				var pools = _internal.omsSpiderPools,
				    poolObject;

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_OMSClearPools is running. Existing pools: ", pools ] );
				}

				// remove all existing pool visuals
				while ( pools.length ) {

					poolObject = pools.pop ();

					// remove it from map
					poolObject.poolLabel.setMap ( null );

				}

			};

			/**
			 * Setup map visuals at the centre each of the given map bounds
			 * @param {{bounds: google.maps.LatLngBounds, markerCount: number, markers: google.maps.Marker[]}[]} mapBounds Array of objects containing map bounds and marker counts
			 */
			_fn.map_gmaps_OMSSpiderPoolsVisualsSetup = function ( mapBounds )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_OMSSpiderPoolsVisualsSetup is running. Data: ", mapBounds ] );
				}

				var gmBounds, markers, poolRef, i,
				    poolCentre, poolLabel, poolTempCentre,
				    pools  = _internal.omsSpiderPools,
				    zIndex = 50000,
				    markerCount, poolObject;

				// remove all existing pool visuals
				_fn.map_gmaps_OMSClearPools ();

				while ( mapBounds.length ) {

					poolObject = mapBounds.pop ();

					// get bounds and marker count
					gmBounds    = poolObject.bounds;
					markerCount = poolObject.markerCount;
					markers     = poolObject.markers;

					// get the pool centre
					poolCentre = gmBounds.getCenter ();

					// create the text
					poolLabel = new MapLabel (
						{
							text        : markerCount,
							map         : _internal.map_handle,
							position    : poolCentre,
							zIndex      : zIndex,
							strokeColor : "black",
							strokeWeight: 1.35,
							fontSize    : 14,
							fontColor   : "#3498db",
							//fontColor   : "#f1c40f",
							fontFamily  : "Helvetica Neue",
							align       : "center",
							minZoom     : 15,
							maxZoom     : 23
						}
					);

					// shift the label position slightly
					poolTempCentre = poolLabel.getProjection ().fromLatLngToContainerPixel ( poolCentre );
					poolTempCentre.x += 2;
					poolTempCentre.y -= 18;
					poolCentre     = poolLabel.getProjection ().fromContainerPixelToLatLng ( poolTempCentre );

					poolLabel.set ( "position", poolCentre );

					// save references
					poolRef = pools.push (
							{
								poolLabel: poolLabel
							}
						) - 1;

					for ( i = 0; i < markerCount; i++ ) {

						// save pool reference in marker
						markers[ i ].spiderPool = pools[ poolRef ];

					}

				}

			};

			/**
			 * Callback for Marker clicks
			 * @param {google.maps.Marker} marker Clicked Marker
			 */
			_fn.map_gmaps_markerClick = function ( marker/*, event*/ )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_markerClick: Marker has been clicked. Info: ", marker ] );
				}

				var $listItemElement   = marker._info.listViewItem.$el,
				    scrollPosition     = app.maps.stream.listView.height - marker._info.listViewItemTop,
				    $messagesContainer = $ ( _settings.messagesContainerQueryString );

				// scroll to message
				app.scrollElementToTopOf ( $messagesContainer, scrollPosition, null, 1, true );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_markerClick: ListView.find $listItem: ", $listItemElement ] );
				}

				// unhighlight any other messages and markers, if any
				_fn.map_gmaps_highlightMarkerMessage ( null );

				// highlight current message and marker
				_fn.map_gmaps_highlightMarkerMessage ( marker, $listItemElement );

			};

			/**
			 * Generic Highlights and unhighlights marker and message, points to API specific method
			 * @param {google.maps.Marker=} marker Map marker. If null, it will unhighlight marker and message
			 * @param {jQuery=} $message List $ message
			 * @returns {*} Any output from method
			 */
			_fn.map_highlightMarkerMessage = function ( marker, $message )
			{

				return _fn[ "map_" + _settings.omap.api + "_highlightMarkerMessage" ] ( marker, $message );

			};

			/**
			 * Highlights and unhighlights marker and message
			 * @param {google.maps.Marker=} marker Map marker. If null, it will unhighlight marker and message
			 * @param {jQuery=} $message List $ message
			 */
			_fn.map_gmaps_highlightMarkerMessage = function ( marker, $message )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_highlightMarkerMessage: Marker highlight change is requested. Info: ", marker, $message ] );
				}

				if ( !marker ) {

					if ( !!_internal.highlights.message ) {
						_internal.highlights.message.removeClass ( "selected" );
						_internal.highlights.marker.setIcon ( _internal.highlights.markerIcon );
						_internal.highlights.marker.setZIndex ( _internal.highlights.zIndex );
						_internal.highlights.marker.highlighted = false;
					}

				}
				else {

					// save highlight references
					_internal.highlights.message            = $message;
					_internal.highlights.marker             = marker;
					_internal.highlights.markerIcon         = marker.getIcon ();
					_internal.highlights.zIndex             = marker.getZIndex ();

					// highlight message and marker
					_internal.highlights.message.addClass ( "selected" );
					_internal.highlights.marker.setIcon (
						/**
						 * @type google.maps.Icon
						 */
						{
							//anchor      : new google.maps.Point ( 15, 15 ), // middle middle
							anchor      : new google.maps.Point ( 40, 40 ), // top middle
							path        : _settings.iconShapes.highlight,
							fillColor   : _settings.iconColours.highlight,
							fillOpacity : 1,
							scale       : 0.3,
							strokeColor : "black",
							strokeWeight: 1.25
						}
					);
					_internal.highlights.marker.setZIndex ( 15000 );
					_internal.highlights.marker.highlighted = true;

				}

			};

			/**
			 * Callback for when markers get spiderfied
			 * @param {Array} markers Markers which have been spiderfied
			 */
			_fn.map_gmaps_markerSpiderfied = function ( markers )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_markerSpiderfied: Markers have been spiderfied. Markers In: ", markers ] );
				}

				// hide the pool label
				markers[ 0 ].spiderPool.poolLabel.setMap ( null );

			};

			/**
			 * Callback for when markers get unspiderfied
			 * @param {Array} markers Markers which have been unspiderfied
			 */
			_fn.map_gmaps_markerUnSpiderfied = function ( markers )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_markerSpiderfied: Markers have been un-spiderfied. Markers Out: ", markers ] );
				}

				var marker, i;

				// enforce highlighted marker z-index
				for ( i in markers ) {

					if ( markers.hasOwnProperty ( i ) ) {

						marker = markers[ i ];

						if ( marker.highlighted ) {

							marker.setZIndex ( 15000 );

						}

					}

				}

				// show the pool label
				//markers[ 0 ].spiderPool.poolBg.setMap ( _internal.map_handle );
				markers[ 0 ].spiderPool.poolLabel.setMap ( _internal.map_handle );

			};

			/**
			 * Checks for the existance of a Marker at the given coordinates
			 * @param {google.maps.LatLng} coords Coordinates to check
			 * @returns {int,null} Marker array position if found, null otherwise
			 */
			_fn.map_gmaps_markerGetSameCoords = function ( coords )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_markerGetSameCoords is running. Coords: ", coords ] );
				}

				var markers = _internal.map_markers,
				    i, len;

				if ( markers.length === 0 ) {

					return null;

				}

				len = markers.length;

				for ( i = 0; i < len; i++ ) {

					if ( coords.equals ( markers[ i ].getPosition () ) ) {

						return parseInt ( i, 10 );

					}

				}

				return null;

			};

			/**
			 * Create marker icon
			 * @param {{ created: string, smType: string, sentiment: string }} info Data object
			 * @param {int=} _interval Active interval ID
			 * @returns {google.maps.Icon} {{ anchor: google.maps.Point, path: string, fillColor: string, fillOpacity: number, scale: number, strokeColor: string, strokeWeight: number }} Marker icon
			 */
			_fn.map_gmaps_getMarkerIcon = function ( info, _interval )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_getMarkerIcon is running. Data: ", info ] );
				}

				var opacity = _settings.ageThresholdOpacity.old,
				    ageMinutes, threshold, icon,
				    isOld   = true;

				// calculate marker age
				ageMinutes = moment ().diff ( info.created, "minutes" );

				// get the correct opacity setting
				for ( threshold in _settings.ageThresholdOpacity ) {

					if ( _settings.ageThresholdOpacity.hasOwnProperty ( threshold ) && ageMinutes < parseInt ( threshold, 10 ) ) {

						opacity = _settings.ageThresholdOpacity[ threshold ];

						isOld = false;

						break;

					}

				}

				icon = {
					//anchor      : new google.maps.Point ( 15, 30 ), // bottom middle
					anchor      : new google.maps.Point ( 15, 15 ), // middle middle
					path        : _settings.iconShapes[ info.smType ],
					fillColor   : _settings.iconColours[ info.smType ][ info.sentiment ],
					fillOpacity : opacity,
					scale       : 0.8,
					strokeColor : "white",
					strokeWeight: 1.35
				};

				if ( isOld ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.map_gmaps_getMarkerIcon: Stopping Marker Icon interval, marker is as old as it's gonna get ..." ] );
					}

					clearInterval ( _interval );

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_getMarkerIcon: Finished building Icon data: ", icon ] );
				}

				return icon;

			};

			/**
			 * Creates marker and adds it to internal tracking lists
			 * @param {{ showMarker: boolean, lat: number, lng: number, created: string, smType: string, sentiment: string, user: string, dom_id: string, listViewItem: infinity.ListItem, listViewItemTop: int }} info Data object
			 * @returns {google.maps.marker} Created Marker or old marker if it already exists
			 */
			_fn.map_gmaps_addMarker = function ( info )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_addMarker is running. Info: ", info ] );
				}

				var g = _settings.omap.gmaps,
				    marker, position, existingMarkerPosition, iconInfo, _interval, markerDefaults;

				// marker limit reached
				if ( _internal.map_markers.length === g.noOfMarkers && !_settings.stopOnMessageLimit ) {

					// remove first marker
					_fn.map_gmaps_clearMarker ();

				}

				// get position of marker
				position               = new google.maps.LatLng ( info.lat, info.lng );
				existingMarkerPosition = _fn.map_gmaps_markerGetSameCoords ( position );
				iconInfo               = {
					"created"  : info.created,
					"smType"   : info.smType,
					"sentiment": info.sentiment
				};

				_interval = setInterval (
					function ()
					{ // update icon every minute

						marker.setIcon ( _fn.map_gmaps_getMarkerIcon ( iconInfo, _interval ) );

					}, 60 * 1000
				);

				markerDefaults = {
					map      : _internal.map_handle,
					animation: google.maps.Animation.DROP,
					position : position,
					title    : info.user,
					icon     : _fn.map_gmaps_getMarkerIcon ( iconInfo, _interval )
				};

				marker = new google.maps.Marker (
					_.extend (
						{}, markerDefaults, {
							zIndex: _settings.omap.uniqueMarkers === true && existingMarkerPosition !== null ?
							        -5000 : _internal.map_markers.length + 1000
						}
					)
				);

				// hide marker if requested
				if ( !info.showMarker ) {

					marker.setVisible ( false );

					delete info.showMarker;

				}

				// store marker information with marker
				marker._info = info;

				// add marker to temporary list
				_internal.map_temp_markers.push ( marker );

				if ( _settings.omap.uniqueMarkers === true && existingMarkerPosition !== null ) {

					// reference to existing marker
					return _internal.map_markers[ existingMarkerPosition ];

				}

				// permanently store marker
				_internal.map_markers.push ( marker );

				// used for external purposes
				return marker;

			};

			/**
			 * Google maps clear marker
			 * @param {int=} id Marker array tracker ID, clears away oldest marker if not given
			 * @returns {boolean,google.maps.marker} False if there are no markers or if marker id is out of bounds, the marker if removed
			 */
			_fn.map_gmaps_clearMarker = function ( id )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_clearMarker is running. ID: ", id ] );
				}

				var markers = _internal.map_markers,
				    // marker handle
				    marker;

				// pre check for user mistakes
				if ( markers.length === 0 || (
						id && id >= markers.length
					) ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.map_gmaps_clearMarker: Marker id is greater than total markers. id=" + id + ", total=" + markers.length ] );
					}

					return false;

				}

				if ( typeof id === "number" ) { // id given

					// get marker handle
					marker = markers[ id ];

					// remove marker from tracking array
					markers.splice ( id, 1 );

				}
				else { // no id given

					// get marker handle and remove it from tracking array
					marker = markers.shift ();

				}

				// remove marker from MC
				_internal.map_mc_handle.removeMarker ( marker, false );

				// remove marker from OMS
				_internal.map_oms_handle.removeMarker ( marker );

				// remove marker
				marker.setMap ( null );

				return marker;

			};

			/**
			 * Set up MC and OMS
			 */
			_fn.map_gmaps_addMarkersToManagers = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.map_gmaps_addMarkersToManagers is running." ] );
				}

				// only fiddle with the map if there are any markers there
				if ( !_internal.map_temp_markers.length ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.map_gmaps_addMarkersToManagers: No markers to process, skipping ..." ] );
					}

					return false;

				}

				// add markers to map Spiderfier
				try {

					// add markers to marker clusterer
					_internal.map_mc_handle.addMarkers ( _internal.map_temp_markers );

				}
				catch ( e ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.map_gmaps_addMarkersToManagers: Marker Clusterer Setup error: ", e ] );
					}

				}

				var reFitMap = false,
				    i, len, m;

				// add markers to spiderfier
				len = _internal.map_temp_markers.length;
				for ( i = 0; i < len; i++ ) {

					m = _internal.map_temp_markers[ i ];

					try {

						// add marker to spiderfier
						_internal.map_oms_handle.addMarker ( m );

						// also use it to extend the map bounds if need be
						if ( !_internal.map_bounds.contains ( m.getPosition () ) ) {

							_internal.map_bounds.extend ( m.getPosition () );

							reFitMap = true;

						}

					}
					catch ( e ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.map_gmaps_addMarkersToManagers: Spiderfier Setup error: ", e ] );
						}

					}

				}

				// clear temporary marker container
				_internal.map_temp_markers = [];

				// reset all spiderfied markers
				_internal.map_oms_handle.unspiderfy ();

				// first time markers are added to map since page load
				if ( _internal.firstTime || reFitMap ) {

					// reposition map to include all markers in view
					_internal.map_mc_handle.fitMapToMarkers ();
					_internal.map_mc_handle.repaint ();

					// first time just passed
					_internal.firstTime = false;

				}

				return true;

			};

			/**
			 * Goes through unprocessed messages and prepares them for viewing
			 * @returns {Boolean} False if maximum items achieved or there were no messages to process, true otherwise
			 */
			_fn.parseMessages = function ( oldMessages )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.parseMessages is running." ] );
				}

				//if (_fn.checkMessageLimit()) {
				//
				//	if ( DEBUG ) {
				//		app.logThis ( [ "oPheme.parseMessages: Skipping request due to number of messages reached." ] );
				//	}
				//
				//	return false;
				//
				//}

				var messages, msgCpl, msgData, messagesCount, messagesCountNew, interactionsCountNew, newArrayPositions;

				if ( oldMessages === true ) {

					messages = _internal.messages_old_temp;

				}
				else {

					messages = _internal.messages_temp;

				}

				if ( messages instanceof Array && messages.length ) {

					messagesCount        = 0;
					messagesCountNew     = 0;
					interactionsCountNew = 0;
					newArrayPositions    = [];

					do {

						try {

							/**
							 * Processed message info
							 * @type {{id: number, isNew: boolean, interactionsCountNew: number}}
							 */
							msgData = _fn.processRawMessage ( messages.shift (), false, null, null, oldMessages );

							if ( DEBUG ) {
								app.logThis ( [ "oPheme.parseMessages: Data from processRawMessage: ", msgData ] );
							}

							// link to compiled message data
							msgCpl = _internal.compiledMessages[ msgData.id ];

							// count new messages
							if ( msgData.isNew ) {

								messagesCountNew++;

							}

							// count new interactions
							interactionsCountNew += msgData.interactionsCountNew;

							// keep track of the new messages within the array
							newArrayPositions.push ( msgData.id );

							// create marker
							_fn.map_addMarker (
								{
									lat            : msgCpl.messageRaw.coords.latitude,
									lng            : msgCpl.messageRaw.coords.longitude,
									sentiment      : msgCpl.messageRaw.sentiment,
									smType         : msgCpl.messageRaw.social_media_type,
									created        : msgCpl.messageRaw.timestamp,
									user           : msgCpl.messageRaw.user.screen_name,
									dom_id         : msgCpl.messageRaw.dom_id,
									// the associated listView message, to be set after message is added to listView
									listViewItem   : null,
									// the message top position within listView
									listViewItemTop: 0,
									showMarker     : _internal.currentListView === "messages" || (
										!!msgData.interactionsCountNew && _internal.currentListView === "interactions"
									)
								}
							);

						}
						catch ( e ) {

							if ( DEBUG ) {
								app.logThis ( [ "oPheme.parseMessages: Message parse error: ", e ] );
							}

						}

						// stop when current message count has reached max queue items | there are no more messages to process now | total max number of messages reached
					} while ( ++messagesCount < _settings.max_queue_items && messages.length && _internal.compiledMessages.length < _settings.omap.noOfMarkers );

					// add markers to map managers
					_fn.map_addMarkersToManagers ();

					if ( !oldMessages ) {

						// add the new messages to list
						_self.doAddNewMessagesToList ( newArrayPositions );

						// after message setup
						_fn.messagesAfterSetup ();

					}

					// update counts
					_fn.updateVisualCounts ( messagesCountNew, interactionsCountNew );

					// stop queue if there are no more messages to process
					if ( _internal.total_message_count === _internal.compiledMessages.length ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.parseMessages: No more messages to process, all done." ] );
						}

						_self.stopQueue ();

					}

					// successfully processed some messages
					return true;

				}

				if ( DEBUG ) {

					app.logThis ( [ "oPheme.parseMessages: No markers to process." ] );

					_self.stopQueue ();

				}

				// no messages were processed
				return false;

			};

			/**
			 * Process a raw message and (re)compile it
			 * @param {Object} message Raw message
			 * @param {boolean?} update If TRUE it will update an existing message
			 * @param {number?} id If "update" is TRUE, then give the array position of the element to update
			 * @param {object?} overwriteData Pass an object with properties used to overwrite message data
			 * @param {boolean?} oldMessage Pass TRUE to change behaviour for an old message
			 * @returns { { id: number, isNew: boolean, interactionsCountNew: number } } Some info on the processed message
			 */
			_fn.processRawMessage = function ( message, update, id, overwriteData, oldMessage )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.processRawMessage is running. Data: ", message, update, id, overwriteData, oldMessage ] );
				}

				var i, msg, msgData, msgCpl, images, img, videos, vid,
				    interactionsCountNew  = 0,
				    // TODO: change below var to false for Live, it makes old messages appear as new
				    reverseNewStatus      = false,
				    lastLoginUnix         = _internal.last_checks.job === "1970-01-01 00:00:00" ? app.session.storage.user.get ( "last_login_unix" ) : moment.utc ( _internal.last_checks.job ).unix () - (
					    _settings.timeout / 1000
				    ), // substract the timeout because it's updated just before messages are parsed
				    /**
				     * Replaces any http set of characters with https
				     * @param {string} string Character string to search through
				     * @returns {string} Input string with any http replaced with https
				     */
				    replaceHTTPWithHTTPS  = function ( string )
				    {
					    return string.replace ( /^http:\/\//i, "https://" );
				    },
				    /**
				     * Builds a URL for a certain username and social media network
				     * @param {string} smType Social media type - instagram/twitter
				     * @param {string} screenName User screen name
				     * @returns {string} URL string
				     */
				    buildProfileURL       = function ( smType, screenName )
				    {
					    return "https://www." + smType + ".com/" + screenName;
				    },
				    /**
				     * Add data-bypass to anchor links within the string
				     * @param {string} string Input string
				     * @returns {string} String with data-bypass added
				     */
				    addDataBypassToAnchor = function ( string )
				    {
					    return string.replace ( "a href", "a data-bypass href" );
				    };
				//externalDateFormat    = "ddd MMM D H:mm:ss z YYYY", // Thu Aug 14 0:33:46 BST 2014

				if ( !message.sentiment ) {
					message.sentiment = "none";
				}

				if ( !message.user.klout ) {
					_ ( message.user ).extend (
						{
							klout: {
								score: 0
							}
						}
					);
				}

				if ( !message.text.length ) {
					message.text = "&nbsp;";
				}

				if ( message.interactions ) {

					for ( i in message.interactions.messages ) {

						if ( message.interactions.messages.hasOwnProperty ( i ) ) {

							/**
							 * @type { { social_media_type: string, timestamp_server: string } }
							 */
							msg = message.interactions.messages[ i ];

							//if ( (
							//	     created_at = moment ( msg.timestamp, externalDateFormat ).format ()
							//     ) === "Invalid date" ) {
							//
							//	created_at = msg.timestamp;
							//
							//}

							_ ( msg ).extend (
								{
									//created_at: created_at,
									text       : addDataBypassToAnchor ( _self.replaceURLWithHTMLLinks ( msg.text ) ),
									timestamp  : moment ( moment.utc ( msg.timestamp ).toDate () ).format ( "YYYY-MM-DD HH:mm:ss" ),
									klout_score: Math.round ( msg.klout_score )
								}
							);

							if ( moment.utc ( msg.timestamp_server.date ).unix () >= lastLoginUnix || reverseNewStatus ) {

								msg.isNew = true;

								interactionsCountNew++;

							}
							else {

								msg.isNew = false;

							}

							if ( !msg.text.length ) {
								msg.text = "<span class='missing-text'>Missing text</span>";
							}

							_ ( msg.user ).extend (
								{
									profile_url      : buildProfileURL ( msg.social_media_type, msg.user.screen_name ),
									profile_image_url: replaceHTTPWithHTTPS ( msg.user.profile_image_url )
								}
							);

							if ( !msg.user.klout ) {
								_ ( msg.user ).extend (
									{
										klout: {
											score: 0
										}
									}
								);
							}

						}

					}

				}

				//if ( (
				//	     created_at = moment ( message.timestamp, externalDateFormat ).format ()
				//     ) === "Invalid date" ) {
				//
				//	created_at = message.timestamp;
				//
				//}

				if ( message.images && message.images instanceof Array && message.images.length ) {

					images = [];

					for ( i in message.images ) {

						if ( message.images.hasOwnProperty ( i ) ) {

							img = message.images[ i ];

							_ ( img ).extend (
								{
									url: replaceHTTPWithHTTPS ( img.url )
								}
							);

							images.push ( img );

						}

					}

					_ ( message ).extend (
						{
							images: images
						}
					);

				}

				if ( message.videos && message.videos instanceof Array && message.videos.length ) {

					videos = [];

					for ( i in message.videos ) {

						if ( message.videos.hasOwnProperty ( i ) ) {

							vid = message.videos[ i ];

							_ ( vid ).extend (
								{
									url: replaceHTTPWithHTTPS ( vid.url )
								}
							);

							videos.push ( vid );

						}

					}

					_ ( message ).extend (
						{
							videos: videos
						}
					);

				}

				msgData = {
					smHandles: _settings.smHandles,
					message  : _.extend (
						{}, message, {
							user       : _.extend (
								{}, message.user, {
									profile_url      : buildProfileURL ( message.social_media_type, message.user.screen_name ),
									profile_image_url: replaceHTTPWithHTTPS ( message.user.profile_image_url )
								}
							),
							//created_at: created_at,
							text       : addDataBypassToAnchor ( _self.replaceURLWithHTMLLinks ( message.text ) ),
							dom_id     : "message_" + message.backend_message_id,
							timestamp  : moment ( moment.utc ( message.timestamp ).toDate () ).format ( "YYYY-MM-DD HH:mm:ss" ),
							klout_score: Math.round ( message.klout_score )
						}, overwriteData == null ? {} : overwriteData
					)
				};

				msgData.message.isNew = !!(
					moment.utc ( msgData.message.timestamp_server.date ).unix () >= lastLoginUnix || reverseNewStatus
				);

				msgCpl = {
					message             : $ ( _internal.compiledTpl ( msgData ) ),
					messageRaw          : msgData.message,
					hasInteractions     : !!message.interactions,
					interactionsCountNew: message.interactions ? message.interactions.messages.length : 0
				};

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.processRawMessage: Final Message Data: ", msgData, "Compiled Message: ", msgCpl ] );
				}

				if ( !!update && typeof id === "number" && id > -1 && _internal.compiledMessages[ id ] !== undefined ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.processRawMessage: Updating existing message ..." ] );
					}

					_internal.compiledMessages[ id ] = msgCpl;

				}
				else {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.processRawMessage: Adding new message ..." ] );
					}

					if ( oldMessage ) {

						id = 0;

						_internal.compiledMessages.unshift ( msgCpl );

					}
					else {

						id = _internal.compiledMessages.push ( msgCpl ) - 1;

					}

				}

				return {
					id                  : id,
					isNew               : msgData.message.isNew,
					interactionsCountNew: interactionsCountNew
				};

			};

			/**
			 * Checks whether marker limit has been reached and stops job if so
			 * @returns {Boolean} TRUE if limit reached, FALSE otherwise
			 */
			_fn.checkMessageLimit = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.checkMessageLimit is running." ] );
				}

				if ( _internal.compiledMessages.length >= _settings.omap.noOfMarkers ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.checkMessageLimit: Marker limit reached, stopping job ..." ] );
					}

					_self.stopJob ();

					return true;

				}

				return false;

			};

			/**
			 * Update the visual elements relating to message and interaction count
			 * @param {number} messagesCount Positive or negative to change existing value by
			 * @param {number} interactionsCount Positive or negative to change existing value by
			 * @param {boolean?} replace If TRUE given, messagesCount/interactionsCount must be positive, otherwise defaults to 0
			 */
			_fn.updateVisualCounts = function ( messagesCount, interactionsCount, replace )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.updateVisualCounts is running. Data: ", messagesCount, interactionsCount, replace ] );
				}

				if ( typeof messagesCount === "number" ) { _fn.updateVisualCountsFor ( $ ( "a[href='#messages'] span" ), messagesCount, replace ); }
				if ( typeof interactionsCount === "number" ) { _fn.updateVisualCountsFor ( $ ( "a[href='#interactions'] span" ), interactionsCount, replace ); }

			};

			/**
			 * Changes the inner html number value of a jquery element
			 * @param {jQuery} $msgCountContainer jquery container
			 * @param {number} value positive or negative value
			 * @param {boolean} replace if true, value must be positive, otherwise defaults to 0
			 */
			_fn.updateVisualCountsFor = function ( $msgCountContainer, value, replace )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.updateVisualCountsFor is running. Data: ", $msgCountContainer, value, replace ] );
				}

				// number of new messages handle
				var oldValue, newValue,
				    $parentLi = $msgCountContainer.parents ( "li" );

				if ( !!replace ) {

					if ( value > -1 ) {

						newValue = value;

					}
					else {

						newValue = 0;

					}

				}
				else {

					/*jslint bitwise: true */
					oldValue = parseInt ( $msgCountContainer.html (), 10 ) | 0;

					if ( value < 0 && value > oldValue ) {

						newValue = 0;

					}
					else {

						newValue = oldValue + value;

					}

				}

				if ( newValue < 1 ) {

					newValue = "";

					if ( $parentLi.hasClass ( "newContent" ) ) {

						$parentLi.removeClass ( "newContent" );

					}

				}
				else {

					if ( !$parentLi.hasClass ( "newContent" ) ) {

						$parentLi.addClass ( "newContent" );

					}

				}

				$msgCountContainer.html ( newValue );

			};

			/**
			 * Refresh or update current messages list.
			 * @param {Array} newArrayPositions These message IDs from the compiled messages array will be added to the list
			 */
			_self.doAddNewMessagesToList = function ( newArrayPositions )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.doAddNewMessagesToList is running. Data: ", newArrayPositions ] );
				}

				if ( newArrayPositions instanceof Array ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.doAddNewMessagesToList: Processing new message positions: ", newArrayPositions, "All messages: ", _internal.compiledMessages ] );
					}

					//var $msgWrapper;

					// first ever batch
					if ( _internal.compiledMessages.length === newArrayPositions.length ) {

						//$msgWrapper = $ ( "#stream-container" );

						// remove the no messages block, we now have stuff
						hideMessagesBlockUI ();

					}
					//else {

					//$msgWrapper = $ ( _settings.messagesContainerQueryString + "Wrapper" );

					//}

					// add overlay
					//$msgWrapper.block (
					//	_.extend (
					//		{}, app.config.blockUIDefaults, {
					//			onBlock: function ()
					//			{
					//
					//				if ( DEBUG ) {
					//					app.logThis ( [ "oPheme.doAddNewMessagesToList: Wrapper has been Blocked. Beginning process ..." ] );
					//				}

					var i, len1, index, j, len2, interaction, marker, msg, bindScroll,
					    listHeight         = app.maps.stream.listView.height, // current list height
					    $messagesContainer = $ ( _settings.messagesContainerQueryString ), // handle on messages container
					    scrollPosition     = $messagesContainer.scrollTop (), // current container scroll position
					    isFirstRun         = !!$messagesContainer[ 0 ].firstChild;

					// go through any new messages recently added to the compiled messages array
					len1 = newArrayPositions.length;

					for ( i = 0; i < len1; i++ ) {

						index = newArrayPositions[ i ];

						msg    = _internal.compiledMessages[ index ];
						marker = _internal.map_markers[ index ];

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.doAddNewMessagesToList: Processing message: ", msg ] );
						}

						// if interactions are required and the messages does not have any, skip
						if ( _internal.currentListView === "interactions" && !msg.hasInteractions ) {

							if ( DEBUG ) {
								app.logThis ( [ "oPheme.doAddNewMessagesToList: Interactions requested, this message has none, skipping ..." ] );
							}

							continue;

						}

						bindScroll = false;

						// count new messages
						if ( msg.messageRaw.isNew ) {

							bindScroll = true;

						}

						// count new interactions
						if ( msg.hasInteractions ) {

							len2 = msg.messageRaw.interactions.messages.length;

							for ( j = 0; j < len2; j++ ) {

								interaction = msg.messageRaw.interactions.messages[ j ];

								if ( interaction.isNew ) {

									bindScroll = true;

								}

							}

							if ( _internal.currentListView === "messages" ) {

								msg.message.find ( ".hs_comments" ).hide ();

								msg.message.find ( ".hs_commentsHeader" ).show ();

							}
							// interactions
							else {

								msg.message.find ( ".hs_comments" ).show ();

								msg.message.find ( ".hs_commentsHeader" ).hide ();

							}

						}

						if ( bindScroll ) {

							// add element to scroll watch list
							msg.message.scrolledIntoView ();

						}

						msg.message.data ( "messageIndex", index );

						// fetch message content and add it to the list
						// and save its reference and current top position inside the associated marker
						marker._info.listViewItem    = app.maps.stream.listView.prepend ( msg.message );
						marker._info.listViewItemTop = app.maps.stream.listView.height;

						// whenever the DOM message is clicked
						_fn.messageClickSetup ( marker._info.listViewItem.$el, marker );

					}

					// if previous scrollPosition was greater than 0, then make sure list does not scroll current message out of view
					//if ( scrollPosition > 0 ) {
					if ( !isFirstRun ) {

						// calculate latest scroll position
						scrollPosition = scrollPosition + app.maps.stream.listView.height - listHeight;

					}
					else {

						_internal.newest_older_message_timestamp = moment.utc ( _internal.compiledMessages[ 0 ].messageRaw.timestamp_server.date ).format ( "YYYY-MM-DD HH:mm:ss" );

					}
					
					// trigger the scroll event
					$messagesContainer.scroll ();

					setTimeout (
						function ()
						{

							// scroll to ensure previous message visible in listview stays there
							$messagesContainer.scrollTop ( scrollPosition );

							// remove overlay
							//$msgWrapper.unblock ();

						}, 0
					);

					//			}
					//		}
					//	)
					//);

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.doAddNewMessagesToList has finished." ] );
				}

			};

			/**
			 * Refresh or update current messages list.
			 * @param {String} which Give "messages" or "interactions" to rebuild the list with them
			 * @param {boolean?} force Pass TRUE to force the reload
			 * @param {number?} offsetHeight Adds this number, positive or negative, to the final scroll position
			 * @param {boolean?} disableBlock Pass TRUE to disable the UI block
			 * @param {function?} callback Function to run once finished
			 * @returns {*} Null if which is incorrect or there are no messages to process
			 */
			_self.doRebuildMessagesList = function ( which, force, offsetHeight, disableBlock, callback )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.doRebuildMessagesList is running. Data: ", which ] );
				}

				// only fiddle with the map if there are any markers there
				if ( !_internal.compiledMessages.length ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.doRebuildMessagesList: No messages to process, skipping ..." ] );
					}

					return null;

				}

				// wrong which parameter value
				if ( typeof which !== "string" || _internal.allowedListTypes.indexOf ( which ) === -1 ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.doRebuildMessagesList: WARNING: The value of 'which' parameter is not 'messages' or 'interactions'. Skipping request ..." ] );
					}

					return null;

				}

				// current list already matches the request
				if ( which === _internal.currentListView && !force ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.doRebuildMessagesList: The requested list is already in view, skipping request ..." ] );
					}

					return null;

				}

				// update current list view tracker
				_internal.currentListView = which;

				// UI element to block
				var $msgWrapper = $ ( _settings.messagesContainerQueryString + "Wrapper" ),
				    rebuildFn   = function ()
				    {

					    if ( DEBUG ) {
						    app.logThis ( [ "oPheme.doRebuildMessagesList: View blocked, processing messages ..." ] );
					    }

					    var $messages                     = $ ( _settings.messagesContainerQueryString ),
					        messagesCountNew              = 0,
					        interactionsCountNew          = 0,
					        messagesWithInteractionsCount = 0,
					        marker, bindScroll, previousListScrollPosition,
					        i, len1, msg, j, len2, interaction;

					    // if a force refresh was done, remember current list position and return to it
					    if ( force ) {

						    previousListScrollPosition = $messages.scrollTop ();

					    }
					    // otherwise, proceed as normal
					    else {

						    // get last scroll position
						    previousListScrollPosition = _internal.previousListScrollPosition;

						    // remember current scroll position
						    _internal.previousListScrollPosition = $messages.scrollTop ();

					    }

					    if ( typeof offsetHeight === "number" ) {

						    previousListScrollPosition += offsetHeight;

					    }

					    // re-initialise the list
					    _fn.initialiseListView ();

					    // go through all existing messages
					    len1 = _internal.compiledMessages.length;

					    for ( i = 0; i < len1; i++ ) {

						    msg = _internal.compiledMessages[ i ];

						    if ( msg === undefined ) { continue; }

						    marker = _internal.map_markers[ i ];

						    if ( DEBUG ) {
							    app.logThis ( [ "oPheme.doRebuildMessagesList: Processing message: ", msg, "Marker: ", marker ] );
						    }

						    bindScroll = false;

						    // count new messages
						    if ( msg.messageRaw.isNew ) {

							    messagesCountNew++;

							    bindScroll = true;

						    }

						    // if interactions are required and the messages does not have any, skip
						    if ( _internal.currentListView === "interactions" ) {

							    if ( !msg.hasInteractions ) {

								    if ( DEBUG ) {
									    app.logThis ( [ "oPheme.doRebuildMessagesList: Interactions requested, this message has none, skipping ..." ] );
								    }

								    // hide marker
								    if ( marker.getVisible () ) {

									    marker.setVisible ( false );

								    }

								    continue;

							    }

							    msg.message.find ( ".hs_comments" ).show ();

							    msg.message.find ( ".hs_commentsHeader" ).hide ();

						    }
						    else {

							    if ( msg.hasInteractions ) {

								    msg.message.find ( ".hs_comments" ).hide ();

								    msg.message.find ( ".hs_commentsHeader" ).show ();

							    }

						    }

						    // count new interactions
						    if ( msg.hasInteractions ) {

							    messagesWithInteractionsCount++;

							    len2 = msg.messageRaw.interactions.messages.length;

							    for ( j = 0; j < len2; j++ ) {

								    interaction = msg.messageRaw.interactions.messages[ j ];

								    if ( interaction.isNew ) {

									    interactionsCountNew++;

									    bindScroll = true;

								    }

							    }

						    }

						    if ( bindScroll ) {

							    // add element to scroll watch list
							    msg.message.scrolledIntoView ();

						    }

						    msg.message.data ( "messageIndex", i );

						    // fetch message content and add it to the list
						    // and save its reference and current top position inside the associated marker
						    marker._info.listViewItem    = app.maps.stream.listView.prepend ( msg.message );
						    marker._info.listViewItemTop = app.maps.stream.listView.height;

						    // show marker
						    if ( !marker.getVisible () ) {

							    marker.setVisible ( true );

						    }

						    // whenever the DOM message is clicked
						    _fn.messageClickSetup ( marker._info.listViewItem.$el, marker );

					    }

					    // reposition map to include all markers in view
					    _internal.map_mc_handle.fitMapToMarkers ();

					    // refresh the marker clusterer
					    _internal.map_mc_handle.repaint ();

					    // trigger map refresh
					    google.maps.event.trigger ( _internal.map_handle, "resize" );

					    // trigger the scroll event
					    $messages.scroll ();

					    setTimeout (
						    function ()
						    {

							    // set scroll position
							    $messages.scrollTop ( previousListScrollPosition );

							    // update counts
							    _fn.updateVisualCounts ( messagesCountNew, interactionsCountNew, true );

							    // run messages setup
							    _fn.messagesAfterSetup ();

							    // remove overlay
							    $msgWrapper.unblock ();

							    // if there is a deferred object in place
							    if ( typeof _internal.listViewDeferredObject === "object" ) {

								    // resolve it
								    _internal.listViewDeferredObject.resolve ();

							    }

							    // no interactions at all to display, show no messages message
							    if ( _internal.currentListView === "interactions" && !messagesWithInteractionsCount ) {

								    setTimeout (
									    function ()
									    {
										    showNoMessagesBlockUI ();
									    }, app.config.blockUIDefaults.fadeIn + 5
								    );

							    }

							    if ( typeof callback === "function" ) {

								    callback ();

							    }

						    }, 0
					    );

				    };

				if ( disableBlock ) {

					rebuildFn ();

				}
				else {

					// hide blockUI element, if any
					hideMessagesBlockUI ();

					// add overlay
					$msgWrapper.block (
						_.extend (
							{}, app.config.blockUIDefaults, {
								onBlock: rebuildFn
							}
						)
					);

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.doRebuildMessagesList has finished." ] );
				}

			};

			/**
			 * Set up click event for DOM message
			 * @param {jQuery} $message $ DOM element
			 * @param {google.maps.Marker} marker Map marker
			 */
			_fn.messageClickSetup = function ( $message, marker )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageClickSetup is running. Data: ", $message, marker ] );
				}

				// whenever the DOM message is clicked
				$message.click (
					function ()
					{

						if ( $message.hasClass ( "selected" ) ) {
							return;
						}

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.messageClickSetup: DOM Message has been clicked. Data: ", $message, marker ] );
						}

						// unhighlight any existing messages and markers
						_fn.map_highlightMarkerMessage ( null );

						// highlight it and the marker
						_fn.map_highlightMarkerMessage ( marker, $message );

						// and focus the map on the marker
						_internal.map_handle.setCenter ( marker.getPosition () );
						_internal.map_handle.setZoom ( 16 );

					}
				);

			};

			/**
			 * Message setup to be done after messages have been recently added to DOM
			 * @param {jQuery?} $parent Parent message container, context
			 */
			_fn.messagesAfterSetup = function ( $parent )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messagesAfterSetup is running. Parent: ", $parent ] );
				}

				var $timeElements, $timeElementsQuery,
				    $tooltips, $tooltipQuery,
				    $actions, $action,
				    $avatarLinks, $avatarLink, processAvatarLinkFn,
				    $commentsHeader,
				    $imagesList, processListImage, processListImageFn,
				    $imagesProfile, processProfileImage, processProfileImageFn,
				    parentIsListView = false,
				    i, len;

				// if parent not given, assume messages container
				if ( !(
						$parent instanceof jQuery
					) ) {

					$parent = app.maps.stream.listView;//$ ( _settings.messagesContainerQueryString );
					parentIsListView = true;

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.messagesAfterSetup: No jQuery parent given. Using the list as parent. List: ", $parent ] );
					}

				}

				/**
				 * Actual image processing function. Ensures images fill their container's dimensions, or replaces them with a placeholder if they fail to load
				 */
				processListImage = function ( image, instance )
				{

					if ( !image.isLoaded ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.messagesAfterSetup.imagesLoaded: Broken LIST image detected, replacing with placeholder ... Info: ", instance, image ] );
						}

						image.img.src = "/images/img-placeholder.png";

					}

					var $this = $ ( image.img );

					// Do not process this image on subsequent checks
					$this.addClass ( "hs_isProcessed" );
					$this.next ().addClass ( "hideMeSlow" );

				};

				// function for imagesLoaded plugin
				processListImageFn = function ( instance, image )
				{

					processListImage.call ( this, image, instance );

				};

				/**
				 * Actual image processing function. Replaces profile images with default one if they fail to load
				 */
				processProfileImage = function ( image, instance )
				{

					if ( !image.isLoaded ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.messagesAfterSetup.imagesLoaded: Broken PROFILE image detected, replacing with placeholder ... Info: ", instance, image ] );
						}

						image.img.src = "/images/default-profile.png";

					}

				};

				processProfileImageFn = function ( instance, image )
				{

					processProfileImage.call ( this, image, instance );

				};

				// TODO: this seems to be ignored for Comments
				processAvatarLinkFn = function ( evt )
				{

					var $element   = $ ( evt.target ),
					    screenName = $element.attr ( "title" ),
					    smType     = $element.data ( "socialmedia" ),
						smAvatar = $element.prev().attr("src");

					if (smType === undefined || screenName === undefined) {
						return;
					}

					app.eventPreventDefault ( evt );

					if ($element.attr("class") === "hs_userName") {
						smAvatar = $element.parent().prev().children().first().attr("src");
					}

					if ( smType === "instagram" ) {

						app.showModal(smType.toCamelCase(true) + " - " + screenName, "<div style='text-align: center;'><img src='" + smAvatar + "' title='" + screenName + "\'s Avatar' style='max-width: 100%'></div>", null, null, "no-display", "Close");

						return;

					}

					hsp.showUser ( screenName );

				};

				$timeElements  = $parent.find ( ".hs_postTime:not(.timeagoAttached), .hs_commentPostTime:not(.timeagoAttached)" );
				$tooltips      = $parent.find ( "[data-toggle='tooltip']" );
				$imagesList    = $parent.find ( ".hs_message .hs_mediaGallery:has(img:not(.hs_isProcessed))" );
				$imagesProfile = $parent.find ( ".hs_message .hs_avatar" );
				$actions       = $parent.find ( ".hs_message .actions" );
				$avatarLinks   = $parent.find ( ".hs_message .hs_avatarLink, .hs_message .hs_userName, .hs_message .hs_commentAvatarLink, .hs_message .hs_commentUserName" );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messagesAfterSetup: Loaded element arrays: Time Elements: ", $timeElements, "Tooltips: ", $tooltips, "Images List:", $imagesList, "Images Profile: ", $imagesProfile, "Actions: ", $actions ] );
				}

				// if current view is messages
				if ( _internal.currentListView === "messages" ) {

					// locate all comments headers
					$commentsHeader = $parent.find ( ".hs_commentsHeader" );

				}

				// parent is the listview
				if ( parentIsListView ) {

					// Process time fields

					len = $timeElements.length;

					for ( i = 0; i < len; i++ ) {

						$timeElementsQuery = $timeElements[ i ].$el.find ( ".hs_postTime, .hs_commentPostTime" );

						$timeElementsQuery.timeago ();
						$timeElementsQuery.addClass ( "timeagoAttached" );

					}

					// Process tooltips

					len = $tooltips.length;

					for ( i = 0; i < len; i++ ) {

						$tooltipQuery = $tooltips[ i ].$el.find ( "[data-toggle='tooltip']" );

						$tooltipQuery.tooltip (
							{
								container: "body"
							}
						);

					}

					// Process list images

					len = $imagesList.length;

					for ( i = 0; i < len; i++ ) {

						$imagesList[ i ].$el.imagesLoaded ().progress ( processListImageFn );

					}

					// Process profile images

					len = $imagesProfile.length;

					for ( i = 0; i < len; i++ ) {

						$imagesProfile[ i ].$el.imagesLoaded ().progress ( processProfileImageFn );

					}

					// process list actions

					len = $actions.length;

					for ( i = 0; i < len; i++ ) {

						$action = $actions[ i ].$el;

						// message reply button clicked
						$action.find ( ".fa-reply" ).on ( "click", _fn.messageReply );

						// message follow button clicked
						$action.find ( ".follow:not(.clicked)" ).on ( "click", _fn.messageFollow );

						// message unfollow button clicked
						$action.find ( ".follow.clicked" ).on ( "click", _fn.messageUnFollow );

						// message follow button clicked
						$action.find ( ".fa-retweet:not(.clicked)" ).on ( "click", _fn.messageRetweet );

						// message favourite button clicked
						$action.find ( ".fa-star:not(.clicked)" ).on ( "click", _fn.messageFavourite );

						// message unfavourite button clicked
						$action.find ( ".fa-star.clicked" ).on ( "click", _fn.messageUnFavourite );

					}

					// avatar links

					len = $avatarLinks.length;

					for ( i = 0; i < len; i++ ) {

						$avatarLink = $avatarLinks[ i ].$el;

						$avatarLink.click ( processAvatarLinkFn );

					}

					// if current view is messages
					if ( _internal.currentListView === "messages" ) {

						len = $commentsHeader.length;

						for ( i = 0; i < len; i++ ) {

							// on click, switch over to the message in the interactions view
							$commentsHeader[ i ].$el.on ( "click", _fn.messageSwitchToInteractions );

						}

					}

				}

				else {

					$timeElements.timeago ();
					$timeElements.addClass ( "timeagoAttached" );

					$tooltips.tooltip (
						{
							container: "body"
						}
					);

					$imagesList.imagesLoaded ().progress ( processListImageFn );

					$imagesProfile.imagesLoaded ().progress ( processProfileImageFn );

					// message reply button clicked
					$actions.find ( ".fa-reply" ).on ( "click", _fn.messageReply );

					// message follow button clicked
					$actions.find ( ".follow:not(.clicked)" ).on ( "click", _fn.messageFollow );

					// message unfollow button clicked
					$actions.find ( ".follow.clicked" ).on ( "click", _fn.messageUnFollow );

					// message follow button clicked
					$actions.find ( ".fa-retweet:not(.clicked)" ).on ( "click", _fn.messageRetweet );

					// message favourite button clicked
					$actions.find ( ".fa-star:not(.clicked)" ).on ( "click", _fn.messageFavourite );

					// message unfavourite button clicked
					$actions.find ( ".fa-star.clicked" ).on ( "click", _fn.messageUnFavourite );

					// clicking on avatar links
					$avatarLinks.on ( "click", processAvatarLinkFn );

					// if current view is messages
					if ( _internal.currentListView === "messages" ) {

						// on click, switch over to the message in the interactions view
						$commentsHeader.on ( "click", _fn.messageSwitchToInteractions );

					}

				}

			};

			/**
			 * Called when interactions symbol is clicked on a message with interactions in messages view
			 */
			_fn.messageSwitchToInteractions = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageSwitchToInteractions is running." ] );
				}

				// create new deferred object
				_internal.listViewDeferredObject = new $.Deferred ();

				// once resolved, initiate scroll
				_internal.listViewDeferredObject.done (
					function ()
					{

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.messageSwitchToInteractions: ListView finished rendering, scrolling to highlighted message ... Scrolling to position: ", _internal.highlights.marker._info.listViewItemTop ] );
						}

						// scroll to item position
						$ ( _settings.messagesContainerQueryString ).scrollTop ( _internal.highlights.marker._info.listViewItemTop );

						// remove deferred object
						delete _internal.listViewDeferredObject;

						// unhighlight everything when map is clicked
						//_self.map_highlightMarkerMessage ( null );

					}
				);

				// trigger listView render
				$ ( "a[href='#interactions']" ).trigger ( "click", { switchRequested: true } );

			};

			/**
			 * Get the raw message data object which relates to a certain DOM message
			 * @param {jQuery} $target Element found within a DOM message container
			 * @returns {{message: object, id: number}} The raw message object and its id inside the _internal.messages_data.messages array
			 */
			_fn.getMessageRawFromDomElement = function ( $target )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.getMessageRawFromDomElement is running. Element: ", $target ] );
				}

				var $message    = $target.parents ( ".hs_message" ),
				    id          = $message.data ( "backend-id" ),
				    msg, i, len = _internal.total_message_count;

				for ( i = 0; i < len; i++ ) {

					/**
					 * @type { { backend_message_id: string } }
					 */
					msg = _internal.messages_data.messages[ i ];

					if ( msg === undefined ) { continue; }

					if ( msg.backend_message_id === id ) {

						if ( DEBUG ) {
							app.logThis ( [ "oPheme.getMessageRawFromDomElement: Raw message found: ", msg ] );
						}

						return {
							message: msg,
							id     : i
						};

					}

				}

				return {};

			};

			/**
			 * Get the raw interaction data object which relates to a certain DOM message
			 * @param {jQuery} $target Element found within a DOM message container
			 * @param {object} msg Parent message object for this interaction
			 * @returns {Object} The raw message object
			 */
			_fn.getInteractionRawFromDomElement = function ( $target, msg )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.getInteractionRawFromDomElement is running. Element: ", $target ] );
				}

				if ( msg.interactions ) {

					var id = $target.parents ( ".hs_comment" ).data ( "id" ),
					    interactionMsg;

					msg.interactions.messages.some (
						function ( interaction )
						{

							if ( interaction.message_id === id ) {

								if ( DEBUG ) {
									app.logThis ( [ "oPheme.getInteractionRawFromDomElement: Raw message found: ", msg ] );
								}

								interactionMsg = interaction;

								return true;

							}

							return false;

						}
					);

					if ( interactionMsg ) {

						return interactionMsg;

					}

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.getInteractionRawFromDomElement: No interaction found. Error?" ] );
					}

					return {};

				}

				return {};

			};

			// TODO: this action duplicates initial message
			/**
			 * Action to be taken when message reply button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageReply = function ( evt )
			{

				app.eventPreventDefault ( evt );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageReply is running. Element: ", this, evt ] );
				}

				var $elem                 = $ ( this ),
				    message               = _fn.getMessageRawFromDomElement ( $elem ),
				    msg                   = message.message,
				    $replyBody, $characterCounter,
				    characterCount        = _settings.messageReplyCharacterLimit[ msg.social_media_type ],
				    initialCharacterCount = characterCount,
				    text, intn, data, screen_name, which;

				// reply to root message
				if ( $elem.parents ( ".hs_comment" ).data ( "id" ) === undefined ) {

					screen_name = msg.user.screen_name;

					which = "discoverMessage";

				}
				// reply to interaction
				else {

					intn = _fn.getInteractionRawFromDomElement ( $elem, msg );

					screen_name = intn.user.screen_name;

					which = "interactionMessage";

				}

				app.showModal (
					"Reply to @" + screen_name + " on " + msg.social_media_type.toCamelCase ( true ), $ ( ReplyElementTpl ),
					function ()
					{

						$replyBody        = $ ( "#replyBody" );
						$characterCounter = $ ( "#characterCounter" );

						$characterCounter.html ( characterCount );

						$replyBody.keyup (
							function ()
							{

								// get current text
								text = $replyBody.val ();
								// get its length
								characterCount = text.length;

								// if we are over the limit
								if ( characterCount > initialCharacterCount ) {

									// set current character count to max allowed
									characterCount = initialCharacterCount;

									// slice the string to the right length
									text = text.slice ( 0, initialCharacterCount );

									// replace current text with new, shortened version
									$replyBody.val ( text );

								}

								// set the new character count
								$characterCounter.html (
									(
										initialCharacterCount - characterCount
									)
								);

							}
						);

						// default message body includes user's screen name, then focus the element
						$replyBody.val ( "@" + screen_name + " " ).focus ();

						// trigger the keyup event so the count updates
						$replyBody.trigger ( "keyup" );

					},
					function ()
					{

						var screenName = "@" + screen_name + " ";

						// if screenName is not present
						if ( $replyBody.val ().indexOf ( screenName ) === -1 ) {

							// add it to the beginning
							$replyBody.val ( screenName + $replyBody.val () );

							// trigger text length check
							$replyBody.trigger ( "keyup" );

							app.showAlert ( "The message needs to contain '" + screenName + "'. Please check the text for accuracy before attempting to send it again.", "warning" );

							return false;

						}

						// check for empty body i.e. the same as screenName
						if ( $replyBody.val ().length === screenName.length ) {

							app.showAlert ( "Please make sure you type a response before attempting to send it off.", "warning" );

							return false;

						}

						// gather request data
						data = {
							authkey_id: _settings.smHandles[ msg.social_media_type ].id,
							message   : $replyBody.val ()
						};

						switch ( which ) {
							case "discoverMessage":
								_ ( data ).extend (
									{
										inreplyto_message_id: msg.message_id,
										discover_id         : app.session.storage.currentDiscover.id
									}
								);
								break;
							case "interactionMessage":
								_ ( data ).extend (
									{
										inreplyto_message_id: intn.message_id,
										interaction_id      : intn.interaction_id
									}
								);
								break;
						}

						app.startActivity ( "oPheme.messageReply", $elem, true, true );
						$elem.hide ();

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "POST",
										endpoint: "/interactions/replies",
										data    : {
											json_data: {
												data: data
											}
										},
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ( /* interaction */ )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageReply.success is running." ] );
											}

											app.showAlert ( "You have successfully replied to @" + screen_name + "!", "success" );
											$elem.addClass ( "clicked" );

											//if (!msg.interactions || !(msg.interactions.messages instanceof Array)) {
											//
											//	msg.interactions = {
											//		messages: []
											//	};
											//
											//}
											//
											//msg.interactions.messages.push ( interaction );
											//
											//_fn.processRawMessage ( msg, true, message.id );
											//
											// rebuild current view
											_self.doRebuildMessagesList ( _internal.currentListView, true );

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageReply.error is running. Message: ", error ] );
											}

											app.showAlert ( "Could not send reply to @" + screen_name + ". Reason: " + error, "error" );

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageReply.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageReply" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this ), "Reply", "Cancel"
				);

			};

			/**
			 * Action to be taken when message follow button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageFollow = function ( evt )
			{

				app.eventPreventDefault ( evt );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageFollow is running. Element: ", this, evt ] );
				}

				var $elem   = $ ( this ),
				    message = _fn.getMessageRawFromDomElement ( $elem ),
				    msg     = message.message,
				    authkey = _settings.smHandles[ msg.social_media_type ],
				    data    = {
					    screen_name: msg.user.screen_name,
					    authkey_id : authkey.id
				    };

				app.showModal (
					"Follow @" + msg.user.screen_name + " on " + msg.social_media_type.toCamelCase ( true ), "Are you sure you want to follow @" + msg.user.screen_name + "?",
					null,
					function ()
					{

						app.startActivity ( "oPheme.messageFollow", $elem, true, true );
						$elem.hide ();

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "POST",
										endpoint: "/interactions/follows",
										data    : {
											json_data: {
												data: data
											}
										},
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFollow.success is running." ] );
											}

											app.showAlert ( "You have successfully followed @" + msg.user.screen_name + "!", "success" );
											$elem.addClass ( "clicked" );

											var keys = app.session.storage.currentDiscover.authkeys.data, i, len = keys.length;

											for ( i = 0; i < len; i++ ) {

												if ( keys[ i ].id === data.authkey_id ) {

													keys[ i ].userFollows[ msg.user.screen_name ] = true;

													break;

												}

											}

											authkey.userFollows[ msg.user.screen_name ] = true;

											// rebuild current view
											_self.doRebuildMessagesList ( _internal.currentListView, true );

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFollow.error is running. Message: ", error ] );
											}

											app.showAlert ( "Could not follow @" + msg.user.screen_name + ". Reason: " + error, "error" );

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFollow.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageFollow" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this )
				);

			};

			/**
			 * Action to be taken when message unfollow button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageUnFollow = function ( evt )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageUnFollow is running. Element: ", this, evt ] );
				}

				app.eventPreventDefault ( evt );

				var $elem   = $ ( this ),
				    message = _fn.getMessageRawFromDomElement ( $elem ),
				    msg     = message.message,
				    authkey = _settings.smHandles[ msg.social_media_type ];

				app.showModal (
					"Un-Follow @" + msg.user.screen_name + " on " + msg.social_media_type.toCamelCase ( true ), "Are you sure you want to un-follow @" + msg.user.screen_name + "?",
					null,
					function ()
					{

						app.startActivity ( "oPheme.messageUnFollow", $elem, true, true );
						$elem.hide ();

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "DELETE",
										endpoint: "/interactions/follows/" + authkey.id + "/" + msg.user.screen_name,
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFollow.success is running." ] );
											}

											app.showAlert ( "You have successfully un-followed @" + msg.user.screen_name + "!", "success" );
											$elem.removeClass ( "clicked" );

											var keys = app.session.storage.currentDiscover.authkeys.data, i, len = keys.length;

											for ( i = 0; i < len; i++ ) {

												if ( keys[ i ].id === authkey.id ) {

													delete keys[ i ].userFollows[ msg.user.screen_name ];

													break;

												}

											}

											delete authkey.userFollows[ msg.user.screen_name ];

											// rebuild current view
											_self.doRebuildMessagesList ( _internal.currentListView, true );

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFollow.error is running. Message: ", error ] );
											}

											app.showAlert ( "Could not un-follow @" + msg.user.screen_name + ". Reason: " + error, "error" );

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFollow.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageUnFollow" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this )
				);

			};

			/**
			 * Action to be taken when message favourite button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageFavourite = function ( evt )
			{

				app.eventPreventDefault ( evt );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageFavourite is running. Element: ", this, evt ] );
				}

				var $elem   = $ ( this ),
				    message = _fn.getMessageRawFromDomElement ( $elem ),
				    msg     = message.message,
				    data, interaction, which, screen_name;

				data = {
					authkey_id: _settings.smHandles[ msg.social_media_type ].id
				};

				// favourite root message
				if ( $elem.parents ( ".hs_comment" ).data ( "id" ) === undefined ) {

					_ ( data ).extend (
						{
							message_id: msg.message_id
						}
					);

					which = "discoverMessage";

					screen_name = msg.user.screen_name;

				}
				// favourite interaction
				else {

					interaction = _fn.getInteractionRawFromDomElement ( $elem, msg );

					_ ( data ).extend (
						{
							message_id: interaction.message_id
						}
					);

					which = "interactionMessage";

					screen_name = interaction.user.screen_name;

				}

				app.showModal (
					"Favourite @" + screen_name + "'s  message on " + msg.social_media_type.toCamelCase ( true ), "Are you sure you want to favourite @" + screen_name + "'s message?",
					null,
					function ()
					{

						app.startActivity ( "oPheme.messageFavourite", $elem, true, true );
						$elem.hide ();

						var successfulAction = function ()
						{

							app.showAlert ( "You have successfully favourited @" + screen_name + "'s message!", "success" );
							$elem.addClass ( "clicked" );

							switch ( which ) {
								case "discoverMessage":
									msg.isFavourited = true;
									break;
								case "interactionMessage":
									interaction.isFavourited = true;
									break;
							}

							// rebuild current view
							_self.doRebuildMessagesList ( _internal.currentListView, true );

						};

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "POST",
										endpoint: "/interactions/favorites",
										data    : {
											json_data: {
												data: data
											}
										},
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFavourite.success is running." ] );
											}

											successfulAction ();

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFavourite.error is running. Message: ", error ] );
											}

											if ( error.indexOf ( "[139]" ) > -1 ) { successfulAction (); }
											else { app.showAlert ( "Could not favourite @" + screen_name + "'s message. Reason: " + error, "error" ); }

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageFavourite.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageFavourite" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this )
				);

			};

			/**
			 * Action to be taken when message unfavourite button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageUnFavourite = function ( evt )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageUnFavourite is running. Element: ", this, evt ] );
				}

				app.eventPreventDefault ( evt );

				var $elem   = $ ( this ),
				    message = _fn.getMessageRawFromDomElement ( $elem ),
				    /**
				     *
				     * @type {{backend_message_id: string, isFavourited: boolean}}
				     */
				    msg     = message.message,
				    data, interaction, which, screen_name;

				data = {
					authkey_id: _settings.smHandles[ msg.social_media_type ].id
				};

				// unfavourite root message
				if ( $elem.parents ( ".hs_comment" ).data ( "id" ) === undefined ) {

					_ ( data ).extend (
						{
							message_id: msg.message_id
						}
					);

					which = "discoverMessage";

					screen_name = msg.user.screen_name;

				}
				// unfavourite interaction
				else {

					interaction = _fn.getInteractionRawFromDomElement ( $elem, msg );

					_ ( data ).extend (
						{
							message_id: interaction.message_id
						}
					);

					which = "interactionMessage";

					screen_name = interaction.user.screen_name;

				}

				app.showModal (
					"Un-Favourite @" + screen_name + "'s  message on " + msg.social_media_type.toCamelCase ( true ), "Are you sure you want to un-favourite @" + screen_name + "'s message?",
					null,
					function ()
					{

						app.startActivity ( "oPheme.messageUnFavourite", $elem, true, true );
						$elem.hide ();

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "DELETE",
										endpoint: "/interactions/favorites/" + data.authkey_id + "/" + data.message_id,
										data    : {
											json_data: {
												data: data
											}
										},
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFavourite.success is running." ] );
											}

											app.showAlert ( "You have successfully un-favourited @" + screen_name + "'s message!", "success" );
											$elem.removeClass ( "clicked" );

											switch ( which ) {
												case "discoverMessage":
													delete msg.isFavourited;
													break;
												case "interactionMessage":
													delete interaction.isFavourited;
													break;
											}

											// rebuild current view
											_self.doRebuildMessagesList ( _internal.currentListView, true );

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFavourite.error is running. Message: ", error ] );
											}

											app.showAlert ( "Could not un-favourite @" + screen_name + "'s message. Reason: " + error, "error" );

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageUnFavourite.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageUnFavourite" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this )
				);

			};

			/**
			 * Action to be taken when message retweet button is clicked
			 * @param {object} evt Click event triggered
			 */
			_fn.messageRetweet = function ( evt )
			{

				app.eventPreventDefault ( evt );

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.messageRetweet is running. Element: ", this, evt ] );
				}

				var $elem   = $ ( this ),
				    message = _fn.getMessageRawFromDomElement ( $elem ),
				    msg     = message.message,
				    data, interaction, which, screen_name;

				data = {
					authkey_id: _settings.smHandles[ msg.social_media_type ].id
				};

				// retweet root message
				if ( $elem.parents ( ".hs_comment" ).data ( "id" ) === undefined ) {

					_ ( data ).extend (
						{
							message_id: msg.message_id
						}
					);

					which = "discoverMessage";

					screen_name = msg.user.screen_name;

				}
				// retweet interaction
				else {

					interaction = _fn.getInteractionRawFromDomElement ( $elem, msg );

					_ ( data ).extend (
						{
							message_id: interaction.message_id
						}
					);

					which = "interactionMessage";

					screen_name = interaction.user.screen_name;

				}

				app.showModal (
					"Retweet @" + screen_name + "'s  message on " + msg.social_media_type.toCamelCase ( true ), "Are you sure you want to retweet @" + screen_name + "'s message?",
					null,
					function ()
					{

						app.startActivity ( "oPheme.messageRetweet", $elem, true, true );
						$elem.hide ();

						setTimeout (
							function ()
							{

								app.callAPI (
									{
										method  : "POST",
										endpoint: "/interactions/retweets",
										data    : {
											json_data: {
												data: data
											}
										},
										headers : {
											"Authorization": app.session.storage.apiInfo.get ( "access_token" )
										}
									}, {
										success : function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageRetweet.success is running." ] );
											}

											app.showAlert ( "You have successfully retweeted @" + screen_name + "'s message!", "success" );
											$elem.addClass ( "clicked" );

											switch ( which ) {
												case "discoverMessage":
													msg.isRetweeted = true;
													break;
												case "interactionMessage":
													interaction.isRetweeted = true;
													break;
											}

											// rebuild current view
											_self.doRebuildMessagesList ( _internal.currentListView, true );

										},
										error   : function ( error )
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageRetweet.error is running. Message: ", error ] );
											}

											app.showAlert ( "Could not retweet @" + screen_name + "'s message. Reason: " + error, "error" );

										},
										complete: function ()
										{

											if ( DEBUG ) {
												app.logThis ( [ "oPheme.messageRetweet.complete is running." ] );
											}

											app.stopActivity ( "oPheme.messageRetweet" );
											$elem.show ();

										}
									}
								);

							}, 0
						);

					}.bind ( this )
				);

			};

			/**
			 * If there are messages to process, start message queue.
			 */
			_fn.maybeStartQueue = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.oPheme.check.maybeStartQueue is running." ] );
				}

				if ( _internal.messages_temp.length ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.check: New messages detected, starting Queue ... Messages: ", _internal.messages_temp ] );
					}

					_self.startQueue ();

				}

			};

			/**
			 * Updates the last check timestamp for a given check
			 * @param {string} which One of: interactions, job, retweets, favourites, follows
			 * @returns {boolean} TRUE if successful, FALSE if invalid WHICH given
			 */
			_fn.updateLastCheckFor = function ( which )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.updateLastCheckFor is running. Which: ", which ] );
				}

				if ( _internal.last_checks.hasOwnProperty ( which ) ) {

					_internal.last_checks[ which ] = moment.utc ().format ( "YYYY-MM-DD HH:mm:ss" );

					return true;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.updateLastCheckFor: Invalid WHICH given." ] );
				}

				return false;

			};

			/**
			 * Gets the last check timestamp for a given check
			 * @param {string} which One of: interactions, job, retweets, favourites, follows
			 * @returns {string|boolean} Timestamp if successful, FALSE if invalid WHICH given
			 */
			_fn.getLastCheckFor = function ( which )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.getLastCheckFor is running. Which: ", which ] );
				}

				if ( _internal.last_checks.hasOwnProperty ( which ) ) {

					return _internal.last_checks[ which ];

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.getLastCheckFor: Invalid WHICH given." ] );
				}

				return false;

			};

			/**
			 * Server api call, new content check
			 */
			_fn.check = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.check is running." ] );
				}

				// check whether marker limit has been reached
				if ( _fn.checkMessageLimit () ) {

					return;

				}

				// check for new messages
				_fn.checkMessages ();

			};

			/**
			 * Server api call, retweeted messages check
			 */
			_fn.checkRetweets = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.checkRetweets is running." ] );
				}

				var i, authkeys = app.session.storage.currentDiscover.authkeys.data, len = authkeys.length,
				    lastChecked,
				    successFn   = function ( retweets )
				    {

					    if ( DEBUG ) {
						    app.logThis ( [ "oPheme.checkRetweets.success is running. Data: ", retweets ] );
					    }

					    var j, len2     = retweets.length, ret,
					        k, messages = _internal.messages_data.messages, len3 = messages.length, msg,
					        l, inter, len4,
					        matched     = 0,
					        actualPosition;

					    for ( j = 0; j < len2; j++ ) {

						    ret = retweets[ j ];

						    for ( k = 0; k < len3; k++ ) {

							    msg = messages[ k ];

							    if ( msg.message_id === ret.message_id && !msg.isRetweeted ) {

								    msg.isRetweeted = true;

								    break;

							    }

							    if ( msg.interactions ) {

								    len4 = msg.interactions.messages.length;

								    for ( l = 0; l < len4; l++ ) {

									    inter = msg.interactions.messages[ l ];

									    if ( inter.message_id === ret.message_id && !inter.isRetweeted ) {

										    inter.isRetweeted = true;

										    break;

									    }

								    }

								    // ensure that k keeps its last value
								    if ( l !== len4 ) {

									    break;

								    }

							    }

						    }

						    // the loops stopped early
						    if ( k !== len3 || l !== len4 ) {

							    matched++;

							    // calculate actual position of the message since messages are reversed when displayed
							    actualPosition = _internal.total_message_count - 1 - k;

							    // re-compile/re-process the message
							    _fn.processRawMessage ( msg, true, actualPosition );

						    }

					    }

					    // if there were some retweets matched
					    if ( matched > 0 ) {

						    // rebuild current view
						    _self.doRebuildMessagesList ( _internal.currentListView, true );

					    }

					    _fn.updateLastCheckFor ( "retweets" );

				    };

				// check retweets for all authkeys
				for ( i = 0; i < len; i++ ) {

					if ( authkeys[ i ].social_media_platform_name === "instagram" ) { continue; }

					lastChecked = _fn.getLastCheckFor ( "retweets" );

					app.callAPI (
						{
							method  : "GET",
							endpoint: "/interactions/retweets/" + authkeys[ i ].id,
							data    : {
								number: _settings.max_refresh_items,
								after : lastChecked
							},
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success: successFn
						}
					);

				}

			};

			/**
			 * Server API call, favourited messages check
			 */
			_fn.checkFavourites = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.checkFavourites is running." ] );
				}

				var i, authkeys = app.session.storage.currentDiscover.authkeys.data, len = authkeys.length,
				    lastChecked,
				    successFn   = function ( favourites )
				    {

					    if ( DEBUG ) {
						    app.logThis ( [ "oPheme.checkFavourites.success is running. Data: ", favourites ] );
					    }

					    var j, len2     = favourites.length, fav,
					        k, messages = _internal.messages_data.messages, len3 = messages.length, msg,
					        l, inter, len4,
					        matched     = 0,
					        actualPosition;

					    for ( j = 0; j < len2; j++ ) {

						    fav = favourites[ j ];

						    for ( k = 0; k < len3; k++ ) {

							    msg = messages[ k ];

							    if ( msg.message_id === fav.message_id && !msg.isFavourited ) {

								    msg.isFavourited = true;

								    break;

							    }

							    if ( msg.interactions ) {

								    len4 = msg.interactions.messages.length;

								    for ( l = 0; l < len4; l++ ) {

									    inter = msg.interactions.messages[ l ];

									    if ( inter.message_id === fav.message_id && !inter.isFavourited ) {

										    inter.isFavourited = true;

										    break;

									    }

								    }

								    // ensure that k keeps its last value
								    if ( l !== len4 ) {

									    break;

								    }

							    }

						    }

						    // the loops stopped early
						    if ( k !== len3 || l !== len4 ) {

							    matched++;

							    // calculate actual position of the message since messages are reversed when displayed
							    actualPosition = _internal.total_message_count - 1 - k;

							    // re-compile/re-process the message
							    _fn.processRawMessage ( msg, true, actualPosition );

						    }

					    }

					    // if there were some favourites matched
					    if ( matched > 0 ) {

						    // rebuild current view
						    _self.doRebuildMessagesList ( _internal.currentListView, true );

					    }

					    _fn.updateLastCheckFor ( "favourites" );

				    };

				// check favourites for all authkeys
				for ( i = 0; i < len; i++ ) {

					lastChecked = _fn.getLastCheckFor ( "favourites" );

					app.callAPI (
						{
							method  : "GET",
							endpoint: "/interactions/favorites/" + authkeys[ i ].id,
							data    : {
								number: _settings.max_refresh_items,
								after : lastChecked
							},
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success: successFn
						}
					);

				}

			};

			/**
			 * Check server for new messages.
			 */
			_fn.checkMessages = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.checkMessages is running." ] );
				}

				var lastChecked = _fn.getLastCheckFor ( "job" );

				app.callAPI (
					{
						method  : "GET",
						endpoint: "/discovers/" + app.session.storage.currentDiscover.id + "/messages/count",
						data    : {
							after: lastChecked
						},
						headers : {
							"Authorization": app.session.storage.apiInfo.get ( "access_token" )
						}
					}, {
						success: function ( mCountContainer )
						{

							if ( DEBUG ) {
								app.logThis ( [ "oPheme.checkMessages.mCount.success is running. New message count: ", mCountContainer ] );
							}

							if ( _internal.timers.interactions === null ) {

								$ ( "#stream-container" ).unblock ();

							}

							if ( mCountContainer.count > 0 ) {

								if ( DEBUG ) {
									app.logThis ( [ "oPheme.checkMessages.mCount.success: New messages on server, fetching them ..." ] );
								}

								var remainingMessages = mCountContainer.count,
								    cursor            = null,
								    checkFn           = function ()
								    {

									    app.callAPI (
										    {
											    method  : "GET",
											    endpoint: "/discovers/" + app.session.storage.currentDiscover.id + "/messages",
											    data    : {
												    after : lastChecked,
												    cursor: cursor,
												    number: _settings.max_refresh_items
											    },
											    headers : {
												    "Authorization": app.session.storage.apiInfo.get ( "access_token" )
											    }
										    }, {
											    /**
											     * @param {object[]} messages
											     * @param {object} meta
											     */
											    success: function ( messages, meta )
											    {

												    if ( DEBUG ) {
													    app.logThis ( [ "oPheme.checkMessages.mCount.success.messages.success is running. Messages: ", messages, "Meta: ", meta ] );
												    }

												    // UI message allowance
												    var left     = _settings.omap.noOfMarkers - _internal.total_message_count,
												        position = (
													        left > messages.length ? 0 : (
														                                     left - messages.length - 1
													                                     ) * -1
												        );

												    if ( DEBUG ) {
													    app.logThis ( [ "oPheme.checkMessages.mCount.success.messages.success: Calculated UI message allowance left: ", left, "Getting messages from array starting at position: ", position ] );
												    }

												    _internal.messages_temp = _internal.messages_temp.concat ( messages.slice ( position ) );
												    _internal.messages_temp.reverse ();
												    //_internal.messages_data.messages = _internal.messages_data.messages.concat ( messages.slice ( position ) );
												    _internal.messages_data.messages = messages.slice ( position ).concat ( _internal.messages_data.messages );

												    _internal.total_message_count = _internal.messages_data.messages.length;

												    // mark messages as taken care of
												    remainingMessages -= _settings.max_refresh_items;

												    _internal.cursors.messages = meta.cursor.next;

												    // save cursor if there are more messages
												    if ( remainingMessages > 0 && position === 0 ) {

													    _internal.cursors.messages = meta.cursor.next;

												    }
												    // otherwise just remove the load more UI button
												    else {

													    $ ( _settings.loadMoreButtonSelector ).remove ();

												    }

												    //// fetch next set of messages, if needed
												    //if ( remainingMessages > 0 && position === 0 ) {
												    //
												    //   // save cursor for next set of results
												    //   cursor = meta.cursor.next;
												    //
												    //   // get next set of messages
												    //   checkFn ();
												    //
												    //}
												    //else {

												    _fn.updateLastCheckFor ( "job" );

												    _internal.newest_older_message_timestamp = moment.utc ( messages[ messages.length - 1 ].timestamp_server.date ).format ( "YYYY-MM-DD HH:mm:ss" );

												    // signal the fact that messages have finished processing, if required
												    if ( _internal.messages_defer_object ) {

													    _internal.messages_defer_object.resolve ();

													    delete _internal.messages_defer_object;

												    }

												    // start queue
												    _self.startQueue ();

												    //}

											    }
										    }
									    );

								    };

								// start the process
								checkFn ();

							}
							else {

								_fn.updateLastCheckFor ( "job" );

								// interactions not started yet, meaning that there aren't any messages for this discover yet, so unblock the view
								if ( _internal.timers.interactions === null ) {

									$ ( "#stream-container" ).unblock ();

								}

							}

						}
					}
				);

			};

			/**
			 * Runs when Load More Ui button is clicked to fetch messages
			 */
			_fn.fetchOlderMessages = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.fetchOlderMessages is running." ] );
				}

				if ( _internal.cursors.messages === null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.fetchOlderMessages: No more old messages to fetch. Skipping request ..." ] );
					}

					return;

				}

				var $loadMoreContainer = $ ( _settings.loadMoreButtonSelector ),
				    $spinner           = $ ( app.spinner ).css ( "color", "white" ),
				    $this              = $ ( this ),
				    $currentButton;

				// map button clicked
				if ( $this.parents ( "#load-more-map" ).length ) {

					$currentButton = $this.find ( ".fa-refresh" );

				}
				// buttom stream button clicked
				else {

					$currentButton = $this;

				}

				$spinner.insertAfter ( $currentButton );
				$currentButton.hide ();

				setTimeout (
					function ()
					{

						app.callAPI (
							{
								method  : "GET",
								endpoint: "/discovers/" + app.session.storage.currentDiscover.id + "/messages",
								data    : {
									cursor: _internal.cursors.messages,
									number: _settings.max_refresh_items
								},
								headers : {
									"Authorization": app.session.storage.apiInfo.get ( "access_token" )
								}
							}, {
								success: function ( messages, meta )
								{

									if ( DEBUG ) {
										app.logThis ( [ "oPheme.fetchOlderMessages.success is running. Messages: ", messages, "Meta: ", meta ] );
									}

									_internal.messages_old_temp = _internal.messages_old_temp.concat ( messages );
									//_internal.messages_old_temp.reverse ();
									_internal.messages_data.messages = messages.concat ( _internal.messages_data.messages );
									//_internal.messages_data.messages = _internal.messages_data.messages.concat ( messages );

									_internal.total_message_count = _internal.messages_data.messages.length;

									_internal.cursors.messages = meta.cursor.next;

									// save cursor if there are more messages
									if ( parseInt ( meta.cursor.count, 10 ) % _settings.max_refresh_items === 0 ) {

										_internal.cursors.messages = meta.cursor.next;

									}
									// otherwise just remove the load more UI button
									else {

										$loadMoreContainer.remove ();

									}

									_internal.newest_older_message_timestamp = moment.utc ( messages[ messages.length - 1 ].timestamp_server.date ).format ( "YYYY-MM-DD HH:mm:ss" );

									// parse messages
									_fn.parseMessages ( true );

									// rebuild the list to recalculate heights
									_self.doRebuildMessagesList (
										_internal.currentListView, true, null, true, function ()
										{

											$currentButton.show ();
											$spinner.remove ();

											_fn.checkInteractions ( true );

										}
									);

								}

							}
						);

					}, 0
				);

			};

			// TODO: this action duplicates initial message
			/**
			 * Check server for new interactions.
			 */
			_fn.checkInteractions = function ( oldInteractions )
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.checkInteractions is running." ] );
				}

				var lastChecked = !!oldInteractions ? _internal.newest_older_message_timestamp : _fn.getLastCheckFor ( "interactions" ),
					$blockUIElement = $("div.blockUI.blockMsg" ),
					$nowCheckingMessage;

				if ($blockUIElement.length) {

					$nowCheckingMessage = $("<div style='display: none'><br>Now checking again ...</div>");

					$blockUIElement.append($nowCheckingMessage);

					$nowCheckingMessage.fadeIn(300);

				}

				app.callAPI (
					{
						method  : "GET",
						endpoint: "/discovers/" + app.session.storage.currentDiscover.id + "/interactions/count",
						data    : (
							!!oldInteractions ? {
								before: lastChecked
							} : {
								after: lastChecked
							}
						),
						headers : {
							"Authorization": app.session.storage.apiInfo.get ( "access_token" )
						}
					}, {
						success: function ( iCountContainer )
						{

							if ( DEBUG ) {
								app.logThis ( [ "oPheme.checkInteractions.iCount.success is running. New interactions count: ", iCountContainer ] );
							}

							if ( iCountContainer.count > 0 ) {

								if ( DEBUG ) {
									app.logThis ( [ "oPheme.checkInteractions.iCount.success: New interactions on server, fetching them ..." ] );
								}

								var remainingMessages = iCountContainer.count,
								    cursor            = null,
								    checkFn           = function ()
								    {

									    app.callAPI (
										    {
											    method  : "GET",
											    endpoint: "/discovers/" + app.session.storage.currentDiscover.id + "/interactions",
											    data    : (
												    !!oldInteractions ? {
													    before: lastChecked,
													    cursor: cursor,
													    number: _settings.max_refresh_items
												    } : {
													    after : lastChecked,
													    cursor: cursor,
													    number: _settings.max_refresh_items
												    }
											    ),
											    headers : {
												    "Authorization": app.session.storage.apiInfo.get ( "access_token" )
											    }
										    }, {
											    /**
											     * @param { { interaction_id: string, root_message_id: string, root_backend_message_id: string, message_id: string, backend_message_id: string, user: { id: string, screen_name: string, profile_image_url: string }, text: string, timestamp: string, sentiment: string, klout_score: number=, social_media_type: string }[] } interactions
											     * @param { { cursor: object } } meta Extra info used for pagination purposes
											     */
											    success: function ( interactions, meta )
											    {

												    cursor = meta.cursor.next;

												    if ( DEBUG ) {
													    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success is running. Interactions: ", interactions ] );
												    }

												    var inter, i, msgChk, j, found, msgStats, newInts = 0, actualPosition;

												    if ( DEBUG ) {
													    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success: Pairing up interactions with messages ..." ] );
												    }

												    // match up the interactions with their parent message
												    for ( i = 0; i < iCountContainer.count; i++ ) {

													    /**
													     *
													     * @type {{ root_message_id: string }}
													     */
													    inter = $.extend ( true, {}, interactions[ i ] );

													    found = false;

													    for ( j = 0; j < _internal.total_message_count; j++ ) {

														    /**
														     * @type {{ message_id: string }}
														     */
														    msgChk = _internal.messages_data.messages[ j ];

														    if ( DEBUG ) {
															    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success: Comparing Interaction Root Message ID", inter.root_message_id, inter.social_media_type, "With Message ID: ", msgChk.message_id, msgChk.social_media_type, "Message Array Position: ", j ] );
														    }

														    if ( inter.social_media_type === msgChk.social_media_type && inter.root_message_id === msgChk.message_id ) {

															    if ( DEBUG ) {
																    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success: Matched IDs!" ] );
															    }

															    if ( !msgChk.interactions ) {

																    msgChk.interactions = {
																	    messages: []
																    };

															    }

															    // attach the interaction to the message
															    msgChk.interactions.messages.push ( inter );

															    // calculate actual position of the message since messages are reversed when displayed
															    actualPosition = _internal.total_message_count - 1 - j;

															    // re-compile/re-process the message
															    msgStats = _fn.processRawMessage ( msgChk, true, actualPosition );

															    if ( DEBUG ) {
																    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success: Data from processRawMessage: ", msgStats ] );
															    }

															    // keep track of new interactions
															    newInts += msgStats.interactionsCountNew;

															    // set found to true
															    found = true;

															    // stop, found a home for this interaction
															    break;

														    }

													    }

													    if ( !found ) {

														    if ( DEBUG ) {
															    app.logThis ( [ "oPheme.checkInteractions.iCount.success.interactions.success: Error! DID NOT Match ANY IDs!" ] );
														    }

													    }

												    }

												    // update interactions count
												    _fn.updateVisualCounts ( null, newInts );

												    // mark messages as taken care of
												    remainingMessages -= _settings.max_refresh_items;

												    // fetch next set of messages, if needed
												    if ( remainingMessages > 0 ) {

													    // save cursor for next set of results
													    cursor = meta.cursor.next;

													    // get next set of interactions
													    checkFn ();

												    }
												    // all done, trigger refresh
												    else {

													    _fn.updateLastCheckFor ( "interactions" );

													    // rebuild current view
													    _self.doRebuildMessagesList ( _internal.currentListView, true );

													    // if current view is interactions
													    //if ( _internal.currentListView === "interactions" ) {
													    //
													    //    // refresh view
													    //    _self.doRebuildMessagesList ( "interactions", true );
													    //
													    //}

												    }

											    }

										    }
									    );

								    };

								// start off the process
								checkFn ();

							}
							else {

								_fn.updateLastCheckFor ( "interactions" );

							}

						},
						complete: function () {

							if ($nowCheckingMessage && $nowCheckingMessage.length) {

								setTimeout(function () { $nowCheckingMessage.fadeOut(300, function() { $(this).remove(); }); }, 1000);

							}

						}
					}
				);

			};

			/**
			 * Start job interval check
			 */
			_self.startJob = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startJob is running." ] );
				}

				if ( _internal.timers.job === null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.startJob: Starting Job ..." ] );
					}

					var checkFn = function ()
					{

						// get the new messages
						_fn.check ();

					};

					// set the timer
					_internal.timers.job = setInterval ( checkFn, _settings.timeout );

					// create a deferred object which should be resolved when first set of messages are in
					_internal.messages_defer_object = new $.Deferred ();

					// when defered is resolved, start interactions check
					_internal.messages_defer_object.done ( _self.startInteractions );

					// get messages
					checkFn ();

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startJob: Job already started, skipping request ..." ] );
				}

			};

			/**
			 * Start interactions interval check
			 */
			_self.startInteractions = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startInteractions is running." ] );
				}

				if ( _internal.timers.interactions === null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.startInteractions: Starting Interactions ..." ] );
					}

					var checkFn = function ()
					{

						// get the new messages
						_fn.checkInteractions ();

						// see if any of them have been favourited
						_fn.checkFavourites ();

						// see if any of them have been retweeted
						_fn.checkRetweets ();

					};

					// set the timer
					_internal.timers.interactions = setInterval ( checkFn, _settings.timeout );

					// check right away
					checkFn ();

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startInteractions: Interactions already started, skipping request ..." ] );
				}

			};

			/**
			 * Start queue interval process
			 */
			_self.startQueue = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startQueue is running." ] );
				}

				if ( _internal.timers.queue === null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.startQueue: Starting Queue ..." ] );
					}

					// set the timer
					_internal.timers.queue = setInterval (
						function ()
						{

							_fn.parseMessages ();

						}, _settings.display_freq
					);

					// parse messages straight away
					_fn.parseMessages ();

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.startQueue: Queue is already started, skipping request ..." ] );
				}

			};

			/**
			 * Stop job
			 */
			_self.stopJob = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopJob is running." ] );
				}

				if ( _internal.timers.job !== null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.stopJob: Stopping Job ..." ] );
					}

					// clear the job
					clearInterval ( _internal.timers.job );

					_internal.timers.job = null;

					_self.stopQueue ();

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopJob: Job already stopped, skipping request ..." ] );
				}

			};

			/**
			 * Stop interactions
			 */
			_self.stopInteractions = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopInteractions is running." ] );
				}

				if ( _internal.timers.interactions !== null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.stopInteractions: Stopping Interactions ..." ] );
					}

					// clear the job
					clearInterval ( _internal.timers.interactions );

					_internal.timers.interactions = null;

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopInteractions: Interactions already stopped, skipping request ..." ] );
				}

			};

			/**
			 * Stop queue
			 */
			_self.stopQueue = function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopQueue is running." ] );
				}

				if ( _internal.timers.queue !== null ) {

					if ( DEBUG ) {
						app.logThis ( [ "oPheme.stopQueue: Stopping Queue ..." ] );
					}

					// clear the job
					clearInterval ( _internal.timers.queue );

					_internal.timers.queue = null;

					return;

				}

				if ( DEBUG ) {
					app.logThis ( [ "oPheme.stopQueue: Queue is already stopped, skipping request ..." ] );
				}

			};

			// replace http(s) links with anchor tags
			_self.replaceURLWithHTMLLinks = function ( text )
			{
				var exp = /(\b(https?|ftp|file):\/\/[A-Z0-9+&@#\/%?=~_:.]*[A-Z0-9+&@#\/%=~_])/ig;
				return text.replace ( exp, "<a href='$1' target='_blank'>$1</a>" );
			};

			// initiate Opheme instance setup
			_init ();

		};

	}
)
;
