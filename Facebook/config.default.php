<?php
### FACEBOOK CONFIGURATION VARIABLES ###

/**
 * To use Facebook you will first need to create a Facebook application:
 *    1.  Visit the "Create an Application" setup wizard:
 *        https://developers.facebook.com/setup/
 *    2.  Enter a descriptive name for your wiki in the Site Name field.
 *        This will be seen by users when they sign up for your site.
 *    3.  Enter the Site URL and Locale, then click "Create application".
 *    4.  Copy the displayed App ID and Secret into this config file.
 *    5.  One more step... Inside the developer app select your new app
 *        and click "Edit App". Now scroll down, click the check next to
 *        "Website," and finally enter your wiki's URL.
 * 
 * Optionally, you may customize your application:
 *    A.  Upload icon and logo images. The icon appears in Timeline events.
 *    B.  Create a Page for your app (if you don't already have one). Visit the
 *        settings, click Advanced, scroll to the bottom and click the buttom in
 *        the "App Page" field. This will create a new page for your app. Paste
 *        the Page ID for your app below.
 *    C.  Add a Privacy Policy URL to your app's Contact Info (under Advanced
 *        settings). The URL can be found by clicking "Privacy policy" at the
 *        bottom of your wiki's Main Page.
 * 
 * It is recommended that, rather than changing the settings in this file, you
 * instead override them in LocalSettings.php by adding new settings after
 * require_once("$IP/extensions/Facebook/Facebook.php");
 */
$wgFbAppId          = 'YOUR_APP_ID';    # Change this!
$wgFbSecret         = 'YOUR_SECRET';    # Change this!
//$wgFbPageId       = 'YOUR_PAGE_ID';   # Optional

/**
 * Turns the wiki into a Facebook-only wiki. This setting has three side-effects:
 *    1.  All users are stripped of the 'createaccount' right. To override this
 *        behavior, see FacebookHooks::UserGetRights.
 *    2.  Special:Userlogin and Special:CreateAccount redirect to Special:Connect
 *    3.  The "Log in / create account" links in the personal toolbar are removed.
 * 
 * If you have a Facebook-only wiki, you probably want to hide IP addresses. This
 * can be done by setting $wgShowIPinHeader to false in LocalSettings.php. For more
 * information, see <http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader>.
 */
$wgFbDisableLogin = false;

/**
 * For easier wiki rights management, create a group on Facebook and place the
 * group ID here. The "user_groups" right will automatically be requested from
 * users.
 * 
 * Two new implicit groups will be created:
 * 
 *     fb-groupie    A member of the specified group
 *     fb-admin      An administrator of the Facebook group
 * 
 * By default, they map to User and Sysop privileges. Users will automatically
 * be promoted or demoted when their membership or admin status is modified
 * from the group page within Facebook. Unfortunately, this has a minor
 * degredation on performance.
 * 
 * This setting can also be used in conjunction with $wgFbDisableLogin. To have
 * this group exclusively control access to the wiki, set $wgFbDisableLogin
 * to true and add the following settings to Localsettings.php:
 * 
 * # Disable reading and editing by anonymous users
 * $wgGroupPermissions['*']['edit'] = false;
 * $wgGroupPermissions['*']['read'] = false;
 * 
 * # Reserve normal wiki browsing for only Facebook group members (and admins)
 * $wgGroupPermissions['sysop'] = array_merge($wgGroupPermissions['sysop'], $wgGroupPermissions['user']);
 * $wgGroupPermissions['user'] = $wgGroupPermissions['*'];
 * 
 * # But allow all users to read these pages:
 * $wgWhitelistRead = array('-', 'Special:Connect', 'Special:UserLogin', 'Special:UserLogout');
 */
$wgFbUserRightsFromGroup = false;  # Or a quoted group ID

/**
 * Allow the use of social plugins in wiki text. To learn more about social
 * plugins, please see <https://developers.facebook.com/docs/plugins>.
 * 
 * Open Graph Beta social plugins can also be used.
 * <https://developers.facebook.com/docs/beta/plugins>
 */
$wgFbSocialPlugins = true;

/**
 * Shows the real name for all Facebook users in the personal toolbar (in the
 * upper right) instead of their wiki username.
 */
$wgFbUseRealName = true;

/**
 * Another personal toolbar option: always show a button to log in with
 * Facebook. By default, this button is only shown to anonymous users.
 * 
 * This button simply links to Special:Connect. If you would like to let users
 * convert their usernames to Facebook-enabled accounts, consider linking to
 * Special:Connect from the Main Page instead of showing this button on every
 * page.
 */
$wgFbAlwaysShowLogin = false;

/**
 * The Facebook icon. You can copy this image to your server if you want, or
 * set to false to disable.
 */
$wgFbLogo = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

/**
 * URL of the Facebook JavaScript SDK. If the URL includes the token %LOCALE%
 * then it will be replaced with the correct Facebook locale based on the
 * user's configured language. To disable localization, hardcode the locale:
 * 
 * https://connect.facebook.net/en_US/all.js
 * 
 * You may wish to insulate your production wiki from changes by downloading and
 * hosting your own copy of the JavaScript SDK. If you still wish to support
 * multiple languages, you will also need to host localized versions. For a list
 * of locales supported by Facebook, see FacebookLanguage.php.
 */
$wgFbScript = 'https://connect.facebook.net/%LOCALE%/all.js';

/**
 * Path to the extension's client-side JavaScript.
 *     facebook.js        For development
 *     facebook.min.js    Minified version for deployment
 */
global $wgScriptPath;
$wgFbExtensionScript = "$wgScriptPath/extensions/Facebook/facebook.js";

/**
 * PUSH EVENTS
 *
 * This section allows controlling of whether push events are enabled, and which
 * of the push events to use.
 */

// NOTE: THIS FEATURE IS NOT COMPLETELY IMPLEMENTED. TEST AT YOUR OWN RISK.
$wgFbEnablePushToFacebook = false;
if ( !empty( $wgFbEnablePushToFacebook ) ) {
	$wgFbPushDir = dirname( __FILE__ ) . '/pushEvents/';
	
	// Convenience loop for push event classes in the $wgFbPushDir directory
	// whose file name corresponds to the class name.  To add a push event
	// which does not meet these criteria, just explicitly add it below.
	$pushEventClassNames = array(
		'FBPush_OnAchBadge',
		'FBPush_OnAddBlogPost',
		'FBPush_OnAddImage',
		'FBPush_OnAddVideo',
		'FBPush_OnArticleComment',
		'FBPush_OnBlogComment',
		'FBPush_OnLargeEdit',
		'FBPush_OnRateArticle',
		'FBPush_OnWatchArticle',
	);
	foreach ( $pushEventClassNames as $pClassName ) {
		$wgFbPushEventClasses[] = $pClassName;
		$wgAutoloadClasses[$pClassName] = $wgFbPushDir . "$pClassName.php";
	}
	
	// Example of explicitly adding a push event which doesn't meet the criteria above.
	// $wgFbPushEventClasses[] = 'FBPush_OnEXAMPLE_CLASS';
	// $wgAutoloadClasses['FBPush_OnEXAMPLE_CLASS'] = $wgFbPushDir . 'FBPush_OnEXAMPLE_version_1.php';
}
