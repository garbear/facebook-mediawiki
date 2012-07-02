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
 * Thrown when a FacebookUser encounters a problem.
 */
class FacebookUserException extends Exception
{
	protected $titleMsg;
	protected $textMsg;
	protected $msgParams;

	public function __construct($titleMsg, $textMsg, $msgParams = NULL) {
		$this->titleMsg  = $titleMsg;
		$this->textMsg   = $textMsg;
		$this->msgParams = $msgParams;

		// In general, $msg and $code are not meant to be used
		$msg = wfMsg( $this->titleMsg );
		$code = 0;

		parent::__construct($msg, $code);
	}

	public function getTitleMsg() {
		return $this->titleMsg;
	}

	public function getTextMsg() {
		return $this->textMsg;
	}

	public function getMsgParams() {
		return $this->msgParams;
	}

	public function getType() {
		return 'Exception';
	}

	public function __toString() {
		return wfMsg( $this->msg );
	}
}

/**
 * Class FacebookUser. Whereas User represents a MediaWiki user, this class
 * represents a Facebook user. FacebookUsers can only be constructed from
 * existing MediaWiki users. A FacebookUser is always associated with exactly
 * one MediaWiki user; calling login(), updateFromFacebook(), etc will affect
 * the associated MW user.
 */
class FacebookUser {
	
	static public $availableUserUpdateOptions = array('fullname', 'gender', 'nickname', 'email', 'language', 'timecorrection');
	
	static private $userNamePrefix;
	
	/**
	 * Constructor: Create this object from a Facebook ID. If the ID isn't
	 * specified or is 0, the currently logged-in user will be used.
	 */
	function __construct($facebookId = 0) {
		global $facebook;
		$this->id = !empty( $facebookId ) ? $facebookId : $facebook->getUser();
		$this->user = FacebookDB::getUser( $this->id ); // TODO: cache
		if (empty( $this->user )) {
			$this->user = new User();
		}
	}
	
	/**
	 * Get whether the user is logged in to Facebook.
	 * 
	 * If $ping is true, this function will ping Facebook's servers and verify
	 * that the oauth access_token is still valid.
	 * 
	 * According to <https://developers.facebook.com/blog/post/500/>, there are
	 * four ways the user could invalidate their access_token:
	 *   1. The token expires after expires time (2 hours is the default).
	 *   2. The user changes her password which invalidates the access token.
	 *   3. The user de-authorizes your app.
	 *   4. The user logs out of Facebook.
	 * 
	 * If problems are being reported about expiring access_tokens, try asking
	 * for the offline_access permission.
	 * 
	 * Also see http://stackoverflow.com/questions/6119300/facebook-auto-re-login-from-cookie-php
	 */
	function isLoggedIn($ping = false) {
		global $facebook;
		if ( $ping && $facebook->getUser() ) {
			try {
				$facebook->api('/me');
			} catch (FacebookApiException $e) {
				if ( $e->getType() == 'OAuthException' ) {
					$this->logout();
				}
			}
		}
		return $this->id != 0;
	}
	
	function getId() {
		return $this->id;
	}
	
	function getMWUser() {
		return $this->user;
	}
	
	function logout() {
		global $facebook;
		// Only destroy the session if it's for the current user
		if ( $this->id && $this->id == $facebook->getUser() ) {
			$facebook->destroySession();
		}
		$this->id = 0;
		$this->user = new User();
	}
	
	/**
	 * Requests information about the user from Facebook.
	 *
	 * Possible fields, depending on the app's permissions, are:
	 *     id, name, first_name, last_name, username, gender, locale, email
	 * 
	 * @return Array of info, or string if $field is one of the above
	 */
	public function getUserInfo($field = NULL) {
		global $facebook;
		
		if ( $this->id ) {
			// Cache information about users
			static $userinfo = array();
			if ( !isset( $userinfo[$this->id] ) ) {
				try {
					// Can't use /me here. If our token is acquired for a Facebook Application,
					// then "me" isn't you anymore - it's the app or maybe nothing.
					// http://stackoverflow.com/questions/2705756/facebook-access-token-invalid
					$userinfo[$this->id] = $facebook->api('/' . $this->id);
				} catch (FacebookApiException $e) {
					error_log( "Failure in the api when requesting /{$this->id}: " . $e->getMessage() );
					// If our access_token expires, invalidate the FacebookUser object
					if ( $e->getType() == 'OAuthException' ) {
						$this->logout();
					}
				}
			}
			if ( isset( $userinfo[$this->id] ) ) {
				return !empty( $field ) ? $userinfo[$this->id][$field] : $userinfo[$this->id];
			}
		}
		return NULL;
	}
	
