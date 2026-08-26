<?php
/**
 * Skin Demo importer
 *
 * @package ORTO
 * @since ORTO 1.76.0
 */


// Theme storage
//-------------------------------------------------------------------------

orto_storage_set( 'theme_demo_url', '//orto.ancorathemes.com' );


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'orto_skin_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'orto_skin_importer_set_options', 9 );
	function orto_skin_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			$demo_type = function_exists( 'orto_skins_get_current_skin_name' ) ? orto_skins_get_current_skin_name() : 'default';
			if ( 'default' != $demo_type ) {
				$options['demo_type'] = $demo_type;
				$options['files'][ $demo_type ] = $options['files']['default'];	// Copy all settings from 'default' to the new demo type
				unset($options['files']['default']);
			}
			// Override some settings in the new demo type
			$theme_slug = get_template();
			$theme_name = wp_get_theme( $theme_slug )->get( 'Name' );
			$options['files'][ $demo_type ]['title'] = sprintf( esc_html__( '%s Demo', 'orto' ), $theme_name )
				. ( $demo_type != 'default'
					? '. ' . sprintf( esc_html__( 'Skin %s', 'orto' ), ucfirst( str_replace( array( '-', '_' ), ' ', $demo_type ) ) )
					: ''
					);
			$options['files'][ $demo_type ]['domain_dev']  = ''; // Developers domain, example: orto_add_protocol( '//orto.dev.themerex.net' ); 
			$options['files'][ $demo_type ]['domain_demo'] = orto_add_protocol( orto_storage_get( 'theme_demo_url' ) ); // Demo-site domain
		}
		return $options;
	}
}