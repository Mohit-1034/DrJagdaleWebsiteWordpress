/**
 * Sticky header.
 *
 * The theme's own fixed-header behaviour lives in the trx_addons plugin, which
 * this site does not run - the pages are built in the child theme's own PHP
 * instead. So the menu row is pinned here, in about forty lines, rather than by
 * switching a page builder on for one feature.
 *
 * The bar also carries the mega menu (js/mega-menu.js). Nothing there needs to
 * know about pinning: the panels are positioned against the bar, so they travel
 * with it.
 *
 * How it works:
 *   - A zero-height sentinel is dropped in immediately above the header row.
 *     While the sentinel is on screen the page is at the top; the moment it
 *     scrolls out of view the row is pinned. That is one IntersectionObserver
 *     callback per state change, where a scroll listener would run on every
 *     frame of every scroll.
 *   - The row's natural height is written to a CSS custom property and held
 *     open by a spacer, so pinning it does not yank the page up by its height.
 *   - The height is re-measured on resize, because the row is taller on mobile
 *     once the menu wraps.
 */
( function () {
	'use strict';

	var row = document.querySelector( '.top_panel_navi' );

	if ( ! row || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var STUCK = 'djo_header_stuck';
	var root = document.documentElement;

	function measure() {
		// The pinned row is compact, so measuring it while stuck would record
		// the smaller height and let the page jump when it unpins.
		if ( document.body.classList.contains( STUCK ) ) {
			return;
		}

		// Margins included on purpose. The stripe is inset from the top of the
		// window by a 12px margin, and offsetHeight does not count it - so
		// holding the gap open with offsetHeight alone left the page 12px
		// short and the content visibly jumped up the moment it pinned.
		var box = getComputedStyle( row );
		var height = row.offsetHeight
			+ parseFloat( box.marginTop || 0 )
			+ parseFloat( box.marginBottom || 0 );

		root.style.setProperty( '--djo-header-h', Math.round( height ) + 'px' );
	}

	measure();

	// Sits directly above the header: on screen only while the page is at the top.
	//
	// It goes OUTSIDE .top_panel, not inside it, and that placement is the whole
	// trick. Pinning the row takes it out of the flow, so .top_panel gets a
	// padding-top to hold the gap open. A sentinel inside .top_panel gets pushed
	// down by that padding, back into view - which unpins the header, which
	// removes the padding, which scrolls the sentinel out of view again. That
	// loop ran at about sixty toggles a second and looked like the whole screen
	// flickering. Out here, nothing the header does can move it.
	var panel = row.closest ? row.closest( '.top_panel' ) : null;
	var anchor = panel || row;

	var sentinel = document.createElement( 'div' );
	sentinel.className = 'djo_header_sentinel';
	sentinel.setAttribute( 'aria-hidden', 'true' );
	anchor.parentNode.insertBefore( sentinel, anchor );

	new IntersectionObserver(
		function ( entries ) {
			var stuck = ! entries[ 0 ].isIntersecting;

			document.body.classList.toggle( STUCK, stuck );

			// Re-measure the moment it unpins. measure() refuses to run while
			// pinned - it would record the compact height - so a window resized
			// mid-scroll would otherwise keep the old orientation's height until
			// the next resize, and the gap would be wrong on the next pin.
			if ( ! stuck ) {
				measure();
			}
		},
		{
			// The admin bar covers the top 32px for logged-in users, which would
			// otherwise hide the sentinel and pin the header at rest.
			rootMargin: ( document.body.classList.contains( 'admin-bar' ) ? '-32px' : '0px' ) + ' 0px 0px 0px'
		}
	).observe( sentinel );

	var resizeTimer;
	window.addEventListener( 'resize', function () {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( measure, 150 );
	} );
}() );
