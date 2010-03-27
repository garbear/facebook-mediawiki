<?php
/*
 * To install:
 *    1.  Copy this file to config.php (remove the .sample part).
 *    2.  Follow the instructions below to make the extension work.
 */


### FBCONNECT CONFIGURATION VARIABLES ###

/**
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

/**
 * Disables the creation of new accounts and prevents old accounts from being
 * used to log in if they aren't associated with a Facebook Connect account.
 */
$fbConnectOnly = false;

/**
 * The prefix to be used for the auto-generated username suggestion when the
 * user connects for the first time. A number will be appended onto this prefix
 * to prevent duplicate usernames.
 */
$fbUserName = 'FacebookUser';

/**
 * Allow the use of XFBML in wiki text.
 * For more info, see http://wiki.developers.facebook.com/index.php/XFBML.
 */
$fbUseMarkup = true;

/**
 * If XFBML is enabled, then <fb:photo> maybe be used as a replacement for
 * $wgAllowExternalImages with the added benefit that all photos are screened
 * against Facebook's Code of Conduct <http://www.facebook.com/codeofconduct.php>
 * and subject to dynamic privacy. To disable just <fb:photo> tags, set this to false.
 * 
 * Disabled until the alpha JS SDK supports <fb:photo> tags.
 */
#$fbAllowFacebookImages = true;

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
$fbUserRightsFromGroup = false;  # Or a group ID

/**
 * The Facebook icon. You can copy this image to your server if you want, or
 * set to false to disable.
 */
$fbLogo = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

/**
 * URL of the Facebook Connect JavaScript SDK. Because this library is currently
 * an alpha release, changes to the APIs may be made on a regular basis. If you
 * use FBConnect on your production website, you may wish to insulate yourself
 * from these changes to the alpha library by downloading and hosting your own
 * copy of the library.
 */
$fbScript = 'http://static.ak.fbcdn.net/connect/en_US/core.js';

// Not used (yet...)
$fbRestrictToGroup = true;
$fbRestrictToNotReplied = false;


### GLOBAL CONFIGURATION VARIABLES ###

// Remove link to user's talk page in the personal toolbar (upper right).
$fbRemoveUserTalkLink = true;

// Don't show IP or its talk link in the personal header.
// See also: http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader.
$wgShowIPinHeader = false;
