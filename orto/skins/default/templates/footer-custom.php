<?php
/**
 * The template to display default site footer
 *
 * @package ORTO
 * @since ORTO 1.0.10
 */

$orto_footer_id = orto_get_custom_footer_id();
$orto_footer_meta = orto_get_custom_layout_meta( $orto_footer_id );
if ( ! empty( $orto_footer_meta['margin'] ) ) {
	orto_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( orto_prepare_css_value( $orto_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $orto_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $orto_footer_id ) ) ); ?>">
	<?php
	// Custom footer's layout
	do_action( 'orto_action_show_layout', $orto_footer_id );
	?>
</footer>