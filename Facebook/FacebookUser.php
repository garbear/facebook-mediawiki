<?php
/*
 * Copyright © 2010-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Class FacebookUser
 * 
 * Extends the User class.
 */
class FacebookUser extends User {
	
	static public $availableUserUpdateOptions = array('fullname', 'gender', 'nickname', 'email', 'language', 'timecorrection');
	
	static private $userNamePrefix;
	
	/**
	 * Constructor: Create this object from an existing User. Bonus points if
	 * the existing User was created form an ID and has not yet been loaded! 
	 */
	function __construct($user) {
		$this->mId = $user->getId();
		$this->mFrom = 'id';
	}
	
	/**
	 * Update a user's settings with the values retrieved from the current
	 * logged-in Facebook user. Settings are only updated if a different value
	 * is returned from Facebook and the user's settings allow an update on
	 * login.
	 */
	function updateFromFacebook($saveSettings = true) {
		wfProfileIn(__METHOD__);
		global $facebook;
		
		// Keep track of whether any settings were modified
		$mod = false;
		
		// Connect to the Facebook API and retrieve the user's info 
		$userinfo = $facebook->getUserInfo();
		// Update the following options if the user's settings allow it
		foreach (self::$availableUserUpdateOptions as $option) {
			// Translate Facebook parameters into MediaWiki parameters
			$value = self::getOptionFromInfo($option, $userinfo);
			if ($value && ($this->getOption("facebook-update-on-login-$option", '0') == '1')) {
				switch ($option) {
					case 'fullname':
						$this->setRealName($value);
						break;
					case 'email':
						if (is_null($this->mEmailAuthenticated) || $value != $this->getEmail()) {
							$this->setEmail($value);
							// Auto-authenticate email address if it was changed
							$this->mEmailAuthenticated = wfTimestampNow();
						}
						break;
					default:
						$this->setOption($option, $value);
				}
				$mod = true;
			}
		}
		// Only save the updated settings if something was changed
		if ( $mod && $saveSettings ) {
			$this->saveSettings();
		}
		
		wfProfileOut(__METHOD__);
	}
	
	/**
	 * Helper function for updateFromFacebook(). Takes an array of info from
	 * Facebook, and looks up the corresponding MediaWiki parameter.
	 */
	static function getOptionFromInfo($option, $userinfo) {
		// Lookup table for the names of the settings
		$params = array('nickname'       => 'username',
		                'fullname'       => 'name',
		                'firstname'      => 'first_name',
		                'gender'         => 'gender',
		                'language'       => 'locale',
		                'timecorrection' => 'timezone',
		                'email'          => 'email');
		if (empty($userinfo)) {
			return null;
		}
		$value = array_key_exists($params[$option], $userinfo) ? $userinfo[$params[$option]] : '';
		// Special handling of several settings
		switch ($option) {
			case 'fullname':
			case 'firstname':
				// If real names aren't allowed, then simply ignore the parameter from Facebook
				global $wgAllowRealName;
				if ( empty($wgAllowRealName) ) {
					$value = '';
				}
				break;
			case 'gender':
				// Unfortunately, Facebook genders are localized (but this might change)
				if ($value != 'male' && $value != 'female') {
					$value = '';
				}
				break;
			case 'language':
				/**
				 * Convert Facebook's locale into a MediaWiki language code.
				 * For an up-to-date list of Facebook locales, see
				 * <http://www.facebook.com/translations/FacebookLocales.xml>.
				 * For an up-to-date list of MediaWiki languages, see:
				 * <http://svn.wikimedia.org/svnroot/mediawiki/trunk/phase3/languages/Names.php>.
				 */
				if ($value == '') {
					break;
				}
				// These regional languages get special treatment
				$locales = array('en_PI' => 'en', # Pirate English
				                 'en_GB' => 'en-gb', # British English
				                 'en_UD' => 'en', # Upside Down English
				                 'fr_CA' => 'fr', # Canadian French
				                 'fb_LT' => 'en', # Leet Speak
				                 'pt_BR' => 'pt-br', # Brazilian Portuguese
				                 'zh_CN' => 'zh-cn', # Simplified Chinese
				                 'es_ES' => 'es', # European Spanish
				                 'zh_HK' => 'zh-hk', # Traditional Chinese (Hong Kong)
				                 'zh_TW' => 'zh-tw'); # Traditional Chinese (Taiwan)
				if (array_key_exists($value, $locales)) {
					$value = $locales[$value];
				} else {
					// No special regional treatment exists in MW; chop it off
					$value = substr($value, 0, 2);
				}
				break;
			case 'timecorrection':
				// Convert the timezone into a local timezone correction
				// TODO: $value = TimezoneToOffset($value);
				$value = '';
				break;
			case 'email':
				// For information on emails, see <https://www.facebook.com/help/?page=216574428360510>
				// TODO: if the user's email is updated from Facebook, then
				// automatically authenticate the email address
				#$user->mEmailAuthenticated = wfTimestampNow();
		}
		// If an appropriate value was found, return it
		return $value == '' ? null : $value;
	}
	
	/**
	 * Allows the prefix to be changed at runtime.  This is useful, for example,
	 * to generate a username based off of a facebook name. Make sure to call
	 * this function before getUserNamePrefix() is called.
	 * 
	 * TODO: Add these two lines to this class's constructor/static initializer
	 * wfLoadExtensionMessages( 'Facebook' );
	 * FacebookUser::setUserNamePrefix( wfMsg('facebook-usernameprefix') );
	 */
	static function setUserNamePrefix( $prefix ) {
		self::$userNamePrefix = $prefix;
	}
	
	/**
	 * Returns the prefix set by.  This is useful, for example, to generate a
	 * username based off of a facebook name.
	 */
	static function getUserNamePrefix() {
		return self::$userNamePrefix;
	}
	
	/**
	 * Generates a unique username for a wiki account based on the prefix specified
	 * in the message 'facebook-usernameprefix'. The number appended is equal to
	 * the number of Facebook Connect to user ID associations in the user_fbconnect
	 * table, so quite a few numbers will be skipped. However, this approach is
	 * more scalable. For smaller wiki installations, uncomment the line $i = 1 to
	 * have consecutive usernames starting at 1.
	 */
	static function generateUserName() {
		// Because $i is incremented the first time through the while loop
		$i = FacebookDB::countUsers(); // rough estimate
		$max = $i + 100;
		while ($i < PHP_INT_MAX && $i < $max) {
			$name = self::getUserNamePrefix() . $i;
			if (FacebookUser::userNameOK($name)) {
				return $name;
			}
			++$i;
		}
		return $prefix;
	}
	
	/**
	 * Tests whether the name is OK to use as a user name.
	 */
	static function userNameOK($name) {
		global $wgReservedUsernames;
		
		$name = trim( $name );
		
		if ( empty( $name ) ) {
			return false;
		}
		
		$u = User::newFromName( $name, 'creatable' );
		if ( !is_object( $u ) ) {
			return false;
		}
		
		if ( !empty($wgReservedUsernames) && in_array($name, $wgReservedUsernames) ) {
			return false;
		}
		
		$mExtUser = ExternalUser::newFromName( $name );
		if ( is_object( $mExtUser ) && ( 0 != $mExtUser->getId() ) ) {
			return false;
		} elseif ( 0 != $u->idForName( true ) ) {
			return false;
		}
		return true;
	}
}
