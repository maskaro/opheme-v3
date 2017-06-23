define (
	[
		"underscore",
		"app",
		"Views/base",
		"textplugin!/templates/page_elements/errors/_404.html",
		"textplugin!/templates/pages_full/error.html"
	], function ( _,
	              app,
	              BaseView,
	              Error404ElementTpl,
	              ErrorPageTpl )
	{

		"use strict";

		return BaseView.extend (
			{

				viewName: "Error404View",

				viewPublicName: "Page not found!",

				viewDefaultLocation: "error/404",

				pageRequested: null,

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "Error404View.initialize is running." ] );
					}

				},

				events: {
					"click a#gotoHome": "gotoHome"
				},

				gotoHome: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "Error404View.gotoHome is running." ] );
					}

					app.eventPreventDefault ( evt );

					app.router.showIndex ();
				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "Error404View.render is running." ] );
					}

					var subTemplates = [],
						data;

					this.template = _.template ( ErrorPageTpl );

					data = { data: {} };

					if ( this.pageRequested ) {

						subTemplates.push (
							{
								"render404": _.template ( Error404ElementTpl )
							}
						);

						data.data = {
							page: this.pageRequested
						};

					}

					if ( DEBUG ) {
						app.logThis ( [ "Error404View.render: Data: ", data ] );
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
						app.logThis ( [ "Error404View.viewSetup is running." ] );
					}

				}

			}
		);

	}
);