	/**
	 * Logs in the user by their Facebook ID.
	 */
	function login() {
		global $wgUser;
		
		$this->updateFromFacebook();
		
		// Setup the session
		global $wgSessionStarted;
		if (!$wgSessionStarted) {
			wfSetupSession();
		}
		
		// Only log the user in if they aren't already logged in
		if ($this->user->getId() && $this->user->getId() != $wgUser->getId() ) {
			// TODO: calling setCookies() and load() might hit the database twice
		
			// Log the user in and store the new user as the global user object
			$this->user->setCookies();
			$wgUser = $this->user;
			
			// Similar to what's done in LoginForm::authenticateUserData().
			// Load $wgUser now. This is necessary because loading $wgUser (say
			// by calling getName()) calls the UserLoadFromSession hook, which
			// potentially creates the user in the local database.
			$sessionUser = User::newFromSession();
			$sessionUser->load();
			
			// Provide user interface in correct language immediately on this first page load
			global $wgLang;
			$wgLang = Language::factory( $wgUser->getOption( 'language' ) );
		}
	}
	
	/**
	 * Attaches the Facebook ID to the current user.
	 * 
	 * @throws FacebookUserException
	 */
	function attachCurrentUser($updatePrefs) {
		global $wgUser;
		
		if ( !$wgUser->isLoggedIn() ) {
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		$this->attachUserInternal($wgUser, $updatePrefs);
		$this->updateFromFacebook();
	}
	
	/**
	 * Attaches the Facebook ID to an existing wiki account. If the user does
	 * not exist, or the supplied password does not match, then an exception is
	 * thrown. Otherwise, the accounts are matched in the database and the new
	 * user object is logged in.
	 * 
	 * Post condition: user is logged in to MediaWiki
	 *
	 * @throws FacebookUserException
	 */
	function attachUser($name, $password, $updatePrefs) {
		global $wgUser;
		
		// Look up the user by their name
		$user = User::newFromName($name, 'creatable');
		
		// Check to see if the specified user actually exists
		if ( !( $user instanceof User && $user->getId() ) ) {
			throw new FacebookUserException('connectNewUserView', 'wrongpassword');
		}
		if ( !$user->checkPassword($password) ) {
			throw new FacebookUserException('connectNewUserView', 'wrongpassword');
		}
		
		// Username checks out. Do the attach
		$this->attachUserInternal($user, $updatePrefs);
		$this->login();
	}
	
	/**
	 * Do the attach.
	 * 
	 * @throws FacebookUserException
	 */
	private function attachUserInternal($user, $updatePrefs) {
		// The user must be logged into Facebook
		if ( !$this->id ) {
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		if ( $this->user->getId() ) {
			$this->sendError('facebook-error', 'facebook-errortext'); // TODO: new error msg
		}
		
		$fb_ids = FacebookDB::getFacebookIDs($user);
		if ( count( $fb_ids ) ) {
			$this->sendError('facebook-error', 'facebook-errortext'); // TODO: new error msg
		}
		
		// Attach the user to their Facebook account in the database
		FacebookDB::addFacebookID($user, $this->id);
		$this->user = $user;
		
		// Update the user with settings from Facebook
		if ( count( $updatePrefs ) ) {
			foreach ( $updatePrefs as $option ) {
				$this->user->setOption("facebookupdate-on-login-$option", '1');
			}
		}
		
		// User has been successfully attached
		#wfRunHooks( 'SpecialConnect::userAttached', array( &$this ) );
	}
	
	
	/**
	 * @throws FacebookUserException
	 */
	function createUser($username, $domain = '') {
		global $wgUser, $wgAuth;
		
		// Make sure we're not stealing an existing user account (it can't hurt to check twice)
		if ( empty( $username ) || !FacebookUser::userNameOK($username)) {
			wfDebug("Facebook: Name not OK: '$username'\n");
			// TODO: Provide an error message that explains that they need to pick a name or the name is taken.
			throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
		}
		
		/// START OF TYPICAL VALIDATIONS AND RESTRICTIONS ON ACCOUNT-CREATION. ///
		
		// Check the restrictions again to make sure that the user can create this account.
		if ( wfReadOnly() ) {
			// Indicate readOnlyPage error
			throw new FacebookUserException('readonlypage', null);
		}
		
		global $wgFbDisableLogin;
		if ( empty( $wgFbDisableLogin ) ) {
			// These two permissions don't apply in $wgFbDisableLogin mode because
			// then technically no users can create accounts
			if ( $wgUser->isBlockedFromCreateAccount() ) {
				wfDebug("Facebook: Blocked user was attempting to create account via Facebook Connect.\n");
				throw new FacebookUserException('facebook-error', 'facebook-errortext');
			} else {
				$titleObj = SpecialPage::getTitleFor( 'Connect' );
				$permErrors = $titleObj->getUserPermissionsErrors('createaccount', $wgUser, true);
				if (count($permErrors) > 0) {
					// Special case for permission errors
					throw new FacebookUserException($permErrors, 'createaccount');
				}
			}
		}
		
		// If we are not allowing users to login locally, we should be checking
		// to see if the user is actually able to authenticate to the authenti-
		// cation server before they create an account (otherwise, they can
		// create a local account and login as any domain user). We only need
		// to check this for domains that aren't local.
		if ($domain != '' && $domain != 'local' && !$wgAuth->canCreateAccounts() && !$wgAuth->userExists($username))
			throw new FacebookUserException('facebook-error', 'wrongpassword');
		
		// IP-blocking (and open proxy blocking) protection from SpecialUserLogin
		global $wgEnableSorbs, $wgProxyWhitelist;
		$ip = wfGetIP();
		if ($wgEnableSorbs && !in_array($ip, $wgProxyWhitelist) && $wgUser->inSorbsBlacklist($ip))
			throw new FacebookUserException('facebook-error', 'sorbs_create_account_reason');
		
		// Run a hook to let custom forms make sure that it is okay to proceed with
		// processing the form. This hook should only check preconditions and should
		// not store values.  Values should be stored using the hook at the bottom of
		// this function. Can use 'this' to call
		// sendPage('chooseNameFormView', 'SOME-ERROR-MSG-CODE-HERE') if some of the
		// preconditions are invalid.
		#if (!wfRunHooks( 'SpecialConnect::createUser::validateForm', array( &$this ) )) {
		#	return;
		#}
		
		$user = User::newFromName($username);
		if ( !$user ) {
			wfDebug("Facebook: Error creating new user.\n");
			throw new FacebookUserException('facebook-error', 'facebook-error-creating-user');
		}
		// TODO: Make user a Facebook user here: $fbUser = new FacebookUser($user);
		
		// Let extensions abort the account creation.
		// NOTE: Currently this is commented out because it seems that most wikis might have a
		// handful of restrictions that won't be needed on Facebook Connections. For instance,
		// requiring a CAPTCHA or age-verification, etc. Having a Facebook account as a pre-
		// requisite removes the need for that.
		/*
		$abortError = '';
		if( !wfRunHooks( 'AbortNewAccount', array( $user, &$abortError ) ) ) {
			// Hook point to add extra creation throttles and blocks
			wfDebug( "SpecialConnect::createUser: a hook blocked creation\n" );
			throw new FacebookUserException('facebook-error', 'facebook-error-user-creation-hook-aborted',
					array( $abortError ));
		}
		*/
		
		// Apply account-creation throttles
		global $wgAccountCreationThrottle, $wgMemc;
		if ( $wgAccountCreationThrottle && $wgUser->isPingLimitable() ) {
			$key = wfMemcKey( 'acctcreate', 'ip', $ip );
			$value = $wgMemc->get( $key );
			if ( !$value ) {
				$wgMemc->set( $key, 0, 86400 );
			}
			if ( $value >= $wgAccountCreationThrottle ) {
				// 'acct_creation_throttle_hit' should actually use 'parseinline' not 'parse' in $wgOut->showErrorPage()
				throw new FacebookUserException('facebook-error', 'acct_creation_throttle_hit',
						array($wgAccountCreationThrottle));
			}
			$wgMemc->incr( $key );
		}
		
		/// END OF TYPICAL VALIDATIONS AND RESTRICTIONS ON ACCOUNT-CREATION. ///
		
		// Fill in the info we know
		$userinfo = $this->getUserInfo();
		$email    = FacebookUser::getOptionFromInfo('email', $userinfo);
		$realName = FacebookUser::getOptionFromInfo('fullname', $userinfo);
		$pass     = '';
		// Create the account (locally on main cluster or via $wgAuth on other clusters)
		// $wgAuth essentially checks to see if these are valid parameters for new users
		if( !$wgAuth->addUser( $user, $pass, $email, $realName ) ) {
			wfDebug("Facebook: Error adding new user to database.\n");
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		// Add the user to the local database (regardless of whether $wgAuth was used)
		// This is a custom version of similar code in SpecialUserLogin's LoginForm
		// with differences due to the fact that this code doesn't require a password, etc.
		global $wgExternalAuthType;
		if ( $wgExternalAuthType ) {
			$user = ExternalUser::addUser( $user, $pass, $email, $realName );
			if ( is_object( $user ) ) {
				$extUser = ExternalUser::newFromName( $username );
				$extUser->linkToLocal( $user->getId() );
				$extEmail = $extUser->getPref( 'emailaddress' );
				if ( !empty( $extEmail ) && empty( $email ) ) {
					$user->setEmail( $extEmail );
				}
			}
		} else {
			$user->addToDatabase();
		}
		
		// Attach the user to their Facebook account in the database.
		// This must be done up here, because somewhere after this (I'm not too
		// sure where) the data must be in the database before copy-to-local is
		// done for shared setups.
		FacebookDB::addFacebookID($user, $this->id);
		$this->user = $user;
		
		$wgAuth->initUser( $this->user, true ); // $autocreate == true
		$wgAuth->updateUser( $this->user );
	
		// No passwords for Facebook accounts.
		/*
		if ( $wgAuth->allowPasswordChange() ) {
			$this->user->setPassword( $pass );
		}
		*/
		
		// Store which fields should be auto-updated from Facebook when the user logs in.
		global $wgRequest;
		$updateFormPrefix = 'wpUpdateUserInfo';
		foreach (self::$availableUserUpdateOptions as $option) {
			if ($wgRequest->getVal($updateFormPrefix . $option, '') != '') {
				$user->setOption("facebook-update-on-login-$option", 1);
			} else {
				$user->setOption("facebook-update-on-login-$option", 0);
			}
		}
		
		// Process the FacebookPushEvent preference checkboxes if Push Events are enabled
		global $wgFbEnablePushToFacebook;
		if( !empty( $wgFbEnablePushToFacebook ) ) {
			global $wgFbPushEventClasses;
			if ( !empty( $wgFbPushEventClasses ) ) {
				foreach( $wgFbPushEventClasses as $pushEventClassName ) {
					$pushObj = new $pushEventClassName;
					$className = get_class();
					$prefName = $pushObj->getUserPreferenceName();
					$this->user->setOption($prefName, ($wgRequest->getCheck($prefName) ? '1' : '0'));
				}
			}
	
			// Save the preference for letting user select to never send anything to their newsfeed
			$prefName = FacebookPushEvent::$PREF_TO_DISABLE_ALL;
			$this->user->setOption($prefName, $wgRequest->getCheck($prefName) ? '1' : '0');
		}
		
		// I think this should be done here
		$this->user->setToken();
		
		// This is done via login()
		#$this->user->saveSettings();
		
		// Log the user in
		$this->login();
		
		// Update user count
		$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
		$ssUpdate->doUpdate();
		
		wfRunHooks( 'AddNewAccount', array( $this->user ) );
		
		// Allow custom form processing to store values since this form submission was successful.
		// This hook should not fail on invalid input, instead check the input using the SpecialConnect::createUser::validateForm hook above.
		#wfRunHooks( 'SpecialConnect::createUser::postProcessForm', array( &$this ) );
		
		$wgUser->addNewUserLogEntryAutoCreate();
	}
	
	/**
	 * Disconnects the Facebook user from its MediaWiki account.
	 */
	function disconnect() {
		// Remove the user from the database
		$rows = FacebookDB::removeFacebookID($this->user);
		
		if ( $rows ) {
			// Successful deauthorization. Notify the user by email, and possibly
			// ask them to choose a new password
			
			// Remind password attempt
			$params = new FauxRequest( array (
					'wpName' => $this->user->getName()
			));
			$loginForm = new LoginForm($params);
			
			// TODO: Update the messages sent by email
			//$this->user->load() // called by getName()
			if ( $this->user->mPassword == '' ) {
				//$loginForm->mailPasswordInternal( $mwUser, true, 'facebook-passwordremindertitle', 'facebook-passwordremindertext' );
			} else {
				//$loginForm->mailPasswordInternal( $mwUser, true, 'facebook-passwordremindertitle-exist', 'facebook-passwordremindertext-exist' );
			}
		}
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
		$userinfo = $this->getUserInfo();
		// Update the following options if the user's settings allow it
		foreach (self::$availableUserUpdateOptions as $option) {
			// Translate Facebook parameters into MediaWiki parameters
			$value = self::getOptionFromInfo($option, $userinfo);
			if ($value && ($this->user->getOption("facebook-update-on-login-$option", '0') == '1')) {
				switch ($option) {
					case 'fullname':
						$this->user->setRealName($value);
						break;
					case 'email':
						if ( $value != $this->user->getEmail() ) {
							$this->user->setEmail($value);
							// Auto-authenticate email address if it was changed
							$this->user->mEmailAuthenticated = wfTimestampNow();
						}
						break;
					default:
						$this->user->setOption($option, $value);
				}
				$mod = true;
			}
		}
		// Only save the updated settings if something was changed
		if ( $mod && $saveSettings ) {
			$this->user->saveSettings();
		}
		
		wfProfileOut(__METHOD__);
	}
	
	/**
	 * Helper function for updateFromFacebook(). Takes an array of info from
	 * Facebook, and looks up the corresponding MediaWiki parameter.
	 */
	static function getOptionFromInfo($option, $userinfo) {
		// Lookup table for the names of the settings
		              /* MediaWiki */     /* Facebook */
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
				global $wgHiddenPrefs;
				if ( in_array( 'realname', $wgHiddenPrefs ) ) {
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
				break;
		}
		// If an appropriate value was found, return it
		return $value == '' ? null : $value;
	}
	
	/**
	 * Allows the prefix to be changed at runtime. This is useful, for example,
	 * to generate a username based off of a facebook name.
	 */
	static function setUserNamePrefix( $prefix ) {
		self::$userNamePrefix = $prefix;
	}
	
	/**
	 * Returns the prefix set by setUserNamePrefix().
	 */
	static function getUserNamePrefix() {
		static $default = NULL;
		if ( is_null( $default ) && empty( self::$userNamePrefix ) ) {
			//wfLoadExtensionMessages( 'Facebook' ); // Deprecated since 1.16
			$default = wfMsg('facebook-usernameprefix');
			self::setUserNamePrefix( $default );
		}
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
	
	/**
	 * Retrieves group membership data from Facebook.
	 */
	public function getGroupRights() {
		global $wgFbUserRightsFromGroup, $wgUser, $facebook;
		
		// Groupies can be members or admins (the latter infers the former)
		$rights = array(
			'member'  => false,
			'admin'   => false,
		);
		
		// If no group ID is specified, then there's no group to belong to
		// Also, restrict rights to users using the correct Facebook account
		if ( empty( $wgFbUserRightsFromGroup ) || !$this->id ) {
			return $rights;
		}
		
		// It's not documented, but $wgFbUserRightsFromGroup can actually be an array
		if ( is_array( $wgFbUserRightsFromGroup ) ) {
			$gids = $wgFbUserRightsFromGroup;
		} else {
			$gids = array( $wgFbUserRightsFromGroup );
		}
		
		// Cache the rights for an individual user to prevent hitting Facebook twice
		static $rights_cache = array();
		if ( array_key_exists( $this->id, $rights_cache ) ) {
			return $rights_cache[$this->id];
		}
		
		try {
			$groups = $facebook->api('/' . $this->id . '/groups?limit=5000&offset=0');
			
			if ( isset( $groups['data'] ) ) {
				foreach ( $groups['data'] as $group ) {
					if ( in_array( $group['id'], $gids ) ) {
						$rights['member'] = true;
						$rights['admin'] = !empty( $group['administrator'] );
						// Check to see if we should keep searching
						if ( count( $gids ) <= 1 || $rights['admin'] )
							break;
					}
				}
			}
		} catch (FacebookApiException $e) {
			error_log( "Failure in the api when requesting groups: " . $e->getMessage() );
			// If our access_token expires, invalidate the FacebookUser object
			if ( $e->getType() == 'OAuthException' ) {
				$this->logout();
			}
		}
		
		// Cache the rights
		$rights_cache[$this->id] = $rights;
		return $rights;
	}
}
