window["oph"] = new $.oPhemeUI($("#map_preview"), { sModule: "none" });
window["coords_default_camb"] = { lat: 52.225550717210936, lng: 0.1366367567297857 };
window["map"] = null;
window["json"] = null;
window["centre_marker"] = null;
window["centre_circle"] = null;

var max_length = 110; //social media max message length

//map coordinates, defaults to user's home location
window["coords_default"] = codeLatLngFromAddress();

function codeLatLngFromAddress() {

	var result = window["coords_default_camb"],
		geocoder = new google.maps.Geocoder();
	
	geocoder.geocode({ address: window["homeLocation"] }, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				result.lat = results[0].geometry.location.lat();
				result.lng = results[0].geometry.location.lng();
			}
		}
	});
	
	return result;

}

//set up default map
window["map"] = window["oph"].map({
	api: "gmaps",
	settings: {
		options: {
			//initial map centre location
			map_centre: window["coords_default"],
			zoom: 13
		}
	}
});

window["centre_marker"] = window["oph"].map_addMarker({
	lat: window["coords_default"].lat,
	lng: window["coords_default"].lng,
	user: "Center of " + window["moduleName"],
	msg: "<div class='opheme-bubble-header'>\
			<div class='opheme-bubble-follower-name'>\
				Centre of " + window["moduleName"] + "\
			</div>\
			<div class='close-bubble' onclick='$(this.parentNode.parentNode).hide();'>\
				<i class='fa fa-times fa-lg'></i>\
			</div>\
		</div>\
		<div class='opheme-bubble-content'>\
			<div class='opheme-bubble-image'>\
				<img src='/images/phem_small.png'>\
			</div>\
			<div class='opheme-bubble-text' style='height: 80px'>\
				This is where the centre of the " + window["moduleName"] + " is now set. Tweets will be monitored around this within the set radius. To choose a new location, simply click/tap this pin and drag to a new position, or click outside the radius area.\
			</div>\
		</div>",
	sntmt: "none",
	newJobContainer: true,
	smType: "none",
	isNew: "yes",
	editor: true
});
window["centre_marker"].setVisible(false);
window["centre_marker"].setDraggable(true);
DrawCircle();
window["centre_circle"].setVisible(false);
google.maps.event.addListener(window["centre_marker"], "dragend", function() { DrawCircle(); });

//get coords on click
$("#click_get_client_coords").click(function() {
	if (navigator.geolocation) {
		$("#loader-element.free-for-all").insertBefore("#map-preview").fadeIn(300);
		//get coordinates
		navigator.geolocation.getCurrentPosition(function (position) {
			//trigger resize
			google.maps.event.trigger(window["map"], "resize");
			//google coords object
			var gc = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			//set form coords
			$("#centre_lat").val(gc.lat()); $("#centre_lng").val(gc.lng());
			//move map centre
			window["map"].panTo(gc);
			//move marker
			window["centre_marker"].setVisible(false);
			window["centre_marker"].setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
			window["centre_marker"].setVisible(true);
			DrawCircle();
			$("form#editor input[name=radius]").focus();
			$("form#editor").valid();
			$("#loader-element.free-for-all").fadeOut(300);
		});
	}
});

//bind click result to these elements
window["oph"].map_gmaps_bindClickCoords({ lat: "#centre_lat", lng: "#centre_lng", form: "form#editor" }, window["centre_marker"]);

//timepicker
$("#startTimeField").timepicker({
	scrollDefaultTime: "09:00",
	maxTime: "23:59",
	timeFormat: "H:i"
}).on("change", function() {
	$("#endTimeField").timepicker("option", "minTime", $("#startTimeField").val());
	if ($("#endTimeField").val().length === 0) { $("#endTimeField").focus(); }
	$("form#editor").valid();
});
$("#endTimeField").timepicker({
	scrollDefaultTime: "17:00",
	timeFormat: "H:i",
	maxTime: "23:59"
}).on("change", function() {
	if ($("#startDateField").val().length === 0 && moduleId === "campaign") { $("#startDateField").focus(); }
	$("form#editor").valid();
});

var nowTemp = new Date();
var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

