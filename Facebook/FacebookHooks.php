<?php
/*
 * Copyright © 2008-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Class FacebookHooks
 * 
 * This class contains all the hooks used in this extension. HOOKS DO NOT NEED
 * TO BE EXPLICITLY ADDED TO $wgHooks. Simply write a public static function
 * with the same name as the hook that provokes it, place it inside this class
 * and let FacebookInit::init() do its magic. Helper functions should be private,
 * because only public static methods are added as hooks.
 */
class FacebookHooks {
	/**
	 * Hook is called whenever an article is being viewed... Currently, figures
	 * out the Facebook ID of the user that the userpage belongs to.
	 */
	public static function ArticleViewHeader( &$article, &$outputDone, &$pcache ) {
		// Get the article title
		$nt = $article->getTitle();
		// If the page being viewed is a user page
		if ($nt && $nt->getNamespace() == NS_USER && strpos($nt->getText(), '/') === false) {
			$user = User::newFromName($nt->getText());
			if (!$user || $user->getID() == 0) {
				return true;
			}
			$fb_ids = FacebookDB::getFacebookIDs($user->getId());
			if ( !count($fb_ids) ) {
				return true;
			}
			$fb_id = $fb_ids[0]; // possibly multiple Facebook accounts associated with this user
			// TODO: Something with the Facebook ID stored in $fb_id
			return true;
		}
		return true;
	}
	
	/**
	 * Checks the autopromote condition for a user.
	 */
	static function AutopromoteCondition( $cond_type, $args, $user, &$result ) {
		global $wgFbUserRightsFromGroup;
		
		// If there's no group to pull rights from, the user can't be a member
		$result = false;
		if ( !empty( $wgFbUserRightsFromGroup ) ) {
			$types = array(
				APCOND_FB_INGROUP   => 'member',
				APCOND_FB_ISADMIN   => 'admin'
			);
			$type = $types[$cond_type];
			switch( $type ) {
			case 'member':
			case 'admin':
				$fbUser = new FacebookUser();
				// Only check for the currently logged-in user
				if ($user->getId() && $user->getId() == $fbUser->getMWUser()->getId()) {
					$rights = $fbUser->getGroupRights();
					$result = $rights[$type];
				}
			}
		}
		return true;
	}
	
