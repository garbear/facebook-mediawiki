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
		
		// Allow this extension to pick up clicks on <fb:login-button>. The
		// "auth.login" event is also fired when stale cookies are refreshed
		// (like when a user closes the browser and reloads the wiki page).
		// A side effect is that sometimes users are logged in without
		// clicking on a login button. If this behavior isn't desired, more
		// research is needed...
		FB.Event.subscribe('auth.login', window.FacebookLogin);
		
		$(document).ready(function() {
			// Attach event to the Login with Facebook button
			$("#pt-facebook a").click(function(ev) {

				// Load AJAX spinner to get it ready for later on
				$('<img/>').attr('src', window.loadingSrc).load();
				
				FB.login(window.FacebookLogin, {scope: window.fbScope});
				ev.preventDefault();
			});
			// TODO: Set the "href" attribute of the login button to destUrl
		});
	};
	
	// Location of the AJAX loading spinner icon
	window.loadingSrc = window.stylepath + '/common/images/ajax-loader.gif';
	
	/**
	 * This function is called to log the user into MediaWiki. Three events will
	 * cause this function to fire:
	 *    1.  User clicks the "Log in with Facebook" button in the personal URLs toolbar
	 *    2.  User authenticates the app through a social plugin (e.g. <fb:login-button>)
	 *    3.  Stale cookies are refreshed and the 'auth.login' is fired. Note that this
	 *        sometimes happens when loading the wiki in a new browser window. For now,
	 *        unfortunately, this is indistinguishable from an actual login event.
	 * 
	 * The login process tries to proceed via AJAX. Upon any error, the mentality
	 * is this: send the user to Special:Connect. This special page always knows
	 * what to do; the AJAX calls here simply attempt to shorten the process.
	 */
	window.FacebookLogin = function(response) {
		// Check if the user logged in and fully authorized the app
		if (response && response.authResponse) {
			
			// Test if the new ResourceLoader is present
			var compatible = window.wgVersion && (parseInt(window.wgVersion.split('.')[1]) || 0) >= 17 && window.mw;
			
			var gotoSpecialConnect = function() {
				// Build the fallback URL for if the AJAX requests fail
				// TODO: Build this URL properly
				var destUrl = window.wgServer + window.wgScript;
				destUrl += "?title=Special:Connect&returnto=" + encodeURIComponent(window.wgPageName);
				if (window.wgPageQuery)
					destUrl += "&returntoquery=" + encodeURIComponent(window.wgPageQuery);
			};
			var refresh = function() {
				window.location.href = window.location.href;
			};
			
			// TODO: If we are already on Special:Connect, reload the page
			
			// Set up a window above the body content for our AJAX forms
			$('#fb-root').after('<div id="facebook-ajax-window"/>');
			
			if (window.wgUserName) {
				// The user is logged in to MediaWiki
				if (window.fbId) {
					// The MediaWiki user is already connected to a Facebook user
					// Check to see if it's the one that just logged in
					if (window.fbId == response.authResponse.userID) {
						// User is already logged in to MediaWiki
						// For now, reload to re-render XFBML tags
						refresh();
					} else {
						// MediaWiki user is connected to a Facebook account different
						// from the one that just logged in
						if (compatible) {
							/*
							var type = 'html'; // or text
							$.ajax({
								type: "GET",
								url: mw.util.wikiScript('api'), // or text
								data: {
									'action'     : 'ChooseName',
									'format'     : type,
									'fbid'       : response.authResponse.userID,
									'lgpassword' : 'foobar'
								},
								dataType: type,
								success: function(html) {
									// AJAX: Ask if response.authResponse.userID has a MediaWiki account
									// If yes, add html to page "Logout and continue"
									// If no, add html to page "Logout and create new user"
									alert(html);
									
								},
								error: function() {
									gotoSpecialConnect(); // Fallback if AJAX fails
								}
							});
							*/
						}
						gotoSpecialConnect();
					}
				} else {
					// New connection, get Special:Connect/ConnectExisting form over AJAX and post to Special:Connect/ConnectExisting
					gotoSpecialConnect(); // Fallback if AJAX fails
				}
			} else {
				// User is trying to log in with Facebook
				// Ask the server about the user over AJAX
				// If the user exists, redirect to destUrl
				// If the user is new, a ChooseName form will be returned over AJAX
				
				// Set up the AJAX call
				//var url = mw.util.wikiScript('api');
				var url = window.wgScriptPath + '/' + 'api' + (window.wgScriptExtension || '.php');
				var getForm = function() {
					// First, get user information to pre-populate the form. We do this
					// here because the server might not have Facebook access_token yet.
					FB.api('/me', 'GET', function(info) {
						if (info && !info.error) {
							// We got the info. Now, get the form.
							$.ajax({
								type: 'POST',
								url: url,
								data: {
									'action'     : 'facebookchoosename',
									// Don't use 'html' here because ApiMain::$Formats doesn't support it
									'format'     : 'json', // not 'text'
									'id'         : response.authResponse.userID,
									'name'       : info.name,
									'first_name' : info.first_name,
									'last_name'  : info.last_name,
									'username'   : info.username,
									'gender'     : info.gender,
									'locale'     : info.locale,
									'timezone'   : info.timezone,
									'email'      : info.email,
								},
								dataType: 'json',
								cache: false,
								// Response is a unit-sized array of html
								success: function(json_html) {
									console.log(json_html);
									if (json_html.length) {
										// Add the html to the document
										var form = $('<div/>').html(json_html[0]).hide();
										var w = $('#facebook-ajax-window');
										w.append(form);
										window.addFormListener();
										
										// Don't resize the window if form height < ajax icon
										if (form.height() > 32) {
											$('#facebook-ajax-window img').fadeOut('slow');
											w.animate({
												'height': form.height() + 'px'
											}, 'slow', 'swing', function() {
												form.fadeIn('slow');
												w.css('height', 'inherit');
											});
										} else {
											$('#facebook-ajax-window img').fadeOut('slow', function() {
												w.css('height', 'inherit');
												form.fadeIn('slow');
											});
										}
									} else {
										gotoSpecialConnect(); // Maybe FB user has an account already
									}
								},
								error: function() {
									gotoSpecialConnect(); // Fallback if AJAX fails
								}
							});
						} else {
							gotoSpecialConnect(); // Facebook if api(/me) fails
						}
					});
				};
				
				// Now start the AJAX process
				$('#facebook-ajax-window').animate({
					'height'         : '32px',
					'padding-bottom' : '20px'
				}, 'slow', 'swing', getForm).append('<img src="' + window.loadingSrc + '"/>');
			}
		}
	};
	
	// Form to connect Facebook with an existing account on Special:Connect
	window.addFormListener = function() {
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
	};
	$(document).ready(function() {
		window.addFormListener();
	});
	
})(window.jQuery);
