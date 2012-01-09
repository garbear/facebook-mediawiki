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
 * Contains code that is run on Special:Connect/Debug.
 * 
 * The original plan was to have application parameters be adjusted via the
 * Facebook JS SDK. However, these parameters require an application access
 * token (APP_ID + '|' + APP_SECRET) instead of a user access token. See:
 * https://developers.facebook.com/docs/reference/api/application/#application_access_tokens
 * 
 * Handing this application access token to the client should be done through
 * the MakeGlobalVariablesScript hook instead of the ResourceLoaderGetConfigVars
 * hook because skipping the ResourceLoader allows us to screen the page title
 * and only serve this access token to authenticated users viewing Special:Connect/Debug.
 * 
 * Once that's done, do a normal API POST with {access_token: fbAppAccessToken} as a param.
 */

(function($) {
	// Check for MediaWiki 1.17+
	if (window.wgVersion && (parseInt(window.wgVersion.split('.')[1]) || 0) >= 17 && window.mw) {
		$(document).ready(function() {
			// Make warnings and criticals clickable
			var icon = $('.facebook-field-warning,.facebook-field-critical').siblings('img').wrap('<a href="#"/>').parent();
			// Install the click handler
			icon.click(function(ev) {
				//var field = $(this).parent().attr('id').substring('facebook-field-'.length);
				var title = $(this).parent().prev().children('b').text();
				// Strip the ':' and lower-case the first letter
				title = title[0].toLowerCase() + title.substring(1, title.length - 1);
				var correct = $(this).siblings('div').text();
				alert("The " + title + " does not match the value in MediaWiki: " + correct);
				ev.preventDefault();
			});
		});
	}
})(jQuery);
