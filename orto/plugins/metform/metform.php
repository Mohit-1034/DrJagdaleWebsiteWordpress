<?php
/* MetForm support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'orto_metform_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'orto_metform_theme_setup9', 9 );
	function orto_metform_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'orto_filter_tgmpa_required_plugins', 'orto_metform_tgmpa_required_plugins' );
			add_filter( 'orto_filter_theme_plugins', 'orto_metform_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'orto_metform_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('orto_filter_tgmpa_required_plugins',	'orto_metform_tgmpa_required_plugins');
	function orto_metform_tgmpa_required_plugins( $list = array() ) {
		if ( orto_storage_isset( 'required_plugins', 'metform' ) && orto_storage_get_array( 'required_plugins', 'metform', 'install' ) !== false ) {
			$list[] = array(
				'name'     => orto_storage_get_array( 'required_plugins', 'metform', 'title' ),
				'slug'     => 'metform',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'orto_metform_theme_plugins' ) ) {
	//Handler of the add_filter( 'orto_filter_theme_plugins', 'orto_metform_theme_plugins' );
	function orto_metform_theme_plugins( $list = array() ) {
		return orto_add_group_and_logo_to_slave( $list, 'metform', 'metform-' );
	}
}



// Check if a plugin is installed and activated
if ( ! function_exists( 'orto_exists_metform' ) ) {
	function orto_exists_metform() {
		return class_exists( 'MetForm\Plugin' );
	}
}
