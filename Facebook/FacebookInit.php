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
 * Class FacebookInit
 * 
 * This class initializes the extension, and contains the core non-hook,
 * non-authentification code.
 */
class FacebookInit {
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
			global $wgGroupPermissions, $wgImplicitGroups, $wgAutopromote;
			$wgGroupPermissions['fb-groupie'] = $wgGroupPermissions['user'];
			$wgGroupPermissions['fb-admin'] = $wgGroupPermissions['sysop'];
			$wgImplicitGroups[] = 'fb-groupie';
			$wgImplicitGroups[] = 'fb-admin';
			$wgAutopromote['fb-groupie'] = APCOND_FB_INGROUP;
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
		       '" perms="' . implode( ',', $wgFbExtendedPermissions ) . '" id="' . $id .
		       '">' . wfMsg('fbconnect-log-in') . '</fb:login-button>';
	}
}
