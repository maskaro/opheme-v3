define (
	[
		"jquery",
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/forms/_setup.html",
		"textplugin!/templates/pages_full/discovers.html",
		"opheme"
		//"moment"
	], function ( $,
	              _,
	              app,
	              BaseView,
	              DiscoversSetupFormElementTpl,
	              DiscoversPageTpl,
	              Opheme
	              //moment
	)
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "DiscoversSetupView",

				viewPublicName: "Discover Setup",

				viewDefaultLocation: "discovers/view-setup",

				eventList: [
					"doSetupProcess"
				],

				controllerName: "discovers",

				controllerAction: "view-setup",

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversSetupView.initialize is running." ] );
					}

					return this;

				},

				events: {
					"submit #discover-setup-form": "discover-setup-process"
				},

				"discover-setup-process": function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoverSetupView.discover-setup-process is running." ] );
					}

					app.eventPreventDefault ( evt );

					if ( $ ( "#discover-setup-form" ).parsley ().isValid () ) {

						this.dispatcher.trigger ( "doSetupProcess" );

					}
					else {

						app.showAlert ( "Please deal with the red form fields!", "error" );

					}

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversSetupView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( DiscoversPageTpl );

					subTemplates.push (
						{
							"renderDiscoversSetup": _.template ( DiscoversSetupFormElementTpl )
						}
					);

					data = {
						data: {
							user           : app.session.storage.user.toJSON (),
							smHandles      : app.session.storage.smHandles,
							smModules      : app.config.smModules,
							currentDiscover: app.session.storage.currentDiscover
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversSetupView.render: Data: ", data ] );
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
						app.logThis ( [ "DiscoversSetupView.viewSetup is running." ] );
					}

					/* AUTHKEY SELECT BOXES */
					$ ( "select[name$='-handle']" ).select2 (
						{
							minimumResultsForSearch: 5
						}
					);

					/* KEYWORD FILTER */
					$ ( "#filter" ).tagsinput (
						{
							confirmKeys: [ 13 ] //, 44] // only react to Enter/Return key
						}
					);

					$ ( ".bootstrap-tagsinput input" ).attr ( "style", "width: 10em !important;" );

					/* DISCOVER OPTIONS */
					//$ ( "#discoverOptions" ).on (
					//	"show.bs.collapse", function ()
					//	{
					//
					//		$ ( "#optionToggler" ).text ( "Clear Refinements" );
					//
					//		$ ( "#refinementsWrapper" ).toggleClass ( "well well-sm" );
					//
					//		app.scrollBodyToTopOf ( $ ( "#radiusWrapper" ) );
					//
					//	}
					//);
					//
					//$ ( "#discoverOptions" ).on (
					//	"hide.bs.collapse", function ()
					//	{
					//
					//		$ ( "#optionToggler" ).text ( "Refine Discover (optional)" );
					//
					//		$ ( "#refinementsWrapper" ).toggleClass ( "well well-sm" );
					//
					//		// setup optional defaults
					//		resetOptionalsDefaults ();
					//
					//	}
					//);
					//
					//var discoverOptionsTpl     = $ ( "#discoverOptions" ).clone (),
					//    $startDate, $endDate, $startDateTime, $endDateTime,
					//    intervalsIdPattern     = "ti",
					//    timepickerDefaults     = {
					//	    maxTime   : "11:59pm",
					//	    timeFormat: "g:ia",
					//	    step      : 15,
					//	    useSelect : true,
					//	    noneOption: [ "Select" ],
					//	    show2400  : true
					//    },
					//    maxIntervals           = 3,
					//    resetOptionalsDefaults = function ( callback )
					//    {
					//
					//	    $ ( "#discoverOptions" ).empty ().html ( discoverOptionsTpl.html () );
					//
					//	    setupOptionals ( callback );
					//
					//    },
					//    setupOptionals         = function ( callback )
					//    {
					//
					//	    /* FILTER TAGSINPUT */
					//
					//	    $ ( "#filter" ).tagsinput (
					//		    {
					//			    confirmKeys: [ 13 ] //, 44] // only react to Enter/Return key
					//		    }
					//	    );
					//
					//	    $ ( ".bootstrap-tagsinput input" ).attr ( "style", "width: 10em !important;" );
					//
					//	    /* TIME INTERVALS & TIMEPICKER */
					//	    var $timeIntervalTpl         = $ ( "#" + intervalsIdPattern + "1" ).clone (),
					//	        renumberTimeIntervalsIDs = function ()
					//	        {
					//
					//		        var id = 1;
					//
					//		        $ ( ".time-interval" ).each (
					//			        function ()
					//			        {
					//
					//				        $ ( this ).attr (
					//					        "id", intervalsIdPattern + (
					//						        id++
					//					        )
					//				        );
					//
					//			        }
					//		        );
					//
					//	        }, // timepicker
					//	        setupTimepicker          = function ( $elem, id, $prev )
					//	        {
					//
					//		        var $startElem, $endElem,
					//		            options;
					//
					//		        options    = _.extend (
					//			        {}, timepickerDefaults, {
					//				        scrollDefault: "9:00am"
					//			        }
					//		        );
					//		        $startElem = $elem.find ( ".startTimeField" ).timepicker ( options )
					//			        .on (
					//			        "changeTime", function ()
					//			        {
					//
					//				        $endElem.timepicker (
					//					        "option", {
					//						        "minTime"     : $ ( this ).val (),
					//						        "showDuration": true
					//					        }
					//				        );
					//
					//				        // (!$endElem.timepicker("getTime") ? $endElem.timepicker("show") : "");
					//
					//			        }
					//		        );
					//
					//		        options  = _.extend (
					//			        {}, timepickerDefaults, {
					//				        scrollDefault: "5:00pm"
					//			        }
					//		        );
					//		        $endElem = $elem.find ( ".endTimeField" ).timepicker ( options )
					//			        .on (
					//			        "changeTime", function ()
					//			        {
					//
					//				        var $next = $ ( this ).parent ( "#timeIntervals" ).find (
					//					        "#" + intervalsIdPattern + (
					//					        id + 1
					//					        )
					//				        );
					//
					//				        $next = (
					//					        $next.length ? $next.find ( ".startTimeField" ) : ""
					//				        );
					//
					//				        if ( $next.length && !$next.timepicker ( "getTime" ) ) {
					//
					//					        $next.timepicker ( "option", "minTime", $ ( this ).val () );
					//					        // $next.timepicker("show");
					//
					//				        }
					//
					//			        }
					//		        );
					//
					//		        if ( $prev ) {
					//
					//			        var minTime = $prev.find ( ".endTimeField" ).timepicker ( "getTime" );
					//
					//			        if ( minTime ) {
					//
					//				        $startElem.timepicker ( "option", "minTime", minTime );
					//				        $startElem.timepicker ( "setTime", minTime );
					//				        $endElem.timepicker ( "option", "minTime", minTime );
					//				        $endElem.timepicker ( "setTime", minTime );
					//
					//			        }
					//
					//		        }
					//
					//	        };
					//
					//	    // remove time interval click
					//	    $ ( "#timeIntervals" ).on (
					//		    "click", ".remove-timeinterval", function ()
					//		    {
					//
					//			    if ( $ ( ".time-interval" ).length === 1 ) {
					//
					//				    return false;
					//
					//			    }
					//
					//			    $ ( this ).parent ().parent ( ".time-interval" ).slideUp (
					//				    "fast", function ()
					//				    {
					//
					//					    $ ( this ).remove ();
					//
					//					    renumberTimeIntervalsIDs ();
					//
					//					    if ( $ ( ".time-interval" ).length === 1 ) {
					//
					//						    $ ( ".remove-timeinterval" ).hide ();
					//
					//					    }
					//
					//					    if ( $ ( ".time-interval" ).length < maxIntervals ) {
					//
					//						    $ ( "#addInterval" ).slideDown (
					//							    "fast", function ()
					//							    {
					//
					//								    $ ( this ).show ();
					//
					//							    }
					//						    );
					//
					//						    $ ( "#timeIntervals p.text-danger" ).slideUp (
					//							    "fast", function ()
					//							    {
					//
					//								    $ ( this ).hide ();
					//
					//							    }
					//						    );
					//
					//					    }
					//
					//				    }
					//			    );
					//
					//			    return false;
					//
					//		    }
					//	    );
					//
					//	    // add interval click
					//	    $ ( "#timeIntervals" ).on (
					//		    "click", "#addInterval", function ( evt )
					//		    {
					//
					//			    app.eventPreventDefault ( evt );
					//
					//			    if ( $ ( ".time-interval" ).length < maxIntervals ) {
					//
					//				    // get the id number of the last element of this class
					//				    var c     = parseInt (
					//					        $ ( ".time-interval" ).last ().attr ( "id" ).substr ( intervalsIdPattern.length ),
					//					        10
					//				        ),
					//				        $prev = $ ( "#" + intervalsIdPattern + c ),
					//				        newId = c + 1;
					//
					//				    var $el = $timeIntervalTpl.clone ()
					//					    .attr ( "id", intervalsIdPattern + newId ).hide ();
					//
					//				    $el.insertAfter ( $prev ).slideDown ( "fast" );
					//
					//				    // binds for new elements
					//				    setupTimepicker ( $el, newId, $prev );
					//
					//				    if ( $ ( ".time-interval" ).length === maxIntervals ) {
					//
					//					    $ ( "#addInterval" ).slideUp (
					//						    "fast", function ()
					//						    {
					//
					//							    $ ( this ).hide ();
					//
					//						    }
					//					    );
					//
					//					    $ ( "#timeIntervals p.text-danger" ).slideDown (
					//						    "fast", function ()
					//						    {
					//
					//							    $ ( this ).show ();
					//
					//						    }
					//					    );
					//
					//				    }
					//
					//				    $ ( "#timeIntervals .remove-timeinterval" ).show ();
					//
					//			    }
					//
					//		    }
					//	    );
					//
					//	    // clear the parsley elements from within $timeIntervalTpl
					//	    $timeIntervalTpl.find ( "ul[id^='parsley-id']" ).remove ();
					//	    // initial timepicker setup
					//	    setupTimepicker ( $ ( "#ti1" ) );
					//
					//	    /* DATEPICKER */
					//
					//	    var nowTemp     = new Date (),
					//	        now         = new Date ( nowTemp.getFullYear (), nowTemp.getMonth (), nowTemp.getDate (), 0, 0, 0, 0 ),
					//	        dateOptions = {
					//		        startDate  : now,
					//		        defaultDate: now,
					//		        minDate    : now,
					//		        dateFormat : "dd/mm/yy",
					//		        firstDay   : 1
					//	        };
					//
					//	    $startDate = $ ( "#startDateField" ).datepicker ( dateOptions );
					//	    $endDate   = $ ( "#endDateField" ).datepicker ( dateOptions );
					//
					//	    $startDate.on (
					//		    "change", function ()
					//		    {
					//
					//			    var $el  = $ ( this ).datepicker ( "hide" ),
					//			        date = $el.datepicker ( "getDate" );
					//
					//			    $endDate.datepicker (
					//				    "option", {
					//					    "minDate"    : date,
					//					    "defaultDate": date,
					//					    "startDate"  : date
					//				    }
					//			    );
					//
					//			    if ( $endDate.val ().length === 0 ) {
					//
					//				    $endDate.focus ();
					//
					//			    }
					//
					//		    }
					//	    );
					//	    // needed to prevent datepicker from disappearing immediately after focus for some reason...
					//	    $startDate.on (
					//		    "focus", function ()
					//		    {
					//
					//			    var $el = $ ( this );
					//
					//			    setTimeout (
					//				    function ()
					//				    {
					//
					//					    $el.datepicker ( "show" );
					//
					//				    }, 15
					//			    );
					//
					//		    }
					//	    );
					//
					//	    $endDate.on (
					//		    "change", function ()
					//		    {
					//
					//			    $ ( this ).datepicker ( "hide" );
					//
					//		    }
					//	    );
					//	    // needed to prevent datepicker from disappearing immediately after focus for some reason...
					//	    $endDate.on (
					//		    "focus", function ()
					//		    {
					//
					//			    var $el = $ ( this );
					//
					//			    setTimeout (
					//				    function ()
					//				    {
					//
					//					    $el.datepicker ( "show" );
					//
					//				    }, 15
					//			    );
					//
					//		    }
					//	    );
					//
					//	    $startDateTime = $ ( "#startDateTimeField" ).timepicker ( timepickerDefaults )
					//		    .on (
					//		    "changeTime", function ()
					//		    {
					//
					//			    $endDateTime.timepicker (
					//				    "option", {
					//					    "minTime"     : $ ( this ).val (),
					//					    "showDuration": false
					//				    }
					//			    );
					//
					//		    }
					//	    );
					//
					//	    $endDateTime = $ ( "#endDateTimeField" ).timepicker ( timepickerDefaults );
					//
					//	    /* WEEKDAYS */
					//
					//	    // keep track of how many checkboxes are ticked
					//	    var sum                   = 0,
					//	        // change checkbox state for one $ element
					//	        toggleCheckboxElement = function ( $el )
					//	        {
					//
					//		        $el.prop (
					//			        "checked", !(
					//				        $el.is ( ":checked" )
					//			        )
					//		        );
					//
					//	        },
					//	        countCheckedElements  = function ()
					//	        {
					//
					//		        var count = 0;
					//
					//		        $ ( "#discover-setup-form div.checkbox-wrapper input[name='days[]']" ).each (
					//			        function ()
					//			        {
					//
					//				        if ($ ( this ).is ( ":checked" )) {
					//
					//					        count++;
					//
					//				        } else {
					//
					//					        count--;
					//
					//				        }
					//
					//			        }
					//		        );
					//
					//		        return count;
					//
					//	        };
					//
					//	    $ ( "#discover-setup-form div.checkbox-wrapper.everyday, #discover-setup-form div.checkbox-wrapper.everyday input" ).click (
					//		    function ( evt )
					//		    {
					//
					//			    var $el = $ ( evt.target );
					//
					//			    if ( !$el.is ( "input" ) ) {
					//
					//				    $el = $el.children ( "input" );
					//				    toggleCheckboxElement ( $el );
					//
					//			    }
					//
					//			    $ ( "#discover-setup-form input[name='days[]']" )
					//				    .each (
					//				    function ()
					//				    {
					//
					//					    $ ( this ).prop ( "checked", $el.is ( ":checked" ) );
					//
					//				    }
					//			    );
					//
					//			    sum = $el.is ( ":checked" ) ? 7 : 0;
					//
					//		    }
					//	    );
					//
					//	    $ ( "#discover-setup-form div.checkbox-wrapper, #discover-setup-form div.checkbox-wrapper input[name='days[]']" ).not ( ".everyday" ).click (
					//		    function ( evt )
					//		    {
					//
					//			    var $el = $ ( evt.target );
					//
					//			    if ( !$el.is ( "input" ) ) {
					//
					//				    $el = $el.children ( "input" );
					//				    toggleCheckboxElement ( $el );
					//
					//			    }
					//
					//			    sum = countCheckedElements ();
					//
					//			    $ ( "input#day-all" ).prop (
					//				    "checked", (
					//				    sum === 7
					//				    )
					//			    );
					//
					//		    }
					//	    );
					//
					//	    if ( callback && typeof callback === "function" ) {
					//		    callback ();
					//	    }
					//
					//    };

					var setDiscoverFormData,
					    /* MAP SETUP */
					    radiusField                = $ ( "#radius" ),
					    ophemeHandle,
					    mapContainer               = $ ( "#gmap_canvas" ),
					    mapHandle,
					    coordsDefault,
					    centreMarker, centreCircle = null,
					    coordsDefaultCambs         = {
						    lat: 52.225550717210936,
						    lng: 0.1366367567297857
					    },
					    drawCircle                 = function ( position, radius )
					    {

						    radius *= 1637; // convert to metres if in Miles

						    if ( centreCircle === null ) {
							    centreCircle = new google.maps.Circle (
								    {
									    center       : position,
									    radius       : radius,
									    strokeColor  : "#3498db",
									    strokeOpacity: 1,
									    strokeWeight : 2,
									    fillColor    : "#3498db",
									    fillOpacity  : 0.15,
									    map          : mapHandle
								    }
							    );
						    }
						    else {
							    centreCircle.setCenter ( position );
							    centreCircle.setRadius ( radius );
						    }

						    centreMarker.setPosition ( position );

						    $ ( "#centre_lat" ).val ( centreMarker.getPosition ().lat () );
						    $ ( "#centre_lng" ).val ( centreMarker.getPosition ().lng () );

					    },
					    getLatLngFromAddress       = function ( location, callback )
					    {

						    var result   = coordsDefaultCambs,
						        geocoder = new google.maps.Geocoder ();

						    if ( DEBUG ) {
							    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Starting Geocode request." ] );
						    }

						    geocoder.geocode (
							    {
								    address: location
							    }, function ( results, status )
							    {

								    if ( DEBUG ) {
									    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Geocode request has finished. Data: ", results, status ] );
								    }

								    if ( status === google.maps.GeocoderStatus.OK ) {

									    if ( DEBUG ) {
										    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Geocode request was completed successfully." ] );
									    }

									    if ( results[ 0 ] ) {

										    if ( DEBUG ) {
											    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Geocode results are in! Results: ", results ] );
										    }

										    result.lat = results[ 0 ].geometry.location.lat ();
										    result.lng = results[ 0 ].geometry.location.lng ();

									    }
									    else {

										    if ( DEBUG ) {
											    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Geocode results were empty, using default coordinates." ] );
										    }

									    }

								    }
								    else {

									    if ( DEBUG ) {
										    app.logThis ( [ "DiscoverSetupView.viewSetup: getLatLngFromAddress: Geocode request failed, using default coordinates. Info: ", status ] );
									    }

								    }

								    if ( callback ) {
									    callback ( result );
								    }

							    }
						    );

					    },
					    getLatLngFromUserLocation  = function ( evt )
					    {

						    app.eventPreventDefault ( evt );

						    var $el = $ ( this ).find ( "i" );

						    if ( $el.length === 0 ) { // feature not available
							    return false;
						    }

						    if ( DEBUG ) {
							    app.logThis ( [ "DiscoverSetupView.viewSetup.getLatLngFromUserLocation: Attempting to get User's current location coordinates." ] );
						    }

						    if ( navigator.geolocation ) {

							    if ( DEBUG ) {
								    app.logThis ( [ "DiscoverSetupView.viewSetup.getLatLngFromUserLocation: Browser has geolocation support." ] );
							    }

							    if ( !app.startActivity ( "DiscoverSetupView.viewSetup.getLatLngFromUserLocation", $el, false, true ) ) {

								    if ( DEBUG ) {
									    app.logThis ( [ "DiscoverSetupView.viewSetup.getLatLngFromUserLocation: Action already in progress, skipping request ... " ] );
								    }

								    return false;

							    }

							    $el.hide (); // hide the button
							    $el.next ().attr ( "style", "margin: 0px" ); // style the spinner

							    //get coordinates
							    navigator.geolocation.getCurrentPosition (
								    function ( position )
								    {

									    if ( DEBUG ) {
										    app.logThis ( [ "DiscoverSetupView.viewSetup.getLatLngFromUserLocation: Position acquired: ", position ] );
									    }

									    //trigger resize
									    google.maps.event.trigger ( mapHandle, "resize" );

									    //google coords object
									    var gc = new google.maps.LatLng ( position.coords.latitude, position.coords.longitude );

									    drawCircle ( gc, radiusField.val () );
									    mapHandle.panTo (
										    /**
										     * @type google.maps.LatLng
										     */
										    centreMarker.getPosition ()
									    );

									    $el.show (); // show the button once more

									    app.stopActivity ( "DiscoverSetupView.viewSetup.getLatLngFromUserLocation" );

								    }
							    );

						    }
						    else {

							    if ( DEBUG ) {
								    app.logThis ( [ "DiscoverSetupView.viewSetup.getLatLngFromUserLocation: Browser does NOT have geolocation support." ] );
							    }

						    }

					    };

					// prevent form submission from this field, and instead make it intuitive for the user
					$ ( "#googleLocationSearch" ).keypress (
						function ( evt )
						{

							if ( evt.keyCode === 13 ) {

								app.eventPreventDefault ( evt );

								// activate the click_findAddressCoords click event
								$ ( "#click_findAddressCoords" ).click ();

								return false;

							}

						}
					);

					// handle address lookup
					$ ( "#click_findAddressCoords" ).click (
						function ()
						{

							var $el     = $ ( "#googleLocationSearch" ),
							    address = $el.val ();

							if ( !address.length || !$el.parsley ().isValid () ) {
								return false;
							}

							getLatLngFromAddress (
								address, function ( coords )
								{

									drawCircle ( coords, radiusField.val () );
									mapHandle.panTo (
										/**
										 * @type google.maps.LatLng
										 */
										centreMarker.getPosition ()
									);

								}
							);

						}
					);

					// if geolocation is not available
					if ( !navigator.geolocation ) {

						// remove the get user location button
						$ ( "#click_getClientCoords" ).remove ();

					}
					else { // otherwise

						// handle the button click
						$ ( "#click_getClientCoords" ).click ( getLatLngFromUserLocation );

					}

					/* RANGE SLIDER */
					$ ( "#slider-range-radius" ).slider (
						{
							range: "max",
							min  : 0.1,
							max  : 10,
							step : 0.1,
							value: 5,
							slide: function ( event, ui )
							{

								$ ( "input[name=radius]" ).val ( ui.value );

								drawCircle ( centreMarker.getPosition (), ui.value, event );

							}
						}
					);

					/* CHOSEN DISCOVER DATA FORM PREFILLING */

					setDiscoverFormData = function ( discover )
					{

						$ ( "button[type='submit']" ).text ( "Save" );

						// Discover Name
						$ ( "input[name=name]" ).val ( discover.name );

						// Discover AuthKeys
						var authKeys   = discover.authkeys.data,
						    addAuthKey = function ( smModule, id )
						    {

							    $ ( "select[name='" + smModule + "-handle']" ).select2 ( "val", id );

						    },
						    radius, coords,
						    keywords, addKeyword,
						    i, len,
						    authKey, keyword;
						//showOptionals,
						//processTime;

						if ( authKeys instanceof Array ) {

							len = authKeys.length;

							for ( i = 0; i < len; i++ ) {

								authKey = authKeys[ i ];

								if ( authKey.valid ) {

									addAuthKey ( authKey.social_media_platform_name, authKey.id );

								}

							}

						}
						else {

							addAuthKey ( authKeys.social_media_platform_name, authKeys.id );

						}

						// radius - miles
						radius = discover.radius;
						$ ( "#slider-range-radius" ).slider ( "value", radius );
						radiusField.val ( radius );

						// map location and circle
						coords = new google.maps.LatLng ( discover.latitude, discover.longitude );
						drawCircle ( coords, radius );
						mapHandle.panTo (
							/**
							 * @type google.maps.LatLng
							 */
							centreMarker.getPosition ()
						);

						/* OPTIONALS */

						//var showOptionals = false;
						//
						//if ( discover.timeperiods || discover.end_date || discover.start_date || discover.days || discover.keywords ) {
						//
						//	showOptionals = true;
						//
						//}
						//
						//if ( discover.timeperiods || discover.end_date || discover.start_date ) {
						//
						//	var processTime = function ( time )
						//	{
						//
						//		// extract hour and minute
						//		timeParts = time.split ( ":" );
						//		minute    = parseInt ( timeParts[ 1 ] );
						//		hour      = parseInt ( timeParts[ 0 ] );
						//
						//		// round to nearest quarter
						//		(
						//			minute > 52.5 ? (
						//				++hour === 24 ? (
						//					(
						//						hour = 23
						//					) && (
						//						minute = 45
						//					) ? "" : ""
						//				) : ""
						//			) : ""
						//		);
						//		minute    = (
						//		            (
						//		            (
						//		            minute + 7.5
						//		            ) / 15 | 0
						//		            ) * 15
						//		            ) % 60;
						//
						//		return moment (
						//			{
						//				hour  : hour,
						//				minute: minute
						//			}
						//		).format ( "h:mma" );
						//
						//	};
						//
						//}

						// keywords
						if ( discover.keywords ) {

							//showOptionals = true;

							keywords   = discover.keywords.data;
							addKeyword = function ( keyword )
							{

								$ ( "#filter" ).tagsinput ( "add", keyword );

							};

							if ( keywords instanceof Array && keywords.length ) {

								len = keywords.length;

								for ( i = 0; i < len; i++ ) {

									keyword = keywords[ i ];

									addKeyword ( keyword.keyword );

								}

							}
							else {

								addKeyword ( keywords.keyword );

							}

						}

						// start date
						//if ( discover.start_date ) {
						//
						//	var startDateParts    = discover.start_date.split ( " " ),
						//	    startDateVal      = startDateParts[ 0 ],
						//	    startDateValParts = startDateVal.split ( "-" );
						//
						//	startDateVal = startDateValParts[ 2 ] + "/" + startDateValParts[ 1 ] + "/" + startDateValParts[ 0 ];
						//
						//	$startDate.datepicker (
						//		"option", {
						//			"minDate"    : startDateVal,
						//			"defaultDate": startDateVal,
						//			"startDate"  : startDateVal
						//		}
						//	);
						//	$startDate.datepicker ( "setDate", startDateVal );
						//
						//	$startDateTime.timepicker ( "setTime", processTime ( startDateParts[ 1 ] ) );
						//
						//}
						//
						//// end date
						//if ( discover.end_date ) {
						//
						//	var endDateParts    = discover.end_date.split ( " " ),
						//	    endDateVal      = endDateParts[ 0 ],
						//	    endDateValParts = endDateVal.split ( "-" );
						//
						//	endDateVal = endDateValParts[ 2 ] + "/" + endDateValParts[ 1 ] + "/" + endDateValParts[ 0 ];
						//
						//	$endDate.datepicker (
						//		"option", {
						//			"minDate"    : endDateVal,
						//			"defaultDate": endDateVal,
						//			"startDate"  : endDateVal
						//		}
						//	);
						//	$endDate.datepicker ( "setDate", endDateVal );
						//
						//	$endDateTime.timepicker ( "setTime", processTime ( endDateParts[ 1 ] ) );
						//
						//}
						//
						//// weekdays
						//if ( discover.days ) {
						//
						//	var days   = discover.days.data,
						//	    addDay = function ( day )
						//	    {
						//
						//		    $ ( "#checkbox-group-days input[value='" + day + "']" ).prop ( "checked", true );
						//
						//	    };
						//
						//	if ( days instanceof Array && days.length ) {
						//
						//		if ( days.length === 7 ) {
						//
						//			$ ( "#checkbox-group-days input#day-all" ).click ();
						//
						//		}
						//		else {
						//
						//			days.forEach (
						//				function ( day )
						//				{
						//
						//					addDay ( day );
						//
						//				}
						//			);
						//
						//		}
						//
						//	}
						//	else {
						//
						//		addDay ( days );
						//
						//	}
						//
						//}
						//
						//// time periods
						//if ( discover.timeperiods ) {
						//
						//	var times   = discover.timeperiods.data,
						//	    addTime = function ( index, start, end )
						//	    {
						//
						//		    $ ( "#" + intervalsIdPattern + index + " .startTimeField" ).timepicker ( "setTime", start );
						//		    $ ( "#" + intervalsIdPattern + index + " .endTimeField" ).timepicker ( "setTime", end );
						//
						//	    },
						//	    timeParts, hour, minute, start, end;
						//
						//	if ( times instanceof Array && times.length ) {
						//
						//		times.forEach (
						//			function ( time, index )
						//			{
						//
						//				if ( index ) {
						//
						//					$ ( "button#addInterval" ).click ();
						//
						//				}
						//
						//				start = processTime ( time.start );
						//				end   = processTime ( time.end );
						//
						//				addTime ( index + 1, start, end );
						//
						//			}
						//		);
						//
						//	}
						//	else {
						//
						//		start = processTime ( times.start );
						//		end   = processTime ( times.end );
						//
						//		addTime ( 1, start, end );
						//
						//	}
						//
						//}
						//
						//// if necessary, open up the Optionals container
						//if ( showOptionals ) {
						//
						//	$ ( "#optionToggler" ).click ();
						//
						//	$ ( "html, body" ).stop ( true );
						//
						//}

					};

					/* FINAL INITIATION PROCEDURES */

					// initiate Opheme object
					ophemeHandle = new Opheme ( mapContainer );

					// initiate final view startup
					getLatLngFromAddress (
						app.defaultMapLocation, function ( coords )
						{

							coordsDefault = coords;

							mapHandle = ophemeHandle.map (
								{
									options: {
										//initial map centre location
										map_centre: coordsDefault,
										zoom      : 10
									}
								}
							);

							centreMarker = new google.maps.Marker (
								{
									map     : mapHandle,
									position: coordsDefault
								}
							);

							centreMarker.setVisible ( true );
							centreMarker.setDraggable ( true );

							google.maps.event.addListener (
								centreMarker, "drag", function ( evt )
								{
									drawCircle ( evt.latLng, radiusField.val () );
								}
							);
							google.maps.event.addListener (
								centreMarker, "dragend", function ()
								{
									mapHandle.panTo ( centreMarker.getPosition () );
								}
							);

							// handle map click
							google.maps.event.addListener (
								mapHandle, "click", function ( evt )
								{
									drawCircle ( evt.latLng, radiusField.val () );
									mapHandle.panTo ( evt.latLng );
								}
							);

							// draw initial map circle
							drawCircle ( centreMarker.getPosition (), radiusField.val () );

							// save the handles for later use
							app.maps.setup = {
								oHandle     : ophemeHandle,
								mHandle     : mapHandle,
								centreMarker: centreMarker,
								centreCircle: centreCircle
							};

							// setup optional defaults
							//setupOptionals(function () {

							/* CHOSEN DISCOVER DATA FORM PREFILLING, if any */
							var discoverData = app.session.storage.currentDiscover;
							if ( discoverData ) {
								setDiscoverFormData ( discoverData );
							}

							//});

						}
					);

					/* BOOTSTRAP TOUR CODE */

					app.views.tour = new Tour (
						{
							onStart   : app.views.onTourStart,
							onEnd     : app.views.onTourEnd,
							autoscroll: true,
							steps     : [
								{
									element  : "#discoverSetup .panel-heading",
									placement: "bottom",
									title    : "3ish Steps to Create!",
									content  : "The setup process is made up of just 3 steps, unless you want to be technical about it and consider clicking the “Create” button a step, which case it’s 4."

								},
								{
									element  : "#socialMediaAccountsWrapper",
									placement: "bottom",
									title    : "Step 1: Select Social Media Accounts",
									content  : "We will start off by choosing which Social Media account, of the ones you have already authorised a minute ago, you would like us to use for this Discover. You may only pick one of each, Twitter and Instagram, and either one, the other, or both. Once created, these accounts will be used to fetch new posts from their respective Social Media environments as well as allow you to interact with said posts i.e. reply, favourite, etc. Note: If you would like to use a completely diferent Social Media account, then just click on the “Manage Social Media Accounts” button, which will give you the opportunity to authorise more accounts."
								},
								{
									element  : "#discoverNameWrapper",
									placement: "bottom",
									title    : "Step 2: Discover Name",
									content  : "Next stop: Discover Name! Keeping it sensible is highly recommended, as this will be allow you to identify a particular Discover both at the top in the Discover dropdown as well as in the Discover List. We personally like to name Discovers after the area they are in and/or what it is they focus on i.e. Cambridge Coffee."
								},
								{
									element  : "#discoverLocationWrapper",
									placement: "bottom",
									title    : "Step 3: Discover Location",
									content  : "Now, you also need to choose the location that you would like this Discover to observe. There are three ways of doing this ..."
								},
								{
									element  : "#discoverLocationWrapper",
									placement: "bottom",
									title    : "Current Location",
									content  : "Click the “Current Location” button and allow the Browser to use your current (estimated) location."
								},
								{
									element  : "#discoverLocationWrapper",
									placement: "bottom",
									title    : "Type",
									content  : "Type in the name of a city, post code, pretty much any information that might help us identify the location you are interested in, followed by Return/Enter key. For example, “Cambridge” might point to Cambridge, MA, USA, so if you want the UK one then something like “Cambridge, UK” should do the trick."
								},
								{
									element  : "#discoverLocationWrapper",
									placement: "bottom",
									title    : "The Map",
									content  : "You can also just navigate the map and click anywhere to pick the centre of a location. Then, you are free to click, hold, and drag the map pin to further fine tune your choice."
								},
								{
									element  : "#discoverLocationWrapper",
									placement: "bottom",
									title    : "Filtering",
									content  : "Then, there are two features that will help us narrow down the results so that you will only see exactly what interests you."
								},
								{
									element  : ".opheme_setupMap",
									placement: "bottom",
									title    : "Filtering - Location Radius",
									content  : "Radius Slider - you can click and drag the Slider to pick a radius anywhere between 0.1 (176 yards) and 10 miles that we will use to filter your messages within the chosen area."
								},
								{
									element  : "#filterWrapper",
									placement: "top",
									title    : "Filtering - Words",
									content  : "Search term(s) - this beauty allows you to further refine the results by specifying a word or a series of words you would like the posts we deliver to contain. Each different term should be followed by the Enter/Return key. Let’s have a look at a couple of examples to get you started.<br />- If you would like to see all the posts referring to Coffee in Cambridge, you might type “coffee” in the field, followed by the Enter/Return key.<br />- If you would like to see posts referring specifically to Starbucks Coffee, then a term like “Stabucks Cofee” followed by the Enter/Return key should help."
								},
								{
									element  : "#filterWrapper",
									placement: "top",
									title    : "All Done!",
									content  : "There we go, all done! You can now go ahead and click Next.",
									onNext   : function ()
									{
										$ ( "#discover-setup-form" ).submit ();
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

					if (!window.tour_ended) {

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
