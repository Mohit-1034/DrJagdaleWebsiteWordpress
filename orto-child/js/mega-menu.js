/**
 * The Speciality and Services mega panels.
 *
 * The panels are rendered once, in skins/default/templates/header-navi.php, as
 * siblings of the menu rather than children of their menu items - the parent
 * theme's menu CSS gives every descendant of an item absolute positioning and a
 * fixed width, and a panel that spans the window cannot live inside a 220px
 * column. This script is what ties the two back together.
 *
 * What it does:
 *   - Finds the top-level menu items whose link points at /speciality/ and
 *     /services/ and pairs each with its panel. Matching on the URL rather than
 *     on a CSS class means nothing has to be saved on the menu item in the
 *     database, which matters because orto_child_ensure_menus() rebuilds the
 *     menu from scratch on every deploy.
 *   - Opens on hover and on focus, closes on Escape or on a click elsewhere,
 *     and keeps aria-expanded honest throughout.
 *   - Hides the parent theme's own dropdown for those two items, because the
 *     sub-items are still real menu children - that is what gives the mobile
 *     menu something to expand - and two panels opening at once is nobody's
 *     idea of a menu.
 *
 * Without this script the panels stay hidden and the theme's ordinary dropdown
 * does the job, so the menu is never broken, only plainer.
 *
 * The close is delayed by a beat. The panel is a separate element from the item
 * that opens it, and the pointer has to cross a few pixels of nothing to get
 * from one to the other; closing on the first mouseleave shut the panel while
 * the pointer was still travelling towards it.
 */
( function () {
	'use strict';

	var CLOSE_DELAY = 180;
	var OPEN_CLASS = 'djo_mega_open';

	var bar = document.querySelector( '.djo_bar' );
	var menu = document.querySelector( '.djo_bar_menu' );

	if ( ! bar || ! menu ) {
		return;
	}

	var panels = bar.querySelectorAll( '[data-djo-mega]' );

	if ( ! panels.length ) {
		return;
	}

	// Path of a URL, with the trailing slash normalised away so /services and
	// /services/ compare equal.
	function pathOf( url ) {
		try {
			return new URL( url, window.location.href ).pathname.replace( /\/+$/, '' );
		} catch ( e ) {
			return '';
		}
	}

	var pairs = [];

	Array.prototype.forEach.call( panels, function ( panel ) {
		var group = panel.getAttribute( 'data-djo-mega' );
		var item = null;

		// Top-level items only: a child link points at the same page (it is the
		// page plus a #fragment), and pairing the panel with one of its own
		// entries would make it reopen itself.
		Array.prototype.forEach.call(
			menu.querySelectorAll( ':scope > ul > li > a' ),
			function ( link ) {
				if ( item ) {
					return;
				}
				var path = pathOf( link.getAttribute( 'href' ) || '' );
				if ( path && path.split( '/' ).pop() === group ) {
					item = link.parentNode;
				}
			}
		);

		if ( item ) {
			pairs.push( { item: item, link: item.querySelector( 'a' ), panel: panel } );
		}
	} );

	if ( ! pairs.length ) {
		return;
	}

	var openPair = null;
	var closeTimer = null;

	function open( pair ) {
		clearTimeout( closeTimer );

		if ( openPair === pair ) {
			return;
		}

		if ( openPair ) {
			close( openPair );
		}

		pair.panel.hidden = false;

		// Painted hidden, then unhidden in the next frame, so the transition
		// has two states to move between rather than appearing already open.
		requestAnimationFrame( function () {
			pair.panel.classList.add( OPEN_CLASS );
		} );

		pair.item.classList.add( 'djo_mega_item_open' );

		if ( pair.link ) {
			pair.link.setAttribute( 'aria-expanded', 'true' );
		}

		openPair = pair;
	}

	function close( pair ) {
		if ( ! pair ) {
			return;
		}

		pair.panel.classList.remove( OPEN_CLASS );
		pair.item.classList.remove( 'djo_mega_item_open' );

		if ( pair.link ) {
			pair.link.setAttribute( 'aria-expanded', 'false' );
		}

		// Kept in the accessibility tree until the fade has finished, so a
		// screen reader is not told the panel vanished while it is still
		// visible on screen.
		window.setTimeout( function () {
			if ( ! pair.panel.classList.contains( OPEN_CLASS ) ) {
				pair.panel.hidden = true;
			}
		}, 250 );

		if ( openPair === pair ) {
			openPair = null;
		}
	}

	function scheduleClose() {
		clearTimeout( closeTimer );
		closeTimer = window.setTimeout( function () {
			close( openPair );
		}, CLOSE_DELAY );
	}

	pairs.forEach( function ( pair ) {
		// The theme's own dropdown for this item would otherwise open under the
		// mega panel. The class is styled in style.css rather than the display
		// being set here, so the rule is visible where every other menu rule is.
		pair.item.classList.add( 'djo_has_mega' );

		if ( pair.link ) {
			pair.link.setAttribute( 'aria-expanded', 'false' );
			pair.link.setAttribute( 'aria-controls', pair.panel.id );
		}

		pair.item.addEventListener( 'mouseenter', function () {
			open( pair );
		} );
		pair.item.addEventListener( 'mouseleave', scheduleClose );

		pair.panel.addEventListener( 'mouseenter', function () {
			clearTimeout( closeTimer );
		} );
		pair.panel.addEventListener( 'mouseleave', scheduleClose );

		// Keyboard: tabbing into the item opens the panel, tabbing out of the
		// last link in it closes again. focusin/focusout bubble, which is what
		// makes one listener on the item cover everything inside it.
		pair.item.addEventListener( 'focusin', function () {
			open( pair );
		} );
		pair.panel.addEventListener( 'focusin', function () {
			clearTimeout( closeTimer );
		} );
		pair.panel.addEventListener( 'focusout', function ( event ) {
			if ( ! pair.panel.contains( event.relatedTarget ) ) {
				scheduleClose();
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key || ! openPair ) {
			return;
		}

		var link = openPair.link;

		close( openPair );

		// Focus goes back to the item that opened it, not wherever it happened
		// to be inside a panel that is no longer on screen.
		if ( link ) {
			link.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! openPair ) {
			return;
		}

		if ( ! openPair.panel.contains( event.target ) && ! openPair.item.contains( event.target ) ) {
			close( openPair );
		}
	} );
}() );
