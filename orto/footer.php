<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package ORTO
 * @since ORTO 1.0
 */

							do_action( 'orto_action_page_content_end_text' );
							
							// Widgets area below the content
							orto_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'orto_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'orto_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'orto_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'orto_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$orto_body_style = orto_get_theme_option( 'body_style' );
					$orto_widgets_name = orto_get_theme_option( 'widgets_below_page', 'hide' );
					$orto_show_widgets = ! orto_is_off( $orto_widgets_name ) && is_active_sidebar( $orto_widgets_name );
					$orto_show_related = orto_is_single() && orto_get_theme_option( 'related_position', 'below_content' ) == 'below_page';
					if ( $orto_show_widgets || $orto_show_related ) {
						if ( 'fullscreen' != $orto_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $orto_show_related ) {
							do_action( 'orto_action_related_posts' );
						}

						// Widgets area below page content
						if ( $orto_show_widgets ) {
							orto_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $orto_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'orto_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'orto_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! orto_is_singular( 'post' ) && ! orto_is_singular( 'attachment' ) ) || ! in_array ( orto_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<span id="footer_skip_link_anchor" class="orto_skip_link_anchor"></span>
				<?php

				do_action( 'orto_action_before_footer' );

				// Footer
				$orto_footer_type = orto_get_theme_option( 'footer_type' );
				if ( 'custom' == $orto_footer_type && ! orto_is_layouts_available() ) {
					$orto_footer_type = 'default';
				}
				get_template_part( apply_filters( 'orto_filter_get_template_part', "templates/footer-" . sanitize_file_name( $orto_footer_type ) ) );

				do_action( 'orto_action_after_footer' );

			}
			?>

			<?php do_action( 'orto_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'orto_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'orto_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>