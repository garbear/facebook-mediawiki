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
				FB.login(window.FacebookLogin, {scope: window.fbScope});
				ev.preventDefault();
			});
			// TODO: Set the "href" attribute of the login button to destUrl
		});
	};
	
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
			// TODO: If we are already on Special:Connect, reload the page
			if (window.wgPageName == "Special:Connect") { // FIXME: Is this the right test?
				window.location.href = window.location.href;
			}
			// Check for repeated clicks
			else if ($('#facebook-ajax-window').length) {
				// Only redirect if the other form had a chance to fully load
				if (window.formLoaded) {
					// no params means no form, just go to Special:Connect
					window.getAndShowGetForm();
				}
				// Else, do nothing (other form is still loading, "auth.login" might have fired twice)
			} else {
				// Set up a window above the body content for our AJAX forms
				$('#fb-root').after('<div id="facebook-ajax-window"/>');
				
				if (window.wgUserName) {
					// The user is logged in to MediaWiki
					if (window.fbId) {
						// The MediaWiki user is already connected to a Facebook user.
						// Check to see if it's the one that just logged in.
						if (window.fbId == response.authResponse.userID) {
							// User is already logged in to MediaWiki. For now, reload
							// to re-render XFBML tags.
							window.location.href = window.location.href;
						} else {
							// MediaWiki user is connected to a Facebook account that is
							// different from the one that just logged in.
							window.getAndShowGetForm('facebooklogoutandcontinue');
						}
					} else {
						// New connection, show a MergeAccount form
						window.getAndShowGetForm('facebookmergeaccount');
					}
				} else {
					// User is trying to log in with Facebook. If the user exists, they will
					// be redirected to Special:Connect. Otherwise, a ChooseName form will be
					// shown at the top of the page.
					window.getAndShowGetForm('facebookchoosename');
				}
			}
		}
	};
	
	/**
	 * Uses the MediaWiki API to retrieve a form over AJAX and show it to the
	 * user. In all cases, if anything goes wrong, we simply redirect the user
	 * to Special:Connect with the understanding that this page knows how to
	 * handle everything we can throw at it.
	 * 
	 * @param formName is the name of a valid API call. If formName is omitted,
	 * the user will be sent to Special:Connect without showing any form.
	 */
	window.getAndShowGetForm = function(formName) {
		var gotoSpecialConnect = function() {
			// Build the fallback URL for if the AJAX requests fail
			// TODO: Build this URL properly
			var destUrl = window.wgServer + window.wgScript;
			destUrl += "?title=Special:Connect&returnto=" + encodeURIComponent(window.wgPageName);
			if (window.wgPageQuery)
				destUrl += "&returntoquery=" + encodeURIComponent(window.wgPageQuery);
			window.location.href = destUrl;
		};
		// If fbUseAjax is false, always go directly to Special:Connect
		if (!formName || !window.fbUseAjax) {
			gotoSpecialConnect();
		} else {
			FB.api('/me', 'GET', function(info) {
				if (info && !info.error) {
					// We got the info. Now, get the form.
					$.ajax({
						type: 'POST',
						// In MW >= 1.17: url = mw.util.wikiScript('api');
						url: window.wgScriptPath + '/' + 'api' + (window.wgScriptExtension || '.php'),
						data: {
							'action'     : formName,
							'format'     : 'json',
							'id'         : info.id,
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
							if (json_html.length) {
								// Add the html to the document
								var form = $('<div/>').html(json_html[0]).hide();
								$('#facebook-ajax-window').append(form).animate({
									'height': form.height() + 'px'
								}, 'slow', 'swing', function() {
									form.fadeIn('slow');
									$(this).css('height', 'inherit');
									if (formName == 'facebookchoosename') {
										window.addFormListener();
									}
									// Set a flag indicating we loaded a form
									window.formLoaded = true;
								});
							} else {
								gotoSpecialConnect(); // Fallback if form is empty or error occurs
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							gotoSpecialConnect(); // Fallback if AJAX fails
						}
					});
				} else {
					gotoSpecialConnect(); // Facebook if FB.api('/me') fails
				}
			});
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
