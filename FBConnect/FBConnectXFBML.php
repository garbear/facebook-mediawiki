<?php
/**
 * Copyright © 2008 Garrett Bruin <http://www.mediawiki.org/wiki/User:Gbruin>
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
 */
class FBConnectXFBML {
	/**
	 * This function is the callback that the ParserFirstCallInit hook assigns
	 * to the parser whenever a FBML tag is encountered (like <fb:name>).
	 * Function createParserHook($tag) creates an anonymous (lambda-style)
	 * function that simply redirects to parserHook(), filling in the missing
	 * $tag argument with the $tag provided to createParserHook.
	 */
	static function parserHook($text, $args, &$parser, $tag = '' ) {
		switch ($tag) {
			case '':
				return ''; // Error: We shouldn't be here!
			// To implement a custom XFBML tag handler, simple case it here like so  
			case 'fb:name':
			default:
				$attrs = "";
				foreach( $args as $name => $value ) {
					// TODO: !!!VERY IMPORTANT FOR SECURITY!!! strip all double-quotes from $value
					$attrs .= " $name=\"$value\"";
				}
				return '<' . $tag . $attrs . '>' . $parser->recursiveTagParse($text) . "</$tag>";
		}
	}
	
	/**
	 * Helper function for parserHook. Originally, all tags were directed to
	 * this function, but I had no way of knowing whick tag provoked the function.
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
	 * Returns the availabe XFBML tags. For now, this is just an array copied from
	 * <http://wiki.developers.facebook.com/index.php/XFBML> and the second-to-last line in
	 * <http://static.ak.fbcdn.net/rsrc.php/zAE5U/lpkg/2netpns0/en_US/141/136684/js/connect.js.pkg.php>.
	 * Eventually, this method can be replaced with one that gathers these tags
	 * from the internet (e.g. by downloading an xml or js file) in real time.
	 * 
	 * This is necessary because the Facebook Platform is so new, and major updates
	 * are being pushed out every week. With such turmoil regarding the available tags
	 * and the features they offer, our list of tags should not be hardcoded into this file.
	 * 
	 * But for now... HELP! Where does Firefox pull in the XFBML tags in from??
	 */
	static function availableTags() {
		if (!self::isEnabled()) {
			// If XFBML isn't enabled, then don't report any tags
			return array( );
		}
		
		// These are DEFINITELY valid tags (sarcasm intended -- see function doc comment)
		$validTags = array('fb:container',
		                   'fb:eventLink',
		                   'fb:groupLink',
		                   'fb:login-button',
		                   'fb:name',
		                   'fb:photo',
		                   'fb:profile-pic',
		                   'fb:prompt-permission',
		                   'fb:pronoun',
		                   'fb:serverFbml',
		                   'fb:unconnected-friends-count',
		                   'fb:user-status');
		// This tag is listed in the facebook dev wiki's documentation, but I couldn't
		// find any mention of it in the JavaScript package connect.js.pkg.php
		// Is it valid? What's going on in this JavaScript file?
		$wikiTags =  array('fb:connect-form');
		// I found these in the JavaScript package connect.js.pkg.php, but the wiki
		// makes no mention of them. Are they valid?
		$jsTags =    array('fb:add-section-button',
		                   'fb:share-button',
		                   'fb:userLink',
		                   'fb:video');
		// Oh well, include them anyway
		return array_merge( $validTags, $wikiTags, $jsTags );
	}
}
