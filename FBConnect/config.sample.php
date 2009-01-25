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
 */
$fbFeedBundleId  = 99999999;

// The Facebook icon. You can copy this to your server if you want, or set to false to disable.
$fbLogo = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

// Allow non-Connected user accounts to login (false may work but is untested)
$fbAllowOldAccounts = true;

// Disable new account creation (accounts can only be created by a successful Connection)
// Currently, this feature is locked to true. Look for dual-signin and account merging in the future
$fbConnectOnly = true;

// Allow the use of XFBML in wiki text
$fbUseMarkup = true;

// If XFBML is enabled, then <fb:photo> maybe be used as a replacement for $wgAllowExternalImages
// with the added benefit that all photos are screened against Facebook's Code of Conduct
// <http://www.facebook.com/codeofconduct.php> and subject to dynamic privacy. To disable just
// <fb:photo> tags, set this to false.
$fbAllowFacebookImages = true;

// Groups
$fbUserRightsFromGroup = false;


### GLOBAL CONFIGURATION VARIABLES ###

// Remove link to user's talk page in the personal toolbar (upper right)
$fbRemoveUserTalkLink = true;

// Don't show IP or its talk link in the personal header.
// See http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader
$wgShowIPinHeader = false;
