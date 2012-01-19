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
 *    5.  Make sure you set the Death Callback. See Special:Connect/Debug.
 * 
 * Optionally, you may customize your application:
 *    A.  Upload icon and logo images. The icon appears in Timeline events.
 *    B.  Create a Page for your app (only if you don't already have one).
 *        Visit the settings, click Advanced, scroll to the bottom and click
 *        the button in the "App Page" field. This will create a new page for
 *        your app. Paste the Page ID for your app below.
 *    C.  Customize auth dialog messages. See Special:Connect/Debug.
 * 
 * Special:Connect/Debug
 * 
 * How awesome is the extension's author? He made a program that configures
 * your Facebook application for you. Visit Special:Connect/Debug to begin.
 * The extension will detect fields that aren't filled in properly and will
 * warn you or indicate an error. Click on the warning/error icon and MediaWiki
 * will confirm the new setting. No further action is required on your part;
 * the setting has automatically been saved to Facebook. You must have admin
 * rights on the wiki and be listed as a Developer or Admin of the Facebook
 * application to use this special page.
 * 
 * It is recommended that rather than changing the settings in this file, you
 * instead override them in LocalSettings.php by adding new settings after
 * require_once("$IP/extensions/Facebook/Facebook.php");
 */
$wgFbAppId          = 'YOUR_APP_ID';    # Change this!
$wgFbSecret         = 'YOUR_SECRET';    # Change this!
$wfFbNamespace      = 'YOUR_NAMESPACE'; # Change this too
//$wgFbPageId       = 'YOUR_PAGE_ID';   # Optional

/**
 * Enables the debug page (Special:Connect/Debug). It is OK to leave this
 * enabled because only users who are both developers of the application and
 * admins on the wiki (or admins of the Facebook page below) may view this page.
 *
 * Regardless, make sure you visit Special:Connect/Debug at least once.
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
#	'edit'    => 'edit',
#	'tweak'   => 'tweak', # for a minor edit
#	'discuss' => 'discuss',
#	'watch'   => 'watch',
#	'protect' => 'protect',
#	'upload'  => 'upload',
#);
$wgFbOpenGraphRegisteredObjects = array(
#	'article' => 'article', # Uncomment this after registering an "article" object in the Dev App
);

/**
 * Allow the use of social plugins in wiki text. To learn more about social
 * plugins, please see <https://developers.facebook.com/docs/plugins>.
 *
 * Open Graph Beta social plugins can also be used.
 * <https://developers.facebook.com/docs/beta/plugins>
 */
$wgFbSocialPlugins = true;

/**
 * For easier wiki rights management, create a group on Facebook and place the
 * group ID here. The "user_groups" permission will automatically be requested
 * from users. Two new implicit groups will be created:
 *    1.  fb-groupie     A member of the specified group
 *    2.  fb-admin       An administrator of the Facebook group
 * 
 * Users will automatically be promoted or demoted when their status is
 * modified from the group page within Facebook. If you find users are
 * sometimes not being auto-promoted, try requesting the "offline_access"
 * permission using the FacebookPermissions hook.
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
$wgFbAjax = true;

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
 * Location of the extension script (MediaWiki <= 1.16). If you override, it
 * is recommended that you use $wgExtensionAssetsPath (defined to be
 * "$wgScriptPath/extensions") instead of $wgScriptPath.
 * 
 * This setting is deprecated and is not used in version 1.17 onward.
 */ 
$wgFbExtScript = "$wgScriptPath/extensions/Facebook/modules/ext.facebook.js";


