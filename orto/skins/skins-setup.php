<?php
/**
 * This file is NOT updated when the theme engine is updated
 * and contains features that affect all skins in the theme lineup.
 *
 * @package ORTO
 * @since ORTO 2.34.6
 */

if ( ! function_exists( 'orto_ai_assistant_type' ) ) {
	add_filter( 'trx_addons_filter_ai_assistant_type', 'orto_ai_assistant_type' );
	/**
	 * Set the type of AI Support Assistant to use.
	 *
	 * @since ORTO 2.34.6
	 * 
	 * @hooked trx_addons_filter_ai_assistant_type
	 * 
	 * @param string $type  The type of AI Support Assistant to use: 'v1' for Qwery-based, 'v2' for Orto-based themes
	 *
	 * @return string  The modified type of AI Support Assistant to use
	 */
	function orto_ai_assistant_type( $type = 'v1') {
		return 'v2';
	}
}

/* ThemeREX Addons components
------------------------------------------------------------------------------- */
if ( ! function_exists( 'orto_skins_trx_addons_theme_specific_setup1' ) ) {
	add_action( 'after_setup_theme', 'orto_skins_trx_addons_theme_specific_setup1', 1 );
	function orto_skins_trx_addons_theme_specific_setup1() {
		if ( orto_exists_trx_addons() ) {
			add_filter( 'trx_addons_addons_list', 'orto_trx_addons_addons_list', 100 );
		}
	}
}

// Addons
if ( ! function_exists( 'orto_trx_addons_addons_list' ) ) {
	//Handler of the add_filter( 'trx_addons_addons_list', 'orto_trx_addons_addons_list', 100 );
	function orto_trx_addons_addons_list( $list = array() ) {
		// To do: Enable/Disable theme-specific addons via add/remove it in the list
		if ( is_array( $list ) ) {
			// List of the theme/skin required addons:
			$required_addons = array(
				'elementor-templates' => array( 'title' => esc_html__( 'Elementor Templates', 'orto' ) ),
				'elementor-widgets'   => array( 'title' => esc_html__( 'Elementor Widgets', 'orto' ) ),
				'expand-collapse'     => array( 'title' => esc_html__( 'Expand / Collapse', 'orto' ) ),
			);
			foreach( $required_addons as $k => $v ) {
				if ( ! isset( $list[ $k ] ) || ! is_array( $list[ $k ] ) ) {
					$list[ $k ] = $v;
				}
				$list[ $k ]['required'] = true;
			}
		}
		return $list;
	}
}