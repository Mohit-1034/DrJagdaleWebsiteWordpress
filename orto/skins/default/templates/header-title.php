<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package ORTO
 * @since ORTO 1.0
 */

// Page (category, tag, archive, author) title

if ( orto_need_page_title() ) {
	orto_sc_layouts_showed( 'title', true );
	?>
	<div class="top_panel_title sc_layouts_row">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Blog/Page title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$orto_blog_title           = orto_get_blog_title();
							$orto_blog_title_text      = '';
							$orto_blog_title_class     = '';
							$orto_blog_title_link      = '';
							$orto_blog_title_link_text = '';
							if ( is_array( $orto_blog_title ) ) {
								$orto_blog_title_text      = $orto_blog_title['text'];
								$orto_blog_title_class     = ! empty( $orto_blog_title['class'] ) ? ' ' . $orto_blog_title['class'] : '';
								$orto_blog_title_link      = ! empty( $orto_blog_title['link'] ) ? $orto_blog_title['link'] : '';
								$orto_blog_title_link_text = ! empty( $orto_blog_title['link_text'] ) ? $orto_blog_title['link_text'] : '';
							} else {
								$orto_blog_title_text = $orto_blog_title;
							}
							?>
							<h1 class="sc_layouts_title_caption<?php echo esc_attr( $orto_blog_title_class ); ?>"<?php
								if ( orto_is_on( orto_get_theme_option( 'seo_snippets' ) ) ) {
									?> itemprop="headline"<?php
								}
							?>>
								<?php
								$orto_top_icon = orto_get_term_image_small();
								if ( ! empty( $orto_top_icon ) ) {
									$orto_attr = orto_getimagesize( $orto_top_icon );
									?>
									<img src="<?php echo esc_url( $orto_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'orto' ); ?>"
										<?php
										if ( ! empty( $orto_attr[3] ) ) {
											orto_show_layout( $orto_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $orto_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $orto_blog_title_link ) && ! empty( $orto_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $orto_blog_title_link ); ?>" class="theme_button sc_layouts_title_link"><?php echo esc_html( $orto_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'orto_action_breadcrumbs' );
						$orto_breadcrumbs = ob_get_contents();
						ob_end_clean();
						orto_show_layout( $orto_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
