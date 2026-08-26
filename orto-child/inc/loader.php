<?php
/**
 * The page loader
 *
 * A white overlay shown on every navigation, carrying a single animated mark: a
 * joint flexing.
 *
 * THE IDEA
 *
 * Two bones and a pivot. The upper one holds still, the lower one swings
 * through a flexion arc and comes back, the way a knee or an elbow does. It is
 * the clinic's tagline made literal - Ensuring Painfree Mobility - and it is
 * the one thing an orthopaedic practice does that a circle spinning cannot say.
 *
 * Drawn entirely in CSS. No logo, no photograph, no SVG, no request: three
 * elements, a rotation and a pivot. That matters more than it sounds, because
 * this now runs on every page view and anything it has to fetch is a thing that
 * can arrive late and be seen arriving.
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

/* The joint ---------------------------------------------------------------
   An upper bone that holds still, a pivot, and a lower bone that swings
   through a flexion arc and comes back. Three elements and one rotation.

   The proportions are the point: the shaft is slim, the ends are round, and
   the pivot is wider than either shaft - which is roughly how a condyle sits
   against a shaft, and is what stops the pair reading as two sticks.

   The colours are literal rather than custom properties: this paints before
   the theme's stylesheet has been parsed, so there is no --theme-color-* to
   read yet. They are the skin's own sage, with the fixed bone and the pivot in
   the deeper #4C6A44 the call-to-action band uses - the skin's sage alone is
   about 2.5:1 on its warm white, which is fine for a link and thin for the only
   mark on the screen. Keep them in step by hand if the skin ever changes. */
.doc-jagadale-loader-joint {
	position: relative;
	width: 88px;
	height: 114px;
}

.doc-jagadale-loader-bone {
	position: absolute;
	left: 50%;
	width: 12px;
	height: 47px;
	margin-left: -6px;
	/* An ellipse rather than a pill: the long axis is barely curved and the
	   ends are properly round, which is what makes it read as bone rather
	   than as a rounded rectangle. */
	border-radius: 6px / 10px;
	background-color: #4C6A44;
}

/* The femur, above the joint, fixed. */
.doc-jagadale-loader-bone-upper {
	top: 5px;
}

/* The tibia, below the joint, swinging. transform-origin sits at the top of
   the bone - the pivot itself - so it rotates about the joint rather than
   about its own middle. */
.doc-jagadale-loader-bone-lower {
	top: 62px;
	background-color: #7BA16E;
	transform-origin: 50% 5px;
	animation: doc-jagadale-loader-flex 1.75s cubic-bezier(0.45, 0, 0.55, 1) infinite;
}

/*
 * The flexion. Out to 38 degrees and back, with the pause built into the
 * keyframe rather than into a delay - a delay would restart the easing and the
 * swing would stutter at the top of every cycle.
 *
 * 38 is chosen, not arbitrary: enough that the movement is unmistakable at
 * 88px, and short of the angle where the lower bone crosses the tagline
 * beneath it.
 */
@keyframes doc-jagadale-loader-flex {
	0%, 8%    { transform: rotate(0deg); }
	46%, 54%  { transform: rotate(34deg); }
	92%, 100% { transform: rotate(0deg); }
}

/* The joint itself. Sits over both bones, so the shafts disappear behind it
   and the pair articulates rather than overlapping. */
.doc-jagadale-loader-pivot {
	position: absolute;
	top: 47px;
	left: 50%;
	width: 23px;
	height: 23px;
	margin-left: -11.5px;
	border-radius: 50%;
	background-color: #4C6A44;
}

/* A hairline ring around the joint, breathing very slightly in time with the
   swing. The only flourish in the piece, and one is the right number. */
.doc-jagadale-loader-pivot::after {
	content: "";
	position: absolute;
	inset: -8px;
	border: 1.5px solid rgba(123, 161, 110, 0.5);
	border-radius: 50%;
	animation: doc-jagadale-loader-pulse 1.75s cubic-bezier(0.45, 0, 0.55, 1) infinite;
}

@keyframes doc-jagadale-loader-pulse {
	0%, 100% { transform: scale(1);    opacity: 0.9; }
	50%      { transform: scale(1.16); opacity: 0.4; }
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
	.doc-jagadale-loader-joint { transform: scale(0.78); }
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
	.doc-jagadale-loader-bone-lower,
	.doc-jagadale-loader-pivot::after {
		animation: none;
	}
	.doc-jagadale-loader-bone-lower {
		transform: rotate(16deg);
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
		<div class="doc-jagadale-loader-joint" aria-hidden="true">
			<span class="doc-jagadale-loader-bone doc-jagadale-loader-bone-upper"></span>
			<span class="doc-jagadale-loader-bone doc-jagadale-loader-bone-lower"></span>
			<span class="doc-jagadale-loader-pivot"></span>
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
		out.innerHTML = '<div class="doc-jagadale-loader-stage">' +
			'<div class="doc-jagadale-loader-joint">' +
			'<span class="doc-jagadale-loader-bone doc-jagadale-loader-bone-upper"></span>' +
			'<span class="doc-jagadale-loader-bone doc-jagadale-loader-bone-lower"></span>' +
			'<span class="doc-jagadale-loader-pivot"></span>' +
			'</div>' +
			<?php echo ! empty( $config['show_tagline'] ) ? "'<p class=\"doc-jagadale-loader-tagline\">" . esc_js( $business['tagline'] ) . "</p>' +" : ''; ?>
			'<div class="doc-jagadale-loader-track">' +
			'<span class="doc-jagadale-loader-bar"></span></div></div>';

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
