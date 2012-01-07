/*
 * Copyright © 2010-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * ext.facebook.js
 * 
 * The Facebook extension relies on several different libraries and frameworks
 * for its JavaScript code. Each framework has its own method to verify that the
 * proper code won't be called before it's ready. Be mindful of race conditions.
 */

(function($) {
	/**
	 * After the Facebook JavaScript SDK has been loaded, it looks for
	 * window.fbAsyncInit and executes the function when found.
	 */
	window.fbAsyncInit = function() {
		// Initialize the library with the API key
		FB.init({
			appId  : window.fbAppId,    // See $wgFbAppId in config.php
			status : true,              // Check login status
			cookie : true,              // Enable cookies to allow the server to access the session
			xfbml  : window.fbUseXFBML, // Whether XFBML should be automatically parsed
			oauth  : true
		});
		
		// Allow this extension to pick up clicks on <fb:login-button>
		FB.Event.subscribe('auth.login', window.FacebookLogin);
		
		$(document).ready(function() {
			// Attach event to the Login with Facebook button
			$("#pt-facebook a").click(function(ev) {
				FB.login(window.FacebookLogin, {scope: window.fbScope});
				ev.preventDefault();
			});
			// TODO: Set the "href" attribute of the login button to destUrl
		});
	};
	
	window.FacebookLogin = function(response) {
		// Check if the user logged in and fully authorized the app
		if (response && response.authResponse) {
			// Build the fallback URL for if the AJAX requests fail
			var destUrl = window.wgServer + window.wgScript;
			destUrl += "?title=Special:Connect&returnto=" + encodeURIComponent(window.wgPageName);
			if (window.wgPageQuery)
				destUrl += "&returntoquery=" + encodeURIComponent(window.wgPageQuery);
			
			if (window.wgUserName) {
				// The user is logged in to MediaWiki
				if (window.fbId) {
					// The MediaWiki user is already connected to a Facebook user
					// Check to see if it's the one that just logged in
					if (window.fbId == response.authResponse.userID) {
						// User is already logged in to MediaWiki
						// Hide the login button? For now, reload to re-render XFBML tags
						window.location.href = window.location.href;
					} else {
						// MediaWiki user is connected to a Facebook account different
						// from the one that just logged in
						// AJAX: Ask if response.authResponse.userID has a MediaWiki account
						// Yes: Ask to log in as the correct MediaWiki user (if so, redirect to Special:Connect)
						// "Your username is already connected to a Facebook account. Would you
						// like to connect your username with this Facebook acount also?" Yes/No
						// If Yes, post to Special:Connect/LogoutAndConnect
						// If no, don't do anything, hide prompt
						window.location.href = destUrl;
					}
				} else {
					// New connection, get Special:Connect/ConnectExisting form over AJAX and post to Special:Connect/ConnectExisting
					window.location.href = destUrl; // Fallback if AJAX fails
				}
			} else {
				// User is trying to log in with Facebook 
				// Ask the server about the user over AJAX
				// If the user exists, redirect to destUrl
				// If the user is new, a ChooseName form will be returned over AJAX
				// (let the user fill out the form and post to Special:Connect/ChooseName)
				window.location.href = destUrl; // Fallback if AJAX fails
			}
		}
	};
	
	// Form to connect Facebook with an existing account on Special:Connect
	$(document).ready(function() {
		$('input[name="wpNameChoice"]').change(function() {
			var selected;
			try {
				selected = $('#wpNameChoiceExisting').prop('checked'); // jQuery >= 1.6
			} catch(err) {
				selected = $('#wpNameChoiceExisting').attr('checked');
			}
			if (selected) {
				$("#mw-facebook-choosename-update").slideDown('slow');
			} else {
				$("#mw-facebook-choosename-update").slideUp('slow');
			}
		});
	});
})(jQuery);
