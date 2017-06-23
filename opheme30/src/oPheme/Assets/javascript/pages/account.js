if (screen.width > 568 && window["accountOK"] === false) { // not for iPhone
	window["tour"] = new Tour({
		backdrop: true,
		steps: [
		{
			title: "Welcome to " + brand + "!",
			content: "It is our greatest pleasure to welcome you to " + brand + "! Please take a few minutes to set up your account by following this step-by-step Tour Guide. <br><br>Once you are done with a step, please make sure you hit the Next button found at the bottom of each of these Guides. If you wish to go to a previous step, feel free to hit the Prev button at any time during the tour. If, on the other hand, you wish to end this Tour Guide before it's done, please hit the End tour button at any time. Thank you and enjoy!",
			orphan: true
		},
		{
			element: "#account-information",
			placement: "right",
			title: "Account Information",
			content: "Here you can view and manage your Account. You can set your personal details such as your name, phone number, business name and website, current location (we won't track you using this, we promise, it's only used to personalise your experience within our products), and change your password. For any change you make you will always require your current password, for security purposes. <br><br>All fields are required in order to use our products, so please go ahead and fill these fields in now and click the Save button at the bottom here.<br><br>When you're done, please hit the Next button below."
		},
		{
			element: "#authorisation-status",
			placement: "left",
			title: "Authorisation Status",
			content: "Here you can view and manage your social media accounts linked with your Account. Don't worry, we promise we won't do anything without your consent or request. We do, however, require you to link at least one account in order to use our products. Please go ahead and do this now by clicking the Authorise button above."
		},
		{
			element: "#current-jobs",
			placement: "right",
			title: "Campaign and Discover Overview",
			content: "Here you can see how many Campaigns and Discovers you have created, as well as some other relevant information.",
			path: "/dashboard"
		}
		]
	});
	tour.init();
	tour.start();
} else {
	$(".navbar i.fa-road").parent().parent().prev().remove();
	$(".navbar i.fa-road").parent().parent().remove();
}

$("form#account").validate({
	validClass: "alert alert-success",
	//validation rules
	rules: {
		"first_name": "required",
		"last_name": "required",
		phone: "required",
		"password_new": "passwordCheck",
		"password_confirm": {
			equalTo: "#password_new"
		}
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