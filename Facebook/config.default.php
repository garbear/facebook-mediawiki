<?php
### FACEBOOK CONFIGURATION VARIABLES ###

/**
 * To use Facebook you will first need to create a Facebook application:
 *    1.  Visit the "Create an Application" setup wizard:
 *        https://developers.facebook.com/apps/?action=create
 *    2.  Enter a descriptive name for your wiki in the Site Name field.
 *        This will be seen by users when they sign up for your site.
 *    3.  Choose an app namespace (something simple, like coffee-wiki)
 *    4.  Copy the App ID, Secret and Namespace into this config file.
 *    5.  One more step... Inside the developer app scroll down, click the
 *        check next to "Website" and enter your wiki's URL.
 *    6   I lied, it's another step. Under Advanced settings, specify your
 *        deauth callback: http://wiki.example.com/wiki/Special:Connect/Deauth
 *        This will disconnect users when they remove your app.
 * 
 * Optionally, you may customize your application:
 *    A.  Upload icon and logo images. The icon appears in Timeline events.
 *    B.  Create a Page for your app (only if you don't already have one).
 *        Visit the settings, click Advanced, scroll to the bottom and click
 *        the button in the "App Page" field. This will create a new page for
 *        your app. Paste the Page ID for your app below.
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
$wfFbNamespace      = 'YOUR_NAMESPACE'; # Change this too
//$wgFbPageId       = 'YOUR_PAGE_ID';   # Optional

/**
 * Enables Facebook's Open Graph Protocol. This will integrate your wiki into the
 * social graph. To verify that this process is working, go to the Object Debugger:
 * https://developers.facebook.com/tools/debug
 * 
 * For more info, see: https://developers.facebook.com/docs/opengraph/
 * 
 * N.B. This parameter is incompatible with $wgHtml5. If set, $wgHtml5 will be
 * automatically disabled.
 */
$wgFbOpenGraph = true;

/**
 * If you have registered Open Graph actions and objects for the supported types
 * below, you can define them here. Registration can be done from the Open Graph
 * dashboard in your app's settings. If you are completely clueless, you can
 * leave these values undefined and this extension will "do the right thing" (TM).
 * 
 * If you registered and defined everything correctly, the Object Debugger above
 * should not show any errors.
 * 
 * $wgFbOpenGraphActions currently has no effect. In the future, it will allow
 * actions to be pushed to a user's timeline.
 * 
 * If you have more ideas for Facebook Actions, please contact me on GitHub. I'm
 * open to creative suggestions. https://github.com/garbear
 */
#$wgFbOpenGraphRegisteredActions = array(
#	'edit'   => 'edit',
#	'tweak'  => 'tweak', # for a minor edit
#	'watch'  => 'watch',
#	'upload' => 'upload',
#);
$wgFbOpenGraphRegisteredObjects = array(
	#'article' => 'article', # Uncomment this after registering an "article" object in the Dev App
);

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
$wgFbUseRealName = false;

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
 * 
 * Do not load this script asynchronously. It contains a callback for another
 * async script (Facebook JS SDK), which would then cause a race condition.
 */
global $wgScriptPath;
$wgFbExtensionScript = "$wgScriptPath/extensions/Facebook/facebook.js";

