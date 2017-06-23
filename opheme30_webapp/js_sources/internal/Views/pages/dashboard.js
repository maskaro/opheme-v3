define([
	"jquery",
	"underscore",
	"backbone",
	"app",
	"textplugin!/templates/page_elements/overviews/jobs.html",
	"textplugin!/templates/page_elements/overviews/subscription.html",
	"textplugin!/templates/pages_full/dashboard.html",
	"parsley"
], function (
	$,
	_,
	Backbone,
	app,
	JobsOverviewElementTpl,
	SubscriptionOverviewElementTpl,
	DashboardPageTpl
) {

	var DashboardView = Backbone.View.extend({

		initialize: function () {

			var that = this;
			var funcs = _.functions(that);
			_.each(funcs, function (f) {
				that[f] = _.bind(that[f], that);
			});

			return that;

		},

		events: {},

		render: function () {

			var subTemplates = [];

			this.template = _.template(DashboardPageTpl);

			subTemplates.push({
				"renderJobs": _.template(JobsOverviewElementTpl)
			});
			subTemplates.push({
				"renderSubscription": _.template(SubscriptionOverviewElementTpl)
			});

			var data = {
				user: app.session.storage.get("user")
			};

			_.each(subTemplates, function (template) {
				data = _.extend(data, template);
			})

			this.$el.html(this.template(data));

			return this;

		}

	});

	return DashboardView;
});