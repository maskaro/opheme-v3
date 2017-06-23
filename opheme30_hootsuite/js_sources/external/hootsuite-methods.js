/**
 * Doc Written by the Opheme Team 2015
 */

/**
 * Hootsuite JDK API
 */
hsp = {};

/**
 * Assign some textual message content to a Hootsuite user
 * @param {{messageId: string, messageAuthor: string, messageAuthorAvatar: string, message: string, timestamp: string}} item
 */
hsp.assignItem = function(item) {};

/**
 * Registers a Hootsuite dashboard event to be handled with the app and an associated event handler
 * @param {string} eventName Name of the event to handle, can be one of the following: closepopup - Fires when a custom popup opened by your app stream is closed; dropuser - Fires when a user drags & drops a Twitter user avatar from the Hootsuite dashboard into your app stream; refresh - Fires when the app column is refreshed, either by the user within the stream, or as part of a dashboard refresh; sendtoapp - Registering for this event will cause users to see a menu item reading Send to "app stream"...in Twitter and Facebook stream message menus. This event is fired when users click on the menu item.
 * @param {function} callback Function to handle the callback, the function's parameters are determined by the event. See the events section for details on expected callback formats.
 */
hsp.bind = function(eventName, callback) {};

/**
 * Removes all currently visible status messages
 */
hsp.clearStatusMessage = function() {};

/**
 * Closes the modal pop-up dialog
 * @param {string} apiKey Your app's API Key
 * @param {string} pid The pid is associated with each unique installation of an app stream (or plugin). You can get the pid of each app stream from the url request your server gets. For example: https://demo.ca/stream.html?lang=en&theme=blue_steel&timezone=-25200&pid=60956&uid=136
 */
hsp.closeCustomPopup = function(apiKey, pid) {};

/**
 * Opens the Share to Social Networks dialog
 * @param {string} message Twitter message rules apply, eg. to compose a DM to @Hootsuite would be: d hootsuite <your message here>
 * @param {{shortenLinks: bool, timestamp: bool|int, replyToId: string}=} params
 */
hsp.composeMessage = function(message, params) {};

/**
 * Opens a user info popup window
 * @param {{fullName: string, screenName: string, avatar: string, profileUrl: string, userLocation: string, bio: string, extra: [{ label: string, value: string }], links: [{ label: string, url: string }] }} data
 */
hsp.customUserInfo = function(data) {};

/**
 * Gets a list of all Twitter profiles for the logged-in user
 * @param {function} callback
 */
hsp.getTwitterAccounts = function(callback) {};

/**
 * Gets a list of all Instagram profiles for the logged-in user
 * @param {function} callback
 */
hsp.getInstagramAccounts = function(callback) {};

/**
 * Initializes the JS API
 * @param {{apiKey: string, receiverPath: string, subtitle: string=, callBack: function=, sendToAppDisableList: Array=, sendProfileToAppDisableList: Array=}} params
 */
hsp.init = function(params) {};

/**
 * Item resolved in app stream
 * @param {{assignmentId: string}} item ID of the assignment in Hootsuite dashboard from both callbacks
 */
hsp.resolveItem = function(item) {};

/**
 * Starts retweet dialog process
 * @param {string} id Twitter's id_str tweetId for the tweet to retweet
 * @param {string} screenName The Twitter screen_name you want to retweet with
 */
hsp.retweet = function(id, screenName) {};

/**
 * Stores per-stream JSON data for later retrieval
 * @param {object} object JSON data
 */
hsp.saveData = function(object) {};

/**
 * Retrieves per-stream JSON data saved through saveData method
 * @param {object} callback Function to run once stream JSON data is retrieved - Is passed JSON object with data
 */
hsp.getData = function(callback) {};

/**
 * Closes the modal pop-up dialog
 * @param {string} src URL of the content to show in the popup
 * @param {string} title The title displayed on the popup window
 * @param {int} width The width in pixels of the popup. The width has the following constraints: If not specified, the width defaults to 640 pixels; The minimum width is 300 pixels; The maximum width is 900 pixels.
 * @param {int} height The height in pixels of the popup. The width has the following constraints: If not specified, the height defaults to 445 pixels; The minimum height is 225 pixels; The maximum height is 500 pixels.
 */
hsp.showCustomPopup = function(src, title, width, height) {};

/**
 * Opens a dialog window for following or un-following a Twitter user
 * @param {string} twitterHandle Twitter username
 * @param {boolean} isFollow true to follow, false to unfollow
 */
hsp.showFollowDialog = function(twitterHandle, isFollow) {};

/**
 * Shows an image in a popup window
 * @param {string} src Image URL
 * @param {string} externalUrl URL to open if user clicks on the image
 */
hsp.showImagePreview = function(src, externalUrl) {};

/**
 * Shows a notification
 * @param {string} message Should be brief, max. 70 characters.
 * @param {string} type Can be one of the following: info: Blue background; error: Red background; warning: Yellow background; success: Green background
 */
hsp.showStatusMessage = function(message, type) {};

/**
 * Shows Twitter trends
 */
hsp.showTrends = function() {};

/**
 * Opens a user info popup for the specified Twitter username
 * @param {string} twitterHandle Twitter username
 */
hsp.showUser = function(twitterHandle) {};

/**
 * Updates the subtitle of the App's stream
 * @param {string} name Max 35 characters
 */
hsp.updatePlacementSubtitle = function(name) {};
