<?php
/**
 *  Body class of the Special Page "Special:Connect". This entire page needs to be deprecated,
 *  as Facebook Connect is an auto-signon implementation.
 */

class SpecialConnect extends SpecialPage {
	function __construct() {
		parent::__construct( 'SpecialConnect' );
		wfLoadExtensionMessages('FBConnect');
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut;
 
		$this->setHeaders();
 
		# Get request data from, e.g.
		$param = $wgRequest->getText('param');
 
		# Output
		$wgOut->addWikiText( '== ' . wfMsg('fbconnectwelcome') . ' ==' );
		$wgOut->addWikiText( wfMsg('fbconnectintro') );
		$wgOut->addHTML('<br><table><tr><td width="50%"><div width="100%" height="100%" style="border-right:solid 1px #839553;">');
		$wgOut->addWikiText("'''[[Special:UserLogin|Log in]]''' with Wiki username.");
		$wgOut->addHTML('<br><br><br><br><br></div></td><td valign="top">');
		$wgOut->addWikiText("Or '''login''' with Facebook:");
		#$wgOut->addHTML('<a href="#" onclick="FB.Connect.requireSession(); return false;" >'.
		#  '<img id="fb_login_image" src="http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" alt="Connect"/></a>'.
		$wgOut->addHTML('<fb:login-button></fb:login-button>');
		$wgOut->addHTML('</td></tr></table><br><br><br><br><br><br><br><br><br><br><br><br>');
	}
}
