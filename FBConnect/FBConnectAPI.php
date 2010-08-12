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


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class FBConnectAPI
 * 
 * This class contains the code used to interface with Facebook via the
 * Facebook Platform API. 
 */
class FBConnectAPI extends Facebook {
	// Constructor
	public function __construct() {
		global $wgFbAppId, $wgFbSecret, $wgFbDomain;
		// Check to make sure config.sample.php was renamed properly
		if ( !$this->isConfigSetup() ) {
			exit( 'Please update $wgFbAppId and $wgFbSecret in config.php' );
		}
		$config = array(
			'appId' => $wgFbAppId,
			'secret' => $wgFbSecret,
			'cookie' => true,
		);
		// Include the optional domain parameter if it has been set
		if ( !empty($wgFbDomain) && $wgFbDomain != 'BASE_DOMAIN' ) {
			$config['domain'] = $wgFbDomain;
		}
		parent::__construct( $config );
	}
	
	/**
	 * Check to make sure config.sample.php was properly renamed to config.php
	 * and the instructions to fill out the first two important variables were
	 * followed correctly.
	 */
	public function isConfigSetup() {
		global $wgFbAppId, $wgFbSecret;
		$isSetup = isset($wgFbAppId) && $wgFbAppId != 'YOUR_APP_KEY' &&
		           isset($wgFbSecret) && $wgFbSecret != 'YOUR_SECRET';
		if(!$isSetup) {
			// Check to see if they are still using the old variables
			global $fbApiKey, $fbApiSecret;
			if ( isset($fbApiKey) ) {
				$wgFbAppId = $fbApiKey;
			}
			if ( isset($fbApiSecret) ) {
				$wgFbSecret= $fbApiSecret;
			}
			$isSetup = isset($wgFbAppId) && $wgFbAppId != 'YOUR_APP_KEY' &&
		               isset($wgFbSecret) && $wgFbSecret != 'YOUR_SECRET';
		}
		return $isSetup;
	}
	
	/**
	 * Calls users.getInfo. Requests information about the user from Facebook.
	 */
	public function getUserInfo() {
		// First check to see if we have a session (if not, return null)
		$user = $this->getUser();
		if ( !$user ) {
			return null;
		}
		try {
			// Cache information about users to reduce the number of Facebook hits
			static $userinfo = array();
			
			if ( !isset($userinfo[$user]) ) {
				// Query the Facebook API with the users.getInfo method
				$query = array(
						'method' => 'users.getInfo',
						'uids' => $user,
						'fields' => join(',', array(
							'first_name', 'name', 'sex', 'timezone', 'locale',
							/*'profile_url',*/
							'username', 'proxied_email', 'contact_email',
						)),
				);
				$user_details = $this->api( $query );
				// Cache the data in the $userinfo array
				$userinfo[$user] = $user_details[0];
			}
			return isset($userinfo[$user]) ? $userinfo[$user] : null;
		} catch (FacebookApiException $e) {
			error_log( 'Failure in the api when requesting users.getInfo ' .
			           "on uid $user: " . $e->getMessage());
		}
		return null;
	}
	
	/**
	 * Retrieves group membership data from Facebook.
	 */
	public function getGroupRights( $user = null ) {
		global $wgFbUserRightsFromGroup;
		
		// Groupies can be members, officers or admins (the latter two infer the former)
		$rights = array('member'  => false,
		                'officer' => false,
		                'admin'   => false);
		
		$gid = !empty($wgFbUserRightsFromGroup) ? $wgFbUserRightsFromGroup : false;
		if (// If no group ID is specified, then there's no group to belong to
			!$gid ||
			// If $user wasn't specified, set it to the logged in user
			!$user ||
			// If there is no logged in user
			!($user = $this->getUser())
		) {
			return $rights;
		}
		
		// If a User object was provided, translate it into a Facebook ID
		if ($user instanceof User) {
			// TODO: Does this call for a special api call without access_token?
			$users = FBConnectDB::getFacebookIDs($user);
			if (count($users)) {
				$user = $users[0];
			} else {
				// Not a Connected user, can't be in a group
				return $rights;
			}
		}
		
		// Cache the rights for an individual user to prevent hitting Facebook for duplicate info
		static $rights_cache = array();
		if ( array_key_exists( $user, $rights_cache )) {
			// Retrieve the rights from the cache
			return $rights_cache[$user];
		}
		
		// This can contain up to 500 IDs, avoid requesting this info twice
		static $members = false;
		// Get a random 500 group members, along with officers, admins and not_replied's
		if ($members === false) {
			try {
				// Check to make sure our session is still valid
				$members = $this->api(array(
						'method' => 'groups.getMembers',
						'gid' => $gid
				));
			} catch (FacebookApiException $e) {
				// Invalid session; we're not going to be able to get the rights
				error_log($e);
				$rights_cache[$user] = $rights;
				return $rights;
			}
		}
		
		if ( $members ) {
			// Check to see if the user is an officer
			if (array_key_exists('officers', $members) && in_array($user, $members['officers'])) {
				$rights['member'] = $rights['officer'] = true;
			}
			// Check to see if the user is an admin of the group
			if (array_key_exists('admins', $members) && in_array($user, $members['admins'])) {
				$rights['member'] = $rights['admin'] = true;
			}
			// Because the latter two rights infer the former, this step isn't always necessary
			if( !$rights['member'] ) {
				// Check to see if we are one of the (up to 500) random users
				if ((array_key_exists('not_replied', $members) && is_array($members['not_replied']) &&
					in_array($user, $members['not_replied'])) || in_array($user, $members['members'])) {
					$rights['member'] = true;
				} else {
					// For groups of over 500ish, we must use this extra API call
					// Notice that it occurs last, because we can hopefully avoid having to call it
					try {
						$group = $this->api(array(
								'method' => 'groups.get',
								'uid' => $user,
								'gids' => $gid
						));
					} catch (FacebookApiException $e) {
						error_log($e);
					}
					if (is_array($group) && is_array($group[0]) && $group[0]['gid'] == "$gid") {
						$rights['member'] = true;
					}
				}
			}
		}
		// Cache the rights
		$rights_cache[$user] = $rights;
		return $rights;
	}
	
	/*
	 * Publish message on Facebook wall.
	 */
	public function publishStream($message, $link = "", $link_title ) {
		/*
		$attachment = array(
			'name' => $message,
			'href' => $link,
			'caption' => $caption,
			'description' => $description
		);
		
		if( count($media) > 0 ) {
			foreach ( $media as $value ) {
				$attachment[ 'media' ][] = $value;
			}
		}
		/**/
		// $api_error_descriptions
		$attachment = array();
		
		$query = array(
			'method' => 'stream.publish',
			'attachment' => json_encode($attachment),
			'action_links' => json_encode(array(
				'text' => $link_title,
				'href' => $link
			)),
		);
		
		$result = json_decode( $this->api( $query ) );
		
		if ( is_array( $result ) ) {
			// Error
			#error_log(FacebookAPIErrorCodes::$api_error_descriptions[$result]);
			error_log("stream.publish returned error code $result->error_code");
			return false;
		} else if ( is_string( $result ) ) {
			// Success! Return value is "$UserId_$PostId"
			return true;
		} else {
			error_log("stream.publish: Unknown return type: " . gettype($result));
			return false;
		}
	} 
}
