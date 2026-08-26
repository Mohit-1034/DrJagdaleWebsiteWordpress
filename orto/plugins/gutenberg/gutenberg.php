<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'orto_gutenberg_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'orto_gutenberg_theme_setup9', 9 );
	function orto_gutenberg_theme_setup9() {

		// Add wide and full blocks support
		add_theme_support( 'align-wide' );

		// Add a block library styles support for the FSE themes
		if ( orto_gutenberg_is_fse_theme() ) {
			add_theme_support( "wp-block-styles" );
		}

		// The theme supports responsive embedded content
		add_theme_support( "responsive-embeds" );

		// Add editor styles to backend
		add_theme_support( 'editor-styles' );
		if ( is_admin() && ( ! is_rtl() || ! is_customize_preview() ) ) {
			if ( orto_exists_gutenberg() && orto_gutenberg_is_preview() ) {
				if ( ! orto_get_theme_setting( 'gutenberg_add_context' ) ) {
					if ( ! orto_exists_trx_addons() ) {
						// Attention! This place need to use 'trx_addons_filter' instead 'orto_filter'
						add_editor_style( apply_filters( 'trx_addons_filter_add_editor_style', array(), 'gutenberg' ) );
					}
				}
			} else {
				// Styles for TinyMCE
				add_editor_style( apply_filters( 'orto_filter_add_editor_style', array(
					orto_get_file_url( 'css/font-icons/css/fontello.css' ),
					orto_get_file_url( 'css/__custom.css' ),
					orto_get_file_url( 'css/editor-style.css' )
					), 'editor' )
				);
			}
		}

		if ( orto_exists_gutenberg() ) {
			add_action( 'wp_enqueue_scripts', 'orto_gutenberg_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'orto_gutenberg_responsive_styles', 2000 );
			add_filter( 'orto_filter_merge_styles', 'orto_gutenberg_merge_styles' );
			add_filter( 'orto_filter_merge_styles_responsive', 'orto_gutenberg_merge_styles_responsive' );
		}
		add_action( 'enqueue_block_editor_assets', 'orto_gutenberg_editor_scripts' );
		add_filter( 'orto_filter_localize_script_admin',	'orto_gutenberg_localize_script');
		add_action( 'after_setup_theme', 'orto_gutenberg_add_editor_colors' );
		add_action( 'init', 'orto_gutenberg_add_block_styles' );
		add_action( 'init', 'orto_gutenberg_add_block_patterns' );
		if ( is_admin() ) {
			add_filter( 'orto_filter_tgmpa_required_plugins', 'orto_gutenberg_tgmpa_required_plugins' );
			add_filter( 'orto_filter_theme_plugins', 'orto_gutenberg_theme_plugins' );
		}
	}
}

// Add theme's icons styles to the Gutenberg editor
if ( ! function_exists( 'orto_gutenberg_add_editor_style_icons' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'orto_gutenberg_add_editor_style_icons', 10 );
	function orto_gutenberg_add_editor_style_icons( $styles ) {
		$orto_url = orto_get_file_url( 'css/font-icons/css/fontello.css' );
		if ( '' != $orto_url ) {
			$styles[] = $orto_url;
		}
		return $styles;
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'orto_gutenberg_add_editor_style' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'orto_gutenberg_add_editor_style', 1100 );
	function orto_gutenberg_add_editor_style( $styles ) {
		$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' );
		if ( '' != $orto_url ) {
			$styles[] = $orto_url;
		}
		return $styles;
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'orto_gutenberg_add_editor_style_responsive' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'orto_gutenberg_add_editor_style_responsive', 2000 );
	function orto_gutenberg_add_editor_style_responsive( $styles ) {
		$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( '' != $orto_url ) {
			$styles[] = $orto_url;
		}
		return $styles;
	}
}

