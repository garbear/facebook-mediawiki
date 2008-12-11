<?php

if( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

require_once("AuthPlugin.php");

class FBConnectAuthPlugin extends AuthPlugin {

	// Return whether $username is a valid username
	function userExists( $username ) {
		// Since the username will be passed from our external source, this will probably always be true. However, the
		// security paranoid says to check the data, e.g. in an LDAP plugin you could do an LDAP verify here, just to be safe
		return true; // or return false if the username is invalid
	}
	// Whether the given username and password authenticate
	function authenticate( $username, $password ) {
		# return $username == FBConnectClient::getClient()->get_loggedin_user();
		return true;
	}

	// The authorization is external, so autocreate accounts as necessary
	function autoCreate() {
		return true;
	}

	// Do not look in the local database for user authentication because our authentication method is all that counts
	function strict() {
		return true;
	}

	function updateUser( &$user ) {
		$realName = FBConnectClient::get_fields($user->getName(), array('name'));
		if ($realName != "" && $realName != $user->getRealName()) {
			$user->setRealName($realName);
			$user->saveSettings();
		}
		return true;
	}

	// This function gets called when the user is created. $user is an instance of the User class (see includes/User.php)
	function initUser(&$user, $autocreate=false) {
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

	function modifyUITemplate(&$template) {
		// Disable the mail new password box
		$template->set("useemail", false);

		// Disable 'remember me' box
		$template->set("remember", false);

		$template->set("create", false);
		$template->set("domain", false);
		$template->set("usedomain", false);
	}
}
