<?php

/**
 *  Class containing all the hooks used in this extension
 */
class FBConnectHooks {
	// Set the global variable $wgAuth to our custom authentification plugin
	static function onAuthPluginSetup (&$auth) {
		$auth = new StubObject('wgAuth', 'FBConnectAuthPlugin');
		return true;
	}
	
	// If the user isn't logged in, try to auto-authenticate via Facebook Connect
	static function onUserLoadFromSession($user, &$result) {
		global $wgAuth;
		//FBConnectClient
		$fb_uid = FBConnectClient::getClient()->get_loggedin_user();	
		if (!isset($fb_uid) || $fb_uid == 0) {
			// No connection with facebook, so use local sessions only
			return true;
		}
		if( $user->isLoggedIn() ) {
			// Already logged in; don't worry about the global session
			return true;
		}
		
		// Username is the user's facebook ID
		$userName = "$fb_uid";
		
		// Why doesn't this work? Is the facebook client not fully initialized yet?
		// $real_name = facebook_get_fields($fb_uid, array('name'));
		
		$localId = User::idFromName( $userName );
		
		// If the user does not exist locally, attempt to create it
		if ( !$localId ) {
			// Denied by configuration?
			if ( !$wgAuth->autoCreate() ) {
				wfDebug( __METHOD__.": denied by configuration\n" );
				// Can't create new user, give up now
				return true;
			}	
			
			/* Skip this check for now, until we hammer down the other problems
			// Is the user blocked?
			$anon = new User;
			if ( !$anon->isAllowedToCreateAccount() ) {
				wfDebug( __METHOD__.": denied by configuration. \$user->isAllowedToCreateAccount() returned false.\n" );
				// Can't create new user, give up now
				return true;
			} */

			// Checks passed, create the user
			//wfDebug( __METHOD__.": creating new user\n" );
			$user->loadDefaults( $userName );
			$user->addToDatabase();

			$wgAuth->initUser( $user, true );
			//$wgAuth->updateUser( $user );	// Called by $wgAuth->initUser(). Should it not be?

			// Update user count
			$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
			$ssUpdate->doUpdate();

			// Notify hooks (e.g. Newuserlog)
			wfRunHooks( 'AuthPluginAutoCreate', array( $user ) );
			//$user->addNewUserLogEntryAutoCreate();	// Which MediaWiki versions can we call this function in?
		} else {
			$user->setID( $localId );
			$user->loadFromId();
		}
		
		// Auth OK.
		wfDebug( __METHOD__.": logged in from session\n" );
		wfSetupSession();
		$result = true;
		
		return true;
	}

	// Modify the user's persinal toolbar (in the upper right)
	static function onPersonalUrls(&$personal_urls, &$title) {
		global $wgUser, $wgLang, $wgOut, $wgFBConnectOnly, $wgFBConnectLogoUrl;
		wfLoadExtensionMessages('FBConnect');
		$sk = $wgUser->getSkin();
		
		if ( !$wgUser->isLoggedIn() ) {
			$returnto = ($title->getPrefixedUrl() == $wgLang->specialPage( 'Userlogout' )) ?
			  '' : ('returnto=' . $title->getPrefixedURL());

			$personal_urls['fbconnect'] = array('text' => wfMsg('fbconnectlogin'),
			                                    #'href' => $sk->makeSpecialUrl( 'Userlogin', $returnto ),
			                                    'href' => $sk->makeSpecialUrl( 'Connect', $returnto ),
			                                    'active' => $title->isSpecial( 'Userlogin' ) );
			if ($wgFBConnectOnly) {
				# remove other personal toolbar links
				foreach (array('login', 'anonlogin') as $k) {
					if (array_key_exists($k, $personal_urls)) {
						unset($personal_urls[$k]);
					}
				}
			}
		} else {
			/* User's real name is not set at account creation. Why not? And why doesn't this workaround seem to work?
			if ($wgUser->getRealName() == "") {
				$wgAuth->updateUser($wgUser);
			}
			*/
			if ($wgUser->getRealName() == "") {
				$personal_urls['userpage']['text'] .= ' (change "Real Name" in preferences) ';
			} else {
				$personal_urls['userpage']['text'] = $wgUser->getRealName();
			}
			unset($personal_urls['logout']);
			$thisurl = $title->getPrefixedURL();
			$personal_urls['fblogout'] = array('text' => wfMsg('fbconnectlogout'),
			                                   'href' => Skin::makeSpecialUrl('Userlogout', $title->isSpecial('Preferences') ?
			                                             '' : "returnto={$thisurl}"),
			                                   'active' => false);
			$personal_urls['fblink'] = array('text' => wfMsg('fbconnectlink'),
			                                 'href' => 'http://www.facebook.com/profile.php?id=' . $wgUser->getName(),
			                                 'active' => false);
		}
		
		// Unset user talk page links
		if (array_key_exists('mytalk', $personal_urls))
			unset($personal_urls['mytalk']);

		return true;
	}

