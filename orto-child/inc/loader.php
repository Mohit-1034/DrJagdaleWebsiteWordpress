<?php
/**
 * The page loader
 *
 * A white overlay shown on every navigation, carrying a single animated mark:
 * the clinic's own tree, drawing itself.
 *
 * THE IDEA
 *
 * The Andry tree - a crooked sapling lashed to a straight stake - is the symbol
 * orthopaedics has used since 1741, and it is the mark already sitting inside
 * the roundel in this site's header. It says what the practice does in one
 * picture: something bent, held straight until it grows straight. The loader
 * draws it on, part by part, and the ropes arrive last.
 *
 * Inline SVG and CSS, no request. Not images/logo-mark.png: this paints before
 * anything else on the page, and an <img> here is a thing that can arrive after
 * the loader it belongs to - the roundel is also a 512px scan whose ring of
 * type is unreadable at this size. Same symbol, redrawn clean.
 *
 * WHY IT IS SHORT
 *
 * The previous build showed once per session and held for 1.7s, which is a
 * reasonable length for a greeting. Shown on every navigation, 1.7s is not a
 * greeting, it is a toll gate on the menu - seven clicks around the site and a
 * visitor has spent twelve seconds watching a bone swing. The hold is 600ms
 * now, and the whole reveal is over in 360ms, so the loader is a beat between
 * pages rather than an event.
 *
 * ON ROUTE CHANGES
 *
 * This is a multi-page WordPress site, so a route change is an ordinary
 * navigation. The overlay is therefore shown twice per click: once immediately
 * when a link is clicked, covering the dead time while the server responds, and
 * again by the new document as it paints. The two are visually identical, so
 * the join between them is invisible and the gap that usually sits between
 * "clicked" and "something happened" is covered.
 *
 * WHERE IT LIVES
 *
 *   Markup  - wp_body_open, so it is the first element in the body.
 *   CSS     - inlined in wp_head at priority 1. A loader that waits on an
 *             external stylesheet is a loader that flashes unstyled, which is
 *             the one thing it exists to prevent.
 *   JS      - inlined directly after the markup.
 *
 * IT CAN NEVER TRAP THE SITE
 *
 * The overlay clears itself from CSS after eight seconds whatever happens, a
 * <noscript> rule means it never appears where scripting is off, and the
 * controller has its own six-second backstop for a 'load' that never fires. The
 * script is the fast path, not the only path.
 *
 * The class prefix is doc-jagadale-loader-, which is not this theme's djo_
 * house style. That is deliberate and was specified: the loader is meant to be
 * liftable into another build without a rename.
 *
 * @package ORTO
 */

/**
 * Loader settings, in one place.
 *
 * @return array
 */
if ( ! function_exists( 'orto_child_loader_config' ) ) {
	function orto_child_loader_config() {
		return apply_filters(
			'orto_child_loader',
			array(
				// Turn the whole thing off.
				'enabled'       => true,

				/*
				 * Milliseconds the overlay holds even when the page is already
				 * painted.
				 *
				 * Deliberately short. This runs on every navigation, so it is
				 * buying two things and nothing else: cover for the moment
				 * between click and paint, and enough time that the reveal is
				 * not cut off mid-fade. Below about 400 it flickers; above
				 * about 900 the site starts to feel slower than it is.
				 */
				'min_duration'  => 600,

				/*
				 * Cover outgoing navigation as well as incoming.
				 *
				 * With this on, clicking a link paints the overlay immediately
				 * rather than leaving the visitor on a dead page while the
				 * server thinks. Set false and the loader only appears on the
				 * arriving document.
				 */
				'cover_exit'    => true,

				// The line under the mark.
				'show_tagline'  => true,
			)
		);
	}
}

/**
 * Should the loader run on this request?
 *
 * @return bool
 */
