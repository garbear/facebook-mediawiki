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
 * Thrown when a FacebookUser encounters a problem.
 */
class FacebookUserException extends Exception
{
	protected $titleMsg;
	protected $textMsg;
	protected $msgParams;

	/**
	 * Make a new FacebookUser Exception with the given result.
	 */
	public function __construct($titleMsg, $textMsg, $msgParams = NULL) {
		$this->titleMsg  = $titleMsg;
		$this->textMsg   = $textMsg;
		$this->msgParams = $msgParams;
		
		// In general, $msg and $code are not meant to be used
		$msg = wfMsg( $this->textMsg );
		$code = 0;
		
		parent::__construct($msg, $code);
	}
	
	public function getTitleMsg() {
		return $this->titleMsg;
	}
	
	public function getTextMsg() {
		return $this->textMsg;
	}
	
	public function getMsgParams() {
		return $this->msgParams;
	}
	
	public function getType() {
		return 'Exception';
	}

	public function __toString() {
		return wfMsg( $this->textMsg );
	}
}


/**
 * Class SpecialConnect
 * 
 * This class represents the body class for the page Special:Connect.
 * 
 * Currently, this page has one valid subpage at Special:Connect/ChooseName.
 * Visiting the subpage will generate an error; it is only useful when POSTed to.
 */
class SpecialConnect extends SpecialPage {
	private $isNewUser = false;
	private $mEmail = '';
	private $mRealName = '';
	
	/**
	 * Constructor.
	 */
	function __construct() {
		global $wgSpecialPageGroups;
		// Initiate SpecialPage's constructor
		parent::__construct( 'Connect' );
		// Add this special page to the "login" group of special pages
		$wgSpecialPageGroups['Connect'] = 'login';
		
		wfLoadExtensionMessages( 'Facebook' );
		FacebookUser::setUserNamePrefix( wfMsg('facebook-usernameprefix') );
	}
	
	/**
	 * Returns the list of user options that can be updated by facebook on each login.
	 */
	public function getAvailableUserUpdateOptions() {
		return FacebookUser::$availableUserUpdateOptions;
	}
	
	/**
	 * Overrides getDescription() in SpecialPage. Looks in a different wiki message
	 * for this extension's description.
	 */
	function getDescription() {
		return wfMsg( 'facebook-title' );
	}
	
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
			// a temporary array
			$aReturnToQuery = array();
			// decompose the query string to the array
			parse_str( $this->mReturnToQuery, $aReturnToQuery );
			// remove unwanted elements
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
		
