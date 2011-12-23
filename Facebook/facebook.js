/*
 * Copyright © 2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Simple jQuery plugin: postJSON
 */
(function() {
	if (!jQuery.postJSON) {
		jQuery.postJSON = function(url, data, callback) {
			return jQuery.post(url, data, callback, "json");
		};
	}
})();

//Clone the jQuery reference from the MediaWiki alias $j
if (typeof $j !== 'undefined') $ = $j;

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

	// NOTE: Auth.login doesn't appear to work anymore.
	// The onlogin attribute of the fb:login-buttons is being used instead.
	
	// Register a function for when the user logs out of Facebook
	/*
	FB.Event.subscribe('auth.logout', function(response) {
		// TODO: Internationalize
		var login = confirm("Not logged in.\n\nYou have been loggout out of " +
                            "Facebook. Press OK to log in with Facebook again, " +
                            "or press Cancel to stay on the current page.");
		if (login) {
			window.location = window.wgArticlePath.replace(/\$1/, "Special:Connect");
		}
	});
	*/
	
	// Events involving Facebook code should only be attached once Facebook and
	// jQuery have both been loaded
	$(document).ready(function() {
		// Attach event to the Login with Facebook button
		$("#pt-facebook a").click(function(ev) {
			/*
			// See <http://developers.facebook.com/docs/reference/javascript/FB.login>
			fn = FB.bind(sendToConnectOnLogin, null);
			// Can we just use this? Why call sendToConnectOnLogin in the context of "null"?
			//fn = sendToConnectOnLogin;
			FB.login(fn, {scope : "publish_stream"});
			*/
			FacebookLogin();
			ev.preventDefault();
		});
		
		// Add the logout behavior to the "Logout of Facebook" button
		$('#pt-fblogout').click(function() {
			// TODO: Where did the fancy DHTML window go? Maybe consider jQuery Alert Dialogs:
			// http://abeautifulsite.net/2008/12/jquery-alert-dialogs/
			var logout = confirm("You are logging out of both this site and Facebook.");
			if (logout) {
				FB.logout(function(response) {
					window.location = window.fbLogoutURL;
				});
			}
		});
	});
};

// debug
if (wgPageName != fbReturnToTitle)
	alert("wgPageName: " + wgPageName + "\nfbReturnToTitle: " + fbReturnToTitle);

function FacebookLogin() {
	//var perms = "publish_stream"; // email also?
	var perms = "email";
	FB.login(function(response) {
		// Check if the user logged in and fully authorized the app
		if (response && response.authResponse) {
			var destUrl = window.wgServer + window.wgScript + "?title=Special:Connect&returnto=" +
				encodeURIComponent(fbReturnToTitle ? fbReturnToTitle : wgPageName) + "&returntoquery=" +
				encodeURIComponent(wgPageQuery);
			// Check if the user is logged in to MediaWiki
			if (wgUserName) {
				// If fbId is set, the MediaWiki user is already connected to a Facebook user
				if (fbId) {
					if (fbId == response.authResponse.userID) {
						// 
					} else {
						// MediaWiki user is connected to a different Facebook account
						// AJAX: Ask if response.authResponse.userID has a MediaWiki account
						// Yes: redirect to Special:Connect
						// No: "Your username is already connected to a Facebook account. Would you
						// like to connect your username with this Facebook acount also?" Yes/No
						// goto ASDF
						
						// For now, goto Special:Connect
						// Special:Connect: check if fbid has a MediaWiki account
						// Yes: Display successful login page (TODO: or Special:UserLogin?)
						// No: "Your username is already connected to a Facebook account. Would you
						// like to connect your username with this Facebook acount also?" Yes/No
						
						// fbId means Facebook wasn't logged in before and now it is
						// However, the connecting account is different than the one the MW user is associated with
						// In this case, check the new ID
						// If it's free, on Special:Connect, ask if the user would like to add the Facebook account to their username
						// If it's not free, on Special:Connect, ask if the user would like to log out and continue with the new account
						//  (No: Return to previous page. Yes: Post to Special:Connect/LogoutAndContinue.)
						// Special:Connect/LogoutAndContinue: Display sucessful login page. Internally, log out MW user and log in new one.
						window.location.href = destUrl;
						return;
					}
				}
				// New connection, get Special:Connect/ConnectExisting form over AJAX and post to Special:Connect/ConnectExisting
				window.location.href = destUrl; // Fallback if AJAX fails
			} else {
				// ASDF:
				// Ask the server if the MW user can be automatically logged in, and get a ChooseName form over AJAX
				// If so the user can be logged in, redirect to Special:Connect (logs the user in and shows a success message with a returnto link)
				// If not, let the user fill out the form and post to Special:Connect/ChooseName
				window.location.href = destUrl; // Fallback if AJAX fails
			}
		}
	}, {scope: perms});
}

