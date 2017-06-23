var DEBUG = false;

require.config (
	{
		baseUrl: "/js/internal",
		paths  : {
			// libs
			textplugin        : "../lib/external_requirejstext",
			json2             : "../lib/external_json2",
			jquery            : "../lib/external_jquery",
			underscore        : "../lib/external_underscore",
			backbone          : "../lib/external_backbone",
			backbonemvc       : "../lib/external_backbonemvc",
			bootstrapjs       : "../lib/external_bootstrap",
			bootstraptour     : "../lib/external_bootstraptour",
			bootstraptagsinput: "../lib/external_bootstraptagsinput",
			jqueryui          : "../lib/external_jqueryui",
			jqueryuitouchpunch: "../lib/external_jqueryuitouchpunch",
			jqueryblockui     : "../lib/external_jqueryblockui",
			jquerycookie      : "../lib/external_jquerycookie",
			jqueryfiledownload: "../lib/external_jqueryfiledownload",
			jqueryimagesloaded: "../lib/external_jqueryimagesloaded",
			jquerytimeago     : "../lib/external_jquerytimeago",
			jquerytooltipster : "../lib/external_jquerytooltipster",
			jquerytimepicker  : "../lib/external_jquerytimepicker",
			jquerystorageapi  : "../lib/external_jquerystorageapi",
			jquerynoty        : "../lib/external_jquerynoty",
			jqueryselect2     : "../lib/external_jqueryselect2",
			parsley           : "../lib/external_parsley",
			markerclusterer   : "../lib/external_markerclustererplus",
			async             : "../lib/external_requirejsasync",
			font              : "../lib/external_requirejsfont",
			goog              : "../lib/external_requirejsgoog",
			image             : "../lib/external_requirejsimage",
			json              : "../lib/external_requirejsjson",
			noext             : "../lib/external_requirejsnoext",
			mdown             : "../lib/external_requirejsmdown",
			propertyParser    : "../lib/external_requirejspropertyParser",
			moment            : "../lib/external_moment",
			momenttz          : "../lib/external_momenttz",
			momenttzutils     : "../lib/external_momenttzutils",
			infinity          : "../lib/external_infinity",
			omspiderfier      : "../lib/external_omspiderfier",
			gmapsmaplabel     : "../lib/external_gmapsmaplabel"
		},
		shim   : {
			goog              : {
				deps: [ "async", "propertyParser" ]
			},
			underscore        : {
				exports: "_"
			},
			jquery            : {
				exports: "$"
			},
			infinity          : {
				deps: [ "jquery" ]
			},
			parsley           : {
				deps: [ "jquery" ]
			},
			jqueryui          : {
				deps: [ "jquery" ]
			},
			jqueryuitouchpunch: {
				deps: [ "jqueryui" ]
			},
			backbone          : {
				deps   : [ "underscore", "jquery" ],
				exports: "Backbone"
			},
			backbonemvc       : {
				deps   : [ "backbone" ],
				exports: "BackboneMVC"
			},
			bootstrapjs       : {
				deps: [ "jqueryui" ]
			},
			bootstraptour     : {
				deps: [ "bootstrapjs" ]
			},
			bootstraptagsinput: {
				deps: [ "bootstrapjs" ]
			},
			jqueryfiledownload: {
				deps: [ "jquery" ]
			},
			jqueryimagesloaded: {
				deps: [ "jquery" ]
			},
			jquerytimeago     : {
				deps: [ "jquery" ]
			},
			jqueryblockui     : {
				deps: [ "jquery" ]
			},
			jquerytooltipster : {
				deps: [ "jquery" ]
			},
			jquerytimepicker  : {
				deps: [ "jquery" ]
			},
			jquerycookie      : {
				deps: [ "jquery" ]
			},
			jquerystorageapi  : {
				deps: [ "jquery" ]
			},
			jquerynoty        : {
				deps: [ "jqueryui" ]
			},
			jqueryselect2     : {
				deps: [ "bootstrapjs" ]
			},
			moment            : {
				exports: [ "moment" ]
			},
			momenttz          : {
				deps: [ "moment" ]
			},
			momenttzutils     : {
				deps: [ "momenttz" ]
			}
		},
		config : {
			moment: {
				noGlobal: true
			}
		}
	}
);

require ( [ "main" ] );
