<?php
/**
 * Copyright © 2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Body class for the special page Special:Connect.
 */
class SpecialConnect extends SpecialPage {
	/**
	 * Constructor
	 */
	function __construct() {
		global $wgSpecialPageGroups;
		// Initiate SpecialPage's constructor
		parent::__construct( 'Connect' );
		// Add this special page to the "login" group of special pages
		$wgSpecialPageGroups['Connect'] = 'login';
	}
	
	/**
	 * Overrides getDescription() in SpecialPage. Looks in a different wiki message
	 * for this extension's description.
	 */
	function getDescription() {
		return wfMsg( 'fbconnect-title' );
	}
	
	/**
	 * Performs any necessary execution and outputs the resulting Special page.
	 */
	function execute( $par ) {
		global $wgUser, $wgRequest;
		
		// Check to see if the user is already logged in
		if ( $wgUser->getID() != 0 ) {
			$this->sendPage('alreadyLoggedIn');
			return;
		}
		
		$this->FacebookAPI = new FBConnectAPI();
		
		// Look at the subpage name to discover where we are in the login process
		switch ( $par ) {
		case 'Login': # Returning from a server
			$fb = new FBConnectAPI();
			$fb_user = $fb->user();
			
			// Code to see if fb_user exists
			$user = FBConnectDB::getUser( $fb_user );
			
			if ( $user instanceof User ) {
				// @TODO: Update user from facebook
				#$this->updateUser( $user, $sreg );
				$wgUser = $user;
				$this->setupSession();
				$wgUser->SetCookies();
				// @TODO (maybe): Set a cookie for later check-immediate use
				#$this->loginSetCookie( $fb_user );
				$this->sendPage('displaySuccessLogin');
				return;
			} else {
				$this->sendPage('chooseNameForm');
				return;
			}
			break;
		case 'ChooseName': # User logged into Facebook, but needs a wiki username
			$fb = new FBConnectAPI();
			$fb_user = $fb->user();
			
			if ( !$fb_user ) {
				wfDebug("FBConnect: aborting in ChooseName because no Facebook UID was reported.\n");
				$wgOut->showErrorPage( 'fbconnecterror', 'fbconnecterrortext' );
				return;
			}
			
			if ( $wgRequest->getCheck( 'wpCancel' ) ) {
				$wgOut->showErrorPage( 'fbconnectcancel', 'fbconnectcanceltext' );
				return;
			}
	
			$choice = $wgRequest->getText( 'wpNameChoice' );
			$nameValue = $wgRequest->getText( 'wpNameValue' );
			
			if ( $choice == 'existing' ) {
				$user = $this->attachUser($fb_user, $wgRequest->getText('wpExistingName'), $wgRequest->getText('wpExistingPassword'));
				if (!$user) {
					$this->sendPage('chooseNameForm', 'fbconnectwrongpassword');
					return;
				}
				// @TODO
				#$this->updateUser($user);
			} else {
				// @TODO: fixme
				#$name = $this->getUserName( $openid, $sreg, $choice, $nameValue );
				
				if (!$name || !$this->userNameOK($name)) {
					wfDebug("FBConnect: Name not OK: '$name'\n");
					$this->sendPage('chooseNameForm');
					return;
				}
				
				// @TODO: fixme
				#$user = $this->createUser($openid, $sreg, $name);
			}
			
			if (is_null($user)) {
				wfDebug("FBConnect: aborting in ChooseName because we could not create user object\n");
				$wgOut->showErrorPage('fbconnecterror', 'fbconnecterrortext');
				return;
			}
			// Store the new user as the global user object
			$wgUser = $user;
			$this->sendPage('displaySuccessLogin');
			break;
		default: # Main entry point
			if ( $wgRequest->getText( 'returnto' ) ) {
				$this->setReturnTo( $wgRequest->getText( 'returnto' ), $wgRequest->getVal( 'returntoquery' ) );
			}
			
			$fb = new FBConnectAPI();
			$fb_user = $fb->user();
			
			if ($fb_user) {
				$this->login($fb_user, $this->getTitle('Login'));
			} else {
				$this->sendPage('connectForm');
			}
		}
	}
	
	/*
	 * Sets up the session, if it hasn't already been started.
	 */
	protected function setupSession() {
		global $wgSessionStarted;
		if (!$wgSessionStarted)
			wfSetupSession();
	}
	
