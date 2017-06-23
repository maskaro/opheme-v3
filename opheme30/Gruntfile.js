'use strict';

//Gruntfile
module.exports = function (grunt) {
	//Initializing the configuration object
	grunt.initConfig({
		// Task configuration
		concat: {
			options: {
				separator: ";"
			},
			opheme_js: {
				src: [
					"./bower_components/jquery/dist/jquery.js",
					"./bower_components/jquery-validate/dist/jquery.validate.js",
					"./bower_components/jquery-validate/dist/additional-methods.js",
					"./bower_components/jqueryui/jquery-ui.js",
					"./bower_components/backbone/backbone.js",
					"./bower_components/requirejs/require.js",
					"./bower_components/timeago/jquery.timeago.js",
					"./bower_components/tooltipster/jquery.tooltipster.js",
					"./bower_components/bootstrap/dist/js/bootstrap.js",
					"./bower_components/underscore/underscore.js",
					"./src/oPheme/Assets/javascript/**/*.js"
				],
				dest: "./public/js/opheme.min.js"
			}
		},
		less: {
			opheme_less: {
				options: {
					compress: true  //minifying the result
				},
				files: {
					"./public/css/opheme.min.css": "./src/oPheme/Assets/stylesheets/**/*.css"
				}
			}
		},
		uglify: {
			options: {
				mangle: true  // Use if you want the names of your functions and variables unchanged
			},
			opheme_js: {
				files: {
					"./public/js/opheme.min.js": "./public/js/opheme.min.js"
				}
			}
		},
		/*phpunit: {
		 classes: {
		 dir: "app/tests/"   //location of the tests
		 },
		 options: {
		 bin: "vendor/bin/phpunit",
		 colors: true
		 }
		 },*/
		watch: {
			opheme_js: {
				files: [
					//watched files
					"./bower_components/jquery/jquery.js",
					"./bower_components/bootstrap/dist/js/bootstrap.js",
					"./src/oPheme/Assets/javascript/**/*.js"
				],
				tasks: [//tasks to run
					"concat:opheme_js",
					"uglify:opheme_js"
				],
				options: {
					livereload: true                        //reloads the browser
				}
			},
			opheme_less: {
				files: [
					"./src/oPheme/Assets/stylesheets/**/*.css"
				], //watched files
				tasks: ["less"], //tasks to run
				options: {
					livereload: true                        //reloads the browser
				}
			}
			/*tests: {
			 files: ["app/controllers/*.php","app/models/*.php"],  //the task will run only when you save files in this location
			 tasks: ["phpunit"]
			 }*/
		}
	});
	// Plugin loading
	grunt.loadNpmTasks("grunt-contrib-concat");
	grunt.loadNpmTasks("grunt-contrib-watch");
	grunt.loadNpmTasks("grunt-contrib-less");
	grunt.loadNpmTasks("grunt-contrib-uglify");
	//grunt.loadNpmTasks("grunt-phpunit");
	// Task definition
	grunt.registerTask("default", ["concat", "less", "uglify"]);
};
