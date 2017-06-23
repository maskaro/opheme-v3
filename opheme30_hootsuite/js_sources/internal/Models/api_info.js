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
						app.logThis ( [ "APIInfoModel.initialize is running." ] );
					}

					return this;
				},

				defaults: {}

			}
		);

	}
);