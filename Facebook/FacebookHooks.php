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
 * UserLogin --> Connect
 *
 * This faux special page redirects the user to Special:Connect if
 * $wgFbDisableLogin is set to true.
 */
class SpecialUserLoginToConnect extends SpecialRedirectToSpecial {
	function __construct(){
		parent::__construct( 'UserLogin', 'Connect', false,
				array( 'returnto', 'returntoquery' ) );
	}
}

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
		if ( $nt instanceof Title && $nt->getNamespace() == NS_USER &&
				strpos( $nt->getText(), '/' ) === false ) {
			$user = User::newFromName( $nt->getText() );
			if ( $user instanceof User && $user->getID() ) {
				$fb_ids = FacebookDB::getFacebookIDs( $user->getId() );
				if ( count($fb_ids) ) {
					$fb_id = $fb_ids[0];
					// TODO: Something with the Facebook ID stored in $fb_id
				}
			}
		}
		return true;
	}
	
	/**
	 * Checks the autopromote condition for a user.
	 */
	static function AutopromoteCondition( $cond_type, $args, $user, &$result ) {
		global $wgFbUserRightsFromGroup;
		
		switch ( $cond_type ) {
			case APCOND_FB_USER:
				$ids = FacebookDB::getFacebookIDs($user);
				$result = count( $ids );
				break;
			case APCOND_FB_INGROUP:
				$type = 'member';
			case APCOND_FB_ISADMIN:
				if (empty( $type ))
					$type = 'admin';
				// If there's no group to pull rights from, the user can't be a member
				$result = false;
				if ( !empty( $wgFbUserRightsFromGroup ) ) {
					$ids = FacebookDB::getFacebookIDs($user);
					if ( count( $ids ) ) {
						// Assume $user doesn't have more than one Facebook ID
						$fbUser = new FacebookUser( $ids[0] );
						$rights = $fbUser->getGroupRights();
						$result = $rights[$type];
					}
				}
			// end switch
		}
		
		return true;
	}
	
	/**
	 * Injects some important CSS and Javascript into the <head> of the page.
	 */
	public static function BeforePageDisplay( &$out, &$skin ) {
		global $wgFbExtScript, $wgVersion, $wgJsMimeType, $wgStyleVersion;
		
		// Wikiaphone skin for mobile device doesn't need JS or CSS additions 
		if ( get_class( $skin ) === 'SkinWikiaphone' )
			return true;
		
		// Add a Facebook logo to the class .mw-fblink
		global $wgFbLogo;
		$style = empty( $wgFbLogo ) ? '' : <<<STYLE
.mw-facebook-logo {
	background-image: url($wgFbLogo) !important;
	background-repeat: no-repeat !important;
	background-position: left center !important;
	padding-left: 19px !important;
}

STYLE;
		$style .= '.fbInitialHidden {display:none;}'; // Forms on Special:Connect
		
		if ( version_compare( $wgVersion, '1.17', '>=' ) ) {
			// Throw in a <div id="fb-root"> tag here.
			// Facebook documentation says to include this <div> before loading
			// the library. Well, I went through the source code to find out why.
			//     <https://github.com/facebook/facebook-js-sdk>
			// This <div> is indeed used by the library, but only for some kind
			// of dialog (which means that the <div> must be rendered before the
			// dialog is shown, an almost certain scenario). In all other cases,
			// the library creates its own <div> 10000px above the top of the page.
			$out->prependHTML("\n<div id=\"fb-root\"></div>\n");
			
			$out->addInlineStyle( $style );
			
			// Use the ResourceLoader to add the JS SDK 
			$out->addModules( 'ext.facebook.sdk' );
		} else {
			// Asynchronously load the Facebook JavaScript SDK before the page's content
			// See <https://developers.facebook.com/docs/reference/javascript>
			global $wgNoExternals;
			if ( empty( $wgNoExternals ) ) {
				$out->prependHTML('
<div id="fb-root"></div>
<script type="' . $wgJsMimeType . '">
(function(d){var js;js=d.createElement("script");js.async=true;js.type="' .
$wgJsMimeType . '";js.src="' . self::getFbScript() .
'";d.getElementsByTagName("head")[0].appendChild(js);}(document));
</script>' . "\n"
				);
			}
			
			// Include special JavaScript on Special:Connect/Debug
			$isSpecialConnectDebug = false;
			$title = $skin->getTitle();
			global $wgFbAllowDebug;
			if (!empty($wgFbAllowDebug) && $title instanceof Title && $title->getNamespace() == NS_SPECIAL) {
				list( $name, $subpage ) = SpecialPageFactory::resolveAlias( $title->getDBkey() );
				if ( $name == 'Connect' && $subpage == 'Debug' ) {
					$isSpecialConnectDebug = true;
					// Use $wgFbExtScript as a base
					$applicationScript = str_replace('ext.facebook.js', 'ext.facebook.application.js',
							$wgFbExtScript);
				}
			}
			
			if ( version_compare( $wgVersion, '1.16', '>=' ) ) {
				// Include the common jQuery library
				$out->includeJQuery();
				$out->addInlineStyle( $style );
				$out->addScriptFile( $wgFbExtScript );
				if ( $isSpecialConnectDebug ) {
					$out->addScriptFile( $applicationScript );
				}
			} else {
				$out->addScript( '<style type="text/css">' . $style . '</style>' );
				// Include the most recent 1.7 version
				$out->addScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js');
				// Add the script file specified by $url
				$out->addScript( "<script type=\"$wgJsMimeType\" " .
						"src=\"$wgFbExtScript?$wgStyleVersion\"></script>\n" );
				if ( $isSpecialConnectDebug ) {
					$out->addScript( "<script type=\"$wgJsMimeType\" " .
						"src=\"$applicationScript?$wgStyleVersion\"></script>\n" );
				}
				// Inserts list of global JavaScript variables if necessary
				if (self::MGVS_hack( $mgvs_script )) {
					$out->addInlineScript( $mgvs_script );
				}
			}
		}
		
		return true;
	} // BeforePageDisplay hook
	
	/**
	 * Returns the URL to the Facebook JavaScript SDK. If $wgFbScript contains
	 * a %LOCALE% token, it will be replaced with the current user's language.
	 * Additionally, the URL is appended with a fragment containing the app ID
	 * and an option to render XFBML tags.
	 */
	private static function getFbScript() {
		global $wgFbScript, $wgLang, $wgFbAppId, $wgFbSocialPlugins;
		
		static $fbScript = ''; // In MW <= 1.17 this runs from two different hooks
		if ( $fbScript === '' ) {
			$fbScript = $wgFbScript;
			// Check to see if we should localize the JS SDK
			if (strpos( $fbScript, FACEBOOK_LOCALE ) !== false) {
				wfProfileIn( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
				// NOTE: Can't use $wgLanguageCode here because the same Facebook config
				// can run for many $wgLanguageCode's on one site (such as Wikia).
				$locale = FacebookLanguage::getFbLocaleForLangCode( $wgLang->getCode() );
				$fbScript = str_replace( FACEBOOK_LOCALE, $locale, $fbScript );
				wfProfileOut( __METHOD__ . '::fb-locale-by-mediawiki-lang' );
			}
			
			// Give Facebook some hints. Commented out because they don't affect anything...
			#$fbScript .= '#appId=' . $wgFbAppId .
			#             '&xfbml=' . (!empty( $wgFbSocialPlugins ) ? '1' : '0');
		}
		return $fbScript;
	}
	
	/**
	 * Register the <opengraph> tag with the parser.
	 */
	public static function LanguageGetMagic( array &$magicWords, $langCode ) {
		$tag = FacebookOpenGraph::GetTag( $langCode );
		if ( $tag != '' ) {
			// 0 == not case sensitive
			$magicWords[$tag] = array( 0, $tag );
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
	 * The $updater parameter was added in r71140 (after 1.16)
	 * <http://svn.wikimedia.org/viewvc/mediawiki?view=revision&revision=71140>
	 */
	static function LoadExtensionSchemaUpdates( $updater = null ) {
		global $wgSharedDB, $wgDBname, $wgDBtype;
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
			
			// GitHub pull request #4, https://github.com/garbear/facebook-mediawiki/pull/4
			// Thanks to Varnent for triaging the bug and isolating the offending code.
			// No thanks to $wgDBprefix.
			/*
			global $wgDBprefix;
			if ( $wgDBprefix ) {
				$table = $wgDBprefix . $table;
			}
			*/
			
			// Location of the table schema file
			$schema = "$sql/$table.$ext";
			// If we're using the new version of the LoadExtensionSchemaUpdates hook
			if ( $updater !== null ) {
				// >= 1.17 support
				$updater->addExtensionUpdate( array( 'addTable', $table, $schema, true ) );
			} else {
				// <= 1.16 support
				global $wgExtNewTables;
				$wgExtNewTables[] = array( $table, $schema );
			}
		}
		return true;
	} // LoadExtensionSchemaUpdates hook
	
	/**
	 * Adds several Facebook variables to the page:
	 */
	public static function ResourceLoaderGetConfigVars( &$vars ) {
		global $wgRequest, $wgVersion, $wgFbAppId, $wgFbSocialPlugins, $wgFbStreamlineLogin, $wgUser;
		/*
		// Disabled (ext.facebook.js still uses wgPageName, but not wgPageQuery)
		if (!isset($vars['wgPageQuery'])) {
			$query = $wgRequest->getValues();
			if (isset($query['title'])) {
				unset($query['title']);
			}
			$vars['wgPageQuery'] = wfUrlencode( wfArrayToCGI( $query ) );
		}
		*/
		$vars['fbScript']       = self::getFbScript();
		$vars['fbAppId']        = $wgFbAppId;
		$vars['fbUseXFBML']     = $wgFbSocialPlugins;
		$vars['fbUseAjax']      = $wgFbStreamlineLogin;
		if ( $wgUser->isLoggedIn() ) {
			global $facebook;
			$ids = FacebookDB::getFacebookIDs($wgUser);
			// If the user is logged in, let the JavaScript code know who the
			// account belongs to. The primary reason for this is so that if a
			// user logs in to Facebook with a different account, we can show
			// the "facebooklogoutandcontinue" form.
			// 
			// Previously, if the user was logged in and had a valid Facebook
			// session, we would skip this step with the mentality that it was
			// unnecessary as the JavaScript code would obviously already know
			// the Facebook ID. However, this created a problem that can be
			// reproduced as follows:
			//    1. Log in to MediaWiki with a valid Facebook session
			//    2. In another tab, log out of Facebook and then log in again
			//       as the same user, creating a different session
			//    3. Now reload the MediaWiki page. The server will see a valid
			//       session and skip the fbId variable. However, the client
			//       will fire the Facebook Login event because a new session
			//       was picked up, even though the user was also logged in the
			//       last time the page loaded.
			// Now, because the JavaScript can't find the fbId variable, it
			// assumes that the MediaWiki account isn't connected to a Facebook
			// account and shows the "facebookmergeaccount" form. However, when
			// this form is retrieved, the new session is synchronized to the
			// server and the AJAX request is invalid because you can't merge
			// an account that is already connected to a Facebook user.
			// 
			// In and of itself, we skip this extra check and always include
			// the fbId variable.
			// 
			// There must be a prize for finding bugs like this one. Because
			// seriously, I deserve it.
			if ( count( $ids ) ) {
				$vars['fbId'] = strval( $ids[0] );
			}
		}
		$scope = FacebookAPI::getPermissions();
		if ( !empty( $scope ) ) {
			$vars['fbScope'] = $scope;
		}
		return true;
	}
	
	/**
	 * Backwards compatibility for MediaWiki < 1.17.
	 * 
	 * This hook was added in MediaWiki 1.14. If we are not 1.14 or later, this
	 * function is called from BeforePageDisplay via MGVS_hack() to retain
	 * backward compatibility.
	 * 
	 * And then this hook was deprecated in 1.17, so it calls the new hook.
	 */
	public static function MakeGlobalVariablesScript( &$vars ) {
		global $wgVersion, $wgUser;
		if ( version_compare( $wgVersion, '1.17', '<' ) ) {
			self::ResourceLoaderGetConfigVars( $vars );
			unset( $vars['fbScript'] ); // Made obsolete by ResourceLoader
		}
		
		// We want fbAppAccessToken to be set here instead of loaded through
		// ResourceLoader. I forget why this is the case, unfortunately.
		global $wgFbAllowDebug;
		if ( !empty( $wgFbAllowDebug ) ) {
			$title = $wgUser->getSkin()->getTitle();
			if ( $title instanceof Title &&
					SpecialPage::getTitleFor('Connect', 'Debug')->equals($title) ) {
				$app = new FacebookApplication();
				if ( $app->canEdit() ) {
					global $wgFbAppId, $wgFbSecret;
					$vars['fbAppAccessToken'] = $wgFbAppId . '|' . $wgFbSecret;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Hack: Run MakeGlobalVariablesScript for backwards compatibility.
	 * The MakeGlobalVariablesScript hook was added to MediaWiki 1.14 in revision 38397:
	 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/Skin.php?view=log&pathrev=38397
	 */
	private static function MGVS_hack( &$script ) {
		global $wgVersion;
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
	 * Add the Open Graph meta tags to the current page.
	 * 
	 * This hook is used instead of BeforePageDisplay, (I quote from mediawiki.org,)
	 * "to make it easier on parser caching."
	 */
	public static function ParserAfterTidy(&$parser, &$text) {
		global $wgFbOpenGraph, $wgOut;
		if ( !empty( $wgFbOpenGraph ) ) {
			
			$parser->disableCache();
			
			$object = FacebookOpenGraph::newObjectFromTitle( $parser->getTitle() );
			if ( $object ) {
				if ($object instanceof OpenGraphArticleObject && $object->needsDescription()) {
					$object->setDescriptionFromText( $text );
				}
				foreach ( $object->getProperties() as $property => $content ) {
					$parser->mOutput->addHeadItem("<meta property=\"$property\" content=\"$content\" />\n", $property);
				}
			}
		}
		return true;
	}
	
	/**
	 * Installs a parser hook for every tag reported by FacebookXFBML::availableTags().
	 * Accomplishes this by asking FacebookXFBML to create a hook function that then
	 * redirects to FacebookXFBML::parserHook().
	 *
	 * Secondly, this function installs a parser hook for our <opengraph> tag.
	 */
	public static function ParserFirstCallInit( &$parser ) {
		// XFBML tags (for example, <fb:login-button>)
		$pHooks = FacebookXFBML::availableTags();
		foreach( $pHooks as $tag ) {
			$parser->setHook( $tag, FacebookXFBML::createParserHook( $tag ) );
		}
		// Open Graph tag (for example, <opengraph type="article">)
		$tag = FacebookOpenGraph::GetTag();
		if ( $tag != '' ) {
			$parser->setHook( $tag, 'FacebookOpenGraph::parserHook' );
		}
		return true;
	}
	
	/**
	 * Simple boolean test of whether the "Log in with Facebook" button should
	 * be shown. This test is isolated in its own function so that the
	 * PersonalUrls and MakeGlobalVariablesScript hooks can both use it. The
	 * rationality behind this is that we only needs to pass the list of
	 * Facebook permissions via JavaScript if the Login button is actually shown.
	 * 
	 * For now, we always pass the permissions in the fbScope variable.
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
	public static function PersonalUrls( &$personal_urls, &$title ) {
		global $wgUser, $wgFbUseRealName, $wgFbDisableLogin;
		//wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
		
		// Transmogrify usernames into real names
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
		
		// Render a Log in with Facebook button
		if ( self::showLogin() ) {
			if ( isset( $personal_urls['logout'] ) ) {
				// Place the convert button before the logout link
				$logout_item = end( $personal_urls );
				$personal_urls = array_slice( $personal_urls, 0, -1, true );
			}
			$personal_urls['facebook'] = array(
				'text'   => wfMsg( 'facebook-connect' ),
				'href'   => '#', # SpecialPage::getTitleFor('Connect')->getLocalUrl('returnto=' . $title->getPrefixedURL()),
				'class'  => 'mw-facebook-logo',
				'active' => $title->isSpecial( 'Connect' ),
			);
			if ( !empty( $logout_item ) ) {
				$personal_urls['logout'] = $logout_item;
			}
		}
		
		// Remove other personal toolbar links 
		if ( !$wgUser->isLoggedIn() && !empty( $wgFbDisableLogin ) ) {
			foreach (array('login', 'anonlogin') as $k) {
				if (array_key_exists($k, $personal_urls)) {
					unset($personal_urls[$k]);
				}
			}
		}
		
		return true;
	} // PersonalUrls hook
	
	/**
	 * Modify the preferences form. At the moment, we simply turn the user name
	 * into a link to the user's facebook profile.
	 * 
	 * TODO: This hook no longer seems to work...
	 *
	public static function RenderPreferencesForm( $form, $output ) {
		global $facebook, $wgUser;
		
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
		return true;
	} // RenderPreferencesForm hook
	
	/**
	 * Add several meta property namespace attributes to the <head> tag.
	 * 
	 * This hook follows the steps outlined in the Open Graph Beta tutorial:
	 * https://developers.facebook.com/docs/beta/opengraph/tutorial/
	 */
	static function SkinTemplateOutputPageBeforeExec(&$skin, &$tpl) {
		global $wgFbOpenGraph, $wgFbNamespace, $wgFbOpenGraphRegisteredObjects;
		// If there are no Open Graph tags, we can skip this step
		if ( !empty( $wgFbOpenGraph ) ) {
			$head = '<head prefix="og: http://ogp.me/ns#';
			// I think this is for the fb:app_id and fp:page_id meta tags (see link),
			// but your guess is as good as mine
			// https://developers.facebook.com/docs/opengraph/objects/builtin/
			$head .= ' fb: http://ogp.me/ns/fb#';
			if ( FacebookAPI::isNamespaceSetup() ) {
				$head .= " $wgFbNamespace: http://ogp.me/ns/fb/$wgFbNamespace#";
			}
			// Use article prefix for built-in type (when $wgFbOpenGraphRegisteredObjects['article'] is empty)
			if (empty($wgFbOpenGraphRegisteredObjects) || empty($wgFbOpenGraphRegisteredObjects['article'])) {
				$head .= ' article: http://ogp.me/ns/article#';
			}
			$head .= '">';
			
			$headElement = $tpl->data['headelement'];
			$headElement = str_replace('<head>', $head, $headElement);
			$tpl->set( 'headelement', $headElement );
		}
		return true;
	}
	
	/**
	 * This hook is unused.
	 *
	public static function SkinTemplatePageBeforeUserMsg(&$msg) {
		global $wgRequest, $wgUser, $wgServer, $facebook;
		//wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
		$pref = Title::newFromText('Preferences', NS_SPECIAL);
		if ($wgRequest->getVal('fbconnected', '') == 1) {
			$id = FacebookDB::getFacebookIDs($wgUser, DB_MASTER);
			if( count($id) > 0 ) {
				$msg =  Xml::element("img", array("id" => "fbMsgImage", "src" => $wgServer.'/skins/common/fbconnect/fbiconbig.png' ));
				$msg .= "<p>".wfMsg('facebook-connect-msg', array("$1" => $pref->getFullUrl() ))."</p>";
			}
		}
		if ($wgRequest->getVal('fbconnected', '') == 2) {
			if( strlen($facebook->getUser()) < 1 ) {
				$msg =  Xml::element("img", array("id" => "fbMsgImage", "src" => $wgServer.'/skins/common/fbconnect/fbiconbig.png' ));
				$msg .= "<p>".wfMsgExt('facebook-connect-error-msg', 'parse', array("$1" => $pref->getFullUrl() ))."</p>";
			}
		}
		return true;
	}
	
	/**
	 * Show the real name of users on Special:ListUsers if $wgFbUseRealName is
	 * true. The local database is queried first for two reasons: performance
	 * (each Facebook lookup is done separately) and an OAuth access_token may
	 * be required to query the Facebook user's name (our access token is
	 * usually only valid for the currently-logged-in user).
	 */
	static function SpecialListusersFormatRow( &$item, $row ) {
		global $wgFbUseRealName, $wgFbLogo;
		if ( !empty( $wgFbUseRealName ) ) {
			$user = User::newFromId( $row->user_id );
			$fb_ids = FacebookDB::getFacebookIDs( $user );
			if ( count( $fb_ids ) ) {
				// Start with the real name in the database
				$name = $user->getRealName();
				if ( empty( $name ) ) {
					// Ask Facebook for the real name
					$fbUser = new FacebookUser($fb_ids[0]);
					$name = $fbUser->getUserInfo('name');
				}
				// Make sure we were able to get a name from the database or Facebook
				if ( !empty( $name ) ) {
					// Instead of regexes, we know the text so just search for it
					$item = str_replace( ">{$row->user_name}</a>", ">{$name}</a>", $item );
				}
				// Add a pretty Facebook logo next to Facebook users
				if ( !empty( $wgFbLogo ) ) {
					// Look to see if class="..." appears in the link
					$regs = array();
					preg_match( '/^([^>]*?)class=(["\'])([^"]*)\2(.*)/', $item, $regs );
					if (count( $regs )) {
						// If so, append "mw-facebook-logo" to the end of the class list
						$item = $regs[1] . "class=$regs[2]$regs[3] mw-facebook-logo$regs[2]" . $regs[4];
					} else {
						// Otherwise, stick class="mw-facebook-logo" into the link just before the '>'
						preg_match( '/^([^>]*)(.*)/', $item, $regs );
						$item = $regs[1] . ' class="mw-facebook-logo"' . $regs[2];
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Adds some info about the governing Facebook group to the header form of
	 * Special:ListUsers.
	 *
	// r274: Fix error with PHP 5.3 involving parameter references (thanks, PChott)
	static function SpecialListusersHeaderForm( $pager, &$out ) {
		global $wgFbUserRightsFromGroup, $facebook;
		
		if ( !empty( $wgFbUserRightsFromGroup ) ) {
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
		}
		return true;
	} // SpecialListusersHeaderForm hook
	
	/**
	 * Removes Special:UserLogin and Special:CreateAccount from the list of
	 * special pages if $wgFbDisableLogin is set to true.
	 */
	static function SpecialPage_initList( &$aSpecialPages ) {
		global $wgFbDisableLogin;
		if ( !empty( $wgFbDisableLogin) ) {
			// U can't touch this
			$aSpecialPages['Userlogin'] = array(
				'SpecialUserLoginToConnect',
			);
			// Used in 1.12.x and above
			$aSpecialPages['CreateAccount'] = array(
				'SpecialUserLoginToConnect',
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
	 * 
	 * TODO: A potential security flaw is exposed for users who run untrusted
	 * JavaScript code. Because no password exists, JavaScript could set a new
	 * password without the user's knowledge. To guard against this, we need to
	 * send the user an email and preemptively generate a password reset token.
	 */
	public static function UserComparePasswords( $hash, $password, $userId, &$result ) {
		global $wgUser;
		// Only override if no password exists and the old password ($hash) is blank
		if ( $hash == '' && $password == '' && $userId ) {
			// Only check for password on Special:ChangePassword
			// TODO: should we use RequestContext::getMain()->getTitle() instead?
			$title = $wgUser->getSkin()->getTitle();
			if ($title instanceof Title && $title->isSpecial('Resetpass') || $title->isSpecial('ChangePassword')) {
				// Check to see if the MediaWiki user has connected via Facebook
				// before. For a more strict check, we could check if the user
				// is currently logged in to Facebook
				$user = User::newFromId( $userId );
				$fb_ids = FacebookDB::getFacebookIDs($user);
				if ( count($fb_ids) && $fb_ids[0] ) {
					$result = true;
					return false; // to override internal check
				}
			}
		}
		return true;
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
			$groups = $user->getGroups();
			if ( !$whitelistSysops || !in_array( 'sysop', $groups ) &&
			                          !in_array( 'fb-admin', $groups ) ) {
				foreach ( $aRights as $i => $right ) {
					if ( $right == 'createaccount' ) {
						unset( $aRights[$i] );
						break;
					}
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
		global $wgFbDisableLogin;
		
		// Don't mess with authentication on Special:Connect
		$title = $user->getSkin()->getTitle();
		if ( !empty( $wgFbDisableLogin ) && $user->isLoggedIn() &&
				$title instanceof Title && !$title->isSpecial('Connect') ) {
			$fbUser = new FacebookUser();
			
			// If possible, force a preemptive ping to Facebook's servers. Otherwise, we
			// must wait until the next page view to pick up the user's Facebook login status
			#$fbUser->isLoggedIn($ping = true);
			
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
		global $facebook, $wgUser;
		$title = $wgUser->getSkin()->getTitle();
		if ( $facebook->getUser() && $title instanceof Title &&
				$title->isSpecial('Userlogout') ) {
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
	 *
	static function initPreferencesExtensionForm( $user, &$preferences ) {
		global $wgOut, $wgJsMimeType, $wgExtensionsPath, $wgStyleVersion, $wgBlankImgUrl;
		$wgOut->addScript("<script type=\"{$wgJsMimeType}\" src=\"{$wgExtensionsPath}/Facebook/prefs.js?{$wgStyleVersion}\"></script>\n");
		//wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
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
	} // initPreferencesExtensionForm hook
	/**/
}
