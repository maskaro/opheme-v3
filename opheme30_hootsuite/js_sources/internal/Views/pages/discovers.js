define (
	[
		"jquery",
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/overviews/_discovers.html",
		"textplugin!/templates/pages_full/discovers.html"
	], function ( $,
	              _,
	              app,
	              BaseView,
	              DiscoversOverviewElementTpl,
	              DiscoversPageTpl )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "DiscoversView",

				viewPublicName: "Discovers List",

				viewDefaultLocation: "discovers/view",

				eventList: [
					"doRemoveProcess",
					"doStartProcess",
					"doStopProcess"
				],

				controllerName: "discovers",

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView.initialize is running." ] );
					}

					return this;

				},

				events: {
					"click a.discoverName": "_viewDiscoverStream",
					"click i.fa-pencil"   : "_editDiscover",
					"click i.fa-pause"    : "_stopDiscover",
					"click i.fa-play"     : "_startDiscover",
					"click i.fa-remove"   : "_removeDiscover"
				},

				_viewDiscoverStream: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._viewDiscoverStream is running." ] );
					}

					this._selectDiscover ( evt.target );

					app.router.navigate (
						"main-stream/view", {
							trigger: true,
							replace: false
						}
					);

				},

				_editDiscover: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._editDiscover is running." ] );
					}

					this._selectDiscover ( evt.target );

					app.router.navigate (
						"discovers/view-setup", {
							trigger: true,
							replace: false
						}
					);

				},

				_removeDiscover: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._removeDiscover is running." ] );
					}

					app.startActivity ( "DiscoversView._removeDiscover", $ ( "div.panel-title h3" ) );

					app.showModal (
						"Remove Discover", "Are you sure you want to remove this Discover?",
						null,
						function ()
						{

							this._selectDiscover ( evt.target );

							this.dispatcher.trigger ( "doRemoveProcess" );

							this._selectDiscover ( null );

						}.bind ( this ),
						null, null, null, function ()
						{

							app.stopActivity ( "DiscoversView._removeDiscover" );

						}
					);

				},

				_stopDiscover: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._stopDiscover is running." ] );
					}

					//app.showModal (
					//	"Pause Discover", "Are you sure you want to pause this Discover?",
					//	function ()
					//	{

					this._selectDiscover ( evt.target );

					this.dispatcher.trigger ( "doStopProcess" );

					this._selectDiscover ( null );
					//
					//	}.bind ( this )
					//);

				},

				_startDiscover: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._startDiscover is running." ] );
					}

					//app.showModal (
					//	"Start Discover", "Are you sure you want to start this Discover?",
					//	function ()
					//	{

					this._selectDiscover ( evt.target );

					this.dispatcher.trigger ( "doStartProcess" );

					this._selectDiscover ( null );

					//	}.bind ( this )
					//);

				},

				_selectDiscover: function ( target )
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._selectDiscover is running. Target: ", target ] );
					}

					if ( !target ) {

						app.session.resetCurrentDiscover();

						return;

					}

					var id = $ ( target ).closest ( "li" ).data ( "id" );

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView._selectDiscover: Discover ID: ", id ] );
					}

					app.session.selectCurrentDiscover ( id );

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( DiscoversPageTpl );

					subTemplates.push (
						{
							"renderDiscoversView": _.template ( DiscoversOverviewElementTpl )
						}
					);

					data = {
						data: {
							user     : app.session.storage.user.toJSON (),
							discovers: app.session.storage.discovers
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "DiscoversView.render: Data: ", data ] );
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
						app.logThis ( [ "DiscoversView.viewSetup is running." ] );
					}

					// trigger view refresh if no activity in progress
					var fn = function ()
					{

						if ( !app.anyActivityRunning () ) {

							app.session.getDiscovers (
								{
									complete: function ()
									{

										// Render inside the page wrapper
										$ ( "#content" ).empty ().append ( this.render ().$el );

										// rebind events for newly generated page elements
										this.delegateEvents(this.events);

									}.bind(this)
								}
							);

						}

					}.bind(this);

					// make this view refresh every 5 seconds to pick up any changes that might have been made on the server from a third party
					app.addTimer (
						"refreshDiscoversView", "interval",
						setInterval ( fn, 15 * 1000 ), true
					);

				}

			}
		);

	}
);
