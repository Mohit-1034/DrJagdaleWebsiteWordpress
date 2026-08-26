<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package ORTO
 * @since ORTO 1.0
 */

if ( orto_sidebar_present() ) {
	
	$orto_sidebar_type = orto_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $orto_sidebar_type && ! orto_is_layouts_available() ) {
		$orto_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $orto_sidebar_type ) {
		// Default sidebar with widgets
		$orto_sidebar_name = orto_get_theme_option( 'sidebar_widgets' );
		orto_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $orto_sidebar_name ) ) {
			dynamic_sidebar( $orto_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$orto_sidebar_id = orto_get_custom_sidebar_id();
		do_action( 'orto_action_show_layout', $orto_sidebar_id );
	}
	$orto_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $orto_out ) ) {
		$orto_sidebar_position    = orto_get_theme_option( 'sidebar_position' );
		$orto_sidebar_position_ss = orto_get_theme_option( 'sidebar_position_ss', 'below' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $orto_sidebar_position );
			echo ' sidebar_' . esc_attr( $orto_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $orto_sidebar_type );

			$orto_sidebar_scheme = apply_filters( 'orto_filter_sidebar_scheme', orto_get_theme_option( 'sidebar_scheme', 'inherit' ) );
			if ( ! empty( $orto_sidebar_scheme ) && ! orto_is_inherit( $orto_sidebar_scheme ) && 'custom' != $orto_sidebar_type ) {
				echo ' scheme_' . esc_attr( $orto_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<span id="sidebar_skip_link_anchor" class="orto_skip_link_anchor"></span>
			<?php

			do_action( 'orto_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $orto_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$orto_title = apply_filters( 'orto_filter_sidebar_control_title', 'float' == $orto_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'orto' ) : '' );
				$orto_text  = apply_filters( 'orto_filter_sidebar_control_text', 'above' == $orto_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'orto' ) : '' );
				?>
				<a href="#" role="button" class="sidebar_control" title="<?php echo esc_attr( $orto_title ); ?>"><?php echo esc_html( $orto_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'orto_action_before_sidebar', 'sidebar' );
				orto_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $orto_out ) );
				do_action( 'orto_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'orto_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
