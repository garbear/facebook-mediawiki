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
 */


/**
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
class FBConnectAPI {
	private static $__Facebook = null;
	
	// Stores a list of valid Facebook IDs for adding tooltip info
	private $ids = array();
	
	public function __construct() {
		/**
		if (is_null($this->__Facebook)) {
			$this->__Facebook = new Facebook($api_key, $)
		}
		/**/
	}
	
	/**
	 * Get the Facebook client object for easy access.
	 */
	public function Facebook() {
		global $fbApiKey, $fbApiSecret;
		// Construct a new Facebook object on first time access
		if ( is_null(self::$__Facebook) && $this->isConfigSetup() ) {
			self::$__Facebook = new Facebook( $fbApiKey, $fbApiSecret );
			if (!self::$__Facebook) {
				error_log('Could not create facebook client.');
			}
		}
		return self::$__Facebook;
	}
	
	/**
	 * Check to make sure config.sample.php was properly renamed to config.php
	 * and the instructions to fill out the first three important variables were
	 * followed correctly.
	 */
	public function isConfigSetup() {
		global $fbApiKey, $fbApiSecret;
		$isSetup = isset($fbApiKey) && $fbApiKey != 'YOUR_API_KEY' &&
		           isset($fbApiSecret) && $fbApiSecret != 'YOUR_API_SECRET' &&
		           !is_null($this->getCallbackUrl());
		if( !$isSetup )
			error_log( 'Please update the $fbApiKey in config.php' );
		return $isSetup;
	}
	
	/**
	 * Checks to see if the string is a valid (64-bit) Facebook ID.
	 */
	public function isIdValid( $id ) {
		if (!is_numeric( $id ))
			$id = intval( $id );
		return 0 < $id && $id < hexdec( 'FFFFFFFFFFFFFFFF' );
	}
	
	/**
	 * Returns true if the name of the global user $wgUser is a valid Facebook ID.
	 *
	public function isConnected() {
		global $wgUser;
		return $wgUser->isLoggedIn() && $this->isIdValid( $wgUser->getName() );
	}
	
	/**
	 * Retrieves the ID of the current logged-in user. If no user is logged in,
	 * then an ID of 0 is returned (I think).
	 */
	public function user() {
		return $this->Facebook()->get_loggedin_user();
	}
	
	/**
	 * Requests the user's full name from Facebook.
	 */
	public function getRealName( $user ) {
		// eventually we'll need the following info about the user:
		// "first_name, name, timezone, locale, username" and maybe "contact_email, proxied_email"
		$name = $this->getFields( $user->getName(), array( 'name' ));
		return $name[0]['name'];
	}
	
	/**
	 * Batches up user IDs so we can get their details with a single Platform query.
	 *
	public function addPersonById( $id ) {
		if ($this->isIdValid( $id ) && !in_array( $id, $this->ids )) {
			$this->ids[] = $id;
		}
	}
	
	/**
	 * Performs that queries requested by addPersonById().
	 *
	public function getPersons() {
		// If possible, switch to http://wiki.developers.facebook.com/index.php/Users.getStandardInfo
		//$user_details = $this->getClient()->api_client->users_getInfo($this->ids, array('last_name', 'first_name', 'pic', 'pic_big', 'pic_small', 'pic_square'));
		$user_details = $this->getFields( $this->ids, array('last_name', 'first_name', 'pic_square') );
		if (!is_null( $user_details )) {
			foreach ( $user_details as $user ) {
				$fbuid = "fb" . $user['uid'];
				$users->$fbuid->name = $user['first_name'] . ' ' . $user['last_name'];
				// If the user's picture is hidden, use a default generic picture
				$users->$fbuid->pic = ($user['pic_square'] != "" ? $user['pic_square'] :
				                       'http://static.ak.connect.facebook.com/pics/q_silhouette.gif');
			}
			return $users;
		}
		return array();
	}
	
	/**
	 * Returns the name of the group specified by $fbUserRightsFromGroup, or null if it is false.
	 */
	public function groupInfo() {
		global $fbUserRightsFromGroup;
		if ( !$fbUserRightsFromGroup ) {
			return null;
		}
		$group = $this->Facebook()->api_client->groups_get( null, $fbUserRightsFromGroup );
		$info = null;
		if ( is_array( $group ) && is_array( $group[0] )) {
			$info['name'] = $group[0]['name'];
			$info['creator'] = $group[0]['creator'];
			$info['picture'] = $group[0]['pic'];
		}
		return  $info;
	}
	
	/**
	 * Retrieves group membership data from Facebook as specified by $fbUserRightsFromGroup.
	 */
	public function getGroupRights( $user = null ) {
		global $fbUserRightsFromGroup;
		
		// Groupies can be members, officers or admins (the latter two infer the former)
		$rights = array('member'  => false,
		                'officer' => false,
		                'admin'   => false);
		
		// If no group ID is specified by $fbUserRightsFromGroup, then there's no group to belong to
		$gid = $fbUserRightsFromGroup;
		if( !$gid || !$this->user() ) {
			return $rights;
		}
		if( $user == null ) {
			$user = $this->user();
		} else if ( $user instanceof User ) {
			$user = $user->getName();
		}
		if( !FBConnect::$api->isIdValid( $user )) {
			return $rights;
		}
		if( is_int( $user )) {
			$user = "$user";
		}
		
		// Cache the rights for an individual user to prevent hitting Facebook for duplicate info
		static $rights_cache = array();
		if ( array_key_exists( $user, $rights_cache )) {
			// Retrieve the rights from the cache
			return $rights_cache[$user];
		}
		
		// This can contain up to 500 ids, avoid requesting this info twice
		static $members = false;
		// Get a random 500 group members, along with officers, admins and not_replied's
		if ( $members === false )
			$members = FBConnect::$api->getClient()->api_client->groups_getMembers( $gid );
		if ( $members ) {
			if( array_key_exists( 'officers', $members ) && in_array( $user, $members['officers'] )) {
				$rights['member'] = $rights['officer'] = true;
			}
			if( array_key_exists( 'admins', $members ) && in_array( $user, $members['admins'] )) {
				$rights['member'] = $rights['admin'] = true;
			}
			// Because the latter two rights infer the former, this step isn't always necessary
			if( !$rights['member'] ) {
				// Check to see if we are one of the (up to 500) random users
				if (( array_key_exists( 'not_replied', $members ) && is_array( $members['not_replied'] ) &&
					  in_array( $user, $members['not_replied'] )) || in_array( $user, $members['members'] ))  {
					$rights['member'] = true;
				} else {
					// For groups of over 500ish, we must use this extra API call
					// Notice that it occurs last, because we can hopefully avoid having to call it
					$group = FBConnect::$api->getClient()->api_client->groups_get( intval( $user ), $gid );
					if( is_array( $group ) && is_array( $group[0] ) && $group[0]['gid'] == "$gid" ) {
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
	 * Fetch fields about a user from Facebook.
	 *
	 * If performance is an issue, then you may want to implement caching on top of this
	 * function. The cache would have to be cleared every 24 hours.
	 */
	private function getFields( $fb_uids, $fields ) {
		try {
			$user_details = $this->getClient()->api_client->users_getInfo( $fb_uids, $fields );
		} catch( Exception $e ) {
			error_log( 'Failure in the api when requesting ' . join( ',', $fields ) .
			           ' on uid ' . $fb_uids . ' : ' . $e->getMessage() );
			return null;
		}
		return $user_details ? ( !empty( $user_details ) ? $user_details : null ) : null;
	}

	/**
	 * Retrieves the proxied email address of a particular user. This function is untested.
	 * See: http://wiki.developers.facebook.com/index.php/Proxied_Email
	 * 
	 * @Unused
	 * 
	 * @TODO: Test this function!
	 */
	public function getProxiedEmail( $ids ) {
		return self::getClient()->api_client->users_getInfo($ids, 'proxied_email');
	}
	
	/**
	 * Returns the "public" hash of the email address (i.e., the one Facebook
	 * gives out via their API). The hash is of the form crc32($email)_md5($email).
	 * 
	 * @Unused (for now)
	 */
	public function hashEmail($email) {
		if ($email == null)
			return '';
		$email = trim( strtolower( $email ));
		return crc32( $email ) . '_' . md5( $email );
	}
}
