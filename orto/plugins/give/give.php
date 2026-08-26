<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

if ( ! defined( 'ORTO_GIVE_FORMS_PT_FORMS' ) )			define( 'ORTO_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'ORTO_GIVE_FORMS_PT_PAYMENT' ) )			define( 'ORTO_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'ORTO_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'ORTO_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'ORTO_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'ORTO_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );


// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'orto_give_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'orto_give_theme_setup3', 3 );
	function orto_give_theme_setup3() {
		if ( orto_exists_give() ) {
			// Section 'Give'
			orto_storage_merge_array(
				'options', '', array_merge(
					array(
						'give' => array(
							'title' => esc_html__( 'Give Donations', 'orto' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the Give Donations pages', 'orto' ) ),
							'icon'  => 'icon-donation',
							'type'  => 'section',
						),
					),
					orto_options_get_list_cpt_options( 'give', esc_html__( 'Give Donations', 'orto' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'orto_give_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'orto_give_theme_setup9', 9 );
	function orto_give_theme_setup9() {
		if ( orto_exists_give() ) {
			add_action( 'wp_enqueue_scripts', 'orto_give_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_give', 'orto_give_frontend_scripts', 10, 1 );
			add_filter( 'orto_filter_merge_styles', 'orto_give_merge_styles' );
			add_filter( 'orto_filter_get_post_categories', 'orto_give_get_post_categories', 10, 2 );
			add_filter( 'orto_filter_post_type_taxonomy', 'orto_give_post_type_taxonomy', 10, 2 );
			add_filter( 'orto_filter_detect_blog_mode', 'orto_give_detect_blog_mode' );
			add_filter( 'give_get_locate_template', 'orto_give_get_locate_template', 100, 3 );
			add_filter( 'give_get_template_part', 'orto_give_get_template_part', 100, 3 );
			add_filter( 'trx_addons_filter_elementor_animate_items', 'orto_give_elementor_animate_items', 10, 1 );
		}
		if ( is_admin() ) {
			add_filter( 'orto_filter_tgmpa_required_plugins', 'orto_give_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'orto_give_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('orto_filter_tgmpa_required_plugins', 'orto_give_tgmpa_required_plugins');
	function orto_give_tgmpa_required_plugins( $list = array() ) {
		if ( orto_storage_isset( 'required_plugins', 'give' ) && orto_storage_get_array( 'required_plugins', 'give', 'install' ) !== false ) {
			$list[] = array(
				'name'     => orto_storage_get_array( 'required_plugins', 'give', 'title' ),
				'slug'     => 'give',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'orto_exists_give' ) ) {
	function orto_exists_give() {
		return class_exists( 'Give' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'orto_give_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'orto_give_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_give', 'orto_give_frontend_scripts', 10, 1 );
	function orto_give_frontend_scripts( $force = false ) {
		orto_enqueue_optimized( 'give', $force, array(
			'css' => array(
				'orto-give' => array( 'src' => 'plugins/give/give.css' ),
			)
		) );
	}
}

// Merge custom styles
if ( ! function_exists( 'orto_give_merge_styles' ) ) {
	//Handler of the add_filter('orto_filter_merge_styles', 'orto_give_merge_styles');
	function orto_give_merge_styles( $list ) {
		$list[ 'plugins/give/give.css' ] = false;
		return $list;
	}
}

// Return true, if current page is any give page
if ( ! function_exists( 'orto_is_give_page' ) ) {
	function orto_is_give_page() {
		$rez = false;
		if ( orto_exists_give() && ! is_search() ) {
			$page_id = is_page() ? get_the_ID() : 0;
			$rez = ( orto_is_single() && in_array( get_query_var('post_type'), array( ORTO_GIVE_FORMS_PT_FORMS, ORTO_GIVE_FORMS_PT_PAYMENT ) ) )
					|| orto_check_url( array( 'donation', 'donor' ) )
					|| is_post_type_archive( ORTO_GIVE_FORMS_PT_FORMS )
					|| is_tax( ORTO_GIVE_FORMS_TAXONOMY_CATEGORY )
					|| is_tax( ORTO_GIVE_FORMS_TAXONOMY_TAG )
					|| ( function_exists( 'is_give_form' ) && is_give_form() )
					|| ( function_exists( 'is_give_category' ) && is_give_category() )
					|| ( function_exists( 'is_give_tag' ) && is_give_tag() )
					|| ( $page_id > 0 && function_exists( 'give_get_option' )
						&& (   give_get_option( 'success_page' ) == $page_id
							|| give_get_option( 'failure_page' ) == $page_id
							|| give_get_option( 'history_page' ) == $page_id
							|| give_get_option( 'donor_dashboard_page' ) == $page_id
							|| give_get_option( 'subscriptions_page' ) == $page_id
							|| ( function_exists( 'give_is_campaign_page' ) && give_is_campaign_page() )
							)
						);
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'orto_give_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'orto_filter_detect_blog_mode', 'orto_give_detect_blog_mode' );
	function orto_give_detect_blog_mode( $mode = '' ) {
		if ( orto_is_give_page() ) {
			$mode = 'give';
		}
		return $mode;
	}
}


// Return taxonomy for current post type
if ( ! function_exists( 'orto_give_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'orto_filter_post_type_taxonomy',	'orto_give_post_type_taxonomy', 10, 2 );
	function orto_give_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( orto_exists_give() && ORTO_GIVE_FORMS_PT_FORMS == $post_type ) {
			$tax = ORTO_GIVE_FORMS_TAXONOMY_CATEGORY;
		}
		return $tax;
	}
}


// Show categories of the current product
if ( ! function_exists( 'orto_give_get_post_categories' ) ) {
	//Handler of the add_filter( 'orto_filter_get_post_categories', 'orto_give_get_post_categories', 10, 2 );
	function orto_give_get_post_categories( $cats = '', $args = array() ) {
		if ( get_post_type() == ORTO_GIVE_FORMS_PT_FORMS ) {
			$cat_sep = apply_filters(
									'orto_filter_post_meta_cat_separator',
									'<span class="post_meta_item_cat_separator">' . ( ! isset( $args['cat_sep'] ) || ! empty( $args['cat_sep'] ) ? ', ' : ' ' ) . '</span>',
									$args
									);
			$cats = orto_get_post_terms( $cat_sep, get_the_ID(), ORTO_GIVE_FORMS_TAXONOMY_CATEGORY );
		}
		return $cats;
	}
}


// Search skin-specific templates in the skin dir (if exists)
if ( ! function_exists( 'orto_give_get_locate_template' ) ) {
	//Handler of the add_filter( 'give_get_locate_template', 'orto_give_get_locate_template', 100, 3 );
	function orto_give_get_locate_template( $template, $template_name, $template_path ) {
		$folders = apply_filters( 'orto_filter_give_locate_template_folders', array(
			$template_path,
			'plugins/give/templates'
		) );
		foreach ( $folders as $f ) {
			$theme_dir = apply_filters( 'orto_filter_get_theme_file_dir', '', trailingslashit( orto_esc( $f ) ) . $template_name );
			if ( '' != $theme_dir ) {
				$template = $theme_dir;
				break;
			}
		}
		return $template;
	}
}


// Search skin-specific templates parts in the skin dir (if exists)
if ( ! function_exists( 'orto_give_get_template_part' ) ) {
	//Handler of the add_filter( 'give_get_template_part', 'orto_give_get_template_part', 100, 3 );
	function orto_give_get_template_part( $template, $slug, $name ) {
		$folders = apply_filters( 'orto_filter_give_get_template_part_folders', array(
			'give',
			'plugins/give/templates'
		) );
		foreach ( $folders as $f ) {
			$theme_dir = apply_filters( 'orto_filter_get_theme_file_dir', '', trailingslashit( orto_esc( $f ) ) . "{$slug}-{$name}.php" );
			if ( '' != $theme_dir ) {
				$template = $theme_dir;
				break;
			}
			$theme_dir = apply_filters( 'orto_filter_get_theme_file_dir', '', trailingslashit( orto_esc( $f ) ) . "{$slug}.php" );
			if ( '' != $theme_dir ) {
				$template = $theme_dir;
				break;
			}
		}
		return $template;
	}
}


// Add Give items to the separate animation list
if ( ! function_exists( 'orto_give_elementor_animate_items' ) ) {
	add_filter( 'trx_addons_filter_elementor_animate_items', 'orto_give_elementor_animate_items', 10, 1 );
	function orto_give_elementor_animate_items( $list ) {
		if ( is_array( $list ) && ! in_array( '.give-grid__item', $list ) ) {
			$list[] = '.give-grid__item';
		}
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( orto_exists_give() ) {
	$orto_fdir = orto_get_file_dir( 'plugins/give/give-style.php' );
	if ( ! empty( $orto_fdir ) ) {
		require_once $orto_fdir;
	}
}
