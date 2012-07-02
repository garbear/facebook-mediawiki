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
 * Class SpecialConnect
 * 
 * Special:Connect is where the magic of this extension takes place. All
 * authentication, adding and removing accounts happens through this special page
 * (with the small exception of FacebookHooks::UserLoadAfterLoadFromSession).
 * 
 * The entry point of this special page is execute($subPageName). Refer to the
 * documentation there for a description of how this special page operates.
 */
class SpecialConnect extends SpecialPage {
	/**
	 * Constructor. Invoke the super class's constructor with the default arguments.
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
		return wfMsg( 'facebook-title' );
	}
	
	/**
	 * Helper function. Always called when rendering this special page.
	 * 
	 * TODO: Don't return to special pages Connect, Userlogin and Userlogout
	 */
	private function setReturnTo() {
		global $wgRequest;
		
		$this->mReturnTo = $wgRequest->getVal( 'returnto' );
		$this->mReturnToQuery = $wgRequest->getVal( 'returntoquery' );
		
		/**
		 * Wikia BugId: 13709
		 * Before the fix, the logic and the usage of parse_str was wrong
		 * which had fatal side effects.
		 *
		 * The goal of the block below is to remove the fbconnected
		 * variable from the $this->mReturnToQuery (which is supposed
		 * to be a QUERY_STRING-like string.
		 */
		if( !empty($this->mReturnToQuery) ) {
			// A temporary array
			$aReturnToQuery = array();
			// Decompose the query string to the array
			parse_str( $this->mReturnToQuery, $aReturnToQuery );
			// Remove unwanted elements
			unset( $aReturnToQuery['fbconnected'] );
	
			//recompose the query string
			foreach ( $aReturnToQuery as $k => $v ) {
				$aReturnToQuery[$k] = "{$k}={$v}";
			}
			// Oh, parse_str implicitly urldecodes values which wasn't
			// mentioned in the PHP documentation.
			$this->mReturnToQuery = urlencode( implode( '&', $aReturnToQuery ) );
			// remove the temporary array
			unset( $aReturnToQuery );
		}
		
		$title = Title::newFromText($this->mReturnTo);
		if ($title instanceof Title) {
			$resolvedReturnTo = strtolower(array_shift(SpecialPageFactory::resolveAlias($title->getDBKey())));
			if (in_array( $resolvedReturnTo, array('userlogout', 'signup', 'connect') )) {
				$titleObj = Title::newMainPage();
				$this->mReturnTo = $titleObj->getText();
				$this->mReturnToQuery = '';
			}
		}
	}
	
	/**
	 * The controller interacts with the views through these three functions.
	 */
	public function sendPage($function, $arg = NULL) {
		global $wgOut;
		
		// Setup the page for rendering
		//wfLoadExtensionMessages( 'Facebook' ); // Deprecated since 1.16
		$this->setHeaders();
		$wgOut->disallowUserJs();  # just in case...
		// Disable page caching (this was messing with users visiting Special:Connect/Debug)
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
		$wgOut->enableClientCache( false );
		$wgOut->setArticleRelated( false );
		
		// Call the specified function to continue generating the page
		if (is_null($arg)) {
			$this->$function();
		} else {
			$this->$function($arg);
		}
	}
	
	protected function sendError($titleMsg, $textMsg, $msgParams = NULL) {
		global $wgOut;
		// Special case: $titleMsg is a list of permission errors
		if ( is_array( $titleMsg ) )
			$wgOut->showPermissionsErrorPage( $titleMsg, $textMsg );
		// Special case: read only error
		else if ( $titleMsg == 'readonlypage' )
 			$wgOut->readOnlyPage();
		// General cases: normal error page
		else if ($msgParams)
 			$wgOut->showErrorPage($titleMsg, $textMsg, $msgParams);
		else
			$wgOut->showErrorPage($titleMsg, $textMsg);
	}
	
	protected function sendRedirect($specialPage) {
		global $wgOut, $wgUser, $wgFbDisableLogin;
		
		// $wgFbDisableLogin disables UserLogin page. Avoid infinite redirects.
		if ( !empty( $wgFbDisableLogin ) && $specialPage == 'UserLogin' ) {
			$this->sendPage('exclusiveLoginToFacebookView');
			return;
		}
		
		$urlaction = '';
		if ( !empty( $this->mReturnTo ) ) {
			$urlaction = 'returnto=' . $this->mReturnTo;
			if ( !empty( $this->mReturnToQuery ) )
				$urlaction .= '&returntoquery=' . $this->mReturnToQuery;
		}
		
		// Compatiblity with MW < 1.18
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
			$skin = $this->getSkin();
		} else {
			global $wgUser;
			$skin = $wgUser->getSkin();
		}
		
