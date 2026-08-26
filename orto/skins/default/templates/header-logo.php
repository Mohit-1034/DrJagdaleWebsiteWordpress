<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package ORTO
 * @since ORTO 1.0
 */

$orto_args = get_query_var( 'orto_logo_args' );

// Site logo
$orto_logo_type   = isset( $orto_args['type'] ) ? $orto_args['type'] : '';
$orto_logo_image  = orto_get_logo_image( $orto_logo_type );
$orto_logo_text   = orto_is_on( orto_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$orto_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $orto_logo_image['logo'] ) || ! empty( $orto_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $orto_logo_image['logo'] ) ) {
			if ( empty( $orto_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric( $orto_logo_image['logo'] ) && (int) $orto_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$orto_attr = orto_getimagesize( $orto_logo_image['logo'] );
				echo '<img src="' . esc_url( $orto_logo_image['logo'] ) . '"'
						. ( ! empty( $orto_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $orto_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $orto_logo_text ) . '"'
						. ( ! empty( $orto_attr[3] ) ? ' ' . wp_kses_data( $orto_attr[3] ) : '' )
						. '>';
			}
		} else {
			orto_show_layout( orto_prepare_macros( $orto_logo_text ), '<span class="logo_text">', '</span>' );
			orto_show_layout( orto_prepare_macros( $orto_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
