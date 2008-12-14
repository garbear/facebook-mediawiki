<?php

// If this is set on by your webserver, then client access doesn't work
ini_set('zend.ze1_compatibility_mode', 0);

$dir = dirname(__FILE__) . '/';
if (file_exists($dir . 'config.php')) {
	include_once		$dir . 'facebook-client/facebook.php';
} else {
	include_once		$dir . 'facebook-client/facebook.php';
}

class FBConnectClient {
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
