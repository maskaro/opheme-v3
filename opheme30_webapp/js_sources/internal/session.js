define([
	"jquery",
    "underscore",
    "backbone",
	"app",
	"Models/user",
	"jquerycookie",
	"jquerystorageapi"
], function (
	$,
	_,
	Backbone,
	app,
	UserModel
) {

	var Session = function () {

		var self = this;

		// Initialize with negative/empty defaults
		// These will be overriden after the initial checkAuth
		self.defaults = {
			logged_in: false,
			user: new UserModel({})
		};
		
		self.resetToDefault = function() {
			self.storage.removeAll();
			for (var key in self.defaults) {
				self.storage.set(key, self.defaults[key]);
			}
		}

		self.initialize = function () {

			if (self.storageContainers.local.isSet("api_info")) {
				self.storage = self.storageContainers.local;
			} else {
				self.storage = self.storageContainers.session;
			}
			
			if (self.storage.isSet("logged_in") && self.storage.get("logged_in") === false) 
				self.resetToDefault();

			_.extend(self, Backbone.Events);

			return self;

		};

		// Fxn to update user attributes after recieving API response
		self.updateSessionUser = function (userData) {
			var user = new UserModel({});
			user.set(userData);
			// TODO: this net line needs to go away in production
			user.set({ "is_valid": false, "is_admin": true });
			self.storage.set("user", user);
		};

		/*
		 * Check for session from API
		 * The API will parse client cookies using its secret token
		 * and return a user object if authenticated
		 */
		self.checkAuth = function (callback) {

			// not yet initialised, first ever use
			if (self.storage === null) {
				if (callback && "error" in callback) callback.error();
			} else if (
				self.storage.get("logged_in") === true &&
				self.storage.get("user").id !== 0
			) {
				if (callback && "success" in callback) callback.success();
			} else {
				// not logged in
				if (callback && "error" in callback) callback.error();
			}
			
			if (callback && "complete" in callback) callback.complete();
			
		};

		// opts { username, password }
		self.login = function (opts, callback, args) {
			if (DEBUG) console.log("Session.login.opts: ", opts);
			app.callAPI({
				method: "POST",
				endpoint: "/oauth/access_token",
				data: {
					"username": opts.username,
					"password": opts.password,
					"grant_type": "password",
					"client_credentials": "yes"
				},
				headers: {}
			}, {
				success: function (data) {
					if (DEBUG) console.log("Session.login.success: ", data);
					
					if (opts.remember === true) {
						self.storage = self.storageContainers.local;
					} else {
						self.storage = self.storageContainers.session;
					}
					
					var api_info = {
						"key": data.access_token,
						"expires": data.expires_in,
						"refresh_key": data.refresh_token,
						"refreshed_at": (new Date().getTime() / 1000 | 0)
					};
					
					self.storage.set("logged_in", true);
					self.storage.set("api_info", api_info);
					
					if (callback && "success" in callback) var oldSuccess = callback.success;
					
					self.getUser({
						success: function(user) {
							self.trigger("change:logged_in");
							if (oldSuccess) oldSuccess(user);
						}
					});

					//if (callback && "success" in callback) callback.success(data);
				},
				error: function (data) {
					if (DEBUG) console.log("Session.login.error: ", data);
					if (callback && "error" in callback) callback.error(data);
				}
			});
			
		};

		self.getUser = function (callback) {
			
			app.callAPI({
				method: "GET",
				endpoint: "/me",
				data: {},
				headers: {
					"Authorization": self.storage.get("api_info.key")
				}
			}, {
				success: function (user) {
					if (DEBUG) console.log("Session.getUser.success: ", user);

					self.updateSessionUser(user);

					if (callback && "success" in callback) callback.success(user);
				},
				error: function (data) {
					if (DEBUG) console.log("Session.getUser.error: ", data);
					if (callback && "error" in callback) callback.error(data);
				}
			});
			
		};

		self.logout = function (opts, callback, args) {
			
			self.resetToDefault();
			self.trigger("change:logged_in");
			
		};

		self.register = function (opts, callback, args) {
			// TODO
		};

		var namespace = $;//$.initNamespaceStorage("hootsuite_app");
		if ("localStorage" in window && window["localStorage"] !== null) {
			self.storageContainers = {
				"local": namespace.localStorage,
				"session": namespace.sessionStorage
			};
		} else {
			self.storageContainers = {
				"local": namespace.cookieStorage,
				"session": namespace.cookieStorage
			};
		}
		self.storage = null;

		return self.initialize();

	};

	return Session;
});