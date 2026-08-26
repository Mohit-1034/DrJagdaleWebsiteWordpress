<?php
/**
 * The template to display the background video in the header
 *
 * @package ORTO
 * @since ORTO 1.0.14
 */
$orto_header_video = orto_get_header_video();
$orto_embed_video  = '';
if ( ! empty( $orto_header_video ) && ! orto_is_from_uploads( $orto_header_video ) ) {
	if ( orto_is_youtube_url( $orto_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $orto_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php orto_show_layout( orto_get_embed_video( $orto_header_video ) ); ?></div>
		<?php
	}
}