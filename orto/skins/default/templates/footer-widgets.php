<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package ORTO
 * @since ORTO 1.0.10
 */

// Footer sidebar
$orto_footer_name    = orto_get_theme_option( 'footer_widgets' );
$orto_footer_present = ! orto_is_off( $orto_footer_name ) && is_active_sidebar( $orto_footer_name );
if ( $orto_footer_present ) {
	orto_storage_set( 'current_sidebar', 'footer' );
	ob_start();
	if ( is_active_sidebar( $orto_footer_name ) ) {
		dynamic_sidebar( $orto_footer_name );
	}
	$orto_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $orto_out ) ) {
		$orto_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $orto_out );
		$orto_need_columns = true;   //or check: strpos($orto_out, 'columns_wrap')===false;
		if ( $orto_need_columns ) {
			$orto_columns = max( 0, (int) orto_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $orto_columns ) {
				$orto_columns = min( 4, max( 1, orto_tags_count( $orto_out, 'aside' ) ) );
			}
			if ( $orto_columns > 1 ) {
				$orto_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $orto_columns ) . ' widget', $orto_out );
			} else {
				$orto_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area sc_layouts_row">
			<?php do_action( 'orto_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<div class="content_wrap">
					<?php
					if ( $orto_need_columns ) {
						?>
						<div class="columns_wrap">
						<?php
					}
					do_action( 'orto_action_before_sidebar', 'footer' );
					orto_show_layout( $orto_out );
					do_action( 'orto_action_after_sidebar', 'footer' );
					if ( $orto_need_columns ) {
						?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php do_action( 'orto_action_after_sidebar_wrap', 'footer' ); ?>
		</div>
		<?php
	}
}