var startDate = $("#startDateField").datepicker({ // scd start date
	format: 'yyyy-mm-dd',
	startDate: now,
	todayHighlight: true
}).on("changeDate", function() {
	$("#startDateField").datepicker("hide");
	$("#endDateField").datepicker("setStartDate", $("#startDateField").datepicker("getDate"));
	if ($("#endDateField").val().length === 0) { $("#endDateField").focus(); }
	$("form#editor").valid();
});
var endDate = $("#endDateField").datepicker({
	format: 'yyyy-mm-dd',
	startDate: now,
	todayHighlight: true
}).on("changeDate", function() {
	$("#endDateField").datepicker("hide");
	$("form#editor").valid();
});

$("form#editor input[name=time_start]").change(function() { $("form#editor").valid(); });
$("form#editor input[name=time_end]").change(function() { $("form#editor").valid(); });

var tickNow = true;
var tickDays = function() {
	$("form#editor input[name='days[]']").each(function (index, val) {
		$(val).prop("checked", tickNow);
	});
	$("#day-all").prop("checked", tickNow);
	$("form#editor").valid();
	tickNow = !tickNow;
};

//$("#tickAllDays").click(function () { tickDays(); });
//$("#day-all").click(function () { tickDays(true); });
$("form#editor .everyday").click(function () { tickDays(); });

$("form#editor input[name='days[]']").change(function () {
	var sum = 0;
	$("form#editor input[name='days[]']:not([id='day-all'])").each(function () { if (this.checked) { sum++; } });
	if (sum === 7) {
		$("#day-all").prop("checked", true);
		tickNow = false;
	} else {
		$("#day-all").prop("checked", false);
		tickNow = true;
	}
});

for (var authKey in window["authKeys"]) { bindSMAccountChoice(authKey); }
function bindSMAccountChoice(smType) {
	$("form#editor input[name='authKeyUse_" + smType + "']").change(function() {
		if ($("form#editor input[name='authKeyUse_" + smType + "']").is(":checked")) {
			if ($("#authKeys_" + smType).children().length > 1) {
				$("#authKeys_" + smType).show();
			} else {
				$("#authKeys_" + smType + " option").attr("selected", "selected");
			}
		} else {
			$("#authKeys_" + smType + " option").removeAttr("selected");
			if ($("#authKeys_" + smType).children().length > 1) {
				$("#authKeys_" + smType).hide();
			}
		}
		$("form#editor").valid();
	});
}

