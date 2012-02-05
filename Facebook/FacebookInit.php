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
 * non-authentication code.
 */
class FacebookInit {
	/**
	 * Initializes and configures the extension.
	 */
	public static function init() {
		global $wgSharedTables, $facebook, $wgHooks, $wgResourceModules;
		
		// The xmlns:fb attribute is required for proper rendering on IE
		global $wgXhtmlNamespaces;
		$wgXhtmlNamespaces['fb'] = 'https://www.facebook.com/2008/fbml';
		
		// Disable $wgHtml5 so that our Open Graph xmlns tags show up
		global $wgFbOpenGraph, $wgHtml5;
		if ( !empty( $wgFbOpenGraph ) ) {
			$wgHtml5 = false;
			$wgXhtmlNamespaces['og'] = 'http://ogp.me/ns#';
		}
		
		// Facebook/username associations should be shared when $wgSharedDB is enabled
		$wgSharedTables[] = 'user_fbconnect';
		
		// Create our Facebook instance and make it available through $facebook
		$facebook = new FacebookAPI();
		
		// Load JavaScript client libraries (MediaWiki 1.17+)
		$moduleInfo = array(
			'localBasePath' => dirname( __FILE__ ) . '/modules',
			'remoteExtPath' => 'Facebook/modules',
		);
		
		// Note that the JS SDK depends on the facebook module, not the other
		// way around. This is because the facebook module installs a hook that
		// must be called by the JS SDK upon completion.
		$wgResourceModules['ext.facebook'] = array(
			'scripts'       => 'ext.facebook.js',
		) + $moduleInfo;
		
		$wgResourceModules['ext.facebook.sdk'] = array(
			'scripts'       => 'ext.facebook.sdk.js',
			'dependencies'  => array( 'ext.facebook' ),
			'position'      => 'top',
		) + $moduleInfo;
		
		$wgResourceModules['ext.facebook.actions'] = array(
			'scripts'       => 'ext.facebook.actions.js',
			'dependencies'  => array( 'ext.facebook.sdk' ),
			'position'      => 'bottom',
		) + $moduleInfo;
		
		$wgResourceModules['ext.facebook.application'] = array(
				'scripts'       => 'ext.facebook.application.js',
				'dependencies'  => array( 'ext.facebook.sdk' ),
				'position'      => 'bottom',
		) + $moduleInfo;
		
		// Install all public static functions in class FacebookHooks as MediaWiki hooks
		global $wgFbHooksToAddImmediately;
		$hooks = self::enumMethods( 'FacebookHooks' );
		foreach( $hooks as $hookName ) {
			if (!in_array( $hookName, $wgFbHooksToAddImmediately )) {
				$wgHooks[$hookName][] = "FacebookHooks::$hookName";
			}
		}
		
		// Install FacebookTimeline hooks
		global $wgFbOpenGraphRegisteredActions;
		if (!empty($wgFbOpenGraph) && !empty($wgFbOpenGraphRegisteredActions)) {
			$hooks = FacebookInit::enumMethods( 'FacebookTimeline' );
			foreach( $hooks as $hookName ) {
				$wgHooks[$hookName][] = "FacebookTimeline::$hookName";
			}
		}
		
		// Default to pull new info from Facebook
		global $wgDefaultUserOptions;
		foreach (FacebookUser::$availableUserUpdateOptions as $option) {
			$wgDefaultUserOptions["facebook-update-on-login-$option"] = 1;
		}
		
		// Set up group autopromote conditions
		global $wgFbUserRightsFromGroup;
		if ( !empty( $wgFbUserRightsFromGroup ) ) {
			global $wgImplicitGroups, $wgAutopromote;
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
}
