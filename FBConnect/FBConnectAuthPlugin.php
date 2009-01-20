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
 * Class AuthPlugin must be defined before we can extend it!
 */
if (!isset($IP))
	$IP = defined('MW_INSTALL_PATH') ? MW_INSTALL_PATH : dirname( __FILE__ ) . "/../..";
require_once("$IP/includes/AuthPlugin.php");


/**
 * Class FBConnectAuthPlugin extends AuthPlugin
 * 
 * Custom implementation of user authentification as it pertains to
 * Facebook Connect. This plugin bypasses the typical username-password
 * system currently in place in MediaWiki. Authentification takes place
 * against the cookies set by Facebook; if the user is signed into
 * Facebook Connect and has authorized the application, then they will
 * by logged into MediaWiki by their Facebook ID.
 */
class FBConnectAuthPlugin extends AuthPlugin {
	/**
	 * Returns whether $username is a valid username.
	 * 
	 * @return false if the username is invalid
	 */
	public function userExists( $username ) {
		global $fbAllowOldAccounts;
		if (!$fbAllowOldAccounts) {
			return true;
		}
		return FBConnect::$api->isIdValid( $username );
	}
	
	/**
	 * Whether the given username and password authenticate as a valid login.
	 */
	public function authenticate( $username, $password = '' ) {
		// Only let people login if they are first connected through Facebook Connect
		return $username == FBConnect::$api->user();
	}

	/**
	 * When a user logs in, attempt to fill in preferences and such. Here, we query
	 * for the user's real name.
	 *
	public function updateUser( &$user ) {
		$realName = FBConnect::$api->get_fields($user->getName(), array('name'));
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
     * Check to see if external accounts can be created on Facebook. Returns false
     * because they obviously can't be.
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
		global $fbAllowOldAccounts;
		return !$fbAllowOldAccounts;
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