		$wgOut->redirect($skin->makeSpecialUrl($specialPage, $urlaction));
	}
	
	/**
	 * Entry point of the special page.
	 * 
	 * Special:Connect uses a MVC architecture, with execute() being the
	 * controller. The control flow happens by switching the subpage's name and
	 * then moving through a boatload of nested ifs (notice how early returns
	 * are avoided). Subpages are exclusively used for secondary stages of the
	 * connecting process; that is, they are only invoked from Special:Connect
	 * itself. AJAX functions can also streamline the connecting process by
	 * sending the user directly to one of these subpages. A similar control
	 * structure has been laid out in the login function of ext.facebook.js.
	 * 
	 * Now on to the MVC part.
	 * 
	 * Function execute() operates on three possible models: the MediaWiki User
	 * class, FacebookUser and FacebookApplication. At the end of a control path,
	 * the process is this: update the model, then call the view (which may depend
	 * on the model, but strictly on a read-only basis).
	 * 
	 * Models are only updated from subpages, not Special:Connect. Subpages are
	 * the endpoint of any connecting/disconnecting process, and are only valid
	 * when POSTed to from Special:Connect or AJAX calls acting on behalf of
	 * Special:Connect. The one exception is if the user is logged in to Facebook
	 * and has a MediaWiki account, but isn't logged in to MediaWiki; they will
	 * then be logged in to MediaWiki. Oh, and a second exception is that
	 * Special:Connect/Debug can be navigated to, but it's only for testing purposes.
	 */
	public function execute( $subPageName ) {
		global $wgUser, $wgRequest;
		
		// Setup the session
		global $wgSessionStarted;
		if (!$wgSessionStarted) {
			wfSetupSession();
		}
		
		$this->setReturnTo();
		
		switch ( $subPageName ) {
		/**
		 * Special:Connect/ChooseName is POSTed to after the new Facebook user
		 * has chosen their MediaWiki account options (the wpNameChoice param),
		 * either to connect an existing account (if allowed) or to create a
		 * new account with the specified options.
		 * 
		 * TODO: Verify that the request is a POST, not a GET (currently they
		 * both do the same thing, I think).
		 */
		case 'ChooseName':
			if ( $wgRequest->getCheck('wpCancel') ) {
				$this->sendError('facebook-cancel', 'facebook-canceltext');
			} else {
				$choice = $wgRequest->getText('wpNameChoice');
				try {
					if ( $choice == 'existing' ) {
						// Handle accidental reposts
						if ( !$wgUser->isLoggedIn() ) {
							$name = $wgRequest->getText('wpExistingName');
							$password = $wgRequest->getText('wpExistingPassword');
							// Update the model
							$fbUser = new FacebookUser();
							$fbUser->attachUser($name, $password, $this->getUpdatePrefs());
						}
						// Send the view
						$this->sendPage('displaySuccessAttachingView');
					} else {
						// Handle accidental reposts
						if ( !$wgUser->isLoggedIn() ) {
							// Update the model (wpDomain isn't currently set...)
							$fbUser = new FacebookUser();
							$fbUser->createUser($this->getUsername($choice), $wgRequest->getText('wpDomain'));
						}
						$this->sendPage('loginSuccessView', true);
					}
				} catch (FacebookUserException $e) {
					// HACK: If the title msg is 'connectNewUserView' then we
					// send the view instead of an error
					if ($e->getTitleMsg() == 'connectNewUserView') {
						$this->sendPage('connectNewUserView', $e->getTextMsg());
					} else {
						$this->sendError($e->getTitleMsg(), $e->getTextMsg(), $e->getMsgParams());
					}
				}
			}
			break;
		/**
		 * Special:Connect/LogoutAndContinue does just that -- logs the current
		 * MediaWiki user out and another MediaWiki user in based on the current
		 * Facebook credentials. No parameters, so if a non-Facebook users GETs
		 * this they will be logged out and sent to Special:UserLogin.
		 * 
		 * TODO: In the case above, redirect to Special:UserLogout.
		 */
		case 'LogoutAndContinue':
			// Update the model (MediaWiki user)
			$oldName = $wgUser->logout();
			$injected_html = ''; // unused
			wfRunHooks( 'UserLogoutComplete', array(&$wgUser, &$injected_html, $oldName) );
			
			$fbUser = new FacebookUser();
			if ($fbUser->getMWUser()->getId()) {
				// Update the model again (Facebook user)
				$fbUser->login();
				$this->sendPage('loginSuccessView');
			} else {
				$this->sendRedirect('UserLogin');
			}
			break;
		/**
		 * Special:Connect/MergeAccount takes care of connecting Facebook users
		 * to existing accounts.
		 * 
		 * TODO: Verify this is a POST request
		 */
		case 'MergeAccount':
			if ( !$wgUser->isLoggedIn() ) {
				// Shouldn't be here
				$this->sendError('facebook-error', 'facebook-errortext');
			} else {
				try {
					$fbUser = new FacebookUser();
					// Handle accidental reposts
					if ( $fbUser->getMWUser()->getId() != $wgUser->getId() ) {
						$fbUser->attachCurrentUser( $this->getUpdatePrefs() );
					}
					$this->sendPage('displaySuccessAttachingView');
				} catch (FacebookUserException $e) {
					$this->sendError($e->getTitleMsg(), $e->getTextMsg(), $e->getMsgParams());
				}
			}
			break;
		/**
		 * Special:Connect/Deauth is a callback used by Facebook to notify the
		 * application that the Facebook user has deauthenticated the app
		 * (removed it from their app settings page). If the request for this
		 * page isn't signed by Facebook, it will redirect to Special:Connect.
		 */
		case 'Deauth':
			// Facebook will include a signed_request param to verify authenticity
			global $facebook;
			$signed_request = $facebook->getSignedRequest();
			if ( $signed_request ) {
				// Update the model
				$fbUser = new FacebookUser($signed_request['user_id']);
				$fbUser->disconnect();
				// What view should we show to Facebook? It doesn't really matter...
				$this->setHeaders();
			} else {
				// signed_request not present or hash mismatch
				$this->sendRedirect('Connect');
			}
			break;
		/**
		 * Special:Connect/Debug allows an administrator to verify that both
		 * the Facebook application this extension are setup and working
		 * correctly. This page can only be accessed if $wgFbAllowDebug is
		 * true; see config.default.php for more information.
		 */
		case 'Debug':
			global $wgFbAllowDebug;
			if ( !empty( $wgFbAllowDebug ) ) {
				$app = new FacebookApplication();
				if ( $app->canEdit() ) {
					$this->sendPage('debugView');
				} else {
					// Something went wrong
					$fbUser = new FacebookUser();
					
					// First, check MediaWiki permissions. Then check with Facebook
					if ( $fbUser->getMWUser()->getId() ) {
						// If $wgFbUserRightsFromGroups is set, this should trigger a group check
						$groups = $fbUser->getMWUser()->getEffectiveGroups();
						if ( in_array('sysop', $groups) || in_array('fb-admin', $groups) ) {
							$this->sendError('facebook-error', 'facebook-error-application'); // roles
						} else {
							global $wgLang, $wgFbUserRightsFromGroup;
							
							$groupArray = array( 'sysop' );
							if ( !empty( $wgFbUserRightsFromGroup ) ) {
								$groupArray[] = 'fb-admin';
							}
							
							$groupsList = array_map( array('User', 'makeGroupLinkWiki'), $groupArray );
							
							$this->sendError('facebook-error', 'badaccess-groups', array(
									$wgLang->commaList( $groupsList ),
									count( $groupsList ),
							));
						}
					} else {
						// Facebook user is not logged in or is not connected to a MediaWiki user
						// Show special instructions to MediaWiki administrators
						$isAdmin = false;
						if ( $wgUser->isLoggedIn() ) {
							$groups = $wgUser->getEffectiveGroups();
							if ( in_array('sysop', $groups) || in_array('fb-admin', $groups) ) {
								$isAdmin = true;
							}
						}
						if ( $isAdmin ) {
							$this->sendError('facebook-error', 'facebook-error-needs-convert');
						} else {
							// Generic validation error
							$this->sendError('facebook-error', 'facebook-errortext');
						}
					}
				}
				break;
			}
			// no break
		/**
		 * Special:Connect was called with no subpage specified.
		 */
		default:
			// If $subPageName was invalid, redirect to Special:Connect with no subpage
			if ( !empty( $subPageName ) ) {
				$this->sendRedirect('Connect');
				break;
			}
			
			$fbUser = new FacebookUser();
			
			// Our Facebook session might be stale. If this becomes a problem, use
			// $fbUser->isLoggedIn($ping = true) to force a refresh of the state
			if ( !$fbUser->isLoggedIn() ) {
				// The user isn't logged in to Facebook
				if ( !$wgUser->isLoggedIn() ) {
					// The user isn't logged in to Facebook or MediaWiki
					$this->sendRedirect('UserLogin'); // Nothing to see here, move along
				} else {
					// The user is logged in to MediaWiki but not Facebook
					$this->sendPage('loginToFacebookView');
				}
			} else {
				// The user is logged in to Facebook
				$mwId = $fbUser->getMWUser()->getId();
				if ( !$wgUser->isLoggedIn() ) {
					// The user is logged in to Facebook but not MediaWiki
					if ( !$mwId ) {
						// The Facebook user is new to MediaWiki
						$this->sendPage('connectNewUserView');
					} else {
						// The user is logged in to Facebook, but not MediaWiki.
						// The UserLoadAfterLoadFromSession hook might have misfired
						// if the user's "remember me" option was disabled.
						$fbUser->login();
						$this->sendPage('loginSuccessView');
					}
				} else {
					// The user is logged in to Facebook and MediaWiki
					if ( $mwId == $wgUser->getId() ) {
						// MediaWiki user belongs to the Facebook account
						$this->sendRedirect('UserLogin'); // Nothing to see here, move along
					} else {
						// Accounts don't agree
						if ( !$mwId ) {
							// Facebook user is new
							$fb_ids = FacebookDB::getFacebookIDs($wgUser);
							if ( count( $fb_ids ) == 0 ) {
								// MediaWiki user is free
								// Both accounts are free. Ask to merge
								$this->sendPage('mergeAccountView');
							} else {
								// MediaWiki user already associated with Facebook ID
								// TODO: LogoutAndCreateNewUser form
								global $wgContLang;
								$param1 = '[[' . $wgContLang->getNsText( NS_USER ) . ":{$wgUser->getName()}|{$wgUser->getName()}]]";
								$param2 = $fbUser->getUserInfo('name');
								$this->sendError('errorpagetitle', 'facebook-error-wrong-id', array('$1' => $param1, '$2' => $param2));
							}
						} else {
							// Facebook account has a MediaWiki user
							// Ask to log out and continue as the new user ($mwId)
							$this->sendPage('logoutAndContinueView', $mwId);
						}
					}
				}
			}
		}
	} // function execute()
	
	/**
	 * Figures out the best username to use for the creation of a new user.
	 * 
	 * If $choice isn't an invalid option, a FacebookUserException is thrown to
	 * signal an error.
	 * 
	 * @throws FacebookUserException
	 */
	private function getUsername($choice) {
		global $wgRequest;
		switch ($choice) {
		// Figure out the username to send to the model
		case 'nick':
		case 'first':
		case 'full':
			// Get the username from Facebook (note: not from the form)
			$fbUser = new FacebookUser();
			$username = FacebookUser::getOptionFromInfo($choice . 'name', $fbUser->getUserInfo());
			if ( !empty( $username ) && FacebookUser::userNameOK($username) ) {
				return $username;
			}
			// no break
		case 'manual':
			// Use manual name if no username is set (even if manual wasn't chosen)
			$username = $wgRequest->getText( 'wpName2' );
			if ( !empty( $username ) && FacebookUser::userNameOK($username) ) {
				return $username;
			}
			// If no valid username was found, something's not right; ask again
			throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
		case 'auto':
			$username = FacebookUser::generateUserName();
			// Just in case the automatically-generated username is a bad egg
			if ( !empty($username) && FacebookUser::userNameOK($username) ) {
				return $username;
			}
			throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
		default:
			throw new FacebookUserException('facebook-invalid', 'facebook-invalidtext');
		}
	}
	
	/**
	 * Helper function for Special:Connect/ChooseName and Special:Connect/MergeAccount.
	 * 
	 * Returns an array representing the checkboxes specified by wpUpdateUserInfo*OPTION*.
	 */
	private function getUpdatePrefs() {
		global $wgRequest;
		$updatePrefs = array();
		foreach (FacebookUser::$availableUserUpdateOptions as $option) {
			if ( $wgRequest->getText("wpUpdateUserInfo$option", '0') == '1' ) {
				$updatePrefs[] = $option;
			}
		}
		return $updatePrefs;
	}
	
	/**
	 * Strip <p> and </p> tags from a string.
	 */
	private function trimPTags($str) {
		$str = str_replace('<p>', '', $str);
		$str = str_replace('</p>', '', $str);
		$str = trim($str);
		return $str;
	}
	
	/**
	 * Special:Connect/Debug
	 * 
	 * This is the only subpage that can be called directly. It allows an admin
	 * to verify that the app is set up correctly inside Facebook, and offers
	 * to automatically fix some of the problems it detects.
	 */
	private function debugView() {
		global $wgRequest, $wgOut, $wgFbNamespace, $wgFbLogo, $wgEmergencyContact, $wgStylePath, $wgVersion;
		
		// "Enter a page name to view it as an object in the Open Graph." Render a button that
		// submits the wpPageName field to Special:Connect/Debug and handle the submission here.
		// TODO: handle the redirect in execute() maybe
		// The following code is untested
		$pageName = $wgRequest->getText('wpPageName');
		if ( $pageName != '' ) {
			$title = Title::newFromText( $pageName );
			if ( !( $title instanceof Title ) ) {
				$title = Title::newMainPage();
			}
			$url = 'https://developers.facebook.com/tools/debug/og/object?q=' . urlencode( $title->getFullURL() );
			$wgOut->redirect( $url );
			return;
		}
		
		$wgOut->setPageTitle(wfMsg('facebook-debug-title'));
		
		// Include the JavaScript that lets us change the application properties
		if ( version_compare( $wgVersion, '1.17', '>=' ) ) {
			$wgOut->addModules( 'ext.facebook.application' );
		}
		
		$app = new FacebookApplication();
		$info = $app->getInfo();
		
		// If the request failed, 'id' will be the only field
		$id = $info['id'];
		unset($info['id']);
		
		if ( empty( $info ) ) {
			$wgOut->addHTML("No application information could be retrieved from Facebook.");
			return;
		}
		
		$server = '';
		// Lookup the server name
		if( isset( $_SERVER['SERVER_NAME'] ) ) {
	        $server = $_SERVER['SERVER_NAME'];
		} elseif( isset( $_SERVER['HOSTNAME'] ) ) {
	        $server = $_SERVER['HOSTNAME'];
		} elseif( isset( $_SERVER['HTTP_HOST'] ) ) {
	        $server = $_SERVER['HTTP_HOST'];
		} elseif( isset( $_SERVER['SERVER_ADDR'] ) ) {
	        $server = $_SERVER['SERVER_ADDR'];
		}
		
		// Might as well not show a blank logo
		if ( !$info['icon_url'] && !empty( $wgFbLogo ) ) {
			$info['icon_url'] = $wgFbLogo;
		}
		
		// Get a link to the creator's wiki page or Facebook profile page
		$creatorLink = '';
		if ( $info['creator_uid'] ) {
			$creator = new FacebookUser( $info['creator_uid'] );
			if ( $creator->getMWUser()->getId() ) {
				$creatorLink = '<a href="' .
						$creator->getMWUser()->getUserPage()->getFullURL() . '">' .
						$creator->getMWUser()->getName() . '</a>';
			} else {
				$creatorLink = '<span class="mw-facebook-logo"><a ' .
						'href="https://www.facebook.com/profile.php?id=' .
						$info['creator_uid'] . '">' . $info['creator_uid'] . '</a></span>';
			}
		}
		
		// The values of these fields are pulled from the extension messages
		$fields_with_msgs = array(
			'privacy_policy_url'            => 'privacypage',
			'terms_of_service_url'          => 'facebook-termsofservicepage',
			'auth_dialog_headline'          => 'facebook-auth-dialog-headline',
			'auth_dialog_description'       => 'facebook-auth-dialog-description',
			'auth_dialog_perms_explanation' => 'facebook-auth-dialog-explanation',
		);
		
		// Format is: (field_name, Display name, Description, Suggested value)
		$field_array = array(
			array(
				'namespace',
				'Namespace',
				'Namespace your app uses for Open Graph',
				FacebookAPI::isNamespaceSetup() ? $wgFbNamespace : '',
			),
			array(
				'website_url',
				'Website URL',
				'URL of the Main Page',
				Title::newMainPage()->getFullURL(),
			),
				array(
				'deauth_callback_url',
				'Deauthorize URL',
				'Set this to Special:Connect/Deauth',
				self::getTitleFor('Connect', 'Deauth')->getFullURL(),
			),
			array(
				'privacy_policy_url',
				'Privacy policy URL',
				'The Facebook Platform Terms of Service require a privacy policy URL',
				Title::newFromText(wfMsg($fields_with_msgs['privacy_policy_url']))->getFullURL(),
			),
			array(
				'terms_of_service_url',
				'Terms of service URL',
				'',
				Title::newFromText(wfMsg($fields_with_msgs['terms_of_service_url']))->getFullURL(),
			),
			array(
				'app_domains',
				'App domains',
				'Domains and subdomains this app can use (e.g., "example.com" will enable *.example.com)',
				$server,
			),
			array(
				'contact_email',
				'Contact email',
				'Primary email used for important communication related to your app ($wgEmergencyContact)',
				!empty( $wgEmergencyContact ) ? $wgEmergencyContact : '',
			),
			array(
				'user_support_email',
				'User support email',
				'Required by Facebook: Main contact email for this app ($wgEmergencyContact)',
				!empty( $wgEmergencyContact ) ? $wgEmergencyContact : '',
			),
			array(
				'auth_dialog_headline',
				'Auth dialog headline',
				'Description that appears in the Auth Dialog (30 characters or less)',
				$this->trimPTags(wfMsgWikiHtml($fields_with_msgs['auth_dialog_headline'])),
			),
			array(
				'auth_dialog_description',
				'Auth dialog description',
				'Description that appears in the Auth Dialog (140 characters or less)',
				$this->trimPTags(wfMsgWikiHtml($fields_with_msgs['auth_dialog_description'])),
			),
			array(
				'auth_dialog_perms_explanation',
				'Explanation for permissions',
				'Provide an explanation for how your app plans to use extended permissions, if any (140 characters or less)',
				$this->trimPTags(wfMsgWikiHtml($fields_with_msgs['auth_dialog_perms_explanation'])),
			),
			/*
			// This extension doesn't use the News Feed
			array(
				'description',
				'News feed description',
				'Description that appears on News Feed stories',
				'',
			),
			*/
			array(
				'daily_active_users',
				'Daily active users',
				'',
				'',
			),
			array(
				'weekly_active_users',
				'Weekly active users',
				'',
				'',
			),
			array(
				'monthly_active_users',
				'Monthly active users',
				'',
				'',
			),
		);
		
		// Build the html
		$html = '
<table>
	<tr>
		<td>
			<a href="' . $info['link'] . '">
				<img src="' . $info['logo_url'] . '" style="width:75px; height:75px; margin-right:8px;">
			</a>
		</td>
		<td><div>
			<h3 style="float:left;padding:0 !important;">
				<span style="padding-left: 22px; background:url(\'' . $info['icon_url'] . '\') no-repeat left center;">
					' . $info['name'] . '
				</span>
			</h3>
			<div style="font-size:0.9em;"><table cellpadding="0" cellspacing="0">
				<tr>
					<td><b>App ID:</b></td>
					<td style="padding:0 14px;"><a href="' . $info['link'] . '">' . $id . '</a></td>
				</tr>
				<tr>
					<td><b>App creator:</b></td>
					<td style="padding:0 14px;">' . $creatorLink . '</td>
				</td>
				<tr>
					<td colspan="2" style="font-size:0.95em">
						<a href="https://developers.facebook.com/apps/' . $id . '/summary">(Edit settings)</a>
					</td>
				</tr>
			</table></div>
		</div></td>
	</tr>
</table>
' . wfMsgWikiHtml( 'facebook-debug-msg' ) . '<br/>
<table>';
		
		// The icons we use are included in MW 1.17 (use bits.wikimedia.org if not available)
		if ( version_compare( $wgVersion, '1.17', '>=' ) ) {
			$icon_base = $wgStylePath;
		} else {
			$icon_base = 'http://bits.wikimedia.org/skins-1.18';
		}
		$icons = array(
			'ok' => array(
				'tooltip' => 'OK',
				'src' => $icon_base . '/common/images/tick-32.png',
			),
			'warning' => array(
				'tooltip' => 'Click to update',
				'src' => $icon_base . '/common/images/warning-32.png',
			),
			'error' => array(
				'tooltip' => 'Click to update',
				'src' => $icon_base . '/common/images/critical-32.png',
			),
		);
		
		// Render each setting field of the application as a table row
		foreach ( $field_array as $item ) {
			$field   = $item[0]; // field_name
			$title   = $item[1]; // Display name
			$tip     = $item[2]; // Description
			$correct = $item[3]; // Suggested value
			
			if ( $field == 'app_domains' )
				continue; // TODO: $field is an array and involves wildcards
			
			$icon = false;
			if ( $correct != '' ) {
				if ( $info[$field] == $correct ) {
					$icon = 'ok';
				} else {
					switch ($field) {
						// Critical errors
						case 'namespace':
						case 'deauth_callback_url':
						case 'privacy_policy_url': // Necessary per Facebook's TOS
						case 'user_support_email': // Required: https://developers.facebook.com/blog/post/630/
							$icon = 'error';
							break;
						default:
							$icon = 'warning';
					}
				}
			}
			
			// If the field's value came from a message, link to the message in NS_MEDIAWIKI
			foreach ( $fields_with_msgs as $field_name => $msg_name ) {
				if ( $field == $field_name ) {
					$title = '<a href="' .
							Title::newFromText($fields_with_msgs[$field], NS_MEDIAWIKI)->getFullURL() .
							'">' . $title . '</a>';
					break;
				}
			}
			
			// Also, if the message is a page name, link to the page (in red) if it doesn't exist
			if ( $field == 'privacy_policy_url' || $field == 'terms_of_service_url' ) {
				$titleObj = Title::newFromText(wfMsg($fields_with_msgs[$field]));
				// Don't add a link to an empty $info[$field]
				if ( !$titleObj->exists() && $info[$field] != '' ) {
					$info[$field] = '<a href="' . $titleObj->getFullURL() . '" class="new">' . $info[$field] . '</a>';
				}
			}
			
			// Placeholder indicating particular field is empty
			if ($info[$field] == '') {
				$info[$field] = '<em>empty</em>';
			}
			
			// Generate the html for the row
			$html .= '
	<tr>
		<td style="text-align:right; padding:0;">
			' . ($tip == '' ? '' :
			'<img class="mw-facebook-tip" id="facebook-tip-' . $field . '" src="' . $wgStylePath .
				'/common/images/tooltip_icon.png" title="' . $tip . '" /> &nbsp;') . '
			<b>' . $title .':</b>
		</td>
		<td class="facebook-field" id="facebook-field-' . $field . '" style="padding:0 0 0 16px; height:22px;">
			<div class="facebook-field-current">
				<span>' . $info[$field] . '</span>
				' . ($icon ? '&nbsp; ' . ($icon != 'ok' ? '<a href="#">' : '') . '<img src="' . $icons[$icon]['src'] .
				'" style="width:22px; height:22px;" title="' . $icons[$icon]['tooltip'] . '" />' . ($icon != 'ok' ?
				'</a>' : '') : '') . '
			</div>' . ($icon ? '
			<div class="facebook-field-' . $icon . '" style="display:none;">
				<span>' . $correct . '</span>
				&nbsp; <img src="' . $icons['ok']['src'] . '" style="width:22px; height:22px;" title="' .
				$icons['ok']['tooltip'] . ' " />
			</div>' : '') . '
		</td>
	</tr>';
		}
		
		$html .= '
