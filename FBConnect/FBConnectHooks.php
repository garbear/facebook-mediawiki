<?php
/*
 * Copyright © 2008-2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Class FBConnectHooks
 * 
 * This class contains all the hooks used in this extension. HOOKS DO NOT NEED
 * TO BE EXPLICITLY ADDED TO $wgHooks. Simply write a static function with the
 * same name as the hook that provokes it, place it inside this class and let
 * FBConnect::init() do its magic. Helper functions should be private, because
 * only public static methods are added as hooks.
 */
class FBConnectHooks {
	/**
	 * Hook is called whenever an article is being viewed... Currently, figures
	 * out the Facebook ID of the user that the userpage belongs to.
	 */
	public static function ArticleViewHeader( &$article, &$outputDone, &$pcache ) {
		global $wgOut;
		// Get the article title
		$nt = $article->getTitle();
		// If the page being viewed is a user page
		if ($nt && $nt->getNamespace() == NS_USER && strpos($nt->getText(), '/') === false) {
			$user = User::newFromName($nt->getText());
			if (!$user || $user->getID() == 0) {
				return true;
			}
			$fb_id = FBConnectDB::getFacebookIDs($user->getId());
			if (!count($fb_id) || !($fb_id = $fb_id[0])) {
				return true;
			}
			// TODO: Something with the Facebook ID stored in $fb_id
			return true;
		}
		return true;
	}
	
	/**
	 * Checks the autopromote condition for a user.
	 */
	static function AutopromoteCondition( $cond_type, $args, $user, &$result ) {
		$types = array(APCOND_FB_INGROUP   => 'member',
		               APCOND_FB_ISOFFICER => 'officer',
		               APCOND_FB_ISADMIN   => 'admin');
		$type = $types[$cond_type];
		switch( $type ) {
			case 'member':
			case 'officer':
			case 'admin':
				// Connect to the Facebook API and ask if the user is in the group
				$fb = new FBConnectAPI();
				$rights = $fb->getGroupRights($user);
				$result = $rights[$type];
		}
		return true;
	}
	
	/**
	 * Injects some important CSS and Javascript into the <head> of the page.
	 */
	static function BeforePageDisplay( &$out, &$sk ) {
		global $fbLogo, $wgScriptPath, $wgJsMimeType, $fbScript;
		
		// Asynchronously load the Facebook Connect JavaScript SDK before the page's content
		$out->prependHTML('
			<div id="fb-root"></div>
			<script>
				(function(){var e=document.createElement("script");e.type="' .
				$wgJsMimeType . '";e.src="' . $fbScript .
				'";e.async=true;document.getElementById("fb-root").appendChild(e)})();
			</script>' . "\n"
		);
		
		// Add a pretty Facebook logo in front of userpage links if $fbLogo is set
		$style = '<style type="text/css">
			@import url("' . $wgScriptPath . '/extensions/FBConnect/fbconnect.css");' . ($fbLogo ? '
			
			/* Add a pretty Facebook logo to links of Connected users */
			.mw-fbconnectuser {
				background: url(' . $fbLogo . ') top right no-repeat;
				padding-right: 17px;
			}
			
			li#pt-fblink' . ($fb->user() != 0 ? ', li#pt-userpage' : '') . ' {
				background: url(' . $fbLogo . ') top left no-repeat;
				padding-left: 17px;
			}' : '') . '
			
			/* Modify the style of #userloginForm for Special:Connect */
			#userloginForm {
				float: right;
			}
			
			#userloginForm form {
				margin: 0 !important;
			}
		</style>';
		
		/**/
		// Styles and Scripts have been built, so add them to the page
		if (self::MGVS_hack( $mgvs_script ))
			// Inserts list of global JavaScript variables
			$out->addInlineScript( $mgvs_script );
		// Required Facebook Connect JavaScript code
		$out->addScriptFile("$wgScriptPath/extensions/FBConnect/fbconnect-min.js");
		// Styles DHTML tooltips, adds pretty Facebook logos to userpage links
		#$out->addScript( $style );
		