	protected function createUser($fb_id, $name) {
		global $wgAuth;
		$user = User::newFromName($name);
		if (!$user) {
			wfDebug("FBConnecr: Error adding new user.\n");
			return null;
		}
		$user->addToDatabase();
		$user->addNewUserLogEntry();

		if (!$user->getId()) {
			wfDebug( "FBConnect: Error adding new user.\n" );
		} else {
			// Give $wgAuth a chance to deal with the user object
			$wgAuth->initUser($user);
			$wgAuth->updateUser($user);
			// Update site statistics
			$ssUpdate = new SiteStatsUpdate(0, 0, 0, 0, 1);
			$ssUpdate->doUpdate();
			// 
			self::addFacebookID($user, $fb_id);
			// @TODO: fixme
			#$this->updateUser( $user, $sreg, $ax, true );
			$user->saveSettings();
			return $user;
		}
	}
	
	protected function attachUser($fb_user, $name, $password) {
		$user = User::newFromName($name);
		if (!$user) {
			return null;
		}
		if (!$user->checkPassword($password)) {
			return null;
		}
		FBConnectDB::addFacebookID($user, $fb_user);
		return $user;
	}
	
	/**
	 * Update some user's settings with value get from OpenID
	 *
	 * @param $user User object
	 * @param $sreg Array of options get from OpenID
	 * @param $force forces update regardless of user preferences
	 */
	function updateUser( $user, $sreg, $ax, $force = false ) {
		global $wgAllowRealName;
		
		// Nick name
		if ( $this->updateOption( 'nickname', $user, $force ) ) {
			if ( array_key_exists( 'nickname', $sreg ) && $sreg['nickname'] != $user->getOption( 'nickname' ) ) {
				$user->setOption( 'nickname', $sreg['nickname'] );
			}
		}
		
		// Full name
		if ( $wgAllowRealName && ( $this->updateOption( 'fullname', $user, $force ) ) ) {
			if ( array_key_exists( 'fullname', $sreg ) ) {
				$user->setRealName( $sreg['fullname'] );
			}
			
			if ( array_key_exists( 'http://axschema.org/namePerson/first', $ax ) || array_key_exists( 'http://axschema.org/namePerson/last', $ax ) ) {
				$user->setRealName($ax['http://axschema.org/namePerson/first'][0] . " " . $ax['http://axschema.org/namePerson/last'][0]);
			}
		}
		
		// Language
		if ( $this->updateOption( 'language', $user, $force ) ) {
			if ( array_key_exists( 'language', $sreg ) ) {
				# FIXME: check and make sure the language exists
				$user->setOption( 'language', $sreg['language'] );
			}
		}
		
		if ( $this->updateOption( 'timezone', $user, $force ) ) {
			if ( array_key_exists( 'timezone', $sreg ) ) {
				# FIXME: do something with it.
				# $offset = OpenIDTimezoneToTzoffset($sreg['timezone']);
				# $user->setOption('timecorrection', $offset);
			}
		}
		
		$user->saveSettings();
	}
	
	/**
	 * Tests whether the name is OK to use as a user name.
	 */
	function userNameOK ($name) {
		global $wgReservedUsernames;
		return (0 == User::idFromName($name) && !in_array($name, $wgReservedUsernames));
	}
	
	private function sendPage($function, $arg = NULL) {
		global $wgOut;
		// Setup the page for rendering
		wfLoadExtensionMessages( 'FBConnect' );
		$this->setHeaders();
		$wgOut->disallowUserJs();  # just in case...
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		// Call the specified function to continue generating the page
		if (is_null($arg)) {
			$this->$function();
		} else {
			$this->$function($arg);
		}
	}
	
