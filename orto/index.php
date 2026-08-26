<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package ORTO
 * @since ORTO 1.0
 */

$orto_template = apply_filters( 'orto_filter_get_template_part', orto_blog_archive_get_template() );

if ( ! empty( $orto_template ) && 'index' != $orto_template ) {

	get_template_part( $orto_template );

} else {

	orto_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$orto_stickies   = is_home()
								|| ( in_array( orto_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) orto_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$orto_post_type  = orto_get_theme_option( 'post_type' );
		$orto_args       = array(
								'blog_style'     => orto_get_theme_option( 'blog_style' ),
								'post_type'      => $orto_post_type,
								'taxonomy'       => orto_get_post_type_taxonomy( $orto_post_type ),
								'parent_cat'     => orto_get_theme_option( 'parent_cat' ),
								'posts_per_page' => orto_get_theme_option( 'posts_per_page' ),
								'sticky'         => orto_get_theme_option( 'sticky_style', 'inherit' ) == 'columns'
															&& is_array( $orto_stickies )
															&& count( $orto_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		orto_blog_archive_start();

		do_action( 'orto_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'orto_action_before_page_author' );
			get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'orto_action_after_page_author' );
		}

		if ( orto_get_theme_option( 'show_filters', 0 ) ) {
			do_action( 'orto_action_before_page_filters' );
			orto_show_filters( $orto_args );
			do_action( 'orto_action_after_page_filters' );
		} else {
			do_action( 'orto_action_before_page_posts' );
			orto_show_posts( array_merge( $orto_args, array( 'cat' => $orto_args['parent_cat'] ) ) );
			do_action( 'orto_action_after_page_posts' );
		}

		do_action( 'orto_action_blog_archive_end' );

		orto_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
