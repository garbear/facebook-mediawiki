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


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class FBConnectXFBML
 * 
 * This class allows FBML (Facebook Markup Language, an extension to HTML) to
 * be incorporated into the wiki through XFBML.
 * 
 * See: <http://wiki.developers.facebook.com/index.php/XFBML>
 */
class FBConnectXFBML {
	/**
	 * This function is the callback that the ParserFirstCallInit hook assigns
	 * to the parser whenever a FBML tag is encountered (like <fb:name>).
	 * Function createParserHook($tag) creates an anonymous (lambda-style)
	 * function that simply redirects to parserHook(), filling in the missing
	 * $tag argument with the $tag provided to createParserHook.
	 */
	static function parserHook($innertext, $args, &$parser, $tag = '' ) {
		global $fbAllowFacebookImages;
		
		// Run hook to allow modifying the default behavior. If $override is
		// set, it is used instead. Return false to disable the tag. 
		$override = '';
		if ( !wfRunHooks('XFBMLParseTag',
				array( $tag, &$args, &$innertext, &$override )) ) {
			return '';
		}
		if ( $override != '' ) {
			return $override;
		}
		
		switch ($tag) {
			case '':
				break; // Error: We shouldn't be here!
			
			// To implement a custom XFBML tag handler, simply case it here like so
			case 'fb:add-profile-tab':
				// Disable these tags by returning an empty string
				break;
			case 'fb:serverfbml':
				// TODO: Is this safe? Does it respect $fbAllowFacebookImages?
				$attrs = self::implodeAttrs( $args );
				// Don't recursively parse $innertext
				return "<fb:serverfbml{$attrs}>$innertext</fb:serverfbml>";
			case 'fb:profile-pic':
			#case 'fb:photo': // Dropped in new JavaScript SDK
			#case 'fb:video': // Dropped in new JavaScript SDK
				if (!$fbAllowFacebookImages) {
					break;
				}
				// Careful - no "break;" if $fbAllowFacebookImages is true
			default:
				// Allow other tags by default
				$attrs = self::implodeAttrs( $args );
				return "<{$tag}{$attrs}>" . $parser->recursiveTagParse( $innertext ) . "</$tag>";
		}
		// Strip the tag entirely
		return '';
	}
	
	/**
	 * Helper function to create name-value pairs from the list of attributes passed to the
	 * parser hook.
	 */
	static function implodeAttrs( $args ) {
		$attrs = "";
		// The default action is to strip all event handlers and allow the tag
		foreach( $args as $name => $value ) {
			// Disable all event handlers (e.g. onClick, onligin)
			if ( substr( $name, 0, 2 ) == "on" )
				continue;
			// Otherwise, pass the attribute through htmlspecialchars unmodified
			$attrs .= " $name=\"" . htmlspecialchars( $value ) . '"';
		}
		return $attrs;
	}
	
	/**
	 * Helper function for parserHook. Originally, all tags were directed to
	 * that function, but I had no way of knowing which tag provoked the function.
	 */
	static function createParserHook($tag) {
		$args = '$text,$args,&$parser';
		$code = 'return FBConnectXFBML::parserHook($text,$args,$parser,\''.$tag.'\');';
		return create_function($args, $code);
	}
	
	/**
	 * Returns true if XFBML is enabled (i.e. $fbUseMarkup is not false).
	 * Defaults to true if $fbUseMarkup is not set.
	 */
	static function isEnabled() {
		global $fbUseMarkup;
		return !isset($fbUseMarkup) || $fbUseMarkup;
	}
	
	/**
	 * Returns the availabe XFBML tags. For help on including these in your
	 * site, please see: <http://developers.facebook.com/plugins>.
	 * 
	 * If Facebook adds a new tag (or you create your own!) then this list can
	 * be updated with the XFBMLAvailableTags hook.
	 */
	static function availableTags() {
		if (!self::isEnabled()) {
			// If XFBML isn't enabled, then don't report any tags
			return array( );
		}
		// http://github.com/facebook/connect-js/blob/master/src/xfbml/xfbml.js#L237
		$tags = array('fb:activity',
		              'fb:add-profile-tab',
		              'fb:bookmark',
		              'fb:comments',
		              'fb:connect-bar',
		              'fb:fan',
		              'fb:like',
		              'fb:like-box',
		              'fb:live-stream',
		              'fb:login',
		              'fb:login-button',
		              'fb:facepile',
		              'fb:name',
		              'fb:profile-pic',
		              'fb:recommendations',
		              'fb:serverfbml',
		              'fb:share-button',
		              'fb:social-bar',
		              /*
		               * From the Facebook Developer's Wiki under the old JS library.
		               * These may still be possible with a <fb:serverFbml> tag.
		               * <http://wiki.developers.facebook.com/index.php/XFBML>
		               */
		              #'fb:connect-form',
		              #'fb:container',
		              #'fb:eventlink',
		              #'fb:grouplink',
		              #'fb:photo',
		              #'fb:prompt-permission',
		              #'fb:pronoun',
		              #'fb:unconnected-friends-count',
		              #'fb:user-status'
		              /*
		               * In 2008 I found these in the deprecated Facebook Connect
		               * JavaScript library, connect.js.pkg.php, though no documentation
		               * was available for them on the Facebook dev wiki.
		               */
		              #'fb:add-section-button',
		              #'fb:share-button',
		              #'fb:userlink',
		              #'fb:video',
		);
		
		// Reject discarded tags (that return an empty string) from Special:Version
		$tempParser = new DummyParser();
		foreach( $tags as $i => $tag ) {
			if ( self::parserHook('', array(), $tempParser, $tag) == '' ) {
				unset( $tags[$i] );
			}
		}
		// Allow other functions to modify the available XFBML tags
		wfRunHooks( 'XFBMLAvailableTags', array( &$tags ));
		return $tags;
	}
}


/**
 * Class DummyParser
 * 
 * Allows FBConnectXML::availableTags() to pre-sanatize the list of tags reported to
 * MediaWiki, excluding any tags that result in the tag being replaced by an empty
 * string. Sorry for the confusing summary here, its really late. =)
 */
class DummyParser {
	// We don't pass any text in our testing, so this must return an empty string
	function recursiveTagParse() { return ''; }
}
