define([
    "jquery",
    "underscore",
    "backbone"
], function (
	$,
	_,
	Backbone
) {

	var app = {
		root: "/", // The root path to run the application through.
		URL: "/", // Base application URL
		API: "/api.php", // Base API URL (used by models & collections)

		// Show alert classes and hide after specified timeout
		// klass = warning, info, danger, success
		showAlert: function (title, text, klass) {
			$("#alerts").removeClass("alert-danger alert-warning alert-success alert-info");
			$("#alerts").addClass("alert-" + klass);
			$("#alerts").html("<button class='close' data-dismiss='alert'>×</button><strong>" + title + "</strong> " + text);
			$("#alerts").show("fast");
			setTimeout(function () {
				$("#alerts").hide();
			}, 7000);
		},

		// opts = { method (GET/POST/PUT/PATCH/DELETE), endpoint(/oauth/auth_token), data(JSON), headers(JSON) }
		// callback = { complete, success, error }
		callAPI: function (opts, callback) {
			if (DEBUG) console.log("AJAX callAPI: ", opts, callback);
			var self = this;
			var data = _.extend(opts.data, { 
				"request_method": opts.method,
				"request_apipath": opts.endpoint
			});
			$.ajax({
				url: this.API,
				dataType: "json",
				type: "POST",
				beforeSend: function (xhr) {
					if (opts.headers) {
						for (var header in opts.headers) {
							xhr.setRequestHeader(header, opts.headers[header]);
						}
					}
				},
				data: data,
				success: function (data, textStatus, jqXHR) {
					if (DEBUG) console.log("Ajax Success Response: ", data);//, "AJAX Success jqXHR: ", jqXHR);
					if (!data.error) {
						if (callback && "success" in callback) {
							if (data.data) callback.success(data.data, data.meta);
							else callback.success(data); // error? should not be the case
						}
					} else {
						if (callback && "error" in callback) {
							callback.error(data);
						}
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					if (DEBUG) console.log("AJAX Error Response: ", textStatus);//, "Ajax Error jqXHR: ", jqXHR);
					if (callback && "error" in callback) callback.error(data);
				}
			}).complete(function (jqXHR, textStatus) {
				//if (DEBUG) console.log("AJAX Complete Response: ", textStatus);//, "Ajax Complete jqXHR: ", jqXHR);
				if (callback && "complete" in callback) callback.complete(data);
			});
		},
	};

	$.ajaxSetup({
		cache: false
	}); // force ajax call on all browsers


	// Global event aggregator
	app.eventAggregator = _.extend({}, Backbone.Events);
	
	/*app.callAPI({
		method: "GET",
		endpoint: "/users",
		data: {
			"include": "companies",
			"cursor": null,
			"number": 20,
			"client_scope": "yes"
		},
		headers: {
		}
	}, {
		success: function (data) {
			if (DEBUG) console.log("getUsers.success: ", data);
		},
		error: function (data) {
			if (DEBUG) console.log("getUsers.error: ", data);
		}
	});*/

	return app;

});