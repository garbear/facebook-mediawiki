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
if ( !defined( 'MEDIAWIKI' )) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * Class AuthPlugin must be defined before we can extend it!
 */
if ( !isset( $IP ))
	$IP = defined( 'MW_INSTALL_PATH' ) ? MW_INSTALL_PATH : dirname( __FILE__ ) . '/../..';
require_once( "$IP/includes/AuthPlugin.php" );


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
	 */
	public function userExists( $username ) {
		return FBConnect::$api->isIdValid( $username );
	}
	
	/**
	 * Whether the given username and password authenticate as a valid login. We should only
	 * let people login if they are first connected through Facebook Connect.
	 */
	public function authenticate( $username, $password = '' ) {
		return $username == FBConnect::$api->user();
	}
	
	/**
	 * When a user logs in, attempt to fill in preferences and such. Here, we query
	 * for the user's real name.
	 */
	public function updateUser( &$user ) {
		if ( FBConnect::$api->isIdValid( $user->getName() )) {
			/**/
			#$d = $user->getGroups();
			#var_dump($d);
			// Temporary fix for my personal wiki
			if ( !in_array( 'fb-user', $user->getGroups() )) {
				$user->addGroup( 'fb-user' );
			}
			/**/
			$name = FBConnect::$api->getRealName( $user );
			if ( $name != '' && $name != $user->getRealName() ) {
				$user->setRealName( $name );
				$user->saveSettings();
			}
		}
		return true;
	}
	
	/**
	 * The authorization is external via Facebook Connect, so we would normally autocreate
	 * accounts as necessary. However, we set this to false because automatic account
	 * creation is handled internally by the FBConnect extension.
	 */
	public function autoCreate() {
		return true;
	}
	
	/**
	 * Only nonconnected users can change their passwords.
	 */
	public function allowPasswordChange() {
		return !$this->strict() && !FBConnect::$api->user();
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
	 * the User class (see includes/User.php). Note the passed-by-reference nature.
	 */
	public function initUser( &$user, $autocreate = false ) {
		if ( $autocreate && FBConnect::$api->isIdValid( $user->getName() )) {
			$user->mEmailAuthenticated = wfTimestampNow();
			// Turn on e-mail notifications by default
			$user->setOption( 'enotifwatchlistpages', 1 );
			$user->setOption( 'enotifusertalkpages', 1 );
			$user->setOption( 'enotifminoredits', 1 );
			$user->setOption( 'enotifrevealaddr', 1 );
			// No password to remember
			$user->setOption( 'rememberpassword', 0 );
			// Mark the user as a Facebook Connect user
			$user->addGroup( 'fb-user' );
		}
		$this->updateUser( $user );
		return true;
	}
	
	/**
	 * Modify options in the login template.
	 */
	public function modifyUITemplate( &$template ) {
		if( FBConnect::$api->user() ) {
			// Disable the mail new password box
			$template->set( "useemail", false );
			// Disable 'remember me' box
			$template->set( "remember", false );
			// What happens if a Connected user creates an account while logged in?
			$template->set( "create", false );
		}
	}
}