	/**
	 * Injects some important CSS and Javascript into the <head> of the page.
	 */
	public static function BeforePageDisplay( &$out, &$sk ) {
		global $wgUser, $wgVersion, $wgFbLogo, $wgFbScript, $wgFbExtensionScript, $wgJsMimeType, $wgStyleVersion;
		
		// Wikiaphone skin for mobile device doesn't need JS or CSS additions 
		if ( get_class( $wgUser->getSkin() ) === 'SkinWikiaphone' )
			return true;
		
		// Check to see if we should localize the JS SDK
		if (strpos( $wgFbScript, FACEBOOK_LOCALE ) !== false) {
			wfProfileIn( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
			// NOTE: Can't use $wgLanguageCode here because the same Facebook config can
			// run for many $wgLanguageCode's on one site (such as Wikia).
			global $wgLang;
			// Attempt to find a matching Facebook locale
			$locale = FacebookLanguage::getFbLocaleForLangCode( $wgLang->getCode() );
			$wgFbScript = str_replace( FACEBOOK_LOCALE, $locale, $wgFbScript );
			wfProfileOut( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
		}
		
		// Asynchronously load the Facebook JavaScript SDK before the page's content
		// See <https://developers.facebook.com/docs/reference/javascript>
		global $wgNoExternals;
		if ( !empty($wgFbScript) && empty($wgNoExternals) ) {
			$out->prependHTML('
				<div id="fb-root"></div>
<script type="' . $wgJsMimeType . '">
(function(d){var js,id="facebook-jssdk";if(!d.getElementById(id)){js=d.createElement("script");' .
'js.id=id;js.async=true;js.type="' . $wgJsMimeType . '";js.src="' . $wgFbScript .
'";d.getElementsByTagName("head")[0].appendChild(js);}}(document));
</script>' . "\n"
			);
		}
		
		// Inserts list of global JavaScript variables if necessary
		if (self::MGVS_hack( $mgvs_script )) {
			$out->addInlineScript( $mgvs_script );
		}
		
		// Add a Facebook logo to the class .mw-fblink
		$style = empty($wgFbLogo) ? '' : <<<STYLE
.mw-facebook-logo {
	background-image: url($wgFbLogo) !important;
	background-repeat: no-repeat !important;
	background-position: left top !important;
	padding-left: 19px !important;
}

STYLE;
		$style .= '.fbInitialHidden {display:none;}';
		
		// Things get a little simpler in 1.16...
		if ( version_compare( $wgVersion, '1.16', '>=' ) ) {
			$out->addInlineStyle( $style );
			// Include the common jQuery library
			$out->includeJQuery();
			if ( !empty( $wgFbExtensionScript ) ) {
				if ( version_compare( $wgVersion, '1.17', '>=' ) ) {
					// ResourceLoader was introduced in MW 1.17. This shifted the focus
					// on delivering page HTML as fast as possible and deferring all
					// scripts to the end of the page or asynchronous loading. However,
					// our script is a callback for an async script (Facebook's JS SDK).
					// This means it must be in place before the script is loaded!
					$out->addHeadItem('fbscript',
							"<script type=\"$wgJsMimeType\" src=\"$wgFbExtensionScript?$wgStyleVersion\"></script>\n");
				} else {
					$out->addScriptFile( $wgFbExtensionScript );
				}
			}
		} else {
			$out->addScript( '<style type="text/css">' . $style . '</style>' );
			// Include the most recent 1.7 version
			$out->addScriptFile( 'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js' );
			// Add the script file specified by $url
			if( !empty( $wgFbExtensionScript ) ) {
				$out->addScript("<script type=\"$wgJsMimeType\" src=\"$wgFbExtensionScript?$wgStyleVersion\"></script>\n");
			}
		}
		return true;
	}
	
	/**
	 * Fired when MediaWiki is updated (from the command line updater utility or,
	 * if using version 1.17+, from the initial installer). This hook allows
	 * Facebook to update the database with the required tables. Each table
	 * listed below should have a corresponding schema file in the sql directory
	 * for each supported database type.
	 * 
	 * MYSQL ONLY: If $wgDBprefix is set, then the table 'user_fbconnect' will
	 * be prefixed accordingly. Make sure that the .sql files are modified with
	 * the database prefix beforehand.
	 * 
	 * The $updater parameter added in r71140 (after 1.16)
	 * <http://svn.wikimedia.org/viewvc/mediawiki?view=revision&revision=71140>
	 */
	static function LoadExtensionSchemaUpdates( $updater = null ) {
		global $wgSharedDB, $wgDBname, $wgDBtype, $wgDBprefix;
		// Don't create tables on a shared database
		if( !empty( $wgSharedDB ) && $wgSharedDB !== $wgDBname ) {
			return true;
		}
		// Tables to add to the database
		$tables = array( 'user_fbconnect' /*, 'fbconnect_event_stats', 'fbconnect_event_show'*/ );
		// Sql directory inside the extension folder
		$sql = dirname( __FILE__ ) . '/sql';
		// Extension of the table schema file (depending on the database type)
		switch ( $updater !== null ? $updater->getDB()->getType() : $wgDBtype ) {
			case 'mysql':
				$ext = 'sql';
				break;
			case 'postgres':
				$ext = 'pg.sql';
				break;
			default:
				$ext = 'sql';
		}
		// Do the updating
		foreach ( $tables as $table ) {
			if ( $wgDBprefix ) {
				$table = $wgDBprefix . $table;
			}
			// Location of the table schema file
			$schema = "$sql/$table.$ext";
			// If we're using the new version of the LoadExtensionSchemaUpdates hook
			if ( $updater !== null ) {
				$updater->addExtensionUpdate( array( 'addTable', $table, $schema, true ) );
			} else {
				global $wgExtNewTables;
				$wgExtNewTables[] = array( $table, $schema );
			}
		}
		return true;
	}
	
	/**
	 * Adds several Facebook Connect variables to the page:
	 * 
	 * fbAppId		 The application ID (see $wgFbAppId in config.php)
	 * fbUseXFBML    Should XFBML tags be rendered (see $wgFbSocialPlugins in config.default.php)
	 * fbLogo        Facebook logo (see $wgFbLogo in config.php)
	 * 
	 * This hook was added in MediaWiki version 1.14. See:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 * If we are not at revision 38397 or later, this function is called from BeforePageDisplay
	 * to retain backward compatability.
	 */
	public static function MakeGlobalVariablesScript( &$vars ) {
		global $wgFbAppId, $facebook, $wgFbSocialPlugins, $wgTitle, $wgRequest, $wgStyleVersion, $wgUser;
		if (!isset($vars['wgPageQuery'])) {
			$query = $wgRequest->getValues();
			if (isset($query['title'])) {
				unset($query['title']);
			}
			$vars['wgPageQuery'] = wfUrlencode( wfArrayToCGI( $query ) );
		}
		if (!isset($vars['wgStyleVersion'])) {
			$vars['wgStyleVersion'] = $wgStyleVersion;
		}
		$vars['fbAppId']     = $wgFbAppId;
		$vars['fbUseXFBML']  = $wgFbSocialPlugins;
		// Let JavaScript know if the Facebook ID belongs to someone else
		if ( $wgUser->isLoggedIn() && !$facebook->getUser() ) {
			$ids = FacebookDB::getFacebookIDs($wgUser);
			if ( count($ids) ) {
				$vars['fbId'] = strval( $ids[0] );
			}
		}
		return true;
	}
	
	/**
	 * Hack: Run MakeGlobalVariablesScript for backwards compatability.
	 * The MakeGlobalVariablesScript hook was added to MediaWiki 1.14 in revision 38397:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 */
	private static function MGVS_hack( &$script ) {
		global $wgVersion, $IP;
		if (version_compare($wgVersion, '1.14.0', '<')) {
			$script = "";
			$vars = array();
			wfRunHooks('MakeGlobalVariablesScript', array(&$vars));
			foreach( $vars as $name => $value ) {
				$script .= "\t\tvar $name = " . json_encode($value) . ";\n";
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Installs a parser hook for every tag reported by FacebookXFBML::availableTags().
	 * Accomplishes this by asking FacebookXFBML to create a hook function that then
	 * redirects to FacebookXFBML::parserHook().
	 */
	public static function ParserFirstCallInit( &$parser ) {
		$pHooks = FacebookXFBML::availableTags();
		foreach( $pHooks as $tag ) {
			$parser->setHook( $tag, FacebookXFBML::createParserHook( $tag ));
		}
		return true;
	}
	
	/**
	 * Simple boolean test of whether the "Log in with Facebook" button should
	 * be shown. This test is isolated in its own function so that the
	 * PersonalUrls and MakeGlobalVariablesScript hooks can both use it. The
	 * rationality behind this is that we only needs to pass the list of
	 * Facebook permissions via JavaScript if the Login button is actually shown.
	 */
	private static function showLogin() {
		global $wgUser, $wgFbAlwaysShowLogin, $facebook;
		$id = $facebook->getUser();
		return !$wgUser->isLoggedIn() ||
		       !(empty( $wgFbAlwaysShowLogin ) ||
		           ($id && in_array($id, FacebookDB::getFacebookIDs($wgUser))));
	}
	
	/**
	 * Modify the user's persinal toolbar (in the upper right).
	 */
	public static function PersonalUrls( &$personal_urls, &$wgTitle ) {
		global $wgUser, $wgFbUseRealName, $wgFbDisableLogin;
		wfLoadExtensionMessages('Facebook');
		
		if ( $wgUser->isLoggedIn() && !empty( $wgFbUseRealName ) ) {
			$fb_ids = FacebookDB::getFacebookIDs($wgUser);
			if ( count( $fb_ids ) ) {
				// Start with the real name in the database
				$name = $wgUser->getRealName();
				if ( empty( $name ) ) {
					// Ask Facebook for the real name
					$fbUser = new FacebookUser($fb_ids[0]);
					$name = $fbUser->getUserInfo('name');
				}
				// Make sure we were able to get a name from the database or Facebook
				if ( !empty( $name ) ) {
					$personal_urls['userpage']['text'] = $name;
				}
			}
		}
		
		if ( self::showLogin() ) {
			if ( isset( $personal_urls['logout'] ) ) {
				// Place the convert button before the logout link
				$logout_item = end( $personal_urls );
				$personal_urls = array_slice( $personal_urls, 0, -1, true );
			}
			$personal_urls['facebook'] = array(
				'text'   => wfMsg( 'facebook-connect' ),
				'href'   => SpecialPage::getTitleFor('Connect')->getLocalUrl('returnto=' . $wgTitle->getPrefixedURL()),
				'class'  => 'mw-facebook-logo',
				'active' => $wgTitle->isSpecial( 'Connect' ),
			);
			if ( !empty( $logout_item ) ) {
				$personal_urls['logout'] = $logout_item;
			}
		}
		
		if ( !$wgUser->isLoggedIn() && !empty( $wgFbDisableLogin ) ) {
			// Remove other personal toolbar links
			foreach (array('login', 'anonlogin') as $k) {
				if (array_key_exists($k, $personal_urls)) {
					unset($personal_urls[$k]);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Modify the preferences form. At the moment, we simply turn the user name
	 * into a link to the user's facebook profile.
	 * 
	 * TODO!
	 */
	public static function RenderPreferencesForm( $form, $output ) {
		//global $facebook, $wgUser;
		
		// This hook no longer seems to work...
		
		/*
		$ids = FacebookDB::getFacebookIDs($wgUser);
		
		$fb_user = $facebook->getUser();
		if( $fb_user && count($ids) > 0 && in_array( $fb_user, $ids )) {
			$html = $output->getHTML();
			$name = $wgUser->getName();
			$i = strpos( $html, $name );
			if ($i !== FALSE) {
				// If the user has a valid Facebook ID, link to the Facebook profile
				try {
					$fbUser = $facebook->api('/me');
					// Replace the old output with the new output
					$html = substr( $html, 0, $i ) .
					        preg_replace("/$name/", "$name (<a href=\"$fbUser[link]\" " .
					                     "class='mw-userlink mw-facebookuser'>" .
					                     wfMsg('facebook-link-to-profile') . "</a>)",
					                     substr( $html, $i ), 1);
					$output->clearHTML();
					$output->addHTML( $html );
				} catch (FacebookApiException $e) {
					error_log($e);
				}
			}
		}
		/**/
		return true;
	}

	/**
	 * Adds the class "mw-userlink" to links belonging to Connect accounts on
	 * the page Special:ListUsers.
	 */
	static function SpecialListusersFormatRow( &$item, $row ) {
		global $fbSpecialUsers;
		
		// Only modify Facebook Connect users
		if (empty( $fbSpecialUsers ) ||
				!count(FacebookDB::getFacebookIDs(User::newFromName($row->user_name)))) {
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
	 * Adds some info about the governing Facebook group to the header form of
	 * Special:ListUsers.
	 */
	// r274: Fix error with PHP 5.3 involving parameter references (thanks, PChott)
	static function SpecialListusersHeaderForm( $pager, &$out ) {
		global $wgFbUserRightsFromGroup, $facebook;
		
		if ( empty( $wgFbUserRightsFromGroup ) ) {
			return true;
		}
		
		// TODO: Do we need to verify the Facebook session here?
		
		/*
		$gid = $wgFbUserRightsFromGroup;
		// Connect to the API and get some info about the group
		try {
			$group = $facebook->api('/' . $gid);
		} catch (FacebookApiException $e) {
			error_log($e);
			return true;
		}
		$out .= '
			<table style="border-collapse: collapse;">
				<tr>
					<td>
						' . wfMsgWikiHtml( 'facebook-listusers-header',
						wfMsg( 'group-bureaucrat-member' ), wfMsg( 'group-sysop-member' ),
						"<a href=\"http://www.facebook.com/group.php?gid=$gid\">$group[name]</a>",
						"<a href=\"http://www.facebook.com/profile.php?id={$group['owner']['id']}\" " .
						"class=\"mw-userlink\">{$group['owner']['name']}</a>") . "
					</td>
					<td>
						<img src=\"https://graph.facebook.com/$gid/picture?type=large\" title=\"$group[name]\" alt=\"$group[name]\">
					</td>
				</tr>
			</table>";
		*/
		return true;
	}
	
	/**
	 * Removes Special:UserLogin and Special:CreateAccount from the list of
	 * special pages if $wgFbDisableLogin is set to true.
	 */
	static function SpecialPage_initList( &$aSpecialPages ) {
		global $wgFbDisableLogin;
		if ( !empty( $wgFbDisableLogin) ) {
			// U can't touch this
			$aSpecialPages['Userlogin'] = array(
				'SpecialRedirectToSpecial',
				'UserLogin',
				'Connect',
				false,
				array( 'returnto', 'returntoquery' ),
			);
			// Used in 1.12.x and above
			$aSpecialPages['CreateAccount'] = array(
				'SpecialRedirectToSpecial',
				'CreateAccount',
				'Connect',
			);
		}
		return true;
	}
	
	/**
	 * HACK: Please someone fix me or explain why this is necessary!
	 * 
	 * Unstub $wgUser to avoid race conditions and stop returning stupid false
	 * negatives!
	 * 
	 * This might be due to a bug in User::getRights() [called from
	 * User::isAllowed('read'), called from Title::userCanRead()], where mRights
	 * is retrieved from an uninitialized user. From my probing, it seems that
	 * the user is uninitialized with almost all members blank except for mFrom,
	 * equal to 'session'. The second time around, $user seems to point to the
	 * User object after being loaded from the session. After the user is loaded
	 * it has all the appropriate groups. However, before being loaded it seems
	 * that instead of being null, mRights is equal to the array
	 * (createaccount, createpage, createtalk, writeapi).
	 */
	static function userCan (&$title, &$user, $action, &$result) {
		// Unstub $wgUser (is there a more succinct way to do this?)
		$user->getId();
		return true;
	}
	
	/**
	 * We need to override the password checking so that Facebook users can
	 * reset their passwords and give themselves a valid password to log in
	 * without Facebook. This only works if the user specifies a blank password
	 * and hasn't already given themselves one.
	 *
	 * To that effect, you may want to modify the 'resetpass-wrong-oldpass' msg.
	 *
	 * Before version 1.14, MediaWiki used Special:Preferences to reset
	 * passwords instead of Special:ChangePassword, so this hook won't get
	 * called and Facebook users won't be able to give themselves a password
	 * unless they request one over email.
	 */
	public static function UserComparePasswords( $hash, $password, $userId, &$result ) {
		global $wgTitle;
		// Only allow the override if no password exists and a blank old password was specified
		if ( $hash != '' || $password != '' || !$userId ) {
			return true;
		}
		// Only check for password on Special:ChangePassword
		if ( !$wgTitle->isSpecial( 'Resetpass' ) ) {
			return true;
		}
		// Check to see if the MediaWiki user has connected via Facebook before
		// For a more strict check, we could check if the user is currently logged in to Facebook
		$user = User::newFromId( $userId );
		$fb_ids = FacebookDB::getFacebookIDs($user);
		if (count($fb_ids) == 0 || !$fb_ids[0]) {
			return true;
		}
		$result = true;
		return false; // to override internal check
	}
	
	/**
	 * Removes the 'createaccount' right from all users if $wgFbDisableLogin is
	 * enabled.
	 */
	// r270: fix for php 5.3 (cherry-picked from http://trac.wikia-code.com/changeset/24606)
	static function UserGetRights( /*&*/$user, &$aRights ) {
		global $wgFbDisableLogin;
		if ( !empty( $wgFbDisableLogin ) ) {
			// If you would like sysops to still be able to create accounts
			$whitelistSysops = false;
			if ($whitelistSysops && in_array( 'sysop', $user->getGroups() )) {
				return true;
			}
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
	 * If $wgFbDisableLogin is set, make sure the user gets logged out if their
	 * Facebook session is destroyed.
	 * 
	 * This hook was added in MediaWiki 1.14.
	 */
	static function UserLoadAfterLoadFromSession( $user ) {
		global $wgFbDisableLogin, $wgTitle;
		
		// Don't mess with authentication on Special:Connect
		if (!empty($wgFbDisableLogin) && $user->isLoggedIn() && !$wgTitle->isSpecial('Connect')) {
			global $facebook;
			
			$fbUser = new FacebookUser();
			
			// If possible, force a preemptive ping to Facebook's servers. Otherwise, we
			// must wait until the next page view to pick up the user's Facebook login status
			/*
			global $wgFbUserRightsFromGroup;
			if ( !empty( $wgFbUserRightsFromGroup ) ) {
				$rights = $fbUser->getGroupRights();
			}
			/**/
			$fbUser->isLoggedIn($ping = true);
			
			if ( !$fbUser->isLoggedIn() || $user->getId() != $fbUser->getMWUser()->getId() ) {
				$user->logout();
			}
		}
		return true;
	}
	
	/**
	 * Called when the user is logged out to log them out of Facebook as well.
	 *
	static function UserLogoutComplete( &$user, &$inject_html, $old_name ) {
		global $wgTitle, $facebook;
		if ( $wgTitle->isSpecial('Userlogout') && $facebook->getUser() ) {
			// Only log the user out if it's the right user
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getName() == $old_name ) {
				$facebook->destroySession();
			}
		}
		return true;
	}
	
	/**
	 * Create a disconnect button and other things in preferences.
	 */
	static function initPreferencesExtensionForm( $user, &$preferences ) {
		global $wgOut, $wgJsMimeType, $wgExtensionsPath, $wgStyleVersion, $wgBlankImgUrl;
		$wgOut->addScript("<script type=\"{$wgJsMimeType}\" src=\"{$wgExtensionsPath}/Facebook/prefs.js?{$wgStyleVersion}\"></script>\n");
		wfLoadExtensionMessages('Facebook');
		$prefsection = 'facebook-prefstext';
		
		$id = FacebookDB::getFacebookIDs($user, DB_MASTER);
		if( count($id) > 0 ) {
			$html = Xml::openElement("div",array("id" => "fbDisconnectLink" ));
				$html .= wfMsg('facebook-disconnect-link');
			$html .= Xml::closeElement( "div" );
			
			$html .= Xml::openElement("div",array("style" => "display:none","id" => "fbDisconnectProgress" ));
				$html .= wfMsg('facebook-disconnect-done');
				$html .= Xml::openElement("img",array("id" => "fbDisconnectProgressImg", 'src' => $wgBlankImgUrl, "class" => "sprite progress" ),true);
			$html .= Xml::closeElement( "div" );
			
			$html .= Xml::openElement("div",array("style" => "display:none","id" => "fbDisconnectDone" ));
				$html .= wfMsg('facebook-disconnect-info');
			$html .= Xml::closeElement( "div" );
			
			$preferences['facebook-prefstext'] = array(
				'label' => '',
				'type' => 'info',
				'section' => 'facebook-prefstext/facebook-event-prefstext',
			);
			
			$preferences['tog-facebook-push-allow-never'] = array(
				'name' => 'toggle',
				'label-message' => 'facebook-push-allow-never',
				'section' => 'facebook-prefstext/facebook-event-prefstext',
			);
			
			$preferences['facebook-connect'] = array(
				'help' => $html,
				'label' => '',
				'type' => 'info',
				'section' => 'facebook-prefstext/facebook-event-prefstext',
			);
			
		} else {
			// User is a MediaWiki user but isn't connected yet
			// Display a message and button to connect
			$loginButton = '<fb:login-button id="fbPrefsConnect" ' .
					FacebookInit::getPermissionsAttribute() . '></fb:login-button>';
			$html = wfMsg('facebook-convert') . '<br/>' . $loginButton;
			$html .= "<!-- Convert button -->\n";
			$preferences['facebook-disconnect'] = array(
				'help' => $html,
				'label' => '',
				'type' => 'info',
				'section' => 'facebook-prefstext/facebook-event-prefstext',
			);
		}
		return true;
	}
	
	/**
	 * Add Facebook HTML to AJAX script.
	 */
	public static function afterAjaxLoginHTML( &$html ) {
		$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
		wfLoadExtensionMessages('Facebook');
		if ( !LoginForm::getLoginToken() ) {
			LoginForm::setLoginToken();
		}
		$tmpl->set( 'loginToken', LoginForm::getLoginToken() );
		$tmpl->set( 'fbButtton', FacebookInit::getFBButton( 'sendToConnectOnLoginForSpecificForm();', 'fbPrefsConnect' ) );
		$html = $tmpl->execute( 'ajaxLoginMerge' );
		return true;
	}
	
	// TODO
	public static function SkinTemplatePageBeforeUserMsg(&$msg) {
		global $wgRequest, $wgUser, $wgServer, $facebook;
		wfLoadExtensionMessages('Facebook');
		$pref = Title::newFromText('Preferences', NS_SPECIAL);
		if ($wgRequest->getVal('fbconnected', '') == 1) {
			$id = FacebookDB::getFacebookIDs($wgUser, DB_MASTER);
			if( count($id) > 0 ) {
				// TODO
				$msg =  Xml::element("img", array("id" => "fbMsgImage", "src" => $wgServer.'/skins/common/fbconnect/fbiconbig.png' ));
				$msg .= "<p>".wfMsg('facebook-connect-msg', array("$1" => $pref->getFullUrl() ))."</p>";
			}
		}
		// TODO
		if ($wgRequest->getVal('fbconnected', '') == 2) {
			if( strlen($facebook->getUser()) < 1 ) {
				$msg =  Xml::element("img", array("id" => "fbMsgImage", "src" => $wgServer.'/skins/common/fbconnect/fbiconbig.png' ));
				$msg .= "<p>".wfMsgExt('facebook-connect-error-msg', 'parse', array("$1" => $pref->getFullUrl() ))."</p>";
			}
		}
		return true;
	}
}
