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
	private $ids = array();
	
	/**
	 * Checks to see if the string is a valid (64-bit) Facebook ID.
	 */
	public function isIdValid( $id ) {
		if (!is_numeric( $id ))
			$id = intval( $id );
		return 0 < $id && $id < hexdec("FFFFFFFFFFFFFFFF");
	}
	
	/*
	 * Does something
	 */
	public function addPersonById( $id ) {
		if (self::isIdValid( $id ) && !in_array( $id, $this->ids )) {
			$this->ids[] = $id;
		}
	}
	
	public function getPersons() {
		// If possible, switch to http://wiki.developers.facebook.com/index.php/Users.getStandardInfo
		$user_details = self::getClient()->api_client->users_getInfo($this->ids, array('last_name', 'first_name', 'pic', 'pic_big', 'pic_small', 'pic_square'));
		foreach ( $user_details as $user ) {
			$fbuid = "fb" . $user['uid'];
			$users->$fbuid->name = $user['first_name'] . ' ' . $user['last_name'];
			// If the user's picture is hidden, use a default generic picture
			$users->$fbuid->pic = ($user['pic_square'] != "" ? $user['pic_square'] :
			                       'http://static.ak.connect.facebook.com/pics/q_silhouette.gif');
		}
		
		return $users;
	}
	
	/**
	 * Retrieves the proxied email address of a particular user.
	 * See: http://wiki.developers.facebook.com/index.php/Proxied_Email
	 * 
	 * Untested!
	 */
	public function getProxiedEmail( $ids ) {
		return self::getClient()->api_client->users_getInfo($ids, 'proxied_email');
	}
	
	public function user() {
		return $this->getClient()->get_loggedin_user();
	}
	
	/*
	 * Get the facebook client object for easy access.
	 */
	public function getClient() {
		global $fbApiKey, $fbApiSecret;
		
		static $facebook = null;
		static $is_config_setup = false;
		if (!$is_config_setup) {
			if (isset($fbApiKey) && $fbApiKey != 'YOUR_API_KEY' &&
			    isset($fbApiSecret) && $fbApiSecret != 'YOUR_API_SECRET' &&
			    $this->get_callback_url() != null) {
				$is_config_setup = true;
			} else {
				echo "Error: please update the api_key in your configuration.<br>\n";
				return null;
			}
		}
		if ($facebook === null) {
			$facebook = new Facebook($fbApiKey, $fbApiSecret, false, $this->get_base_fb_url());
			if (!$facebook) {
				error_log('Could not create facebook client.');
			}
			
			// Create a Facebook session
			/**
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
	
	// Make sure our environment variables were set corrently
	public function is_config_setup() {
		return $this->getClient() != null;
	}

	// Whether the site is "connected" or not
	public function is_enabled() {
		if (!$this->is_config_setup()) {
			return false;
		}
		// Change this if you want to turn off Facebook connect
		return true;
	}

	public function get_base_fb_url() {
		global $fbBaseURL;
		return $fbBaseURL;
	}

	public function get_static_root() {
		return 'http://static.ak.' . $this->get_base_fb_url();
	}

	public function get_feed_bundle_id() {
		global $fbFeedBundleId;
		return $fbFeedBundleId;
	}

	public function get_callback_url() {
		global $fbCallbackURL;
		return $fbCallbackURL;
	}


	### Facebook Client functions (two useful functions from The Run Around's lib/fbconnect.php)


	/*
	 * Fetch fields about a user from Facebook.
	 *
	 * If performance is an issue, then you may want to implement caching on top of this
	 * function. The cache would have to be cleared every 24 hours.
	 */
	public function get_fields($fb_uid, $fields) {
		try {
			$infos = $this->getClient()->api_client->users_getInfo($fb_uid, $fields);
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
	public function email_get_public_hash($email) {
		if ($email != null) {
			$email = trim(strtolower($email));
			return crc32($email) . '_' . md5($email);
		} else {
			return '';
		}
	}
}
