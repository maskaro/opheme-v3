$("form#register-form").validate({
	rules: {
		//token: "required",
		email: {
			required: true,
			email: true
		},
		first_name: "required",
		password: {
			required: true,
			passwordCheck: true
		},
		"password_confirm": {
			required: true,
			equalTo: "#password"
		},
		terms: "required"
		//captcha_code: "required"
	},
	messages: {
		//token: "Please enter the Secret Token given to you.",
		email: "Please enter a valid email address.",
		first_name: "Please enter your First Name so we can better presonalise your experience.",
		password: {
			required: "Please enter your password.",
			passwordCheck: "Your password needs to have at least 8 characters and contain both numbers and letters."
		},
		"password_confirm": {
			required: "Please enter your password again.",
			equalTo: "Please enter same password as above."
		},
		terms: "You must agree to our Terms and Conditions."
		//captcha_code: "Please enter the captcha code as seen in the image below."
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