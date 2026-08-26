<?php
/**
 * The Header: Logo and main menu
 *
 * @package ORTO
 * @since ORTO 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( orto_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}

	$orto_full_post_loading = ( orto_is_singular( 'post' ) || orto_is_singular( 'attachment' ) ) && orto_get_value_gp( 'action' ) == 'full_post_loading';
	$orto_prev_post_loading = ( orto_is_singular( 'post' ) || orto_is_singular( 'attachment' ) ) && orto_get_value_gp( 'action' ) == 'prev_post_loading';

	// Don't display the short links while actions 'full_post_loading' and 'prev_post_loading'
	if ( ! $orto_full_post_loading && ! $orto_prev_post_loading ) {
		// Short links to fast access to the content, sidebar and footer from the keyboard
		?><a class="skip-link orto_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'orto_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to content", 'orto' ); ?></a><?php
		if ( orto_sidebar_present() ) {
			?><a class="skip-link orto_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'orto_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'orto' ); ?></a><?php
		}
		?><a class="skip-link orto_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'orto_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to footer", 'orto' ); ?></a><?php
	}

	do_action( 'orto_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'orto_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('orto_action_body_wrap_attributes'); ?>>

		<?php do_action( 'orto_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'orto_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('orto_action_page_wrap_attributes'); ?>>

			<?php do_action( 'orto_action_page_wrap_start' ); ?>

			<?php

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $orto_full_post_loading && ! $orto_prev_post_loading ) {

				do_action( 'orto_action_before_header' );

				// Header
				$orto_header_type = orto_get_theme_option( 'header_type' );
				if ( 'custom' == $orto_header_type && ! orto_is_layouts_available() ) {
					$orto_header_type = 'default';
				}
				get_template_part( apply_filters( 'orto_filter_get_template_part', "templates/header-" . sanitize_file_name( $orto_header_type ) ) );

				// Side menu
				if ( in_array( orto_get_theme_option( 'menu_side', 'none' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				if ( apply_filters( 'orto_filter_use_navi_mobile', orto_sc_layouts_showed( 'menu_button' ) || $orto_header_type == 'default' ) ) {
					get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-navi-mobile' ) );
				}

				do_action( 'orto_action_after_header' );

			}
			?>

			<?php do_action( 'orto_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( orto_is_off( orto_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $orto_header_type ) ) {
						$orto_header_type = orto_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $orto_header_type && orto_is_layouts_available() ) {
						$orto_header_id = orto_get_custom_header_id();
						if ( $orto_header_id > 0 ) {
							$orto_header_meta = orto_get_custom_layout_meta( $orto_header_id );
							if ( ! empty( $orto_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$orto_footer_type = orto_get_theme_option( 'footer_type' );
					if ( 'custom' == $orto_footer_type && orto_is_layouts_available() ) {
						$orto_footer_id = orto_get_custom_footer_id();
						if ( $orto_footer_id ) {
							$orto_footer_meta = orto_get_custom_layout_meta( $orto_footer_id );
							if ( ! empty( $orto_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'orto_action_page_content_wrap_class', $orto_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'orto_filter_is_prev_post_loading', $orto_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( orto_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'orto_action_page_content_wrap_data', $orto_prev_post_loading );
			?>>
				<?php
				do_action( 'orto_action_page_content_wrap', $orto_full_post_loading || $orto_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'orto_filter_single_post_header', orto_is_singular( 'post' ) || orto_is_singular( 'attachment' ) ) ) {
					if ( $orto_prev_post_loading ) {
						if ( orto_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) != 'article' ) {
							do_action( 'orto_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$orto_path = apply_filters( 'orto_filter_get_template_part', 'templates/single-styles/' . orto_get_theme_option( 'single_style' ) );
					if ( orto_get_file_dir( $orto_path . '.php' ) != '' ) {
						get_template_part( $orto_path );
					}
				}

				// Widgets area above page
				$orto_body_style   = orto_get_theme_option( 'body_style' );
				$orto_widgets_name = orto_get_theme_option( 'widgets_above_page', 'hide' );
				$orto_show_widgets = ! orto_is_off( $orto_widgets_name ) && is_active_sidebar( $orto_widgets_name );
				if ( $orto_show_widgets ) {
					if ( 'fullscreen' != $orto_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					orto_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $orto_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'orto_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $orto_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'orto_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'orto_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<span id="content_skip_link_anchor" class="orto_skip_link_anchor"></span>
						<?php
						// Single posts banner between prev/next posts
						if ( ( orto_is_singular( 'post' ) || orto_is_singular( 'attachment' ) )
							&& $orto_prev_post_loading 
							&& orto_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) == 'article'
						) {
							do_action( 'orto_action_between_posts' );
						}

						// Widgets area above content
						orto_create_widgets_area( 'widgets_above_content' );

						do_action( 'orto_action_page_content_start_text' );
