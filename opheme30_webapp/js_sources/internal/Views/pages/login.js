define([
	"jquery",
	"underscore",
	"backbone",
	"app",
	"textplugin!/templates/page_elements/forms/login.html",
	"textplugin!/templates/page_elements/forms/register.html",
	"textplugin!/templates/page_elements/forms/forgotPassword.html",
	"textplugin!/templates/page_elements/simple/terms.html",
	"textplugin!/templates/pages_full/login.html",
	"parsley"
], function (
	$,
	_,
	Backbone,
	app,
	LoginElementTpl,
	RegisterElementTpl,
	ForgotPasswordElementTpl,
	TermsElementTpl,
	LoginPageTpl
) {

	var LoginView = Backbone.View.extend({

		initialize: function () {

			var that = this;
			
			var funcs = _.functions(that);
			_.each(funcs, function (f) {
				that[f] = _.bind(that[f], that);
			});
			
			// Listen for session logged_in state changes and re-render
			app.session.on("change:logged_in", that.render);

			return that;
		},

		events: {
			"click #login-form input[type=submit]": "doLogin",
			"click #register-form input[type=submit]": "doRegister",
			"keyup #login-form input[name=password]": "_onPasswordKeyup",
			"keyup #register-form input[name=password_confirm]": "_onConfirmPasswordKeyup",
			"click #forgot-header": "_forgotToggle"
		},

		_forgotToggle: function (evt) {
			var i,
				el = $(evt.target);

			for (i = 0; i < 3; i++) {
				el = el.next().slideToggle("slow");
			};

			$(".flipper").animate({
					height: "470px"
				},
				"fast"
			);
		},

		// Allow enter press to trigger login
		_onPasswordKeyup: function (evt) {
			
			evt.preventDefault();

			var k = evt.keyCode || evt.which;

			if (k == 13 && $("#login-form input[name=password]").val() !== "") {
				this.doLogin();
				return false;
			}
			
		},

		// Allow enter press to trigger signup
		_onConfirmPasswordKeyup: function (evt) {
			
			evt.preventDefault();

			var k = evt.keyCode || evt.which;

			if (k == 13 && $("#register-form input[name=password_confirm]").val() !== "") {
				this.doRegister();
				return false;
			}
			
		},

		doLogin: function (evt) {

			evt.preventDefault();

			if ($("#login-form").parsley().isValid()) {
				app.session.login({
					username: $("#login-form input[name=email]").val(),
					password: $("#login-form input[name=password]").val(),
					remember: $("#login-form input[name=remember]").is(":checked")
				}, {
					success: function (user) {
						app.showAlert("Great stuff!", "Welcome, " + user.first_name + "!", "success");
					},
					error: function (err) {
						app.showAlert("Bummer dude!", err.error, "danger");
					}
				});
			} else {
				// Invalid clientside validations thru parsley
				if (DEBUG) console.log("Login :: Did not pass client side validation");
				app.showAlert("Bummer dude!", "Please enter your username and password!", "danger");
			}

		},

		doRegister: function (evt) {

			evt.preventDefault();

			if ($("#register-form").parsley("validate")) {

				app.session.register({
					email: $("#register-form input[name=email]").val(),
					password: $("#register-form input[name=password]").val(),
					first_name: $("#register-form input[name=first_name]").val()
				}, {
					success: function (mod, res) {
						if (DEBUG) console.log("SUCCESS", mod, res);
					},
					error: function (err) {
						if (DEBUG) console.log("ERROR", err);
						app.showAlert("Uh oh!", err.error, "danger");
					}
				});
			} else {
				// Invalid clientside validations through parsley
				if (DEBUG) console.log("Did not pass client side validation");
			}

		},

		render: function () {

			if (app.session.storage.get("logged_in")) {
				
				if (app.session.storage.get("user").is_valid === true) {

					app.router.navigate("dashboard", {
						trigger: true,
						replace: true
					});
				
				} else {
					
					app.router.navigate("account", {
						trigger: true,
						replace: true
					});
					
				}
				
				app.showAlert("Great stuff!", "Welcome, " + app.session.storage.get("user").email + "!", "success");

			} else {

				var subTemplates = [];

				this.template = _.template(LoginPageTpl);

				subTemplates.push({
					"renderLogin": _.template(LoginElementTpl)
				});
				subTemplates.push({
					"renderRegister": _.template(RegisterElementTpl)
				});
				subTemplates.push({
					"renderForgot": _.template(ForgotPasswordElementTpl)
				});
				subTemplates.push({
					"renderTerms": _.template(TermsElementTpl)
				});

				var data = {
					user: app.session.storage.get("user")
				};

				_.each(subTemplates, function (template) {
					data = _.extend(data, template);
				})

				this.$el.html(this.template(data));

			}

			return this;

		}

	});

	return LoginView;
});