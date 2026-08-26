<?php
// Add theme-specific CSS-animations
if ( ! function_exists( 'orto_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'orto_elm_add_theme_animations' );
	function orto_elm_add_theme_animations( $animations ) {
		/* To add a theme-specific animations to the list:
			1) Merge to the array 'animations': array(
													esc_html__( 'Theme Specific', 'orto' ) => array(
														'ta_custom_1' => esc_html__( 'Custom 1', 'orto' )
													)
												)
			2) Add a CSS rules for the class '.ta_custom_1' to create a custom entrance animation
		*/
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'orto' ) => array(
																			'ta_fadeinup' 		=> esc_html__( 'Fade In Up (Short)', 'orto' ),
																			'ta_fadeinright'	=> esc_html__( 'Fade In Right (Short)', 'orto' ),
																			'ta_fadeinleft'		=> esc_html__( 'Fade In Left (Short)', 'orto' ),
																			'ta_fadeindown'		=> esc_html__( 'Fade In Down (Short)', 'orto' ),
																			'ta_fadein' 		=> esc_html__( 'Fade In (Short)', 'orto' ),
																			'ta_popup' 			=> esc_html__( 'Pop Up', 'orto' ),
																			'ta_infiniterotate' => esc_html__( 'Infinite Rotate', 'orto' ),
																			)
							)
						);
		return $animations;
	}
}
