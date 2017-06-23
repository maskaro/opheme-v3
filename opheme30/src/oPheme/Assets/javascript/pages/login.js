$(".flipper").height(400); //hack to make the container have the expected height, without this it has 0px height

$("#login-button").click(function() {
	$(".flipper").animate({ height: "400px" }, "fast"); //hack to make the container have the expected height, without this it has 0px height
	$("#flip-toggle").toggleClass("flip");
	$.scrollTo(0, 400);
	setTimeout("$('#terms-content').fadeOut();",100);
	if ($("#forgot .panel-body").is(":visible")) { $("#forgot .panel-body").hide(); }
});

$("#register-button").click(function() {
	$(".flipper").animate({ height: "500px" }, "fast"); //hack to make the container have the expected height, without this it has 0px height
	$("#flip-toggle").toggleClass("flip");
	$.scrollTo(0, 400);
});

$("#forgot-header").next().hide(); //hide next div following header
$("#forgot-header").click(function() {
	var i, el = $(this);
	for (i = 0; i < 3; i++) { el = el.next().slideToggle('slow'); };
	$('.flipper').animate({ height: '470px' }, 'fast');
});

if (window.location.href.indexOf("register") >= 0) { $("div[href='#register-panel']").click(); }

$("#terms-content").hide();
$("#terms-button").click(function() {
	$("#terms-content").fadeToggle();
	$.scrollTo("#terms-content", 400);
});

$("form#login-form").validate({
	rules: {
		email: {
			required: true,
			email: true
		},
		password: "required"
	},
	messages: {
		email: "Please enter a valid email address.",
		password: "Please enter your password."
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
	}
});

if ($(".msgs-container").length) { $("form input:not([type=submit])").on("focus", function() { setTimeout(function() { $(".msgs-container").remove(); }, 5000); }); }