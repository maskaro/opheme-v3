'use strict';

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
				options: {},
				src: [
					"./bower_components/requirejs/require.js"
				],
				dest: "./js_sources/__generated/external_requirejs.js"
			},
			js_external_requirejstext: {
				options: {},
				src: [
					"./bower_components/requirejs-text/text.js"
				],
				dest: "./js_sources/__generated/external_requirejstext.js"
			},
			js_external_jquery: {
				options: {},
				src: [
					"./bower_components/jquery/dist/jquery.js",
				],
				dest: "./js_sources/__generated/external_jquery.js"
			},
			js_external_jqueryvalidate: {
				options: {},
				src: [
					"./bower_components/jquery-validate/dist/jquery.validate.js",
					"./bower_components/jquery-validate/dist/additional-methods.js"
				],
				dest: "./js_sources/__generated/external_jqueryvalidate.js"
			},
			js_external_jqueryui: {
				options: {},
				src: [
					"./bower_components/jquery-ui/jquery-ui.js",
				],
				dest: "./js_sources/__generated/external_jqueryui.js"
			},
			js_external_jquerytimeago: {
				options: {},
				src: [
					"./bower_components/timeago/jquery.timeago.js"
				],
				dest: "./js_sources/__generated/external_jquerytimeago.js"
			},
			js_external_jqueryscrollto: {
				options: {},
				src: [
					"./bower_components/jquery.scrollTo/jquery.scrollTo.js"
				],
				dest: "./js_sources/__generated/external_jqueryscrollto.js"
			},
			js_external_jquerytooltipster: {
				options: {},
				src: [
					"./bower_components/tooltipster/js/jquery.tooltipster.js"
				],
				dest: "./js_sources/__generated/external_jquerytooltipster.js"
			},
			js_external_jqueryfiledownload: {
				options: {},
				src: [
					"./bower_components/jquery.fileDownload/src/Scripts/jquery.fileDownload.js"
				],
				dest: "./js_sources/__generated/external_jqueryfiledownload.js"
			},
			js_external_jquerytimepicker: {
				options: {},
				src: [
					"./bower_components/jquery-timepicker-jt/jquery.timepicker.js"
				],
				dest: "./js_sources/__generated/external_jquerytimepicker.js"
			},
			js_external_bootstrap: {
				options: {},
				src: [
					"./bower_components/bootstrap-css/js/bootstrap.js"
				],
				dest: "./js_sources/__generated/external_bootstrap.js"
			},
			js_external_underscore: {
				options: {},
				src: [
					"./bower_components/underscore/underscore.js"
				],
				dest: "./js_sources/__generated/external_underscore.js"
			},
			js_external_backbone: {
				options: {},
				src: [
					"./bower_components/backbone/backbone.js",
				],
				dest: "./js_sources/__generated/external_backbone.js"
			},
			js_external_parsley: {
				options: {},
				src: [
					"./bower_components/parsleyjs/dist/parsley.js",
					"./bower_components/parsleyjs/dist/parsley.remote.js"
				],
				dest: "./js_sources/__generated/external_parsley.js"
			},
			js_external_json2: {
				options: {},
				src: [
					"./js_sources/external/json2.js"
				],
				dest: "./js_sources/__generated/external_json2.js"
			},
			js_external_jquerycookie: {
				options: {},
				src: [
					"./js_sources/external/jquery.cookie.js"
				],
				dest: "./js_sources/__generated/external_jquerycookie.js"
			},
			js_external_jquerystorageapi: {
				options: {},
				src: [
					"./bower_components/jQuery-Storage-API/jquery.storageapi.js",
				],
				dest: "./js_sources/__generated/external_jquerystorageapi.js"
			},
		},
		less: {
			options: {
				compress: true //minifying the result
			},
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
					"./public/css/external_auto.css": [
						"./bower_components/jquery-ui/themes/base/jquery-ui.css",
						"./bower_components/tooltipster/css/tooltipster.css",
						"./bower_components/tooltipster/css/themes/tooltipster-noir.css",
						"./bower_components/jquery-timepicker-jt/jquery.timepicker.css",
						"./bower_components/bootstrap-css/css/bootstrap.css",
						"./bower_components/components-font-awesome/css/font-awesome.css"
					]
				}
			},
			// external css which did not come through npm or bower
			css_external_custom: {
				files: {
					"./public/css/external_custom.css": [
						"./public/css/external/**/*.css",
						"./public/css/external/**/*.less"
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
					}
				]
			},
			/* ONLY FOR DEVELOPMENT */
			dev_js_external_auto: {
				files: [
					{
						expand: true,
						cwd: "./js_sources/__generated",
						src: "**/*.js",
						dest: "./public/js/lib"
					}
				]
			},
			/* ONLY FOR DEVELOPMENT */
			dev_js_external_custom: {
				files: [
					{
						expand: true,
						cwd: "./js_sources/external",
						src: "**/*.js",
						dest: "./public/js/lib"
					}
				]
			},
			/* ONLY FOR DEVELOPMENT */
			dev_js_internal: {
				files: [
					{
						expand: true,
						cwd: "./js_sources/internal",
						src: "**/*.js",
						dest: "./public/js/internal"
					}
				]
			}
		},
		uglify: {
			options: {
				mangle: false, // Use FALSE if you want the names of your functions and variables unchanged
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
			// external scripts which did not come through npm or bower
			js_external_custom: {
				files: [{
					expand: true,
					cwd: "./js_sources/external",
					src: "**/*.js",
					dest: "./public/js/lib"
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
                "./public/fonts/*"
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
			css_external: [
                "./public/css/external_*.css"
            ],
			css_internal: [
                "./public/css/internal.min.css"
            ],
			html: [
				"./public/templates/*"
			]
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
					"concat:js_external_requirejs",
					"concat:js_external_requirejstext",
					"concat:js_external_jquery",
					"concat:js_external_underscore",
					"concat:js_external_backbone",
					"concat:js_external_bootstrap",
					"concat:js_external_jqueryvalidate",
					"concat:js_external_jqueryui",
					"concat:js_external_jquerytimepicker",
					"concat:js_external_jqueryfiledownload",
					"concat:js_external_jquerytooltipster",
					"concat:js_external_jqueryscrollto",
					"concat:js_external_jquerytimeago",
					/*"uglify:js_external_auto",
					"uglify:js_external_custom"*/
					"copy:dev_js_external_auto", // USE ABOVE WHEN IN PRODUCTION
					"copy:dev_js_external_custom"
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
					//"uglify:js_internal"
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
                    "clean:css_external",
					"less:css_external_auto",
					"less:css_external_custom"
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
				]
			},
			html: {
				files: [
					"./html_sources/templates/**/*.html"
				],
				tasks: [
					"clean:html",
					"copy:html"
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
	grunt.loadNpmTasks("grunt-contrib-watch");
	// Task definition
	grunt.registerTask("default", [
        "clean",
		"concat",
		"less",
		"uglify",
		"copy"
	]);
};