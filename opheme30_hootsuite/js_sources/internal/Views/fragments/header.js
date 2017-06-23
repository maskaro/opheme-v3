define (
	[
		"require",
		"jquery",
		"underscore",
		"backbone",
		"app",
		"textplugin!/templates/page_elements/common/_navigation.html",
		"textplugin!/templates/page_elements/common/_header.html"
	], function ( require,
	              $,
	              _,
	              Backbone,
	              app,
	              NavigationElementTpl,
	              HeaderElementTpl )
	{

		"use strict";

		return Backbone.View.extend (
			{

				initialize: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView.initialize is running." ] );
					}

				},

				events: {
					"click #navigation-container .btn-settings"            : "_navToggle",
					"click #navigation-container .btn-more"                : "_navToggle",
					"click .link-new-discover"                             : "_clickNewDiscover",
					"change #discoverSelect"                               : "_discoverSelectChanged",
					"click a.remove-discover"                              : "_clickRemoveDiscover",
					"click #retakeTour"                                    : "_clickRetakeTour"
				},

				_navToggle: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView._navToggle is running." ] );
					}

					app.eventPreventDefault(evt);

					var navContainer              = "#navigation-container",
					    $this                     = $ ( evt.currentTarget ),
					    dropdownDataValue         = $this.data ( "dropdown" ),
					    $previousButton           = $ ( navContainer + " .btn-" + dropdownDataValue.toLowerCase () ).filter ( ".active" ),
					    $previousDropdown         = $ ( navContainer + " .hs_topBarDropdown" ).filter ( ".active" ),
					    previousDropdownDataValue = "",
					    $currentDropdown;

					// Hide the previous drop down
					if ( $previousDropdown.length ) {

						previousDropdownDataValue = $previousDropdown.data ( "dropdown" );
						$previousDropdown.hide ().removeClass ( "active" );
						$previousButton.removeClass ( "active" );

					}

					// Show the drop down associated with the clicked control button
					if ( dropdownDataValue !== previousDropdownDataValue ) {

						$currentDropdown = $ ( navContainer + " .hs_dropdown" + dropdownDataValue );

						$this
							.addClass ( "active" );
						$currentDropdown
							.addClass ( "active" )
							.show ();

					}

				},

				_discoverSelectChanged: function ( evt )
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView._discoverSelectChanged is running." ] );
					}

					app.eventPreventDefault ( evt );

					var $select = $ ( evt.currentTarget ),
					    $target = $select.find ( "option:selected" ),
					    id      = $target.val ();

					if ( id !== "#" ) {

						app.session.selectCurrentDiscover ( id );

						// $("#discoverControls").show();
						app.router.navigate (
							"main-stream/view", {
								trigger: true,
								replace: false
							}
						);

					}
					else {

						app.session.resetCurrentDiscover ();

						$ ( "#discoverControls" ).hide ();

					}

				},

				_clickNewDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView._clickNewDiscover is running." ] );
					}

					// reset currentDiscover
					app.session.resetCurrentDiscover ();

				},

				_clickRemoveDiscover: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView._clickRemoveDiscover is running." ] );
					}

					app.startActivity ( "HeaderView._clickRemoveDiscover", $ ( "div.panel-title h3" ) );

					app.showModal (
						"Remove Discover", "Are you sure you want to remove this Discover?",
						null,
						function ()
						{

							var DiscoverController = require ( "Controllers/discovers" );

							(
								new DiscoverController ()
							).user_remove ();

						}, null, null, function ()
						{

							app.stopActivity ( "HeaderView._clickRemoveDiscover" );

						}
					);

				},

				_clickRetakeTour: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView._clickRetakeTour is running." ] );
					}

					if ( app.views.tour !== null ) {

						app.views.tour.init ();
						app.views.tour.restart ();
						app.views.tour.start ();

					}

				},

				render: function ()
				{

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView.render is running." ] );
					}

					var subTemplates = [],
					    data;

					this.template = _.template ( HeaderElementTpl );

					subTemplates.push (
						{
							"renderNavigation": _.template ( NavigationElementTpl )
						}
					);

					data = {
						data: {
							user           : app.session.storage.user.toJSON (),
							discovers      : app.session.storage.discovers,
							currentDiscover: app.session.storage.currentDiscover
						}
					};

					if ( DEBUG ) {
						app.logThis ( [ "HeaderView.render: Data: ", data ] );
					}

					_.each (
						subTemplates, function ( template )
						{
							data = _.extend ( data, template );
						}
					);

					this.$el.html ( this.template ( data ) );

					return this;

				}

			}
		);

	}
);
