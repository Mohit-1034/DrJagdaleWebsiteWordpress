<?php
/**
 * Child-Theme functions and definitions
 *
 * The site is built in code rather than in wp-admin: pages, menus, the header,
 * the footer and every page's content live in this theme. Deploying the theme
 * is meant to be enough to get a working site on any environment, and a
 * database wipe is meant to cost nothing but the blog posts.
 *
 * @package ORTO
 */

// Load rtl.css because it is not autoloaded from the child theme
if ( ! function_exists( 'orto_child_load_rtl' ) ) {
	add_filter( 'wp_enqueue_scripts', 'orto_child_load_rtl', 3000 );
	function orto_child_load_rtl() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'orto-style-rtl', get_template_directory_uri() . '/rtl.css' );
		}
	}
}

/**
 * The enquiry form and its handler, shared by Contact Us and any other page
 * that wants it.
 *
 * Loaded globally rather than left in a page template: a template is only
 * included when its own page is rendered, so two pages cannot share one that
 * lives inside one of them.
 */
require_once get_stylesheet_directory() . '/inc/enquiry-form.php';

/**
 * The navigation tree, the speciality list and the service list.
 *
 * Kept in one file because the mega menu, the Speciality page, the Services
 * page and the footer all read the same arrays - add a treatment once and it
 * appears in all four.
 */
require_once get_stylesheet_directory() . '/inc/nav.php';

/**
 * The privacy and cookie policies: their shared layout, and the note on which
 * Indian instruments they are written against.
 */
require_once get_stylesheet_directory() . '/inc/legal.php';

/**
 * The arrival loader: a full-screen overlay shown while the page paints.
 * Self-contained - its CSS and its controller are inlined by that file.
 */
require_once get_stylesheet_directory() . '/inc/loader.php';


/* ---------------------------------------------------------------------------
 * Business details
 * ------------------------------------------------------------------------- */

/**
 * Clinic details, in one place.
 *
 * The header, the footer and the Contact page all show this information, so it
 * lives here rather than in any one template - change it once and every page
 * follows. Kept in code (not in theme options) so it survives a database wipe
 * or a move between environments, same as the code-driven pages below.
 *
 * Sourced from the clinic's own signage and its Google Business Profile.
 *
 * @return array
 */
if ( ! function_exists( 'orto_child_get_business' ) ) {
	function orto_child_get_business() {
		return apply_filters(
			'orto_child_business',
			array(
				'name'           => 'Dr. Jagadale\'s Orthocare & Fracture Clinic',
				/*
				 * The name in the two parts the clinic's own signage sets it in:
				 * the possessive on a small line above, the clinic name as the
				 * wordmark under it. The header lockup uses these rather than
				 * 'name', which runs to three lines in the width a header bar
				 * can spare.
				 */
				'name_prefix'    => 'Dr. Jagadale\'s',
				'name_short'     => 'Orthocare & Fracture Clinic',
				'tagline'        => 'Ensuring Painfree Mobility',
				'doctor'         => 'Dr. Ganesh Jagadale',
				'doctor_creds'   => 'M.B.B.S., D.Ortho.',
				'doctor_role'    => 'Consulting Orthopaedic & Trauma Surgeon',
				'reg_number'     => '2003010108',

				// Primary number - the one the header dials and the footer shows.
				'phone'          => '99214 58773',
				'phone_link'     => '+919921458773',
				// Second clinic line, shown alongside the first on Contact Us.
				'phone_alt'      => '82088 83110',
				'phone_alt_link' => '+918208883110',
				// Physiotherapy department, from the department board.
				'phone_physio'      => '91560 89209',
				'phone_physio_link' => '+919156089209',

				'whatsapp_link'  => 'https://wa.me/919921458773',

				// From the clinic's own printed card. A rediffmail address is
				// what the clinic publishes; swap it here if it moves.
				'email'          => 'drganeshjagadale@rediffmail.com',

				'address'        => 'Shop No. S-66, Second Floor, Destination Center 1, Near P.M.S. Office, Nanded City, Sinhgad Road, Pune, Maharashtra 411068',
				'address_short'  => 'Nanded City, Sinhgad Road, Pune',

				'facebook_url'   => 'https://www.facebook.com/p/Dr-Jagadales-Orthocare-Fracture-Clinic-And-Digital-Xray-100064227964234/',
				'instagram_url'  => 'https://www.instagram.com/dr_jagdale_nanded_city/',

				// Consulting hours, from the Google Business Profile.
				'hours'          => array(
					array( 'label' => 'Monday - Saturday', 'value' => '11:00 AM - 8:00 PM' ),
					array( 'label' => 'Sunday', 'value' => '11:00 AM - 8:00 PM' ),
				),

				'rating'         => '4.8',
				'rating_count'   => '165',
				'experience'     => '11',
				// The Nanded City clinic's first anniversary was 8 September 2021.
				'established'    => '2020',
			)
		);
	}
}

/**
 * The clinic's own domain, used for the canonical link in the footer credit.
 */
if ( ! function_exists( 'orto_child_site_domain' ) ) {
	function orto_child_site_domain() {
		return apply_filters( 'orto_child_site_domain', 'drjagadaleorthocare.com' );
	}
}


