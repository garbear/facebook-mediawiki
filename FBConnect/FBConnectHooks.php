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
 * Class FBConnectHooks
 * 
 * This class contains all the hooks used in this extension. HOOKS DO NOT NEED
 * TO BE EXPLICITLY ADDED TO $wgHooks. Simply write a function with the same
 * name as the hook that provokes it, place it inside this class and let
 * FBConnect::init() do its magic. Helper functions should be private, because
 * only public static methods with an initial capital letter are added as hooks.
 */
class FBConnectHooks {
	/**
	 * Checks the autopromote condition for a user.
	 * 
	 * @TODO: Filter FBConnect::$api->getGroupRights() to reduce Facebook API overhead.
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
				$rights = FBConnect::$api->getGroupRights( $user );
				$result = $rights[$type];
		}
		return true;
	}
	
	/**
	 * Check against stricter requirements (if any) for Facebook Connect users. Counterintuitively,
	 * we do the requirement checks first. This is to prevent unnecessary API group-related queries.
	 * 
	 * $promote contains the groups that will be added. If the user isn't entitled to these groups,
	 * then we flush this array down the toilet.
	 */
	static function GetAutoPromoteGroups( &$user, &$promote ) {
		//echo "\nGetAutoPromoteGroups\n";
		//var_dump( $promote );
		/**/
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
	static function XXXgetUserPermissionsErrorsExpensive( $title, $user, $action, &$result ) {
		//echo "getUserPermissionsErrorsExpensive\n";
		return true;
	}
	
	/**
	 * Adds several Facebook Connect variables to the page:
	 * 
	 * fbAPIKey			The application's API key (see $fbAPIKey in config.php)
	 * fbLoggedIn		Whether the PHP client reports the user being Connected
	 * fbLogoutURL		The URL to be redirected to on a disconnect
	 * 
	 * This hook was added in MediaWiki version 1.14. See:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 * If we are not at revision 38397 or later, this function is called from BeforePageDisplay
	 * to retain backward compatability.
	 */
	static function MakeGlobalVariablesScript( &$vars ) {
		global $wgTitle, $fbApiKey; 
		$thisurl = $wgTitle->getPrefixedURL();
		$vars['fbApiKey'] = $fbApiKey;
		$vars['fbLoggedIn'] = FBConnect::$api->user() ? true : false;
		$vars['fbLogoutURL'] = Skin::makeSpecialUrl('Userlogout',
		                       $wgTitle->isSpecial('Preferences') ? '' : "returnto={$thisurl}");
		$vars['fbNames'] = FBConnect::$api->getPersons();
		return true;
	}
	
	/**
	 * Hack: run MakeGlobalVariablesScript for backwards compatability.
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
	 * Injects some important CSS and Javascript into the <head> of the page.
	 */
	static function BeforePageDisplay( &$out, &$sk ) {
		global $fbLogo, $wgScriptPath, $wgJsMimeType;
		
		// Parse page output for Facebook IDs
		$html = $out->getHTML();
		// 64-bit user IDs can be up to 20 characters long in base 10
		preg_match_all('/User:(\d{1,19})["\'&]/', $html, $ids);
		foreach( $ids[1] as $id )
			FBConnect::$api->addPersonById(intval( $id ));
		
		// Add a pretty Facebook logo in front of userpage links if $fbLogo is set
		$style = '<style type="text/css">
			@import url("' . $wgScriptPath . '/extensions/FBConnect/fbconnect.css");' . ($fbLogo ? '
			
			/* Add a pretty Facebook logo to links of Connected users */
			.mw-fbconnectuser {
				background: url(' . $fbLogo . ') top right no-repeat;
				padding-right: 17px;
			}
			
			li#pt-fblink' . (FBConnect::$api->isConnected() ? ', li#pt-userpage' : '') . ' {
				background: url(' . $fbLogo . ') top left no-repeat;
				padding-left: 17px;
			}' : '') . (FBConnect::$special_connect ? '
			
			/* Modify the style of #userloginForm for Special:Connect */
			#userloginForm {
				float: right;
			}
			
