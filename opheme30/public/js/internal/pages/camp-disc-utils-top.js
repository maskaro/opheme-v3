//maps array filled with map handles and ids
window["maps_json"] = {};

window["distinctMapsComplete"] = [];

$(document).ready(function() {
	//try { $("#job-table tbody tr:first").click(); } catch (e) {}
	//$("#loader-element.map-loader").fadeOut("slow");
	$("body").on("messagesInitialComplete", function(ev) {
		if (window["distinctMapsComplete"].indexOf(ev.id) === -1) {
			window["distinctMapsComplete"].push(ev.id);
			if (window["distinctMapsComplete"].length === $("#job-table tbody tr").length) {
				$("#job-table tbody tr:first").click();
				$("#loader-element.map-loader").fadeOut("slow");
			}
		}
	});
	window["accountsSelectOptions"] = $("#replyToMsg select[name=authKeyId]").html();
});

// draws circle around pin on editor map
function DrawCircle() {
	
	var rad = $("input[name=radius]").val();
	
    rad *= 1637; // convert to meters if in Miles
	
    if (window["centre_circle"] !== null) {
        window["centre_circle"].setMap(null);
    }
	
    window["centre_circle"] = new google.maps.Circle({
        center: window["centre_marker"].getPosition(),
        radius: rad,
        strokeColor: "#0000FF",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#0000FF",
        fillOpacity: 0.35,
        map: window["map"]
    });
	
	window["centre_circle"].setVisible(true);
	
}