<?php
### FBCONNECT CONFIGURATION VARIABLES ###

/**
 * To use Facebook Connect you will first need to create a Facebook application:
 *    1.  Visit the "Create an Application" setup wizard:
 *        http://developers.facebook.com/setup/
 *    2.  Enter a descriptive name for your wiki in the Site Name field.
 *        This will be seen by users when they sign up for your site.
 *    3.  Enter the Site URL and Locale, then click "Create application".
 *    4.  Copy the displayed App ID and Secret into this config file.
 * 
 * Optionally, you may customize your application:
 *    A.  Click "developer dashboard" link on the previous screen or visit:
 *        http://www.facebook.com/developers/apps.php
 *    B.  Select your application and click "Edit Settings".
 *    C.  Upload icon and logo images. The icon appears in Stream stories.
 *    D.  If you need Facebook authentication across subdomains, specify the base
 *        domain in the "Web Site" section and copy it into this config file.
 *
 * It is recommended that, rather than changing the settings in this file, you
 * instead override them in LocalSettings.php by adding new settings after
 * require_once("$IP/extensions/FBConnect/FBConnect.php");
 */
$wgFbAppId          = 'YOUR_APP_ID';    # Change this!
$wgFbSecret         = 'YOUR_SECRET';    # Change this!
//$wgFbDomain       = 'BASE_DOMAIN';    # Optional

/**
 * Turns the wiki into a Facebook-only wiki. All users must authenticate using
 * Facebook Connect. This setting has three side-effects:
 * 1. All users are stripped of the 'createaccount' right. To override this
 *    behaviour, see FBConnectHooks::UserGetRights.
 * 2. Special:Userlogin and Special:CreateAccount redirect to Special:Connect
 * 3. The "Log in / create account" links in the personal toolbar are removed.
 */
$wgFbConnectOnly = false;

/**
 * Allow the use of XFBML in wiki text.
 * 
 * To learn more about Facebook Markup Language, please see
 * <http://developers.facebook.com/docs/reference/fbml/>.
 */
$wgFbUseMarkup = true;

/**
 * For easier wiki rights management, create a group on Facebook and place the
 * group ID here. Three new implicit groups will be created:
 * 
 *     fb-groupie    A member of the specified group
 *     fb-officer    A group member with an officer title
 *     fb-admin      An administrator of the Facebook group
 * 
 * By default, they map to User, Bureaucrat and Sysop privileges, respectively.
 * Users will automatically be promoted or demoted when their membership, title
 * or admin status is modified from the group page within Facebook.
 */
$wgFbUserRightsFromGroup = false;  # Or a group ID

// Not used (yet...)
#$wgFbRestrictToGroup = true;
#$wgFbRestrictToNotReplied = false;

/**
 * Options regarding the personal toolbar (in the upper right).
 */
$wgFbShowPersonalUrls = array(
	'connect',        // Show the "Log in with Facebook Connect" button
#	'connect-simple', // Shorter "Connect" button 
#	'convert',        // Give logged-in users a link to Connect their accounts (not available)
	'logout',         // Log the user out of Facebook when they log out of the wiki
	'link',           // Show a handy "Back to facebook.com" link. This helps enforce
                      // the idea that this wiki is "in front" of Facebook
);

/**
 * The Personal Urls above can be blacklisted for specific skins. An asterisk
 * acts to match all FBConnect-related buttons.
 */
$wgFbHidePersonalUrlsBySkin = array(
	'SkinMonaco' => array( 'connect', 'convert', 'logout', 'link' ),
	'SkinAnswers' => array( 'connect-simple', 'convert', 'logout', 'link' ),
	'SkinCorporate' => '*',
	'SkinOasis' => array( 'connect', 'convert', 'logout', 'link' ),
);

/**
 * Shows the real name for all Connected users instead of their wiki user name.
 */
$wgFbUseRealName = false;

/**
 * This MediaWiki variable can also be used to customized the Personal Urls.
 * For more information, see <http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader>.
 */
