define([
	"jquery",
	"underscore",
	"backbone",
	"app",
	"textplugin!/templates/page_elements/common/header.html"
], function (
	$,
	_,
	Backbone,
	app,
	HeaderElementTpl
) {

	var HeaderView = Backbone.View.extend({

		template: _.template(HeaderElementTpl),

		initialize: function () {

			var that = this;
			var funcs = _.functions(that);
			_.each(funcs, function (f) {
				that[f] = _.bind(that[f], that);
			});
			
			// Listen for session logged_in state changes and re-render
			app.session.on("change:logged_in", that.onLoginStatusChange);
			
			return that;
			
		},

		events: {
		},

		onLoginStatusChange: function (evt) {
			this.render();
			if (app.session.storage.get("logged_in")) {
				app.showAlert("Success!", "Logged in as " + app.session.storage.get("user").email, "success");
			} else {
				app.showAlert("See ya!", "Logged out successfully", "success");
			}
		},

		render: function () {
			if (DEBUG) console.log("RENDER::", app.session.storage.get("user"), app.session.storage.keys());
			this.$el.html(this.template({
				logged_in: app.session.storage.get("logged_in"),
				user: app.session.storage.get("user")
			}));
			return this;
		},

	});

	return HeaderView;
});