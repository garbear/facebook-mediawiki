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

$messages = array();

/** English */
$messages['en'] = array(
// Extension name
'fbconnect'          => 'Facebook Connect',
// Personal toolbar
'fbconnect-connect'  => 'Connect this account with Facebook',
'fbconnect-logout'   => 'Logout of Facebook',
'fbconnect-link'     => 'Back to facebook.com',
// Special:Connect
'fbconnect-special'  => 'Connect account with Facebook',
'fbconnect-intro'    => 'This wiki is enabled with Facebook Connect, the next evolution of Facebook ' .
                        'Platform. This mean that when you are Connected, in addition to the normal ' .
                        '[[Wikipedia:Help:Logging in#Why log in?|benefits]] you see when logging in, ' .
                        'you will be able to take advantage of some extra features...',
'fbconnect-conv'     => 'Convenience',
'fbconnect-convdesc' => 'Connected users are automatically logged you in. If permission is given, ' .
                        'then this wiki can even use Facebook as an email proxy so you can continue ' .
                        'to receive important notifications without revealing your email address.',
'fbconnect-fbml'     => 'Facebook Markup Language',
'fbconnect-fbmldesc' => 'Facebook has provided a bunch of built-in tags that will render dynamic ' .
                        'data. Many of these tags can be included in wiki text, and will be rendered ' .
                        'differently depending on which Connected user they are being viewed by.',
'fbconnect-comm'     => 'Communication',
'fbconnect-commdesc' => 'Facebook Connect ushers in a whole new level of networking. See which of ' .
                        'your friends are using the wiki, and optionally share your actions with ' .
                        'your friends through the Facebook News Feed.',
);