			#userloginForm form {
				margin: 0 !important;
			}' : '') . '
		</style>';
		
		// Styles and Scripts have been built, so add them to the page
		if (self::MGVS_hack( $mgvs_script ))
			// Inserts list of global JavaScript variables
			$out->addInlineScript( $mgvs_script );
		// Enables DHTML tooltips
		$out->addScript("<script type=\"$wgJsMimeType\" " .
		                "src=\"$wgScriptPath/extensions/FBConnect/wz_tooltip/wz_tooltip.js\"></script>\n");
		// Required Facebook Connect JavaScript code
		$out->addScript("<script type=\"$wgJsMimeType\" " . 
		                "src=\"$wgScriptPath/extensions/FBConnect/fbconnect.js\"></script>\n");
		// Styles DHTML tooltips, adds pretty Facebook logos to userpage links
		$out->addScript( $style );
		
		return true;
	}
	
	/**
	 * Adds Facebook tooltip info to the rows of Connected users in Special:ListUsers.
	 */
	static function SpecialListusersFormatRow( &$item, $row ) {
		// Only add DHTML tooltips to Facebook Connect users
		if (!FBConnect::$api->isIdValid( $row->user_name ))  # || $row->edits == 0) {
			return true;
		
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
	 * This script is necessary for Facebook Connect because it refers the browser to the
	 * Facebook JavaScript Feature Loader file. This script should be referenced in the
	 * BODY not in the HEAD, as low as possible before FB.init() is called.
	 */
	static function SkinAfterBottomScripts( $skin, &$text ) {
		$text = "\n\t\t<script type=\"text/javascript\" " .
			"src=\"http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php\">" .
			"</script>$text";
		return true;
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
	 * Modify the user's persinal toolbar (in the upper right)
	 */
	static function PersonalUrls( &$personal_urls, &$wgTitle ) {
		global $wgUser, $fbAllowOldAccounts, $fbRemoveUserTalkLink;
		
		wfLoadExtensionMessages( 'FBConnect' );
		
		if ( $wgUser->isLoggedIn() ) {
			if( FBConnect::$api->isConnected() ) {
				
				// Replace ugly Facebook ID numbers with the user's real name
				if( $wgUser->getRealName() != "" )
					$personal_urls['userpage']['text'] = $wgUser->getRealName();
				
				// Replace logout link with a button to disconnect from Facebook Connect
				unset( $personal_urls['logout'] );
				$personal_urls['fblogout'] = array(
					'text'   => wfMsg( 'fbconnect-logout' ),
					'href'   => '#',
					'active' => false );
				
				// Add a convenient link back to facebook.com
				// This helps enforce the idea that this wiki is "in front" of Facebook
				$personal_urls['fblink'] = array(
					'text'   => wfMsg( 'fbconnect-link' ),
					'href'   => 'http://www.facebook.com/profile.php?id=' . $wgUser->getName(),
					'active' => false );
				
			} else {  # User is logged in but not Connected
				
				// Link to a special page that connects the user's account with their Facebook ID
				$personal_urls['fblink'] = array(
					'text'   => wfMsg( 'fbconnect-connect' ),
					'href'   => Skin::makeSpecialUrl( 'Connect', '' ),
					'active' => false );
			}
		} else {  # User is not logged in
			
			// Add an option to connect via Facebook Connect
			$personal_urls['fbconnect'] = array(
				'text'   => wfMsg( 'fbconnectlogin' ),
				'href'   => SpecialPage::getTitleFor( 'Connect' )->
				              getLocalUrl( 'returnto=' . $wgTitle->getPrefixedURL() ),
			//	'href'   => '#',   # TODO: Update JavaScript and then use this href
				'active' => $wgTitle->isSpecial( 'Connect' ) );
			
			// Remove other personal toolbar links
			if( !$fbAllowOldAccounts ) {
				foreach( array( 'login', 'anonlogin' ) as $k ) {
					if( array_key_exists( $k, $personal_urls ) )
						unset( $personal_urls[$k] );
				}
			}
		}
		
		// Unset user talk page links
		if ( $fbRemoveUserTalkLink && array_key_exists( 'mytalk', $personal_urls ))
			unset( $personal_urls['mytalk'] );

		return true;
	}
	
	/**
	 * Modify the preferences form. At the moment, we simply turn the user name
	 * into a link to the user's facebook profile.
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
	 * Adds some info about the governing Facebook group to the header form of Special:ListUsers.
	 */
	static function SpecialListusersHeaderForm( &$pager, &$out ) {
		global $fbUserRightsFromGroup, $fbLogo;
		$gid = $fbUserRightsFromGroup;
		if ( $gid ) {
			$group = FBConnect::$api->groupInfo();
			$groupName = $group['name'];
			$cid = $group['creator'];
			$pic = $group['picture'];
			$out .= '
				<table>
					<tr>
						<td>
							' . wfMsgWikiHtml( 'fbconnect-listusers-header',
							wfMsg( 'group-bureaucrat-member' ), wfMsg( 'group-sysop-member' ),
							"<a href=\"http://www.facebook.com/group.php?gid=$gid\">$groupName</a>",
							"<a href=\"http://www.facebook.com/profile.php?id=$cid#User:$cid\" " .
							"class=\"mw-userlink\">$cid</a>") . '
						</td>
		        		<td>
		        			<img src="' . $pic . '" alt="' . $groupName . '">
		        		</td>
		        	</tr>
		        </table>';
		}
		return true;
	}
	
	/**
	 * If the user isn't logged in, try to auto-authenticate via Facebook Connect.
	 */
	static function UserLoadFromSession( $user, &$result ) {
		global $wgAuth, $wgUser;
		// Check to see if we have a connection with Facebook
		$fb_uid = FBConnect::$api->user();
		if ( $fb_uid == 0 ) {
			// No connection with facebook, so use local sessions only if FBConnectAuthPlugin allows it
			if( $wgAuth->strict() ) {
				$result = false;
			}
			return true;
		}
		$localId = User::idFromName( "$fb_uid" );
		// If the user exists, then log them in
		if ( $localId ) {
			$user->setID( $localId );
			$user->loadFromId();
			// Updates the user's info from Facebook if no real name is set
			#echo "Udating...\n";
			$wgAuth->updateUser( $user );
		} else {
			// User has not visited the wiki before, so create a new user from their Facebook ID
			$userName = "$fb_uid";
			
			// Test to see if we are denied by FBConnectAuthPlugin or the user can't create an account
			if ( !$wgAuth->autoCreate() || !$wgAuth->userExists( $userName ) ||
			     !$wgAuth->authenticate( $user->getName() ) || $wgUser->isBlockedFromCreateAccount() ) {
				/*
				if( $wgAuth->strict() ) {
			     	$result = false;
				}
				/**/
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
}
