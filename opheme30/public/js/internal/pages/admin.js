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

$("form#new-company").validate({
	errorClass: "alert alert-danger",
	validClass: "alert alert-success",
	//validation rules
	rules: {
		compId: "required",
		users: "required",
		modules: "required"
	}
});


// Display MB values in on ohver tooltips
$("#ram-check h3").tooltip();

$("#system-counter .counter").each(function(){
    var num = this.innerHTML.replace(/\D/g, "");
    var result;
    
    if (num.length < 4)
        return true;
    else if (num.length > 3 && num.length < 7)   
        result = num.substr(0, num.length - 3) + "k";
    else if (num.length > 6 && num.length < 10)   
        result = num.substr(0, num.length - 6) + "m";
    
    $(this).data("previous-value", this.innerHTML);
    this.innerHTML = result; 
});

$("#new-company-account-container").on("hide.bs.collapse", function () {
	$("#new-company-account-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#new-company-account-container").on("show.bs.collapse", function () {
	$("#new-company-account-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#companies-overview-container").on("hide.bs.collapse", function () {
	$("#companies-overview-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#companies-overview-container").on("show.bs.collapse", function () {
	$("#companies-overview-toggler i").toggleClass("fa-angle-down fa-angle-up");
});