<?php
/*
 * Copyright © 2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Base class for form queries.
 * 
 * Subclasses must implement the execute() method.
 */
abstract class ApiFacebookFormQuery extends ApiBase {
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	public function getVersion() {
		return __CLASS__ . ': ' . MEDIAWIKI_FACEBOOK_VERSION;
	}
	
	public function getDescription() {
		return array(
			'Retrieve form over AJAX.'
		);
	}
	
	/**
	 * Only 'id' is required. 'id' is used to determine whether the user
	 * already has an account on the wiki. The available additional info is
	 * passed on to the form.
	 */
	public function getAllowedParams() {
		$type_required = array(
			ApiBase::PARAM_TYPE => 'string',
			ApiBase::PARAM_REQUIRED => true,
			ApiBase::PARAM_ISMULTI => false,
		);
		$type_not_required = array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI => false,
		);
		return array(
			'id'         => $type_required,
			'name'       => $type_not_required,
			'first_name' => $type_not_required,
			'last_name'  => $type_not_required,
			'username'   => $type_not_required,
			'gender'     => $type_not_required,
			'locale'     => $type_not_required,
			'timezone'   => $type_not_required,
			'email'      => $type_not_required,
		);
	}
	
	/**
	 * Nope.
	 */
	public function getParamDescription() {
		return array(
			'id'         => '',
			'name'       => '',
			'first_name' => '',
			'last_name'  => '',
			'username'   => '',
			'gender'     => '',
			'locale'     => '',
			'timezone'   => '',
			'email'      => '',
		);
	}
	
	/**
	 * Indicates whether this module must be called with a POST request
	 */
	public function mustBePosted() {
		return true;
	}
}

/**
 * API module to choose a username for new Facebook users.
 * 
 * If the user exists, return an empty response, just as if an error had
 * occurred. This will invoke the fallback action, redirecting the user to
 * Special:Connect, which is what we want to do anyways. Returning a choose
 * name form simply allows us to short-cut the process.
 */
class ApiFacebookChooseName extends ApiFacebookFormQuery {
	public function execute() {
		global $wgFbStreamlineLogin;
		
		if ( !empty( $wgFbStreamlineLogin ) ) {
			$params = $this->extractRequestParams();
			$fbUser = new FacebookUser($params['id']);
			
			if ( !$fbUser->getMWUser()->getId() ) {
				// wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
				$specialConnect = new SpecialConnect();
				$this->getResult()->addValue(null, null, $specialConnect->getChooseNameForm($params));
			}
		}
	}
}

/**
 * API module for existing MediaWiki users to merge their username with a
 * Facebook account.
 * 
 * If the user exists, return an empty response, just as if an error had
 * occurred. This will invoke the fallback action, redirecting the user to
 * Special:Connect, which is what we want to do anyways. Returning a choose
 * name form simply allows us to short-cut the process.
 */
class ApiFacebookMergeAccount extends ApiFacebookFormQuery {
	public function execute() {
		global $wgFbStreamlineLogin;
		
		if ( !empty( $wgFbStreamlineLogin ) ) {
			$params = $this->extractRequestParams();
			$fbUser = new FacebookUser($params['id']);
			
			if ( !$fbUser->getMWUser()->getId() ) {
				//wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
				$specialConnect = new SpecialConnect();
				$this->getResult()->addValue(null, null, $specialConnect->getMergeAccountForm($params));
			}
		}
	}
}

/**
 * To logout-and-continue, or not to logout-and-continue, that is the question.
 * 
 * If the logged-in Facebook user doesn't have a MediaWiki account, this api
 * query will return an empty form and the user will be redirected to
 * Special:Connect. Because there doesn't exist a LogoutAndCreateNewUser form
 * (yet), an error message will be shown prompting the user to log out.
 */
class ApiFacebookLogoutAndContinue extends ApiFacebookFormQuery {
	public function execute() {
		global $wgFbStreamlineLogin;
		
		if ( !empty( $wgFbStreamlineLogin ) ) {
			$params = $this->extractRequestParams();
			$fbUser = new FacebookUser($params['id']);
			$id = $fbUser->getMWUser()->getId();
			
			if ( $id ) {
				//wfLoadExtensionMessages('Facebook'); // Deprecated since 1.16
				$specialConnect = new SpecialConnect();
				$this->getResult()->addValue(null, null, $specialConnect->getLogoutAndContinueForm($params, $id));
			} else {
				// TODO: Add a LogoutAndCreateNewUser form to SpecialConnect.php. For
				// now, return an empty response to send user to Special:Connect
				// (which displays an error message).
			}
		}
	}
}
