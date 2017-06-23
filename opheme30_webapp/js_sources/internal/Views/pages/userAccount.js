define([
	"jquery",
	"underscore",
	"backbone",
	"app",
	"textplugin!/templates/page_elements/forms/userAccount.html",
	"textplugin!/templates/page_elements/overviews/authorisation.html",
	"textplugin!/templates/pages_full/userAccount.html",
	"parsley"
], function (
	$,
	_,
	Backbone,
	app,
	UserAccountFormElementTpl,
	AuthorisationOverviewElementTpl,
	UserAccountPageTpl
) {

	var UserAccountView = Backbone.View.extend({

		initialize: function () {

			var that = this;
			
			var funcs = _.functions(that);
			_.each(funcs, function (f) {
				that[f] = _.bind(that[f], that);
			});

			return that;

		},

		events: {
			"click #account-update-form input[type=submit]": "doUserSaveAccount",
			"click .sma-remove-form input[type=submit]": "doSMRemoveAccount",
			"click .sma-add-form button": "doSMAddAccount"
		},
		
		doUserSaveAccount: function(evt) {
			
			evt.preventDefault();
			
			if (DEBUG) console.log("doUserSaveAccount");
			
		},
		
		doSMRemoveAccount: function(evt) {
			
			evt.preventDefault();
			
			if (DEBUG) console.log("doSMRemoveAccount");
			
			confirm("Are you sure you want to remove this Twitter account?");
			
		},
		
		doSMAddAccount: function(evt) {
			
			evt.preventDefault();
			
			if (DEBUG) console.log("doSMAddAccount");
			
			$("form#new-sma-account").submit();
			
		},

		render: function () {

			var subTemplates = [];

			this.template = _.template(UserAccountPageTpl);

			subTemplates.push({
				"renderUserAccount": _.template(UserAccountFormElementTpl)
			});
			subTemplates.push({
				"renderAuthorisation": _.template(AuthorisationOverviewElementTpl)
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

	return UserAccountView;
});