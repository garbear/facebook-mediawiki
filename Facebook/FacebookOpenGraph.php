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
 * Class FacebookOpenGraph
 */
class FacebookOpenGraph {
	/**
	 * Register the <opengraph> tag with the parser. This function is called
	 * by the LanguageGetMagic hook in FacebookHooks.php.
	 * 
	 * English is the default language. In the future, more languages might
	 * be added.
	 */
	public static function GetTag() {
		global $wgFbOpenGraph;
		if ( !empty( $wgFbOpenGraph ) ) {
			switch ( $langCode ) {
				default:
					return 'opengraph';
			}
		}
		return '';
	}
	
	/**
	 * Parser hook for the <opengraph> tag.
	 * 
	 * This tag is dual-purpose. By specifying the "type" attribute, the type
	 * of Open Graph object represented by the wiki page can be set. The
	 * additional attributes set the <meta> properties for the object. Even
	 * if "type" isn't specified, the other attributes can still be used to
	 * override the default properties.
	 * 
	 * If the "action" attribute is given, this tag will render a link that,
	 * when clicked, adds an action to the Facebook user's Timeline with the
	 * wiki page as the target object. If no action is given, $innertext is
	 * discarded.
	 * 
	 * TODO: actions
	 */
	public static function parserHook($innertext, array $args, Parser $parser, PPFrame $frame) {
		global $wgFbOpenGraph;
		if ( empty( $wgFbOpenGraph ) ) {
			return ''; // Open Graph is disabled for this wiki
		}
		
		if ( isset( $args['action'] ) ) {
			$action = $args['action'];
			unset( $args['action'] );
		} else {
			$action = '';
		}
		
		if ( count( $args ) ) {
			foreach ( $args as $key => $value ) {
				// Check if $value is a wiki link
				$url = '';
				if (substr($value, 0, 2) == '[[' && substr($value, strlen($value)-2, 2) == ']]') {
					$pageName = substr( $value, 2, strlen($value) - 4 );
					$title = Title::newFromText( $pageName );
					if ( $title instanceof Title ) {
						$url = $title->getFullURL();
					}
				}
				$value = ( $url != '' ? $url : $parser->recursiveTagParse( $value, $frame ) );
				$args[$key] = htmlspecialchars( $value );
			}
			self::registerProperties($parser->getTitle(), $args);
		}
		
		// TODO: Insert a placeholder so that actions can be placed above type definitions on the page
		if ( $action != '' ) {
			// See if the action is available for the object type
			$actions = self::getActions(self::newObjectFromTitle($parser->getTitle()));
			if ( count( $actions ) && in_array( $action, $actions ) ) {
				return '<a href="#' . $action . '">' . $parser->recursiveTagParse( $innertext, $frame ) . '</a>';
			}
		}
		return '';
	}
	
	/**
	 * Given a page title, returns the Open Graph representation.
	 * 
	 * TODO: Cache objects using their title object as a key. This will need to
	 * be done in such a way that registerProperties() invalidates the cache
	 * (because it might change an object's properties).
	 * 
	 * @param Title $title
	 * @return OpenGraphObject|NULL
	 */
	public static function newObjectFromTitle($title) {
		return OpenGraphObject::newFromTitle( $title );
	}
	
	/**
	 * Returns the actions that are registered for the given object.
	 */
	static $customObjects = NULL;
	public static function getActions($openGraphObject) {
		$actions = array();
		
		// Invert the array the first time through
		if ( self::$customObjects === NULL ) {
			self::$customObjects = array();
			global $wgFbOpenGraphCustomActions;
			foreach ( $wgFbOpenGraphCustomActions as $action => $objects ) {
				if ( is_string( $objects ) ) {
					$objects = array( $objects );
				}
				if ( is_array( $objects ) ) {
					foreach ( $objects as $object ) {
						if ( !isset( self::$customObjects[$object] ) ) {
							self::$customObjects[$object] = array( $action );
						} else {
							self::$customObjects[$object][] = $action;
						}
					}
				}
			}
		}
		
		if ( $openGraphObject ) {
			$type = $openGraphObject->getType();
			// Start with actions matching the specified object
			if ( isset( self::$customObjects[$type] ) ) {
				$actions = self::$customObjects[$type];
			}
			// Merge in actions matching all objects ('*')
			if ( isset( self::$customObjects['*'] ) ) {
				$actions = array_merge( self::$customObjects['*'], $actions );
			}
		}
		
		return $actions;
	}
	
