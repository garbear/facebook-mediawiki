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

define( 'fb', 'fbconnect-' );

/** English */
$messages['en'] = array(
// Extension name
'fbconnect'   => 'Facebook Connect',
// Personal toolbar
fb.'connect'  => 'Connect this account with Facebook',
fb.'logout'   => 'Logout of Facebook',
fb.'link'     => 'Back to facebook.com',
// Special:Connect
fb.'title'    => 'Connect account with Facebook',
fb.'intro'    => 'This wiki is enabled with Facebook Connect, the next evolution of Facebook Platform. This means ' .
                 'that when you are Connected, in addition to the normal [[Wikipedia:Help:Logging in#Why log in?' .
                 '|benefits]] you see when logging in, you will be able to take advantage of some extra features...',
fb.'conv'     => 'Convenience',
fb.'convdesc' => 'Connected users are automatically logged you in. If permission is given, then this wiki can even ' .
                 'use Facebook as an email proxy so you can continue to receive important notifications without ' .
                 'revealing your email address.',
fb.'fbml'     => 'Facebook Markup Language',
fb.'fbmldesc' => 'Facebook has provided a bunch of built-in tags that will render dynamic data. Many of these tags ' .
                 'can be included in wiki text, and will be rendered differently depending on which Connected user ' .
                 'they are being viewed by.',
fb.'comm'     => 'Communication',
fb.'commdesc' => 'Facebook Connect ushers in a whole new level of networking. See which of your friends are using ' .
                 'the wiki, and optionally share your actions with your friends through the Facebook News Feed.',
fb.'welcome'  => 'Welcome, Facebook Connect user!',
fb.'loginbox' => "Or '''login''' with Facebook:\n\n$1",
fb.'merge'    => 'Merge your wiki account with your Facebook ID',
fb.'mergebox' => 'This feature has not yet been implemented. Accounts can be merged manually with [[Special:' .
                 'Renameuser]] if it is installed. For more information, please visit [[MediaWikiWiki:Extension:' .
                 "Renameuser|Extension:Renameuser]].\n\n\n$1\n\n\nNote: This can be undone by a sysop.",
fb.'logoutbox'=> "$1\n\nThis will also log you out of Facebook and all Connected sites, including this wiki.",
);
