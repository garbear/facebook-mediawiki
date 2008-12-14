<?php

if( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

require_once("AuthPlugin.php");

class FBConnectAuthPlugin extends AuthPlugin {
	/**
	 * Returns whether $username is a valid username.
	 */
	public function userExists( $username ) {
		// Since the username will be passed from our external source, this will probably always be true. However, the
		// security paranoid says to check the data, e.g. in an LDAP plugin you could do an LDAP verify here, just to be safe
		return true; // or return false if the username is invalid
	}
	
	/**
	 * Whether the given username and password authenticate as a valid login.
	 */
	public function authenticate( $username, $password = '') {
		global $wgFBConnectOnly;
		if ($wgFBConnectOnly) {
			// Only let people login if they are first connected through Facebook Connect
			return $username == FBConnectClient::getClient()->get_loggedin_user();
		} else {
			return true;
		}
	}

	/**
	 * When a user logs in, attempt to fill in preferences and such. Here, we query
	 * for the user's real name.
	 */
	public function updateUser( &$user ) {
		$realName = FBConnectClient::get_fields($user->getName(), array('name'));
		$realName = $realName['name'];
		if ($realName != "" && $realName != $user->getRealName()) {
			$user->setRealName($realName);
			$user->saveSettings();
		}
		return true;
	}

	/**
	 * The authorization is external via Facebook Connect, so we would normally autocreate
	 * accounts as necessary. However, we set this to false because automatic account
	 * creation is handled internally by the FBConnect extension.
	 */
	public function autoCreate() {
		return false;
	}
	
	/**
	 * Users cannot change their passwords, because passwords are not used.
	 */
	public function allowPasswordChange() {
		return false;
	}

    /**
     * Check to see if external accounts can be created on Facebook. Returns false because they obviously can't be.
     */
    public function canCreateAccounts() {
        return false;
    }

	/**
	 * Do not look in the local database for user authentication because our
	 * authentication method is all that counts. Returns true to prevent logins
	 * that don't authenticate here from being checked against the local database's
	 * password fields.
	 */
	public function strict() {
		global $wgFBConnectOnly;
		//return $wgFBConnectOnly;
		return false;
	}

	/**
	 * This function gets called when the user is created. $user is an instance of
	 * the User class (see includes/User.php). Note the passed-by-reference.
	 */
	public function initUser(&$user, $autocreate=false) {
		if ($autocreate || $this->autoCreate()) {
			$user->mEmailAuthenticated = wfTimestampNow();
			// Turn on e-mail notifications by default
			$user->setOption('enotifwatchlistpages', 1);
			$user->setOption('enotifusertalkpages', 1);
			$user->setOption('enotifminoredits', 1);
			$user->setOption('enotifrevealaddr', 1);
			$this->updateUser( $user );
		}
		return true;
	}

	/**
	 * Modify options in the login template.
	 */
	public function modifyUITemplate(&$template) {
		// Disable the mail new password box
		$template->set("useemail", false);

		// Disable 'remember me' box
		$template->set("remember", false);

		$template->set("create", false);
		$template->set("domain", false);
		$template->set("usedomain", false);
	}
}
