define (
	[
		"backbone",
		"app"
	], function ( Backbone,
	              app )
	{

		"use strict";

		return Backbone.Model.extend (
			{

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "UserModel.initialize is running." ] );
					}

					return this;
				},

				apiSetup: {
					endpoint: "/admin/users",
					requires: "client"
				},

				defaults: {}

			}
		);
	}
);