</table><br/>';
		
		// Add an option to debug Open Graph objects
		$html .= '
<form action="' . $this->getTitle('Debug')->getLocalUrl() . '" method="POST" style="padding-left:14px;">
	<h3>' . wfMsg('facebook-object-debug-title') . '</h3>
	<label for="wpPageName"><p>' . wfMsg('facebook-object-debug') . '</p></label>
	<input name="wpPageName" id="wpPageName" size="42" value="" style="font-size:1.75em;" /> &nbsp;
	<input type="submit" value="' . wfMsg('facebook-debug') . '" name="Debug" />
</form>';
		
		$html .= "\n<br/><br/>\n";
		
		$wgOut->addHTML( $html );
	}
	
	/**
	 * The user is logged in to MediaWiki but not Facebook.
	 * No Facebook user is associated with this MediaWiki account.
	 * 
	 * TODO: Facebook login button causes a post to a Special:Connect/ConnectUser or something
	 */
	private function loginToFacebookView() {
		global $wgOut, $wgSitename, $wgUser;
		$loginFormWidth = 400; // pixels
		
		$fb_ids = FacebookDB::getFacebookIDs($wgUser);
		
		$this->outputHeader();
		$html = '
<div id="userloginForm">
	<form style="width: ' . $loginFormWidth . 'px;">' . "\n";
		
		if ( !count( $fb_ids ) ) {
			// This message was added recently and might not be translated
			// In that case, fall back to an older, similar message
			$formTitle = wfMsg( 'facebook-merge-title' );
			// This test probably isn't correct. I'm open to ideas
			if ($formTitle == "&lt;facebook-merge-title&gt;") {
				$formTitle = wfMsg( 'login' );
			}
			$html .= '<h2>' . $formTitle . "</h2>\n";
			
			$formText = wfMsg( 'facebook-merge-text', $wgSitename );
			// This test probably isn't correct. I'm open to ideas
			if ($formText == "&lt;facebook-merge-text&gt;") {
				$formText = wfMsg( 'facebook-merge' );
			}
			$html .= '<p>' . $formText . "<br/><br/></p>\n";
		} else {
			$html .= '<h2>' . wfMsg( 'login' ) . "</h2>\n";
			// User is already connected to a Facebook account. Send a page asking
			// them to log in to one of their (possibly several) Facebook accounts
			// For now, scold them for trying to log in to a connected account
			// TODO
			$html .= '<p>' . wfMsg( 'facebook-connect-text' ) . "<br/><br/></p>\n";
		}
		
		// Compatiblity with MW < 1.18
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
			$skin = $this->getSkin();
		} else {
			global $wgUser;
			$skin = $wgUser->getSkin();
		}
		
		$html .= '<fb:login-button show-faces="true" width="' . $loginFormWidth .
				'" max-rows="3" scope="' . FacebookAPI::getPermissions() . '" colorscheme="' .
				FacebookXFBML::getColorScheme( $skin->getSkinName() ) .
				'"></fb:login-button><br/><br/><br/>' . "\n";
		
		// Add a pretty Like box to entice the user to log in
		$html .= '<fb:like href="' . Title::newMainPage()->getFullURL() . '" send="false" width="' .
					 $loginFormWidth . '" show_faces="true"></fb:like>';
		$html .= '
	</form>