/* ---------------------------------------------------------------------------
 * Small shared helpers
 * ------------------------------------------------------------------------- */

/**
 * URL of an image shipped with the child theme, or '' if it hasn't been added.
 *
 * Bundled images rather than media-library attachments: they deploy with the
 * code, so a fresh environment is never missing its logo or its icons, and no
 * template depends on an attachment ID that exists in only one database.
 *
 * @param string $name File name without its extension, relative to images/.
 * @return string
 */
if ( ! function_exists( 'orto_child_image_url' ) ) {
	function orto_child_image_url( $name ) {
		foreach ( array( 'svg', 'webp', 'png', 'jpg', 'jpeg' ) as $ext ) {
			$file = 'images/' . $name . '.' . $ext;
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				return get_stylesheet_directory_uri() . '/' . $file;
			}
		}

		return '';
	}
}

/**
 * URL of a page by slug, falling back to another slug and finally to the home
 * page. Lets templates link to pages that have not been built yet without
 * shipping a dead link.
 *
 * @param string $slug     Preferred page slug.
 * @param string $fallback Slug to use when the preferred page doesn't exist.
 * @return string
 */
if ( ! function_exists( 'orto_child_page_url' ) ) {
	function orto_child_page_url( $slug, $fallback = '' ) {
		foreach ( array( $slug, $fallback ) as $try ) {
			if ( '' === $try ) {
				continue;
			}
			$post = get_page_by_path( $try, OBJECT, 'page' );
			if ( $post && 'publish' === $post->post_status ) {
				return get_permalink( $post );
			}
		}

		return home_url( '/' );
	}
}

/**
 * Inline SVG icons, shared by the header, the footer and the Contact page.
 *
 * Deliberately inline rather than pulled from the media library: these are used
 * by code-driven templates, so they must not depend on attachment IDs that only
 * exist in one particular database. The clinical icons are bitmaps in
 * images/icons/ instead - see orto_child_menu_icon().
 *
 * @param string $name Icon slug.
 * @return string Safe SVG markup (or an empty string for an unknown slug).
 */
if ( ! function_exists( 'orto_child_icon' ) ) {
	function orto_child_icon( $name ) {
		$open  = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true">';
		$close = '</svg>';

		/*
		 * The platforms' own marks, drawn as solid shapes rather than as line
		 * icons like the rest of this set. A brand mark redrawn as an outline
		 * stops being the brand mark, and at 20px these are the glyphs people
		 * recognise without reading a label. Filled with currentColor, so they
		 * take the colour of whatever they sit in.
		 */
		$brand = array(
			'facebook'  => '<path d="M24 12a12 12 0 1 0-13.875 11.855v-8.385H7.078V12h3.047V9.356c0-3.007 1.792-4.669 4.533-4.669 1.313 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874V12h3.328l-.532 3.47h-2.796v8.385A12 12 0 0 0 24 12Z"/>',
			'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.053 1.805.249 2.228.413.56.218.96.478 1.38.898.42.42.68.82.898 1.38.164.423.36 1.058.413 2.228.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.053 1.17-.249 1.805-.413 2.228a3.72 3.72 0 0 1-.898 1.38 3.72 3.72 0 0 1-1.38.898c-.423.164-1.058.36-2.228.413-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.053-1.805-.249-2.228-.413a3.72 3.72 0 0 1-1.38-.898 3.72 3.72 0 0 1-.898-1.38c-.164-.423-.36-1.058-.413-2.228-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.053-1.17.249-1.805.413-2.228a3.72 3.72 0 0 1 .898-1.38 3.72 3.72 0 0 1 1.38-.898c.423-.164 1.058-.36 2.228-.413 1.266-.058 1.646-.07 4.85-.07M12 0C8.741 0 8.332.014 7.052.072 5.775.13 4.902.333 4.14.63a5.88 5.88 0 0 0-2.126 1.384A5.88 5.88 0 0 0 .63 4.14C.333 4.902.13 5.775.072 7.052.014 8.332 0 8.741 0 12s.014 3.668.072 4.948c.058 1.277.261 2.15.558 2.912a5.88 5.88 0 0 0 1.384 2.126A5.88 5.88 0 0 0 4.14 23.37c.762.297 1.635.5 2.912.558C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.277-.058 2.15-.261 2.912-.558a5.88 5.88 0 0 0 2.126-1.384 5.88 5.88 0 0 0 1.384-2.126c.297-.762.5-1.635.558-2.912.058-1.28.072-1.689.072-4.948s-.014-3.668-.072-4.948c-.058-1.277-.261-2.15-.558-2.912a5.88 5.88 0 0 0-1.384-2.126A5.88 5.88 0 0 0 19.86.63c-.762-.297-1.635-.5-2.912-.558C15.668.014 15.259 0 12 0Zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.406-11.845a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88Z"/>',
			'whatsapp'  => '<path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.767.967-.94 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479c0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>',
		);

		/*
		 * Google's own mark, in its own four colours rather than currentColor -
		 * a monochrome G is not the Google logo, and this sits on the reviews
		 * band precisely to say where the reviews came from.
		 */
		if ( 'google' === $name ) {
			return '<svg viewBox="0 0 24 24" width="20" height="20" focusable="false" aria-hidden="true">'
				. '<path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47a5.4 5.4 0 0 1-2.4 3.58v2.77h3.86c2.26-2.09 3.56-5.17 3.56-8.59Z"/>'
				. '<path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09A11.99 11.99 0 0 0 12 24Z"/>'
				. '<path fill="#FBBC05" d="M5.27 14.29A7.2 7.2 0 0 1 4.89 12c0-.8.14-1.57.38-2.29V6.62H1.29A11.99 11.99 0 0 0 0 12c0 1.94.46 3.77 1.29 5.38l3.98-3.09Z"/>'
				. '<path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.7 0 3.99 2.47 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75Z"/>'
				. '</svg>';
		}

		if ( ! empty( $brand[ $name ] ) ) {
			return '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" focusable="false" aria-hidden="true">'
				. $brand[ $name ]
				. '</svg>';
		}

		$paths = array(
			'phone'    => '<path d="M6.5 3h3l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5L16 12l4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 5.2 2 2 0 0 1 6.5 3Z"/>',
			'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
			'pin'      => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
			'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5.2l3.2 2"/>',
			'calendar' => '<rect x="3.5" y="5.2" width="17" height="15.3" rx="2.5"/><path d="M3.5 10.2h17"/><path d="M8 3.6V6.4"/><path d="M16 3.6V6.4"/><path d="m9.4 14.6 1.9 1.9 3.5-3.7"/>',
			'shield'   => '<path d="M12 3l7 2.6v5.2c0 4.3-2.9 8.2-7 10.2-4.1-2-7-5.9-7-10.2V5.6L12 3Z"/><path d="m9 12 2.2 2.2L15.4 10"/>',
			'badge'    => '<circle cx="12" cy="9.5" r="5.5"/><path d="m8.4 14.2-1.2 6.3 4.8-2.5 4.8 2.5-1.2-6.3"/>',
			'star'     => '<path d="m12 3.5 2.6 5.4 5.9.8-4.3 4.1 1 5.9-5.2-2.8-5.2 2.8 1-5.9L3.5 9.7l5.9-.8L12 3.5Z"/>',
			'arrow'    => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
			'check'    => '<circle cx="12" cy="12" r="9"/><path d="m8.2 12.2 2.6 2.6 5-5.4"/>',
		);

		if ( empty( $paths[ $name ] ) ) {
			return '';
		}

		return $open . $paths[ $name ] . $close;
	}
}

