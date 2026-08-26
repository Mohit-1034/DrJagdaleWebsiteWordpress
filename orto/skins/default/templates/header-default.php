<?php
/**
 * The template to display default site header
 *
 * @package ORTO
 * @since ORTO 1.0
 */

$orto_header_css   = '';
$orto_header_image = get_header_image();
$orto_header_video = orto_get_header_video();
if ( ! empty( $orto_header_image ) && orto_trx_addons_featured_image_override( orto_is_singular() || orto_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$orto_header_image = orto_get_current_mode_image( $orto_header_image );
}
?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $orto_header_image ) || ! empty( $orto_header_video ) ? ' with_bg_image' : ' without_bg_image';
	if ( '' != $orto_header_video ) {
		echo ' with_bg_video';
	}
	if ( '' != $orto_header_image ) {
		echo ' ' . esc_attr( orto_add_inline_css_class( 'background-image: url(' . esc_url( $orto_header_image ) . ');' ) );
	}
	if ( orto_is_singular() && has_post_thumbnail() ) {
		echo ' with_featured_image';
	}
	?>
">
	<?php

	// Background video
	if ( ! empty( $orto_header_video ) ) {
		get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-video' ) );
	}

	// Main menu
	get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-navi' ) );

	// Page title and breadcrumbs area
	if ( ! orto_is_single() ) {
		get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-title' ) );
	}
	?>
</header>
