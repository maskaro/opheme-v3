define([
	"jquery",
    "underscore",
    "backbone",
	"app",
], function (
	$,
	_,
	Backbone,
	app
) {

	var UserModel = Backbone.Model.extend({

		initialize: function () {

			var that = this;

			var funcs = _.functions(that);
			_.each(funcs, function (f) {
				that[f] = _.bind(that[f], that);
			});

			return that;
		},

		defaults: {
			id: 0,
			email: "",
			suspended: false,
			created_at: "",
			company: {}
		},

		url: function () {
			return app.API + "/user";
		}

	});

	return UserModel;
});