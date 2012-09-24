<?php
### FACEBOOK CONFIGURATION VARIABLES ###

/**
 * To use Facebook you will first need to create a Facebook application:
 *    1.  Visit the "Create an Application" setup wizard:
 *        https://developers.facebook.com/apps/?action=create
 *    2.  Enter a descriptive name for your wiki in the Site Name field.
 *        This will be seen by users when they sign up for your site.
 *    3.  Choose an app namespace (something simple, like coffeewiki)
 *    4.  Copy the App ID, Secret and Namespace into this config file.
 *    5.  Make sure you set the Death Callback. See $wgFbAllowDebug below.
 * 
 * Optionally, you may customize your application:
 *    A.  Upload icon and logo images. The icon appears in Timeline events.
 *    B.  Create a Page for your app (only if you don't already have one).
 *        Visit the settings, click Advanced, scroll to the bottom and click
 *        the button in the "App Page" field. This will create a new page for
 *        your app. Paste the Page ID for your app below.
 *    C.  Customize auth dialog messages. See $wgFbAllowDebug below.
 *    D.  Defined Open Graph objects and actions. See $wgFbOpenGraph below.
 * 
 * It is recommended that rather than changing the settings in this file, you
 * instead override them in LocalSettings.php by adding new settings after
 * require_once("$IP/extensions/Facebook/Facebook.php");
 */
$wgFbAppId          = 'YOUR_APP_ID';    # Change this!
$wgFbSecret         = 'YOUR_SECRET';    # Change this!
$wgFbNamespace      = 'YOUR_NAMESPACE'; # Change this too
//$wgFbPageId       = 'YOUR_PAGE_ID';   # Optional

/**
 * Special:Connect/Debug
 * 
 * This extension includes a program that configures your Facebook application
 * for you (how awesome is that?). Visit Special:Connect/Debug to begin. The
 * extension will detect fields that aren't filled in properly and will warn
 * you or indicate an error. Click on the warning/error icon and MediaWiki will
 * confirm the new setting. No further action is required on your part; the
 * setting has automatically been saved to Facebook.
 * 
 * The most important setting is the Deauth Callback. When a user removes your
 * application from their Facebook settings, the Death Callback lets Facebook
 * disconnect the user's accounts in the MediaWiki database.
 * 
 * It is OK to leave this special page enabled. To view this special page you
 * must have admin rights on the wiki (or be an admin of the Facebook group
 * below) AND be listed as a Developer or Admin of the Facebook application.
 * Set $wgFbAllowDebug to false to disable Special:Connect/Debug. Regardless,
 * make sure you visit this page at least once.
 */
$wgFbAllowDebug = true;

/**
 * Enables Facebook's Open Graph Protocol. This will integrate your wiki into the
 * social graph. To verify that this process is working, enter an existing page
 * name into the Oject Debugger on Special:Connect/Debug.
 * 
 * For more info, see: https://developers.facebook.com/docs/opengraph/
 * 
 * N.B. This parameter is incompatible with $wgHtml5. If set, $wgHtml5 will be
 * automatically disabled.
 */
$wgFbOpenGraph = true;

/**
 * Wiki pages are represented in the Open Graph as "articles," a built-in type
 * provisioned by Facebook. To include files uploaded to your wiki in the Open
 * Graph, register "file" as a custom object for your application in the Open
 * Graph Dashboard and define it here.
 * 
 * Optionally, you can register a custom object type for articles. It will then
 * be used instead of the built-in type. (As far as I can tell, the only
 * difference is that custom objects are preceeded by your namespace:
 * "NAMESPACE:article" as opposed to just "article.")
 * 
 * Depending on the extensions you have installed, several other object types
 * are available. These include "blog" and "badge."
 * 
 * If you registered and defined everything correctly, the Object Debugger on
 * Special:Connect/Debug should not show any errors.
 */
$wgFbOpenGraphRegisteredObjects = array(
#	'article' => 'article', # Comment out to use your custom type
#	'file'    => 'file',
);

/**
 * If you register Open Graph actions for the following object types, it will
 * be possible to push these actions to a user's Timeline. Actions can only be
 * published for their Connected Object Types; therefore, when you register
 * these actions in the Open Graph Dashboard, connect them to objects in this way:
 *    edit    = article
 *    tweak   = article
 *    discuss = article
 *    watch   = article, file
 *    protect = article, file
 *    upload  = file
 * 
 * You can rename an action by specifying a different value; leave an action
 * commented to disable it. The "tweak" action is used when the minor edit box
 * is checked or an edit is less than the MIN_CHARS_TO_EDIT constant (default 10):
 *    define('MIN_CHARS_TO_EDIT', 10);
 * If "tweak" isn't defined, "edit" will be used instead. Extension-dependent
 * actions inlude "rate" (articles) and "earn" (badges).
 */
