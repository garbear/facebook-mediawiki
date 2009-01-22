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
	function __construct() {
		parent::__construct( 'Connect' );
	}
	
	function getDescription() {
		return wfMsg( 'fbconnect-specialconnect' );
	}
	
	function execute( $par ) {
		global $wgOut, $wgUser, $wgAuth;
		
		wfLoadExtensionMessages( 'FBConnect' );
		$this->setHeaders();
		$wgOut->disallowUserJs();  # just in case...
		
		// Display heading
		$msg = $this->createHead();
		$wgOut->addHTML( $msg );
		
		if( FBConnect::$api->isConnected() ) {
			// Display "Logged in as {Facebook User}" & Facebook info
		} else {
			$template = $this->createLoginForm();
			// Give authentication and captcha plugins a chance to modify the form
			$wgAuth->modifyUITemplate( $template );
			wfRunHooks( 'UserLoginForm', array( &$template ) );
			$wgOut->addTemplate( $template );
		}
		
		// Dispay <fb:login-button>
	}
	
	function createHead() {
		$msg = '
<h2>Why Connect through Facebook?</h2>
<table><tr>
	<td style="vertical-align:top">
		<h4>Simplicity</h4>
		<ul>
			<li><strong>One-click login.</strong> Forget remembering usernames and passwords</li>
			<li><strong>Easily invite friends</strong> to view your linguistic work</li>
			<li><strong>Special rights</strong> based on Facebook\'s dynamic privacy</li>
		</ul>
	</td><td style="vertical-align:top">
		<h4>Features</h4>
		<ul>
			<li><strong>FBML:</strong> Utilize Facebook Markup Langauge</li>
			<li>Proper rendering of FBML that others have written</li>
		</ul>
	</td><td style="vertical-align:top">
		<h4>Communication</h4>
		<ul>
			<li><strong>Notifications.</strong> Recieve wiki notifications through facebook</li>
			<li><strong>News Feed.</strong> Optionally share this wiki with your friends</li>
			<li><strong>Privacy.</strong> No need to reveal your email adress, as emails are proxied through facebook</li>
			<li><strong>Friends.</strong> See which of your friends are also using this wiki</li>
		</ul>
	</td>
</tr></table><br/>';
		return $msg;
	}
	
	/**
	 * Creates the template object and propogate it with parameters.
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
}
