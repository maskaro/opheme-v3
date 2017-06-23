/*

oPheme UI - jQuery Plugin

Copyright Razvan-Ioan Dinita

BASED ON WORK FOUND AT < http://www.queness.com/post/112/a-really-simple-jquery-plugin-tutorial
http://docs.jquery.com/Plugins/Authoring http://stefangabos.ro/jquery/jquery-plugin-boilerplate-oop/ >

//for (var prop in data.options) { alert(prop + " = " + data.options[prop]); break; }

*/

//one job per instance
;(function($) {
	
	google.maps.visualRefresh = true;

	$.oPhemeUI = function(el, options) {
		
		//default options
		var defaults = {
			omap: { //map related options
				api: "gmaps", //must be declared
				gmaps: { 
					options: { //map specific options, must consult with API
						map_centre: { //custom container, defines map centre coords
							lat: 52.2100,
							lng: 0.1300
						},
						zoom: 16,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						panControl: false,
						zoomControl: false,
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
							position: google.maps.ControlPosition.TOP_CENTER
						},
						scaleControl: false,
						streetViewControl: false,
						overviewMapControl: false
					},
					noOfMarkers: 1000, //max number of markers on map
					mc_options: { //marker clusterer options
						gridSize: 30,
						maxZoom: 15,
						batchSize: 40
					},
					precision: 7 //coords precision, digits after dot
				}
			},
			timeout: 15000, //check timeout in ms
			max_refresh_items: 60, //max number of new messages at a time from server
			max_items: 60, //max number of messages from queue to be displayed at a time
			display_freq: 15000, //message display frequency
			module: null,
			sent: null,
			id: null,
			suspended: 0,
			oUserId: null,
			shared: null,
			iconShapes: { 
				"instagram": "M 26.688,0L 5.313,0 C 2.391,0,0,2.391,0,5.313l0,21.375 c0,2.922, 2.391,5.313, 5.313,5.313l 21.375,0 c 2.922,0, 5.313-2.391, 5.313-5.313L 32,5.313 C 32,2.391, 29.609,0, 26.688,0z M 10.244,14l 11.512,0 c 0.218,0.627, 0.338,1.3, 0.338,2c0,3.36-2.734,6.094-6.094,6.094c-3.36,0-6.094-2.734-6.094-6.094 C 9.906,15.3, 10.025,14.627, 10.244,14z M 28,14.002L 28,22 l0,4 c0,1.1-0.9,2-2,2L 6,28 c-1.1,0-2-0.9-2-2l0-4 L 4,14.002 L 4,14 l 3.128,0 c-0.145,0.644-0.222,1.313-0.222,2c0,5.014, 4.079,9.094, 9.094,9.094c 5.014,0, 9.094-4.079, 9.094-9.094 c0-0.687-0.077-1.356-0.222-2L 28,14 L 28,14.002 z M 28,7c0,0.55-0.45,1-1,1l-2,0 c-0.55,0-1-0.45-1-1L 24,5 c0-0.55, 0.45-1, 1-1l 2,0 c 0.55,0, 1,0.45, 1,1L 28,7 z",
				"twitter": "M 32,6.076c-1.177,0.522-2.443,0.875-3.771,1.034c 1.355-0.813, 2.396-2.099, 2.887-3.632 c-1.269,0.752-2.674,1.299-4.169,1.593c-1.198-1.276-2.904-2.073-4.792-2.073c-3.626,0-6.565,2.939-6.565,6.565 c0,0.515, 0.058,1.016, 0.17,1.496c-5.456-0.274-10.294-2.888-13.532-6.86c-0.565,0.97-0.889,2.097-0.889,3.301 c0,2.278, 1.159,4.287, 2.921,5.465c-1.076-0.034-2.088-0.329-2.974-0.821c-0.001,0.027-0.001,0.055-0.001,0.083 c0,3.181, 2.263,5.834, 5.266,6.438c-0.551,0.15-1.131,0.23-1.73,0.23c-0.423,0-0.834-0.041-1.235-0.118 c 0.836,2.608, 3.26,4.506, 6.133,4.559c-2.247,1.761-5.078,2.81-8.154,2.81c-0.53,0-1.052-0.031-1.566-0.092 c 2.905,1.863, 6.356,2.95, 10.064,2.95c 12.076,0, 18.679-10.004, 18.679-18.68c0-0.285-0.006-0.568-0.019-0.849 C 30.007,8.548, 31.12,7.392, 32,6.076z"
			},
			iconColours: {
				"instagram": {
					"none": "#8B4513",
					"negative": "red",
					"neutral": "blue",
					"positive": "green"
				},
				"twitter": {
					"none": "#1E90FF",
					"negative": "red",
					"neutral": "blue",
					"positive": "green"
				}
			},
			ageThresholdOpacity: { // up to threshold has opacity
				"10": 1,
				"20": 0.85,
				"30": 0.70,
				"40": 0.55,
				"50": 0.50,
				"60": 0.45,
				"old": 0.35
			}
		};

		var plugin = this; //internal reference

		//internal tracking of things
		plugin.internal = {
			_map_handle: null, //map handle
			_map_mc_handle: null, //map marker clusterer handle
			_map_markers: [], //keep track of markers
			_map_temp_markers: [], //keep track of temporary markers waiting to be added to map
			_map_marker_tooltips: [], //keep track of marker tooltips
			_map_tooltip_class: "_opheme_bubbleContainer", //name of tooltip css class
			_map_container: $("<div class='opheme_map_'/>"), //custom container in which to display the map
			_php_connection: "/ajax", //php script which handles ajax requests - URL, relative or absolute
			_info: null, //job spec information
			_updates: [], //job messages, looks similar to plugin settings
			_fetched_ids: [], //job message ids already retrieved from server
			_messagesForShareCount: 0, //job messages count parsed until this point
			_timer: null, //job timer handle
			_queue_timer: null //job timer handle
			
		};
		
		plugin.settings = {}; //public settings

		var init = function() { //initial setup
			
			//add custom settings to defaults, overriding as necessary
			plugin.settings = $.extend({}, defaults, options);
			//jquery element reference
			plugin.el = el;
			//add container to view
			plugin.el.append(plugin.internal._map_container);
			
		};
		
		//empty the map element, ready to re-use
		plugin.clearMapElement = function() { plugin.internal._map_container.empty(); };
		
		//generic map setup, will point to map_<custom_API>()
		plugin.map = function(info) {
			
			var map;
			
			if (info.api === "gmaps") {
			
				plugin.settings.omap.api = info.api;

				eval("map = plugin.map_" + info.api + "(info.settings)");
				
			}
			
			return map;
			
		};
		
		//generic add marker, will point to map_<custom_API>_addMarker()
		plugin.map_addMarker = function(info, self) {
			
			var marker;
			
			eval("marker = plugin.map_" + plugin.settings.omap.api + "_addMarker(info, self)");
			
			return marker;
		
		};
		
		//generic add marker, will point to map_<custom_API>_clearMarker()
		plugin.map_clearMarker = function(info, self) {
			
			eval("plugin.map_" + plugin.settings.omap.api + "_clearMarker(info, self)");
		
		};
		
		//generic add marker, will point to map_<custom_API>_closeInfoWindow()
		plugin.map_closeInfoWindow = function(info, self) {
			
			eval("plugin.map_" + plugin.settings.omap.api + "_closeInfoWindow(info, self)");
		
		};
		
		//generic get click coords, will point to map_<custom_API>_getClickCoords()
		plugin.map_getClickCoords = function(info) {
			
			eval("plugin.map_" + plugin.settings.omap.api + "_bindClickCoords(info)");
		
		};
		
		/* GOOGLE MAPS SPECIFIC METHODS */
		
		//google maps initial setup
		plugin.map_gmaps = function(settings) {
			
			var g = plugin.settings.omap.gmaps;
			
			if (settings) { g = $.extend(true, g, settings); }
			
			try { 
			
				//create centre of GMaps view
				g.options.center = new google.maps.LatLng(g.options.map_centre.lat, g.options.map_centre.lng);

				//initialise map
				plugin.internal._map_handle = new google.maps.Map(plugin.internal._map_container[0], g.options);

				//initialise mc
				plugin.internal._map_mc_handle = new MarkerClusterer(plugin.internal._map_handle, [], g.mc_options);
			
			} catch(e) {}
			
			//save settings
			plugin.settings.omap.gmaps = g;
			
			//return handle for further manipulation
			return plugin.internal._map_handle;
			
		};
		
		plugin.map_gmaps_markerGetSameCoords = function(coords, self) {
			
			var ref = self || plugin,
				markers = ref.internal._map_markers,
				mCoords;
			
			if (markers.length === 0) {
				return false;
			}
			
			for (var i = 0; i < markers.length; i++) {
				mCoords = markers[i].getPosition();
				if (coords.equals(mCoords)) {
					break;
				}
			}
			
			if (i === markers.length) {
				return false;
			}
			
			return i;
			
		};
		
		plugin.map_gmaps_getMarkerIcon = function(info, self) {
			
			var ref = self || plugin,
				icon, opacity = null, date, created, ageMinutes;
		
			date = new Date(); created = Date.parse(info.created);
			ageMinutes = (date.getTime() - created) / 1000 / 60;
			for (var threshold in ref.settings.ageThresholdOpacity) {
				if (ageMinutes < parseInt(threshold)) {
					opacity = ref.settings.ageThresholdOpacity[threshold]; break;
				}
			}
			if (!opacity) { opacity = ref.settings.ageThresholdOpacity["old"]; }

			icon = {
				path: ref.settings.iconShapes[info.smType],
				fillColor: ref.settings.iconColours[info.smType][info.sntmt],
				fillOpacity: opacity,
				scale: 0.8,
				strokeColor: "white",
				strokeWeight: 2
			};
			
			return icon;
			
		};
		
		//google maps add marker
		plugin.map_gmaps_addMarker = function(info, self) {
			
			var ref = self || plugin,
				g = ref.settings.omap.gmaps;
			
			//marker limit reached
			if (ref.internal._map_markers.length === g.noOfMarkers) {
				//remove first marker
				ref.map_gmaps_clearMarker(0, ref);
			}
			
			//get position of marker
			var marker,
				where = new google.maps.LatLng(info.lat, info.lng),
				existingMarkerPosition = ref.map_gmaps_markerGetSameCoords(where, ref),
				icon, setIntervalNow = false;
			
			if (info.editor === true) {
				icon = {
					url: "/images/map/none_none_yes_icon.png",
					size: new google.maps.Size(40, 40),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(10, 30)
				};
			} else {
				icon = ref.map_gmaps_getMarkerIcon({ "created": info.created, "smType": info.smType, "sntmt": info.sntmt });
				setIntervalNow = true;
			}
			
			if (existingMarkerPosition !== false) {
				
				ref.internal._map_marker_tooltips[existingMarkerPosition].appendContent(info.msg);
				
				marker = new google.maps.Marker({
					map: ref.internal._map_handle,
					animation: google.maps.Animation.DROP,
					position: where,
					title: info.user,
					icon: icon,
					zIndex: (-5000)
				});
				
				if (setIntervalNow) {
					setInterval(function(m, i, r) {
						m.setIcon(r.map_gmaps_getMarkerIcon(i));
					}, 1000 * 60, marker, { "created": info.created, "smType": info.smType, "sntmt": info.sntmt, "interval": true }, ref);
				}
				
				//add marker to mc
				ref.internal._map_temp_markers.push(marker);
				
				return ref.internal._map_markers[existingMarkerPosition];
			}
			
			//create marker
			marker = new google.maps.Marker({
				map: ref.internal._map_handle,
				animation: google.maps.Animation.DROP,
				position: where,
				title: info.user,
				icon: icon,
				zIndex: (ref.internal._map_markers.length + 1000)
			});
			
			if (setIntervalNow) {
				setInterval(function(m, i, r) {
					m.setIcon(r.map_gmaps_getMarkerIcon(i));
				}, 1000 * 60, marker, { "created": info.created, "smType": info.smType, "sntmt": info.sntmt/*, "interval": true*/ }, ref);
			}
			
			var id = ref.settings.id;
			
			if (info.msg !== undefined && info.msg.length > 0) {
				
				//configure tooltip
				var tooltipOptions = {
					marker: marker,// required
					marker_id: "job_" + id + "_marker_" + ref.internal._map_markers.length,
					marker_type: info.smType,
					marker_sentiment: info.sntmt,
					content: info.msg,// required
					cssClass: ref.internal._map_tooltip_class, // name of a css class to apply to tooltip
					newJobContainer: info.newJobContainer || null
				},

				//create tooltip
				tooltip = new Tooltip(tooltipOptions);

				//keep track of tooltips
				ref.internal._map_marker_tooltips.push(tooltip);
			
			}
			
			//add marker to mc
			//try { ref.internal._map_mc_handle.addMarker(marker); } catch(e) {}
			ref.internal._map_temp_markers.push(marker);
			
			//keep track of markers
			ref.internal._map_markers.push(marker);
			
			//used for external purposes
			return marker;
			
		};
		
		//google maps clear marker
		plugin.map_gmaps_clearMarker = function(id, self) {
			
			var ref = self || plugin,
				markers = ref.internal._map_markers;
			
			//pre check for user mistakes
			if (markers.length === 0 || id >= markers.length) {
				//console.log("Marker id is greater than total markers. id=" + id + ", total=" + markers.length);
				return;
			}
			
			//marker handle
			var m;
			
			if (id !== undefined) { //id given
				//get marker handle
				m = markers[id];
				//remove marker from MC
				ref.internal._map_mc_handle.removeMarker(m, false);
				//remove marker
				m.setMap(null);
				//remove marker from tracking array
				markers.splice(id, 1);
				//remove marker tooltip
				ref.internal._map_marker_tooltips.splice(id, 1);
			} else { //no id given
				//get marker handle and remove it from tracking array
				m = markers.shift();
				//remove marker from MC
				ref.internal._map_mc_handle.removeMarker(m, false);
				//remove marker tooltip
				ref.internal._map_marker_tooltips.shift();
				//remove marker
				m.setMap(null);
			}
			
			//update internal tracking of markers
			ref.internal._map_markers = markers;
			
		};
		
		//google maps close infoWindow
		plugin.map_gmaps_closeInfoWindow = function(id, self) {
			
			var ref = plugin || self,
				markers = ref.internal._map_markers;
			
			//pre check for user mistakes
			if (markers.length === 0 || id >= markers.length) {
				$.error("Marker id is greater than total markers. id=" + id + ", total=" + markers.length);
				return;
			}
			
			if (id) { //id given
				//close infoWindow
				markers[id].infoWindow.close();
			} else { //no id given
				//get marker handle
				var m = markers[0];
				//close infoWindow
				m.infoWindow.close();
			}
		};
		
		/**/
		//google maps get click coords
		plugin.map_gmaps_bindClickCoords = function(info, marker) {
			
			var precision = plugin.settings.omap.gmaps.precision;
			var m = marker;
			var _map = plugin.internal._map_handle;
			
			google.maps.event.addListener(plugin.internal._map_handle, "click", function(e) {
				var coords = { lat: e.latLng.lat().toFixed(precision), lng: e.latLng.lng().toFixed(precision) };
				$(info.lat).val(coords.lat);
				$(info.lng).val(coords.lng);
				_map.panTo(new google.maps.LatLng(coords.lat, coords.lng));
				m.setVisible(false);
				m.setPosition(new google.maps.LatLng(coords.lat, coords.lng));
				m.setVisible(true);
				DrawCircle();
				$(info.form).valid();
			});
			
		};
		
		//return full job specs based on id
		plugin.getSpecs = function(id) {
			
			plugin.internal._info = plugin.doThisSync("job/getSpecs", { jobType: plugin.settings.module, id: id });
			
			//return job info
			return plugin.internal._info;
			
		};
		
		//get job latest messages based on job id
		plugin.check = function(self) {
			
			var ref = self || plugin,
				updates,
				data = { 
					sent: ref.settings.sent, 
					jobType: ref.settings.module,
					id: ref.settings.id, /*refresh: ref.internal._refresh,*/
					maxItems: ref.settings.max_refresh_items,
					fetchedIds: JSON.stringify(ref.internal._fetched_ids)
				};
		
			$.ajax({
				async: true,
				type: "POST",
				cache: false,
				dataType: "json",
				url: ref.internal._php_connection + "/job/getNewMessages",
				data: data
			}).done(function(msg) {
				
				try {
				
					updates = msg;
			
					//update count
					if (updates.msgs instanceof Array) {
						
						if (updates.msgs.length > 0) {
							
							var currentId;
							
							for(var i = 0; i < updates.msgs.length; i++) {
								currentId = parseInt(updates.msgs[i].backend_id);
								ref.internal._fetched_ids.push(currentId);
							}
							
							ref.internal._updates = ref.internal._updates.concat(updates.msgs);
							$("#job_" + ref.settings.id + "_count").html(updates.total);
							
						}
					}
					
					if (window["distinctMapsComplete"].indexOf(ref.settings.id) === -1) {
						//trigger initial load complete event
						$("body").trigger({ type: "messagesInitialComplete", id: ref.settings.id });
					}

				} catch (e) { console.log(e); }
			
			});
			
		};
		
		//setTimeout - check the job every X seconds
		plugin.start = function() {
			
			//get references to the required function and plugin
			var checkFunction = plugin.check,
				self = plugin;
		
			if (self.internal._timer === null) {
				
				//get the new messages right away
				checkFunction(self);

				//set the timer
				self.internal._timer = setInterval(function() {
					//get the new messages
					checkFunction(self);

				}, self.settings.timeout);
				
				//self.startQueue();
			
			}
			
		};
		
		//setTimeout - check the job every X seconds
		plugin.startQueue = function() {
			
			//get references to the required function and plugin
			var self = plugin,
				parseFunction = self.parseMessages;

			parseFunction(self);
			
			if (plugin.internal._queue_timer === null) {
			
				//set the timer
				plugin.internal._queue_timer = setInterval(function() { parseFunction(self); }, plugin.settings.display_freq);
			
			}
			
		};
		
		//stop the job
		plugin.stop = function(self) {
			
			//get proper grasp of this
			var ref = self || plugin;
			
			//clear the job
			clearInterval(ref.internal._timer);
			
			ref.internal._timer = null;
			
		};
		
		//stop the disc
		plugin.stopQueue = function(self) {
			
			//get proper grasp of this
			var ref = self || plugin;
			
			//clear the job
			clearInterval(ref.internal._queue_timer);
			
			ref.internal._queue_timer = null;
			
		};
		
		plugin.pause = function() {
			
			var data = plugin.doThisSync("job/setStatus", { jobType: plugin.settings.module, id: plugin.settings.id, suspended: 1 }),
				status, message;
			
			try {
			
				if (data === true) { 
					status = "success";
					message = "Task successfully paused.";
					$("#job_" + plugin.settings.id + "_status").html("<span style='color: red'><i class='fa fa-times fa-lg'></i></span>");
					plugin.settings.suspended = 1;
				} else {
					status = "danger";
					message = "Task has not been successfully paused. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data;
			
		};
		
		plugin.unPause = function() {
			
			var data = plugin.doThisSync("job/setStatus", { jobType: plugin.settings.module, id: plugin.settings.id, suspended: 0 }),
				status, message;
			
			try {
			
				if (data === true) { 
					status = "success";
					message = "Task successfully resumed.";
					$("#job_" + plugin.settings.id + "_status").html("<span style='color: green'><i class='fa fa-check fa-lg'></i></span>");
					plugin.settings.suspended = 0;
					plugin.start();
					plugin.startQueue();
				} else {
					status = "danger";
					message = "Task has not been successfully resumed. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data;
			
		};
		
		plugin.parseMessages = function(self) {
				
			//get the new messages
			var ref = self || plugin,
				messages = ref.internal._updates,
				message,
				screenName,
				text, innerText,
				smType, smId,
				count = 0,
				profileImg, removeAmount, timeAgo;

			if (messages instanceof Array && messages.length > 0) {

				do {

					try {

						//get message and remove it from the queue
						message = messages.shift();
						
						if (!message.sentiment) { message.sentiment = "none"; }
						
						if (!message.user.klout) { 
							message.user.klout = {};
							message.user.klout.score = 0;
						}
						
						if (message.user.profile_image_url.indexOf("https:") > -1) {
							removeAmount = 6;
						} else if (message.user.profile_image_url.indexOf("http:") > -1) {
							removeAmount = 5;
						} else {
							removeAmount = 0;
						}
						
						profileImg = "http:" + message.user.profile_image_url.substr(removeAmount, message.user.profile_image_url.length);
						timeAgo = message.created_at.substr(4, message.created_at.length).replace("BST ", "");
						
						smType = message.smType;
						smId = message.smId;
						
						screenName = "<a href='http://" + smType + ".com/" + message.user.screen_name + "' target='_blank'>@" + message.user.screen_name + "</a>";
						
						innerText = Autolinker.link(message._o_text?message._o_text:message.text, { className: "innerLink" } );
						
						//put together bubble text
						text = "<div class='opheme-bubble-header'>\
								  	<div class='opheme-bubble-follower-name'>\
										" + screenName + "\
										<!--<span>\
									  		&nbsp;<img src='/images/map/smiley_" + message.sentiment + ".gif' alt='" + message.sentiment + "'>\
										</span>-->\
										" + (Math.round(message.user.klout.score) > 0?"<span class='opheme-bubble-klout-score'>\
											<img class='klout-score' src='/images/klout-score.png'>\
											<span class='klout-score-display'" + (message.user.klout.score<10?" style='left: -17px'":"") + ">" + Math.round(message.user.klout.score) + "</span>\
										</span>":"") + "\
								  	</div>\
								  	<div class='opheme-bubble-post-datetime'>\
										<abbr class='timeago' title='" + timeAgo + "'>" + timeAgo + "</abbr>\
								  	</div>\
								  	<div class='close-bubble'>\
										<i class='fa fa-times fa-lg'></i>\
								  	</div>\
								</div>\
								<div class='opheme-bubble-content'>\
								  	<div class='opheme-bubble-image'>\
										<img src='" + profileImg + "' height='48' width='48'>\
								  	</div>\
								  	<div class='opheme-bubble-text'>\
										" + innerText + "\
								  	</div>\
									<div class='opheme-bubble-buttons'>\
										<button class='btn btn-primary btn-xs' onclick='if (confirm(\"Are you sure you want to follow @" + message.user.screen_name + "?\") === true) { oph_" + ref.settings.id + ".smFollow(\"" + smType + "\", \"" + smId + "\", \"" + message.user.id + "\", \"" + message.user.screen_name + "\"); }'>\
									  		<i class='fa fa-twitter'></i> Follow\
										</button>\
										<button class='btn btn-primary btn-xs' onclick='createReplyForm(\"" + smType + "\", \"" + message.user.screen_name + "\", \"" + message.user.id + "\", \"" + message.id + "\", \"" + message.text.replace("\"", "&quot;").replace("'", "&apos;") + "\")'>\
											<i class='fa fa-reply'></i> Reply\
										</button>\
									</div>\
								</div>";

						//create marker for each
						ref.map_addMarker({
							lat: message.coords[0],
							lng: message.coords[1],
							user: message.user.screen_name,
							msg: text,
							sntmt: message.sentiment,
							smType: smType,
							created: timeAgo
						}, ref);
						
						//keep track of how many messages we parsed so far
						ref.internal._messagesForShareCount++;

					} catch (e) { console.log(e); }

				} while (++count < ref.settings.max_items && messages.length > 0);
				
				if (parseInt(ref.settings.suspended) === 1 && parseInt($("#job_" + ref.settings.id + "_count").html()) === ref.internal._messagesForShareCount) {
					ref.stop(ref); ref.stopQueue(ref);
				}

				//update internal message tracking
				ref.internal._updates = messages;
				
				//add markers to map MC
				try {
					ref.internal._map_mc_handle.addMarkers(ref.internal._map_temp_markers);
					ref.internal._map_temp_markers = [];
				} catch(e) {}
				
				if ($("#loader-element.map-loader").is(":visible")) { $("#loader-element.map-loader").fadeOut("slow"); }

			}

		};
		
		plugin.smFollow = function(smType, smId, user_id, screen_name) {
			
			var data, status, message;
			
			try {
				
				data = plugin.doThisSync("socialMedia/follow", { oUserId: plugin.settings.oUserId, userId: user_id, screen_name: screen_name, authKeyId: smId , smType: smType});
				
				if (data === true) { 
					status = "success";
					message = "User has been successfully followed.";
				} else {
					status = "danger";
					message = "User has not been successfully followed. Message from Twitter: " + data + ". Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
		};
		
		plugin.share = function() {
			
			var data, status, message;
			
			try {
				
				data = plugin.doThisSync("job/share", { jobType: plugin.settings.module, id: plugin.settings.id, messagesCount: plugin.internal._messagesForShareCount });
			
				if (data.status === true) { plugin.settings.shared = 1; }

				if (data.status === true) {
					status = "success";
					message = "Job has been successfully shared with the World! It has been assigned the following link (click on it once to highlight, then right click -> Copy):<br><br><span id='share-link' style='text-decoration: underline'>" + data.link + "<span><br><br>";
				} else {
					status = "danger";
					message = "Job has not been successfully shared. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = {}; data.status = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data.status;
			
		};
		
		plugin.shareToSM = function(msg, smType, smId) {
			
			var data, status, message;
			
			try {
				
				data = plugin.doThisSync("job/share", { jobType: plugin.settings.module, id: plugin.settings.id, toSM: true, smType: smType, smId: smId, message: msg, messagesCount: plugin.internal._messagesForShareCount });
			
				if (data.status === true) { plugin.settings.shared = 1; }

				if (data.status === true) {
					status = "success";
					message = "Job has been successfully shared with the World!";
				} else {
					status = "danger";
					message = "Job has not been successfully shared. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = {}; data.status = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data.status;
			
		};
		
		plugin.unShare = function() {
			
			var data, status, message;
			
			try {
				
				data = plugin.doThisSync("job/unShare", { jobType: plugin.settings.module, id: plugin.settings.id });
			
				if (data === true) {
					plugin.settings.shared = 0;
					status = "success";
					message = "Job has been successfully stopped from being shared with the World.";
				} else {
					status = "danger";
					message = "Job has not been successfully stopped from being shared. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data;
			
		};
		
		plugin.shareStatus = function() {
			return plugin.settings.shared;
		};
		
		plugin.sendReplyToSM = function(msg, smType, smId, usn, uId, mId, origMsg) {
			
			var data, status, message;
			
			try {
				
				data = plugin.doThisSync("socialMedia/sendReply", { oUserId: plugin.settings.oUserId, smType: smType, smId: smId, message: msg, usn: usn, uId: uId, mId: mId , origMsg: origMsg });

				if (data === true) {
					status = "success";
					message = "Message has been successfully sent!";
				} else {
					status = "danger";
					message = "Message has not been sent. Please contact Support for assistance.";
				}

				plugin.displayMessage(status, message);
			
			} catch (e) { data = false; plugin.displayMessage("danger", "Sorry, but the server decided to take a break. Please try again later."); }
			
			return data;
			
		};
		
		plugin.doThisSync = function(action, data) {
			
			var returnData;
			
			$.ajax({
				async: false,
				type: "POST",
				cache: false,
				dataType: "json",
				url: plugin.internal._php_connection + "/" + action,
				data: data
			}).done(function(msg) { returnData = msg; });
			
			return returnData;
			
		};
		
		plugin.displayMessage = function(status, message) {
			
			var alert = "<div class='container msgJs'>\
							<div class='row'>\
								<div class='col-md-12'>\
									<div id='message-container'>\
										<div class='yestouch alert alert-" + status + "'>\
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>\
											" + message + "\
										</div>\
									</div>\
								</div>\
							</div>\
						</div>";
			$(alert).insertBefore($("#job-container"));
			
		};
		
		plugin.replaceURLWithHTMLLinks = function(text) {
			var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
			return text.replace(exp, "<a href='$1' target='_blank'>$1</a>");
		};
		
		//check if a string is JSON - http://stackoverflow.com/questions/4295386/how-can-i-check-if-a-value-is-a-json-object
		plugin.isJsonString = function (str) { return (typeof ($.parseJSON(str)) === "object"); };
		
		//initiate plugin instance setup
		init();

	};
	
	//http://api.jquery.com/serializeArray/ - comment from Arjen Oosterkamp
	$.fn.serializeJSON = function() {
		var json = {};
		jQuery.map($(this).serializeArray(), function(n, i){
			json[n["name"]] = n["value"];
		});
		return json;
	};

})(jQuery);