	private function alreadyLoggedIn() {
		global $wgOut;
		// @TODO: Repalce these with i18n messages
		#$wgOut->setPageTitle( wfMsg( 'fbconnecterror' ) );
		$wgOut->setPageTitle( 'Verification error' );
		#$wgOut->addWikiMsg( 'fbconnectalreadyloggedin', $wgUser->getName() );
		$wgOut->addHtml('<p><b>You are already logged in, ' . $wgUser->getName() . '</b></p>\n' .
		                '<p>If you want to use OpenID to log in in the future, you can ' .
		                '[[Special:Connect/Convert|convert your account to use OpenID]].</p>');
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $wgRequest->getText('returnto'), $wgRequest->getVal('returntoquery'));
	}
	
	private function displaySuccessLogin() {
		global $wgOut;
		// @TODO: Repalce these with i18n messages
		#$wgOut->setPageTitle( wfMsg( 'fbconnectsuccess' ) );
		$wgOut->setPageTitle( 'Verification succeeded' );
		#$wgOut->addWikiMsg( 'openidsuccess', $wgUser->getName(), $openid );
		$wgOut->addHtml( '<p>Verification succeeded</p>' );
		
		// Run any hooks for UserLoginComplete
		$inject_html = '';
		wfRunHooks( 'UserLoginComplete', array( &$wgUser, &$inject_html ) );
		$wgOut->addHtml( $inject_html );
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $wgRequest->getText('returnto'), $wgRequest->getVal('returntoquery'));
	}
	
	private function chooseNameForm($messagekey = NULL) {
		global $wgOut, $wgOpenIDOnly, $wgAllowRealName;
		// Outputs the canonical name of the special page at the top of the page
		$this->outputHeader();
		
		#$fb = new FBConnectAPI();
		#$fb_user = $fb->user();
		
		if ( $messagekey ) {
			$wgOut->addWikiMsg( $messagekey );
		}
		
		#$wgOut->addWikiMsg( 'fbconnectchooseinstructions' );
		$wgOut->addHtml('<p>All users need a nickname; you can choose one from the options below.</p>');
		
		/* @TODO: put in options:
		
		 * An existing account on this wiki
		   Username: ______ Password: ______
		 * The username picked from your Facebook URL (gbruin)
		 * Your first name (Garrett)
		 * Your full name (Garrett_Brown)
		 * An auto-generated name (FacebookUser2)
		 * A name of your choice: _______
		
		   [Login]  [Cancel]
		
		 */
	}
	
	/**
	 * Displays the main connect form.
	 */
	private function connectForm() {
		global $wgOut, $wgUser;
		// Outputs the canonical name of the special page at the top of the page
		$this->outputHeader();
		
		//@TODO: use wfMsgWikiHtml and then add html
		$heading = '
			<div id="specialconnect_info">
				' . wfMsg( 'fbconnect-intro' ) . '
				<table>
					<tr>
						<th>' . wfMsg( 'fbconnect-conv' ) . '</th>
						<th>' . wfMsg( 'fbconnect-fbml' ) . '</th>
						<th>' . wfMsg( 'fbconnect-comm' ) . '</th>
					</tr>
					<tr>
						<td>' . wfMsg( 'fbconnect-convdesc' ) . '</td>
						<td>' . wfMsg( 'fbconnect-fbmldesc' ) . '</td>
						<td>' . wfMsg( 'fbconnect-commdesc' ) . '</td>
					</tr>
				</table>
			</div>';
		$wgOut->addWikiText( $heading );
		/**
		 * Renders two side-by-side boxes that differ based on who is logged in.
		 * 
		 * Anonymous user:     Draws the Special:UserLogin box and a Connect button.
		 * Non-connected user: Draws the Special:UserLogin and a Merge box.
		 * Connected user:     Draws some info about the user and a Logout box.
		 */
		$wgOut->addHTML('
			<table id="specialconnect_boxarea">
				<tr>
					<td class="box_left">');
						if( FBConnect::$api->isConnected() ) {
							// If the user is Connected, display info about them instead of a login form
							$content = '<b><fb:name uid="loggedinuser" useyou="false" linked="false"></fb:name></b> ' .
							           // @TODO: (UCLA) should be replaced by the user's primary network
							           '(UCLA)<br/><a href="/wiki/User:#">my user page</a> | <a href="#" ' .
							           'onclick="return popupFacebookInvite();">invite friends</a>';
							$this->drawBox( 'fbconnect-welcome', '', $content );
						} else {
							$wgOut->addTemplate( $this->createLoginForm() );
						}
						$wgOut->addHTML('
					</td>
					<td class="box_right">');
						if( !$wgUser->isLoggedIn() ) {
							$this->drawBox( 'fbconnect', 'fbconnect-loginbox' );
						} else if( !FBConnect::$api->isConnected() ) {
							$this->drawBox( 'fbconnect-merge', 'fbconnect-mergebox' );
						} else {
							$this->drawBox( 'fbconnect-logout', 'fbconnect-logoutbox' );
						}
						$wgOut->addHTML('
					</td>
				</tr>
			</table>'
		);
	}

	/**
	 * Draws a Facebook-style info box.
	 *
	 * @param string $h1   The name of the message for the title of the box
	 * @param string $msg  The name of the message for the content, or blank to use $html
	 * @param string $html The HTML to use if $msg is blank, or the $1 argument of the given message
	 */
	private function drawBox( $h1, $msg, $html = '' ) {
		global $wgOut;
		$wgOut->addHTML('
			<div id="specialconnect_box">
				<div>');
					if( $html !== '' ) {
						$wgOut->addHTML( '<fb:profile-pic uid="loggedinuser" size="small" ' .
						                 'facebook-logo="true"></fb:profile-pic>' );
					}
					$wgOut->addHTML('
				</div>
				<h1>');
					$wgOut->addWikiText( wfMsg( $h1 ));
					$wgOut->addHTML('
				</h1>
				<div class="box_content">');
					$button = '<fb:login-button size="large" background="white" length="long" ' .
					          'autologoutlink="true" onlogin="window.location = \'' .
					          $this->scriptUrl( $this->getTitle( 'Login' )) . '\';">';
					if( $msg !== '' ) {
						$wgOut->addWikiText( wfMsg( $msg, $button ));
					} else {
						if( $html == '' )
							$html = $button;
						$wgOut->addHTML( '<p>' . $html . '</p>' );
					}
					$wgOut->addHTML('
				</div>
			</div>');
	}
	
	/**
	 * Creates a Login Form template object and propogates it with parameters.
	 */
	private function createLoginForm() {
		global $wgUser, $wgEnableEmail, $wgEmailConfirmToEdit,
		       $wgCookiePrefix, $wgCookieExpiration, $wgAuth;
		
		$template = new UserloginTemplate();
		
		// Pull the name from $wgUser or cookies
		if( $wgUser->isLoggedIn() )
			$name =  $wgUser->getName();
		else if( isset( $_COOKIE[$wgCookiePrefix . 'UserName'] ))
			$name =  $_COOKIE[$wgCookiePrefix . 'UserName'];
		else
			$name = null;
		// Alias some common URLs for $action and $link
		$loginTitle = self::getTitleFor( 'Userlogin' );
		$this_href = wfUrlencode( $this->getTitle() );
		// Action URL that gets posted to
		$action = $loginTitle->getLocalUrl( 'action=submitlogin&type=login&returnto=' . $this_href );
		// Don't show a "create account" link if the user is not allowed to create an account
		if ($wgUser->isAllowed( 'createaccount' )) {
			$link_href = htmlspecialchars( $loginTitle->getLocalUrl( 'type=signup&returnto=' . $this_href ));
			$link_text = wfMsgHtml( 'nologinlink' );
			$link = wfMsgWikiHtml( 'nologin', "<a href=\"$link_href\">$link_text</a>" );
		} else
			$link = '';
		
		// Set the appropriate options for this template
		$template->set( 'header', '' );
		$template->set( 'name', $name );
		$template->set( 'action', $action );
		$template->set( 'link', $link );
		$template->set( 'message', '' );
		$template->set( 'messagetype', 'none' );
		$template->set( 'useemail', $wgEnableEmail );
		$template->set( 'emailrequired', $wgEmailConfirmToEdit );
		$template->set( 'canreset', $wgAuth->allowPasswordChange() );
		$template->set( 'canremember', ( $wgCookieExpiration > 0 ) );
		$template->set( 'remember', $wgUser->getOption( 'rememberpassword' ) );
		// Look this setting up in SpecialUserLogin.php
		$template->set( 'usedomain', false );
		// Give authentication and captcha plugins a chance to modify the form
		$wgAuth->modifyUITemplate( $template, 'login' );
		wfRunHooks( 'UserLoginForm', array( &$template ));
		// Spit out the form we just made
		return $template;
	}
	
	private function anything() {
		global $wgOut;
		// Outputs the canonical name of the special page at the top of the page
		$this->outputHeader();
	}
	
}
