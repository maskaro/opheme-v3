"use strict";

//Gruntfile
module.exports = function (grunt) {
	//Initializing the configuration object
	grunt.initConfig({
		// Task configuration
		concat: {
			options: {
				stripBanners: true, // remove /**/ comments
				separator: ";" // separate the file contents
			},
			// external scripts which came through npm or bower
			js_external_requirejs: {
				src: [
					"./bower_components/requirejs/require.js"
				],
				dest: "./js_sources/__generated/external_requirejs.js"
			},
			js_external_requirejstext: {
				src: [
					"./bower_components/requirejs-text/text.js"
				],
				dest: "./js_sources/__generated/external_requirejstext.js"
			},
			js_external_jquery: {
				src: [
					"./bower_components/jquery/dist/jquery.js"
				],
				dest: "./js_sources/__generated/external_jquery.js"
			},
			js_external_jqueryui: {
				src: [
					"./bower_components/jquery-ui/jquery-ui.js"
				],
				dest: "./js_sources/__generated/external_jqueryui.js"
			},
			js_external_jqueryimagesloaded: {
				src: [
					"./bower_components/imagesloaded/imagesloaded.pkgd.js"
				],
				dest: "./js_sources/__generated/external_jqueryimagesloaded.js"
			},
			js_external_jquerytimeago: {
				src: [
					"./bower_components/timeago/jquery.timeago.js"
				],
				dest: "./js_sources/__generated/external_jquerytimeago.js"
			},
			js_external_jqueryscrollto: {
				src: [
					"./bower_components/jquery.scrollTo/jquery.scrollTo.js"
				],
				dest: "./js_sources/__generated/external_jqueryscrollto.js"
			},
			js_external_jquerytooltipster: {
				src: [
					"./bower_components/tooltipster/js/jquery.tooltipster.js"
				],
				dest: "./js_sources/__generated/external_jquerytooltipster.js"
			},
			js_external_jqueryfiledownload: {
				src: [
					"./bower_components/jquery.fileDownload/src/Scripts/jquery.fileDownload.js"
				],
				dest: "./js_sources/__generated/external_jqueryfiledownload.js"
			},
			js_external_jquerytimepicker: {
				src: [
					"./bower_components/jquery-timepicker-jt/jquery.timepicker.js"
				],
				dest: "./js_sources/__generated/external_jquerytimepicker.js"
			},
			js_external_bootstrap: {
				src: [
					"./bower_components/bootstrap-css/js/bootstrap.js"
				],
				dest: "./js_sources/__generated/external_bootstrap.js"
			},
			js_external_bootstraptour: {
				src: [
					"./bower_components/bootstrap-tour/build/js/bootstrap-tour.js"
				],
				dest: "./js_sources/__generated/external_bootstraptour.js"
			},
			js_external_bootstraptagsinput: {
				src: [
					"./bower_components/tagsinput/dist/bootstrap-tagsinput.js"
				],
				dest: "./js_sources/__generated/external_bootstraptagsinput.js"
			},
			js_external_underscore: {
				src: [
					"./bower_components/underscore/underscore.js"
				],
				dest: "./js_sources/__generated/external_underscore.js"
			},
			js_external_backbone: {
				src: [
					"./bower_components/backbone/backbone.js"
				],
				dest: "./js_sources/__generated/external_backbone.js"
			},
			js_external_backbonemvc: {
				src: [
					"./js_sources/external/backbone-mvc.js"
				],
				dest: "./js_sources/__generated/external_backbonemvc.js"
			},
			js_external_parsley: {
				src: [
					"./bower_components/parsleyjs/dist/parsley.js",
					"./bower_components/parsleyjs/dist/parsley.remote.js"
				],
				dest: "./js_sources/__generated/external_parsley.js"
			},
			js_external_json2: {
				src: [
					"./js_sources/external/json2.js"
				],
				dest: "./js_sources/__generated/external_json2.js"
			},
			js_external_jquerycookie: {
				src: [
					"./js_sources/external/jquery.cookie.js"
				],
				dest: "./js_sources/__generated/external_jquerycookie.js"
			},
			js_external_jquerystorageapi: {
				src: [
					"./js_sources/external/jquery.storageapi.modified.js"
				],
				dest: "./js_sources/__generated/external_jquerystorageapi.js"
			},
			js_external_hootsuiteappreceiver: {
				src: [
					"./js_sources/external/hootsuite.app-receiver.js"
				],
				dest: "./js_sources/__generated/external_hootsuiteappreceiver.js"
			},
			js_external_jqueryuitouchpunch: {
				src: [
					"./bower_components/jquery-ui-touch-punch/jquery.ui.touch-punch.js"
				],
				dest: "./js_sources/__generated/external_jqueryuitouchpunch.js"
			},
			js_external_markerclustererplus: {
				src: [
					"./bower_components/markerclustererplus/src/markerclusterer.js"
				],
				dest: "./js_sources/__generated/external_markerclustererplus.js"
			},
			js_external_moment: {
				src: [
					"./bower_components/moment/moment.js"
				],
				dest: "./js_sources/__generated/external_moment.js"
			},
			js_external_momenttz: {
				src: [
					"./bower_components/moment-timezone/moment-timezone.js"
				],
				dest: "./js_sources/__generated/external_momenttz.js"
			},
			js_external_momenttzutils: {
				src: [
					"./bower_components/moment-timezone/moment-timezone-utils.js"
				],
				dest: "./js_sources/__generated/external_momenttzutils.js"
			},
			js_external_jquerynoty: {
				src: [
					"./bower_components/noty/js/noty/packaged/jquery.noty.packaged.js"
				],
				dest: "./js_sources/__generated/external_jquerynoty.js"
			},
			js_external_jqueryselect2: {
				src: [
					"./bower_components/select2/select2.js"
				],
				dest: "./js_sources/__generated/external_jqueryselect2.js"
			},
			js_external_infinity: {
				src: [
					"./js_sources/external/infinity.js"
				],
				dest: "./js_sources/__generated/external_infinity.js"
			},
			js_external_omspiderfier: {
				src: [
					"./js_sources/external/omspiderfier.js"
				],
				dest: "./js_sources/__generated/external_omspiderfier.js"
			},
			js_external_gmapsmaplabel: {
				src: [
					"./js_sources/external/gmaps-maplabel.js"
				],
				dest: "./js_sources/__generated/external_gmapsmaplabel.js"
			},
			js_external_jqueryblockui: {
				src: [
					"./bower_components/blockui/jquery.blockUI.js"
				],
				dest: "./js_sources/__generated/external_jqueryblockui.js"
			}
		},
		less: {
			// our own css
			css_internal: {
				files: {
					"./public/css/internal.css": [
						"./css_sources/internal/**/*.css",
						"./css_sources/internal/**/*.less"
					]
				}
			},
			// external css which came through npm or bower
			css_external_auto: {
				files: {
					"./css_sources/__generated/external_auto.css": [
						"./bower_components/tooltipster/css/tooltipster.css",
						"./bower_components/tooltipster/css/themes/tooltipster-noir.css",
						"./bower_components/jquery-timepicker-jt/jquery.timepicker.css",
						"./bower_components/bootstrap-css/css/bootstrap.css",
						"./bower_components/components-font-awesome/css/font-awesome.css",
						"./bower_components/tagsinput/dist/bootstrap-tagsinput.css",
						"./bower_components/bootstrap-tour/build/css/bootstrap-tour.css",
						"./bower_components/select2/select2.css",
						"./bower_components/select2-bootstrap-css/select2-bootstrap.css"
					],
					"./css_sources/__generated/external_jqueryui.css": [
						"./bower_components/jquery-ui/themes/base/*.css",
						"!./bower_components/jquery-ui/themes/base/all.css",
						"!./bower_components/jquery-ui/themes/base/base.css",
						"!./bower_components/jquery-ui/themes/base/jquery*.css",
						"!./bower_components/jquery-ui/themes/base/tooltip.css"
					]
				}
			},
			css_external_auto_combine: {
				options: {
					compress: true,
					yuicompress: true,
					optimization: 1
				},
				files: {
					"./public/css/external_auto.css": [
						"./css_sources/__generated/external_jqueryui.css",
						"./css_sources/__generated/external_auto.css"
					]
				}
			},
			// external css which did not come through npm or bower
			css_external_custom: {
				files: {
					"./public/css/external_custom.css": [
						"./css_sources/external/**/*.css",
						"./css_sources/external/**/*.less"
					]
				}
			}
		},
		copy: {
			// copy css images and fonts across
			resources: {
				files: [
					{
						expand: true,
						flatten: true,
						src: "./bower_components/jquery-ui/themes/base/images/*",
						dest: "./public/css/images/"
					},
					{
						expand: true,
						flatten: true,
						src: "./bower_components/select2/*.png",
						dest: "./public/css/"
					},
					{
						expand: true,
						flatten: true,
						src: "./bower_components/select2/*.gif",
						dest: "./public/css/"
					},
					{
						expand: true,
						flatten: true,
						src: "./bower_components/bootstrap-css/fonts/*",
						dest: "./public/fonts/"
					},
					{
						expand: true,
						flatten: true,
						src: "./bower_components/components-font-awesome/fonts/*",
						dest: "./public/fonts/"
					}
				]
			},
			html: {
				files: [
					{
						expand: true,
						flatten: false,
						cwd: "./html_sources/templates",
						src: "./**/*.html",
						dest: "./public/templates/"
					},
					{
						expand: true,
						flatten: false,
						cwd: "./html_sources/",
						src: "./*.html",
						dest: "./public/"
					}
				]
			},
			php: {
				files: [
					{
						expand: true,
						flatten: false,
						cwd: "./php_sources",
						src: "./**/*.php",
						dest: "./public/"
					}
				]
			}
			/* ONLY FOR DEVELOPMENT - simply comment out for live */
			//dev_js_external_auto: {
			//	files: [
			//		{
			//			expand: true,
			//			cwd: "./js_sources/__generated",
			//			src: "**/*.js",
			//			dest: "./public/js/lib"
			//		}
			//	]
			//},
			/* ONLY FOR DEVELOPMENT - simply comment out for live */
			//dev_js_internal: {
			//	files: [
			//		{
			//			expand: true,
			//			cwd: "./js_sources/internal",
			//			src: "**/*.js",
			//			dest: "./public/js/internal"
			//		}
			//	]
			//}
		},
		uglify: {
			options: {
				//mangle: true // Use FALSE if you want the names of your functions and variables unchanged
				//except: ["require", "requirejs", "jQuery", "$", "Backbone", "Underscore", "_"]
			},
			// external scripts which came through npm or bower
			js_external_auto: {
				files: [{
					expand: true,
					cwd: "./js_sources/__generated",
					src: "**/*.js",
					dest: "./public/js/lib"
				}]
			},
			js_external_requirejsplugins: {
				files: [{
					expand: true,
					cwd: "./bower_components/requirejs-plugins/src",
					src: "*.js",
					dest: "./public/js/lib",
					rename: function(dest, src) {
						return dest + "/external_requirejs" + src;
					}
				}]
			},
			// our own scripts
			js_internal: {
				files: [{
					expand: true,
					cwd: "./js_sources/internal",
					src: "**/*.js",
					dest: "./public/js/internal"
                }]
			}
		},
		clean: {
			resources: [
                "./public/css/images/*",
                "./public/fonts/*",
				"./public/css/*.png",
				"./public/css/*.gif"
            ],
			js_generated: [
				"./js_sources/__generated/*.js"
			],
			js_external: [
                "./public/js/lib/**/*.js"
            ],
			js_internal: [
                "./public/js/internal/**/*.js"
            ],
			css_generated: [
				"./css_sources/__generated/*.css"
			],
			css_external: [
                "./public/css/external_*.css"
            ],
			css_internal: [
                "./public/css/internal.css"
            ],
			html: [
				"./public/templates/*",
				"./public/*.html"
			],
			php: [
				"./public/**/*.php"
			]
		},
		manifest: {
			generate: {
				options: {
					basePath: "public/",
					network: [ "/api.php" ],
					preferOnline: false,
					verbose: true,
					timestamp: true,
					hash: true,
					master: [ "index.php" ],
					process: function(path) {
						return "/" + path;
					}
				},
				src: [
					"**/*.js",
					"**/*.css",
					"**/*.gif",
					"**/*.png",
					"**/*.otf",
					"**/*.eot",
					"**/*.svg",
					"**/*.ttf",
					"**/*.woff",
					"**/*.woff2",
					"**/*.ico",
					"**/*.html",
				    "!*.php",
				    "!manifest.appcache"
				],
				dest: "public/manifest.appcache"
			}
		},
		watch: {
			// external js updates
			js_external: {
				files: [
					//watched files
					"./bower_components/**/*.js",
					"./js_sources/external/**/*.js"
				],
				tasks: [ //tasks to run
                    "clean:js_external",
					"clean:js_generated",
					"concat",
//					"concat:js_external_requirejs",
					"uglify:js_external_requirejsplugins",
//					"concat:js_external_requirejstext",
//					"concat:js_external_jquery",
//					"concat:js_external_jqueryblockui",
//					"concat:js_external_jquerycookie",
//					"concat:js_external_jqueryfiledownload",
//					"concat:js_external_jquerynoty",
//					"concat:js_external_jqueryscrollto",
//					"concat:js_external_jqueryselect2",
//					"concat:js_external_jquerystorageapi",
//					"concat:js_external_jquerytimeago",
//					"concat:js_external_jquerytimepicker",
//					"concat:js_external_jquerytooltipster",
//					"concat:js_external_jqueryui",
//					"concat:js_external_jqueryuitouchpunch",
//					"concat:js_external_underscore",
//					"concat:js_external_backbone",
//					"concat:js_external_backbonemvc",
//					"concat:js_external_bootstrap",
//					"concat:js_external_bootstraptagsinput",
//					"concat:js_external_parsley",
//					"concat:js_external_json2",
//					"concat:js_external_hootsuiteappreceiver",
//					"concat:js_external_markerclustererplus",
//					"concat:js_external_moment",
//					"concat:js_external_momenttz",
//					"concat:js_external_momenttzutils",
//					"concat:js_external_omspiderfier",
//					"concat:js_external_infinity",
					/*"uglify:js_external_auto"
					"manifest"*/
					"copy:dev_js_external_auto" // USE ABOVE WHEN IN PRODUCTION
				],
				options: {}
			},
			// us changing our own js files
			js_internal: {
				files: [
					//watched files
					"./js_sources/internal/**/*.js"
				],
				tasks: [ //tasks to run
                    "clean:js_internal",
					//"concat:js_internal",
					//"uglify:js_internal",
					//"manifest"
					"copy:dev_js_internal" // USE ABOVE WHEN IN PRODUCTION
				],
				options: {}
			},
			// external css updates
			css_external: {
				files: [
					"./bower_components/**/*.css",
					"./css_sources/external/**/*.css",
					"./css_sources/external/**/*.less"
				], //watched files
				tasks: [
					"clean:css_generated",
                    "clean:css_external",
					"less:css_external_auto",
					"less:css_external_auto_combine",
					"less:css_external_custom"
					//"manifest" // enable this for live
				], //tasks to run
				options: {}
			},
			// us changing our own css files
			css_internal: {
				files: [
					"./css_sources/internal/**/*.css",
					"./css_sources/internal/**/*.less"
				], //watched files
				tasks: [
                    "clean:css_internal",
					"less:css_internal"
                    //"manifest" // enable this for live
				], //tasks to run
				options: {}
			},
			// copy resources across
			resources: {
				files: [
					"./bower_components/jquery-ui/themes/base/images/*",
					"./bower_components/bootstrap-css/fonts/*",
					"./bower_components/components-font-awesome/fonts/*"
				],
				tasks: [
					"clean:resources",
					"copy:resources"
					//"manifest" // enable this for live
				]
			},
			html: {
				files: [
					"./html_sources/templates/**/*.html",
					"./html_sources/*.html"
				],
				tasks: [
					"clean:html",
					"copy:html"
					//"manifest" // enable this for live
				]
			},
			php: {
				files: [
					"./php_sources/**/*.php"
				],
				tasks: [
					"clean:php",
					"copy:php"
				]
			},
			// react to this config file changing
			grunt_config: {
				files: [
					"./Gruntfile.js"
				],
				tasks: [
					"default"
				]
			}
		}
	});
	// Plugin loading
	grunt.loadNpmTasks("grunt-contrib-clean");
	grunt.loadNpmTasks("grunt-contrib-concat");
	grunt.loadNpmTasks("grunt-contrib-less");
	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-contrib-copy");
	grunt.loadNpmTasks("grunt-manifest");
	grunt.loadNpmTasks("grunt-contrib-watch");
	// Task definition
	grunt.registerTask("default", [
        "clean",
		"concat",
		"less",
		"uglify",
		"copy",
		"manifest"
	]);
};
