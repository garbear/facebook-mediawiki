<?php

/**
 * FBConnect.php -- Make MediaWiki authenticate users against Facebook
 *
 * @author Garrett Brown
 */

if (!defined('MEDIAWIKI')) {
	exit( 1 );
}

define('MEDIAWIKI_FBCONNECT_VERSION', '0.4');

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Facebook Connect',
	'author' => 'Garrett Brown',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FBConnect',
	'description' => 'Facebook Connect',
	'descriptionmsg' => 'fbconnect-desc',
	'version' => MEDIAWIKI_FBCONNECT_VERSION,
);

/**
 * Initialization of the autoloaders, and special extension pages.
 */
$dir = dirname(__FILE__) . '/';
if (file_exists($dir . 'config.php')) {
	include_once							$dir . 'config.php';
} else {
	include_once							$dir . 'config.sample.php';
}

$wgAutoloadClasses['FBConnectAuthPlugin'] =	$dir . 'FBConnectAuthPlugin.php';
$wgAutoloadClasses['FBConnectHooks'] =		$dir . 'FBConnectHooks.php';
$wgAutoloadClasses['FBConnectClient'] =		$dir . 'FBConnectClient.php';
$wgAutoloadClasses['SpecialConnect'] =		$dir . 'SpecialConnect.php';

$wgExtensionMessagesFiles['FBConnect'] =	$dir . 'FBConnect.i18n.php';
$wgExtensionAliasesFiles['FBConnect'] =		$dir . 'FBConnect.alias.php';

$wgHooks['AuthPluginSetup'][] =			'FBConnectHooks::onAuthPluginSetup';
$wgHooks['UserLoadFromSession'][] =		'FBConnectHooks::onUserLoadFromSession';
$wgHooks['PersonalUrls'][]=				'FBConnectHooks::onPersonalUrls';
$wgHooks['BeforePageDisplay'][] =		'FBConnectHooks::onBeforePageDisplay';
$wgHooks['ParserAfterTidy'][] =			'FBConnectHooks::onParserAfterTidy';

$wgSpecialPages['Connect'] = 'SpecialConnect'; # Let MediaWiki know about your new special page.

$wgXhtmlNamespaces['fb'] = 'http://www.facebook.com/2008/fbml';
