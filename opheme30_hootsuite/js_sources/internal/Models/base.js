// Filename: models/project
define (
	[
		"underscore",
		"backbone"
	], function ( _, Backbone )
	{

		"use strict";

		return Backbone.Model.extend (
			{
				defaults: {
					id: null
				},
				apiSetup: {
					endpoint: null, // "/admin/users", "/users"
					requires: null // "client", "user"
				}
			}
		);

	}
);