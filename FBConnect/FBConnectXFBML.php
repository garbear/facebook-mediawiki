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
	 * Because every FBML tag directs to this function, we have no way of
	 * knowing which tag was used.
	 */
	static function parserHook($input, $args, &$parser ) {
		return htmlspecialchars( $input );
	}
	
	/**
	 * Returns the availabe XFBML tags. For now, this is just an array copied
	 * from <http://wiki.developers.facebook.com/index.php/XFBML>. Eventually,
	 * this method can be replaced with one that gathers these tags from the
	 * internet (e.g. by downloading an xml or js file) in real time.
	 */
	static function availableTags() {
		$tags = array( 'sample' );
		return $tags;
	}
}
	