</div>';
		$wgOut->addHTML($html);
		
		// TODO: Add a returnto link
	}
	
	/**
	 * This view is sent when $wgFbDisableLogin is true. In this case, the user
	 * must be logged in to Facebook to view the wiki, so we present a single
	 * login button.
	 */
	private function exclusiveLoginToFacebookView() {
		global $wgOut, $wgSitename, $wgUser;
		$loginFormWidth = 400; // pixels
		
		$this->outputHeader();
		$html = '
<div id="userloginForm">
	<form style="width: ' . $loginFormWidth . 'px;">
		<h2>' . wfMsg( 'userlogin' ) . '</h2>
		<p>' . wfMsg( 'facebook-only-text', $wgSitename ) . '<br/><br/></p>' . "\n";
		
		// Compatiblity with MW < 1.18
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
			$skin = $this->getSkin();
		} else {
			global $wgUser;
			$skin = $wgUser->getSkin();
		}
		
		$html .= '<fb:login-button show-faces="true" width="' . $loginFormWidth .
				'" max-rows="3" scope="' . FacebookAPI::getPermissions() . '" colorscheme="' .
				FacebookXFBML::getColorScheme( $skin->getSkinName() ) .
				'"></fb:login-button><br/><br/><br/>' . "\n";
		
		// Add a pretty Like box to entice the user to log in
		$html .= '<fb:like href="' . Title::newMainPage()->getFullURL() . '" send="false" width="' .
				$loginFormWidth . '" show_faces="true"></fb:like>';
		$html .= '
	</form>
