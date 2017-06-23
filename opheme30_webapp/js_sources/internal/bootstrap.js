var DEBUG = true;

require.config({
	baseUrl: "/js/internal",
	paths: {
		// libs
		textplugin: "../lib/external_requirejstext",
		json2: "../lib/external_json2",
		jquery: "../lib/external_jquery",
		underscore: "../lib/external_underscore",
		backbone: "../lib/external_backbone",
		bootstrapjs: "../lib/external_bootstrap",
		jqueryvalidate: "../lib/external_jqueryvalidate",
		jqueryui: "../lib/external_jqueryui",
		jqueryfiledownload: "../lib/external_jqueryfiledownload",
		jquerytimeago: "../lib/external_jquerytimeago",
		jquerytooltipster: "../lib/external_jquerytooltipster",
		jquerytimepicker: "../lib/external_jquerytimepicker",
		jquerycookie: "../lib/external_jquerycookie",
		jquerystorageapi: "../lib/external_jquerystorageapi",
		parsley: "../lib/external_parsley"
	},
	shim: {
		underscore: {
			exports: "_"
		},
		jquery: {
			exports: "$"
		},
		backbone: {
			deps: ["underscore", "jquery"],
			exports: "Backbone"
		},
		bootstrapjs: {
			deps: ["jquery"]
		},
		parsley: {
			deps: ["jquery"]
		}
	}
});

require(["main"]);