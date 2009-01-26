<?php
/**
 * Copyright © 2008 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * 
 * @author Garrett Bruin
 * @copyright Copyright © 2008 Garrett Brown
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @addtogroup Extensions
 * 
 * 
 * FBConnect plugin. Integrates Facebook Connect into MediaWiki.
 * 
 * Facebook Connect single sign on (SSO) experience and XFBML are currently available.
 * Please rename config.sample.php to config.php, follow the instructions inside and
 * customize variables as you like to set up your Facebook Connect extension.
 *
 * Info is available at http://www.mediawiki.org/wiki/Extension:FBConnect
 * and at http://wiki.developers.facebook.com/index.php/MediaWiki
 * 
 * Limited support is available at  http://www.mediawiki.org/wiki/Extension_talk:FBConnect
 * and at http://wiki.developers.facebook.com/index.php/Talk:MediaWiki
 *  
 */


/**
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' )) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * FBConnect version. Note: this is not necessarily the most recent SVN revision number.
 */
define( 'MEDIAWIKI_FBCONNECT_VERSION', 'r71, January 25, 2009' );

/**
 * Add information about this extension to Special:Version.
 */
$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'Facebook Connect Plugin',
	'author'         => 'Garrett Brown',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:FBConnect',
	'descriptionmsg' => 'fbconnect-desc',
	'version'        => MEDIAWIKI_FBCONNECT_VERSION,
);

/**
 * Initialization of the autoloaders and special extension pages.
 */
$dir = dirname(__FILE__) . '/'; 
require_once $dir . 'config.php';
require_once $dir . 'facebook-client/facebook.php';

$wgExtensionMessagesFiles['FBConnect'] =	$dir . 'FBConnect.i18n.php';
$wgExtensionAliasesFiles['FBConnect'] =		$dir . 'FBConnect.alias.php';

$wgAutoloadClasses['FBConnectAPI'] =		$dir . 'FBConnectAPI.php';
$wgAutoloadClasses['FBConnectAuthPlugin'] =	$dir . 'FBConnectAuthPlugin.php';
$wgAutoloadClasses['FBConnectHooks'] =		$dir . 'FBConnectHooks.php';
$wgAutoloadClasses['FBConnectXFBML'] =		$dir . 'FBConnectXFBML.php';
$wgAutoloadClasses['SpecialConnect'] =		$dir . 'SpecialConnect.php';

$wgSpecialPages['Connect'] = 'SpecialConnect';
#$wgSpecialPages['NewsFeed'] = 'SpecialNewsFeed';

$wgExtensionFunctions[] = 'FBConnect::init';

// If we are configured to pull group info from Facebook, then create the group permissions
if( $fbUserRightsFromGroup ) {
	$wgGroupPermissions['fb-user'] = $wgGroupPermissions['user'];
	$wgGroupPermissions['fb-groupie'] = $wgGroupPermissions['user'];
	$wgGroupPermissions['fb-officer'] = $wgGroupPermissions['bureaucrat'];
	$wgGroupPermissions['fb-admin'] = $wgGroupPermissions['sysop'];
	$wgGroupPermissions['fb-officer']['goodlooking'] = true;
}

/*
// Define new autopromote condition (use quoted text, numbers can cause collisions)
define( 'APCOND_FB_INGROUP',   'fb*g' );
define( 'APCOND_FB_ISOFFICER', 'fb*o' );
define( 'APCOND_FB_ISADMIN',   'fb*a' );

$wgAutopromote['fb-groupie'] = APCOND_FB_INGROUP;
$wgAutopromote['fb-officer'] = APCOND_FB_ISOFFICER;
$wgAutopromote['fb-admin']   = APCOND_FB_ISADMIN;

//$wgImplicitGroups[] = 'fb-groupie';
/**/


/**
 * Class FBConnect
 * 
 * This class initializes the extension, and contains the core non-hook,
 * non-authentification code.
 */
class FBConnect {
	// Instance of our Facebook API class
	public static $api;
	// Whether we are rendering the Special:Connect page
	public static $special_connect;
	
	/**
	 * Initializes and configures the extension.
	 */
	public static function init() {
		global $wgXhtmlNamespaces, $wgHooks;
		
		self::$special_connect = false;
		self::$api = new FBConnectAPI();
		
		// The xmlns:fb attribute is required for proper rendering on IE
		$wgXhtmlNamespaces['fb'] = 'http://www.facebook.com/2008/fbml';
		
		// Install all public static functions in class FBConnectHooks as MediaWiki hooks
		$hooks = self::enumMethods('FBConnectHooks');
		foreach( $hooks as $hookName ) {
			$wgHooks[$hookName][] = "FBConnectHooks::$hookName";
		}
		
		// ParserFirstCallInit was introduced in modern (1.12+) MW versions so as to
		// avoid unstubbing $wgParser on setHook() too early, as per r35980
		if (!defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' )) {
			global $wgParser;
			wfRunHooks( 'ParserFirstCallInit', $wgParser );
		}
	}
	
	/**
	 * Returns an array with the names of all public static functions
	 * in the specified class.
	 */
	public static function enumMethods( $className ) {
		$hooks = array();
		try {
			$class = new ReflectionClass( $className );
			foreach( $class->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
				if ( $method->isStatic() ) {
					$hooks[] = $method->getName();
				}
			}
		} catch( Exception $e ) {
			// If PHP's version doesn't support the Reflection API, then exit
			die( 'PHP version (' . phpversion() . ') must be great enough to support the Reflection API' );
			// or...
			$hooks = array('AuthPluginSetup', 'UserLoadFromSession',
			               'RenderPreferencesForm', 'PersonalUrls',
			               'ParserAfterTidy', 'BeforePageDisplay');
		}
		return $hooks;
	}
}
