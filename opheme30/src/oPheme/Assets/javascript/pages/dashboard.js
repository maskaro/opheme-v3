if (screen.width > 568 && window["accountOK"] === false) { // not for iPhone
	window["tour"] = new Tour({
		backdrop: true,
		steps: [
		{
			element: "#current-jobs",
			placement: "right",
			title: "Campaign and Discover Overview",
			content: "Here you can see how many Campaigns and Discovers you have created, as well as some other relevant information."
		},
		{
			element: "#subscription-info",
			placement: "left",
			title: "Subscription Status",
			content: "Here you can view and manage your current Subscription level. You can see information such as Limits the Subscription level emposes on your Account."
		},
		{
			element: "#wizard-menu-discovers",
			placement: "bottom",
			title: "Discover Management Area",
			content: "Here you can manage all your Discovers. Create, View, Share, Edit, and Remove at your heart's content. You can use this to bring yourself up to speed with what people are talking about anywhere around the world, within a 10 mile radius of your location of interest. Your chosen social media account at the point of Discover creation will dictate what messages we retrieve for your viewing pleasure. All of this, on a handy Google Maps map!",
			onShow: function(tour) {
				$(".navbar-fixed-top").css("z-index", "10300");
			},
			onHidden: function(tour) {
				$(".navbar-fixed-top").css("z-index", "1030");
			}
		},
		{
			element: "#wizard-menu-campaigns",
			placement: "bottom",
			title: "Campaign Management Area",
			content: "Here you can manage all your Campaigns. Same as with the Discover Management Area, you can Create, View, Share, Edit, and Remove stuff at will. You can use this to send messages to people in an area around your location of interest using your chosen social media account at the point of Campaign creation. Then, you can visualise your Campaign's reach using the handy Google Maps map!",
			onShow: function(tour) {
				$(".navbar-fixed-top").css("z-index", "10300");
			},
			onHidden: function(tour) {
				$(".navbar-fixed-top").css("z-index", "1030");
			}
		},
		{
			element: "#wizard-menu-settings",
			placement: "bottom",
			title: "Account Settings",
			content: "Here you can manage your Account. You can change your personal details, link or delink Social Media accounts, ask for Support, Logout, or even start this Tour Guide up again (accessible only from within the Dashboard or Account Areas though).",
			onShow: function(tour) {
				$(".navbar-fixed-top").css("z-index", "10300");
			},
			onHidden: function(tour) {
				$(".navbar-fixed-top").css("z-index", "1030");
			}
		},
		{
			title: "That's it!",
			content: "Thank you for taking the time to follow this step-by-step Tour Guide! We're you'll have an amazing time using " + brand + "! If, however, for whatever reason, you are not happy with our products or require assistance, please do not hesitate to contact us using the Support link found in the Account Settings menu, but we're sure you already knew that. Have a nice day!",
			orphan: true
		}
		]
	});
	if (parseInt(window["localStorage"].tour_current_step) > 0 && !window["localStorage"].tour_end) { window["tour"].restart(); }
	tour.init();
	tour.start();
} else {
	$(".navbar i.fa-road").parent().parent().prev().remove();
	$(".navbar i.fa-road").parent().parent().remove();
}