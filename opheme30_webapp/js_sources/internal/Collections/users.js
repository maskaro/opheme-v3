// Filename: models/project
define([
	"underscore",
	"backbone",
	"Models/user"
], function (_, Backbone, UserModel) {

	var UserCollection = Backbone.Collection.extend({
		model: UserModel
	});

	// You don't usually return a collection instantiated
	return UserCollection;

});