</div>';
		$wgOut->addHTML($html);
	}
	
	
	/**
	 * The user is logged in to Facebook, but not MediaWiki. The Facebook user
	 * is new to MediaWiki.
	 * 
	 * If $messageKey is left blank, and a hook doesn't modify its value, then
	 * the 'facebook-chooseinstructions' message will be used.
	 */
	private function connectNewUserView($messagekey = '') {
		global $wgUser, $wgOut, $wgFbDisableLogin;
		
		if ( wfReadOnly() ) {
			// The wiki is in read-only mode
			$wgOut->readOnlyPage();
			return;
		}
		if ( empty( $wgFbDisableLogin ) ) {
			// These two permissions don't apply in $wgFbDisableLogin mode because
			// then technically no users can create accounts
			if ( $wgUser->isBlockedFromCreateAccount() ) {
				wfDebug("Facebook: Blocked user was attempting to create account via Facebook Connect.\n");
				// This is not an explicitly static method but doesn't use $this and can be called like static
				LoginForm::userBlockedMessage();
				return;
			} else {
				$titleObj = SpecialPage::getTitleFor( 'Connect' );
				$permErrors = $titleObj->getUserPermissionsErrors('createaccount', $wgUser, true);
				if (count( $permErrors ) > 0) {
					// Special case for permission errors
					$this->sendError($permErrors, 'createaccount');
					return;
				}
			}
		}
		
		// Allow other code to have a custom form here (so that this extension
		// can be integrated with existing custom login screens). Hook must
		// output content if it returns false.
		if( !wfRunHooks( 'SpecialConnect::chooseNameForm', array( &$this, &$messagekey ) ) ){
			return;
		}
		
		$fbUser = new FacebookUser();
		$userinfo = $fbUser->getUserInfo();
		
		// Outputs the canonical name of the special page at the top of the page
		$this->outputHeader();
		
		// Grab the UserName from the cookie if it exists
		global $wgCookiePrefix;
		$existingName = isset($_COOKIE["{$wgCookiePrefix}UserName"]) ? trim($_COOKIE["{$wgCookiePrefix}UserName"]) : '';
		
		$form = $this->getChooseNameForm($userinfo, $messagekey, $existingName);
		
		$wgOut->addHTML($form . "\n\n");
	}
	
	/**
	 * Returns the HTML for the ChooseName form.
	 */
	public function getChooseNameForm($userinfo, $messageKey = '', $existingName = '') {
		global $wgFbDisableLogin;
		
		// Keep track of when the first option visible to the user is checked
		$checked = false;
		
		// If a different $messagekey was passed (like 'wrongpassword'), use it instead
		if ( $messageKey == '' ) {
			$messageKey = 'facebook-chooseinstructions';
		}
		
		$html = wfMsgWikiHtml( $messageKey );
		
		$html .= '
<form action="' . $this->getTitle('ChooseName')->getLocalUrl() . '" method="POST">
	<fieldset id="mw-facebook-choosename">
		<legend>' . wfMsg('facebook-chooselegend') . '</legend>
		<table>';
		// Let them attach to an existing. If $wgFbDisableLogin is true, then
		// stand-alone account aren't allowed in the first place
		if (empty( $wgFbDisableLogin )) {
			$updateChoices = "<br/>\n" . $this->getUpdateOptions($userinfo);
			
			// Create the HTML for the "existing account" option
			$html .= '
			<tr>
				<td class="wm-label">
					<input name="wpNameChoice" type="radio" value="existing" id="wpNameChoiceExisting"/>
				</td>
				<td class="mw-input">
					<label for="wpNameChoiceExisting">' . wfMsg('facebook-chooseexisting') . '</label>
					<div id="mw-facebook-choosename-update" class="fbInitialHidden">
						<label for="wpExistingName">' . wfMsgHtml('facebook-chooseusername') . '</label>
						<input name="wpExistingName" size="20" value="' . $existingName . '" id="wpExistingName" />&nbsp;
						<label for="wpExistingPassword">' . wfMsgHtml('facebook-choosepassword') . '</label>
						<input name="wpExistingPassword" size="20" value="" type="password" id="wpExistingPassword" /><br/>
						' . $updateChoices . '
					</div>
				</td>
			</tr>';
		}
		
		// Add the options for nick name, first name and full name if we can get them
		foreach (array('nick', 'first', 'full') as $option) {
			$nickname = FacebookUser::getOptionFromInfo($option . 'name', $userinfo);
			if ($nickname && FacebookUser::userNameOK($nickname)) {
				$html .= '
			<tr>
				<td class="mw-label">
					<input name="wpNameChoice" type="radio" value="' . $option . ($checked ? '' : '" checked="checked') .
						'" id="wpNameChoice' . $option . '" />
				</td>
				<td class="mw-input">
					<label for="wpNameChoice' . $option . '">' . wfMsg('facebook-choose' . $option, $nickname) . '</label>
				</td>
			</tr>';
				// When the first radio is checked, this flag is set and subsequent options aren't checked
				$checked = true;
			}
		}
		
		// The options for auto and manual usernames are always available
		$html .= '
			<tr>
				<td class="mw-label">
					<input name="wpNameChoice" type="radio" value="auto" ' . ($checked ? '' : 'checked="checked" ') .
						'id="wpNameChoiceAuto" />
				</td>
				<td class="mw-input">
					<label for="wpNameChoiceAuto">' . wfMsg('facebook-chooseauto', FacebookUser::generateUserName()) . '</label>
				</td>
			</tr>
			<tr>
				<td class="mw-label">
					<input name="wpNameChoice" type="radio" value="manual" id="wpNameChoiceManual" />
				</td>
				<td class="mw-input">
					<label for="wpNameChoiceManual">' . wfMsg('facebook-choosemanual') . '</label>&nbsp;
					<input name="wpName2" size="16" value="" id="wpName2" />
				</td>
			</tr>';
		// Finish with two options, "Log in" or "Cancel"
		$html .= '
			<tr>
				<td></td>
				<td class="mw-submit">
					<input type="submit" value="Log in" name="wpOK" />
					<input type="submit" value="Cancel" name="wpCancel" />';
		
		// Include returnto and returntoquery parameters if they are set
		if (!empty($this->mReturnTo)) {
			$html .= '
					<input type="hidden" name="returnto" value="' . $this->mReturnTo . '" />';
			// Only need returntoquery if returnto is set
			if (!empty($this->mReturnToQuery)) {
				$html .= '
					<input type="hidden" name="returntoquery" value="' . $this->mReturnToQuery . '" />';
			}
		}
		
		$html .= '
				</td>
			</tr>
		</table>
	</fieldset>
</form>';
		
		return $html;
	}
	
	/**
	 * TODO: Document me
	 */
	private function getUpdateOptions($userinfo) {
		global $wgRequest;
		
		// Build an array of attributes to update
		$updateOptions = array();
		foreach (FacebookUser::$availableUserUpdateOptions as $option) {
			
			// Translate the MW parameter into a FB parameter
			$value = FacebookUser::getOptionFromInfo($option, $userinfo);
			
			// If no corresponding value was received from Facebook, then continue
			if (!$value) {
				continue;
			}
			
			// Check to see if the option was checked on a previous page (default to true)
			$checked = ($wgRequest->getText("wpUpdateUserInfo$option", '0') != '1');
			
			// Build the list item for the update option
			$item  = '<li>';
			$item .= '<input name="wpUpdateUserInfo' . $option . '" type="checkbox" ' .
			         'value="1" id="wpUpdateUserInfo' . $option . '" ' .
			         ($checked ? 'checked="checked" ' : '') . '/>';
			$item .= '<label for="wpUpdateUserInfo' . $option . '">' . wfMsgHtml("facebook-$option") .
			         wfMsgExt('colon-separator', array('escapenoentities')) . " <i>$value</i></label></li>";
			$updateOptions[] = $item;
		}
		
		// Implode the update options into an unordered list
		$updateChoices = '';
		if ( count($updateOptions) > 0 ) {
			$updateChoices .= wfMsgHtml('facebook-updateuserinfo') . "\n";
			$updateChoices .= "<ul>\n" . implode("\n", $updateOptions) . "\n</ul>\n";
		}
		return $updateChoices;
	}
	
	/**
	 * The user has just been logged in by their Facebook account.
	 */
	private function loginSuccessView($newUser = false) {
		global $wgOut, $wgUser;
		$wgOut->setPageTitle( wfMsg('facebook-success') );
		$wgOut->addWikiMsg( 'facebook-successtext' );
		// Run any hooks for UserLoginComplete
		$injected_html = '';
		wfRunHooks( 'UserLoginComplete', array( &$wgUser, &$injected_html ) );
		
		if ( $injected_html !== '' ) {
			$wgOut->addHtml( $injected_html );
			// Render the "return to" text retrieved from the URL
			$wgOut->returnToMain(false, $this->mReturnTo, $this->mReturnToQuery);
		} else {
			$addParam = '';
			if ( $newUser ) {
				$addParam = '&fbconnected=1';
			}
			// Since there was no additional message for the user, we can just
			// redirect them back to where they came from
			$titleObj = Title::newFromText( $this->mReturnTo );
			if ( ($titleObj instanceof Title) && !$titleObj->isSpecial('Userlogout') &&
					!$titleObj->isSpecial('Signup') && !$titleObj->isSpecial('Connect') ) {
				$query = '';
				if ( !empty($this->mReturnToQuery) )
					$query .= $this->mReturnToQuery . '&';
				$query .= 'cb=' . rand(1, 10000);
				$wgOut->redirect( $titleObj->getFullURL( $query ) );
			} else {
				$titleObj = Title::newMainPage();
				$wgOut->redirect( $titleObj->getFullURL( 'cb=' . rand(1, 10000) . $addParam ) );
			}
		}
	}
	
	/**
	 * MediaWiki user and Facebook user are both unconnected. Ask to merge
	 * these two.
	 */
	private function mergeAccountView() {
		global $wgOut;
		
		$wgOut->setPageTitle( wfMsg( 'facebook-merge-title' ) );
		
		$fbUser = new FacebookUser();
		$userinfo = $fbUser->getUserInfo();
		
		$form = $this->getMergeAccountForm( $userinfo );
		$wgOut->addHTML($form . "\n<br/>\n");
		
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $this->mReturnTo, $this->mReturnToQuery);
		$wgOut->addHTML("<br/>\n");
	}
	
	/**
	 * Returns the HTML for the ChooseName form.
	 */
	public function getMergeAccountForm($userinfo) {
		global $wgSitename;
		
		$html = '
<form action="' . $this->getTitle('MergeAccount')->getLocalUrl() . '" method="POST">
	<fieldset id="mw-facebook-chooseoptions">
		<legend>' . wfMsg('facebook-updatelegend') . '</legend>
		' . wfMsgExt('facebook-merge-text', 'parse', array('$1' => $wgSitename )) .
		// TODO
		//<p>Not $user? Log in as a different facebook user...</p>
		'
		<input type="submit" value="' . wfMsg( 'facebook-merge-title' ) . '" /><br/>
		<div id="mw-facebook-choosename-update">
			' . "<br/>\n" . $this->getUpdateOptions($userinfo) . '
		</div>';
		if ( !empty( $this->mReturnTo ) ) {
			$html .= '
		<input type="hidden" name="returnto" value="' . $this->mReturnTo . '" />';
			// Only need returntoquery if returnto is set
			if ( !empty( $this->mReturnToQuery ) ) {
				$html .= '
		<input type="hidden" name="returntoquery" value="' . $this->mReturnToQuery . '" />';
			}
		}
		$html .= '
	</fieldset>
</form>';
		
		return $html;
	}
	
	/**
	 * This error page is shown when the user logs in to Facebook, but the
	 * Facebook account is associated with a different user.
	 * 
	 * A precondition is that a different MediaWiki user is logged in. So, ask
	 * them to log out and continue.
	 * 
	 * TODO: But what about the case where a Facebook user is logged in, but
	 * not as a wiki user, and then logs into the wiki with the wrong account?
	 */
	private function logoutAndContinueView($userId) {
		global $wgOut;
		
		$wgOut->setPageTitle(wfMsg('facebook-logout-and-continue'));
		
		$fbUser = new FacebookUser();
		$userinfo = $fbUser->getUserInfo();
		
		$form = $this->getLogoutAndContinueForm( $userinfo, $userId );
		
		// TODO
		//$form .= '<p>Not $userId? Log in as a different facebook user...</p>';
		
		$wgOut->addHTML( $form . "<br/>\n" );
		
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $this->mReturnTo, $this->mReturnToQuery);
	}
	
	/**
	 * Returns the HTML for the LogoutAndContinue form.
	 */
	public function getLogoutAndContinueForm($userinfo, $userId) {
		global $wgContLang;
		
		$html = '';
		
		// Show a welcome message to the Facebook user who just logged in
		$firstName = ($userinfo && isset($userinfo['first_name'])) ? $userinfo['first_name'] : '';
		if ( $firstName != '' ) {
			$html .= '<p>' . wfMsg('facebook-welcome-name', array('$1' => $firstName)) . "</p>\n";
		}
		
		$username = User::newFromId($userId)->getName();
		
		$html .= '
' . wfMsgExt('facebook-continue-text', 'parse', array(
	'$1' => '[[' . $wgContLang->getNsText( NS_USER ) . ":$username|$username]]")
) . '
<form action="' . $this->getTitle('LogoutAndContinue')->getLocalUrl() . '" method="post">
	<input type="submit" value="' . wfMsg( 'facebook-continue-button' ) . '" />';
		if ( !empty( $this->mReturnTo ) ) {
			$html .= '
	<input type="hidden" name="returnto" value="' . $this->mReturnTo . '" />';
			// Only need returntoquery if returnto is set
			if ( !empty( $this->mReturnToQuery ) ) {
				$html .= '
	<input type="hidden" name="returntoquery" value="' . $this->mReturnToQuery . '" />';
			}
		}
		$html .= '
