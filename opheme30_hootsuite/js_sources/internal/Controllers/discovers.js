define (
	[
		"underscore",
		"jquery",
		"app",
		"Controllers/base",
		"Views/pages/discovers",
		"Views/pages/discovers-setup"
		//"moment"
	], function ( _,
	              $,
	              app,
	              BaseController,
	              DiscoversView,
	              DiscoversSetupView
	              //moment
	)
	{

		"use strict";

		return BaseController.extend (
			{

				name: "discovers",

				viewNames: [
					{
						action: "view", viewName: "DiscoversView"
					},
					{
						action: "view-setup", viewName: "DiscoversSetupView"
					}
				],

				user_view: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController.user_view is running." ] );
					}

					// reset currentDiscover
					app.session.resetCurrentDiscover();

					var deferred = new $.Deferred ();

					(
						new DiscoversView ()
					).show (
						function ()
						{
							deferred.resolve ();
						}
					);

					return deferred;

				},

				"user_view-setup": function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController.user_view-setup is running." ] );
					}

					var deferred = new $.Deferred ();

					(
						new DiscoversSetupView ()
					).show (
						function ()
						{
							deferred.resolve ();
						}
					);

					return deferred;

				},

				user_remove: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController.user_remove is running." ] );
					}

					this._removeDiscover ();

				},

				_listenBindingsDiscoversView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._listenBindingsDiscoversView is running." ] );
					}

					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doRemoveProcess", this._removeDiscover.bind ( this ) );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doStartProcess", this._startDiscover.bind ( this ) );
					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doStopProcess", this._stopDiscover.bind ( this ) );

				},

				_listenBindingsDiscoversSetupView: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._listenBindingsDiscoversSetupView is running." ] );
					}

					this.dispatcher.listenTo ( app.views.currentView.dispatcher, "doSetupProcess", this._saveDiscover );

				},

				_removeDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._removeDiscover is running." ] );
					}

					var discover = app.session.storage.currentDiscover;

					if ( !discover ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._removeDiscover: No Discover selected, skipping request ..." ] );
						}

						return false;

					}

					if ( !app.startActivity ( "DiscoversController._removeDiscover", $ ( "div.panel-title h3" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._removeDiscover: Activity in progress, skipping request ..." ] );
						}

						return false;

					}

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._removeDiscover: Discover data has been processed: ", discover, "Initiating call ..." ] );
					}

					// initiate call
					app.callAPI (
						{
							method  : "DELETE",
							endpoint: "/discovers/" + discover.id,
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success : function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._removeDiscover.success: Discover removed successfully." ] );
								}

								app.showAlert ( "Discover '" + discover.name + "' has been successfully removed!", "success" );

								app.session.removeDiscoverLocal ( discover.id );

								app.router.navigate (
									"discovers/view", {
										trigger: true,
										replace: false
									}
								);

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._removeDiscover.error: ", error ] );
								}

								app.showAlert ( "Something went wrong! Message: " + error, "error" );

							},
							complete: function ()
							{

								app.stopActivity ( "DiscoversController._removeDiscover" );

							}
						}
					);

				},

				_startDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._startDiscover is running." ] );
					}

					this._changeDiscoverStatus ( true );

				},

				_stopDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._stopDiscover is running." ] );
					}

					this._changeDiscoverStatus ( false );

				},

				_changeDiscoverStatus: function ( status )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._changeDiscoverStatus is running. Status: ", status ] );
					}

					var discover = app.session.storage.currentDiscover,
					    data;

					if ( !discover || status === undefined ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._changeDiscoverStatus: No Discover selected or No Status given, skipping request ..." ] );
						}

						return false;

					}

					if ( !app.startActivity ( "DiscoversController._changeDiscoverStatus", $ ( "div.panel-title h3" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._changeDiscoverStatus: Activity in progress, skipping request ..." ] );
						}

						return false;

					}

					data = {
						json_data: {
							data: {
								running: status
							}
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._changeDiscoverStatus: Discover data has been processed: ", data, "Initiating call ..." ] );
					}

					// initiate call
					app.callAPI (
						{
							method  : "PATCH",
							endpoint: "/discovers/" + discover.id,
							data    : data,
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success: function ()
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._changeDiscoverStatus.success: Discover processed successfully." ] );
								}

								discover.running = status;

								var word;

								if ( status ) {

									//$ ( "li[data-id='" + discover.id + "'] i.fa-pause" ).removeClass ( "hidden" );
									//$ ( "li[data-id='" + discover.id + "'] i.fa-play" ).addClass ( "hidden" );

									word = "resumed";

								}
								else {

									//$ ( "li[data-id='" + discover.id + "'] i.fa-play" ).removeClass ( "hidden" );
									//$ ( "li[data-id='" + discover.id + "'] i.fa-pause" ).addClass ( "hidden" );

									word = "paused";

								}

								app.showAlert ( "Discover '" + discover.name + "' has been successfully " + word + "!", "success" );

								app.events.trigger ( "doViewRefresh" );

							},
							error  : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._changeDiscoverStatus.error: ", error ] );
								}

								app.showAlert ( "Something went wrong! Message: " + error, "error" );

								app.stopActivity ( "DiscoversController._changeDiscoverStatus" );

							}
							//complete: function ()
							//{
							//
							//	app.stopActivity ( "DiscoversController._changeDiscoverStatus" );
							//
							//}
						}
					);

				},

				_saveDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._saveDiscover is running." ] );
					}

					if ( !app.startActivity ( "DiscoversController._saveDiscover", $ ( "div.panel-title h3" ) ) ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._saveDiscover: Activity in progress, skipping request ..." ] );
						}

						return false;

					}

					var $discoverFooter = $ ( "#discoverFooter" ),
					    $el, // reusable container
					    stopThis        = function ()
					    {

						    app.stopActivity ( "DiscoversController._saveDiscover" );
						    $discoverFooter.find ( "button, a" ).removeAttr ( "disabled" );

						    return false;
					    },
					    discover, mapData,
					    id,
					    operation, word,
					    authkeys,
					    keywords, kw,
					    data,
					    i, len1, smModule,
					    j, len2, el,
					    $container, keySet;

					$discoverFooter.find ( "button, a" ).attr ( "disabled", "disabled" );
					app.scrollBodyToTopOf ( $ ( "body" ) );

					if ( !app.session.storage.currentDiscover ) {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._saveDiscover: No Discover selected, initiating create." ] );
						}

						discover = {};

						operation = "POST";
						word      = "created";

					}
					else {

						if ( DEBUG ) {
							app.logThis ( [ "DiscoversController._saveDiscover: Discover selected, initiating update. Discover: ", discover ] );
						}

						discover = _.extend ( {}, app.session.storage.currentDiscover );

						operation = "PUT";
						word      = "updated";
						id        = discover.id;
						mapData   = discover.mapData || null;

						delete discover.mapData;
						delete discover.links;
						delete discover.created_at;
						delete discover.id;
						delete discover.start_date;
						delete discover.end_date;
						delete discover.days;
						delete discover.timeperiods;

					}

					// authkeys
					authkeys = [];

					len1 = app.config.smModules.length;

					for ( i = 0; i < len1; i++ ) {

						smModule = app.config.smModules[ i ];

						$container = $ ( "select[name='" + smModule + "-handle']" );

						if ( $container.length ) {

							keySet = $container.select2 ( "val" );

							if ( keySet && keySet.length ) {

								if ( keySet instanceof Array ) {

									len2 = keySet.length;

									for ( j = 0; j < len2; j++ ) {

										el = keySet[ j ];
										if ( el !== "#" ) {

											authkeys.push (
												{
													id: el
												}
											);

										}

									}

								}
								else {

									if ( keySet !== "#" ) {

										authkeys.push (
											{
												id: keySet
											}
										);

									}

								}

							}

						}

					}

					if ( !authkeys.length ) {

						app.showAlert ( "You must select at least one Social Media Account.", "error" );

						$el = $ ( "select[name$='-handle']" ).has ( "option[value='#']:selected" ).first ();

						app.scrollBodyToTopOf ( $el.parent () );

						return stopThis ();

					}

					discover.authkeys = {
						data: authkeys
					};

					// name
					$el           = $ ( "input[name='name']" );
					discover.name = $el.val ();

					if ( !discover.name ) {

						app.showAlert ( "You must give this Discover a name.", "error" );

						$el.focus ();

						app.scrollBodyToTopOf ( $el.parent () );

						return stopThis ();

					}

					// latitude and logitude
					discover.latitude  = parseFloat ( $ ( "#centre_lat" ).val () );
					discover.longitude = parseFloat ( $ ( "#centre_lng" ).val () );

					if ( isNaN ( discover.latitude ) || isNaN ( discover.longitude ) ) {

						app.showAlert ( "You must choose a centre location for this Discover.", "error" );

						$el = $ ( "#googleLocationSearch" ).focus ();

						app.scrollBodyToTopOf ( $el.parent () );

						return stopThis ();

					}

					// radius
					$el             = $ ( "#radius" );
					discover.radius = parseFloat ( $el.val () );

					if ( isNaN ( discover.radius ) ) {

						app.showAlert ( "You must select a radius for this Discover.", "error" );

						$el.focus ();

						app.scrollBodyToTopOf ( $el.parent () );

						return stopThis ();

					}

					// keywords
					keywords = $ ( "#filter" ).tagsinput ( "items" );
					kw = [];

					if ( keywords.length ) {

						len1 = keywords.length;

						for ( i = 0; i < len1; i++ ) {

							el = keywords[ i ];

							kw.push (
								{
									keyword: el
								}
							);

						}

					}
					//else {

						//delete discover.keywords;

					//}

					discover.keywords = {
						data: kw
					};

					// helpers
					//var dateField, timeField;
					//
					//// start date - UTC
					//dateField = $ ( "#startDateField" ).datepicker ( "getDate" );
					//timeField = $ ( "#startDateTimeField" ).timepicker ( "getTime" );
					//
					//if ( dateField && timeField ) {
					//
					//	var dateMoment = moment ( dateField ).utc (),
					//	    timeMoment = moment ( timeField ),
					//	    dateTime   =
					//		    dateMoment.format ( "DD/MM/YYYY" ) +
					//		    " " +
					//		    timeMoment.format ( "HH:mm:ss" );
					//
					//	discover.start_date = dateTime;
					//
					//}
					//else {
					//
					//	delete discover.start_date;
					//
					//}
					//
					//// end date - UTC
					//dateField = $ ( "#endDateField" ).datepicker ( "getDate" );
					//timeField = $ ( "#endDateTimeField" ).timepicker ( "getTime" );
					//
					//if ( dateField && timeField ) {
					//
					//	var dateMoment = moment ( dateField ).utc (),
					//	    timeMoment = moment ( timeField ),
					//	    dateTime   =
					//		    dateMoment.format ( "DD/MM/YYYY" ) +
					//		    " " +
					//		    timeMoment.format ( "HH:mm:ss" );
					//
					//	discover.end_date = dateTime;
					//
					//	if ( !discover.start_date ) { // now
					//
					//		discover.start_date = moment ().utc ().format ( "DD/MM/YYYY HH:mm:00" );
					//
					//	}
					//
					//}
					//else {
					//
					//	delete discover.end_date;
					//
					//}
					//
					//// days
					//var days = [];
					//
					//$ ( "input[type='checkbox'][name='days[]']:checked" ).each (
					//	function ()
					//	{
					//
					//		days.push (
					//			{
					//				day: $ ( this ).val ()
					//			}
					//		);
					//
					//	}
					//);
					//
					//if ( days.length ) {
					//
					//	discover.days = {
					//		data: days
					//	}
					//
					//}
					//else {
					//
					//	delete discover.days;
					//
					//}
					//
					//// time periods
					//var timeperiods = [],
					//    stop        = false,
					//    $lastElem;
					//
					//$ ( "#timeIntervals .time-interval" ).each (
					//	function ()
					//	{
					//
					//		if ( stop ) {
					//			return false;
					//		}
					//
					//		var startTime, endTime;
					//
					//		$el = $ ( this );
					//
					//		startTime = $el.find ( ".startTimeField" ).timepicker ( "getTime" );
					//		endTime   = $el.find ( ".endTimeField" ).timepicker ( "getTime" );
					//
					//		if ( startTime && !endTime ) {
					//
					//			$lastElem = $el.find ( ".startTimeField" );
					//
					//			stop = true;
					//
					//			return false;
					//
					//		}
					//
					//		if ( startTime && endTime ) {
					//
					//			timeperiods.push (
					//				{
					//					start: moment ( startTime ).format ( "HH:mm:ss" ),
					//					end  : moment ( endTime ).format ( "HH:mm:ss" )
					//				}
					//			);
					//
					//		}
					//
					//	}
					//);
					//
					//if ( stop ) {
					//
					//	app.showAlert ( "It seems that you have picked a Start Time for this Discover, but have not chosen a Stop Time complement. Please re-check the form.", "error" );
					//
					//	$lastElem.focus ();
					//
					//	app.scrollBodyToTopOf ( $lastElem.parent () );
					//
					//	return stopThis ();
					//
					//}
					//
					//if ( timeperiods.length ) {
					//
					//	discover.timeperiods = {
					//		data: timeperiods
					//	};
					//
					//}
					//else {
					//
					//	delete discover.timeperiods;
					//
					//}

					// API data
					data = {
						json_data: {
							data: discover
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversController._saveDiscover: Discover data has been processed: ", discover, "Initiating call ..." ] );
					}

					// initiate call
					app.callAPI (
						{
							method  : operation,
							endpoint: "/discovers" + (
								operation === "PUT" ? "/" + id : ""
							),
							data    : data,
							headers : {
								"Authorization": app.session.storage.apiInfo.get ( "access_token" )
							}
						}, {
							success : function ( discoverData )
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._saveDiscover.success: Discover processed successfully." ] );
								}

								app.showAlert ( "Discover '" + discoverData.name + "' has been successfully " + word + "!", "success" );

								if ( operation === "POST" ) {

									app.session.addDiscoverLocal ( discoverData );

								}
								else {

									if ( mapData ) { discoverData.mapData = mapData; }

									app.session.changeDiscoverLocal ( id, discoverData );

								}

								app.session.storage.currentDiscover = discoverData;

								app.router.navigate (
									"main-stream/view", {
										trigger: true,
										replace: true
									}
								);

							},
							error   : function ( error )
							{

								if ( DEBUG ) {
									app.logThis ( [ "DiscoversController._saveDiscover.error: ", error ] );
								}

								app.showAlert ( "Something went wrong! Message: " + error, "error" );

							},
							complete: function ()
							{

								stopThis ();

							}
						}
					);

				}

			}
		);

	}
);