if ( ! function_exists( 'orto_child_loader_active' ) ) {
	function orto_child_loader_active() {
		$config = orto_child_loader_config();

		if ( empty( $config['enabled'] ) ) {
			return false;
		}

		/*
		 * Never in an editing context. An overlay that covers the page is a
		 * nuisance in the Customizer preview and actively breaks a page
		 * builder's canvas, and neither is a place anyone is "arriving".
		 */
		if ( is_admin() || is_customize_preview() || is_embed() ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a builder's own preview flag, not acting on it.
		if ( isset( $_GET['elementor-preview'] ) ) {
			return false;
		}

		return true;
	}
}

/**
 * The loader's stylesheet, inlined in the document head.
 *
 * Priority 1 so it lands before the theme's own stylesheets: this has to be in
 * effect on the very first paint.
 */
if ( ! function_exists( 'orto_child_loader_styles' ) ) {
	add_action( 'wp_head', 'orto_child_loader_styles', 1 );
	function orto_child_loader_styles() {
		if ( ! orto_child_loader_active() ) {
			return;
		}
		?>
<style id="doc-jagadale-loader-css">
/* The overlay -------------------------------------------------------------
   position:fixed with inset:0 rather than width/height:100% - 100vh is the
   large viewport height on a phone, taller than the visible area while the
   browser chrome is showing, and the overlay would be scrollable. */
.doc-jagadale-loader {
	position: fixed;
	inset: 0;
	z-index: 99999;
	display: flex;
	align-items: center;
	justify-content: center;
	background-color: #F8F8F7;
	/* One very faint cool wash behind the mark, so the ground has a centre
	   rather than being a flat sheet. Barely there on purpose. */
	background-image: radial-gradient(100% 70% at 50% 44%, rgba(123, 161, 110, 0.07) 0%, rgba(248, 248, 247, 0) 62%);
	opacity: 1;
	/* Its own layer for its short life: the exit animates opacity over a
	   full-screen element, and without this the browser repaints everything
	   underneath on every frame of it. */
	will-change: opacity;
	/*
	 * THE FAILSAFE. If the controller never runs - a JS error, a minifier that
	 * mangles it, a caching plugin that strips it - this clears the overlay
	 * anyway. A loader must not be able to lock a visitor out of the site it
	 * is loading. The controller normally removes the element long before this.
	 */
	animation: doc-jagadale-loader-failsafe 0.4s cubic-bezier(0.22, 0.61, 0.36, 1) 8s both;
}

@keyframes doc-jagadale-loader-failsafe {
	to { opacity: 0; visibility: hidden; }
}

/* Nothing scrolls while the overlay is up. Set from the controller and undone
   by it, so a failed script cannot leave the page locked. */
html.doc-jagadale-loader-locked,
html.doc-jagadale-loader-locked body {
	overflow: hidden;
}

.doc-jagadale-loader-stage {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 0 24px;
	text-align: center;
	opacity: 0;
	transform: translate3d(0, 6px, 0);
	animation: doc-jagadale-loader-in 0.36s cubic-bezier(0.22, 0.61, 0.36, 1) 0.06s forwards;
}

@keyframes doc-jagadale-loader-in {
	to { opacity: 1; transform: translate3d(0, 0, 0); }
}

/* The mark -----------------------------------------------------------------
   The clinic's own emblem, drawn rather than fetched: the Andry tree - a
   crooked sapling lashed to a straight stake - which is the oldest symbol
   orthopaedics has and the thing already sitting inside the roundel in the
   header. It says what the practice does in one picture: something bent, held
   straight until it grows straight.

   Drawn as inline SVG and not as images/logo-mark.png on purpose. This paints
   before anything else on the page, and an <img> here is a request that can
   arrive after the loader it belongs to - the roundel is also a 512px scan
   whose ring of type is unreadable at this size. Same symbol, redrawn clean.

   It draws itself on: ground, stake, trunk, branches, crown, then the three
   ropes, then it holds and starts again. Every stroked path carries
   pathLength="100", which normalises each one to the same nominal length -
   that is what lets one dash-offset animation drive paths of wildly different
   real lengths without measuring any of them.

   The colours are literal rather than custom properties: there is no
   --theme-color-* to read yet at this point in the document. Sage #7BA16E and
   the deeper #4C6A44 of the call-to-action band, with the rope in the logo's
   own warm brown. Keep them in step by hand if the skin ever changes. */
.doc-jagadale-loader-mark {
	width: 96px;
	height: 123px;
	/* One cycle, shared by every part of the drawing. Declared here so the
	   percentages in the keyframes below all mean the same thing. */
	animation: doc-jagadale-loader-mark-cycle 3.4s linear infinite;
}

.doc-jagadale-loader-mark svg {
	display: block;
	width: 100%;
	height: 100%;
	overflow: visible;
}

/* The whole mark fades out at the end of the cycle and back in at the start,
   so the restart is a breath rather than a jump cut. */
@keyframes doc-jagadale-loader-mark-cycle {
	0%        { opacity: 0; }
	5%, 88%   { opacity: 1; }
	98%, 100% { opacity: 0; }
}

.doc-jagadale-loader-mark path {
	fill: none;
	stroke-linecap: round;
	stroke-linejoin: round;
	/* 100 because every path declares pathLength="100". Offset 100 is an
	   undrawn path; the animations below run it to 0. */
	stroke-dasharray: 100;
	stroke-dashoffset: 100;
	animation-duration: 3.4s;
	animation-timing-function: cubic-bezier(0.65, 0, 0.35, 1);
	animation-iteration-count: infinite;
}

/* One keyframe set, reused. The stagger is done with negative delays rather
   than with a keyframe each: every part draws over the same fraction of the
   cycle, just starting at a different point in it. */
@keyframes doc-jagadale-loader-draw {
	0%       { stroke-dashoffset: 100; }
	22%, 100% { stroke-dashoffset: 0; }
}

.doc-jagadale-loader-ground {
	stroke: rgba(76, 106, 68, 0.34);
	stroke-width: 2.4;
	animation-name: doc-jagadale-loader-draw;
}

.doc-jagadale-loader-stake {
	stroke: #4C6A44;
	stroke-width: 4.4;
	animation-name: doc-jagadale-loader-draw;
	animation-delay: -3.24s; /* starts at 5% of the cycle */
}

.doc-jagadale-loader-trunk {
	stroke: #4C6A44;
	stroke-width: 5.2;
	animation-name: doc-jagadale-loader-draw;
	animation-delay: -3.13s; /* 8% */
}

.doc-jagadale-loader-branch {
	stroke: #4C6A44;
	stroke-width: 2.8;
	animation-name: doc-jagadale-loader-draw;
	animation-delay: -2.79s; /* 18% */
}

/* The ropes, last and one after another - they are the point of the symbol,
   so they arrive after there is something for them to bind. */
.doc-jagadale-loader-rope {
	stroke: #A9714B;
	stroke-width: 2.6;
	animation-name: doc-jagadale-loader-draw;
}

.doc-jagadale-loader-rope-1 { animation-delay: -2.31s; } /* 32% */
.doc-jagadale-loader-rope-2 { animation-delay: -2.18s; } /* 36% */
.doc-jagadale-loader-rope-3 { animation-delay: -2.04s; } /* 40% */

/* The crown. A fill rather than a stroke, so it cannot be drawn on - it grows
   in instead, from the top of the trunk, while the branches are still
   arriving. */
.doc-jagadale-loader-crown {
	fill: #7BA16E;
	transform-origin: 49px 26px;
	opacity: 0;
	animation: doc-jagadale-loader-grow 3.4s cubic-bezier(0.34, 1.3, 0.64, 1) infinite;
	animation-delay: -2.92s; /* 14% */
}

@keyframes doc-jagadale-loader-grow {
	0%        { opacity: 0; transform: scale(0.4); }
	18%, 100% { opacity: 1; transform: scale(1); }
}

/* The tagline -------------------------------------------------------------
   The clinic's own words. The only thing identifying whose site this is now
   that the mark has gone, so it stays - but quiet, and smaller than the
   joint above it. */
.doc-jagadale-loader-tagline {
	margin: 30px 0 0;
	color: #7E847B;
	font-size: clamp(10px, 1.2vw, 12px);
	font-weight: 600;
	line-height: 1.4;
	letter-spacing: 0.2em;
	text-transform: uppercase;
}

/* The line ----------------------------------------------------------------
   A hairline track with a bar running left to right along it. Driven by a
   custom property the controller sets, so both real progress and the minimum
   hold can move it, and a variable-driven transform still composites on the
   GPU. */
.doc-jagadale-loader-track {
	position: relative;
	width: 132px;
	height: 2px;
	margin-top: 20px;
	overflow: hidden;
	border-radius: 2px;
	background-color: #E9E2DB;
}

.doc-jagadale-loader-bar {
	position: absolute;
	inset: 0;
	border-radius: 2px;
	background: linear-gradient(90deg, #8BAE7E, #7BA16E);
	transform-origin: left center;
	transform: scaleX(var(--doc-jagadale-loader-progress, 0));
	transition: transform 0.35s cubic-bezier(0.22, 0.61, 0.36, 1);
}

/*
 * The waiting state. If the page is still loading after the bar has run its
 * predicted course, it stops pretending to know how far along it is and
 * becomes an indeterminate shuttle. Nothing restarts - the bar simply changes
 * job, which is what keeps a slow load from looking like a stutter.
 */
.doc-jagadale-loader-waiting .doc-jagadale-loader-bar {
	transform: none;
	transition: none;
	animation: doc-jagadale-loader-shuttle 1.2s cubic-bezier(0.65, 0, 0.35, 1) infinite;
}

@keyframes doc-jagadale-loader-shuttle {
	0%   { transform: translate3d(-100%, 0, 0) scaleX(0.4); }
	100% { transform: translate3d(250%, 0, 0)  scaleX(0.4); }
}

/* The exit ----------------------------------------------------------------
   The ground dissolves and the stage lifts a few pixels with it. Short,
   because at this cadence the exit is most of what the visitor experiences. */
.doc-jagadale-loader-done {
	opacity: 0;
	transition: opacity 0.34s cubic-bezier(0.22, 0.61, 0.36, 1);
	animation: none;
	pointer-events: none;
}

.doc-jagadale-loader-done .doc-jagadale-loader-stage {
	opacity: 0;
	transform: translate3d(0, -6px, 0);
	transition: opacity 0.24s cubic-bezier(0.22, 0.61, 0.36, 1),
		transform 0.34s cubic-bezier(0.22, 0.61, 0.36, 1);
	animation: none;
}

/* Short viewports - a phone in landscape has no room for the full stack. */
@media (max-height: 480px) {
	.doc-jagadale-loader-mark { transform: scale(0.74); }
	.doc-jagadale-loader-tagline { margin-top: 14px; letter-spacing: 0.14em; }
	.doc-jagadale-loader-track { margin-top: 12px; }
}

/* Reduced motion ----------------------------------------------------------
   The swing stops and the joint rests at its neutral angle; the ring stops
   breathing; the shuttle goes entirely, because an indeterminate bar is pure
   motion with no information in it. What is left is a static diagram and the
   words, which is what the loader is actually for. */
@media (prefers-reduced-motion: reduce) {
	.doc-jagadale-loader-stage {
		opacity: 1;
		transform: none;
		animation: none;
	}
	/* The mark stops drawing itself and is simply there, fully drawn. Nothing
	   is lost: the symbol is the information, the drawing-on was the flourish. */
	.doc-jagadale-loader-mark,
	.doc-jagadale-loader-mark path,
	.doc-jagadale-loader-crown {
		animation: none;
		opacity: 1;
	}
	.doc-jagadale-loader-mark path {
		stroke-dashoffset: 0;
	}
	.doc-jagadale-loader-crown {
		transform: none;
	}
	.doc-jagadale-loader-waiting .doc-jagadale-loader-bar {
		animation: none;
		transform: scaleX(0.5);
	}
	.doc-jagadale-loader-done .doc-jagadale-loader-stage {
		transform: none;
	}
}
</style>
		<?php
		/*
		 * No script, no overlay. Written as its own <noscript><style> rather
		 * than left to the failsafe, because eight seconds of a covered page
		 * is not a graceful degradation.
		 */
		?>
<noscript><style>.doc-jagadale-loader{display:none!important}</style></noscript>
		<?php
	}
}

/**
 * The loader's markup and its controller, at the top of the body.
 */
if ( ! function_exists( 'orto_child_loader_markup' ) ) {
	add_action( 'wp_body_open', 'orto_child_loader_markup', 1 );
	function orto_child_loader_markup() {
		if ( ! orto_child_loader_active() ) {
			return;
		}

		$config   = orto_child_loader_config();
		$business = orto_child_get_business();
		?>
<div class="doc-jagadale-loader" id="doc-jagadale-loader" role="status" aria-live="polite">
	<?php
	/*
	 * The only thing announced is that the page is loading. The joint below is
	 * a decoration and is hidden from the accessibility tree: a screen reader
	 * user gains nothing from being told about a swinging bone.
	 */
	?>
	<span class="screen-reader-text"><?php esc_html_e( 'Loading', 'orto' ); ?></span>

	<div class="doc-jagadale-loader-stage">
		<div class="doc-jagadale-loader-mark" aria-hidden="true">
			<svg viewBox="0 0 100 128" focusable="false">
				<path class="doc-jagadale-loader-ground" pathLength="100" d="M22 113h56"/>
				<path class="doc-jagadale-loader-stake" pathLength="100" d="M63 43v68"/>
				<path class="doc-jagadale-loader-trunk" pathLength="100" d="M46 111c0-12-8-20-4-32s14-20 10-32"/>
				<path class="doc-jagadale-loader-branch" pathLength="100" d="M51 52 41 42m10 4 10-10"/>
				<g class="doc-jagadale-loader-crown">
					<ellipse cx="36" cy="31" rx="17" ry="12.5"/>
					<ellipse cx="62" cy="28" rx="18" ry="13"/>
					<ellipse cx="49" cy="18" rx="16" ry="12"/>
				</g>
				<path class="doc-jagadale-loader-rope doc-jagadale-loader-rope-1" pathLength="100" d="M39 78c8-4 18-1 27-6"/>
				<path class="doc-jagadale-loader-rope doc-jagadale-loader-rope-2" pathLength="100" d="M40 90c8-4 18-1 27-6"/>
				<path class="doc-jagadale-loader-rope doc-jagadale-loader-rope-3" pathLength="100" d="M42 102c8-4 17-1 26-6"/>
			</svg>
		</div>

		<?php if ( ! empty( $config['show_tagline'] ) ) { ?>
			<p class="doc-jagadale-loader-tagline" aria-hidden="true"><?php echo esc_html( $business['tagline'] ); ?></p>
		<?php } ?>

		<div class="doc-jagadale-loader-track" aria-hidden="true">
			<span class="doc-jagadale-loader-bar"></span>
		</div>
	</div>
</div>
<script id="doc-jagadale-loader-js">
/*
 * The controller. An IIFE with no globals and no dependencies - it runs before
 * jQuery, before the theme's scripts, and before anything can fail underneath
 * it.
 */
(function () {
	'use strict';

	var root = document.documentElement;
	var el = document.getElementById('doc-jagadale-loader');

	if (!el) { return; }

	/*
	 * The stage's markup, kept as a string before the overlay is removed. The
	 * outgoing-navigation overlay further down is built from this rather than
	 * from a second hand-written copy - one drawing, one place to change it.
	 */
	var stageEl = el.querySelector('.doc-jagadale-loader-stage');
	var stage = stageEl ? stageEl.innerHTML : '';

	var MIN = <?php echo (int) $config['min_duration']; ?>;
	var COVER_EXIT = <?php echo ! empty( $config['cover_exit'] ) ? 'true' : 'false'; ?>;
	var start = Date.now();
	var finished = false;

	root.classList.add('doc-jagadale-loader-locked');

	function progress(v) {
		if (el) { el.style.setProperty('--doc-jagadale-loader-progress', v); }
	}

	function remove() {
		if (el && el.parentNode) { el.parentNode.removeChild(el); }
		root.classList.remove('doc-jagadale-loader-locked');
		el = null;
	}

	// A little movement while the document is still parsing, so the bar is
	// never sitting at zero looking stuck.
	progress(0.15);
	document.addEventListener('readystatechange', function () {
		if (!finished) { progress(document.readyState === 'interactive' ? 0.6 : 0.88); }
	});

	function finish() {
		if (finished) { return; }
		finished = true;

		// Hold the floor. A page served from cache paints in 40ms, and an
		// overlay seen for 40ms is not a transition, it is a flash.
		var wait = Math.max(0, MIN - (Date.now() - start));

		window.setTimeout(function () {
			if (!el) { return; }

			el.classList.remove('doc-jagadale-loader-waiting');
			progress(1);

			// Let the bar reach the end of its track before the ground goes.
			window.setTimeout(function () {
				if (!el) { return; }
				el.classList.add('doc-jagadale-loader-done');

				/*
				 * Removed on transitionend rather than a matching timeout, so
				 * the node goes when the fade actually ends. The timeout behind
				 * it is the guarantee: transitionend does not fire in a
				 * background tab, and the element must not survive that.
				 */
				var gone = false;
				function drop() { if (!gone) { gone = true; remove(); } }

				el.addEventListener('transitionend', drop, { once: true });
				window.setTimeout(drop, 600);
			}, 200);
		}, wait);
	}

	// Hand over to the indeterminate shuttle if the page is still loading.
	window.setTimeout(function () {
		if (!finished && el) { el.classList.add('doc-jagadale-loader-waiting'); }
	}, Math.max(MIN, 900));

	if (document.readyState === 'complete') {
		finish();
	} else {
		window.addEventListener('load', finish);
	}

	/*
	 * Last resort. If 'load' never fires - a hung third-party request, an image
	 * that stalls - the visitor still gets the site. The CSS failsafe at eight
	 * seconds sits behind even this.
	 */
	window.setTimeout(finish, 6000);

	/*
	 * Coming back through the back/forward cache fires pageshow, not load, and
	 * the overlay would otherwise be restored from the snapshot and left up
	 * over a page that is already complete.
	 */
	window.addEventListener('pageshow', function (e) { if (e.persisted) { remove(); } });

	/* --------------------------------------------------------------------
	 * Covering the outgoing navigation
	 *
	 * Clicking a link on a multi-page site leaves the visitor on a dead page
	 * until the server answers. Painting the overlay on the way out covers
	 * that, and because the arriving document paints the identical overlay,
	 * the join between the two is invisible.
	 *
	 * The guards below are the whole job. Get any of them wrong and the
	 * overlay appears over a page that is not going anywhere, which is the
	 * one failure worse than no loader at all.
	 * ------------------------------------------------------------------ */
	if (!COVER_EXIT) { return; }

	document.addEventListener('click', function (e) {
		// Modified clicks open a new tab or download; this page is staying put.
		if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }

		var a = e.target.closest ? e.target.closest('a') : null;
		if (!a || !a.href) { return; }

		// Anything that opens elsewhere, downloads, or is not http(s) - a
		// tel: or mailto: link hands off to another app and leaves the page
		// exactly where it was.
		if (a.target && a.target !== '_self') { return; }
		if (a.hasAttribute('download')) { return; }
		if (a.protocol !== 'http:' && a.protocol !== 'https:') { return; }
		if (a.origin !== window.location.origin) { return; }
		if (a.getAttribute('href').indexOf('#') === 0) { return; }

		/*
		 * A same-page anchor. The mega menu is full of these - every item
		 * points at /speciality/#knee-pain - and from the Speciality page
		 * itself those are jumps, not navigations. Comparing path and query
		 * rather than the whole href is what tells the two apart.
		 */
		if (a.pathname === window.location.pathname && a.search === window.location.search) { return; }

		// Re-show the overlay for the outgoing trip. A fresh node rather than
		// the old one, which by now has been removed from the document.
		var out = document.createElement('div');
		out.className = 'doc-jagadale-loader';
		out.setAttribute('aria-hidden', 'true');
		out.innerHTML = '<div class="doc-jagadale-loader-stage">' + stage +
			'</div>';

		// Straight to the indeterminate shuttle: there is nothing to measure
		// on the way out, and a bar creeping to a number we invented would be
		// a lie about progress we cannot see.
		out.classList.add('doc-jagadale-loader-waiting');
		document.body.appendChild(out);
		root.classList.add('doc-jagadale-loader-locked');
	}, false);
}());
</script>
		<?php
	}
}
