<?php
/*
 * Copyright © 2008-2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * @copyright Copyright © 2010 Garrett Brown, Sean Colombo
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// Make it so that the code will survive the push until the config gets updated.
$wgEnablePreferencesExt = true;

// Facebook version
define( 'MEDIAWIKI_FACEBOOK_VERSION', '3.0.0 (r375), January 3, 2011' );

// Magic string to use in substitution (must be defined prior to including config.php).
define( 'FACEBOOK_LOCALE', '%LOCALE%');

/*
 * Add information about this extension to Special:Version.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Facebook for MediaWiki',
	'author'         => 'Garrett Brown, Sean Colombo, Tomek Odrobny',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Facebook',
	'descriptionmsg' => 'facebook-desc',
	'version'        => MEDIAWIKI_FACEBOOK_VERSION,
);

/*
 * Initialization of the autoloaders and special extension pages.
 */
$dir = dirname( __FILE__ ) . '/';
// Load the default configuration
// It's recommended that you override these in LocalSettings.php
include_once $dir . 'config.default.php';
// If config.php exists, import those settings over the default ones
if (file_exists( $dir . 'config.php' )) {
	require_once $dir . 'config.php';
}
// Load the PHP SDK
require_once $dir . 'php-sdk/facebook.php';

// Install the extension
$wgExtensionFunctions[] = 'Facebook::init';

if( !empty( $wgFbEnablePushToFacebook ) ) {
	// Need to include it explicitly instead of autoload since it has initialization
	// code of its own. This should be done after Facebook::init is added to
	// $wgExtensionFunctions so that Facebook gets fully initialized first.
	require_once $dir . 'FacebookPushEvent.php';
}

$wgExtensionMessagesFiles['Facebook'] =	$dir . 'Facebook.i18n.php';
$wgExtensionMessagesFiles['FBPushEvents'] = $dir . 'pushEvents/FBPushEvents.i18n.php';
$wgExtensionMessagesFiles['FacebookLanguage'] = $dir . 'FacebookLanguage.i18n.php';
$wgExtensionAliasesFiles['Facebook'] =		$dir . 'Facebook.alias.php';

$wgAutoloadClasses['FacebookAPI'] =		$dir . 'FacebookAPI.php';
$wgAutoloadClasses['FacebookDB'] =			$dir . 'FacebookDB.php';
$wgAutoloadClasses['FacebookHooks'] =		$dir . 'FacebookHooks.php';
$wgAutoloadClasses['FacebookProfilePic'] =	$dir . 'FacebookProfilePic.php';
$wgAutoloadClasses['FacebookLanguage'] =   $dir . 'FacebookLanguage.php';
$wgAutoloadClasses['FacebookUser'] =		$dir . 'FacebookUser.php';
$wgAutoloadClasses['FacebookXFBML'] =		$dir . 'FacebookXFBML.php';
$wgAutoloadClasses['SpecialConnect'] =		$dir . 'SpecialConnect.php';
$wgAutoloadClasses['ChooseNameTemplate'] =	$dir . 'templates/ChooseNameTemplate.class.php';

$wgSpecialPages['Connect'] = 'SpecialConnect';

// Define new autopromote condition (use quoted text, numbers can cause collisions)
define( 'APCOND_FB_INGROUP',   'fb*g' );
define( 'APCOND_FB_ISOFFICER', 'fb*o' );
define( 'APCOND_FB_ISADMIN',   'fb*a' );

// rt#68127 - Giving basic permissions to other groups might open security holes
// See <http://trac.wikia-code.com/changeset/27160> and <http://trac.wikia-code.com/changeset/27928> for fix
$wgGroupPermissions['fb-user'] = $wgGroupPermissions['user']; // Create a new group for Facebook users

$wgAjaxExportList[] = 'Facebook::disconnectFromFB';
$wgAjaxExportList[] = 'SpecialConnect::getLoginButtonModal';
$wgAjaxExportList[] = 'SpecialConnect::ajaxModalChooseName'; 
$wgAjaxExportList[] = 'SpecialConnect::checkCreateAccount';

// These hooks need to be hooked up prior to init() because runhooks may be called for them before init is run.
$wgFbHooksToAddImmediately = array( 'SpecialPage_initList' );
foreach( $wgFbHooksToAddImmediately as $hookName ) {
	$wgHooks[$hookName][] = "FacebookHooks::$hookName";
}

/**
 * Class Facebook
 * 
 * This class initializes the extension, and contains the core non-hook,
 * non-authentification code.
 */
class Facebook {
	static private $fbOnLoginJs;
	