// Add all skin-specific font-faces to the editor styles
if ( ! function_exists( 'orto_gutenberg_add_editor_style_font_urls' ) ) {
	add_filter( 'orto_filter_add_editor_style', 'orto_gutenberg_add_editor_style_font_urls', 9990 );
	add_filter( 'trx_addons_filter_add_editor_style', 'orto_gutenberg_add_editor_style_font_urls', 9990 );
	function orto_gutenberg_add_editor_style_font_urls( $styles ) {
		return array_merge( $styles, orto_theme_fonts_for_editor( true ) );
	}
}

// Remove main-theme and child-theme urls from the editor style paths
if ( ! function_exists( 'orto_gutenberg_add_editor_style_remove_theme_url' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'orto_gutenberg_add_editor_style_remove_theme_url', 9999 );
	function orto_gutenberg_add_editor_style_remove_theme_url( $styles ) {
		if ( is_array( $styles ) ) {
			$template_uri   = trailingslashit( get_template_directory_uri() );
			$stylesheet_uri = trailingslashit( get_stylesheet_directory_uri() );
			$theme_replace  = '';
			$from           = array(
				$template_uri,
				strpos( $template_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $template_uri ) : $template_uri,
				$stylesheet_uri,
				strpos( $stylesheet_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $stylesheet_uri ) : $stylesheet_uri,
			);
			$to             = array(
				$theme_replace,
				$theme_replace,
				$theme_replace,
				$theme_replace,
			);
			if ( apply_filters( 'trx_addons_filter_add_editor_style_remove_plugins_url', true ) ) {
				$plugins_uri    = trailingslashit( defined( 'WP_PLUGIN_URL' ) ? WP_PLUGIN_URL : plugins_url() );
				$plugin_replace = '../'            // up to the folder 'themes'
									. '../'        // up to the folder 'wp-content'
									. 'plugins/';  // open the folder 'plugins'
				$from           = array_merge( $from, array(
					$plugins_uri,
					strpos( $plugins_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $plugins_uri ) : $plugins_uri,
				) );
				$to             = array_merge( $to, array(
					$plugin_replace,
					$plugin_replace,
				) );
			}
			foreach( $styles as $k => $v ) {
				$styles[ $k ] = str_replace( $from, $to, $v );
			}
		}
		return $styles;
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'orto_gutenberg_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('orto_filter_tgmpa_required_plugins',	'orto_gutenberg_tgmpa_required_plugins');
	function orto_gutenberg_tgmpa_required_plugins( $list = array() ) {
		if ( orto_storage_isset( 'required_plugins', 'gutenberg' ) ) {
			if ( orto_storage_get_array( 'required_plugins', 'gutenberg', 'install' ) !== false && version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
				$list[] = array(
					'name'     => orto_storage_get_array( 'required_plugins', 'gutenberg', 'title' ),
					'slug'     => 'gutenberg',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'orto_gutenberg_theme_plugins' ) ) {
	//Handler of the add_filter( 'orto_filter_theme_plugins', 'orto_gutenberg_theme_plugins' );
	function orto_gutenberg_theme_plugins( $list = array() ) {
		$list = orto_add_group_and_logo_to_slave( $list, 'gutenberg', 'coblocks' );
		$list = orto_add_group_and_logo_to_slave( $list, 'gutenberg', 'kadence-blocks' );
		return $list;
	}
}


// Check if Gutenberg is installed and activated
if ( ! function_exists( 'orto_exists_gutenberg' ) ) {
	function orto_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}

// Return true if Gutenberg exists and current mode is preview
if ( ! function_exists( 'orto_gutenberg_is_preview' ) ) {
	function orto_gutenberg_is_preview() {
		return orto_exists_gutenberg() 
				&& (
					orto_gutenberg_is_block_render_action()
					||
					orto_is_post_edit()
					||
					orto_gutenberg_is_widgets_block_editor()
					||
					orto_gutenberg_is_site_editor()
					);
	}
}

// Return true if current mode is "Full Site Editor"
if ( ! function_exists( 'orto_gutenberg_is_site_editor' ) ) {
	function orto_gutenberg_is_site_editor() {
		return is_admin()
				&& orto_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.9', '>=' )
				&& orto_check_url( 'site-editor.php' )
				&& orto_gutenberg_is_fse_theme();
	}
}

// Return true if current mode is "Widgets Block Editor" (a new widgets panel with Gutenberg support)
if ( ! function_exists( 'orto_gutenberg_is_widgets_block_editor' ) ) {
	function orto_gutenberg_is_widgets_block_editor() {
		return is_admin()
				&& orto_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.8', '>=' )
				&& orto_check_url( 'widgets.php' )
				&& function_exists( 'wp_use_widgets_block_editor' )
				&& wp_use_widgets_block_editor();
	}
}

// Return true if current mode is "Block render"
if ( ! function_exists( 'orto_gutenberg_is_block_render_action' ) ) {
	function orto_gutenberg_is_block_render_action() {
		return orto_exists_gutenberg() 
				&& orto_check_url( 'block-renderer' ) && ! empty( $_GET['context'] ) && 'edit' == $_GET['context'];
	}
}

// Return true if content built with "Gutenberg"
// $post can be int (post ID) | string (post content) | object (post object)
if ( ! function_exists( 'orto_gutenberg_is_content_built' ) ) {
	function orto_gutenberg_is_content_built( $post = null ) {
		return orto_exists_gutenberg() 
				&& has_blocks( $post );	// This condition is equval to: strpos( $post, '<!-- wp:' ) !== false;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'orto_gutenberg_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'orto_gutenberg_frontend_scripts', 1100 );
	function orto_gutenberg_frontend_scripts() {
		if ( orto_is_on( orto_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg-general.css' );
			if ( '' != $orto_url ) {
				wp_enqueue_style( 'orto-gutenberg-general', $orto_url, array(), null );
			}
			// Skin-specific styles
			$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg.css' );
			if ( '' != $orto_url ) {
				wp_enqueue_style( 'orto-gutenberg', $orto_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'orto_gutenberg_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'orto_gutenberg_responsive_styles', 2000 );
	function orto_gutenberg_responsive_styles() {
		if ( orto_is_on( orto_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg-general-responsive.css' );
			if ( '' != $orto_url ) {
				wp_enqueue_style( 'orto-gutenberg-general-responsive', $orto_url, array(), null, orto_media_for_load_css_responsive( 'gutenberg-general' ) );
			}
			// Skin-specific styles
			$orto_url = orto_get_file_url( 'plugins/gutenberg/gutenberg-responsive.css' );
			if ( '' != $orto_url ) {
				wp_enqueue_style( 'orto-gutenberg-responsive', $orto_url, array(), null, orto_media_for_load_css_responsive( 'gutenberg' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'orto_gutenberg_merge_styles' ) ) {
	//Handler of the add_filter('orto_filter_merge_styles', 'orto_gutenberg_merge_styles');
	function orto_gutenberg_merge_styles( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg.css' ] = true;
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'orto_gutenberg_merge_styles_responsive' ) ) {
	//Handler of the add_filter('orto_filter_merge_styles_responsive', 'orto_gutenberg_merge_styles_responsive');
	function orto_gutenberg_merge_styles_responsive( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general-responsive.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg-responsive.css' ] = true;
		return $list;
	}
}


// Load required styles and scripts for Gutenberg Editor mode
if ( ! function_exists( 'orto_gutenberg_editor_scripts' ) ) {
	//Handler of the add_action( 'enqueue_block_editor_assets', 'orto_gutenberg_editor_scripts');
	function orto_gutenberg_editor_scripts() {
		orto_admin_scripts(true);
		orto_admin_localize_scripts();
		// Editor styles
		wp_enqueue_style( 'orto-gutenberg-editor', orto_get_file_url( 'plugins/gutenberg/gutenberg-editor.css' ), array(), null );
		// Block styles
		if ( orto_get_theme_setting( 'gutenberg_add_context' ) ) {
			wp_enqueue_style( 'orto-gutenberg-preview', orto_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' ), array(), null );
			wp_enqueue_style( 'orto-gutenberg-preview-responsive', orto_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' ), array(), null );
		}
		// Load merged scripts ?????
		wp_enqueue_script( 'orto-main', orto_get_file_url( 'js/__scripts-full.js' ), apply_filters( 'orto_filter_script_deps', array( 'jquery' ) ), null, true );
		// Editor scripts
		wp_enqueue_script( 'orto-gutenberg-preview', orto_get_file_url( 'plugins/gutenberg/gutenberg-preview.js' ), array( 'jquery' ), null, true );
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'orto_gutenberg_localize_script' ) ) {
	//Handler of the add_filter( 'orto_filter_localize_script_admin',	'orto_gutenberg_localize_script');
	function orto_gutenberg_localize_script( $arr ) {
		// Not overridden options
		$arr['color_scheme']     = orto_get_theme_option( 'color_scheme' );
		// Overridden options
		$arr['override_classes'] = apply_filters( 'orto_filter_override_options_list', array(
													'body_style'       => 'body_style_%s',
													'sidebar_position' => 'sidebar_position_%s',
													'expand_content'   => '%s_content'
									) );
		$post_id   = orto_get_value_gpc( 'post' );
		$post_type = '';
		$post_slug = '';
		if ( orto_gutenberg_is_preview() )  {
			if ( ! empty( $post_id ) ) {		// Edit post
				$post_type = orto_get_edited_post_type();
				$meta = get_post_meta( $post_id, 'orto_options', true );
			} else {							// New post
				$post_type = orto_get_value_gpc( 'post_type' );
				if ( empty( $post_type ) ) {
					$post_type = 'post';
				}
			}
			if ( ! empty( $post_type ) ) {
				$post_slug = str_replace( 'cpt_', '', $post_type );
			}
		}
		foreach( $arr['override_classes'] as $opt => $class_mask ) {
			$arr[ $opt ] = 'inherit';
			if ( ! empty( $post_type ) ) {
				// Get an overridden value from the post meta
				if ( 'page' != $post_type && ! empty( $meta["{$opt}_single"] ) ) {
					$arr[ $opt ] = $meta["{$opt}_single"];
				} elseif ( 'page' == $post_type && ! empty( $meta[ $opt ] ) ) {
					$arr[ $opt ] = $meta[ $opt ];
				}
				// Get an overridden value from the theme options
				if ( 'inherit' == $arr[ $opt ] ) {
					if ( 'post' == $post_type ) {
						if ( orto_check_theme_option( "{$opt}_single" ) ) {
							$arr[ $opt ] = orto_get_theme_option( "{$opt}_single" );
						}
						if ( 'inherit' == $arr[ $opt ] && orto_check_theme_option( "{$opt}_blog" ) ) {
							$arr[ $opt ] = orto_get_theme_option( "{$opt}_blog" );
						}
					} else if ( 'page' != $post_type && orto_check_theme_option( "{$opt}_single_" . sanitize_title( $post_slug ) ) ) {
						$arr[ $opt ] = orto_get_theme_option( "{$opt}_single_" . sanitize_title( $post_slug ) );
						if ( 'inherit' == $arr[ $opt ] && orto_check_theme_option( "{$opt}_" . sanitize_title( $post_slug ) ) ) {
							$arr[ $opt ] = orto_get_theme_option( "{$opt}_" . sanitize_title( $post_slug ) );
						}
					}
				}
			}
			if ( 'inherit' == $arr[ $opt ] ) {
				$arr[ $opt ] = orto_get_theme_option( $opt );
			}
		}
		return $arr;
	}
}

// Save CSS with custom colors and fonts to the gutenberg-preview.css
if ( ! function_exists( 'orto_gutenberg_save_css' ) ) {
	add_action( 'orto_action_save_options', 'orto_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options', 'orto_gutenberg_save_css', 30 );
	function orto_gutenberg_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'orto' )
				. "\n----------------------------------------------------------------------- */\n";

		$add_context = array(
							'context'      => '.edit-post-visual-editor ',
							'context_self' => array( 'html', 'body', '.edit-post-visual-editor' )
							);

		// Get main styles
		//----------------------------------------------
		$css = '';
		// Add styles from the theme style.css file is not recommended, because this file contains reset styles and it's can broke the editor styles
		if ( apply_filters( 'orto_filter_add_style_css_to_gutenberg_preview', false ) ) {
			$css = orto_fgc( orto_get_file_dir( 'style.css' ) );
		}
		// Allow to add a skin-specific styles
		$css = apply_filters( 'orto_filter_gutenberg_get_styles', $css );

		// Append single post styles
		if ( apply_filters( 'orto_filters_separate_single_styles', false ) ) {
			$css .= orto_fgc( orto_get_file_dir( 'css/__single.css' ) );
		}
		// Append supported plugins styles
		$css .= orto_fgc( orto_get_file_dir( 'css/__plugins-full.css' ) );
		// Append theme-vars styles
		$css .= orto_customizer_get_css();
		// Add context class to each selector
		if ( orto_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css = trx_addons_css_add_context( $css, $add_context );
		} else {
			$css = apply_filters( 'orto_filter_prepare_css', $css );
		}

		// Get responsive styles
		//-----------------------------------------------
		$css_responsive = apply_filters( 'orto_filter_gutenberg_get_styles_responsive',
								orto_fgc( orto_get_file_dir( 'css/__responsive-full.css' ) )
								. ( apply_filters( 'orto_filters_separate_single_styles', false )
									? orto_fgc( orto_get_file_dir( 'css/__single-responsive.css' ) )
									: ''
									)
								);
		// Add context class to each selector
		if ( orto_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css_responsive = trx_addons_css_add_context( $css_responsive, $add_context );
		} else {
			$css_responsive = apply_filters( 'orto_filter_prepare_css', $css_responsive );
		}

		// Save styles to separate files
		//-----------------------------------------------

		// Save responsive styles
		$preview = orto_get_file_dir( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( $preview ) {
			orto_fpc( $preview, $msg . $css_responsive );
			$css_responsive = '';
		}
		// Save main styles (and append responsive if its not saved to the separate file)
		orto_fpc( orto_get_file_dir( 'plugins/gutenberg/gutenberg-preview.css' ), $msg . $css . $css_responsive );
	}
}


// Add theme-specific colors to the Gutenberg color picker
if ( ! function_exists( 'orto_gutenberg_add_editor_colors' ) ) {
	//Handler of the add_action( 'after_setup_theme', 'orto_gutenberg_add_editor_colors' );
	function orto_gutenberg_add_editor_colors() {
		$scheme = orto_get_scheme_colors();
		$groups = orto_storage_get( 'scheme_color_groups' );
		$names  = orto_storage_get( 'scheme_color_names' );
		$colors = array();
		foreach( $groups as $g => $group ) {
			foreach( $names as $n => $name ) {
				$c = 'main' == $g ? ( 'text' == $n ? 'text_color' : $n ) : $g . '_' . str_replace( 'text_', '', $n );
				if ( isset( $scheme[ $c ] ) ) {
					$colors[] = array(
						'slug'  => preg_replace( '/([a-z])([0-9])+/', '$1-$2', str_replace( '_', '-', $c ) ),
						'name'  => ( 'main' == $g ? '' : $group['title'] . ' ' ) . $name['title'],
						'color' => $scheme[ $c ]
					);
				}
			}
			// Add only one group of colors
			// Delete next condition (or add false && to them) to add all groups
			if ( 'main' == $g ) {
				break;
			}
		}
		add_theme_support( 'editor-color-palette', $colors );
	}
}


// Add theme-specific block styles for Gutenberg editor
if ( ! function_exists( 'orto_gutenberg_add_block_styles' ) ) {
	//Handler of the add_action( 'init', 'orto_gutenberg_add_block_styles' );
	function orto_gutenberg_add_block_styles() {
		if ( orto_get_theme_setting( 'add_gutenberg_block_styles' ) && function_exists( 'register_block_style' ) ) {
			$dir = orto_get_file_dir( 'templates/block-styles' );
			if ( ! empty( $dir ) ) {
				$scheme = orto_get_scheme_colors();
				$files = scandir( $dir );
				foreach( $files as $file ) {
					if ( in_array( $file, array( '.', '..' ) ) ) {
						continue;
					}
					$file = orto_prepare_path( $dir . '/' . $file );
					if ( is_file( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) == 'json' ) {
						$json = orto_fgc( $file );
						if ( empty( $json ) ) {
							continue;
						}
						$data = json_decode( $json, true );
						if ( empty( $data ) ) {
							continue;
						}
						if ( ! empty( $data['block'] ) ) {
							$data = array( $data );
						}
						if ( is_array( $data ) ) {
							foreach( $data as $block ) {
								if ( is_array( $block ) && ! empty( $block['block'] ) && ! empty( $block['styles'] ) && is_array( $block['styles'] ) ) {
									foreach( $block['styles'] as $style ) {
										// Replace color names to the color values
										if ( ! empty( $style['inline_style'] ) ) {
											$style['inline_style'] = preg_replace_callback(
												'/%([a-z_]+)%/i',
												function( $match ) use ( $scheme ) {
													$color_name = orto_get_scheme_color_name( $match[1] );
													return ( ! empty( $scheme[ $color_name ] ) )
														? $scheme[ $color_name ]
														: $match[0];
												},
												$style['inline_style']
											);
										}
										// Register block style
										register_block_style( $block['block'], $style );
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


// Add theme-specific block patterns for Gutenberg editor
if ( ! function_exists( 'orto_gutenberg_add_block_patterns' ) ) {
	//Handler of the add_action( 'init', 'orto_gutenberg_add_block_patterns' );
	function orto_gutenberg_add_block_patterns() {
		if ( orto_get_theme_setting( 'add_gutenberg_block_patterns' ) && function_exists( 'register_block_pattern' ) ) {
			$dir = orto_get_file_dir( 'templates/block-patterns' );
			if ( ! empty( $dir ) ) {
				$scheme = orto_get_scheme_colors();
				$files = scandir( $dir );
				foreach( $files as $file ) {
					if ( in_array( $file, array( '.', '..' ) ) ) {
						continue;
					}
					$file = orto_prepare_path( $dir . '/' . $file );
					if ( is_file( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) == 'json' ) {
						$json = orto_fgc( $file );
						if ( empty( $json ) ) {
							continue;
						}
						$data = json_decode( $json, true );
						if ( empty( $data ) ) {
							continue;
						}
						if ( ! empty( $data['name'] ) ) {
							$data = array( $data );
						}
						if ( is_array( $data ) ) {
							foreach( $data as $pattern ) {
								if ( is_array( $pattern ) && ! empty( $pattern['name'] ) && ! empty( $pattern['pattern'] ) && is_array( $pattern['pattern'] ) ) {
									foreach( $pattern['pattern'] as $pattern_data ) {
										// Register pattern
										register_block_pattern( $pattern['name'], $pattern_data );
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( orto_exists_gutenberg() ) {
	$orto_fdir = orto_get_file_dir( 'plugins/gutenberg/gutenberg-style.php' );
	if ( ! empty( $orto_fdir ) ) {
		require_once $orto_fdir;
	}
	$orto_fdir = orto_get_file_dir( 'plugins/gutenberg/gutenberg-fse.php' );
	if ( ! empty( $orto_fdir ) ) {
		require_once $orto_fdir;
	}
}