#$wgShowIPinHeader = false;

/**
 * The Facebook icon. You can copy this image to your server if you want, or
 * set to false to disable.
 */
$wgFbLogo = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

/**
 * URL of the Facebook Connect JavaScript SDK. Because this library is currently
 * a beta release, changes to the APIs may be made on a regular basis. If you
 * use FBConnect on your production website, you may wish to insulate yourself
 * from these changes by downloading and hosting your own copy of the library.
 * 
 * For more info, see <http://developers.facebook.com/docs/reference/javascript/>
 */
$wgFbScript = 'http://connect.facebook.net/en_US/all.js';
#$wgFbScript = 'https://connect.facebook.net/en_US/all.js';

/**
 * If this is set to true, and the user's language is anything other than wgFbScriptLangCode, then
 * the wgFbScriptLangCode URL will be used to load the correspondingly-localized version of the
 * Facebook JS SDK.
 */
$wgFbScriptEnableLocales = true;

/**
 * The MEDIAWIKI language code which corresponds to the file in $wgFbScript.  If the user
 * is in a language other than wgFbScriptLangCode, then the wgFbScriptByLocale will be used
 * if wgFbScriptEnableLocales is set to true.
 */
$wgFbScriptLangCode = 'en';

/**
 * If wgFbScriptEnableLocales is true and the user's language is anything except for the
 * wgFbScriptLangCode, we assume that instead of using the file in wgFbScript (which is quite likely a
 * locally-cached version of the JS SDK), that we should instead take the risk and use the
 * Facebook-hosted version for the corresponding language.
 *
 * NOTE: URL should include "%LOCALE%" which will be replaced with the correct Facebook Locale based
 * on the user's configured language.
 *
 * WARNING: It is highly-unlikely that you'll need to change this fallback-URL. If you're not sure that
 * you need to change it, it's best to leave it alone.
 */
$wgFbScriptByLocale = 'http://connect.facebook.net/%LOCALE%/all.js';

/**
 * Path to the extension's client-side JavaScript
 */
global $wgScriptPath;
$wgFbExtensionScript = "$wgScriptPath/extensions/FBConnect/fbconnect.js"; // for development
#$wgFbExtensionScript = "$wgScriptPath/extensions/FBConnect/fbconnect.min.js";

/**
 * Whether to include jQuery. This option is for backwards compatibility and is
 * ignored in version 1.16. Otherwise, if you already have jQuery included on
 * your site, you can safely set this to false.
 */
$wgFbIncludeJquery = true;

/**
 * Optionally override the default javascript handling which occurs when a user logs in.
 *
 * This will generally not be needed unless you are doing heavy customization of this extension.
 *
 * NOTE: This will be put inside of double-quotes, so any single-quotes should be used inside
 * of any JS in this variable.
 */
//$wgFbOnLoginJsOverride = "sendToConnectOnLogin();";

/**
 * PUSH EVENTS
 *
 * This section allows controlling of whether push events are enabled, and which
 * of the push events to use.
 */

// NOTE: THIS FEATURE IS NOT COMPLETELY IMPLEMENTED. TEST AT YOUR OWN RISK.
$wgFbEnablePushToFacebook = false;
if(!empty($wgFbEnablePushToFacebook)){
	$wgFbPushDir = dirname(__FILE__) . '/pushEvents/';
	
	// Convenience loop for push event classes in the fbPushDir directory
	// whose file-name corresponds to the class-name.  To add a push event
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
	foreach($pushEventClassNames as $pClassName){
		$wgFbPushEventClasses[] = $pClassName;
		$wgAutoloadClasses[$pClassName] = $wgFbPushDir . "$pClassName.php";
	}
	
	// Example of explicitly adding a push event which doesn't meet the criteria above.
	// $wgFbPushEventClasses[] = 'FBPush_OnEXAMPLE_CLASS';
	// $wgAutoloadClasses['FBPush_OnEXAMPLE_CLASS'] = $wgFbPushDir . 'FBPush_OnEXAMPLE_version_1.php';
}
