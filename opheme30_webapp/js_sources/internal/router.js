define([
	"jquery",
	"underscore",
	"backbone",
	"app",
	"Views/fragments/header",
	"textplugin!/templates/page_elements/common/footer.html",
	"Views/pages/login",
	"Views/pages/dashboard",
	"Views/pages/userAccount"
], function (
	$,
	_,
	Backbone,
	app,
	HeaderFragmentView,
	FooterElementTpl,
	LoginPageView,
	DashboardPageView,
	UserAccountPageView
) {

	var WebRouter = Backbone.Router.extend({

		initialize: function () {
			return this;
		},

		routes: {
			"": "showIndex",
			"login": "showLogin",
			"logout": "doLogout",
			"dashboard": "showDashboard",
			"account": "showUserAccount",
			"*path": "showIndex"
		},

		show: function (view, options) {

			// Every page view in the router should need a header.
			// Instead of creating a base parent view, just assign the view to this
			// so we can create it if it doesn't yet exist
			if (!this.headerView) {
				this.headerView = new HeaderFragmentView({});
				this.headerView.setElement($("#header")).render();
			}

			if (!this.footerView) {
				this.footerView = _.template(FooterElementTpl);
				$("#footer").html(this.footerView());
			}

			// Close and unbind any existing page view
			if (this.currentView) {
				this.currentView.remove();
			}

			// Establish the requested view into scope
			this.currentView = view;

			$("#content").children().remove();

			// Need to be authenticated before rendering view.
			// For cases like a user's settings page where we need to double check against the server.
			if (typeof options !== "undefined" && options.requiresAuth) {
				var self = this;
				app.session.checkAuth({
					success: function () {
						// If auth successful, render inside the page wrapper
						$("#content").append(self.currentView.render().$el);
					},
					error: function () {
						app.showAlert("Bummer dude!", "WebRouter::show() - Failed to authenticate you!", "danger");
						self.navigate("/", {
							trigger: true,
							replace: true
						});
					}
				});

			} else {
				// Render inside the page wrapper
				$("#content").append(this.currentView.render().$el);
				// Re-delegate events (unbound when closed)
				//this.currentView.delegateEvents(this.currentView.events);
			}

		},

		showIndex: function () {

			this.navigate("/login", {
				trigger: true,
				replace: true
			});

		},

		showDashboard: function () {

			this.show(new DashboardPageView({}), {
				requiresAuth: true
			});

		},

		showUserAccount: function () {

			this.show(new UserAccountPageView({}), {
				requiresAuth: true
			});

		},

		showLogin: function () {

			this.show(new LoginPageView({}));

		},

		doLogout: function () {

			app.session.logout();

			this.navigate("/login", {
				trigger: true,
				replace: true
			});

		}

	});

	return WebRouter;

});