/**
 * An optional handler to use in fbOnLoginJsOverride for when a user logs in
 * with Facebook. This will redirect to Special:Connect with the returnto
 * variables configured properly.
 */
function sendToConnectOnLogin() {
	sendToConnectOnLoginForSpecificForm("");
}

/**
 * Allows optional specification of a form to force on Special:Connect (such as
 * ChooseName, ConnectExisting, or Convert)
 */
function sendToConnectOnLoginForSpecificForm(formName) {
	// I don't think we need to wrap this in FB.getLoginStatus(function(response) {...});
	
	// Format the form name
	if(formName != "") {
        formName = "/" + formName;
    }
	// If the AJAX methods fail, accomplish the same thing with a GET request
	// by sending the user to destUrl
	var destUrl = wgServer + wgScript + "?title=Special:Connect" + formName + "&returnto=" + encodeURIComponent(fbReturnToTitle ? fbReturnToTitle : wgPageName) + "&returntoquery=" + encodeURIComponent(wgPageQuery);
	
	// No AJAX form for ConnectExisting, redirect the user now
	if (formName == "/ConnectExisting") {
		// alert("Redirecting to " + destUrl);
		window.location.href = destUrl;
		return;
	}
	
	// At this point, formName is empty as no other forms are currently available
	
	// If a Wikia modal box is being displayed, nuke it
	$('#facebookModalWrapper').remove();
	
	// Attempt the AJAX request
	$.postJSON(window.wgScript + '?action=ajax&rs=SpecialConnect::checkCreateAccount&cb=' + wgStyleVersion, function(data) {
		// If all the checks in SpecialConnect::checkCreateAccount pass, continue
		if(data.status == "ok") {
			// jQuery.fn.getModal isn't defined (see jquery.wikia.js)
			if (!jQuery.fn.getModal) {
				// alert("jQuery.fn.getModal isn't defined. Click OK to continue normally.");
				// alert("Redirecting to " + destUrl);
				window.location.href = destUrl;
				return;
			}
			$().getModal(window.wgScript + '?action=ajax&rs=SpecialConnect::ajaxModalChooseName&returnto=' + encodeURIComponent(wgPageName) + '&returntoquery=' + encodeURIComponent(wgPageQuery),  "#facebookModal", {
				id: "facebookModalWrapper",
				width: 600,
				callback: function() {
					$('#facebookModalWrapper .close').click(function() {
						WET.byStr( 'FBconnect/ChooseName/X' );
					});
				}
			});
		} else {
			// alert("Error: SpecialConnect::checkCreateAccount returned " + data.code + " (" + data.message + ")");
			window.location.href = destUrl;
		}
	});
	return;
}

/**
 * When user wants to log in using a Wikia account and connect
 * it to a Facebook account at the same time.
 */
function loginAndConnectExistingUser() {
	AjaxLogin.action = 'loginAndConnect'; // for clicktracking
	AjaxLogin.form.unbind('submit'); // unbind the hander for previous form
	AjaxLogin.form = $('#userajaxconnectform');
	
	//window.wgAjaxLoginOnSuccess = loggedInNowNeedToConnect;
	
	// Make sure the default even doesn't happen
	AjaxLogin.form.submit(function(ev) {
		AjaxLogin.formSubmitHandler(ev);
		return false;
	});
}


/**
 * Expand ajax login to use slider login/merge switch.
 */
