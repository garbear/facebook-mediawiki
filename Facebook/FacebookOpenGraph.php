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


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class OpenGraphObject.
 * 
 * Objects represent the type of things that users can connect with in your
 * application. See:
 * https://developers.facebook.com/docs/beta/opengraph/#actions-objects
 */
abstract class OpenGraphObject {
	/**
	 * An array of prefixed properties e.g. og:type, og:url.
	 */
	protected $properties = array();
	
	/**
	 * Constructor. Adds properties inherent to the application:
	 *    fb:app_id   The application ID
	 *    fb:page_id  The application's page ID (or a custom page)
	 * 
	 * The page ID is only added if one has been defined by $wgFbPageId.
	 */
	function __construct() {
		global $wgFbAppId, $wgFbPageId;
		
		// fb:app_id
		$this->properties['fb:app_id'] = $wgFbAppId;
		
		// fb:page_id
		if ( !empty( $wgFbPageId ) && $wgPageId != 'YOUR_PAGE_ID' ) {
			$this->properties['fb:page_id'] = $wgFbPageId;
		}
	}
	
	/**
	 * Returns the URL of the object.
	 * 
	 * This URL is scraped by Facebook when connecting the object to the
	 * user's Graph.
	 */
	public function getUrl() {
		return $this->propertes['og:url'];
	}
	
	/**
	 * Returns the translated type of the object.
	 * 
	 * If a namespace has been defined and $wgFbOpenGraphRegisteredObjects
	 * contains a map for the object type, the resulting type will be
	 * NAMESPAPCE:TYPE. Otherwise, the generic type is used.
	 */
	public function getType() {
		return $this->properties['og:type'];
	}
	
	/**
	 * Returns the properties of an object as specified by the Open Graph
	 * protocol. These properties can vary from object to object.
	 * 
	 * Properties with the fb: prefix are common to all objects associated with
	 * with application (fb:app_id and fb:page_id).
	 */
	public function getProperties() {
		return $this->properties;
	}
	
	/**
	 * Given a page title, returns the Open Graph representation.
	 * @param Title $title
	 * @return OpenGraphObject|NULL
	 */
	public static function newFromTitle($title) {
		if ( $title instanceof Title ) {
			// Don't consider NS_SPECIAL and NS_MEDIA to be articles
			if ( $title->canExist() ) {
				return new OpenGraphArticleObject( $title );
			}
		}
		return NULL;
	}
	
	/**
	 * TODO: Given an image, returns the Open Graph representation.
	 * 
	 * When this function is implemented, it may be renamed to newFromMedia()
	 * or newFromFile().
	 * 
	 * @return OpenGraphObject|NULL
	 */
	public static function newFromFile($item) {
		// TODO: return new OpenGraph[File|Image|Media]Object( $title ); 
		return NULL;
	}
	
	/**
	 * Performs the translation between generic Open Graph object types and
	 * custom ones defined by this Facebook application.
	 * 
	 * @param string $generic
	 */
	protected function translateType( $generic ) {
		global $wgFbNamespace, $wgFbOpenGraphRegisteredObjects;
		
		if ( FacebookAPI::isNamespaceSetup() && !empty( $wgFbOpenGraphRegisteredObjects ) &&
				!empty( $wgFbOpenGraphRegisteredObjects[$generic] ) ) {
			return $wgFbNamespace . ':' . $wgFbOpenGraphRegisteredObjects[$generic];
		}
		return $generic;
	}
}

class OpenGraphArticleObject extends OpenGraphObject {
	
	private $type = 'article';
	
	function __construct( $title ) {
		global $wgVersion, $wgSitename, $wgServer, $wgLogo;
		
		// Talk pages redirect to subject pages
		if ( $title->isTalkPage() ) {
			$title = $title->getSubjectPage();
		}
		
		$pageName = $title->getPrefixedText();
		if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
			// Title::getPageLanguage() is since 1.18
			$langCode = $title->getPageLanguage()->getCode();
		} else {
			global $wgContLang;
			$langCode = $wgContLang->getCode();
		}
		$article = new Article( $title, 0 );
		$timestamp = $article->getTimestamp( TS_UNIX, $article->getTimestamp() );
		
		$this->properties['og:type']         = $this->translateType( $this->type );
		$this->properties['og:site_name']    = $wgSitename;
		$this->properties['og:title']        = $pageName;
		$this->properties['og:url']          = Title::newFromText( $pageName )->getFullURL(); // no additional params
		/*
		 * TODO. This algorithm should work for vector: Start inside the rendered
		 * page body, <div id="bodyContent">. Next, enter the actual body content,
		 * <div class="mw-content-ltr">. Then, skip all tags that are not <p>. Now
		 * that we have come to the actual page content, obtain the first sentence
		 * inside the <p> tag and strip away all html tags.
		 */
		//$this->properties['og:description']  = '';
		// TODO: use first image on page, otherwise default to $wgLogo
		$this->properties['og:image']        = $wgServer . $wgLogo;
		$this->properties['og:locale']       = FacebookLanguage::getFbLocaleForLangCode($langCode);
		$this->properties['og:updated_time'] = $timestamp;
		
		parent::__construct();
	}
}
