<?php
/**
 * The template to display the logo in the header
 *
 * Overrides orto/skins/default/templates/header-logo.php.
 *
 * Why override: the parent renders either the logo image or the site name,
 * never both. The clinic's mark is a circular roundel with the clinic's name
 * set in a ring around a tree - at the 50px a header allows, that ring of type
 * is a grey smudge and nothing in it can be read. So the mark is used as a mark
 * and the name is set beside it in live text, which stays sharp at any size, is
 * selectable and searchable, and gives the header something a visitor can
 * actually read on arrival.
 *
 * The name is set in the two parts the signage itself uses - the possessive on
 * a small line above, the clinic name as the wordmark under it - because the
 * whole string on one line runs to three lines in the width a header bar can
 * spare, and three lines makes the bar taller than the menu beside it.
 *
 * The class .sc_layouts_logo is kept because the theme's own mobile-menu and
 * sticky-header styling both hang off it.
 *
 * @package ORTO
 */

$orto_child_logo_args     = get_query_var( 'orto_logo_args' );
$orto_child_logo_type     = isset( $orto_child_logo_args['type'] ) ? $orto_child_logo_args['type'] : '';
$orto_child_logo_image    = orto_get_logo_image( $orto_child_logo_type );
$orto_child_logo_business = orto_child_get_business();
?>
<a class="sc_layouts_logo djo_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php if ( ! empty( $orto_child_logo_image['logo'] ) ) { ?>
		<img class="djo_logo_mark"
			src="<?php echo esc_url( $orto_child_logo_image['logo'] ); ?>"
			<?php if ( ! empty( $orto_child_logo_image['logo_retina'] ) ) { ?>
				srcset="<?php echo esc_url( $orto_child_logo_image['logo_retina'] ); ?> 2x"
			<?php } ?>
			alt=""
			width="512" height="512"
			decoding="async">
	<?php } ?>

	<?php
	/*
	 * aria-hidden on the mark above and a real name here, rather than the name
	 * as the image's alt text: the name is on the page either way, and putting
	 * it in text means it is not read out twice.
	 */
	?>
	<span class="djo_logo_text">
		<span class="djo_logo_prefix"><?php echo esc_html( $orto_child_logo_business['name_prefix'] ); ?></span>
		<span class="djo_logo_name"><?php echo esc_html( $orto_child_logo_business['name_short'] ); ?></span>
	</span>
</a>
