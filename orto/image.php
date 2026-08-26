<?php
/**
 * The template to display the attachment
 *
 * @package ORTO
 * @since ORTO 1.0
 */


get_header();

while ( have_posts() ) {
	the_post();

	// Display post's content
	get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/content', 'single-' . orto_get_theme_option( 'single_style' ) ), 'single-' . orto_get_theme_option( 'single_style' ) );

	// Parent post navigation.
	$orto_posts_navigation = orto_get_theme_option( 'posts_navigation' );
	if ( 'links' == $orto_posts_navigation ) {
		?>
		<div class="nav-links-single<?php
			if ( ! orto_is_off( orto_get_theme_option( 'posts_navigation_fixed', 0 ) ) ) {
				echo ' nav-links-fixed fixed';
			}
		?>">
			<?php
			the_post_navigation( apply_filters( 'orto_filter_post_navigation_args', array(
					'prev_text' => '<span class="nav-arrow"></span>'
						. '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Published in', 'orto' ) . '</span> '
						. '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'orto' ) . '</span> '
						. '<h5 class="post-title">%title</h5>'
						. '<span class="post_date">%date</span>',
			), 'image' ) );
			?>
		</div>
		<?php
	}

	// Comments
	do_action( 'orto_action_before_comments' );
	comments_template();
	do_action( 'orto_action_after_comments' );
}

get_footer();
