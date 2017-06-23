define (
	[
		"app",
		"jquery"
	], function ( app, $ )
	{

		"use strict";

		hsp.init (
			{
				apiKey      : app.config.hootsuite.apiKey,
				receiverPath: app.config.hootsuite.receiverPath
			}
		);

		hsp.bind (
			"refresh", function ()
			{

				if ( DEBUG ) {
					app.logThis ( [ "Hootsuite.refresh: The Hootsuite Dashboard initiated a refresh." ] );
				}

				var activity                    = app.anyActivityRunning (),
				    $mainStreamViewLoadMoreLink = $ ( "#load-more" );

				if ( !!activity ) {

					if ( DEBUG ) {
						app.logThis ( [ "Hootsuite.refresh: Event skipped due to an Activity (" + activity + ") being in progress ..." ] );
					}

					return false;

				}

				if ( app.views.currentView.viewName === "MainStreamView" ) {

					if ( DEBUG ) {
						app.logThis ( [ "Hootsuite.refresh: Current view is MainStreamView, loading older messages ..." ] );
					}

					$mainStreamViewLoadMoreLink.slideDown (
						"fast", function ()
						{

							$mainStreamViewLoadMoreLink.find ( "a" ).click ();

						}
					);

					return false;

				}

				// stop existing activities and timers
				app.stopAllActivities ();
				app.stopAndRemoveAllTimers ();

				// refresh current view
				app.events.trigger ( "doViewRefresh" );

			}
		);

	}
);