/**
 * A bundled clinical icon, as an <img>.
 *
 * These are the line drawings the brief supplied (DraftContent/HomePageImages),
 * renamed by what they depict and shipped in images/icons/. Bitmaps rather than
 * SVG because that is the form they arrived in; they are drawn at 2x the size
 * they display at, so they hold up on a retina screen.
 *
 * @param string $name Icon file name without its extension.
 * @param string $class Extra class for the <img>.
 * @return string Safe markup, or '' when the file isn't bundled.
 */
if ( ! function_exists( 'orto_child_menu_icon' ) ) {
	function orto_child_menu_icon( $name, $class = '' ) {
		$url = orto_child_image_url( 'icons/' . $name );

		if ( '' === $url ) {
			return '';
		}

		return sprintf(
			'<img class="djo_icon%1$s" src="%2$s" alt="" loading="lazy" decoding="async" width="48" height="48">',
			$class ? ' ' . esc_attr( $class ) : '',
			esc_url( $url )
		);
	}
}


/* ---------------------------------------------------------------------------
 * Logo and page chrome
 * ------------------------------------------------------------------------- */

/**
 * Put the bundled logo in the site header.
 *
 * The theme reads its logo from the 'custom_logo' theme mod, so filtering that
 * is enough to make the header, the sticky header and the mobile menu all show
 * it - no template override, and nothing to set in wp-admin on a new install.
 *
 * Front end only: in wp-admin the Customizer's site-identity panel expects an
 * attachment ID here and would be confused by a URL. Anyone who does upload a
 * logo through the Customizer keeps their upload - an explicit choice made in
 * the admin should always beat a default shipped in code.
 */
if ( ! function_exists( 'orto_child_use_bundled_logo' ) ) {
	add_filter( 'theme_mod_custom_logo', 'orto_child_use_bundled_logo' );
	function orto_child_use_bundled_logo( $logo ) {
		if ( is_admin() || ! empty( $logo ) ) {
			return $logo;
		}

		$bundled = orto_child_image_url( 'logo-mark' );

		return $bundled ? $bundled : $logo;
	}
}

/**
 * Keep the site name available as the logo's alt text.
 *
 * The theme uses the "logo text" option for two things: the wordmark shown when
 * no logo image is set, and the alt attribute of the logo image when one is.
 * Leaving it on means the logo is described to screen readers, and that a
 * missing image file degrades to the clinic's name rather than to nothing.
 */
if ( ! function_exists( 'orto_child_keep_logo_text' ) ) {
	add_filter( 'theme_mod_logo_text', 'orto_child_keep_logo_text' );
	function orto_child_keep_logo_text( $value ) {
		return is_admin() ? $value : 1;
	}
}

