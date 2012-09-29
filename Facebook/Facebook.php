<?php
/*
 * Copyright © 2008-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Facebook for MediaWiki.
 * 
 * Integrates Facebook authentication and features into MediaWiki.
 * 
 * Info is available at <http://www.mediawiki.org/wiki/Extension:Facebook>.
 * Limited support is available at
 * <http://www.mediawiki.org/wiki/Extension_talk:Facebook>.
 * 
 * @file
 * @ingroup Extensions
 * @author Garrett Brown, Sean Colombo
 * @copyright Copyright © 2008-2012 Garrett Brown, Sean Colombo
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// Facebook version
define( 'MEDIAWIKI_FACEBOOK_VERSION', '4.0.6-stable, September 28, 2012' );

// Magic string to use in substitution (must be defined prior to including config.php).
define( 'FACEBOOK_LOCALE', '%LOCALE%');

// Add information about this extension to Special:Version.
$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Facebook Open Graph for MediaWiki',
	'author'         => 'Garrett Brown, Sean Colombo, Tomek Odrobny',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Facebook',
	'descriptionmsg' => 'facebook-desc',
	'version'        => MEDIAWIKI_FACEBOOK_VERSION,
);

// Load the default config. It's recommended that you override these in LocalSettings.php
$dir = dirname( __FILE__ ) . '/';
include_once $dir . 'config.default.php';
if (file_exists( $dir . 'config.php' )) {
	// If config.php exists, import those settings over the default ones
	require_once $dir . 'config.php';
}

// Install the extension
$wgExtensionFunctions[] = 'FacebookInit::init';

$wgExtensionMessagesFiles['Facebook'] = $dir . 'Facebook.i18n.php';
$wgExtensionMessagesFiles['FacebookLanguage'] = $dir . 'FacebookLanguage.i18n.php';
$wgExtensionAliasesFiles['Facebook'] = $dir . 'Facebook.alias.php';

$wgAutoloadClasses['FacebookAPI'] = $dir . 'FacebookAPI.php';
$wgAutoloadClasses['FacebookApplication'] = $dir . 'FacebookApplication.php';
$wgAutoloadClasses['FacebookDB'] = $dir . 'FacebookDB.php';
$wgAutoloadClasses['FacebookHooks'] = $dir . 'FacebookHooks.php';
$wgAutoloadClasses['FacebookInit'] = $dir . 'FacebookInit.php';
$wgAutoloadClasses['FacebookLanguage'] = $dir . 'FacebookLanguage.php';
$wgAutoloadClasses['FacebookOpenGraph'] = $dir . 'FacebookOpenGraph.php';
$wgAutoloadClasses['FacebookTimeline'] = $dir . 'FacebookTimeline.php';
$wgAutoloadClasses['FacebookUser'] = $dir . 'FacebookUser.php';
$wgAutoloadClasses['FacebookXFBML'] = $dir . 'FacebookXFBML.php';
$wgAutoloadClasses['SpecialConnect'] = $dir . 'SpecialConnect.php';

// Special:Connect and accompanying AJAX modules
$wgSpecialPages['Connect'] = 'SpecialConnect';

$wgAutoloadClasses['ApiFacebookChooseName']        = $dir . 'FacebookAJAX.php';
$wgAutoloadClasses['ApiFacebookLogoutAndContinue'] = $dir . 'FacebookAJAX.php';
$wgAutoloadClasses['ApiFacebookMergeAccount']      = $dir . 'FacebookAJAX.php';

$wgAPIModules['facebookchoosename']        = 'ApiFacebookChooseName';
$wgAPIModules['facebookmergeaccount']      = 'ApiFacebookMergeAccount';
$wgAPIModules['facebooklogoutandcontinue'] = 'ApiFacebookLogoutAndContinue';


// Define new autopromote condition (use quoted text, numbers can cause collisions)
define( 'APCOND_FB_USER',      'fb*u' );
define( 'APCOND_FB_INGROUP',   'fb*g' );
define( 'APCOND_FB_ISADMIN',   'fb*a' );

// Add 'fb-user' group to Facebook users and grant the 'facebook-user' right
$wgImplicitGroups[] = 'fb-user';
$wgAutopromote['fb-user'] = APCOND_FB_USER;
$wgGroupPermissions['fb-user'] = array('facebook-user' => true);

// These hooks need to be hooked up prior to init() because runhooks may be called for them before init is run.
$wgFbHooksToAddImmediately = array( 'SpecialPage_initList', 'LanguageGetMagic' );
foreach( $wgFbHooksToAddImmediately as $hookName ) {
	$wgHooks[$hookName][] = "FacebookHooks::$hookName";
}
