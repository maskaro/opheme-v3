/*
adapted from script created by Mohamed <me[at]medelbou[dot]com>
http://medelbou.wordpress.com/2012/02/03/creating-a-tooltip-for-google-maps-javascript-api-v3/

Constructor for the tooltip
@ param options an object containing: marker(required), content(required) and cssClass(a css class, optional)
@ see google.maps.OverlayView()
*/

function Tooltip(options) {

   // Now initialize all properties.
    this.marker_ = options.marker;
	this.marker_id = options.marker_id;
	this.marker_type = options.marker_type;
	this.marker_sentiment = options.marker_sentiment;
    this.content_ = "<div class='bubble_individual'>" + options.content + "</div>";
    this.map_ = options.marker.get("map");
	this.cssClass_ = options.cssClass||null;
	this.newJobContainer = options.newJobContainer||null;
	
	//this.alreadySeen = false;

    //Explicitly call setMap on this overlay
    this.setMap(this.map_);
	var me = this;
	
	// Create the DIV and set some basic attributes.
    var div = document.createElement("DIV");
    div.style.position = "absolute";
	// Hide tooltip
	div.style.display = "none";
	if(this.cssClass_) {
		div.className += this.cssClass_;
	}
	
	if (this.marker_id) {
		div.id = this.marker_id;
	}
	
	// Set the overlay's div_ property to this DIV
    this.div_ = div;
	
	// Show tooltip on mouseover event.
	google.maps.event.addListener(me.marker_, "mouseover", function() {
		me.show();
	});
	google.maps.event.addListener(me.marker_, "mousedown", function() {
		me.show();
	});
	// Hide tooltip on mouseout event.
	google.maps.event.addListener(me.marker_, "mouseout", function() {
		me.hide();
	});
	//redraw on drag
	google.maps.event.addListener(me.map_, "dragend", function() {
		me.draw();
	});
	
}

// Now we extend google.maps.OverlayView()
Tooltip.prototype = new google.maps.OverlayView();

// onAdd is one of the functions that we must implement, 
// it will be called when the map is ready for the overlay to be attached.
Tooltip.prototype.onAdd = function() {};

// We here implement draw
Tooltip.prototype.draw = function() {

    // Position the overlay. We use the position of the marker
    // to peg it to the correct position, just northeast of the marker.
    // We need to retrieve the projection from this overlay to do this.
    var overlayProjection = this.getProjection();

    // Retrieve the coordinates of the marker
    // in latlngs and convert them to pixels coordinates.
    // We'll use these coordinates to place the DIV.
    var ne = overlayProjection.fromLatLngToContainerPixel(this.marker_.getPosition());

    // Position the DIV.
    this.div_.style.left = ne.x + "px";
    this.div_.style.top = ne.y + "px";
    
};

// We here implement onRemove
Tooltip.prototype.onRemove = function() {
    //this.div_.parentNode.removeChild(this.div_);
};

// Note that the visibility property must be a string enclosed in quotes
Tooltip.prototype.hide = function(now) {
    if (this.div_) {
		if (!now) {
			this._to = setTimeout(function(el) {
				$(el.div_).fadeOut(400);
				setTimeout(function(elem) {
					$(elem.div_).remove();
				}, 500, el);
			}, 2500, this);
		} else {
			$(this.div_).fadeOut(400);
			setTimeout(function(elem) {
				$(elem.div_).remove();
			}, 500, this);
		}
    }
};

Tooltip.prototype.show = function() {
	var me = this;
    if (me.div_) {
		//Attach content to the DIV.
		try {
			me.div_.innerHTML = me.content_;
			//var panes = me.getPanes();
			//panes.floatPane.appendChild(me.div_);
			me.map_.getDiv().parentNode.appendChild(me.div_);
			
			var widthComp = (me.newJobContainer===true?$("#newJobContainer #job-preview").width():$("#job-preview").width());
			var heightComp = (me.newJobContainer===true?$("#newJobContainer #job-preview").height():$("#job-preview").height());
			
			//overflows right
			if (parseInt($(me.div_).css("left")) + $(me.div_).width() > widthComp) {
				$(me.div_).css("left", (widthComp - $(me.div_).width() - parseInt($(me.div_).css("margin-left")) - 5) + "px");
			}
			//overflows bottom
			if (parseInt($(me.div_).css("top")) + $(me.div_).height() > heightComp) {
				$(me.div_).css("top", (heightComp - $(me.div_).height() - parseInt($(me.div_).css("margin-top")) - 5) + "px");
			}
			$("#" + me.marker_id + " .timeago").timeago();
			$("#" + me.marker_id + " .close-bubble").click(function() { me.hide(true); });
			//deal with tooltip mouse events
			//stop it from closing on mouse over
			$(me.div_).mouseenter(function() {
				window.clearTimeout(me._to); me._to = null;
			});
			//make it go away on mouse out
			$(me.div_).mouseleave(function() {
				me.hide(true);
			});
			
			$(".bubble_individual .opheme-bubble-text").each(function () {
				var $container = $(this),
					containerText = $container.html();
				if (containerText.indexOf("/-/ Image:") > -1) {
					var urlCount = 0;
					$container.children("a").each(function () {
						var url = $(this).attr("href"),
							$el = $(this);
						if (url.indexOf("opheme.com") > -1 && urlCount === 0) { // only do this for the first opheme.com link, second one would be a video, if any
							urlCount++;
							$el.tooltipster({
								content: $("<img src='" + url + "' style='max-width: 300px'>"),
								theme: "tooltipster-noir",
								animation: "fade",
								delay: 200,
								position: "bottom-left",
								touchDevices: false,
								trigger: "hover",
								maxWidth: "300px"
							});
						}
					});
				}
			});
			
			/*if (me.alreadySeen === false) {
				me.marker_.setIcon({
					url: "/images/map/" + me.marker_type + "_" + me.marker_sentiment + "_no_icon.png",
					size: new google.maps.Size(25, 25),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(0, 0)
				});
				me.alreadySeen = true;
			}*/
			
			//make it visible
			$(me.div_).fadeIn();
		} catch(e) {}
    }
};

Tooltip.prototype.appendContent = function(content) {
	this.content_ += "<div class='bubble_individual'>" + content + "</div>";
};