window.wgAjaxLoginOnInit = function() {
	AjaxLogin.slideToNormalLogin = function(el) {
		$().log('AjaxLogin: slideToNormalLogin()');
		var firstSliderCell = $("#AjaxLoginSliderNormal");
		var slideto = 0;
		
		AjaxLogin.beforeDoSuccess = function() {
			return true;
		};
		$("#AjaxLoginConnectMarketing a.forward").show();
		$("#AjaxLoginConnectMarketing a.back").hide();
		firstSliderCell.animate({
			marginLeft: slideto
		}, function() {
			$('#fbLoginAndConnect').hide();
		});
	};
	AjaxLogin.slideToLoginAndConnect = function(el) {
		$().log('AjaxLogin: slideToLoginAndConnect()');
		$('#fbLoginAndConnect').show();
		var firstSliderCell = $("#AjaxLoginSliderNormal");
		var slideto = -351;
		$("#AjaxLoginConnectMarketing a.forward").hide();
		$("#AjaxLoginConnectMarketing a.back").show();

		AjaxLogin.beforeDoSuccess = function() {
			FB.getLoginStatus(function(response) {
				if (response.session) {
					// already logged-in/connected via Facebook
					sendToConnectOnLoginForSpecificForm("ConnectExisting");
				} else {
					var slideto = -354;
					$('#userloginErrorBox3').hide();
					$('#fbLoginLastStep').show();
					$('#AjaxLoginConnectMarketing').animate({
						marginLeft: slideto
					}, function() {
						$('#fbLoginAndConnect').animate({
							marginLeft: slideto
						});
					});
					$('#fbLoginAndConnect').hide();
				}
			});
			return false;
		}
		
		firstSliderCell.animate({
			marginLeft: slideto
		});
	};
	AjaxLogin.LoginAndConnectHideBack = function() {
		$("#AjaxLoginConnectMarketing a.back").hide();
	};
	
	AjaxLogin.slider = function(e) {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}
	
		// Split into diff functions so that they can be called from elsewhere.
		if ($(this).hasClass("forward")) {
			AjaxLogin.slideToLoginAndConnect(this);
		} else {
			AjaxLogin.slideToNormalLogin(this);
		}
	};
	
	// setup slider
	$("#AjaxLoginConnectMarketing a").click(AjaxLogin.slider);
	
	$('#fbAjaxLoginConnect').click(function() {
		WET.byStr( 'FBconnect/login_dialog/connect' );
	});
	
	$("#AjaxLoginConnectMarketing .forward").click(function() {
		WET.byStr( 'FBconnect/login_dialog/slider/forward' );
	});
	
	$("#AjaxLoginConnectMarketing .back").click(function() {
		WET.byStr( 'FBconnect/login_dialog/slider/back' );
	});
	
	$("#wpLoginAndConnectCombo").click(function() {
		WET.byStr( 'FBconnect/login_dialog/login_and_connect' );
	});
	
	$().log('Facebook: AjaxLogin expend');
};


/**
 * Called after the AJAX has logged the user in on a request to login and connect.
 * Now that they are logged in, we will prompt them to connect with Facebook,
 * then drop them on the Special:Connect page to finish the process.
 */
/*
function loggedInNowNeedToConnect() {
	FB.getLoginStatus(function(response) {
		if (response.session) {
			// already logged-in/connected via facebook
			sendToConnectOnLoginForSpecificForm("ConnectExisting");
		} else {
			// Not logged/connected w/ Facebook. Show dialog w/ a button (to get around popup blockers in IE/webkit).
			$().getModal(window.wgScript + '?action=ajax&rs=SpecialConnect::getLoginButtonModal&uselang=' + window.wgUserLanguage + '&cb=' + wgMWrevId + '-' + wgStyleVersion,  false, {
				callback: function() {
					window.fbAsyncInit(); // need to init again so that the button that comes from the ajax request works
					
					var fb_loginAndConnect_WET_str = 'signupActions/fbloginandconnect';
					
					$('#fbNowConnectBox').makeModal({
						width: 300,
						persistent: false,
						onClose: function() {
							WET.byStr(fb_loginAndConnect_WET_str + '/close');
							window.location.reload(true);
						}
					});
					$('#fbNowConnectBoxWrapper').load(function() {
						setTimeout(function() {
							$('#fbNowConnectBoxWrapper').css({'top' : '130px'});
						});
					});
					WET.byStr(fb_loginAndConnect_WET_str + '/open');
				}
			});
		}
	});
}
/**/
