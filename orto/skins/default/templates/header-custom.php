<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package ORTO
 * @since ORTO 1.0.06
 */

$orto_header_css   = '';
$orto_header_image = get_header_image();
if ( ! empty( $orto_header_image ) && orto_trx_addons_featured_image_override( orto_is_singular() || orto_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$orto_header_image = orto_get_current_mode_image( $orto_header_image );
}

$orto_header_id = orto_get_custom_header_id();
$orto_header_meta = orto_get_custom_layout_meta( $orto_header_id );
if ( ! empty( $orto_header_meta['margin'] ) ) {
	orto_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( orto_prepare_css_value( $orto_header_meta['margin'] ) ) ) );
	orto_storage_set( 'custom_header_margin', orto_prepare_css_value( $orto_header_meta['margin'] ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $orto_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $orto_header_id ) ) ); ?>
				<?php
				echo ! empty( $orto_header_image )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $orto_header_image ) {
					echo ' ' . esc_attr( orto_add_inline_css_class( 'background-image: url(' . esc_url( $orto_header_image ) . ');' ) );
				}
				if ( orto_is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				?>
">
	<?php

	// Custom header's layout
	do_action( 'orto_action_show_layout', $orto_header_id );

	?>
</header>
