define (
	[
		"app",
		"Controllers/base",
		"Views/pages/errors/404"
	], function ( app,
	              BaseController,
	              Error404View )
	{

		"use strict";

		return BaseController.extend (
			{

				name: "error",

				viewNames: [
					{
						action: "404", viewName: "Error404View"
					}
				],

				"404": function ( page, action )
				{

					if ( DEBUG ) {
						app.logThis ( [ "ErrorController.404 is running. Page: ", page, " / Action: ", action ] );
					}

					if ( !page ) {

						return app.router.showIndex ();

					}

					var errorView = new Error404View ();

					if ( page && page.length ) {

						errorView.pageRequested = page;

						if ( action && action.length ) {

							errorView.pageRequested += "/" + action;

						}

					}

					errorView.show ();

				}

			}
		);

	}
);