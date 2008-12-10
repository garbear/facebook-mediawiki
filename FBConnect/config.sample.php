<?php
/*
 * To install:
 *   1. Copy this file to config.php (remove the .sample part)
 *   2. Follow the instructions below to make the extension work.
 */

### CONFIGURATION VARIABLES

/*
 * Enter your callback URL here. That's the location where index.php
 * resides. Make sure it's your exact root - facebook.com
 * and www.facebook.com are different.
 */
$callback_url     = 'http://www.example.com/callback/w/';

/*
 * Get the API key and secret from http://facebook.com/developers
 * Note that each callback URL needs its own app id.
 *
 * Set the callback URL in your developer app to match the one you chose above.
 * This is important so that the Javascript cross-domain library works correctly.
 *
 */
$api_key         = 'YOUR_API_KEY';
$api_secret      = 'YOUR_SECRET';

// This is the root of the facebook site you'll be hitting. In production, this will be facebook.com
$base_fb_url     = 'connect.facebook.com';

/*
 * The feed story template needs to be registered with your app_key, and then just passed
 * at run time. To register the feed bundle for your app, visit:
 *
 * www.yourwiki.com/path/to/extensions/FBConnect/register_feed_forms.php
 *
 * Then copy/paste the resulting feed bundle ID here.
 */
$feed_bundle_id  = 99999999;

// Only allow login with Facebook Connect
if (!isset($wgFBConnectOnly))
	$wgFBConnectOnly = true;

// Remove link to user's talk page in the personal toolbar (upper right)
if (!isset($wgRemoveUserTalkLink))
	$wgRemoveUserTalkLink = true;

// Location of the Facebook logo. You can copy this to your server if you want.
if (!isset($wgFBConnectLogoUrl))
	$wgFBConnectLogoUrl = 'http://static.ak.fbcdn.net/images/icons/favicon.gif';

// Don't show IP or its talk link in the personal header. See http://www.mediawiki.org/wiki/Manual:$wgShowIPinHeader
$wgShowIPinHeader = false;

### END CONFIGURATION VARIABLES
