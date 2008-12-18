<?php
/**
 * Copyright © 2008 Garrett Bruin <http://www.mediawiki.org/wiki/User:Gbruin>
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

$wgAutoloadClasses['FBConnectAuthPlugin'] =	$dir . 'FBConnectAuthPlugin.php';
$wgAutoloadClasses['FBConnectHooks'] =		$dir . 'FBConnectHooks.php';
$wgAutoloadClasses['FBConnectXFBML'] =		$dir . 'FBConnectXFBML.php';

$wgExtensionFunctions[] = 'FBConnect::setup';



/**
 * Class FBConnect
 * 
 * This class initializes the extension, and contains the core non-hook,
 * non-authentification code.
 */
class FBConnect {
	/**
	 * Initializes and configures the extension.
	 */
	public static function setup() {
		global $wgHooks, $wgXhtmlNamespaces;
		
		// The xmlns:fb attribute is required for proper rendering on IE
		$wgXhtmlNamespaces['fb'] = 'http://www.facebook.com/2008/fbml';
		
		// Install all public static functions in class FBConnectHooks as MediaWiki hooks
		$hooks = FBConnect::enumMethods('FBConnectHooks');
		foreach( $hooks as $hookName ) {
			$wgHooks["$hookName"][] = "FBConnectHooks::$hookName";
		}
		
		// ParserFirstCallInit was introduced in modern (1.12+) MW versions to
		// avoid unstubbing $wgParser on setHook() too early, as per r35980
		if (!defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' )) {
			global $wgParser;
			wfRunHooks( 'ParserFirstCallInit', $wgParser );
		}
		return true;
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
	
	/*
	 * Get the facebook client object for easy access.
	 */
	public static function getClient() {
		static $facebook = null;
		static $is_config_setup = false;
		if (!$is_config_setup) {
			if (self::get_api_key() && self::get_api_key() != 'YOUR_API_KEY' &&
			        self::get_api_secret() && self::get_api_secret() != 'YOUR_API_SECRET' &&
			        self::get_callback_url() != null) {
				$is_config_setup = true;
			} else {
				echo "Error: please update the api_key in your configuration.<br>\n";
				return null;
			}
		}
		if ($facebook === null) {
			$facebook = new Facebook(self::get_api_key(), self::get_api_secret());
			if (!$facebook) {
				error_log('Could not create facebook client.');
			}
		}
		return $facebook;
	}

	// Make sure our environment variables were set corrently
	public static function is_config_setup() {
		return getClient() != null;
	}

	// Whether the site is "connected" or not
	public static function is_enabled() {
		if (!self::is_config_setup()) {
			return false;
		}
		// Change this if you want to turn off Facebook connect
		return true;
	}

	public static function get_api_key() {
		global $api_key;
		return $api_key;
	}

	public static function get_api_secret() {
		global $api_secret;
		return $api_secret;
	}

	public static function get_base_fb_url() {
		global $base_fb_url;
		return $base_fb_url;
	}

	public static function get_static_root() {
		return 'http://static.ak.' . self::get_base_fb_url();
	}

	public static function get_feed_bundle_id() {
		global $feed_bundle_id;
		return $feed_bundle_id;
	}

	public static function get_callback_url() {
		global $callback_url;
		return isset($callback_url) ? $callback_url : null;
	}


	### Facebook Client functions (two useful functions from The Run Around's lib/fbconnect.php)


	/*
	 * Fetch fields about a user from Facebook.
	 *
	 * If performance is an issue, then you may want to implement caching on top of this
	 * function. The cache would have to be cleared every 24 hours.
	 */
	public static static function get_fields($fb_uid, $fields) {
		try {
			$infos = self::getClient()->api_client->users_getInfo($fb_uid, $fields);
			if (empty($infos)) {
				return null;
			}
			return reset($infos);
		} catch (Exception $e) {
			error_log("Failure in the api when requesting " . join(",", $fields) .
			          " on uid " . $fb_uid . " : ". $e->getMessage());
			return null;
		}
	}

	/**
	 * Returns the "public" hash of the email address, i.e., the one we give out via our API.
	 *
	 * @param  string $email An email address to hash
	 * @return string        A public hash of the form crc32($email)_md5($email)
	 */
	public static function email_get_public_hash($email) {
		if ($email != null) {
			$email = trim(strtolower($email));
			return crc32($email) . '_' . md5($email);
		} else {
			return '';
		}
	}
	
}
