<?php
/*
 * Copyright © 2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class FacebookApplication.
 * 
 * Represents an application created on Facebook. Apps can be modified from the
 * App Dashboard at https://developers.facebook.com/apps.
 * 
 * This class abstracts an application on Facebook. It allows for application
 * properties to be retrieved, and compares them against the values defined by the
 * global configuration variables. If a discrepancy is found, Special:Connect/Debug
 * will have a "Fixit" button that allows the administrator to quickly correct
 * application parameters.
 */
class FacebookApplication {
	static $roles;
	static $info;
	
	/**
	 * Constructor
	 */
	function __construct() {
		global $wgFbAppId, $wgFbSecret;
		$this->id = $wgFbAppId;
		$this->secret = $wgFbSecret;
	}
	
	/**
	 * Returns true if the specified Facebook user (default to current one) can
	 * edit properties for the application. The user must be an administrator
	 * or developer of the app and must also be an administrator of the wiki.
	 */
	function canEdit($fbUser = NULL) {
		global $facebook, $wgUser;
		if ( !( $fbUser instanceof FacebookUser ) ) {
			$fbUser = new FacebookUser();
		}
		
		// First, check MediaWiki permissions. Then check with Facebook
		if ( $fbUser->getMWUser()->getId() == 0 || $fbUser->getMWUser()->getId() != $wgUser->getId() )
			return false;
		
		// If $wgFbUserRightsFromGroups is set, this should trigger a group check
		$groups = $fbUser->getMWUser()->getEffectiveGroups();
		if ( !in_array('sysop', $groups) && !in_array('fb-admin', $groups) ) {
			return false;
		}
		
		// Check that the Facebook user has a development role with the application
		$roles = $this->getRoles();
		if ( !in_array( $fbUser->getId(), $roles['administrators'] ) &&
		     !in_array( $fbUser->getId(), $roles['developers'] ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns an array containing four roles (administrators, developers, testers
	 * and insights users), each role being a list of user IDs having that role.
	 */
	public function getRoles() {
		if ( empty( self::$roles ) ) {
			global $facebook;
			
			// Calls to an app's properties must be made with an app access token
			// https://developers.facebook.com/docs/reference/api/application/#application_access_tokens
			// The app access token looks like: (APP_ID . '|' . APP_SECRET)
			$user_access_token = $facebook->getAccessToken(); // Back that AcceSS up
			$facebook->setAccessToken($this->id . '|' . $this->secret);
			
			self::$roles = array(
					'administrators' => array(),
					'developers'     => array(),
					'testers'        => array(),
					'insights users' => array(),
			);
			try {
				$result = $facebook->api("/{$this->id}/roles");
				if ( isset( $result['data'] ) ) {
					foreach ( $result['data'] as $user ) {
						self::$roles[$user['role']][] = $user['user'];
					}
				}
			} catch (FacebookApiException $e) {
				error_log( $e->getMessage() );
			}
			
			// Restore the user access_token
			$facebook->setAccessToken($user_access_token);
		}
		return self::$roles;
	}
	
	/**
	 * Requests info about the application from Facebook.
	 * 
	 * The 'id' field will always be returned. If there was an error, this will
	 * be the only field in the returned array.
	 */
	public function getInfo() {
		if ( empty( self::$info ) ) {
			global $facebook;
			
			// Generate an array of the fields we wish to fetch
			$fields = array(
				// No access token required, immutable
				'name',                    // Application name
				'link',                    // URL
				'description',             // Description for the app that appears on News Feed stories
				'icon_url',                // The icon appears in Timeline events
				'logo_url',                // Logo
				'daily_active_users',      // Fun information
				'weekly_active_users',     // More fun information
				'monthly_active_users',    // More fun information
		
				// No access token required, editable via API
				'namespace',               // Should match $wgFbNamespace
		
				// App access token required, editable via API, max length in parentheses
				'app_domains',             // Array of domains
				//'auth_dialog_data_help_url', // I don't know what this URL should be
				'auth_dialog_description', // The description of an app that appears in the Auth Dialog (140)
				'auth_dialog_headline',    // One line description of an app that appears in the Auth Dialog (30)
				'auth_dialog_perms_explanation', // The text to explain why an app needs additional permissions that
				                                 // appears in the Auth Dialog. If you ask for any extended permissions,
				                                 // you should provide an explanation for how your app plans to use them.
				'contact_email',           // Should probably be $wgEmergencyContact
				'creator_uid',             // Application creator
				'deauth_callback_url',     // Deauthorization callback
				'privacy_policy_url',      // Should point to [[WikiName:Privacy_policy]]
				'terms_of_service_url',    // Should this point to [[WikiName:General_disclaimer]]?
				'user_support_email',      // See contact_email above
				'website_url',             // Should point to the main page
			);
			
			// Calls to an app's properties must be made with an app access token
			// https://developers.facebook.com/docs/reference/api/application/#application_access_tokens
			$user_access_token = $facebook->getAccessToken();
			$facebook->setAccessToken($this->id . '|' . $this->secret);
			
			try {
				self::$info = $facebook->api("/{$this->id}?fields=" . implode(',', $fields));
				// Fill in missing fields with false values
				foreach ( $fields as $field ) {
					if ( !isset( self::$info[$field] ) ) {
						self::$info[$field] = false;
					}
				}
			} catch (FacebookApiException $e) {
				error_log( $e->getMessage() );
				self::$info = array( 'id' => $this->id );
			}
			
			// Restore the user access_token
			$facebook->setAccessToken($user_access_token);
		}
		return self::$info;
	}
}
