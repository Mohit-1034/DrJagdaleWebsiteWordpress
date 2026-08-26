<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package ORTO
 * @since ORTO 1.0
 */

$orto_template_args = get_query_var( 'orto_template_args' );

if ( is_array( $orto_template_args ) ) {
	$orto_columns       = empty( $orto_template_args['columns'] ) ? 1 : max( 1, $orto_template_args['columns'] );
	$orto_blog_style    = array( $orto_template_args['type'], $orto_columns );
	$orto_columns_class = orto_get_column_class( 1, $orto_columns, ! empty( $orto_template_args['columns_tablet']) ? $orto_template_args['columns_tablet'] : '', ! empty($orto_template_args['columns_mobile']) ? $orto_template_args['columns_mobile'] : '' );
} else {
	$orto_template_args = array();
	$orto_blog_style    = explode( '_', orto_get_theme_option( 'blog_style' ) );
	$orto_columns       = empty( $orto_blog_style[1] ) ? 1 : max( 1, $orto_blog_style[1] );
	$orto_columns_class = orto_get_column_class( 1, $orto_columns );
}
$orto_expanded   = ! orto_sidebar_present() && orto_get_theme_option( 'expand_content' ) == 'expand';

$orto_post_format = get_post_format();
$orto_post_format = empty( $orto_post_format ) ? 'standard' : str_replace( 'post-format-', '', $orto_post_format );

?><div class="<?php
	if ( ! empty( $orto_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( orto_is_blog_style_use_masonry( $orto_blog_style[0] )
			? 'masonry_item masonry_item-1_' . esc_attr( $orto_columns )
			: esc_attr( $orto_columns_class )
			);
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $orto_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $orto_columns )
				. ' post_layout_' . esc_attr( $orto_blog_style[0] )
				. ' post_layout_' . esc_attr( $orto_blog_style[0] ) . '_' . esc_attr( $orto_columns )
	);
	orto_add_blog_animation( $orto_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	$orto_hover      = ! empty( $orto_template_args['hover'] ) && ! orto_is_inherit( $orto_template_args['hover'] )
							? $orto_template_args['hover']
							: orto_get_theme_option( 'image_hover' );

	$orto_components = ! empty( $orto_template_args['meta_parts'] )
							? ( is_array( $orto_template_args['meta_parts'] )
								? $orto_template_args['meta_parts']
								: array_map( 'trim', explode( ',', $orto_template_args['meta_parts'] ) )
								)
							: orto_array_get_keys_by_value( orto_get_theme_option( 'meta_parts' ) );

	orto_show_post_featured( apply_filters( 'orto_filter_args_featured',
		array(
			'thumb_size' => ! empty( $orto_template_args['thumb_size'] )
								? $orto_template_args['thumb_size']
								: orto_get_thumb_size(
									strpos( orto_get_theme_option( 'body_style' ), 'full' ) !== false
										? ( $orto_columns > 2 ? 'big' : 'full' )
										: ( $orto_columns > 2
											? 'med'
											: ( $orto_expanded || $orto_columns == 1 ? 
												( $orto_expanded && $orto_columns == 1 ? 'huge' : 'big' ) 
												: 'med' 
												)
											)												
								),
			'hover'      => $orto_hover,
			'meta_parts' => $orto_components,
			'no_links'   => ! empty( $orto_template_args['no_links'] ),
		),
		'content-classic',
		$orto_template_args
	) );

	// Title and post meta
	$orto_show_title = get_the_title() != '';
	$orto_show_meta  = count( $orto_components ) > 0;

	if ( $orto_show_title ) {
		?><div class="post_header entry-header"><?php
			// Categories
			if ( apply_filters( 'orto_filter_show_blog_categories', $orto_show_meta && in_array( 'categories', $orto_components ), array( 'categories' ), 'classic' ) ) {
				do_action( 'orto_action_before_post_category' );
				?><div class="post_category"><?php
					orto_show_post_meta( apply_filters(
														'orto_filter_post_meta_args',
														array(
															'components' => 'categories',
															'seo'        => false,
															'echo'       => true,
															),
														'hover_' . $orto_hover, 1
														)
										);
				?></div><?php
				$orto_components = orto_array_delete_by_value( $orto_components, 'categories' );
				do_action( 'orto_action_after_post_category' );
			}
			// Post title
			if ( apply_filters( 'orto_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'orto_action_before_post_title' );
				if ( empty( $orto_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'orto_action_after_post_title' );
			}
		?></div><?php
	}
	
	// Post meta
	if ( apply_filters( 'orto_filter_show_blog_meta', $orto_show_meta, $orto_components, 'classic' ) ) {
		if ( count( $orto_components ) > 0 ) {
			do_action( 'orto_action_before_post_meta' );
			orto_show_post_meta(
				apply_filters(
					'orto_filter_post_meta_args', array(
						'components' => join( ',', $orto_components ),
						'seo'        => false,
						'echo'       => true,
						'author_avatar' => false,
					), $orto_blog_style[0], $orto_columns
				)
			);
			do_action( 'orto_action_after_post_meta' );
		}
	}

	// Post content
	ob_start();
	if ( apply_filters( 'orto_filter_show_blog_excerpt', ( ! isset( $orto_template_args['hide_excerpt'] ) || (int)$orto_template_args['hide_excerpt'] == 0 ) && (int)orto_get_theme_option( 'excerpt_length' ) > 0, 'classic' ) ) {
		orto_show_post_content( $orto_template_args, '<div class="post_content_inner">', '</div>' );
	}
	$orto_content = ob_get_contents();
	ob_end_clean();

	orto_show_layout( $orto_content, '<div class="post_content entry-content">', '</div>' );

		
	// More button
	if ( apply_filters( 'orto_filter_show_blog_readmore', ! $orto_show_title || ! empty( $orto_template_args['more_button'] ), 'classic' ) ) {
		if ( empty( $orto_template_args['no_links'] ) ) {
			do_action( 'orto_action_before_post_readmore' );
			orto_show_post_more_link( $orto_template_args, '<p>', '</p>' );
			do_action( 'orto_action_after_post_readmore' );
		}
	}

	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
