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
 * ext.facebook.actions.js
 * 
 * Code for using custom Open Graph actions.
 */

(function($, mw) {
	$(document).ready(function() {
		var actionLinks = $('a.mw-facebook-logo.opengraph-action');
		fbLogo = actionLinks.css('background-image');
		if (fbLogo && fbLogo.length && fbLogo.length > 5) {
			// Translate css URL into a normal URL
			fbLogo = fbLogo.substring('url('.length, fbLogo.length - 5);
		}
		
		// TODO: The Facebook logo should fade into a green check mark to
		// provide feedback that the action was successful
		
		// Install the click handler
		actionLinks.click(function(ev) {
			ev.preventDefault();
		});
	});
})(window.jQuery, window.mediaWiki);
