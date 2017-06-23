define (
	[
		"jquery",
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/overviews/_stream-header.html",
		"textplugin!/templates/page_elements/overviews/_stream.html",
		"textplugin!/templates/pages_full/main-stream.html",
		"textplugin!/templates/page_elements/overviews/_stream-message.html",
		"opheme"
	], function ( $,
	              _,
	              app,
	              BaseView,
	              StreamHeaderOverviewElementTpl,
	              StreamOverviewElementTpl,
	              MainStreamPageTpl,
	              StreamMessageTpl,
	              Opheme )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "MainStreamView",

				viewPublicName: "Messages Stream",

				viewDefaultLocation: "main-stream/view",

				controllerName: "main-stream",

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamView.initialize is running" ] );
					}

					return this;

				},

				events: {},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamView.render is running." ] );
					}

					var subTemplates = [], data;

					this.template = _.template ( MainStreamPageTpl );

					subTemplates.push (
						{
							"renderMap": _.template ( StreamHeaderOverviewElementTpl )
						}
					);
					subTemplates.push (
						{
							"renderStream": _.template ( StreamOverviewElementTpl )
						}
					);

					data = {};

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamView.render: Data: ", data ] );
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

				destructor: function ()
				{

					// stop job
					app.maps.stream.ophemeHandle.stopJob ();

					// stop interactions
					app.maps.stream.ophemeHandle.stopInteractions ();

					// delete handles
					delete app.maps.stream.ophemeHandle;

					(
						new BaseView ()
					).destructor.apply ( this );

				},

				viewSetup: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "MainStreamView.viewSetup is running. Discover: ", app.session.storage.currentDiscover ] );
					}

					var discover                         = app.session.storage.currentDiscover,
					    mapOptions                       = {
						    zoom      : 10,
						    map_centre: {
							    lat: discover.latitude,
							    lng: discover.longitude
						    }
					    },
					    ophemeHandle, smHandles, $anchors,

					    /* STREAM CONTAINER SETUP */

					    tabContentHeight                 = 0, // window.height - navigation.height - map.height
					    streamContainerTop               = 0, // navigation.height
					    streamContainerHeight            = 0, // window.height - navigation.height
					    animationHeight                  = 90, // pixels - map animation - css-top value
					    $streamContainer                 = $ ( "#stream-container" ),
					    $messagesContainersComboSelector = $ ( "#messagesWrapper, #messages, #interactions" ),
					    $messages                        = $ ( "#messages" ),
					    //$messagesWrapper                 = $ ( "#messagesWrapper" ),
					    $loadMoreLink                    = $ ( "#load-more" ),
					    $loadMoreLinkMap                 = $ ( "#load-more-map" ),
					    $mapParentContainer              = $ ( ".embed" ),
					    //loadMoreElementHeight            = $loadMoreLink.outerHeight (),
					    $navigationContainer             = $ ( "#navigation-container" ),
					    $topMap                          = $ ( ".opheme_topMap" ),
					    windowHeight                     = 0,
					    setElementsSizes                 = function ( e )
					    {

						    windowHeight          = $ ( window ).height ();
						    streamContainerTop    = $navigationContainer.height ();
						    streamContainerHeight = windowHeight - $navigationContainer.height ();
						    tabContentHeight      = windowHeight - $navigationContainer.height () - $topMap.height () + 14;

						    $messagesContainersComboSelector.css ( "height", tabContentHeight + "px" );
						    $streamContainer.css ( "width", $ ( window ).width () + "px" );
						    $streamContainer.css ( "height", streamContainerHeight + "px" );

						    //$messages.css("height", tabContentHeight - loadMoreElementHeight);

						    if ( e !== undefined ) {

							    ophemeHandle.doRebuildMessagesList ( ophemeHandle.getCurrentListView (), true, null, true );

						    }

					    },
					    i, len, smHandle, smType;

					/* STREAM CONTAINER AND CONTAINING ELEMENTS' SIZES */

					setElementsSizes ();

					// on window resize, reset the sizes
					$ ( window ).resize ( setElementsSizes );

					// setup the bottom load more button
					$messages.scroll (
						function ( e )
						{

							var $elem = $ ( e.currentTarget );

							// only operate the button if current list view is messages
							if ( ophemeHandle.getCurrentListView () !== "messages" ) {

								if ( $loadMoreLink.is ( ":visible" ) ) {

									$loadMoreLink.slideUp ( "fast" );

								}

								return;

							}

							// show when scrolled to bottom
							if ( $elem[ 0 ].scrollHeight - $elem.scrollTop () === $elem.height () ) {

								$loadMoreLink.slideDown ( "fast", function () {

									$loadMoreLink.find("a").click();

								} );

								return;

							}

							// hide otherwise if visible
							if ( $loadMoreLink.is ( ":visible" ) ) {

								$loadMoreLink.slideUp ( "fast" );

								return;

							}
						}
					);

					// detect when mouse is over map or load more button on map
					$mapParentContainer.on (
						"mouseenter",
						function ()
						{

							$loadMoreLinkMap.slideDown ( "fast" );

						}
					);

					$mapParentContainer.on (
						"mouseleave",
						function ()
						{

							$loadMoreLinkMap.slideUp ( "fast" );

						}
					);

					/* TOP MAP SETUP */

					$topMap.on (
						"click", ".map_handle.map-minimise", function ()
						{

							$streamContainer.animate (
								{
									top   : "-" + animationHeight + "px",
									height: (
									        streamContainerHeight + animationHeight + streamContainerTop
									        ) + "px"
								}, 1000, "easeOutExpo"
							);

							$messagesContainersComboSelector.animate (
								{
									height: (
									        streamContainerTop + tabContentHeight + animationHeight
									        ) + "px"
								}, 1000, "easeOutExpo"
							);

							$ ( this ).text ( "Click to maximise map" ).toggleClass ( "map-maximise map-minimise" );

						}
					);

					$topMap.on (
						"click", ".map_handle.map-maximise", function ()
						{

							$streamContainer.animate (
								{
									top   : streamContainerTop + "px",
									height: streamContainerHeight + "px"
								}, 1000, "easeOutExpo"
							);

							$messagesContainersComboSelector.animate (
								{
									height: tabContentHeight + "px"
								}, 1000, "easeOutExpo"
							);

							$ ( this ).text ( "Click to minimise map" ).toggleClass ( "map-maximise map-minimise" );

						}
					);

					/* MESSAGES SETUP */

					smHandles = {
						twitter  : {},
						instagram: {}
					};

					len = discover.authkeys.data.length;

					for ( i = 0; i < len; i++ ) {

						/**
						 * @type {{ social_media_platform_name: string }}
						 */
						smHandle = discover.authkeys.data.slice ( i, i + 1 )[ 0 ];

						smType = smHandle.social_media_platform_name;

						smHandles[ smType ]             = smHandle;
						smHandles[ smType ].profile_url = "https://www." + smType + ".com/" + smHandles[ smType ].screen_name;

					}

					/* Stream Toggles setup */

					$ ( ".streamToggles" ).click (
						function ( evt, extraData )
						{

							if ( DEBUG ) {
								app.logThis ( [ "MainStreamView.viewSetup.streamTogglesClick is running. Data: ", evt, extraData ] );
							}

							app.eventPreventDefault ( evt );

							//var $target = $(evt.target);

							// if user clicks on the icon, get the parent anchor
							//if ($target.hasClass("fa")) {
							//
							//	$target = $target.parent();
							//
							//}

							//console.error(evt.target, "#" + ophemeHandle.getCurrentListView (), $target.attr("href"));

							// user clicked to change list view, don't run the things below
							//if ("#" + ophemeHandle.getCurrentListView () !== $target.attr("href")) { return; }

							// do not do anything else here if a list change is requested ie. from messages to interactions
							if ( extraData !== undefined && !!extraData.switchRequested ) { return; }

							var listItem = app.maps.stream.listView.find ( ".hs_message.hs_isNewMessage" )[ 0 ];

							if ( listItem !== undefined ) {

								// scroll to new message
								app.scrollElementToTopOf ( $messages, listItem.$el, null, 1, true, 0, true );

								// trigger scroll event just in case
								$messages.scroll ();

							}
							else {

								// scroll to top
								app.scrollElementToTopOf ( $messages, 0, null, 1, true, 0, true );

							}

						}
					);

					/* Opheme setup */

					ophemeHandle = new Opheme (
						$ ( "#gmap_canvas" ), {
							smHandles: smHandles,
							template : StreamMessageTpl
						}
					);

					ophemeHandle.map (
						{
							options: mapOptions
						}
					);

					// save handle for future use
					app.maps.stream.ophemeHandle = ophemeHandle;

					/* INTERACTIONS SETUP */

					$anchors = $streamContainer.find ( "a" );

					$anchors.filter ( "[href='#interactions']" ).click (
						function ( /*evt*/ )
						{

							//app.eventPreventDefault ( evt );

							$ ( this ).parent ().prev ().removeClass ( "active" );
							$ ( this ).parent ().addClass ( "active" );

							// unhighlight everything when map is clicked
							//ophemeHandle.map_highlightMarkerMessage ( null );

							ophemeHandle.doRebuildMessagesList ( "interactions" );

						}
					);

					$anchors.filter ( "[href='#messages']" ).click (
						function ( /*evt*/ )
						{

							//app.eventPreventDefault ( evt );

							$ ( this ).parent ().next ().removeClass ( "active" );
							$ ( this ).parent ().addClass ( "active" );

							ophemeHandle.doRebuildMessagesList ( "messages" );

						}
					);

					/* Start working */

					// block the UI
					//$streamContainer.block (
					//	_.extend (
					//		{}, app.config.blockUIDefaults, {
					//			onBlock: function ()
					//			{

					// start the job
					ophemeHandle.startJob ();

					//			}
					//		}
					//	)
					//);

					/* BOOTSTRAP TOUR CODE */

					app.views.tour = new Tour (
						{
							onStart   : app.views.onTourStart,
							onEnd     : app.views.onTourEnd,
							autoscroll: true,
							steps     : [
								{
									orphan : true,
									title  : "You have created new Discover!",
									content: "Well done, you have just created your first Discover! Now, before you can start looking through all of the relevant Tweets and Instagram Posts, just bear with us a few moments while we search the Social Media providers for live data. Meanwhile, we have a few neat features we would like to share with you about this particular stream (let’s call it the Main Discover Stream)."
								},
								{
									orphan : true,
									title  : "Discover Stream",
									content: "The stream, as you can see, is made up of the map, posts, and replies. The map displays the Discover posts in a graphical manner (colourful pins, let’s call them Post Pins), while the posts and replies tabs show you detailed information as text (let’s call them Post Bodies) and offers you tools you can use to interact with your (potential) clients. So, let’s go more in depth through each one."
								},
								{
									element  : ".embed",
									placement: "bottom",
									title    : "Map",
									content  : "The Map displays all the Discover posts according to their real world location as colourful pins, each colour and shape providing you with different type of information about each post."
								},
								{
									element  : ".embed",
									placement: "bottom",
									title    : "Map - continued",
									content  : "Clicking on a Pin will focus the bit below the map on the Post Body associated with the Post Pin you just clicked. Cool, we know! You can also use the zoom-in/out controls on the left or switch to satellite view by clicking the top right corner dropdown."
								},
								{
									element  : ".embed",
									placement: "bottom",
									title    : "Map - continued",
									content  : "The colours and shapes of the Post Pins offer you the following information: Pin shape indicates whether it’s a Tweet <i class='fa fa-lg fa-twitter'></i> or Instagram <i class='fa fa-lg fa-instagram'></i> Post; Pin colour indicates the overall textual sentiment of the Post – Positive, Neutral, N/A (Instagram Posts sometimes do not have any accompanying text), or Negative."
								},
								{
									element  : ".embed",
									placement: "bottom",
									title    : "Map - continued",
									content  : "Also, if at any point there are older Posts available not loaded into the Posts Tab, mousing over the Map as well as scrolling to the bottom of the Posts will reveal a 'Load Older Messages' button that will do just that when clicked - put older messages on the Map as well as into the Posts stream."
								},
								{
									element  : ".map_handle",
									placement: "bottom",
									title    : "Map - continued",
									content  : "If you would like to focus on reading through the Posts, then clicking on the blue button just below the map will minimise it, giving you more reading space."
								},
								{
									element  : ".streamToggles",
									placement: "bottom",
									title    : "Posts Tab",
									content  : "Here you can see textual versions of the Tweets and Instagram Posts visible on the Map. Hovering over any of them reveals the Interaction Menu made up of three (four for Twitter Posts) buttons: Follow <i class='fa fa-lg fa-twitter'></i> (the author of the Post), Reply <i class='fa fa-lg fa-reply'></i> (sent directly to the author, so use responsibly!), Favourite/Like <i class='fa fa-lg fa-thumbs-up'></i>, and Retweet <i class='fa fa-lg fa-retweet'></i> (only for Twitter Posts)."
								},
								{
									element  : ".streamToggles",
									placement: "bottom",
									title    : "Posts Tab - continued",
									content  : "Replies sent and received (from the Post authors) are fully visible only in the Replies tab – the Posts tab only shows you a count of the total number of replies <i class='fa fa-bell-o'></i> linked to each Post, a Conversation summary if you will."
								},
								{
									element  : ".streamToggles",
									placement: "bottom",
									title    : "Replies Tab",
									content  : "This is very similar to the Posts tab, except it only shows you Posts involved in conversations between you and Post authors as opposed to all Discover Posts."
								},
								{
									orphan : true,
									title  : "Done!",
									content: "That’s it, you now have all the information you need to expertly use our app! And, if you have any questions or not sure what a button does, you can either take this Tour again by clicking on the 'Take the Tour' option from the 'More' <i class='fa fa-lg fa-ellipsis-v'></i> dropdown menu or get in touch with us directly at support@opheme.com. Happy discovering!"
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

					// set the stream title to current discover name - hootsuite has a limit of 35 characters for the stream title
					//hsp.updatePlacementSubtitle ( discover.name.substring ( 0, 35 ) );

				}

			}
		);

	}
)
;
