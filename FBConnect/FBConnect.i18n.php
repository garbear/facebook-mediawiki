<?php
/**
 * FBConnect.i18n.php - FBConnect for MediaWiki
 * 
 * Internationalization file... for when Facebook Connect is internationalized ;-)
 */


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

global $wgSitename;

$messages = array();

/** English */
$messages['en'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => "Facebook Connect plugin for MediaWiki",
	'fbconnectlogin' => "Connect with Facebook",
	'fbconnectlogout' => "logout of facebook",
	'fbconnectlink' => "Back to Facebook.com",
	'fbconnectwelcome' => "Welcome to $wgSitename",
	'fbconnectintro' => "Facebook Connnect is currently in test phase. For now, existing usernames will still work.",
);