		return true;
	}
	
	/**
	 * Check against stricter requirements (if any) for Facebook Connect users.
	 * Counterintuitively, we do the requirement checks first. This is to prevent
	 * unnecessary API group-related queries.
	 * 
	 * $promote contains the groups that will be added. If the user isn't entitled
	 * to these groups, then we flush this array down the toilet.
	 */
	static function GetAutoPromoteGroups( &$user, &$promote ) {
		// If there isn't any groups to promote to anyway
		if( !count($promote) ) {
			return true;
		}
		/**
		// Requirement checks would go here to prevent unnecessary API group queries
		// E.g. if there was a seperate AutoConfirmAge or AutoConfirmCount check for Facebook users
		global $fbAutoConfirmAge, $fbAutoConfirmCount;
		if( !isset( $fbAutoConfirmAge ))
			$fbAutoConfirmAge = 0;
		if( !isset( $fbAutoConfirmCount ))
			$fbAutoConfirmCount = 0;
		$age = time() - wfTimestampOrNull( TS_UNIX, $user->getRegistration() );
		if( $age >= $fbAutoConfirmAge && $user->getEditCount() >= $fbAutoConfirmCount ) {
			// Matches requirements, don't bother checking if we're in a group
			return true;
		}
		/**
		// If user is not in Facebook group, empty the $promote array
		$inGroup = true;
		if( !$inGroup ) {
			$promote = array();
		}
		/**/
		return true;
	}
	
	/**
	 * Add a permissions error when permissions errors are checked for.
	 * 
	 * The difference between getUserPermissionsErrors and getUserPermissionsErrorsExpensive:
	 * 
	 * Typically, both hooks are run when checking for proper permissions in Title.php. When
	 * it is desireable to skip potentially expensive cascading permission checks, only
	 * getUserPermissionsErrors is run. This behavior is suitable for nonessential UI
	 * controls in common cases, but _not_ for functional access control. This behavior
	 * may provide false positives, but should never provide a false negative.
	 */
	static function getUserPermissionsErrorsExpensive( $title, $user, $action, &$result ) {
		//echo "getUserPermissionsErrorsExpensive\n";
		return true;
	}
	
	/**
	 * Fired when MediaWiki is updated to allow FBConnect to update the database.
	 * If the database type is supported, then a new tabled named 'user_fbconnect'
	 * is created. For the table's layout, see fbconnect_table.sql.
	 * 
	 * TODO: Prefix the table with $wgDBprefix.
	 */
	static function LoadExtensionSchemaUpdates() {
		global $wgDBtype, $wgExtNewTables;
		$base = dirname( __FILE__ );
		if ( $wgDBtype == 'mysql' ) {
			$wgExtNewTables[] = array( 'user_fbconnect', "$base/fbconnect_table.sql" );
		} else if ( $wgDBtype == 'postgres' ) {
			$wgExtNewTables[] = array( 'user_fbconnect', "$base/fbconnect_table.pg.sql" );
		}
		return true;
	}
	
	/**
	 * Adds several Facebook Connect variables to the page:
	 * 
	 * fbAPIKey			The application's API key (see $fbAPIKey in config.php)
	 * fbUseMarkup		Should XFBML tags be rendered? (see $fbUseMarkup in config.php)
	 * fbLoggedIn		(deprecated) Whether the PHP client reports the user being Connected
	 * fbLogoutURL		(deprecated) The URL to be redirected to on a disconnect
	 * 
	 * This hook was added in MediaWiki version 1.14. See:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 * If we are not at revision 38397 or later, this function is called from BeforePageDisplay
	 * to retain backward compatability.
	 */
	static function MakeGlobalVariablesScript( &$vars ) {
		global $wgTitle, $fbApiKey, $fbUseMarkup;
		$thisurl = $wgTitle->getPrefixedURL();
		$vars['fbApiKey'] = $fbApiKey;
		#$vars['fbLoggedIn'] = FBConnect::$api->user() ? true : false;
		#$vars['fbLogoutURL'] = Skin::makeSpecialUrl('Userlogout',
		#                       $wgTitle->isSpecial('Preferences') ? '' : "returnto={$thisurl}");
		#$vars['fbNames'] = FBConnect::$api->getPersons();
		$vars['fbUseMarkup'] = $fbUseMarkup;
		return true;
	}
	
	/**
	 * Hack: Run MakeGlobalVariablesScript for backwards compatability.
	 * The MakeGlobalVariablesScript hook was added to MediaWiki 1.14 in revision 38397:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 */
	private static function MGVS_hack( &$script ) {
		global $wgVersion, $IP;
		if (version_compare($wgVersion, '1.14', '<')) {
			$svn = SpecialVersion::getSvnRevision($IP);
			// if !$svn, then we must be using 1.13.x (as opposed to 1.14alpha+)
			if (!$svn || $svn < 38397)
			{
				$script = "";
				$vars = array();
				wfRunHooks('MakeGlobalVariablesScript', array(&$vars));
				foreach( $vars as $name => $value ) {
					$script .= "\t\tvar $name = " . json_encode($value) . ";\n";
	    		}
	    		return true;
			}
		}
		return false;
	}
	
	/**
	 * Installs a parser hook for every tag reported by FBConnectXFBML::availableTags().
	 * Accomplishes this by asking FBConnectXFBML to create a hook function that then
	 * redirects to FBConnectXFBML::parserHook().
	 */
	static function ParserFirstCallInit( &$parser ) {
		$pHooks = FBConnectXFBML::availableTags();
		foreach( $pHooks as $tag ) {
			$parser->setHook( $tag, FBConnectXFBML::createParserHook( $tag ));
		}
		return true;
	}
	
	/**
	 * Modify the user's persinal toolbar (in the upper right).
	 * TODO!
	 */
	static function PersonalUrls( &$personal_urls, &$wgTitle ) {
		global $wgUser, $wgLang, $wgShowIPinHeader, $fbPersonalUrls, $fbConnectOnly;
		wfLoadExtensionMessages('FBConnect');
		// Get the logged-in user from the Facebook API
		$fb = new FBConnectAPI();
		$fb_user = $fb->user();
		
		/*
		 * Personal URLs option: show_ip_in_header
		 */
		$wgShowIPinHeader = $fbPersonalUrls['show_ip_in_header'];
		
		/*
		 * Personal URLs option: remove_user_talk_link
		 */
		if ($fbPersonalUrls['remove_user_talk_link'] &&
				array_key_exists('mytalk', $personal_urls)) {
			unset($personal_urls['mytalk']);
		}
		
		// If the user is logged in and connected
		if ($wgUser->isLoggedIn() && 0 != $fb_user) {
			/*
			 * Personal URLs option: use_real_name_from_fb
			 */
			$option = $fbPersonalUrls['use_real_name_from_fb'];
			if ($option === true || ($option && strpos($wgUser->getName(), $option) === 0)) {
				// Start with the real name in the database
				$name = $wgUser->getRealName();
				// Ask Facebook for the real name
				if (!$name || $name == '') {
					$name = $fb->userName();
				}
				// Make sure we were able to get a name from the database or Facebook
				if ($name && $name != '') {
					$personal_urls['userpage']['text'] = $name;
				}
			}
			// Replace logout link with a button to disconnect from Facebook Connect
			// TODO: Use JQuery to set logout action
			unset( $personal_urls['logout'] );
			$personal_urls['fblogout'] = array(
				'text'   => wfMsg( 'fbconnect-logout' ),
				'href'   => '#',
				'active' => false );
			/*
			 * Personal URLs option: link_back_to_facebook
			 */
			if ($fbPersonalUrls['link_back_to_facebook']) {
				$personal_urls['fblink'] = array(
					'text'   => wfMsg( 'fbconnect-link' ),
					'href'   => "http://www.facebook.com/profile.php?id=$fb_user",
					'active' => false );
			}
		}
		// User is logged in but not Connected
		else if ($wgUser->isLoggedIn()) {
			/*
			 * Personal URLs option: hide_convert_button
			 */
			// TODO: Link to Special:Connect/Convert
			if (!$fbPersonalUrls['hide_convert_button']) {
				$personal_urls['fblink'] = array(
					'text'   => wfMsg( 'fbconnect-connect' ),
					'href'   => Skin::makeSpecialUrl( 'Connect', '' ),
					'active' => false );
			}
		}
		// User is not logged in
		else {
			// Add an option to connect via Facebook Connect
			$personal_urls['fbconnect'] = array(
				'text'   => wfMsg( 'fbconnectlogin' ),
				'href'   => SpecialPage::getTitleFor( 'Connect' )->
				              getLocalUrl( 'returnto=' . $wgTitle->getPrefixedURL() ),
			//	'href'   => '#',   # TODO: Update JavaScript and then use this href
				'active' => $wgTitle->isSpecial( 'Connect' )
			);
			
			// Remove other personal toolbar links
			if ($fbConnectOnly) {
				foreach (array('login', 'anonlogin') as $k) {
					if (array_key_exists($k, $personal_urls)) {
						unset($personal_urls[$k]);
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Modify the preferences form. At the moment, we simply turn the user name
	 * into a link to the user's facebook profile.
	 * TODO!
	 */
	public static function RenderPreferencesForm( $form, $output ) {
		global $wgUser;
		
		// If the user name is a valid Facebook ID, link to the Facebook profile
		if( FBConnect::$api->isConnected() ) {
			$html = $output->getHTML();
			$name = $wgUser->getName();
			$i = strpos( $html, $name );
			if ($i !== FALSE) {
				// Replace the old output with the new output
				$html =  substr( $html, 0, $i ) . preg_replace( "/$name/",
				    "<a href=\"http://www.facebook.com/profile.php?id=$name\" " .
					"class='mw-userlink mw-fbconnectuser'>$name</a>", substr( $html, $i ), 1 );
				$output->clearHTML();
				$output->addHTML( $html );
			}
		}
		return true;
	}
	
	/**
	 * Adds the class "mw-userlink" to links belonging to Connect accounts on
	 * the page Special:ListUsers.
	 */
	static function SpecialListusersFormatRow( &$item, $row ) {
		global $fbSpecialUsers;
		
		// Only modify Facebook Connect users
		if (!$fbSpecialUsers ||
				!count(FBConnectDB::getFacebookIDs(User::newFromName($row->user_name)))) {
			return true;
		}
		
		// Look to see if class="..." appears in the link
		$regs = array();
		preg_match( '/^([^>]*?)class=(["\'])([^"]*)\2(.*)/', $item, $regs );
		if (count( $regs )) {
			// If so, append " mw-userlink" to the end of the class list
			$item = $regs[1] . "class=$regs[2]$regs[3] mw-userlink$regs[2]" . $regs[4];
		} else {
			// Otherwise, stick class="mw-userlink" into the link just before the '>'
			preg_match( '/^([^>]*)(.*)/', $item, $regs );
			$item = $regs[1] . ' class="mw-userlink"' . $regs[2];
		}
		return true;
	}
	
	/**
	 * Adds some info about the governing Facebook group to the header form of Special:ListUsers.
	 */
	static function SpecialListusersHeaderForm( &$pager, &$out ) {
		global $fbUserRightsFromGroup, $fbLogo;
		if (!$fbUserRightsFromGroup) {
			return true;
		}
		$gid = $fbUserRightsFromGroup;
		// Connect to the API and get some info about the group
		$fb = new FBConnectAPI();
		$group = $fb->groupInfo();
		$groupName = $group['name'];
		$cid = $group['creator'];
		$pic = $group['picture'];
		$out .= '
			<table style="border-collapse: collapse;">
				<tr>
					<td>
						' . wfMsgWikiHtml( 'fbconnect-listusers-header',
						wfMsg( 'group-bureaucrat-member' ), wfMsg( 'group-sysop-member' ),
						"<a href=\"http://www.facebook.com/group.php?gid=$gid\">$groupName</a>",
						"<a href=\"http://www.facebook.com/profile.php?id=$cid#User:$cid\" " .
						"class=\"mw-userlink\">$cid</a>") . '
					</td>
	        		<td>
	        			<img src="' . "$pic\" title=\"$groupName\" alt=\"$groupName" . '">
	        		</td>
	        	</tr>
	        </table>';
		return true;
	}
	
	/**
	 * Removes Special:UserLogin and Special:CreateAccount from the list of
	 * Special Pages if $fbConnectOnly is set to true.
	 */
	static function SpecialPage_initList( &$aSpecialPages ) {
		global $fbConnectOnly;
		if ($fbConnectOnly) {
			$aSpecialPages['Userlogin'] = array('SpecialRedirectToSpecial', 'UserLogin', 'Connect',
				false, array('returnto', 'returntoquery'));
			// Used in 1.12.x and above
			$aSpecialPages['CreateAccount'] = array('SpecialRedirectToSpecial', 'CreateAccount',
				'Connect');
		}
		return true;
	}
	
	/**
	 * Removes the 'createaccount' right from users if $fbConnectOnly is true.
	 */
	static function UserGetRights( &$user, &$aRights ) {
		global $fbConnectOnly;
		if ( $fbConnectOnly ) {
			foreach ( $aRights as $i => $right ) {
				if ( $right == 'createaccount' ) {
					unset( $aRights[$i] );
					break;
				}
			}
		}
		return true;
	}
	
	/**
	 * If the user isn't logged in, try to auto-authenticate via Facebook
	 * Connect. The Single Sign On magic of FBConnect happens in this function.
	 */
	static function UserLoadFromSession( $user, &$result ) {
		global $fbConnectOnly, $wgAuth, $wgUser;
		$fb = new FBConnectAPI();
		// Check to see if we have a connection with Facebook
		if (!$fb->user()) {
			// No connection with facebook, return $fbConnectOnly
			return $fbConnectOnly;
		}
		// Look up the MW ID of the Facebook user
		$localId = FBConnectDB::getUser($fb->user())->getId();
		// If the user exists, then log them in
		if ($localId) {
			$user->setID ($localId);
			$user->loadFromId();
			// Updates the user's info from Facebook if no real name is set
			$wgAuth->updateUser( $user );
		} else {
			// User has not visited the wiki before, so create a new user from their Facebook ID
			$userName = $fb->userName();
			
			// Test to see if we are denied by FBConnectAuthPlugin or the user can't create an account
			if ( !$wgAuth->autoCreate() || !$wgAuth->userExists( $userName ) ||
			                               !$wgAuth->authenticate( $userName )) {
				#if( $wgAuth->strict() ) {
			     	$result = false;
				#}
				return true;
			}
			
			// Checks passed, create the user
			$user->loadDefaults( $userName );
			$user->addToDatabase();
			
			$wgAuth->initUser( $user, true );
			$wgUser = $user;
			
			// Update the user count
			$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
			$ssUpdate->doUpdate();
			
			// Notify hooks (e.g. Newuserlog)
			wfRunHooks( 'AuthPluginAutoCreate', array( $wgUser ));
			
			// Which MediaWiki versions can we call this function in?
			$user->addNewUserLogEntryAutoCreate();
		}
		
		// Authentification okay
		wfSetupSession();
		$result = true;
		return true;
	}
	/**/
}