	/**
	 * Register an object discovered by the <opengraph> tag. The object is
	 * hashed against the page's title. When OpenGraphObject::newFromTitle()
	 * is called, the registered titles are considered when constructing the
	 * Open Graph object; either a custom type is used, or the <opengraph> tag
	 * is used to override individual parameters.
	 */
	static $registeredObjects = array();
	public static function registerProperties($title, $properties) {
		if ( $title instanceof Title ) {
			foreach ( self::$registeredObjects as $i => $object ) {
				// Check if a tag was already recorded for this title
				if ( $title->equals( $object['title'] ) ) {
					// If it's a different type, ignore it
					if (isset($object['properties']['type']) && isset($properties['type']) &&
							$object['properties']['type'] != $properties['type'])
						return;
					// Only update undefined properties
					foreach ( $properties as $name => $value ) {
						if ( !isset( self::$registeredObjects[$i]['properties'][$name] ) ) {
							self::$registeredObjects[$i]['properties'][$name] = $value;
						}
					}
					return;
				}
			}
			// If we got here, no title was found
			self::$registeredObjects[] = array(
					'title' => $title,
					'properties' => $properties,
			);
		}
		return;
	}
	
	/**
	 * Gets a list of properties based on <opengraph> tags in the specified title.
	 */
	public static function resolveTitle($title) {
		if ( $title instanceof Title ) {
			foreach ( self::$registeredObjects as $object ) {
				if ( $title->equals( $object['title'] ) ) {
					return $object['properties'];
				}
			}
		}
		return array();
	}
}


/**
 * Class OpenGraphObject
 * 
 * Objects represent the type of things that users can connect with in your
 * application. See:
 * https://developers.facebook.com/docs/beta/opengraph/#actions-objects
 */
abstract class OpenGraphObject {
	
	public static function getBuiltinProperties() {
		return array(
			'fb:app_id',
			'fb:page_id',
			'og:sitename',
		);
	}
	
