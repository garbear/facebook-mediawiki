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
 *  Body class for the special page Special:Connect.
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
		global $wgOut;
		// Let the extension (specifically, FBConnectHooks::BeforePageDisplay()) know this page is being rendered
		FBConnect::$special_connect = true;
		
		// Setup the page for rendering
		wfLoadExtensionMessages( 'FBConnect' );
		$this->setHeaders();
		$wgOut->disallowUserJs();  # just in case...
		
		// Render the heading (general info and propoganda about Facebook Connect)
		$this->drawHeading();
		
		// Render the Login and Connect/Merge forms
		$this->drawForms();
	}
	
	/**
	 * Creates a header outlining the benefits of using Facebook Connect.
	 * 
	 * @TODO: Move styles to a stylesheet.
	 */
	function drawHeading() {
		global $wgOut;
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
	}
	
	function drawForms() {
		global $wgOut, $wgAuth, $wgUser;
		$wgOut->addHTML('
			<table id="specialconnect_boxarea">
				<tr>
					<td class="box_left">');
						if( FBConnect::$api->isConnected() ) {
							// If the user is Connected, display info about them instead of a login form
							$content = '<b><fb:name uid="loggedinuser" useyou="false" linked="false"></fb:name></b> ' .
							           '(UCLA)<br/><a href="/wiki/User:2539590">my user page</a> | <a href="#" ' .
							           'onclick="return popupFacebookInvite();">invite friends</a>';
							$this->drawBox( 'fbconnect-welcome', '', $content );
							$this->drawInfoForm();
						} else {
							$template = $this->createLoginForm();
							// Give authentication and captcha plugins a chance to modify the form
							$wgAuth->modifyUITemplate( $template );
							wfRunHooks( 'UserLoginForm', array( &$template ));
							$wgOut->addTemplate( $template );
						}
						$wgOut->addHTML('
					</td>
					<td class="box_right">');
						// Display login form and Facebook Connect form
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
			</table>');
	}
	
	/**
	 * If the user is already connected, then show some basic info about their Facebook
	 * account (real name, profile picture, etc).
	 */
	function drawInfoForm() {
	}
	
	function drawBox( $h1, $msg, $html = '' ) {
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
					$button = '<fb:login-button size="large" background="white" length="long" autologoutlink="true">';
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
	function createLoginForm() {
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
			$link = wfMsgHtml( 'nologin', "<a href=\"$link_href\">$link_text</a>" );
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
		
		// Spit out the form we just made
		return $template;
	}
	
	/**
	 * Creates a button that allows users to merge their account with Facebook Connect.
	 */
	function drawConnectForm() {
	}		
}
