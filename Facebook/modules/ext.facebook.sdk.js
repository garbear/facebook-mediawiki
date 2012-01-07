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
 * ext.facebook.sdk.js
 * 
 * This module is a thin loader for the Facebook JavaScript SDK. The URL for
 * the SDK is specified by $wgFbScript.
 * 
 * This module carries ext.facebook.js as a dependency because the
 * window.fbAsyncInit hook must be in place before this library is loaded.
 */

(function($) {
	// Check for MediaWiki 1.17+
	if (window.wgVersion && (parseInt(window.wgVersion.split('.')[1]) || 0) >= 17 && window.mw) {
		// Boo. No async loading. ResourceLoader Version 2 Design Specification mentions
		// a possible future implementation for asynchronous, non-blocking requests that
		// download, parse and execute the script concurrent to document parsing.
		/*
		mw.loader.load(mw.config.get('fbScript')); // Boo. No async loading
		*/
		
		// In the meantime, we steal mw.loader.load() and amend async="true"
		/*
		document.write(mw.html.element('script', {
			'type'  : 'text/javascript',
			'src'   : mw.config.get('fbScript'),
			'async' : 'true'
		}, ''));
		*/
		
		// Just kidding. We really succumb and use jQuery. You evil temptress, you.
		/*
		$.getScript(mw.config.get('fbScript'));
		*/
		
		// This has indeed turned in to an epic quest. The above uses an AJAX call,
		// which refuses to cache, period. Instead, we use a cache-friendly version.
		jQuery.ajax({
			type     : "GET",
			url      : mw.config.get('fbScript'),
			async    : true, // fuck yea hipster kitty
			dataType : "script",
			cache    : true
		});
	}
})(jQuery);