		// TODO: 302 redirect if returnto is a bad page
		$title = Title::newFromText($this->mReturnTo);
		if ($title instanceof Title) {
			$this->mResolvedReturnTo = strtolower(SpecialPage::resolveAlias($title->getDBKey()));
			if (in_array( $this->mResolvedReturnTo, array('userlogout', 'signup', 'connect') )) {
				$titleObj = Title::newMainPage();
				$this->mReturnTo = $titleObj->getText();
				$this->mReturnToQuery = '';
			}
		}
	}
	
	/**
	 * The controller interacts with the views through these two functions.
	 */
	public function sendPage($function, $arg = NULL) {
		global $wgOut;
		// Setup the page for rendering
		wfLoadExtensionMessages( 'Facebook' );
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
	
	protected function sendError($titleMsg, $textMsg, $msgParams = NULL) {
		global $wgOut;
		// Special case: $titleMsg is a list of permission errors
		if ( is_array( $titleMsg ) )
			$wgOut->showPermissionsErrorPage( $titleMsg, $textMsg );
		// Special case: read only error, all parameters are null
		else if ( is_null( $titleMsg ) && is_null( $textMsg ) )
 			$wgOut->readOnlyPage();
		// General cases: normal error page
		else if ($msgParams)
 			$wgOut->showErrorPage($titleMsg, $textMsg, $msgParams);
		else
			$wgOut->showErrorPage($titleMsg, $textMsg);
	}
	
	protected function sendRedirect($specialPage) {
		global $wgOut, $wgUser;
		$urlaction = '';
		if ( !empty( $this->mReturnTo ) ) {
			$urlaction = "returnto=$this->mReturnTo";
			if ( !empty( $this->mReturnToQuery ) )
				$urlaction .= "&returntoquery=$this->mReturnToQuery";
		}
		$wgOut->redirect( $wgUser->getSkin()->makeSpecialUrl( $specialPage, $urlaction ) );
	}
	
	/**
	 * Performs any necessary execution and outputs the resulting Special page.
	 * 
	 * If you are editing this page, try to conform to a MVC architecture.
	 */
	public function execute( $par ) {
		global $wgUser, $wgRequest, $facebook;
		
		/*
		if ( $wgRequest->getVal('action', '') == 'disconnect_reclamation' ) {
			$this->sendPage('disconnectReclamationActionView');
			return;
		}
		*/
		
		// Setup the session
		global $wgSessionStarted;
		if (!$wgSessionStarted) {
			wfSetupSession();
		}
		
		$this->setReturnTo();
		
		switch ( $par ) {
		case 'ChooseName':
			if ( $wgRequest->getCheck('wpCancel') ) {
				$this->sendError('facebook-cancel', 'facebook-canceltext');
			} else {
				$choice = $wgRequest->getText('wpNameChoice');
				try {
					$this->manageChooseNamePost($choice);
					if ( $choice == 'existing' ) {
						$this->sendPage('displaySuccessAttachingView');
					} else {
						$this->sendPage('loginSuccessView', true);
					}
				} catch (FacebookUserException $e) {
					// If the title msg is 'connectNewUserView' then we send the view instead of an error
					if ($e->getTitleMsg() == 'connectNewUserView')
						$this->sendPage('connectNewUserView', $e->getTextMsg());
					else
						$this->sendError($e->getTitleMsg(), $e->getTextMsg(), $e->getMsgParams());
				}
			}
			break;
		case 'LogoutAndContinue':
			$oldName = $wgUser->logout();
			wfRunHooks( 'UserLogoutComplete', array(&$wgUser, &$injected_html, $oldName) );
			
			$user = FacebookDB::getUser( $facebook->getUser() );
			if ( $user && $user->getId() ) {
				$this->login( $user );
				$this->sendPage('loginSuccessView');
			} else {
				$this->sendRedirect('UserLogin');
			}
			break;
		case 'MergeAccount':
			try {
				$this->manageMergeAccountPost($choice);
				$this->sendPage('displaySuccessAttachingView');
			} catch (FacebookUserException $e) {
				$this->sendError($e->getTitleMsg(), $e->getTextMsg(), $e->getMsgParams());
			}
			break;
		default:
			// Logged in status (ID, or 0 if not logged in) 
			$fbid = $facebook->getUser();
			/*
			// remove me
			global $wgOut;
			$wgOut->redirect( $facebook->getLogoutUrl() );
			return;
			/**/
			if ( empty( $fbid ) ) {
				// The user isn't logged in to Facebook
				if ( !$wgUser->isLoggedIn() ) {
					// The user isn't logged in to Facebook or MediaWiki. Nothing to see
					// here, move along
					$this->sendRedirect('UserLogin');
				} else {
					// The user is logged in to MediaWiki but not Facebook
					$this->sendPage('loginToFacebookView');
				}
			} else {
				// The user is logged in to Facebook
				$mwUser = FacebookDB::getUser($fbid);
				$mwId = $mwUser ? $mwUser->getId() : 0;
				if ( !$wgUser->isLoggedIn() ) {
					// The user is logged in to Facebook but not MediaWiki
					if ( !$mwId ) {
						// The Facebook user is new to MediaWiki
						$this->sendPage('connectNewUserView');
					} else {
						// The user is logged in to Facebook, but not MediaWiki. The
						// UserLoadAfterLoadFromSession hook might have failed if the user's
						// "remember me" option was disabled.
						
						$user = User::newFromId( $mwId );
						$this->login( $user ); // TODO: Make sure hook code is the same. And move both to FacebookUser, goddammit
						
						$this->sendPage('loginSuccessView');
					}
				} else {
					// The user is logged in to Facbook and MediaWiki
					if ( $mwId == $wgUser->getId() ) {
						// MediaWiki user belongs to the Facebook account. Nothing to see
						// here, move along
						$this->sendRedirect('UserLogin');
					} else {
						// Accounts don't agree
						$fb_ids = FacebookDB::getFacebookIDs($wgUser);
						if ( !$mwId ) {
							// Facebook user is new
							if ( count( $fb_ids ) == 0 ) {
								// MediaWiki user is free
								// Both accounts are free. Ask to merge
								$this->sendPage('mergeAccountView');
							} else {
								// MediaWiki user already associated with Facebook ID
								global $wgContLang;
								$param1 = '[[' . $wgContLang->getNsText( NS_USER ) . ":{$wgUser->getName()}|{$wgUser->getName()}]]";
								$userInfo = $facebook->getUserInfo();
								$param2 = $userInfo['name'];
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
	} // execute()
	
	/**
	 * @throws FacebookUserException
	 */
	private function manageChooseNamePost($choice) {
		global $wgRequest, $facebook;
		$fbid = $facebook->getUser();
		switch ($choice) {
			// Check to see if the user opted to connect an existing account
			case 'existing':
				$updatePrefs = array();
				foreach ($this->getAvailableUserUpdateOptions() as $option) {
					if ($wgRequest->getText("wpUpdateUserInfo$option", '0') == '1') {
						$updatePrefs[] = $option;
					}
				}
				$name = $wgRequest->getText('wpExistingName');
				$passwprd = $wgRequest->getText('wpExistingPassword');
				$this->attachUser($fbid, $name, $password, $updatePrefs);
				break;
				// Check to see if the user selected another valid option
			case 'nick':
			case 'first':
			case 'full':
				// Get the username from Facebook (Note: not from the form)
				$username = FacebookUser::getOptionFromInfo($choice . 'name', $facebook->getUserInfo());
				// no break
			case 'manual':
				// Use manual name if no username is set, even if manual wasn't chosen
				if ( empty($username) || !FacebookUser::userNameOK($username) )
					$username = $wgRequest->getText('wpName2');
				// If no valid username was found, something's not right; ask again
				if (empty($username) || !FacebookUser::userNameOK($username)) {
					throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
				}
				// no break
			case 'auto':
				if ( empty($username) ) {
					// We got here if and only if $choice is 'auto'
					$username = FacebookUser::generateUserName();
				}
				// Just in case the automatically-generated username is a bad egg
				if ( empty($username) || !FacebookUser::userNameOK($username) ) {
					throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
				}
				// Now that we got our username, create the user
				$this->createUser($fbid, $username);
				break;
			default:
				throw new FacebookUserException('facebook-invalid', 'facebook-invalidtext');
		}
	}
	
	/**
	 * @throws FacebookUserException
	 */
	private function manageMergeAccountPost() {
		global $wgUser, $wgRequest, $facebook;
		
		$fbid = $facebook->getUser();
		if ( !$fbid ) {
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		if ( !$wgUser->isLoggedIn() ) {
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		// Make sure both accounts are free in the database
		$mwUser = FacebookDB::getUser($fbid);
		$fb_ids = FacebookDB::getFacebookIDs($wgUser);
		if ( ($mwUser && $mwUser->getId()) || count($fb_ids) > 0 ) {
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		$updatePrefs = array();
		foreach ($this->getAvailableUserUpdateOptions() as $option) {
			if ($wgRequest->getText("wpUpdateUserInfo$option", '0') == '1') {
				$updatePrefs[] = $option;
			}
		}
		$name = $wgUser->getName();
		$passwprd = '';
		$this->attachUser($fbid, $name, $password, $updatePrefs);
	}
	
	/**
	 * Attaches the Facebook ID to an existing wiki account. If the user does
	 * not exist, or the supplied password does not match, then an error page
	 * is sent. Otherwise, the accounts are matched in the database and the new
	 * user object is logged in.
	 *
	 * NOTE: This isn't used by Wikia and hasn't been tested with some of the new
	 * code. Does it handle setting push-preferences correctly?
	 * 
	 * @throws FacebookUserException
	 */
	protected function attachUser($fbid, $name, $password, $updatePrefs) {
		global $wgUser;
		wfProfileIn(__METHOD__);
		
		// The user must be logged into Facebook before choosing a wiki username
		if ( !$fbid ) {
			wfDebug("Facebook: aborting in attachUser(): no Facebook ID was reported.\n");
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		// Look up the user by their name
		$user = User::newFromName($name, 'creatable');
		
		// If the user is logged in, we can skip the password check
		if ( !($wgUser->isLoggedIn() && $wgUser->getId() == $user->getId()) ) {
			if ( !$user->checkPassword($password) ) {
				throw new FacebookUserException('connectNewUserView', 'wrongpassword');
			}
		}
		
		// Attach the user to their Facebook account in the database
		FacebookDB::addFacebookID($user, $fbid);
		
		// Update the user with settings from Facebook
		if (count($updatePrefs)) {
			foreach ($updatePrefs as $option) {
				$user->setOption("facebookupdate-on-login-$option", '1');
			}
		}
		
		// Log in the user if they aren't already logged in
		$this->login($user);
		
		// User has been successfully attached and logged in
		wfRunHooks( 'SpecialConnect::userAttached', array( &$this ) );
		wfProfileOut(__METHOD__);
		return true;
	}
	
	/**
	 * @throws FacebookUserException
	 */
	protected function createUser($fb_id, $username) {
		global $wgUser, $wgFbDisableLogin, $wgAuth, $wgRequest, $wgMemc, $facebook;
		// Disabled. Because exceptions are used, an RAII model should be used here
		#wfProfileIn(__METHOD__);
		
		// Handle accidental reposts.
		if ( $wgUser->isLoggedIn() ) {
			return;
		}
		
		// Make sure we're not stealing an existing user account (it can't hurt to check twice)
		if ( empty( $username ) || !FacebookUser::userNameOK($username)) {
			wfDebug("Facebook: Name not OK: '$username'\n");
			// TODO: Provide an error message that explains that they need to pick a name or the name is taken.
			throw new FacebookUserException('connectNewUserView', 'facebook-invalidname');
		}
		
		/// START OF TYPICAL VALIDATIONS AND RESTRICITONS ON ACCOUNT-CREATION. ///
		
		// Check the restrictions again to make sure that the user can create this account.
		if ( wfReadOnly() ) {
			// Special case for readOnlyPage error
			throw new FacebookUserException(null, null);
		}
		
		if ( empty( $wgFbDisableLogin ) ) {
			// These two permissions don't apply in $wgFbDisableLogin mode because
			// then technically no users can create accounts
			if ( $wgUser->isBlockedFromCreateAccount() ) {
				wfDebug("Facebook: Blocked user was attempting to create account via Facebook Connect.\n");
				throw new FacebookUserException('facebook-error', 'facebook-errortext');
			} else {
				$titleObj = SpecialPage::getTitleFor( 'Connect' );
				$permErrors = $titleObj->getUserPermissionsErrors('createaccount', $wgUser, true);
				if (count($permErrors) > 0) {
					// Special case for permission errors
					throw new FacebookUserException($permErrors, 'createaccount');
				}
			}
		}
		
		// If we are not allowing users to login locally, we should be checking
		// to see if the user is actually able to authenticate to the authenti-
		// cation server before they create an account (otherwise, they can
		// create a local account and login as any domain user). We only need
		// to check this for domains that aren't local.
		$mDomain = $wgRequest->getText( 'wpDomain' );
		if ('local' != $mDomain && '' != $mDomain && !$wgAuth->canCreateAccounts() && !$wgAuth->userExists($username))
			throw new FacebookUserException('facebook-error', 'wrongpassword');
		
		// IP-blocking (and open proxy blocking) protection from SpecialUserLogin
		global $wgEnableSorbs, $wgProxyWhitelist;
		$ip = wfGetIP();
		if ($wgEnableSorbs && !in_array($ip, $wgProxyWhitelist) && $wgUser->inSorbsBlacklist($ip))
			throw new FacebookUserException('facebook-error', 'sorbs_create_account_reason');
		
		// Run a hook to let custom forms make sure that it is okay to proceed with processing the form.
		// This hook should only check preconditions and should not store values.  Values should be stored using the hook at the bottom of this function.
		// Can use 'this' to call sendPage('chooseNameFormView', 'SOME-ERROR-MSG-CODE-HERE') if some of the preconditions are invalid.
		if (!wfRunHooks( 'SpecialConnect::createUser::validateForm', array( &$this ) )) {
			return;
		}
		
		$user = User::newFromName($username);
		if (!$user) {
			wfDebug("Facebook: Error creating new user.\n");
			throw new FacebookUserException('facebook-error', 'facebook-error-creating-user');
		}
		// TODO: Make user a Facebook user here: $fbUser = new FacebookUser($user);
		
		// Let extensions abort the account creation.
		// NOTE: Currently this is commented out because it seems that most wikis might have a
		// handful of restrictions that won't be needed on Facebook Connections. For instance,
		// requiring a CAPTCHA or age-verification, etc. Having a Facebook account as a pre-
		// requisitie removes the need for that.
		/*
		$abortError = '';
		if( !wfRunHooks( 'AbortNewAccount', array( $user, &$abortError ) ) ) {
		// Hook point to add extra creation throttles and blocks
		wfDebug( "SpecialConnect::createUser: a hook blocked creation\n" );
		throw new FacebookUserException('facebook-error', 'facebook-error-user-creation-hook-aborted',
				array( $abortError ));
		}
		*/
		
		// Apply account-creation throttles
		global $wgAccountCreationThrottle;
		if ( $wgAccountCreationThrottle && $wgUser->isPingLimitable() ) {
			$key = wfMemcKey( 'acctcreate', 'ip', $ip );
			$value = $wgMemc->get( $key );
			if ( !$value ) {
				$wgMemc->set( $key, 0, 86400 );
			}
			if ( $value >= $wgAccountCreationThrottle ) {
				// 'acct_creation_throttle_hit' should actually use 'parseinline' not 'parse' in $wgOut->showErrorPage()
				throw new FacebookUserException('facebook-error', 'acct_creation_throttle_hit',
							array($wgAccountCreationThrottle));
			}
			$wgMemc->incr( $key );
		}
		
		/// END OF TYPICAL VALIDATIONS AND RESTRICITONS ON ACCOUNT-CREATION. ///
		
		// Fill in the info we know
		$userinfo = $facebook->getUserInfo();
		$email    = FacebookUser::getOptionFromInfo('email', $userinfo);
		$realName = FacebookUser::getOptionFromInfo('fullname', $userinfo);
		$pass     = null;
		
		// Create the account (locally on main cluster or via $wgAuth on other clusters)
		// $wgAuth essentially checks to see if these are valid parameters for new users
		if( !$wgAuth->addUser( $user, $pass, $email, $realName ) ) {
			wfDebug("Facebook: Error adding new user to database.\n");
			throw new FacebookUserException('facebook-error', 'facebook-errortext');
		}
		
		// Add the user to the local database (regardless of whether $wgAuth was used)
		// This is a custom version of similar code in SpecialUserLogin's LoginForm
		// with differences due to the fact that this code doesn't require a password, etc.
		global $wgExternalAuthType;
		if ( $wgExternalAuthType ) {
			$user = ExternalUser::addUser( $user, $pass, $email, $realName );
			if ( is_object( $user ) ) {
				$extUser = ExternalUser::newFromName( $username );
			}
		} else {
			$user->addToDatabase();
		}
		
		if ( !empty( $extUser ) && is_object( $extUser ) ) {
			$extUser->linkToLocal( $user->getId() );
			$extEmail = $extUser->getPref( 'emailaddress' );
			if ( !empty( $extEmail ) && empty( $email ) ) {
				$user->setEmail( $extEmail );
			}
		}
		
		$wgAuth->initUser( $user, true ); // $autocreate = true;
		$wgAuth->updateUser($user);
		
		// No passwords for Facebook accounts.
		/*
		if ( $wgAuth->allowPasswordChange() ) {
			$u->setPassword( $this->mPassword );
		}
		*/
		
		// Store which fields should be auto-updated from Facebook when the user logs in.
		$updateFormPrefix = 'wpUpdateUserInfo';
		foreach ($this->getAvailableUserUpdateOptions() as $option) {
			/*
			 if ($wgRequest->getVal($updateFormPrefix . $option, '') != '') {
			$user->setOption("facebook-update-on-login-$option", 1);
			} else {
			$user->setOption("facebook-update-on-login-$option", 0);
			}
			*/
			// Default all values to true
			$user->setOption("facebook-update-on-login-$option", 1);
		}
		
		// Process the FacebookPushEvent preference checkboxes if Push Events are enabled
		global $wgFbEnablePushToFacebook;
		if( !empty( $wgFbEnablePushToFacebook ) ) {
			global $wgFbPushEventClasses;
			if ( !empty( $wgFbPushEventClasses ) ) {
				foreach( $wgFbPushEventClasses as $pushEventClassName ) {
					$pushObj = new $pushEventClassName;
					$className = get_class();
					$prefName = $pushObj->getUserPreferenceName();
					$user->setOption($prefName, ($wgRequest->getCheck($prefName) ? '1' : '0'));
				}
			}
		
			// Save the preference for letting user select to never send anything to their newsfeed
			$prefName = FacebookPushEvent::$PREF_TO_DISABLE_ALL;
			$user->setOption($prefName, $wgRequest->getCheck($prefName) ? '1' : '0');
		}
		
		//$user->setOption( 'rememberpassword', $this->mRemember ? 1 : 0 );
		$user->setOption( 'rememberpassword', 1 );
		//$user->setOption( 'marketingallowed', $this->mMarketingOptIn ? 1 : 0 );
		$user->setOption( 'skinoverwrite', 1 );
		
		// Mark that the user is a Facebook user
		$user->addGroup('fb-user');
		
		// I think this should be done here
		$user->setToken();
		
		$user->saveSettings();
		
		// Update user count
		$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
		$ssUpdate->doUpdate();
		
		// Attach the user to their Facebook account in the database
		// This must be done up here so that the data is in the database before copy-to-local is done for sharded setups
		FacebookDB::addFacebookID($user, $fb_id);
		
		wfRunHooks( 'AddNewAccount', array( $user ) );
		
		// Log the user in
		$this->login($user);
		
		// Allow custom form processing to store values since this form submission was successful.
		// This hook should not fail on invalid input, instead check the input using the SpecialConnect::createUser::validateForm hook above.
		wfRunHooks( 'SpecialConnect::createUser::postProcessForm', array( &$this ) );
		
		$wgUser->addNewUserLogEntryAutoCreate();
	}
	
	/**
	 * Logs in the user by their Facebook ID. If the Facebook user doesn't have
	 * an account on the wiki, then they are presented with a form prompting
	 * them to choose a wiki username.
	 */
	protected function login($user) {
		global $wgUser;
		
		// Update user's info from Facebook
		$fbUser = new FacebookUser($user);
		$fbUser->updateFromFacebook();
		
		// Setup the session
		global $wgSessionStarted;
		if (!$wgSessionStarted) {
			wfSetupSession();
		}
		
		// Only log the user in if they aren't already logged in
		if ($user && $user->getId() != $wgUser->getId() ) {
			// TODO: calling setCookies() and load() might hit the database twice
			
			// Log the user in and store the new user as the global user object
			$user->setCookies();
			$wgUser = $user;
			
			// Similar to what's done in LoginForm::authenticateUserData().
			// Load $wgUser now. This is necessary because loading $wgUser (say
			// by calling getName()) calls the UserLoadFromSession hook, which
			// potentially creates the user in the local database.
			$sessionUser = User::newFromSession();
			$sessionUser->load();
			
			// Provide user interface in correct language immediately on this first page load
			global $wgLang;
			$wgLang = Language::factory( $wgUser->getOption( 'language' ) );
		}			
		return true;
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
		$html = '<div id="userloginForm"><form style="width: ' . $loginFormWidth . 'px;">' . "\n";
		
		if ( !count( $fb_ids ) ) {
			// This message was added recently and might not be translated
			// In that case, fall back to an older, similar message
			$formTitle = wfMsg( 'facebook-merge-title' );
			// This test probably isn't correct. I'm open to ideas
			if ($formTitle == "&lt;facebook-merge-title&gt;") {
				$formTitle = wfMsg( 'facebook-log-in' );
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
		// TODO: bring back:
		//     FacebookInit::getPermissionsAttribute()
		//     FacebookInit::getOnLoginAttribute()
		$html .= '<fb:login-button show-faces="true" width="' . $loginFormWidth .
					'" max-rows="3" scope="email"></fb:login-button><br/><br/><br/>' . "\n";
		
		// Add a pretty Like box to entice the user to log in
		$html .= '<fb:like href="' . Title::newMainPage()->getFullURL() . '" send="false" width="' .
					 $loginFormWidth . '" show_faces="true"></fb:like>' . "\n";
		$html .= '</form></div>';
		$wgOut->addHTML($html);
		
		// TODO: Add a returnto link
	}
	
	/**
	 * The user is logged in to Facebook, but not MediaWiki. The Facebook user
	 * is new to MediaWiki.
	 */
	private function connectNewUserView($messagekey = 'facebook-chooseinstructions') {
		/**
		 * TODO: Add an option to disallow this extension to access your Facebook
		 * information. This option could simply point you to your Facebook privacy
		 * settings. This is necessary in case the user wants to perpetually browse
		 * the wiki anonymously, while still being logged in to Facebook.
		 *
		 * NOTE: The above might be done now that we have checkboxes for which options
		 * to update from fb. Haven't tested it though.
		 */
		global $wgUser, $facebook, $wgOut, $wgFbDisableLogin;
		
		$titleObj = SpecialPage::getTitleFor( 'Connect' );
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
		
		// Connect to the Facebook API
		$userinfo = $facebook->getUserInfo();
		
		// Keep track of when the first option visible to the user is checked
		$checked = false;
		
		// Outputs the canonical name of the special page at the top of the page
		$this->outputHeader();
		// If a different $messagekey was passed (like 'wrongpassword'), use it instead
		$wgOut->addWikiMsg( $messagekey );
		// TODO: Format the html a little nicer
		$wgOut->addHTML('
				<form action="' . $this->getTitle('ChooseName')->getLocalUrl() . '" method="POST">
				<fieldset id="mw-facebook-choosename">
				<legend>' . wfMsg('facebook-chooselegend') . '</legend>
				<table>');
		// Let them attach to an existing. If $wgFbDisableLogin is true, then
		// stand-alone account aren't allowed in the first place
		if (empty( $wgFbDisableLogin )) {
			// Grab the UserName from the cookie if it exists
			global $wgCookiePrefix;
			$name = isset($_COOKIE["{$wgCookiePrefix}UserName"]) ? trim($_COOKIE["{$wgCookiePrefix}UserName"]) : '';
			
			$updateChoices = $this->getUpdateOptions(true);
			
			// Create the HTML for the "existing account" option
			$html = '<tr><td class="wm-label"><input name="wpNameChoice" type="radio" ' .
			        'value="existing" id="wpNameChoiceExisting"/></td><td class="mw-input">' .
			        '<label for="wnNameChoiceExisting">' . wfMsg('facebook-chooseexisting') . '<br/>' .
			        wfMsgHtml('facebook-chooseusername') . '<input name="wpExistingName" size="16" value="' .
			        $name . '" id="wpExistingName"/>' . wfMsgHtml('facebook-choosepassword') .
			        '<input name="wpExistingPassword" ' . 'size="" value="" type="password"/>' . $updateChoices .
			        '</td></tr>';
			
			$wgOut->addHTML($html);
		}
	
		// Add the options for nick name, first name and full name if we can get them
		// TODO: Wikify the usernames (i.e. Full name should have an _ )
		foreach (array('nick', 'first', 'full') as $option) {
			$nickname = FacebookUser::getOptionFromInfo($option . 'name', $userinfo);
			if ($nickname && FacebookUser::userNameOK($nickname)) {
				$wgOut->addHTML('<tr><td class="mw-label"><input name="wpNameChoice" type="radio" value="' .
						$option . ($checked ? '' : '" checked="checked') . '" id="wpNameChoice' . $option .
						'" /></td><td class="mw-input"><label for="wpNameChoice' . $option . '">' .
						wfMsg('facebook-choose' . $option, $nickname) . '</label></td></tr>');
				// When the first radio is checked, this flag is set and subsequent options aren't checked
				$checked = true;
			}
		}
	
		// The options for auto and manual usernames are always available
		$wgOut->addHTML('<tr><td class="mw-label"><input name="wpNameChoice" type="radio" value="auto" ' .
				($checked ? '' : 'checked="checked" ') . 'id="wpNameChoiceAuto" /></td><td class="mw-input">' .
				'<label for="wpNameChoiceAuto">' . wfMsg('facebook-chooseauto', FacebookUser::generateUserName()) .
				'</label></td></tr><tr><td class="mw-label"><input name="wpNameChoice" type="radio" ' .
				'value="manual" id="wpNameChoiceManual" /></td><td class="mw-input"><label ' .
				'for="wpNameChoiceManual">' . wfMsg('facebook-choosemanual') . '</label>&nbsp;' .
				'<input name="wpName2" size="16" value="" id="wpName2" /></td></tr>');
		// Finish with two options, "Log in" or "Cancel"
		$wgOut->addHTML('<tr><td></td><td class="mw-submit">' .
				'<input type="submit" value="Log in" name="wpOK" />' .
				'<input type="submit" value="Cancel" name="wpCancel" />');
		// Include returnto and returntoquery parameters if they are set
		if (!empty($this->mReturnTo)) {
			$wgOut->addHTML('<input type="hidden" name="returnto" value="' .
					$this->mReturnTo . '" />');
			// Only need returntoquery if returnto is set
			if (!empty($this->mReturnToQuery)) {
				$wgOut->addHTML('<input type="hidden" name="returntoquery" value="' .
						$this->mReturnToQuery . '" />');
			}
		}
		
		$wgOut->addHTML("</td></tr></table></fieldset></form>\n\n");
	}
	
	/**
	 * TODO: Document me
	 */
	private function getUpdateOptions($initialHidden = false) {
		global $facebook;
		$userinfo = $facebook->getUserInfo();
		
		// Build an array of attributes to update
		$updateOptions = array();
		foreach ($this->getAvailableUserUpdateOptions() as $option) {
			
			// Translate the MW parameter into a FB parameter
			$value = FacebookUser::getOptionFromInfo($option, $userinfo);
			
			// If no corresponding value was received from Facebook, then continue
			if (!$value) {
				continue;
			}
			
			// Build the list item for the update option
			$item  = '<li>';
			$item .= '<input name="wpUpdateUserInfo' . $option . '" type="checkbox" ' .
			         'value="1" id="wpUpdateUserInfo' . $option . '" checked="checked" />';
			$item .= '<label for="wpUpdateUserInfo' . $option . '">' . wfMsgHtml("facebook-$option") .
			         wfMsgExt('colon-separator', array('escapenoentities')) . " <i>$value</i></label></li>";
			$updateOptions[] = $item;
		}
		$updateChoices = '';
		// Implode the update options into an unordered list
		if ( count($updateOptions) > 0 ) {
			$updateChoices .= "<br />\n";
			$updateChoices .= '<div id="mw-facebook-choosename-update" class="' . ($initialHidden ? 'fbInitialHidden' : '') .
			                  '">' . wfMsgHtml('facebook-updateuserinfo') . "\n";
			$updateChoices .= "<ul>\n" . implode("\n", $updateOptions) . "\n</ul></div>\n";
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
		global $wgOut, $wgUser, $facebook, $wgSitename;
		$wgOut->setPageTitle(wfMsg('facebook-merge-title'));
		
		$html  = '<form action="' . $this->getTitle('MergeAccount')->getLocalUrl() . '" method="POST">' . "\n";
		$html .= '<fieldset id="mw-facebook-chooseoptions">' . "\n";
		$html .= '<legend>' . wfMsg('facebook-updatelegend') . "</legend>\n";
		$html .= wfMsgExt('facebook-merge-text', 'parse', array('$1' => $wgSitename ));
		// TODO
		//$html .= '<p>Not $user? Log in as a different facebook user...</p>';
		$html .= '<input type="submit" value="' . wfMsg( 'facebook-merge-title' ) . '" /><br/>' . "\n";
		$html .= $this->getUpdateOptions(false);
		if ( !empty( $this->mReturnTo ) ) {
			$html .= '<input type="hidden" name="returnto" value="' . $this->mReturnTo . '" />' . "\n";
			// Only need returntoquery if returnto is set
			if ( !empty( $this->mReturnToQuery ) ) {
				$html .= '<input type="hidden" name="returntoquery" value="' . $this->mReturnToQuery . '" />' . "\n";
			}
		}
		$html .= "</fieldset></form><br/>\n";
		$wgOut->addHTML($html);
		
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $this->mReturnTo, $this->mReturnToQuery);
		$wgOut->addHTML('<br/>' . "\n");
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
		global $wgOut, $facebook, $wgContLang;
		
		$wgOut->setPageTitle(wfMsg('facebook-logout-and-continue'));
		
		$html = '';
		
		$profile = $facebook->getUserInfo();
		if ( $profile && isset($profile['first_name']) ) {
			$html = '<p>' . wfMsg('facebook-welcome-name', array('$1' => $profile['first_name'])) . "</p>\n";
		}
		
		$username = User::newFromId($userId)->getName();
		$html .= wfMsgExt('facebook-continue-text', 'parse', array(
			'$1' => '[[' . $wgContLang->getNsText( NS_USER ) . ":$username|$username]]"
		));
		$html .= '<form action="' . $this->getTitle('LogoutAndContinue')->getLocalUrl() . '" method="post">' . "\n";
		$html .= '<input type="submit" value="' . wfMsg( 'facebook-continue-button' ) . '"/>' . "\n";
		if ( !empty( $this->mReturnTo ) ) {
			$html .= '<input type="hidden" name="returnto" value="' . $this->mReturnTo . '"/>' . "\n";
			// Only need returntoquery if returnto is set
			if ( !empty( $this->mReturnToQuery ) ) {
				$html .= '<input type="hidden" name="returntoquery" value="' . $this->mReturnToQuery . '"/>' . "\n";
			}
		}
		$html .= "</form><br/>\n";
		
		// TODO
		//$html .= '<p>Not $user? Log in as a different facebook user...</p>';
		
		$wgOut->addHTML( $html );
		
		// Render the "Return to" text retrieved from the URL
		$wgOut->returnToMain(false, $this->mReturnTo, $this->mReturnToQuery);
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
			/**/
			$titleObj = Title::newMainPage();
			$wgOut->redirect( $titleObj->getFullURL('fbconnected=1&cb=' . rand(1, 10000)) );
		}
		
		wfProfileOut(__METHOD__);
	}
	
	
	
	
	
	
	
	
	/**
	 * Disconnect from Facebook.
	 *
	private function disconnectReclamationActionView() {
		global $wgRequest, $wgOut, $facebook;
	
		$wgOut->setArticleRelated( false );
		$wgOut->enableClientCache( false );
		$wgOut->mRedirect = '';
		$wgOut->mBodytext = '';
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
	
		$fb_user_id = $wgRequest->getVal('u', 0);
		$hash = $wgRequest->getVal('h', '');
		$user_id = $facebook->verifyAccountReclamation($fb_user_id, $hash);
	
		if (!($user_id === false)) {
			$result = FacebookInit::coreDisconnectFromFB($user_id);
		}
	
		$title = Title::makeTitle( NS_SPECIAL, 'Signup' );
	
		$html = Xml::openElement('a', array( 'href' => $title->getFullUrl() ));
		$html .= $title->getPrefixedText();
		$html .= Xml::closeElement( 'a' );
	
		if ( (!($user_id === false)) && ($result['status'] == 'ok') ) {
			$wgOut->setPageTitle( wfMsg('facebook-reclamation-title') );
			$wgOut->setHTMLTitle( wfMsg('facebook-reclamation-title') );
			$wgOut->addHTML( wfMsg('facebook-reclamation-body', array('$1' => $html) ));
	
		} else {
			$wgOut->setPageTitle( wfMsg('facebook-reclamation-title-error') );
			$wgOut->setHTMLTitle( wfMsg('facebook-reclamation-title-error') );
			$wgOut->addHTML( wfMsg('facebook-reclamation-body-error', array('$1' => $html) ));
		}
	
		return true;
	}
	
	/**
	 * This is called when a user is logged into a Wikia account and has just gone through the Facebook Connect popups,
	 * but has not been connected inside the system.
	 *
	 * This function will connect them in the database, save default preferences and present them with "Congratulations"
	 * message and a link to modify their User Preferences. TODO: SHOULD WE JUST SHOW THE CHECKBOXES AGAIN?
	 * 
	 * This is different from attachUser because that is made to synchronously test a login at the same time as creating
	 * the account via the ChooseName form.  This function, however, is designed for when the existing user is already logged in
	 * and wants to quickly connect their Facebook account.  The main difference, therefore, is that this function uses default
	 * preferences while the other form should have already shown the preferences form to the user.
	 *
	public function connectExistingView() {
		global $wgUser, $facebook;
		wfProfileIn(__METHOD__);
		
		// Store the facebook-id <=> mediawiki-id mapping.
		// TODO: FIXME: What sould we do if this fb_id is already connected to a DIFFERENT mediawiki account.
		$fb_id = $facebook->getUser();
		FacebookDB::addFacebookID($wgUser, $fb_id);
		
		// Save the default user preferences.
		global $wgFbEnablePushToFacebook;
		if (!empty( $wgFbEnablePushToFacebook )) {
			global $wgFbPushEventClasses;
			if (!empty( $wgFbPushEventClasses )) {
				$DEFAULT_ENABLE_ALL_PUSHES = true;
				foreach($wgFbPushEventClasses as $pushEventClassName) {
					$pushObj = new $pushEventClassName;
					$prefName = $pushObj->getUserPreferenceName();
					
					$wgUser->setOption($prefName, $DEFAULT_ENABLE_ALL_PUSHES ? '1' : '0');
				}
			}
		}
		$wgUser->setOption('fbFromExist', '1');
		$wgUser->saveSettings();
		
		wfRunHooks( 'SpecialConnect::userAttached', array( &$this ) );
		
		$this->sendPage('displaySuccessAttachingView');
		wfProfileOut(__METHOD__);
	}
	
	
	/**
	 * Check to see if the user can create a Facebook-linked account.
	 *
	function checkCreateAccount() {
		global $wgUser, $facebook;
		// Response object to send return to the client
		$response = new AjaxResponse();
		
		$fb_user = $facebook->getUser();
		if (empty($fb_user)) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 1,
				'message' => 'User is not logged into Facebook',
			)));
			return $response;
		}
		if(( (int)$wgUser->getId() ) != 0) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 2,
				'message' => 'User is already logged into the wiki',
			)));
			return $response;
		}
		if( FacebookDB::getUser($fb_user) != null) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 3,
				'message' => 'This Facebook account is connected to a different user',
			)));
			return $response;
		}
		if ( wfReadOnly() ) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 4,
				'message' => 'The wiki is in read-only mode',
			)));
			return $response;
		}
		if ( $wgUser->isBlockedFromCreateAccount() ) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 5,
				'message' => 'User does not have permission to create an account on this wiki',
			)));
			return $response;
		}
		$titleObj = SpecialPage::getTitleFor( 'Connect' );
		if ( count( $permErrors = $titleObj->getUserPermissionsErrors( 'createaccount', $wgUser, true ) ) > 0 ) {
			$response->addText(json_encode(array(
				'status' => 'error',
				'code' => 6,
				'message' => 'User does not have permission to create an account on this wiki',
			)));
			return $response;
		}
		
		// Success!
		$response->addText(json_encode(array('status' => 'ok')));
		return $response;
	}
	
	/**
	 * 
	 * 
	 *
	function ajaxModalChooseName() {
		global $wgRequest;
		wfLoadExtensionMessages('Facebook');
		$response = new AjaxResponse();
		
		$specialConnect = new SpecialConnect();
		$form = new ChooseNameForm($wgRequest, 'signup');
		$form->mainLoginForm( $specialConnect, '' );
		$tmpl = $form->getAjaxTemplate();
		$tmpl->set('isajax', true);
		ob_start();
		$tmpl->execute();
		$html = ob_get_clean();
		
		$response->addText( $html );
		return $response;
	}
	/**/
}
