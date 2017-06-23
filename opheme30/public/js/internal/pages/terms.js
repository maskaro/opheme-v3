$(".back-up").click(function() {
	$.scrollTo( 0, 400 );
	setTimeout("$('#terms-content').fadeToggle();",200);
});