// Filename: models/project
define([
	"underscore",
	"backbone"
], function (_, Backbone) {

	var RandomModel = Backbone.Model.extend({
		defaults: {
			
		},
		urlRoot: "/random",
		apiBase: "/api.php",
		url: function() {
			var base =
				_.result(this, "urlRoot") ||
				_.result(this.collection, "url") ||
				urlError();
			if (this.isNew()) return base;
			return this.apiBase + "?path=" + base.replace(/([^\/])$/, "$1/") + encodeURIComponent(this.id);
		}
	});

	// Return the model for the module 
	return RandomModel;

});