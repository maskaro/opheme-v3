$("form#new-reg-token").validate({
	errorClass: "alert alert-danger",
	validClass: "alert alert-success",
	//validation rules
	rules: {
		email: {
			required: true,
			email: true
		}
	}
});

$("table").each(function() {
	if ($(this).children("tbody").children("tr").children("td").length > 1) {
		$(this).dataTable();
	}
});

$("[class^=jobs_]").each(function() {
	$(this).modal("hide");
});


$("#new-account-container").on("hide.bs.collapse", function () {
	$("#new-account-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#new-account-container").on("show.bs.collapse", function () {
	$("#new-account-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#unused-accounts-container").on("hide.bs.collapse", function () {
	$("#unused-accounts-toggler i").toggleClass("fa-angle-down fa-angle-up");
});

$("#unused-accounts-container").on("show.bs.collapse", function () {
	$("#unused-accounts-toggler i").toggleClass("fa-angle-down fa-angle-up");
});