/**
 * Drop the theme's centred page-title band on the pages that open with their
 * own hero.
 *
 * Those templates already lead with an eyebrow and a large headline, so the
 * theme's title block repeated the page name immediately above it and pushed
 * the real opening down behind a screen of empty space. Contact Us keeps it -
 * it has no hero of its own and the title is doing useful work there.
 */
if ( ! function_exists( 'orto_child_hide_page_title' ) ) {
	add_filter( 'orto_filter_need_page_title', 'orto_child_hide_page_title' );
	function orto_child_hide_page_title( $need ) {
		$templates = array(
			'template-about.php',
			'template-speciality.php',
			'template-services.php',
			'template-privacy.php',
			'template-cookies.php',
		);

		foreach ( $templates as $template ) {
			if ( is_page_template( $template ) ) {
				return false;
			}
		}

		return $need;
	}
}

/**
 * No sidebar on the home page.
 *
 * WordPress serves the front page through the blog branch of the theme, which
 * brings the blog's sidebar with it - a search box and a list of recent posts
 * down the right-hand side of a page that has nothing to do with the blog, and
 * which would squeeze every section of the home page into two thirds of the
 * width.
 */
if ( ! function_exists( 'orto_child_no_sidebar_on_front' ) ) {
	add_filter( 'orto_filter_sidebar_present', 'orto_child_no_sidebar_on_front' );
	function orto_child_no_sidebar_on_front( $present ) {
		return is_front_page() ? false : $present;
	}
}

/**
 * Mark "Book Appointment" in the main menu so it can be styled as the call to
 * action.
 *
 * Done with a filter rather than by saving a CSS class on the menu item: the
 * menu is built in code by orto_child_ensure_menus(), so a class stored in the
 * database would be lost the moment that ran again.
 *
 * @param array  $classes Existing classes.
 * @param object $item    Menu item.
 * @return array
 */
if ( ! function_exists( 'orto_child_mark_menu_cta' ) ) {
	add_filter( 'nav_menu_css_class', 'orto_child_mark_menu_cta', 10, 2 );
	function orto_child_mark_menu_cta( $classes, $item ) {
		$cta = apply_filters( 'orto_child_menu_cta_slug', 'contact-us' );

		if ( empty( $item->object_id ) || 'post_type' !== $item->type ) {
			return $classes;
		}

		$page = get_post( $item->object_id );

		if ( $page && $cta === $page->post_name ) {
			$classes[] = 'djo_menu_cta';
		}

		return $classes;
	}
}


/* ---------------------------------------------------------------------------
 * Assets
 * ------------------------------------------------------------------------- */

/**
 * Version the child stylesheet by its modification time.
 *
 * The parent enqueues it with a null version, which means the URL never changes
 * and browsers keep serving a cached copy after a deploy. Stamping the file's
 * mtime on it means a changed stylesheet is always a new URL, and an unchanged
 * one still caches for as long as it likes.
 */
if ( ! function_exists( 'orto_child_version_stylesheet' ) ) {
	add_filter( 'style_loader_src', 'orto_child_version_stylesheet', 10, 2 );
	function orto_child_version_stylesheet( $src, $handle ) {
		if ( 'orto-child' !== $handle ) {
			return $src;
		}

		$path = get_stylesheet_directory() . '/style.css';

		if ( ! file_exists( $path ) ) {
			return $src;
		}

		return add_query_arg( 'ver', filemtime( $path ), $src );
	}
}

/**
 * Load the child theme's own scripts.
 *
 * Versioned by file modification time so a deploy busts the cache without
 * anyone having to remember to bump a number.
 */