	/**
	 * Given a page title, returns the Open Graph representation.
	 * @param Title $title
	 * @return OpenGraphObject|NULL
	 */
	public static function newFromTitle($title) {
		global $wgFbNamespace;
		
		if ( $title instanceof Title ) {
			// Don't consider NS_SPECIAL and NS_MEDIA to be articles
			if ( $title->canExist() ) {
				// Talk pages redirect to subject pages
				if ( $title->isTalkPage() ) {
					// TODO: Parse subject page to extract <opengraph> attributes
					$title = $title->getSubjectPage();
				}
				/*
				// Strip parameters from the title's URL
				$baseTitle = Title::newFromText( $title->getPrefixedText() );
				if ( $baseTitle instanceof Title ) {
					$title = $baseTitle;
				}
				*/
				
				$parameters = FacebookOpenGraph::resolveTitle( $title );
				
				// Prepend Open Graph properties with 'og:' or the app namespace
				foreach ( $parameters as $name => $value ) {
					unset( $parameters[$name] );
					// Unset properties with no content
					if ( empty( $value ) )
						continue;
					// Ignore parameters starting with fb:
					if ( substr( $name, 0, 3 ) == 'fb:' )
						continue;
					// Prepent og: to built-in properties and namespace: to custom properties
					if (in_array('og:' . $name, OpenGraphArticleObject::getBuiltinProperties())) {
						$parameters['og:' . $name] = $value;
					} else if ( FacebookAPI::isNamespaceSetup() ) {
						$parameters[$wgFbNamespace . ':' . $name] = $value;
					}
				}
				
				// Need to prepend the custom type with the namespace
				if ( isset( $parameters['og:type'] ) ) {
					if ( FacebookAPI::isNamespaceSetup() ) {
						$parameters['og:type'] = $wgFbNamespace . ':' . $parameters['og:type'];
					}
				}
				return new OpenGraphArticleObject( $title, $parameters );
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
		global $wgFbAppId, $wgFbPageId, $wgSitename;
		
		// fb:app_id
		$this->properties['fb:app_id'] = $wgFbAppId;
		
		// fb:page_id
		if ( !empty( $wgFbPageId ) && $wgPageId != 'YOUR_PAGE_ID' ) {
			$this->properties['fb:page_id'] = $wgFbPageId;
		}
		
		// og:sitename
		$this->properties['og:site_name'] = $wgSitename;
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
	 * Returns the type of the object, minus the preceeding namespace.
	 */
	public function getType() {
		$parts = explode( ':', $this->properties['og:type'], 2 );
		return count( $parts ) >= 2 ? $parts[1] : $parts[0];
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
	 * Set a number of properties. $args is of the form array($property => $content).
	 *  
	 */
	protected function setProperties($args, $override = false) {
		foreach ( $args as $name => $value ) {
			if ( $override || !isset( $this->properties[$name] ) ) {
				$this->properties[$name] = $value;
			}
		}
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
	
	private $genericType = 'article';
	
	public static function getBuiltinProperties() {
		return array_merge( array(
			'og:type',
			'og:url',
			'og:updated_time',
			'og:locale',
			'og:title',
			'og:image',
			'og:description',
		), parent::getBuiltinProperties() );
	}
	
	function __construct( $title, $override = array() ) {
		// og:type
		if ( !isset( $override['og:type'] ) ) {
			$this->properties['og:type'] = $this->translateType( $this->genericType );
		} else {
			$this->properties['og:type'] = $override['og:type'];
		}
		
		// og:url (can't override this)
		$this->properties['og:url'] = $title->getFullURL();
		
		// og:updated_time (can't override this)
		$article = new Article( $title, 0 );
		$timestamp = $article->getTimestamp( TS_UNIX, $article->getTimestamp() );
		$this->properties['og:updated_time'] = $timestamp;
		
		// og:locale
		if ( !isset( $override['og:locale'] ) ) {
			global $wgVersion;
			if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
				// Title::getPageLanguage() is since 1.18
				$langCode = $title->getPageLanguage()->getCode();
			} else {
				global $wgContLang;
				$langCode = $wgContLang->getCode();
			}
			$this->properties['og:locale'] = FacebookLanguage::getFbLocaleForLangCode($langCode);
		}
		
		// og:title
		if ( !isset( $override['og:title'] ) ) {
			$this->properties['og:title'] = $title->getPrefixedText();
		}
		
		// og:image
		if ( !isset( $override['og:image'] ) ) {
			// TODO: attempt to use first image on page, otherwise default to $wgLogo
			global $wgServer, $wgLogo;
			$this->properties['og:image'] = $wgServer . $wgLogo;
		}
		
		// og:description
		if ( !isset( $override['og:description'] ) ) {
			/*
			 * TODO. This algorithm should work for vector: Start inside the rendered
			 * page body, <div id="bodyContent">. Next, enter the actual body content,
			 * <div class="mw-content-ltr">. Then, skip all tags that are not <p>. Now
			 * that we have come to the actual page content, strip away all the html
			 * tags and take the first 200 or so characters. Backtrack to find the end
			 * of the last complete sentence and omit the remaining partial sentence.
			 *
			 * Perhaps this should be done in the BeforePageDisplay hook.
			 */
			$this->properties['og:description']  = '';
		}
		
		// Set properties with values found in the <opengraph> parser hook.
		$this->setProperties( $override, false );
		
		parent::__construct();
	}
}
