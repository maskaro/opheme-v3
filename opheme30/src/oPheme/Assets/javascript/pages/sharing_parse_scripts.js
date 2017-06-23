$(document).ready(function() {
	$("#loader-element.map-loader").fadeOut("slow");
});

if (messages instanceof Array && messages.length > 0) {
						
	var replaceURLWithHTMLLinks = function(text) {
			var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
			return text.replace(exp, "<a href='$1' target='_blank'>$1</a>");
		},
		markerGetSameCoords = function(coords) {
			var markers = window["uniqueMarkers"], mCoords;
			if (markers.length === 0) { return false; }
			for (var i = 0; i < markers.length; i++) { mCoords = markers[i].getPosition(); if (coords.equals(mCoords)) { break; } }
			if (i === markers.length) { return false; }
			return i;
		},
		gMapOptions = {
			center: new google.maps.LatLng(messages[0].coords[0], messages[0].coords[1]), zoom: 16, mapTypeId: google.maps.MapTypeId.ROADMAP,
			panControl: true, zoomControl: true, mapTypeControl: true,
			mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR, position: google.maps.ControlPosition.TOP_CENTER },
			scaleControl: true, streetViewControl: true, overviewMapControl: true
		},
		gMap = new google.maps.Map($("#myMap")[0], gMapOptions),
		gMClusterer = new MarkerClusterer(gMap, [], { gridSize: 30, maxZoom: 15, batchSize: 40 }),
		bounds = new google.maps.LatLngBounds(),
		where, marker, tooltipOptions, message, profileImg, uniqueMarkers = [], allMarkers = [], processedTooltips = [], existingMarkerPosition, timeAgo;

	do {

		try {

			message = messages.shift();

			if (!message.sentiment) { message.sentiment = "none"; }

			if (message.user.profile_image_url.indexOf("https:") > -1) {
				removeAmount = 6;
			} else if (message.user.profile_image_url.indexOf("http:") > -1) {
				removeAmount = 5;
			} else {
				removeAmount = 0;
			}
			
			profileImg = "http:" + message.user.profile_image_url.substr(removeAmount, message.user.profile_image_url.length);
			timeAgo = message.created_at.substr(4, message.created_at.length).replace("BST ", "");
			
			innerText = Autolinker.link(message._o_text?message._o_text:message.text, { className: "innerLink" } );
			
			text = "<div class='opheme-bubble-header'>\
						<div class='opheme-bubble-follower-name'>\
							<span>@" + message.user.screen_name + "</span>\
							<!--<span>\
								&nbsp;<img src='/images/map/smiley_" + message.sentiment + ".gif' alt='" + message.sentiment + "'>\
							</span>-->\
							" + ($.isNumeric(message.user.klout.score) && Math.round(message.user.klout.score) > 0?"<span class='opheme-bubble-klout-score'>\
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
							<img src='" + profileImg + "'>\
						</div>\
						<div class='opheme-bubble-text'>\
							" + innerText + "\
						</div>\
					</div>";

			where = new google.maps.LatLng(message.coords[0], message.coords[1]);
			existingMarkerPosition = markerGetSameCoords(where);
			
			var iconSize, iconAnchor,
				iconUrl = "/images/map/" + message.smType + "_" + message.sentiment + "_no_icon.png";
			
			iconSize = new google.maps.Size(25, 25);
			iconAnchor = new google.maps.Point(0, 0);

			marker = new google.maps.Marker({
				map: gMap,
				animation: google.maps.Animation.DROP,
				position: where,
				title: message.user.screen_name,
				//icon: "/images/map/" + message.sentiment + ".png",
				icon: {
					url: iconUrl,
					size: iconSize,
					origin: new google.maps.Point(0, 0),
					anchor: iconAnchor
				},
				zIndex: (existingMarkerPosition!==false?(messages.length - 1):(messages.length + 1))
			});

			if (existingMarkerPosition === false) {
				tooltipOptions = {
					marker: marker,
					marker_id: "marker_" + uniqueMarkers.length,
					content: text,
					cssClass: "_opheme_bubbleContainer"
				};
				tooltip = new Tooltip(tooltipOptions);
				bounds.extend(where);
				gMap.fitBounds(bounds);
				processedTooltips.push(tooltip);
				uniqueMarkers.push(marker);
				allMarkers.push(marker);
			} else {
				processedTooltips[existingMarkerPosition].appendContent(text);
				allMarkers.push(marker);
			}

		} catch (err) {}

	} while (messages.length > 0);
	
	gMClusterer.addMarkers(allMarkers);

}