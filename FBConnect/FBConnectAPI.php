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
	// Stores a list of valid Facebook IDs for adding tooltip info
	private $ids = array();
	
	/**
	 * Retrieves the application's callback url as specified in the app's
	 * developer settings. Eventually, this function may be replaced by code
	 * that queries Facebook for this info, but for now it pulls the URL out
	 * of the variable $fbCallbackURL specified in config.php.
	 */
	public function getCallbackUrl() {
		global $fbCallbackURL;
		return isset( $fbCallbackURL ) ? $fbCallbackURL : '';
	}
	
	/**
	 * Returns the root of the Facebook site we'll be hitting.
	 */
	public function getBaseUrl() {
		global $fbBaseURL;
		return isset( $fbBaseURL ) ? $fbBaseURL : '';
	}
	
	/**
	 * Turns the base URL into a fully qualified, usable URL.
	 */
	public function getStaticRoot() {
		return 'http://static.ak.' . $this->getBaseUrl();
	}
	
	/**
	 * Checks to see if the string is a valid (64-bit) Facebook ID.
	 */
	public function isIdValid( $id ) {
		if (!is_numeric( $id ))
			$id = intval( $id );
		return 0 < $id && $id < hexdec("FFFFFFFFFFFFFFFF");
	}
	
	/**
	 * Returns true if the name of the global user $wgUser is a valid Facebook ID.
	 */
	public function isConnected() {
		global $wgUser;
		return $wgUser->isLoggedIn() && $this->isIdValid( $wgUser->getName() );
	}
	
	/*
	 * Get the Facebook client object for easy access.
	 */
	public function getClient() {
		global $fbApiKey, $fbApiSecret;
		
		static $facebook = null;
		
		if ( $facebook === null && $this->isConfigSetup() ) {
			$facebook = new Facebook( $fbApiKey, $fbApiSecret, false, $this->getBaseUrl() );
			if (!$facebook) {
				error_log('Could not create facebook client.');
			}
			/**
			// Create a Facebook session
			if (isset($_SESSION)) {
				echo 'Error: isset($_SESSION)';
			}
			$session_key = md5(self::getClient()->api_client->session_key);
			session_id( $session_key );
			session_start();
			/**/
		}
		return $facebook;
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
		           $this->getCallbackUrl() != null;
		if( !$isSetup )
			error_log( 'Please update the $fbApiKey in config.php' );
		return $isSetup;
	}
	
	/**
	 * A simple question of whether the site is "connected" or not.
	 */
	public function isEnabled() {
		if (!$this->isConfigSetup()) {
			return false;
		}
		// Changing this disables Facebook Connect
		return true;
	}
	
	/**
	 * Retrieves the ID of the current logged-in user. If no user is logged in,
	 * then an ID of 0 is returned (I think).
	 */
	public function user() {
		return $this->getClient()->get_loggedin_user();
	}
	
	/**
	 * Batches up user IDs so we can get their details with a single Platform query.
	 */
	public function addPersonById( $id ) {
		if ($this->isIdValid( $id ) && !in_array( $id, $this->ids )) {
			$this->ids[] = $id;
		}
	}
	
	/**
	 * Performs that query.
	 */
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
		}
		return $users;
	}
	
	/*
	 * Fetch fields about a user from Facebook.
	 *
	 * If performance is an issue, then you may want to implement caching on top of this
	 * function. The cache would have to be cleared every 24 hours.
	 */
	public function getFields( $fb_uids, $fields ) {
		try {
			$user_details = $this->getClient()->api_client->users_getInfo( $fb_uids, $fields );
		} catch( Exception $e ) {
			error_log( 'Failure in the api when requesting ' . join( ',', $fields ) .
			           ' on uid ' . $fb_uids . ' : ' . $e->getMessage() );
			return null;
		}
		return empty( $user_details ) ? null : $user_details;
	}

	/**
	 * Retrieves the proxied email address of a particular user.
	 * See: http://wiki.developers.facebook.com/index.php/Proxied_Email
	 * 
	 * @TODO: Test this function!
	 */
	public function getProxiedEmail( $ids ) {
		return self::getClient()->api_client->users_getInfo($ids, 'proxied_email');
	}
	
	/**
	 * Returns the "public" hash of the email address (i.e., the one Facebook
	 * gives out via their API). The hash is of the form crc32($email)_md5($email).
	 */
	public function hashEmail($email) {
		if ($email == null)
			return '';
		$email = trim( strtolower( $email ));
		return crc32( $email ) . '_' . md5( $email );
	}
}