	// We seriously need to use a better hook... But which one allows injecting javascript src's into the page's body???
	// The dynamic source code loading [newElement("source") ...] technique didn't work for me.
	static function onParserAfterTidy(&$parser, &$text) {
		static $wgOnce = false;
		//if (!isset($wgOnce) || !$wgOnce) {
		if (!$wgOnce) {
			$wgOnce = true;
			self::onSomeHookThatAllowsOneTimeRenderingToFooter($text);
		}
		return true;
	}
	
	// Is there any hook for this task?
	static function onSomeHookThatAllowsOneTimeRenderingToFooter(&$text) {
		$text .= '<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/XdCommReceiver.js" type="text/javascript"></script>
		<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script>
		<script src="/w/extensions/FBConnect/fbconnect.js"></script>';
		return true;
	}

	// Injects some CSS and Javascript into the <head> and <body> of the page
	static function onBeforePageDisplay(&$out, &$sk) {
		global $wgTitle, $wgFBConnectLogoUrl;
		$thisurl = $wgTitle->getPrefixedURL();
		
		// Add a pretty Facebook logo in front of the userpage's link
		$style = '<style type="text/css">
			li#pt-userpage {
				background: url(' . $wgFBConnectLogoUrl . ') top left no-repeat;
			}
		</style>';
		
		// Define javascript functions to be executing in the <body> of the page
		// Array format: array( 'functionName' => '{ //do something; }' )
		$functions = array(
		'addFBConnectButtons' =>
			'{
				if (document.getElementById("pt-fbconnect")) {
				'/* Either use a FBXML button (in use), or render an html button */.'
					//document.getElementById("pt-fbconnect").innerHTML = \'<a href="#" onclick="FB.Connect.requireSession(' .
						'function(){facebook_onlogin_ready();}); return false;" ><img id="fb_login_image" ' .
						'src="http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" ' .
						'alt="Connect"/></a>\';
					document.getElementById("pt-fbconnect").innerHTML = \'<fb:login-button length="long" ' .
						'onlogin="facebook_onlogin_ready();"></fb:login-button>\';
				}
				if (document.getElementById("pt-fblogout")) {
				'/* Either use a FBXML button, render an html button, or a combination of both (in use, "necessary" for MediaWiki user logouts */.'
					//document.getElementById("pt-fblogout").innerHTML = \'<a href="#" onclick="facebook_logout_function();" >' .
						'<img id="fb_logout_image" src="http://static.ak.fbcdn.net/images/fbconnect/logout-buttons/logout_small.gif" ' .
						'alt="logout of facebook"/></a>\';
					//document.getElementById("pt-fblogout").innerHTML = \'<fb:login-button autologoutlink="true"></fb:login-button>\';
					document.getElementById("pt-fblogout").innerHTML = \'<span onclick="setTimeout(\\\'facebook_logout_function()\\\', 2000)">' .
						'<fb:login-button autologoutlink="true" size="small"></fb:login-button></span>\';
				}
			}',
		'FB_init' =>
			'{
				FB.init("' . FBConnectClient::get_api_key() . '", "/w/extensions/FBConnect/xd_receiver.php");
			}',
		'alertfunction' => 
			'{
				FB_RequireFeatures(["XFBML"], function()
				{
					FB_init();
					FB.Facebook.get_sessionState().waitUntilReady(function()
					{
						//window.alert("Session is ready");
						// If you want to make Facebook API calls from JavaScript do something like
						FB.Facebook.apiClient.friends_get(null, function(result, ex)
						{
							//Do something with result
							window.alert("Friends list: " + result);
						});
					});
				});
			}',
		'facebook_logout_function' =>
			'{
				window.location = "' . Skin::makeSpecialUrl('Userlogout', $wgTitle->isSpecial('Preferences') ? '' : "returnto={$thisurl}") . '";
			}',
		'facebook_onload_function' =>
			'{
				window.onload = function() { facebook_onload(' . (FBConnectClient::getClient()->get_loggedin_user() ? "true" : "false") . '); };
			}');
		// Include all of $functions in the page's javascript
		#foreach( $functions as $fName => $fBody ) {
		// Only include the functions listed here into the page's javascript
		foreach( array( 'addFBConnectButtons', 'FB_init', 'facebook_onload_function' ) as $fName ) {
			$fBody = $functions[$fName]; // If we are using foreach($functions as $fName => $fBody), then remove this line
			$script[0][] = "\n\t\t\tfunction $fName()\n\t\t\t$fBody";
			$script[1][] = "\n\t\t\taddOnloadHook($fName);";
		}
		$script = implode(array( implode($script[0]), implode($script[1]) ));
		
		// Styles and Scripts have been built, so add them to the page
		if (isset($wgFBConnectLogoUrl) && $wgFBConnectLogoUrl) {
			$out->addScript($style . "\n\t\t");
		}
		$out->addInlineScript($script);
		return true;
	}
}