$wgFbOpenGraphRegisteredActions = array(
#	'edit'    => 'edit',    # Uncomment each action you register...
#	'tweak'   => 'tweak',   # Minor edit
#	'discuss' => 'discuss', # Edit a talk page
#	'watch'   => 'watch',
#	'protect' => 'protect',
#	'upload'  => 'upload',
);

/**
 * Allow the use of social plugins in wiki text. To learn more about social
 * plugins, please see: https://developers.facebook.com/docs/plugins/.
 *
 * Open Graph social plugins can also be used:
 * https://developers.facebook.com/docs/opengraph/plugins/.
 */
$wgFbSocialPlugins = true;

/**
 * For easier wiki rights management, create a group on Facebook and place the
 * group ID here. The "user_groups" permission will automatically be requested
 * from users. Two new implicit groups will be created:
 *    1.  fb-groupie     A member of the specified group
 *    2.  fb-admin       An administrator of the Facebook group
 * 
 * The user's group membership status will be checked on each page load. They
 * will automatically be promoted or demoted when their status is modified from
 * the group page within Facebook.
 * 
 * This setting can also be used in conjunction with $wgFbDisableLogin. To have
 * this group exclusively control access to the wiki, set $wgFbDisableLogin to
 * true and add the following settings to Localsettings.php:
 * 
 * # Inherit privileges from User and Sysop
 * $wgGroupPermissions['fb-groupie'] = $wgGroupPermissions['user'];
 * $wgGroupPermissions['fb-admin']   = $wgGroupPermissions['sysop'];
 * 
 * # Disable reading and editing by anonymous users
 * $wgGroupPermissions['user']['read'] = $wgGroupPermissions['*']['read'] = false;
 * $wgGroupPermissions['user']['edit'] = $wgGroupPermissions['*']['edit'] = false;
 * 
 * # But allow all users to read these pages:
 * $wgWhitelistRead = array('-', 'Special:Connect', 'Special:UserLogin', 'Special:UserLogout');
 */
$wgFbUserRightsFromGroup = false;  # Or a quoted group ID, or an array of groups

/**
 * To streamline the Connecting process, AJAX is used to fetch forms when the
 * user logs in to Facebook via a login button or when the Facebook cookies are
 * refreshed. This occurs in the following situations:
 *    1.  The Facebook user is new to MediaWiki.
 *    2.  The user is logged in to MediaWiki and is asked to merge accounts.
 *    3.  The user logs in to a Facebook account associated with a different
 *        wiki account than the one currently logged in.
 */
$wgFbStreamlineLogin = true;

/**
 * Turns the wiki into a Facebook-only wiki. This setting has three side-effects:
 *    1.  All users are stripped of the 'createaccount' right. To override this
 *        behavior for admins, see UserGetRights() in FacebookHooks.php.
 *    2.  Special:Userlogin and Special:CreateAccount redirect to Special:Connect
 *    3.  The "Log in / create account" links in the personal toolbar are removed.
 * 
 * You can make the wiki exclusively for Facebook users with these four lines:
 * 
 * $wgGroupPermissions['fb-user'] = $wgGroupPermissions['user'];
 * $wgGroupPermissions['user']['read'] = $wgGroupPermissions['*']['read'] = false;
 * $wgGroupPermissions['user']['edit'] = $wgGroupPermissions['*']['edit'] = false;
 * $wgWhitelistRead = array('-', 'Special:Connect', 'Special:UserLogin', 'Special:UserLogout');
 * 
 * You can also hide IP addresses using $wgShowIPinHeader.
 */
$wgFbDisableLogin = false;

/**
 * Shows the real name instead of the wiki username for all Facebook users in
 * the personal toolbar (in the upper right) and on Special:ListUsers. Note
 * that changelogs still use wiki usernames.
 */
$wgFbUseRealName = false;

/**
 * Another personal toolbar option: always show a button to log in with
 * Facebook. By default, this button is only shown to anonymous users. If your
 * wiki enables pushing to a user's Timeline, you should set this to true.
 * 
 * A valid Facebook session (aka access_token) is required to log the Facebook
 * user in and publish to their Timeline. This session expires about two hours
 * after the user has left your wiki, but is usually refreshed when the user
 * returns (this is why pages sometimes load twice - so the server can pick up
 * the new session). If the user changes their Facebook password or logs out of
 * Facebook, the session can only be restored by having them click a Login button
 * (either in the personal toolbar or a <fb:login-button> plugin). If the session
 * expires, the user will continue to browse the wiki using their wiki username,
 * but Timeline events will be disabled.
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
 * Location of the extension script (MediaWiki <= 1.16). If you override, it
 * is recommended that you use $wgExtensionAssetsPath (defined to be
 * "$wgScriptPath/extensions") instead of $wgScriptPath.
 * 
 * This setting is deprecated and is not used in version 1.17 onward.
 */ 
$wgFbExtScript = "$wgScriptPath/extensions/Facebook/modules/ext.facebook.js";


