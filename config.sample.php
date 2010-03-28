<?php
/**
 * To install:
 *    1.  Copy this file to config.php (remove the .sample part)
 *    2.  Follow the instructions below to make the extension work.
 */

### FBCONNECT CONFIGURATION VARIABLES ###

/*
 * To use Facebook Connect you will first need to get a Facebook API Key:
 *    1.  Visit the Facebook application creation page:
 *        http://www.facebook.com/developers/createapp.php
 *    2.  Enter a descriptive name for your wiki in the Application Name field.
 *        This will be seen by users when they sign up for your site.
 *    3.  Accept the Facebook Terms of Service.
 *    4.  Upload icon and logo images. The icon appears in News Feed stories and the
 *        logo appears in the Connect dialog when the user connects with your application.
 *    5.  Click Submit.
 *    6.  Copy the displayed API key and application secret into this config file.
 */
$fbApiKey         = 'YOUR_API_KEY';
$fbApiSecret      = 'YOUR_SECRET';

/*
 * Enter your callback URL here. That's the location where index.php resides.
 * Make sure it's your exact root - facebook.com and www.facebook.com are different.
 * 
 * Set the callback URL in your developer app to match the one you specify here.
 * This is important so that the Javascript cross-domain library works correctly.
 * 
 * Note that each callback URL needs its own app id.
 */
$fbCallbackURL     = 'http://www.example.com/callback/w/';

/*
 * This is the root of the Facebook site you'll be hitting. In production,
 * this will be facebook.com
 */
$fbBaseURL     = 'connect.facebook.com';

/*
 * The feed story template needs to be registered with your app_key, and then just passed
 * at run time. To register the feed bundle for your app, visit:
 *
 * www.yourwiki.com/path/to/extensions/FBConnect/register_feed_forms.php
 *
 * Then copy/paste the resulting feed bundle ID here.
 * 
 * NOTE: This feature is currently unimplemented.
 */
$fbFeedBundleId  = 99999999;

// The Facebook icon. You can copy this to your server if you want, or set to false to disable.
$fbLogo = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

// This will be the form of the Facebook user's name when the user Connects and an account is
// automatically created. The first letter will of course be capitalized, and '_' converted to
// spaces. The user's Facebook ID takes place of the #. If no # is used, then the Facebook ID
// will be appended onto this string to form the user name. Unless your wiki is a new installation
// with a small user base, I recommend 'FB ' or '#-fb', e.g.
//
// To check if $fbUserName meshes with your current wiki setup, set $fbCheckUserNames to true.
//
// NOTE: As of r87, anything other than '' may currently cause tooltips have minor display
// problems, like tooltips and the Facebook logo failing to show up on the page.
$fbUserName = '';  # 'FB ' or '#-fb', for example

// Uncomment this line to check for user name conflicts between existing user names in the database
// and user names that could be generated for Facebook Connect users.
#$fbCheckUserNames = true;

// Allow non-Connected user accounts to login. Set this to true to allow users to continue logging
// into your site with old-style user names.
$fbAllowOldAccounts = true;

// Disable new account creation (accounts can only be created by a successful Connection)
$fbConnectOnly = false;

// Allow the use of XFBML in wiki text
// For more info, see http://wiki.developers.facebook.com/index.php/XFBML
$fbUseMarkup = true;

// If XFBML is enabled, then <fb:photo> maybe be used as a replacement for $wgAllowExternalImages
// with the added benefit that all photos are screened against Facebook's Code of Conduct
// <http://www.facebook.com/codeofconduct.php> and subject to dynamic privacy. To disable just
// <fb:photo> tags, set this to false.
$fbAllowFacebookImages = true;

// For easier wiki rights management, create a group on Facebook and place the group ID here.
// Three new implicit groups will be created:
//   fb-groupie, a member of the specified group
//   fb-officer, a group member with an officer title
//   fb-admin,   an administrator of the Facebook group
// By default, they map to User, Bureaucrat and Sysop privileges, respectively. Users will
// automatically be promoted or demoted when their membership, title or admin status is modified
// from the group page within Facebook.
$fbUserRightsFromGroup = false;  # Or a group ID

// Not used (yet...)
$fbRestrictToGroup = true;
$fbRestrictToNotReplied = false;


### GLOBAL CONFIGURATION VARIABLES ###

// Remove link to user's talk page in the personal toolbar (upper right)
$fbRemoveUserTalkLink = true;

// Don't show IP or its talk link in the personal header.
// See http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader
$wgShowIPinHeader = false;
