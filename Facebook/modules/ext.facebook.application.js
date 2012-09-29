/*
 * Copyright © 2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * ext.facebook.application.js
 * 
 * Contains code that is run on Special:Connect/Debug. This page displays
 * information about the Facebook application specified by $wgFbAppId. It
 * retrieves info about the app from Facebook and marks problematic settings.
 * 
 * This module allows the settings to be updated dynamically without reloading
 * the page.
 */

(function($, mw) {
	// Check for MediaWiki 1.17+
	$(document).ready(function() {
		// Make warnings and criticals clickable
		var icon = $('.facebook-field-warning,.facebook-field-error').siblings('div').children("a");
		// Install the click handler
		icon.click(function(ev) {
			var field = $(this).parent().parent().attr('id').substring('facebook-field-'.length);
			// namespace can't be set outside of Facebook (even though the docs claim it can)
			if (field == 'namespace') {
				alert("The namespace can not be updated from this page. Visit the application's " +
				      "settings from within Facebook or fix $wgFbNamespace.");
			} else {
				var title = $(this).parent().parent().prev().children('b').text();
				// Strip the ':' and lower-case the first letter
				title = title[0].toLowerCase() + title.substring(1, title.length - 1);
				var correct = $(this).parent().siblings('div').children('span').text();
				
				var id = mw ? mw.config.get("fbAppId") : window.fbAppId;
				var app_access_token = mw ? mw.config.get("fbAppAccessToken") : window.fbAppAccessToken;
				if (id && app_access_token) {
					var doit = confirm("Press OK to to update the " + title + " of your Facebook application to " + correct);
					// Make sure the FB object has been loaded from Facebook
					if (doit && FB) {
						FB.api('/' + id + '?' + field + '=' + encodeURIComponent(correct), 'POST', {
							access_token: app_access_token
						}, function(response) {
							if (response && !response.error) {
								old_div = $("#facebook-field-" + field + ">.facebook-field-current");
								new_div = old_div.siblings("div");
								new_div.hide();
								old_div.fadeOut('slow', function() {
									new_div.fadeIn('slow');
								});
							} else {
								alert('There was an error processing your request.\n\n' + response.error.message);
							}
						});
					}
				} else {
					// If app_access_token wasn't set correctly
					alert("The " + title + " of your Facebook application does not match the value in MediaWiki: " + correct);
				}
			}
			ev.preventDefault();
		});
	});
})(window.jQuery, window.mediaWiki);
