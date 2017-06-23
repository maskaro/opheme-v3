// tooltip helper function
$(".more-info-tooltip").hover(function() {
	$(this).tooltip("toggle");
});

$(".map-more-info-tooltip").hover(function() {
	$(this).tooltip({ placement: "bottom" });
	$(this).tooltip("toggle");
});

/*setTimeout(function() {
	$(".yestouch.alert.alert-success").remove();
	$(".yestouch.alert.alert-info").remove();
	$(".yestouch.alert.alert-warning").remove();
	$(".yestouch.alert.alert-danger").remove();
}, 120000);*/

//autologout after 30min and 10sec
setTimeout(function() {
	location.reload();
}, 1810000);

$('a[href="' + this.location.pathname + '"]').parent().addClass('active');