	/**
	 * Initializes and configures the extension.
	 */
	public static function init() {
		global $wgXhtmlNamespaces, $wgSharedTables, $facebook, $wgHooks,
		       $wgFbOnLoginJsOverride, $wgFbHooksToAddImmediately, $wgFbUserRightsFromGroup;
		
		// The xmlns:fb attribute is required for proper rendering on IE
		$wgXhtmlNamespaces['fb'] = 'http://www.facebook.com/2008/fbml';
		
		// Facebook/username associations should be shared when $wgSharedDB is enabled
		$wgSharedTables[] = 'user_fbconnect';
		
		// Create our Facebook instance and make it available through $facebook
		$facebook = new FacebookAPI();
		
		// Install all public static functions in class FacebookHooks as MediaWiki hooks
		$hooks = self::enumMethods( 'FacebookHooks' );
		foreach( $hooks as $hookName ) {
			if (!in_array( $hookName, $wgFbHooksToAddImmediately )) {
				$wgHooks[$hookName][] = "FacebookHooks::$hookName";
			}
		}

		// Allow configurable over-riding of the onLogin handler.
		if( !empty( $wgFbOnLoginJsOverride ) ) {
			self::$fbOnLoginJs = $wgFbOnLoginJsOverride;
		} else {
			self::$fbOnLoginJs = 'window.location.reload(true);';
		}
		
		// Default to pull new info from Facebook
		global $wgDefaultUserOptions;
		foreach (FacebookUser::$availableUserUpdateOptions as $option) {
			$wgDefaultUserOptions["facebook-update-on-login-$option"] = 1;
		}
		
		// If we are configured to pull group info from Facebook, then set up
		// the group permissions here
		if ( !empty( $wgFbUserRightsFromGroup ) ) {
			global $wgGroupPermissions, $wgImplictGroups, $wgAutopromote;
			$wgGroupPermissions['fb-groupie'] = $wgGroupPermissions['user'];
			$wgGroupPermissions['fb-officer'] = $wgGroupPermissions['bureaucrat'];
			$wgGroupPermissions['fb-admin'] = $wgGroupPermissions['sysop'];
			$wgImplictGroups[] = 'fb-groupie';
			$wgImplictGroups[] = 'fb-officer';
			$wgImplictGroups[] = 'fb-admin';
			$wgAutopromote['fb-groupie'] = APCOND_FB_INGROUP;
			$wgAutopromote['fb-officer'] = APCOND_FB_ISOFFICER;
			$wgAutopromote['fb-admin']   = APCOND_FB_ISADMIN;
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
			// Or list the extensions here manually...
			$hooks = array(
				'AuthPluginSetup', 'UserLoadFromSession',
				'RenderPreferencesForm', 'PersonalUrls',
				'ParserAfterTidy', 'BeforePageDisplay', /*...*/
			);
		}
		return $hooks;
	}
	
	/**
	 * Return the code for the permissions attribute (with leading space) to use on all fb:login-buttons.
	 */
	public static function getPermissionsAttribute() {
		global $wgFbExtendedPermissions;
		$attr = '';
		if (!empty($wgFbExtendedPermissions)) {
			$attr = ' perms="' . implode( ',', $wgFbExtendedPermissions ) . '"';
		}
		return $attr;
	} // end getPermissionsAttribute()
	
	/**
	 * Return the code for the onlogin attribute which should be appended to all fb:login-button's in this
	 * extension.
	 *
	 * TODO: Generate the entire fb:login-button in a function in this class.  We have numerous buttons now.
	 */
	public static function getOnLoginAttribute() {
		$attr = '';
		if ( !empty( self::$fbOnLoginJs ) ) {
			$attr = ' onlogin="' . self::$fbOnLoginJs . '"';
		}
		return $attr;
	} // end getOnLoginAttribute()
	
	public static function getFBButton( $onload = '', $id = '' ) {
		global $wgFbExtendedPermissions;
		return '<fb:login-button length="short" size="large" onlogin="' . $onload .
		       '" perms="' . implode(',', $wgFbExtendedPermissions) . '" id="' . $id .
		       '"></fb:login-button>';
	}
	
	/**
	 * Ajax function to disconect from Facebook.
	 */
	public static function disconnectFromFB( $user = null ) {
		$response = new AjaxResponse();
		$response->addText(json_encode(self::coreDisconnectFromFB($user)));
		return $response;
	}
	
	/**
	 * Facebook disconnect function and send mail with temp password.
	 */
	public static function coreDisconnectFromFB( $user = null ) {
		global $wgRequest, $wgUser, $wgAuth;
		
		wfLoadExtensionMessages('Facebook');
		
		if ($user == null) {
			$user = $wgUser;
		}
		$statusError = array('status' => 'error', 'msg' => wfMsg('facebook-unknown-error'));
		
		if ($user->getId() == 0) {
			return $statusError;
		}
		
		$dbw = wfGetDB( DB_MASTER, array(), FacebookDB::sharedDB() );
		$dbw->begin();
		$rows = FacebookDB::removeFacebookID($user);
		
		// Remind password attemp
		$params = new FauxRequest(array (
			'wpName' => $user->getName()
		));
		
		if( !$wgAuth->allowPasswordChange() ) {
			return $statusError;
		}
		
		$result = array();
		$loginForm = new LoginForm($params);
		
		if ($wgUser->getOption('fbFromExist')) {
			$res = $loginForm->mailPasswordInternal( $wgUser, true, 'facebook-passwordremindertitle-exist', 'facebook-passwordremindertext-exist' );
		} else {
			$res = $loginForm->mailPasswordInternal( $wgUser, true, 'facebook-passwordremindertitle', 'facebook-passwordremindertext' );
		}
		
		if( WikiError::isError( $res ) ) {
			return $statusError;
		}
				
		return array( 'status' => 'ok' );
		$dbw->commit();
		return $response;
	}
}
