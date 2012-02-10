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
	 */
	public static function parserHook($innertext, array $args, Parser $parser, PPFrame $frame) {
		global $wgFbOpenGraph, $wgFbOpenGraphCustomObjects;
		if ( empty( $wgFbOpenGraph ) || empty( $wgFbOpenGraphCustomObjects ) ) {
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
				// Expand template arguments
				$value = trim( $parser->replaceVariables( $value, $frame ) );
				// Check if $value is a wiki link
				if ( substr($value, 0, 2) == '[[' ) {
					$resolved = $parser->replaceInternalLinks( $value );
					$parser->replaceLinkHolders( $resolved );
					preg_match( '/^<a\b[^<>]*?\bhref=("[^"]*"|\'[^\']*\')/', $resolved, $matches );
					if ( count( $matches ) >= 2 ) {
						global $wgServer;
						// strip quotes
						$matches[1] = substr( $matches[1], 1, strlen( $matches[1] ) - 2 );
						$url = $wgServer . $matches[1];
					}
				}
				$value = !empty( $url ) ? $url : $parser->recursiveTagParse( $value, $frame );
				$args[$key] = htmlspecialchars( $value );
			}
			self::registerProperties($parser->getTitle(), $args);
		}
		
		// TODO: Insert a placeholder so that actions can be placed above type definitions on the page
		if ( $action != '' ) {
			// See if the action is available for the object type
			$object = self::newObjectFromTitle( $parser->getTitle() );
			$actions = $object->getCustomActions();
			if ( count( $actions ) && in_array( $action, $actions ) ) {
				// Let the page know it should load the actions module
				global $wgVersion;
				if ( version_compare( $wgVersion, '1.17', '>=' ) ) {
					global $wgOut;
					$wgOut->addModules( 'ext.facebook.actions' );
				}
				
				$innertext = htmlspecialchars( $parser->replaceVariables( $innertext, $frame ) );
				return '<a href="#" class="mw-facebook-logo opengraph-action opengraph-action-' . $action . '">' . $innertext . '</a>';
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
	 * Given a file, returns the Open Graph representation.
	 * 
	 * @param File $file
	 * @return OpenGraphObject|NULL
	 */
	public static function newObjectFromFile($file) {
		return OpenGraphObject::newFromFile( $file );
	}
	
	/**
	 * Inverts the $wgFbOpenGraphCustomActions array. Instead of
	 * action => array( objects ) we now have object => array( actions ).
	 * 
	 * Only returns objects defined in this array. If a custom object is
	 * registered with Facebook, but not connected to an action and/or specified
	 * by $wgFbOpenGraphCustomActions, it can still be used on a page.
	 */
	private static $customObjects = NULL;
	public static function getActionObjects() {
		global $wgFbOpenGraphCustomActions;
		
		if ( self::$customObjects === NULL && !empty( $wgFbOpenGraphCustomActions ) ) {
			self::$customObjects = array();
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
		return self::$customObjects;
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
	 * Given a file, returns the Open Graph representation.
	 * 
	 * @return OpenGraphObject|NULL
	 */
	public static function newFromFile($file) {
		global $wgFbOpenGraphRegisteredObjects;
		// Make sure $wgFbOpenGraphRegisteredObjects['file'] is defined
		// possible TODO: switch on $file->getMediaType() so we can support different
		// file types and return new OpenGraph[File|Image|Media]Object( $file );
		if ( !empty( $wgFbOpenGraphRegisteredObjects ) &&
				isset( $wgFbOpenGraphRegisteredObjects['file'] ) ) {
			return new OpenGraphFileObject( $file );
		}
		return NULL;
	}
	
	/**
	 * Given a page title, returns the Open Graph representation.
	 * @param Title $title
	 * @return OpenGraphObject|NULL
	 */
	public static function newFromTitle($title) {
		global $wgFbNamespace;
		
		if ( $title instanceof Title ) {
			// Talk pages redirect to subject pages
			if ( $title->isTalkPage() ) {
				// TODO: Parse subject page to extract <opengraph> attributes
				$title = $title->getSubjectPage();
			}
			
			// Use built-in type "website" for main page
			if ( $title->equals( Title::newMainPage() ) ) {
				global $wgSitename, $wgContentLanguage;
				return new OpenGraphArticleObject( $title, array(
						'og:type'        => 'website',
						'og:description' => $wgSitename . ' (' . $wgContentLanguage . ')',
				) );
			}
			
			// File
			if ( $title->getNamespace() == NS_FILE ) {
				//$file = wfLocalFile( $title );
				$file = wfFindFile( $title );
				if ( !empty( $file ) && $file->exists() ) {
					return self::newFromFile( $file );
				}
				return NULL;
			}
			
			// Blog articles are used by an extension on Wikia
			if ((defined('NS_BLOG_ARTICLE') && $title->getNamespace() == NS_BLOG_ARTICLE) ||
					(defined('NS_BLOG_ARTICLE_TALK') && $title->getNamespace() == NS_BLOG_ARTICLE_TALK)) {
				global $wgServer, $wgUser;
				
				// Use a custom image for blog posts
				$image = $wgServer . '/index.php?action=ajax&rs=FacebookPushEvent::showImage&time=' . time() .
							'&fb_id=' . $wgUser->getId() . '&event=FBPush_OnAddBlogPost&img=blogpost.png';
				$parameters = array(
						'og:type'  => $this->translateType('blog'),
						'og:image' => $image
				);
				return new OpenGraphArticleObject( $title, $parameters );
			}
			
			// Don't consider NS_SPECIAL and NS_MEDIA to be articles
			if ( $title->canExist() ) {
				
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
				if ( isset( $parameters['og:type'] ) && FacebookAPI::isNamespaceSetup() ) {
					$parameters['og:type'] = $wgFbNamespace . ':' . $parameters['og:type'];
				}
				return new OpenGraphArticleObject( $title, $parameters );
			}
		}
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
		return $this->properties['og:url'];
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
	 * Returns the actions that are registered for the object by
	 * $wgFbOpenGraphCustomActions.
	 */
	public function getCustomActions() {
		$actions = array();
		$customObjects = FacebookOpenGraph::getActionObjects();
		
		if ( !empty( $customObjects ) ) {
			// Start with actions matching the specified object
			$type = $this->getType();
			if ( isset( $customObjects[$type] ) ) {
				$actions = $customObjects[$type];
			}
			
			// Merge in actions matching all objects ('*')
			if ( isset( $customObjects['*'] ) ) {
				$actions = array_merge( $customObjects['*'], $actions );
			}
		}
		
		return $actions;
	}
	
	/**
	 * Performs the translation between generic Open Graph object types and
	 * custom ones defined by this Facebook application.
	 * 
	 * @param string $generic
	 */
	protected function translateType( $generic ) {
		global $wgFbNamespace, $wgFbOpenGraphRegisteredObjects;
		if ( FacebookAPI::isNamespaceSetup() ) {
			if (!empty($wgFbOpenGraphRegisteredObjects) && isset($wgFbOpenGraphRegisteredObjects[$generic])) {
				return $wgFbNamespace . ':' . $wgFbOpenGraphRegisteredObjects[$generic];
			} else {
				// "article" is special because it's a built-in type
				return $generic == 'article' ? $generic : $wgFbNamespace . ':' . $generic;
			}
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
		
		// og:description (defer until setDescriptionFromText())
		
		// Set properties with values found in the <opengraph> parser hook.
		$this->setProperties( $override, false );
		
		parent::__construct();
	}
	
	/**
	 * Extracts a description from the article's text.
	 *
	 * This function separates $text into its HTML sibling nodes. It then
	 * searches for the first <p> sibling and returns the first sentence (up to
	 * 300 chars) or multiple sentences to gaurantee at least 40 characters.
	 */
	public function setDescriptionFromText($text) {
		while ( $text != '' ) {
			// Look for the next opening tag
			preg_match( '/<([:A-Z_a-z0-9][:A-Z_a-z-.0-9]*)[^>]*>/', $text, $matches );
			if ( count( $matches ) >= 2 ) {
				$tag = $matches[1];
				// No need to search the text again
				$text = substr( $text, strpos( $text, $matches[0] ) + strlen( $matches[0] ) );
				$self_terminated = substr_compare( $matches[0], '/>', -2, 2 ) === 0;
				if ( !$self_terminated ) {
					// Look for the closing tag
					$endpos = 0;
					$stack = array( $tag );
					while ( count ( $stack ) ) {
						preg_match( '@</?' . $tag . '[^>]*>@', $text, $matches, 0, $endpos );
						if ( count( $matches ) ) {
							$endpos = strpos( $text, $matches[0], $endpos ) + strlen( $matches[0] );
							// Three cases - close tag, self-terminated or open tag
							if ( substr( $matches[0], 1, 1 ) == '/' ) {
								array_pop( $stack );
							} else if ( substr_compare( $matches[0], '/>', -2, 2 ) === 0 ) {
								continue;
							} else {
								array_push( $stack, $tag );
							}
						} else {
							// No closing tag found, use the whole string
							$endpos = strlen( $text );
							break;
						}
					}
					// Got the entire contents of the first tag
					if ( $tag == 'p' ) {
						$content = trim( strip_tags( substr( $text, 0, $endpos ) ) );
						// Look for at least five words (~25 characters)
						if ( strlen( $content ) > 25 ) {
							// But don't be verbose
							if ( strlen( $content ) > 300 ) {
								$content = substr( $content, 0, 300 );
							}
							// Recompose to > 40 chars with as few sentences as possible
							$sentences = explode( '.', $content );
							$content = '';
							while ( strlen( $content ) < 40 && count( $sentences ) ) {
								// If it's the last sentence, strip the partial word
								if ( count( $sentences ) == 1 && strlen( $sentences[0] ) > 1 ) {
									$pos = strrpos( $sentences[0], ' ' );
									if ( $pos !== false ) {
										$sentences[0] = substr( $sentences[0], 0, $pos );
									}
								} else {
									$sentences[0] .= '.';
								}
								$content .= array_shift( $sentences );
							}
							// Check again to see if we still have enough words
							if ( strlen( $content ) > 25 ) {
								$this->properties['og:description'] = $content;
								break; // (return)
							}
						}
					}
					// Go on to the next tag
					$text = substr( $text, $endpos );
				}
			} else {
				// No more tags
				break;
			}
		}
	}
	
	/**
	 * Returns true if no description has been set.
	 */
	public function needsDescription() {
		return empty( $this->properties['og:description'] );
	}
}

class OpenGraphFileObject extends OpenGraphObject {
	
	private $defaultType = 'file';
	
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
	
	function __construct( $file ) {
		$title = $file->getTitle();
		
		// og:type
		$this->properties['og:type'] = $this->translateType( $this->defaultType );
		
		// og:url
		$this->properties['og:url'] = $title->getFullURL();
		
		// og:updated_time
		$article = new Article( $title, 0 );
		$timestamp = $article->getTimestamp( TS_UNIX, $article->getTimestamp() );
		$this->properties['og:updated_time'] = $timestamp;
		
		// og:locale
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.18', '>=' ) ) {
			// Title::getPageLanguage() is since 1.18
			$langCode = $title->getPageLanguage()->getCode();
		} else {
			global $wgContLang;
			$langCode = $wgContLang->getCode();
		}
		$this->properties['og:locale'] = FacebookLanguage::getFbLocaleForLangCode($langCode);
		
		// og:title
		$this->properties['og:title'] = $file->getName();
		
		// og:image
		if ( $file->getMediaType() == MEDIATYPE_BITMAP ) {
			$this->properties['og:image'] = $file->getCanonicalUrl();
		} else {
			global $wgServer;
			$this->properties['og:image'] = $wgServer . $file->iconThumb()->url;
		}
		
		// og:description
		// TODO: This crashes MediaWiki because messages cannot be loaded or some bullshit
		// The problem is that message decoding calls MessageCache::parse(), which calls
		// message decoding, which again calls MessageCache::parse(). To provide reentrancy,
		// MessageCache::parse() violates its signature by returning a string instead of an
		// object. You dirty little bastard, you.
		//$this->properties['og:description'] = $file->getLongDesc();
		
		parent::__construct();
	}
}
