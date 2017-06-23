$("form#new-reg-token").validate({
	errorClass: "alert alert-danger",
	validClass: "alert alert-success",
	//validation rules
	rules: {
		email: {
			required: true,
			email: true
		}
	},
	messages: {
		email: "Please enter a valid email address."
	}
});