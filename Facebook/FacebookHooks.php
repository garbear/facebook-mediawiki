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
			$fb_id = FacebookDB::getFacebookIDs($user->getId());
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
		global $wgFbUserRightsFromGroup;
		
		// Probably a redundant check, but with PHP you can never be too sure...
		if (empty($wgFbUserRightsFromGroup)) {
			// No group to pull rights from, so the user can't be a member
			$result = false;
			return true;
		}
		$types = array(
			APCOND_FB_INGROUP   => 'member',
			APCOND_FB_ISOFFICER => 'officer',
			APCOND_FB_ISADMIN   => 'admin'
		);
		$type = $types[$cond_type];
		switch( $type ) {
			case 'member':
			case 'officer':
			case 'admin':
				global $facebook;
				// Connect to the Facebook API and ask if the user is in the group
				$rights = $facebook->getGroupRights($user);
				$result = $rights[$type];
		}
		return true;
	}
	
	/**
	 * Injects some important CSS and Javascript into the <head> of the page.
	 */
	public static function BeforePageDisplay( &$out, &$sk ) {
		global $wgUser, $wgVersion, $wgFbLogo, $wgFbScript, $wgFbExtensionScript,
		       $wgFbScriptEnableLocales, $wgJsMimeType, $wgStyleVersion;
		
		// Wikiaphone skin for mobile device doesn't need JS or CSS additions 
		if ( get_class( $wgUser->getSkin() ) === 'SkinWikiaphone' )
			return true;
		
		// If the user's language is different from the default language, use the correctly localized Facebook code.
		// NOTE: Can't use wgLanguageCode here because the same Facebook config can run for many wgLanguageCode's on one site (such as Wikia).
		if ( $wgFbScriptEnableLocales ) {
			global $wgFbScriptLangCode, $wgLang;
			wfProfileIn( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
			if ( $wgLang->getCode() !== $wgFbScriptLangCode ) {
				// Attempt to find a matching facebook locale.
				$defaultLocale = FacebookLanguage::getFbLocaleForLangCode( $wgFbScriptLangCode );
				$locale = FacebookLanguage::getFbLocaleForLangCode( $wgLang->getCode() );
				if ( $defaultLocale != $locale ) {
					global $wgFbScriptByLocale;
					$wgFbScript = str_replace( FACEBOOK_LOCALE, $locale, $wgFbScriptByLocale );
				}
			}
			wfProfileOut( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
		}
		
		// Asynchronously load the Facebook Connect JavaScript SDK before the page's content
		global $wgNoExternals;
		if ( !empty($wgFbScript) && empty($wgNoExternals) ) {
			$out->prependHTML('
				<div id="fb-root"></div>
				<script type="text/javascript">
					(function(){var e=document.createElement("script");e.type="' .
					$wgJsMimeType . '";e.src="' . $wgFbScript .
					'";e.async=true;document.getElementById("fb-root").appendChild(e)})();
				</script>' . "\n"
			);
		}
		
		// Inserts list of global JavaScript variables if necessary
		if (self::MGVS_hack( $mgvs_script )) {
			$out->addInlineScript( $mgvs_script );
		}
		
		// Add a Facebook logo to the class .mw-fblink
		$style = empty($wgFbLogo) ? '' : <<<STYLE
		/* Add a pretty logo to Facebook links */
		.mw-fblink {
			background: url($wgFbLogo) top left no-repeat !important;
			padding-left: 17px !important;
		}
STYLE;
		
		// Things get a little simpler in 1.16...
		if ( version_compare( $wgVersion, '1.16', '>=' ) ) {
			// Add a pretty Facebook logo if $wgFbLogo is set
			if ( !empty( $wgFbLogo) ) {
				$out->addInlineStyle( $style );
			}
			// Include the common jQuery library (alias defaults to $j instead of $)
			$out->includeJQuery();
			// Add the script file specified by $url
			if ( !empty( $wgFbExtensionScript ) ) {
				$out->addScriptFile( $wgFbExtensionScript );
			}
		} else {
			// Add a pretty Facebook logo if $wgFbLogo is set
			if ( !empty( $wgFbLogo) ) {
				$out->addScript( '<style type="text/css">' . $style . '</style>' );
			}
			// Include the most recent 1.4 version (currently 1.4.4)
			$out->addScriptFile( 'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js' );
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
		$tables = array( 'user_fbconnect', 'fbconnect_event_stats', 'fbconnect_event_show' );
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
	 * fbSession     Assist the JavaScript SDK with loading the session
	 * fbUseMarkup   Should XFBML tags be rendered (see $wgFbUseMarkup in config.php)
	 * fbLogo        Facebook logo (see $wgFbLogo in config.php)
	 * fbLogoutURL   The URL to be redirected to on a disconnect
	 * 
	 * This hook was added in MediaWiki version 1.14. See:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 * If we are not at revision 38397 or later, this function is called from BeforePageDisplay
	 * to retain backward compatability.
	 */
	public static function MakeGlobalVariablesScript( &$vars ) {
		global $wgFbAppId, $facebook, $wgFbUseMarkup, $wgFbLogo, $wgTitle, $wgRequest, $wgStyleVersion;
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
		// TODO: For debugging purposes -- remove in production
		$vars['fbSession']   = $facebook->getSession();
		$vars['fbUseMarkup'] = $wgFbUseMarkup;
		$vars['fbLogo']      = $wgFbLogo ? true : false;
		$vars['fbLogoutURL'] = Skin::makeSpecialUrl( 'Userlogout',
			$wgTitle->isSpecial('Preferences') ? '' : 'returnto=' . $wgTitle->getPrefixedURL() );
		
		$vals = $wgRequest->getValues();
		if( !empty( $vals ) && !empty( $vals['title'] ) ) {
			$vars['fbReturnToTitle'] = $vals['title'];
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
	
	
	private static function showButton( $which ) {
		global $wgUser, $wgFbShowPersonalUrls, $wgFbHidePersonalUrlsBySkin;
		// If the button isn't marked to be shown in the first place
		if (!in_array($which, $wgFbShowPersonalUrls)) {
			return false;
		}
		$skinName = get_class($wgUser->getSkin());
		// If no blacklist rules exist for the skin
		if (!array_key_exists($skinName, $wgFbHidePersonalUrlsBySkin)) {
			return true;
		}
		// If the value is a string, it's a simple comparison
		if (is_string($wgFbHidePersonalUrlsBySkin[$skinName])) {
			return $wgFbHidePersonalUrlsBySkin[$skinName] != '*' &&
			       $wgFbHidePersonalUrlsBySkin[$skinName] != $which;
		} else {
			return !in_array($which, $wgFbHidePersonalUrlsBySkin[$skinName]) &&
			       !in_array('*', $wgFbHidePersonalUrlsBySkin[$skinName]);
		}
	}
	
	/**
	 * Modify the user's persinal toolbar (in the upper right).
	 * 
	 * TODO: Better 'returnto' code
	 */
	public static function PersonalUrls( &$personal_urls, &$wgTitle ) {
		global $wgUser, $wgFbUseRealName, $wgFbDisableLogin, $facebook;
		
		wfLoadExtensionMessages('Facebook');
		
		// Always false, as 'alt-talk' isn't a valid option currently
		if (self::showButton( 'alt-talk' ) &&
				array_key_exists('mytalk', $personal_urls)) {
			unset($personal_urls['mytalk']);
		}
		
		// If the user is logged in and connected
		if ( $wgUser->isLoggedIn() && $facebook->getSession() &&
				count( FacebookDB::getFacebookIDs($wgUser) ) > 0 ) {
			if ( !empty( $wgFbUseRealName ) ) {
				// Start with the real name in the database
				$name = $wgUser->getRealName();
				if (!$name || $name == '') {
					// Ask Facebook for the real name
					try {
						// This might fail if we load a stale session from cookies
						$fbUser = $facebook->api('/me');
						$name = $fbUser['name'];
					} catch (FacebookApiException $e) {
						error_log($e);
					}
				}
				// Make sure we were able to get a name from the database or Facebook
				if ($name && $name != '') {
					$personal_urls['userpage']['text'] = $name;
				}
			}
			
			if (self::showButton( 'logout' )) {
				// Replace logout link with a button to disconnect from Facebook Connect
				unset( $personal_urls['logout'] );
				$personal_urls['fblogout'] = array(
					'text'   => wfMsg( 'facebook-logout' ),
					'href'   => '#',
					'active' => false,
				);
				/*
				$html = Xml::openElement('span', array('id' => 'fbuser' ));
					$html .= Xml::openElement('a', array('href' => $personal_urls['userpage']['href'], 'class' => 'fb_button fb_button_small fb_usermenu_button' ));
					$html .= Xml::closeElement( 'a' );
				$html .= Xml::closeElement( 'span' );
				$personal_urls['fbuser']['html'] = $html;
				*/
			}
			
			/*
			 * Personal URLs option: link_back_to_facebook
			 */
			if (self::showButton( 'link' )) {
				try {
					$fbUser = $facebook->api('/me');
					$link = $fbUser['link'];
				} catch (FacebookApiException $e) {
					$link = 'http://www.facebook.com/profile.php?id=' .
						    $facebook->getUser();
				}
				$personal_urls['fblink'] = array(
					'text'   => wfMsg( 'facebook-link' ),
					'href'   => $link,
					'active' => false
				);
			}
		}
		// User is logged in but not Connected
		else if ($wgUser->isLoggedIn()) {
			if (self::showButton( 'convert' )) {
				$personal_urls['fbconvert'] = array(
					'text'   => wfMsg( 'facebook-convert' ),
					'href'   => SpecialConnect::getTitleFor('Connect', 'Convert')->getLocalURL(
					                          'returnto=' . $wgTitle->getPrefixedURL()),
					'active' => $wgTitle->isSpecial( 'Connect' )
				);
			}
		}
		// User is not logged in
		else {
			if (self::showButton( 'connect' ) || self::showButton( 'connect-simple' )) {
				// Add an option to connect via Facebook Connect
				$personal_urls['facebook'] = array(
					'text'   => wfMsg( 'facebook-connect' ),
					'class' => 'fb_button fb_button_small',
					'href'   => '#', # SpecialPage::getTitleFor('Connect')->getLocalUrl('returnto=' . $wgTitle->getPrefixedURL()),
					'active' => $wgTitle->isSpecial('Connect'),
				);
			}
			
			if (self::showButton( 'connect-simple' )) {
				$html = Xml::openElement('span', array('id' => 'facebook' ));
					$html .= Xml::openElement('a', array('href' => '#', 'class' => 'fb_button fb_button_small' ));
						$html .= Xml::openElement('span', array('class' => 'fb_button_text' ));
							$html .= wfMsg( 'facebook-connect-simple' );
						$html .= Xml::closeElement( 'span' );
					$html .= Xml::closeElement( 'a' );
				$html .= Xml::closeElement( 'span' );
				$personal_urls['facebook']['html'] = $html;
			}
			
			if ( !empty( $wgFbDisableLogin ) ) {
				// Remove other personal toolbar links
				foreach (array('login', 'anonlogin') as $k) {
					if (array_key_exists($k, $personal_urls)) {
						unset($personal_urls[$k]);
					}
				}
			}
		}
		return true;
	}
	
	public static function GetPreferences( $user, &$preferences ) {
		$email = FacebookUser::getCleanEmail($preferences['emailaddress']['default']);
		if ($email != $preferences['emailaddress']['default']) {
			// User is using a Facebook proxy email address
			#$preferences['emailaddress']['default'] = $email;
			$preferences['enotifrevealaddr']['disabled'] = true;
			// TODO: Inject some JS into the page to take of changing email addresses
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
	static function SpecialListusersHeaderForm( /*&*/$pager, &$out ) {
		global $wgFbUserRightsFromGroup, $facebook;
		
		if ( empty($wgFbUserRightsFromGroup) ) {
			return true;
		}
		
		// TODO: Do we need to verify the Facebook session here?
		
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
	 * Removes the 'createaccount' right from all users if $wgFbDisableLogin is
	 * enabled.
	 */
	// r270: fix for php 5.3 (cherry picked from http://trac.wikia-code.com/changeset/24606)
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
	 * If the user isn't logged in, try to auto-authenticate via Facebook
	 * Connect. The Single Sign On magic of Facebook happens in this function.
	 */
	static function UserLoadFromSession( $user, &$result ) {
		global $facebook, $wgCookiePrefix, $wgTitle, $wgOut, $wgUser;
		
		// Check to see if the user can be logged in from Facebook
		$fbId = $facebook->getSession() ? $facebook->getUser() : 0;
		// Check to see if the user can be loaded from the session
		$localId = isset($_COOKIE["{$wgCookiePrefix}UserID"]) ?
				intval($_COOKIE["{$wgCookiePrefix}UserID"]) :
				(isset($_SESSION['wsUserID']) ? $_SESSION['wsUserID'] : 0);
		
		// Case: Not logged into Facebook, but logged into the wiki
		/*
		if (!$fbId && $localId) {
			$mwUser = User::newFromId($localId);
			// If the user was Connected, the JS should have logged them out...
			// TODO: test to see if they logged in normally (with a password)
			#if (FacebookDB::userLoggedInWithPassword($mwUser)) return true;
			if (count(FacebookDB::getFacebookIDs($mwUser))) {
				// Oh well, they shouldn't be here anyways; silently log them out
				$mwUser->logout();
				// Defaults have just been loaded, so save some time
				$result = false;
			}
		}
		// Case: Logged into Facebook, not logged into the wiki
		else /**/ if ($fbId && !$localId) {
			// Look up the MW ID of the Facebook user
			$mwUser = FacebookDB::getUser($fbId);
			$id = $mwUser ? $mwUser->getId() : 0;
			// If the user doesn't exist, ask them to name their new account
			if (!$id) {
				// TODO: $wgTitle was empty for some strange reason...
				if (!empty( $wgTitle )) {
					$returnto = $wgTitle->isSpecial('Userlogout') || $wgTitle->isSpecial('Connect') ?
								'' : 'returnto=' . $wgTitle->getPrefixedURL();
				} else {
					$returnto = '';
				}
				// Don't redirect if we're on certain special pages
				if ($returnto != '') {
					// Redirect to Special:Connect so the Facebook user can choose a nickname
					$wgOut->redirect($wgUser->getSkin()->makeSpecialUrl('Connect', $returnto));
				}
			} else {
				// TODO: To complete the SSO experience, this should log the user on
				/*
				// Load the user from their ID
				$user->mId = $id;
				$user->mFrom = 'id';
				$user->load();
				// Update user's info from Facebook
				$fbUser = new FacebookUser($mwUser);
				$fbUser->updateFromFacebook();
				// Authentification okay, no need to continue with User::loadFromSession()
				$result = true;
				/**/
			}
		}
		// Case: Not logged into Facebook or the wiki
		// Case: Logged into Facebook, logged into the wiki
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
				'default' => 1,
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
			               FacebookInit::getPermissionsAttribute() .
			               FacebookInit::getOnLoginAttribute() . '></fb:login-button>';
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