</form>';
		
		return $html;
	}
	
	/**
	 * Success page for attaching Facebook account to a pre-existing MediaWiki
	 * account. Shows a link to preferences and a link back to where the user
	 * came from.
	 */
	private function displaySuccessAttachingView() {
		global $wgOut, $wgUser, $wgRequest;
		wfProfileIn( __METHOD__ );
		
		$wgOut->setPageTitle( wfMsg('facebook-success') );
		
		$prefsLink = SpecialPage::getTitleFor('Preferences')->getLinkUrl();
		$wgOut->addHTML(wfMsg('facebook-success-connecting-existing-account', $prefsLink));
		
		// Run any hooks for UserLoginComplete
		$inject_html = '';
		wfRunHooks( 'UserLoginComplete', array( &$wgUser, &$inject_html ) );
		$wgOut->addHtml( $inject_html );
		
		// Since there was no additional message for the user, we can just
		// redirect them back to where they came from
		$titleObj = Title::newFromText( $this->mReturnTo );
		if ( ($titleObj instanceof Title) && !$titleObj->isSpecial('Userlogout') &&
				!$titleObj->isSpecial('Signup') && !$titleObj->isSpecial('Connect') ) {
			$query = '';
			if ( !empty($this->mReturnToQuery) )
				$query .=  $query = $this->mReturnToQuery . '&';
			$query .= 'fbconnected=1&cb=' . rand(1, 10000);
			$wgOut->redirect( $titleObj->getFullURL( $query ) );
		} else {
			/*
			 // Render a "return to" link retrieved from the URL
			$wgOut->returnToMain( false, $this->mReturnTo, $this->mReturnToQuery .
					(!empty($this->mReturnToQuery) ? '&' : '') .
					'fbconnected=1&cb=' . rand(1, 10000) );
			*/
			$titleObj = Title::newMainPage();
			$wgOut->redirect( $titleObj->getFullURL('fbconnected=1&cb=' . rand(1, 10000)) );
		}
		
		wfProfileOut(__METHOD__);
	}
}
