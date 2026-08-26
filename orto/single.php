<?php
/**
 * The template to display single post
 *
 * @package ORTO
 * @since ORTO 1.0
 */

// Full post loading
$full_post_loading          = orto_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = orto_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = orto_get_theme_option( 'posts_navigation_scroll_which_block', 'article' );

// Position of the related posts
$orto_related_position   = orto_get_theme_option( 'related_position', 'below_content' );

// Type of the prev/next post navigation
$orto_posts_navigation   = orto_get_theme_option( 'posts_navigation' );
$orto_prev_post          = false;
$orto_prev_post_same_cat = (int)orto_get_theme_option( 'posts_navigation_scroll_same_cat', 1 );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( orto_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	orto_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'orto_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $orto_posts_navigation ) {
		$orto_prev_post = get_previous_post( $orto_prev_post_same_cat );  // Get post from same category
		if ( ! $orto_prev_post && $orto_prev_post_same_cat ) {
			$orto_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $orto_prev_post ) {
			$orto_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $orto_prev_post ) ) {
		orto_sc_layouts_showed( 'featured', false );
		orto_sc_layouts_showed( 'title', false );
		orto_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $orto_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/content', 'single-' . orto_get_theme_option( 'single_style' ) ), 'single-' . orto_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $orto_related_position, 'inside' ) === 0 ) {
		$orto_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'orto_action_related_posts' );
		$orto_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $orto_related_content ) ) {
			$orto_related_position_inside = max( 0, min( 9, orto_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $orto_related_position_inside ) {
				$orto_related_position_inside = mt_rand( 1, 9 );
			}

			$orto_p_number         = 0;
			$orto_related_inserted = false;
			$orto_in_block         = false;
			$orto_content_start    = strpos( $orto_content, '<div class="post_content' );
			$orto_content_end      = strrpos( $orto_content, '</div>' );

			for ( $i = max( 0, $orto_content_start ); $i < min( strlen( $orto_content ) - 3, $orto_content_end ); $i++ ) {
				if ( $orto_content[ $i ] != '<' ) {
					continue;
				}
				if ( $orto_in_block ) {
					if ( strtolower( substr( $orto_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$orto_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $orto_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $orto_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$orto_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $orto_content[ $i + 1 ] && in_array( $orto_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$orto_p_number++;
					if ( $orto_related_position_inside == $orto_p_number ) {
						$orto_related_inserted = true;
						$orto_content = ( $i > 0 ? substr( $orto_content, 0, $i ) : '' )
											. $orto_related_content
											. substr( $orto_content, $i );
					}
				}
			}
			if ( ! $orto_related_inserted ) {
				if ( $orto_content_end > 0 ) {
					$orto_content = substr( $orto_content, 0, $orto_content_end ) . $orto_related_content . substr( $orto_content, $orto_content_end );
				} else {
					$orto_content .= $orto_related_content;
				}
			}
		}

		orto_show_layout( $orto_content );
	}

	// Comments
	do_action( 'orto_action_before_comments' );
	comments_template();
	do_action( 'orto_action_after_comments' );

	// Related posts
	if ( 'below_content' == $orto_related_position
		&& ( 'scroll' != $orto_posts_navigation || (int)orto_get_theme_option( 'posts_navigation_scroll_hide_related', 0 ) == 0 )
		&& ( ! $full_post_loading || (int)orto_get_theme_option( 'open_full_post_hide_related', 1 ) == 0 )
	) {
		do_action( 'orto_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $orto_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $orto_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $orto_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $orto_prev_post ) ); ?>"
			data-cur-post-link="<?php echo esc_attr( get_permalink() ); ?>"
			data-cur-post-title="<?php the_title_attribute(); ?>"
			<?php do_action( 'orto_action_nav_links_single_scroll_data', $orto_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
