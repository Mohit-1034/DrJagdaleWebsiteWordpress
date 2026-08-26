<?php
/**
 * Required plugins
 *
 * @package ORTO
 * @since ORTO 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
if ( ! function_exists( 'orto_skin_required_plugins' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_required_plugins', -1 );
	function orto_skin_required_plugins() {
		$orto_theme_required_plugins_groups = array(
			'core'          => esc_html__( 'Core', 'orto' ),
			'page_builders' => esc_html__( 'Page Builders', 'orto' ),
			'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'orto' ),
			'socials'       => esc_html__( 'Socials and Communities', 'orto' ),
			'events'        => esc_html__( 'Events and Appointments', 'orto' ),
			'content'       => esc_html__( 'Content', 'orto' ),
			'other'         => esc_html__( 'Other', 'orto' ),
		);
		$orto_theme_required_plugins        = array(
			// Core
			'trx_addons'                 => array(
				'title'       => esc_html__( 'ThemeREX Addons', 'orto' ),
				'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'orto' ),
				'required'    => true, // Check this plugin in the list on load Theme Dashboard
				'logo'        => 'trx_addons.png',
				'group'       => $orto_theme_required_plugins_groups['core'],
			),
			// Page Builders
			'elementor'                  => array(
				'title'       => esc_html__( 'Elementor', 'orto' ),
				'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'orto' ),
				'required'    => false, // Leave this plugin unchecked on load Theme Dashboard
				'logo'        => 'elementor.png',
				'group'       => $orto_theme_required_plugins_groups['page_builders'],
			),
			'gutenberg'                  => array(
				'title'       => esc_html__( 'Gutenberg', 'orto' ),
				'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'orto' ),
				'required'    => false,
				'install'     => false, // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
				'logo'        => 'gutenberg.png',
				'group'       => $orto_theme_required_plugins_groups['page_builders'],
			),
			// Content
			'sitepress-multilingual-cms' => array(
				'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'orto' ),
				'description' => esc_html__( "Allows you to make your website multilingual", 'orto' ),
				'required'    => false,
				'install'     => false, // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
				'logo'        => 'sitepress-multilingual-cms.png',
				'group'       => $orto_theme_required_plugins_groups['content'],
			),
			'metform'                    => array(
				'title'       => esc_html__( 'MetForm', 'orto' ),
				'description' => esc_html__( "Contact Form, Survey, Quiz, & Custom Form Builder for Elementor", 'orto' ),
				'required'    => false,
				'logo'        => 'metform.png',
				'group'       => $orto_theme_required_plugins_groups['content'],
			),
			'woocommerce'                => array(
				'title'       => esc_html__( 'WooCommerce', 'orto' ),
				'description' => esc_html__( "Connect the store to your website and start selling now", 'orto' ),
				'required'    => false,
				'install'     => false, // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
				'logo'        => 'woocommerce.png',
				'group'       => $orto_theme_required_plugins_groups['ecommerce'],
			),
			'trx-wcext'                  => array(
				'title'       => esc_html__( 'ThemeRex WooCommerce Extensions', 'orto' ),
				'description' => esc_html__( "Adds many widgets for Elementor to extend WooCommerce support in the theme", 'orto' ),
				'required'    => false,
				'install'     => false, // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
				'logo'        => 'trx-wcext.png',
				'group'       => $orto_theme_required_plugins_groups['ecommerce'],
			),
			// Other
			'trx_updater'                => array(
				'title'       => esc_html__( 'ThemeREX Updater', 'orto' ),
				'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'orto' ),
				'required'    => false,
				'logo'        => 'trx_updater.png',
				'group'       => $orto_theme_required_plugins_groups['other'],
			)
		);

		if ( ORTO_THEME_FREE ) {
			unset( $orto_theme_required_plugins['sitepress-multilingual-cms'] );
			unset( $orto_theme_required_plugins['trx_updater'] );
		}

		// Add plugins list to the global storage
		orto_storage_set( 'required_plugins', $orto_theme_required_plugins );
	}
}
