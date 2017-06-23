define (
	[
		"jquery",
		"underscore",
		"backbone",
		"app"
	], function ( $,
	              _,
	              Backbone,
	              app )
	{

		"use strict";

		return Backbone.Model.extend (
			{

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "Model.initialize is running." ] );
					}

					return this;
				},

				apiSetup: {
					endpoint: "", // "/admin/users"
					requires: "" // "client" or "user"
				},

				defaults: {}

			}
		);

	}
);