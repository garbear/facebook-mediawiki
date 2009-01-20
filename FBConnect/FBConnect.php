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
 * @author Other Contributors (if any - credit yourself!)
 * @copyright Copyright © 2008 Garrett Brown
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @addtogroup Extensions
 * 
 * 
 * FBConnect plugin. Integrates Facebook Connect into MediaWiki.
 * 
 * Facebook Connect single signon (SSO) experience and XFBML are currently available.
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
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * FBConnect version. Note: this is not necessarily the most recent SVN revision number
 */
define('MEDIAWIKI_FBCONNECT_VERSION', '0.5 r16 (December 17, 2008)');

/**
 * Add extension information to Special:Version
 */
$wgExtensionCredits['other'][] = array(
	'name' => 'Facebook Connect Plugin',
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
require_once $dir . 'config.php';
require_once $dir . 'facebook-client/facebook.php';

$wgExtensionMessagesFiles['FBConnect'] =	$dir . 'FBConnect.i18n.php';
$wgExtensionAliasesFiles['FBConnect'] =		$dir . 'FBConnect.alias.php';

// Let MediaWiki know about your new special page... because we have one...
// $wgSpecialPages['Connect'] = 'SpecialConnect';
// $wgSpecialPages['RegisterNewsFeed'] = 'SpecialRegisterNewsFeed';


$wgAutoloadClasses['FBConnectAPI'] =		$dir . 'FBConnectAPI.php';
$wgAutoloadClasses['FBConnectAuthPlugin'] =	$dir . 'FBConnectAuthPlugin.php';
$wgAutoloadClasses['FBConnectHooks'] =		$dir . 'FBConnectHooks.php';
$wgAutoloadClasses['FBConnectXFBML'] =		$dir . 'FBConnectXFBML.php';

$wgExtensionFunctions[] = 'FBConnect::init';


FBConnect::$api = new FBConnectAPI();
/**
 * Class FBConnect
 * 
 * This class initializes the extension, and contains the core non-hook,
 * non-authentification code.
 */
class FBConnect {
	public static $api;
	
	/**
	 * Initializes and configures the extension.
	 */
	public static function init() {
		global $wgXhtmlNamespaces, $wgHooks;
		
		// The xmlns:fb attribute is required for proper rendering on IE
		$wgXhtmlNamespaces['fb'] = 'http://www.facebook.com/2008/fbml';
		
		// Install all public static functions in class FBConnectHooks as MediaWiki hooks
		$hooks = self::enumMethods('FBConnectHooks');
		foreach( $hooks as $hookName ) {
			$wgHooks["$hookName"][] = "FBConnectHooks::$hookName";
		}
		
		// ParserFirstCallInit was introduced in modern (1.12+) MW versions so as to
		// avoid unstubbing $wgParser on setHook() too early, as per r35980
		if (!defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' )) {
			global $wgParser;
			wfRunHooks( 'ParserFirstCallInit', $wgParser );
		}
		
		self::onUserLoadFromSession();
	}
	
	public static function onUserLoadFromSession() {
		if ( !isset($api) || $api === null )
			$api = new FBConnectAPI();
	}
	
	/**
	 * Returns an array with the names of all public static functions
	 * in the specified class.
	 */
	public static function enumMethods($className) {
		$hooks = array();
		try {
			$class = new ReflectionClass($className);
			foreach( $class->getMethods(ReflectionMethod::IS_PUBLIC) as $method ) {
				if ($method->isStatic()) {
					$hooks[] = $method->getName();
				}
			}
		} catch (Exception $e) {
			// If PHP's version doesn't support the Reflection API
			$hooks = array('AuthPluginSetup', 'UserLoadFromSession',
			               'RenderPreferencesForm', 'PersonalUrls',
			               'ParserAfterTidy', 'BeforePageDisplay');
		}
		return $hooks;
	}
}
