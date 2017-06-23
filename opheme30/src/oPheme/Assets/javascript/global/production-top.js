jQuery.validator.addMethod("selectcheck", function (value) {
	return (value !== "----------");
}, "Please select a valid option.");

jQuery.validator.addMethod("containsPercentR", function(value, element, param) {
	return this.optional(element) || value.toLowerCase().indexOf("%r") !== -1;
}, "You must enter %r");

jQuery.validator.addMethod("containsPercentC", function(value, element, param) {
	return this.optional(element) || value.toLowerCase().indexOf("%c") !== -1;
}, "You must enter %c");

jQuery.validator.addMethod("passwordCheck", function(value, element, param) {
   return /^(?=.*\d)(?=.*[a-zA-Z])(?!.*[\W_\x7B-\xFF]).{8,}$/.test(value);
}, "Your password must be at least 8 characters long and contain both letters and numbers.");

$.each(["show", "hide"], function (i, ev) {
	var el = $.fn[ev];
	$.fn[ev] = function () {
		this.trigger(ev);
		return el.apply(this, arguments);
	};
});

function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}

function hideAllBut(top_cls, cls, id) {
	$("." + cls + id).modal("toggle");
	/*if ($("." + cls + id).is(":visible")) {
		$("." + cls + id).modal("hide");
	} else {
		$("[class^=" + top_cls + "]").each(function() {
			$(this).modal("hide");
		});
		$("." + cls + id).modal("show");
	}*/
}

function hide(cls, id) {
	$("." + cls + id).fadeOut(300);
}

function capitalize (text) {
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
}