if ( ! function_exists( 'orto_child_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'orto_child_enqueue_scripts' );
	function orto_child_enqueue_scripts() {
		foreach ( array( 'header', 'mega-menu' ) as $script ) {
			$path = get_stylesheet_directory() . '/js/' . $script . '.js';

			if ( ! file_exists( $path ) ) {
				continue;
			}

			wp_enqueue_script(
				'orto-child-' . $script,
				get_stylesheet_directory_uri() . '/js/' . $script . '.js',
				array(),
				(string) filemtime( $path ),
				true
			);
		}
	}
}


/* ---------------------------------------------------------------------------
 * Footer
 * ------------------------------------------------------------------------- */

/**
 * Replace the parent theme's footer entirely.
 *
 * The parent's footer is widget-driven; this one is not, so the footer is part
 * of the deploy rather than something that has to be rebuilt by hand in
 * wp-admin on every environment.
 */
if ( ! function_exists( 'orto_child_use_custom_footer' ) ) {
	add_filter( 'orto_filter_get_template_part', 'orto_child_use_custom_footer', 20 );
	function orto_child_use_custom_footer( $slug ) {
		$outer_footers = array( 'footer-default', 'footer-custom' );

		if ( in_array( basename( $slug ), $outer_footers, true ) ) {
			return 'templates/footer-jagadale';
		}

		return $slug;
	}
}

/**
 * The footer's "Pages" column.
 *
 * This is the intended sitemap, not just the pages that happen to exist today.
 * Entries whose page has not been built yet are skipped at render time rather
 * than shipped as dead links, so each one appears in the footer by itself as
 * soon as the page is published at that slug.
 */
if ( ! function_exists( 'orto_child_get_footer_pages' ) ) {
	function orto_child_get_footer_pages() {
		return apply_filters(
			'orto_child_footer_pages',
			array(
				array( 'slug' => '', 'title' => __( 'Home', 'orto' ) ),
				array( 'slug' => 'about-us', 'title' => __( 'About Us', 'orto' ) ),
				array( 'slug' => 'speciality', 'title' => __( 'Speciality', 'orto' ) ),
				array( 'slug' => 'services', 'title' => __( 'Services', 'orto' ) ),
				array( 'slug' => 'contact-us', 'title' => __( 'Contact Us', 'orto' ) ),
			)
		);
	}
}

/**
 * The footer's small-print legal links. Same skip-if-missing rule as above.
 */
if ( ! function_exists( 'orto_child_get_footer_legal_pages' ) ) {
	function orto_child_get_footer_legal_pages() {
		return apply_filters(
			'orto_child_footer_legal_pages',
			array(
				array( 'slug' => 'privacy-policy', 'title' => __( 'Privacy Policy', 'orto' ) ),
				array( 'slug' => 'cookie-policy', 'title' => __( 'Cookie Policy', 'orto' ) ),
			)
		);
	}
}

/**
 * Resolve a footer link list to [ url, title ] pairs, dropping pages that do
 * not exist yet. An empty slug means the site's front page, which always does.
 *
 * @param array $pages List of array( 'slug' => ..., 'title' => ... ).
 * @return array
 */
if ( ! function_exists( 'orto_child_resolve_footer_links' ) ) {
	function orto_child_resolve_footer_links( $pages ) {
		$links = array();

		foreach ( $pages as $page ) {
			if ( '' === $page['slug'] ) {
				$links[] = array(
					'url'   => home_url( '/' ),
					'title' => $page['title'],
				);
				continue;
			}

			$post = get_page_by_path( $page['slug'], OBJECT, 'page' );

			if ( ! $post || 'publish' !== $post->post_status ) {
				continue;
			}

			$links[] = array(
				'url'   => get_permalink( $post ),
				'title' => $page['title'],
			);
		}

		return $links;
	}
}


/* ---------------------------------------------------------------------------
 * Shared page furniture
 * ------------------------------------------------------------------------- */

/**
 * The class for the next full-width band on this page.
 *
 * The page alternates light and dark the whole way down. That used to be
 * written out by hand on each section, and by $index % 2 for the run of
 * entries on the Speciality and Services pages - which is fragile in a way
 * that had already broken: taking one entry out of the list flipped the
 * parity of everything after it, so the last entry and the call to action
 * ended up both light and the seam between them vanished.
 *
 * A counter cannot get that wrong. Each call returns the opposite of the last,
 * so no page can hold two adjacent bands of the same value however many
 * sections it happens to have.
 *
 * Pass 'light' or 'dark' to force one - the hero has to be dark and a page
 * hero has to be light, and those are design decisions rather than positions
 * in a sequence. Forcing also re-seeds the counter, so everything after it
 * carries on alternating correctly from there.
 *
 * The call-to-action band is deliberately NOT part of this. It is the page's
 * closing action rather than another content section, it has its own ground,
 * and keeping it out of the sequence is what stops it being able to break the
 * sequence - see orto_child_cta_band().
 *
 * @param string $force 'light', 'dark', or '' to alternate.
 * @return string
 */
if ( ! function_exists( 'orto_child_band_class' ) ) {
	function orto_child_band_class( $force = '' ) {
		static $dark = false;

		if ( 'dark' === $force || 'light' === $force ) {
			$dark = ( 'dark' === $force );
		} else {
			$dark = ! $dark;
		}

		return $dark ? 'djo_band_dark' : 'djo_band_light';
	}
}

/**
 * The call-to-action band that closes most pages.
 *
 * One function rather than a copy in each template, so the wording and the
 * buttons stay identical everywhere they appear.
 *
 * @param array $args {
 *     @type string $eyebrow Small line above the heading.
 *     @type string $title   The heading.
 *     @type string $text    A sentence under it. Pass '' for none.
 * }
 */
if ( ! function_exists( 'orto_child_cta_band' ) ) {
	function orto_child_cta_band( $args = array() ) {
		$business = orto_child_get_business();

		$args = wp_parse_args(
			$args,
			array(
				'eyebrow' => __( 'Book a consultation', 'orto' ),
				'title'   => __( 'Walk in with pain. Walk out with a plan.', 'orto' ),
				'text'    => __( 'A consultation, an X-ray on site if you need one, and a treatment plan explained in plain language before anything begins.', 'orto' ),
			)
		);

		/*
		 * The two things a visitor weighs in the second before booking:
		 * whether they can be seen soon and whether they will be sent
		 * somewhere else afterwards. Answering both at the point of decision
		 * removes more friction than any amount of persuasion above it.
		 */
		$assurances = apply_filters(
			'orto_child_cta_assurances',
			array(
				__( 'Open seven days, 11am to 8pm', 'orto' ),
				__( 'Digital X-ray on site', 'orto' ),
			)
		);
		?>
		<section class="djo_cta djo_band djo_band_accent">
			<span class="djo_spine djo_spine_cta" aria-hidden="true"></span>

			<div class="content_wrap">
				<div class="djo_cta_inner">
					<span class="djo_eyebrow djo_cta_eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
					<h2 class="djo_cta_title"><?php echo esc_html( $args['title'] ); ?></h2>
					<?php if ( '' !== $args['text'] ) { ?>
						<p class="djo_cta_text"><?php echo esc_html( $args['text'] ); ?></p>
					<?php } ?>

					<div class="djo_cta_actions">
						<a class="djo_button djo_button_solid djo_button_lg" href="<?php echo esc_url( orto_child_page_url( 'contact-us' ) ); ?>">
							<?php esc_html_e( 'Book an Appointment', 'orto' ); ?>
							<?php echo orto_child_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php if ( ! empty( $business['phone'] ) ) { ?>
							<?php
							/*
							 * The phone is a peer of the button, not a footnote.
							 * A good share of the people reading this would
								 * rather speak to somebody than fill in a form,
							 * and burying that costs bookings.
							 */
							?>
							<a class="djo_button djo_button_ghost djo_button_lg" href="tel:<?php echo esc_attr( $business['phone_link'] ); ?>">
								<?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo esc_html( $business['phone'] ); ?>
							</a>
						<?php } ?>
					</div>

					<?php if ( ! empty( $assurances ) ) { ?>
						<ul class="djo_cta_assurances">
							<?php foreach ( $assurances as $assurance ) { ?>
								<li>
									<span class="djo_cta_tick" aria-hidden="true"><?php echo orto_child_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<?php echo wp_kses_post( $assurance ); ?>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/**
 * A section heading: small eyebrow, large title, optional standfirst.
 *
 * @param array $args See $defaults below.
 */
if ( ! function_exists( 'orto_child_section_head' ) ) {
	function orto_child_section_head( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow' => '',
				'title'   => '',
				'text'    => '',
				'align'   => 'center',
				'tag'     => 'h2',
			)
		);

		$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
		?>
		<header class="djo_head djo_head_<?php echo esc_attr( $args['align'] ); ?>">
			<?php if ( '' !== $args['eyebrow'] ) { ?>
				<span class="djo_eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
			<?php } ?>
			<<?php echo esc_html( $tag ); ?> class="djo_head_title"><?php echo wp_kses_post( $args['title'] ); ?></<?php echo esc_html( $tag ); ?>>
			<?php if ( '' !== $args['text'] ) { ?>
				<p class="djo_head_text"><?php echo wp_kses_post( $args['text'] ); ?></p>
			<?php } ?>
		</header>
		<?php
	}
}

/**
 * The orthopaedic conditions the clinic treats, for the home page's rail.
 *
 * Deliberately a flat list of named conditions rather than the joint-by-joint
 * tree in inc/nav.php. The two answer different questions: the Speciality menu
 * answers "where does it hurt", which is what a patient can name on arrival;
 * this answers "what is it called", which is what someone who has already been
 * given a diagnosis elsewhere is searching for. Keeping them separate is also
 * what lets this rail stay eight items long - the width of the rail is the
 * constraint, and it should not be set by how many joints the menu covers.
 *
 * 'icon' is a file name in images/icons/. 'label' is broken over two lines in
 * the rail, so keep each one short.
 *
 * @return array
 */
if ( ! function_exists( 'orto_child_get_conditions' ) ) {
	function orto_child_get_conditions() {
		return apply_filters(
			'orto_child_conditions',
			array(
				array( 'icon' => 'joint',        'label' => __( 'Osteoarthritis', 'orto' ) ),
				array( 'icon' => 'slipped-disc', 'label' => __( 'Slipped Disc &amp; Sciatica', 'orto' ) ),
				array( 'icon' => 'fracture',     'label' => __( 'Fractures &amp; Dislocations', 'orto' ) ),
				array( 'icon' => 'spine-pain',   'label' => __( 'Cervical Spondylosis', 'orto' ) ),
				array( 'icon' => 'shoulder',     'label' => __( 'Frozen Shoulder', 'orto' ) ),
				array( 'icon' => 'knee',         'label' => __( 'Ligament &amp; ACL Injury', 'orto' ) ),
				array( 'icon' => 'hand',         'label' => __( 'Nerve Compression', 'orto' ) ),
				array( 'icon' => 'foot',         'label' => __( 'Heel &amp; Foot Pain', 'orto' ) ),
			)
		);
	}
}

/**
 * The signs that mean a joint or a back is worth having looked at.
 *
 * Written as things a patient can check against themselves this evening, not as
 * a list of diagnoses - the point of the section is to turn "it will probably
 * settle" into a decision. The red-flag note that follows it on the home page
 * is separate and deliberately worded to send people to a hospital rather than
 * to this clinic.
 *
 * @return array
 */
if ( ! function_exists( 'orto_child_get_symptoms' ) ) {
	function orto_child_get_symptoms() {
		return apply_filters(
			'orto_child_symptoms',
			array(
				__( 'The pain has not settled after two weeks of rest', 'orto' ),
				__( 'It started with a fall, a blow or an accident', 'orto' ),
				__( 'Pain shoots down an arm or a leg', 'orto' ),
				__( 'The joint hurts at rest, or wakes you at night', 'orto' ),
				__( 'A joint is swollen, locked, or out of shape', 'orto' ),
				__( 'You cannot put weight on it, or it gives way', 'orto' ),
				__( 'You feel numbness, tingling or weakness in a limb', 'orto' ),
			)
		);
	}
}

/**
 * Patient reviews, as published on the clinic's Google Business Profile.
 *
 * Held in code rather than pulled from the Places API: the API costs a key, a
 * billing account and a network round trip on every page load, and it returns
 * five reviews chosen by Google rather than the ones the clinic wants shown.
 * Re-transcribe this list when the reviews on the profile change.
 *
 * @return array
 */
if ( ! function_exists( 'orto_child_get_reviews' ) ) {
	function orto_child_get_reviews() {
		return apply_filters(
			'orto_child_reviews',
			array(
				array(
					'name'   => 'Vijay Kadam',
					'rating' => 5,
					'text'   => __( 'I am very thankful to Dr. Jagadale for his excellent care. From my very first consultation to my post-operative follow-ups, the doctor was patient, empathetic, and extremely clear in explaining my condition and treatment plan.', 'orto' ),
					'for'    => __( 'Fracture treatment', 'orto' ),
				),
				array(
					'name'   => 'Anjali Ranjanikar',
					'rating' => 5,
					'text'   => __( 'The clinic is clean, well organised and the digital X-ray on site meant I did not have to run between labs. Everything was done in one visit and the follow-up was just as thorough.', 'orto' ),
					'for'    => __( 'Digital X-ray', 'orto' ),
				),
				array(
					'name'   => 'Sandeep More',
					'rating' => 5,
					'text'   => __( 'Went in for long-standing knee pain. The doctor took the time to explain what was actually wrong instead of rushing to surgery, and the physiotherapy plan has made a real difference.', 'orto' ),
					'for'    => __( 'Knee pain treatment', 'orto' ),
				),
			)
		);
	}
}

/**
 * A row of stars for a rating out of five.
 *
 * @param int $rating Whole stars to fill, 0-5.
 * @return string
 */
if ( ! function_exists( 'orto_child_stars' ) ) {
	function orto_child_stars( $rating = 5 ) {
		$rating = max( 0, min( 5, (int) $rating ) );
		$out    = '<span class="djo_stars" role="img" aria-label="'
			. esc_attr( sprintf( /* translators: %d: number of stars */ __( '%d out of 5 stars', 'orto' ), $rating ) )
			. '">';

		for ( $i = 1; $i <= 5; $i++ ) {
			$out .= '<span class="djo_star' . ( $i <= $rating ? ' djo_star_on' : '' ) . '" aria-hidden="true">'
				. '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" focusable="false"><path d="m12 3.5 2.6 5.4 5.9.8-4.3 4.1 1 5.9-5.2-2.8-5.2 2.8 1-5.9L3.5 9.7l5.9-.8L12 3.5Z"/></svg>'
				. '</span>';
		}

		return $out . '</span>';
	}
}


/* ---------------------------------------------------------------------------
 * Page and menu provisioning
 * ------------------------------------------------------------------------- */

/**
 * Auto-provision the code-driven pages.
 *
 * Deploying this theme's code is meant to be enough on its own: no manual
 * "create a page, pick a template, publish" step in wp-admin on a new
 * environment. On every environment this makes sure a page exists at the right
 * slug, wearing the right template, and not still carrying leftover Elementor
 * layout settings that would strip the page container.
 *
 * Bump ORTO_CHILD_PAGES_VERSION whenever the list below changes, so it
 * re-applies on the next request after a code deploy without the theme having
 * to be deactivated and reactivated.
 */
if ( ! defined( 'ORTO_CHILD_PAGES_VERSION' ) ) {
	define( 'ORTO_CHILD_PAGES_VERSION', '3' );
}

if ( ! function_exists( 'orto_child_get_managed_pages' ) ) {
	function orto_child_get_managed_pages() {
		return array(
			'about-us'   => array(
				'title'    => 'About Us',
				'template' => 'template-about.php',
			),
			'speciality' => array(
				'title'    => 'Speciality',
				'template' => 'template-speciality.php',
			),
			'services'   => array(
				'title'    => 'Services',
				'template' => 'template-services.php',
			),
			'contact-us' => array(
				'title'    => 'Contact Us',
				'template' => 'template-contact.php',
			),
			'privacy-policy' => array(
				'title'    => 'Privacy Policy',
				'template' => 'template-privacy.php',
			),
			'cookie-policy'  => array(
				'title'    => 'Cookie Policy',
				'template' => 'template-cookies.php',
			),
		);
	}
}

if ( ! function_exists( 'orto_child_ensure_pages' ) ) {
	function orto_child_ensure_pages() {
		foreach ( orto_child_get_managed_pages() as $orto_child_slug => $orto_child_page ) {
			$orto_child_existing = get_page_by_path( $orto_child_slug, OBJECT, 'page' );

			if ( $orto_child_existing ) {
				$orto_child_page_id = $orto_child_existing->ID;
			} else {
				$orto_child_page_id = wp_insert_post(
					array(
						'post_title'   => $orto_child_page['title'],
						'post_name'    => $orto_child_slug,
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_content' => '',
					)
				);
			}

			if ( ! $orto_child_page_id || is_wp_error( $orto_child_page_id ) ) {
				continue;
			}

			update_post_meta( $orto_child_page_id, '_wp_page_template', $orto_child_page['template'] );

			// Strip any leftover page-builder layout overrides (e.g. a
			// fullscreen/no-margin body style from a previous Elementor design)
			// that would break this template's own layout.
			delete_post_meta( $orto_child_page_id, 'orto_options' );
			delete_post_meta( $orto_child_page_id, '_elementor_edit_mode' );
		}

		orto_child_ensure_menus();

		// The home page is the theme's front-page.php, not a blog listing.
		update_option( 'show_on_front', 'posts' );
	}
}

/**
 * Build the site navigation from the pages above.
 *
 * Without this, the pages exist but nothing links to them: WordPress menus live
 * in the database, so a fresh environment shows whatever menu the theme demo
 * left behind. Deploying the theme should be enough to get a working site, so
 * the menu is assembled in code alongside the pages and assigned to the theme's
 * Main, Mobile and Footer locations.
 *
 * Existing items are cleared and rebuilt rather than merged, so the menu always
 * matches the page list exactly. Pages that don't exist are skipped.
 *
 * The Speciality and Services items get their sub-items from inc/nav.php as
 * real menu children, so the mobile menu - which the parent theme builds from
 * the same menu object - has something to expand. On desktop those children are
 * hidden and the mega panel in header-navi.php is shown instead.
 */
if ( ! function_exists( 'orto_child_ensure_menus' ) ) {
	function orto_child_ensure_menus() {
		$menu_name = 'Dr Jagadale Orthocare';
		$menu      = wp_get_nav_menu_object( $menu_name );

		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $menu_name );
			if ( is_wp_error( $menu_id ) ) {
				return;
			}
		} else {
			$menu_id = $menu->term_id;

			/*
			 * Start clean so the menu can never drift from the page list.
			 *
			 * get_objects_in_term() rather than wp_get_nav_menu_items(): the
			 * latter is filtered, cached, and returns false outside a fully
			 * booted menu context - and a false there means "delete nothing",
			 * which is how a rebuild ends up appending a second copy of every
			 * item instead of replacing the first.
			 */
			$existing = get_objects_in_term( $menu_id, 'nav_menu' );

			if ( ! is_wp_error( $existing ) ) {
				foreach ( $existing as $item_id ) {
					wp_delete_post( (int) $item_id, true );
				}
			}
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => __( 'Home', 'orto' ),
				'menu-item-url'    => home_url( '/' ),
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);

		// Slug => the nav group whose entries become that item's children.
		$order = array(
			'about-us'   => '',
			'speciality' => 'speciality',
			'services'   => 'services',
			'contact-us' => '',
		);

		foreach ( $order as $slug => $group ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );

			if ( ! $page || 'publish' !== $page->post_status ) {
				continue;
			}

			$parent_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $page->post_title,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);

			if ( '' === $group || is_wp_error( $parent_id ) ) {
				continue;
			}

			$page_url = get_permalink( $page );

			foreach ( orto_child_get_nav_group( $group ) as $entry ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $entry['title'],
						'menu-item-url'       => $page_url . '#' . $entry['slug'],
						'menu-item-type'      => 'custom',
						'menu-item-parent-id' => (int) $parent_id,
						'menu-item-status'    => 'publish',
					)
				);
			}
		}

		// Point every menu location the theme registers at this one menu.
		$locations = get_theme_mod( 'nav_menu_locations' );
		$locations = is_array( $locations ) ? $locations : array();

		foreach ( array( 'menu_main', 'menu_mobile', 'menu_footer' ) as $location ) {
			$locations[ $location ] = (int) $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

if ( ! function_exists( 'orto_child_maybe_ensure_pages' ) ) {
	add_action( 'after_switch_theme', 'orto_child_maybe_ensure_pages' );
	add_action( 'init', 'orto_child_maybe_ensure_pages' );
	function orto_child_maybe_ensure_pages() {
		if ( get_option( 'orto_child_pages_provisioned_version' ) === ORTO_CHILD_PAGES_VERSION ) {
			return;
		}

		/*
		 * Claim the version BEFORE doing the work, not after.
		 *
		 * This ran the other way round and produced a real bug: two requests
		 * arriving at the same moment - a page load and the browser's favicon
		 * or heartbeat request, say - both read the old version, both decided
		 * to rebuild, and both appended a fresh set of items to the menu. The
		 * site came back with Services and Contact Us in the navigation twice,
		 * which then overflowed the bar and pushed the parent theme's "more"
		 * chevron into the header.
		 *
		 * update_option() is a single write, so whichever request gets there
		 * first claims the rebuild and the other returns above. Doing the work
		 * afterwards means a fatal mid-rebuild would leave the version claimed
		 * and the job half done - which is why the version is a constant a
		 * developer can bump to force it again, rather than something the site
		 * is trusted to retry on its own.
		 */
		update_option( 'orto_child_pages_provisioned_version', ORTO_CHILD_PAGES_VERSION );

		orto_child_ensure_pages();
	}
}