if (moduleId === "campaign") {
	
	$("#slider-range-max").slider({
		range: "max",
		min: 1,
		max: 6,
		value: 1,
		slide: function(event, ui) {
			$("input[name=hourly_limit]").val(ui.value);
		}
	});
	
	$("#slider-range-radius").slider({
		range: "max",
		min: 0.1,
		max: 1,
		step: 0.1,
		slide: function(event, ui) {
			$("input[name=radius]").val(ui.value);
			if (window["centre_circle"].getVisible()) { DrawCircle(); }
		}
	});
	
	window["formValidator"] = $("form#editor").validate({
		rules: {
			"authKey_twitter[]": {
				required: "#authKeyUse_twitter:checked"
			},
			"authKey_instagram[]": {
				required: "#authKeyUse_instagram:checked"
			},
			authKeyUse_twitter: {
				required: {
					depends: function() {
						return $('.sm-require-one:checked').size() === 0;
					}
				}
			},
			authKeyUse_instagram: {
				required: {
					depends: function() {
						return $('.sm-require-one:checked').size() === 0;
					}
				}
			},
			name: {
				required: true,
				minlength: 5
			},
			category: {
				selectcheck: true
			},
			hourly_limit: {
				number: true,
				min: 1,
				max: 6
			},
			text: {
				required: true,
				minlength: 5
			},
			response_text: {
				required: true,
				minlength: 10,
				maxlength: max_length,
				containsPercentR: true,
				containsPercentC: true
			},
			filter: {
				minlength: 1
			},
			filter_ex: {
				minlength: 1
			},
			"days[]": "required",
			time_start: {
				required: true,
				time: true
			},
			time_end: {
				required: true,
				time: true
			},
			date_start: {
				required: true,
				date: true
			},
			date_end: {
				required: true,
				date: true
			},
			centre_lat: {
				required: true,
				number: true
			},
			centre_lng: {
				required: true,
				number: true
			},
			radius: {
				required: true,
				number: true,
				min: 0.1,
				max: 1
			}
		},
		messages: {
			"authKey_twitter[]": "Please select a Twitter Account for this Campaign.",
			"authKey_instagram[]": "Please select an Instagram Account for this Campaign.",
			authKeyUse_twitter: "Please choose at least one Social Media Account type.",
			authKeyUse_instagram: "Please choose at least one Social Media Account type.",
			name: "Please enter a name for this Campaign, at least 5 characters.",
			category: "Please select a Category for this Campaign.",
			hourly_limit: "Please select a limit for Campaign between 1 and 6 responses per Hour.",
			text: {
				required: "Your Campaign Tweet will contain a link to this message.",
				minlength: "Please write at least 5 characters."
			},
			response_text: {
				required: "This is the Tweet your recipients will receive, can't be empty.",
				minlength: "Please write at least 10 characters.",
				maxlength: "Please write up to " + max_length + " characters.",
				containsPercentR: "You MUST use the %r in your Tweet.",
				containsPercentC: "You MUST use the %c in your Tweet."
			},
			filter: {
				minlength: "Please enter at least 5 characters."
			},
			filter_ex: {
				minlength: "Please enter at least 5 characters."
			},
			"days[]": "At least one day must be selected.",
			time_start: "You must specify a valid start time of the day.",
			time_end: "You must specify a valid end time of the day.",
			date_start: "You must specify a valid start date.",
			date_end: "You must specify a valid end date.",
			centre_lat: "",
			centre_lng: "",
			//centre_lng: "You must specify a valid Longitude coordinate.",
			radius: "You must specify a valid radius for this Campaign (Miles), ranging from 0.1 to 1 mile."
		},
		highlight: function(element) {
			$(element).closest(".form-group").addClass("has-error");
		},
		unhighlight: function(element) {
			$(element).closest(".form-group").removeClass("has-error");
		},
		errorElement: "span",
		errorClass: "help-block",
		errorPlacement: function(error, element) {
			if(element.parent(".input-group").length) {
				error.insertAfter(element.parent());
			} else if ((element).parent().hasClass("checkbox-wrapper")) {
	            error.insertAfter(".checkbox-container");
	        } else {
				error.insertAfter(element);
			}
		},
		ignore: ""
	});
	
	//wizard settings
	window["sections"] =	["info",			"filters",		"user",					"campaign", 		"running", 			"location"];
	window["finalRequiredSection"] = "location";
	window["titles"] =		["General Info",	"Search Terms",	"Recipient", 	"Campaign", "Date and Time", 	"Location"];
	window["formFields"] = {
		section_info: [ "authKey_twitter[]", "authKey_instagram[]", "name", "category", "hourly_limit" ],
		section_filters: [ "filter", "filter_ex" ],
		section_user: [ "response_text" ],
		section_campaign: [ "text" ],
		section_running: [ "days[]", "time_start", "time_end", "date_start", "date_end" ],
		section_location: [ "centre_lat", "centre_lng", "radius" ]
	};
	
	$("#text").keyup(function() {
		$("#preview_text").html($(this).val());
	});

	$("#response_text").keyup(function() {
		var chars = $(this).val();
		if (max_length - chars.length === -1) $("#response_text_count").html("0");
		else $("#response_text_count").html((max_length - chars.length));
		if (chars.length > max_length) {
			$(this).val(chars.substring(0, max_length));
		}
		if (chars.indexOf("%r") === -1) {
			$("#response_text_percent_r").attr("style", "color: red");
		} else {
			$("#response_text_percent_r").attr("style", "color: rgb(73, 73, 73)");
		}
		if (chars.indexOf("%c") === -1) {
			$("#response_text_percent_c").attr("style", "color: red");
		} else {
			$("#response_text_percent_c").attr("style", "color: rgb(73, 73, 73)");
		}
		var preview = chars.replace("%r", "@Username");
		var preview = preview.replace("%c", window["companyName"]);
		$("#response_preview").html(preview);
	});

	//preview banner
	if (window.File && window.FileReader && window.FileList) {
		$("#banner").change(function() {
			var input = this;
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$("#preview_banner").attr("src", e.target.result);
					$("#preview_banner_container").show("slow");
				};
				reader.readAsDataURL(input.files[0]);
			}
		});
	}
	
} else if (moduleId === "discover") {
	
	$("#slider-range-radius").slider({
		range: "max",
		min: 0.1,
		max: 10,
		step: 0.1,
		slide: function(event, ui) {
			$("input[name=radius]").val(ui.value);
			if (window["centre_circle"].getVisible()) { DrawCircle(); }
		}
	});
	
	window["formValidator"] = $("form#editor").validate({
		rules: {
			"authKey_twitter[]": {
				required: "#authKeyUse_twitter:checked"
			},
			"authKey_instagram[]": {
				required: "#authKeyUse_instagram:checked"
			},
			authKeyUse_twitter: {
				required: {
					depends: function() {
						return $('.sm-require-one:checked').size() === 0;
					}
				}
			},
			authKeyUse_instagram: {
				required: {
					depends: function() {
						return $('.sm-require-one:checked').size() === 0;
					}
				}
			},
			name: "required",
			filter: {
				minlength: 1
			},
			filter_ex: {
				minlength: 1
			},
			time_start: {
				time: true,
				required: {
					depends: function() {
						if ($("#endTimeField").val().length > 0){ return true; }
						else { return false; }
					}
				}
			},
			time_end: {
				time: true,
				required: {
					depends: function() {
						if ($("#startTimeField").val().length > 0){ return true; }
						else { return false; }
					}
				}
			},
			date_start: {
				date: true,
				required: {
					depends: function() {
						if ($("#endDateField").val().length > 0){ return true; }
						else { return false; }
					}
				}
			},
			date_end: {
				date: true,
				required: {
					depends: function() {
						if ($("#startDateField").val().length > 0){ return true; }
						else { return false; }
					}
				}
			},
			centre_lat: {
				required: true,
				number: true
			},
			centre_lng: {
				required: true,
				number: true
			},
			radius: {
				required: true,
				number: true,
				min: 0.1,
				max: 10
			},
			messageLifeSpanLimit: "required"
		},
		messages: {
			"authKey_twitter[]": "Please select a Twitter Account for this Discover.",
			"authKey_instagram[]": "Please select an Instagram Account for this Discover.",
			authKeyUse_twitter: "Please choose at least one Social Media Account type.",
			authKeyUse_instagram: "Please choose at least one Social Media Account type.",
			name: "Please enter a name for this Discover.",
			filter: {
				minlength: "Please enter at least 5 characters."
			},
			discover_filter_ex: {
				minlength: "Please enter at least 5 characters."
			},
			time_start: "You must specify a valid start time of the day.",
			time_end: "You must specify a valid end time of the day.",
			date_start: "You must specify a valid start date.",
			date_end: "You must specify a valid end date.",
			centre_lat: "",
			centre_lng: "",
			//centre_lng: "You must specify a valid Longitude coordinate.",
			radius: "Radius for this Discover (Miles) should range between 0.1 and 10 miles.",
			messageLifeSpanLimit: "You must choose one of the presented Message Types."
		},
		highlight: function(element) {
			$(element).closest(".form-group").addClass("has-error");
		},
		unhighlight: function(element) {
			$(element).closest(".form-group").removeClass("has-error");
		},
		errorElement: "span",
		errorClass: "help-block",
		errorPlacement: function(error, element) {
			if(element.parent(".input-group").length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		},
		ignore: ""
	});
	
	//wizard settings
	window["sections"] =	["info",			"location",		"filters",			"running"];
	window["finalRequiredSection"] = "location";
	window["titles"] =		["General Info",	"Location",		"Search Terms",		"Date and Time"];
	window["formFields"] = {
		section_info: [ "authKey_twitter[]", "authKey_instagram[]", "name", "messageLifeSpanLimit" ],
		section_filters: [ "filter", "filter_ex" ],
		section_running: [ "days[]", "time_start", "time_end", "date_start", "date_end" ],
		section_location: [ "centre_lat", "centre_lng", "radius" ]
	};
	
}

$("form#editor").on("submit", function() { return window["formValidator"].valid(); });

var wizardFirst = function() { window["formValidator"].resetForm(); $(".wizard-footer").hide(); $("[id^=wizard_]").each(function(index, el) { if (index !== 0) { $(el).hide(); } else { $(el).show("slow"); } }); };

var wizardify = function() {

	//Wizard Navigation Start
	var nav_header = 
			"<div id='wizard-navigation'>\
				<ul class='%whichOne pagination pagination-centered'>",
		nav_footer = 
			"	</ul>\
			</div>",
		nav_current = 
			"<li>\
				<a href='' class='current-page' onclick='return false;'>\
					<strong>%currTitle</strong>\
				</a>\
			</li>",
		nav_start = 
			nav_header + 
			nav_current + 
			"<li onclick='return wizardNext(this)' data-nextsection='%next' data-currentsection='%currNext'>\
				<a href='' onclick='return false;'>\
					%nextTitle <i class='fa fa-angle-right fa-lg'></i>\
				</a>\
			</li>" + 
			nav_footer,
		nav_mid = 
			nav_header + 
			"<li onclick='return wizardPrev(this)' data-prevsection='%prev' data-currentsection='%currPrev'>\
				<a href='' onclick='return false;'>\
					<i class='fa fa-angle-left fa-lg'></i> %prevTitle\
				</a>\
			</li>" + 
			nav_current + 
			"<li onclick='return wizardNext(this)' data-nextsection='%next' data-currentsection='%currNext'>\
				<a href='' onclick='return false;'>\
					%nextTitle <i class='fa fa-angle-right fa-lg'></i>\
				</a>\
			</li>" + 
			nav_footer,
		nav_end = nav_header + 
			"<li onclick='return wizardPrev(this)' data-prevsection='%prev' data-currentsection='%currPrev'>\
				<a href='' onclick='return false;'>\
					<i class='fa fa-angle-left fa-lg'></i> %prevTitle\
				</a>\
			</li>" + 
			nav_current + 
			nav_footer,
		nav, elem;
	//Wizard Navigation End
	
	for (var index = 0; index < window["sections"].length; index++) {
		
		switch (index) {
			//first
			case 0:
				nav = nav_start.replace("%next", window["sections"][1]);
				nav = nav.replace("%nextTitle", window["titles"][1]);
				nav = nav.replace("%whichOne", "start");
				break;
			//last
			case (sections.length - 1):
				nav = nav_end.replace("%prev", window["sections"][sections.length - 2]);
				nav = nav.replace("%prevTitle", window["titles"][titles.length - 2]);
				nav = nav.replace("%whichOne", "finish");
				break;
			//all others
			default:
				nav = nav_mid.replace("%next", window["sections"][index + 1]);
				nav = nav.replace("%nextTitle", window["titles"][index + 1]);
				nav = nav.replace("%prev", window["sections"][index - 1]);
				nav = nav.replace("%prevTitle", window["titles"][index - 1]);
				nav = nav.replace("%whichOne", "");
				break;
		}

		nav = nav.replace("%currTitle", window["titles"][index]);
		nav = nav.replace("%currPrev", window["sections"][index]);
		nav = nav.replace("%currNext", window["sections"][index]);
		
		elem = $("[id$=" + window["sections"][index] + "]");
		elem.append(nav);

		if (index !== 0) { elem.hide(); }
		
	}

};

var sectionIsClear = function(id, errors) {
	var check = window["formFields"]["section_" + id];
	for (var i = 0; i < check.length; i++) {
		if (check[i] in errors) {
			return false;
		}
	}
	return true;
};

var wizardNext = function(el) {
	$("form#editor").valid();
	var nextSection = $(el).data("nextsection"), currentSection = $(el).data("currentsection"),
		formErrorFields = $("form#editor").validate().errorMap;
	if (sectionIsClear(currentSection, formErrorFields) === false) {
		return false;
	}
	$("#wizard_" + currentSection).hide("slow");
	$("#wizard_" + nextSection).show("slow");
	$("html, body").animate({ scrollTop: 0 }, "slow");
	/*if (nextSection === "location") {
		google.maps.event.trigger(window["map"], "resize");
		window["map"].panTo(new google.maps.LatLng(window["coords"].lat, window["coords"].lng));
	}*/

	if (nextSection === window["finalRequiredSection"]) {
		$(".wizard-footer").show();
		/*if (window["sections"].indexOf(nextSection) === (window["sections"] - 1)) { //next section is final one in the list
			
		}*/
	}
	return false;
};

var wizardPrev = function(el) {
	var prevSection = $(el).data("prevsection"), currentSection = $(el).data("currentsection");
	$("#wizard_" + currentSection).hide("slow");
	$("#wizard_" + prevSection).show("slow");
	if ($(".wizard-footer").is(":visible") && prevSection !== window["finalRequiredSection"] && window["sections"].indexOf(prevSection) < window["sections"].indexOf(window["finalRequiredSection"])) {
		$(".wizard-footer").hide();
	}
	$("html, body").animate({ scrollTop: 0 }, "slow");
	return false;
};

wizardify();