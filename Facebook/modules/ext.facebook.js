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
 * facebook.js and facebook.min.js
 * 
 * The Facebook extension relies on several different libraries and frameworks
 * for its JavaScript code. Each framework has its own method to verify that the
 * proper code won't be called before it's ready. Be mindful of race conditions;
 * the methods for each component are listed below ("lambda" represents a named
 * or anonymous function).
 * 
 * MediaWiki:                  addOnloadHook(lambda);
 *     This function manages an array of window.onLoad event handlers to be
 *     called be called by a MediaWiki script when the window is fully loaded.
 *     Because the DOM may be ready before the window (due to large images to
 *     be downloaded), a faster alternative is JQuery's document-ready function.
 * 
 * Facebook JavaScript SDK:    window.fbAsyncInit = lambda;
 *     This global variable is called when the JavaScript SDK is fully
 *     initialized asynchronously to the document's state. This might be long
 *     after the document is finished rendering the first time the script is
 *     downloaded. Subsequently, it may even be called before the DOM is ready.
 * 
 * jQuery:                     $(document).ready(lambda);
 *     Self-explanatory; to be called when the DOM is ready to be manipulated.
 *     Typically this should occur sooner than MediaWiki's addOnloadHook
 *     function is called.
 */

/**
 * Simple jQuery plugin: postJSON (currently unused)
 *
(function() {
	if (!jQuery.postJSON) {
		jQuery.postJSON = function(url, data, callback) {
			return jQuery.post(url, data, callback, "json");
		};
	}
})();
/**/

/**
 * After the Facebook JavaScript SDK has been asynchronously loaded,
 * it looks for the global fbAsyncInit and executes the function when found.
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
	FB.Event.subscribe('auth.login', FacebookLogin);
	
	// Events involving Facebook code should only be attached once Facebook and
	// jQuery have both been loaded
	jQuery(document).ready(function($) {
		// Attach event to the Login with Facebook button
		$("#pt-facebook a").click(function(ev) {
			FB.login(FacebookLogin, {scope: window.fbScope});
			ev.preventDefault();
		});
	});
};

function FacebookLogin(response) {
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
					//alert("Login successful");
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
}

//Connecting Facebook with an existing account on Special:Connect
jQuery(document).ready(function($) {
	$('input[name="wpNameChoice"]').change(function() {
		var selected;
		try {
			// jQuery >= 1.6
			selected = $('#wpNameChoiceExisting').prop('checked');
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
