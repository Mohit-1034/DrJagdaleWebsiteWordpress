<?php
/**
 * Skins support
 *
 * @package ORTO
 * @since ORTO 1.0.46
 */

if ( ! function_exists( 'orto_skins_get_active_skin_name' ) ) {
	/**
	 * Return a name of the active skin
	 * 
	 * @return string  The name of the active skin
	 */
	function orto_skins_get_active_skin_name() {
		static $orto_active_skin_saved = false;
		$orto_active_skin = '';
		if ( ! is_admin() ) {
			$orto_active_skin = orto_get_value_gp( 'skin' );
			if ( ORTO_REMEMBER_SKIN ) {
				if ( empty( $orto_active_skin ) ) {
					$orto_active_skin = orto_get_cookie( 'orto_current_skin' );
				} else if ( ! $orto_active_skin_saved ) {
					orto_set_cookie( 'orto_current_skin', $orto_active_skin );
					$orto_active_skin_saved = true;
				}
			}
		}
		if ( empty( $orto_active_skin ) ) {
			$orto_active_skin = get_option( sprintf( 'theme_skin_%s', get_stylesheet() ), ORTO_DEFAULT_SKIN );
		}
		return $orto_active_skin;
	}
}

if ( ! function_exists( 'orto_skins_admin_notice_skin_missing' ) ) {
	/**
	 * Show the admin notice if the current skin is missing
	 * 
	 * @hooked 'admin_notices'
	 */
	function orto_skins_admin_notice_skin_missing() {
		get_template_part( apply_filters( 'orto_filter_get_template_part', 'skins/skins-notice-missing' ) );
	}
}

/**
 * Define the constant with the current skin name
 */
if ( ! defined( 'ORTO_SKIN_NAME' ) ) {
	$orto_current_skin = orto_skins_get_active_skin_name();
	// Set current 
	if ( ! file_exists( ORTO_THEME_DIR . "skins/{$orto_current_skin}/skin.php" )
		&&
		( ORTO_CHILD_DIR == ORTO_THEME_DIR || ! file_exists( ORTO_CHILD_DIR . "skins/{$orto_current_skin}/skin.php" ) )
	) {
		if ( is_admin() ) {
			add_action( 'admin_notices', 'orto_skins_admin_notice_skin_missing' );
		}
		$orto_current_skin = 'default';
		// Remove condition to set 'default' as an active skin if current skin is absent
		if ( false ) {
			update_option( sprintf( 'theme_skin_%s', get_stylesheet() ), $orto_current_skin );
		}
	}
	define( 'ORTO_SKIN_NAME', $orto_current_skin );
}

if ( ! function_exists( 'orto_skins_get_current_skin_name' ) ) {
	/**
	 * Return the name of the current skin (can be overriden on the page)
	 * 
	 * @return string  The name of the current skin
	 */
	function orto_skins_get_current_skin_name() {
		return orto_esc( ORTO_SKIN_NAME );
	}
}

if ( ! function_exists( 'orto_skins_get_current_skin_dir' ) ) {
	/**
	 * Return the replative path (from the theme root folder) of the current skin (can be overriden on the page)
	 * 
	 * @param string|bool $skin  The name of the skin. If false, the current skin is used.
	 * 
	 * @return string  The relative path of the current skin
	 */
	function orto_skins_get_current_skin_dir( $skin=false ) {
		return 'skins/' . trailingslashit( $skin ? $skin : orto_skins_get_current_skin_name() );
	}
}

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
if ( ! function_exists( 'orto_skins_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'orto_skins_theme_setup1', 1 );
	/**
	 * Theme setup function to initialize skins support. Add the list of available skins to the storage
	 * (must be filled by the filter 'orto_filter_skins_list').
	 * 
	 * @hooked 'after_setup_theme', 1
	 * 
	 * @trigger orto_filter_skins_list
	 */
	function orto_skins_theme_setup1() {
		orto_storage_set( 'skins', apply_filters( 'orto_filter_skins_list', array() ) );
	}
}


if ( ! function_exists( 'orto_skins_add_body_class' ) ) {
	add_filter( 'body_class', 'orto_skins_add_body_class' );
	/**
	 * Add a class "skin_xxx" to the body with current skin name (slug) instead 'xxx'
	 * 
	 * @hooked 'body_class'
	 * 
	 * @param array $classes  The list of classes to be added to the body
	 * 
	 * @return array  The list of classes to be added to the body
	 */
	function orto_skins_add_body_class( $classes ) {
		$classes[] = sprintf( 'skin_%s', orto_skins_get_current_skin_name() );
		return $classes;
	}
}


if ( ! function_exists( 'orto_skins_get_available_skins' ) ) {
	add_filter( 'orto_filter_skins_list', 'orto_skins_get_available_skins' );
	/**
	 * Retrieve available skins from the upgrade server. The list of skins is stored to the cache for 2 days.
	 * 
	 * @hooked 'orto_filter_skins_list'
	 * 
	 * @param array $skins  The list of skins to be updated with the available skins
	 * 
	 * @return array  The list of available skins
	 */
	function orto_skins_get_available_skins( $skins = array() ) {
		$skins_file      = orto_get_file_dir( 'skins/skins.json' );
		$skins_installed = json_decode( orto_fgc( $skins_file ), true );
		$skins           = get_transient( 'orto_list_skins' );
		if ( ! is_array( $skins ) || count( $skins ) == 0 || ! empty( $_GET['force-check'] ) ) {
			$skins_available = orto_get_upgrade_data( array(
				'action' => 'info_skins'
			) );
			if ( empty( $skins_available['error'] ) && ! empty( $skins_available['data'] ) && $skins_available['data'][0] == '{' ) {
				$skins = json_decode( $skins_available['data'], true );
			}
			if ( ! is_array( $skins ) || count( $skins ) == 0 ) {
				$skins = $skins_installed;
			}
			set_transient( 'orto_list_skins', $skins, 2 * 24 * 60 * 60 );       // Store to the cache for 2 days
		}
		// Check if new skins appears after the theme update
		// (included in the folder 'skins' inside the theme)
		if ( is_array( $skins_installed ) && count( $skins_installed ) > 0 ) {
			foreach( $skins_installed as $k => $v ) {
				if ( ! isset( $skins[ $k ] ) ) {
					$skins[ $k ] = $v;
				}
			}
		}
		// Check the state of each skin
		if ( is_array( $skins ) && count( $skins ) > 0 ) {
			foreach( $skins as $k => $v ) {
				if ( ! is_array( $v ) ) {
					unset( $skins[ $k ] );
				} else {
					$skins[ $k ][ 'installed' ] = orto_skins_get_file_dir( "skin.php", $k ) != '' && ! empty( $skins_installed[ $k ][ 'version' ] )
													? $skins_installed[ $k ][ 'version' ]
													: '';
				}
			}
		}
		return $skins;
	}
}

if ( ! function_exists( 'orto_skins_delete_skins_list' ) ) {
	add_action( 'activated_plugin', 'orto_skins_delete_skins_list');
	/**
	 * Delete the cache with a skins list on any plugin activated
	 * 
	 * @hooked 'activated_plugin'
	 * 
	 * @param string $plugin  The name of the activated plugin
	 * @param bool $network  True if the plugin is activated for the whole network
	 */
	function orto_skins_delete_skins_list( $plugin = '', $network = false) {
		if ( strpos( $plugin, 'trx_addons' ) !== false ) {
			delete_transient( 'orto_list_skins' );
		}
	}
}



// Notice with info about new skins or new versions of installed skins
//------------------------------------------------------------------------

if ( ! function_exists( 'orto_skins_admin_notice' ) ) {
	add_action('admin_notices', 'orto_skins_admin_notice');
	/**
	 * Show the admin notice if new skins are available or if installed skins have new versions
	 * 
	 * @hooked 'admin_notices'
	 */
	function orto_skins_admin_notice() {
		// Check if new skins are available
		if ( current_user_can( 'update_themes' ) && orto_is_theme_activated() ) {
			$hide = get_option( 'orto_hide_notice_skins' ) || get_transient( 'orto_hide_notice_skins' );
			if ( $hide || ! orto_exists_trx_addons() ) {
				return;
			}
			$skins  = orto_storage_get( 'skins' );
			$update = 0;
			$free   = 0;
			$pay    = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) ) {
					if ( version_compare( $data['installed'], $data['version'], '<' ) ) {
						$update++;
					}
				} else if ( ! empty( $data['buy_url'] ) ) {
					$pay++;
				} else { 
					$free++;
				}
			}
			// Show notice
			if ( $update + $free + $pay == 0 ) {
				return;
			}
			set_query_var( 'orto_skins_notice_args', compact( 'update', 'free', 'pay' ) );
			get_template_part( apply_filters( 'orto_filter_get_template_part', 'skins/skins-notice' ) );
		}
	}
}

if ( ! function_exists( 'orto_callback_hide_skins_notice' ) ) {
	add_action('wp_ajax_orto_hide_skins_notice', 'orto_callback_hide_skins_notice');
	/**
	 * Dismiss the admin notice for 7 days on the "Close" button clicked
	 * 
	 * @hooked 'wp_ajax_orto_hide_skins_notice'
	 */
	function orto_callback_hide_skins_notice() {
		orto_verify_nonce();
		if ( current_user_can( 'update_themes' ) ) {
			set_transient( 'orto_hide_notice_skins', true, 7 * 24 * 60 * 60 );	// 7 days
		}
		orto_exit();
	}
}

if ( ! function_exists( 'orto_callback_hide_forever_skins_notice' ) ) {
	add_action('wp_ajax_orto_hide_forever_skins_notice', 'orto_callback_hide_forever_skins_notice');
	/**
	 * Hide the admin notice forever on the "Dismiss" button clicked
	 * 
	 * @hooked 'wp_ajax_orto_hide_forever_skins_notice'
	 */
	function orto_callback_hide_forever_skins_notice() {
		orto_verify_nonce();
		if ( current_user_can( 'update_themes' ) ) {
			update_option( 'orto_hide_notice_skins', true );
		}
		orto_exit();
	}
}


// Add skins folder to the theme-specific file search
//------------------------------------------------------------

if ( ! function_exists( 'orto_skins_get_file_dir' ) ) {
	/**
	 * Add skins folder to the theme-specific file search: check if a file exists
	 * in the skin folder and return its path or empty string if a file is not found
	 * 
	 * @param string $file  The file name to be searched
	 * @param string|bool $skin  The name of the skin. If false, the current skin is used.
	 * @param bool $return_url  If true, return the URL of the file, otherwise return the path
	 * 
	 * @return string  The path or URL of the file in the skin folder, or empty string if not found
	 */
	function orto_skins_get_file_dir( $file, $skin = false, $return_url = false ) {
		if ( orto_is_url( $file ) ) {
			$dir = $file;
		} else {
			$dir = '';
			if ( ORTO_ALLOW_SKINS ) {
				$skin_dir = orto_skins_get_current_skin_dir( $skin );
				if ( strpos( $file, $skin_dir ) === 0 ) {
					$skin_dir = '';
				}
				if ( ORTO_CHILD_DIR != ORTO_THEME_DIR && file_exists( ORTO_CHILD_DIR . ( $skin_dir ) . ( $file ) ) ) {
					$dir = ( $return_url ? ORTO_CHILD_URL : ORTO_CHILD_DIR ) . ( $skin_dir ) . orto_check_min_file( $file, ORTO_CHILD_DIR . ( $skin_dir ) );
				} elseif ( file_exists( ORTO_THEME_DIR . ( $skin_dir ) . ( $file ) ) ) {
					$dir = ( $return_url ? ORTO_THEME_URL : ORTO_THEME_DIR ) . ( $skin_dir ) . orto_check_min_file( $file, ORTO_THEME_DIR . ( $skin_dir ) );
				}
			}
		}
		return $dir;
	}
}

if ( ! function_exists( 'orto_skins_get_file_url' ) ) {
	/**
	 * Add skins folder to the theme-specific file search: check if a file exists
	 * in the skin folder and return its url or empty string if a file is not found
	 * 
	 * @param string $file  The file name to be searched
	 * @param string|bool $skin  The name of the skin. If false, the current skin is used.
	 * 
	 * @return string  The URL of the file in the skin folder, or empty string if not found
	 */
	function orto_skins_get_file_url( $file, $skin = false ) {
		return orto_skins_get_file_dir( $file, $skin, true );
	}
}

if ( ! function_exists( 'orto_skins_get_theme_file_dir' ) ) {
	add_filter( 'orto_filter_get_theme_file_dir', 'orto_skins_get_theme_file_dir', 10, 3 );
	/**
	 * Add skins folder to the theme-specific file search: check if a file exists
	 * in the skin folder and return its url or empty string if a file is not found
	 * 
	 * @hooked 'orto_filter_get_theme_file_dir'
	 * 
	 * @param string $dir  The directory to be searched
	 * @param string $file  The file name to be searched
	 * @param bool $return_url  If true, return the URL of the file, otherwise return the path
	 * 
	 * @return string  The path or URL of the file in the skin folder, or empty string if not found
	 */
	function orto_skins_get_theme_file_dir( $dir, $file, $return_url = false ) {
		return orto_skins_get_file_dir( $file, orto_skins_get_current_skin_name(), $return_url );
	}
}


if ( ! function_exists( 'orto_skins_get_folder_dir' ) ) {
	/**
	 * Check if a folder exists in the current skin folder and return its path
	 * or empty string if the folder is not found
	 * 
	 * @param string $folder  The folder name to be searched
	 * @param string|bool $skin  The name of the skin. If false, the current skin is used.
	 * @param bool $return_url  If true, return the URL of the folder, otherwise return the path
	 * 
	 * @return string  The path or URL of the folder in the skin folder, or empty string if not found
	 */
	function orto_skins_get_folder_dir( $folder, $skin = false, $return_url = false ) {
		$dir = '';
		if ( ORTO_ALLOW_SKINS ) {
			$skin_dir = orto_skins_get_current_skin_dir( $skin );
			if ( ORTO_CHILD_DIR != ORTO_THEME_DIR && is_dir( ORTO_CHILD_DIR . ( $skin_dir ) . ( $folder ) ) ) {
				$dir = ( $return_url ? ORTO_CHILD_URL : ORTO_CHILD_DIR ) . ( $skin_dir ) . ( $folder );
			} elseif ( is_dir( ORTO_THEME_DIR . ( $skin_dir ) . ( $folder ) ) ) {
				$dir = ( $return_url ? ORTO_THEME_URL : ORTO_THEME_DIR ) . ( $skin_dir ) . ( $folder );
			}
		}
		return $dir;
	}
}

if ( ! function_exists( 'orto_skins_get_folder_url' ) ) {
	/**
	 * Check if folder exists in the skin folder and return its url
	 * or empty string if folder is not found
	 * 
	 * @param string $folder  The folder name to be searched
	 * @param string|bool $skin  The name of the skin. If false, the current skin is used.
	 * 
	 * @return string  The URL of the folder in the skin folder, or empty string if not found
	 */
	function orto_skins_get_folder_url( $folder, $skin = false ) {
		return orto_skins_get_folder_dir( $folder, $skin, true );
	}
}

if ( ! function_exists( 'orto_skins_get_theme_folder_dir' ) ) {
	add_filter( 'orto_filter_get_theme_folder_dir', 'orto_skins_get_theme_folder_dir', 10, 3 );
	/**
	 * Check if folder exists in the skin folder and return its path
	 * or empty string if folder is not found
	 * 
	 * @hooked 'orto_filter_get_theme_folder_dir'
	 * 
	 * @param string $dir  The directory to be searched
	 * @param string $folder  The folder name to be searched
	 * @param bool $return_url  If true, return the URL of the folder, otherwise return the path
	 * 
	 * @return string  The path or URL of the folder in the skin folder, or empty string if not found
	 */
	function orto_skins_get_theme_folder_dir( $dir, $folder, $return_url = false ) {
		return orto_skins_get_folder_dir( $folder, orto_skins_get_current_skin_name(), $return_url );
	}
}


if ( ! function_exists( 'orto_skins_get_template_part' ) ) {
	add_filter( 'orto_filter_get_template_part', 'orto_skins_get_template_part', 10, 2 );
	/**
	 * Add skins folder to the get_template_part: check if a template part exists
	 * in the skin folder and return its path or empty string if a template part is not found
	 * 
	 * @hooked 'orto_filter_get_template_part'
	 * 
	 * @param string $slug  The slug of the template part to be searched
	 * @param string $part  The part of the template to be searched (optional)
	 * 
	 * @return string  The path of the template part in the skin folder, or empty string if not found
	 */
	function orto_skins_get_template_part( $slug, $part = '' ) {
		if ( ! empty( $part ) ) {
			$part = "-{$part}";
		}
		$slug_in_skins = str_replace( '//', '/', sprintf( 'skins/%1$s/%2$s', orto_skins_get_current_skin_name(), $slug ) );
		if ( orto_skins_get_file_dir( "{$slug}{$part}.php" ) != '' ) {
			$slug = $slug_in_skins;
		} else {
			if ( orto_get_file_dir( "{$slug}{$part}.php" ) == '' && orto_skins_get_file_dir( "{$slug}.php" ) != '' ) {
				$slug = $slug_in_skins;
			}
		}
		return $slug;
	}
}


if ( ! function_exists( 'orto_skins_gutenberg_get_styles' ) ) {
	add_filter( 'orto_filter_gutenberg_get_styles', 'orto_skins_gutenberg_get_styles' );
	/**
	 * Add skin-specific styles to the Gutenberg preview compiled CSS
	 * 
	 * @hooked 'orto_filter_gutenberg_get_styles'
	 * 
	 * @param string $css  The compiled CSS styles
	 * 
	 * @return string  The compiled CSS styles with skin-specific styles added
	 */
	function orto_skins_gutenberg_get_styles( $css ) {
		$css .= orto_fgc( orto_get_file_dir( orto_skins_get_current_skin_dir() . 'css/style.css' ) );
		return $css;
	}
}



// Add tab with skins to the 'Theme Panel'
//------------------------------------------------------

if ( ! function_exists( 'orto_skins_theme_panel_section_filters' ) ) {
	/**
	 * Return a list of categories from the skins list to be used as filters in the Theme Panel
	 * 
	 * @hooked 'orto_skins_theme_panel_section_filters'
	 * 
	 * @param array $skins  The list of skins to be filtered
	 * 
	 * @return array  The list of categories to be used as filters in the Theme Panel
	 */
	function orto_skins_theme_panel_section_filters( $skins ) {
		$list = array();
		if ( is_array( $skins ) ) {
			foreach ( $skins as $skin ) {
				if ( ! empty( $skin['category'] ) ) {
					$parts = array_map( 'strtolower', array_map( 'trim', explode( ',', $skin['category'] ) ) );
					foreach ( $parts as $cat ) {
						if ( ! in_array( $cat, $list ) ) {
							$list[] = $cat;
						}
					}
				}
			}
			if ( count( $list ) > 0 ) {
				sort( $list );
				array_unshift( $list, 'all' );
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'orto_skins_theme_panel_steps' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_steps', 'orto_skins_theme_panel_steps' );
	/**
	 * Add a step (tab) 'Skins' to the Theme Panel
	 * 
	 * @hooked 'trx_addons_filter_theme_panel_steps'
	 * 
	 * @param array $steps  The list of steps (tabs) in the Theme Panel
	 * 
	 * @return array  The list of steps (tabs) in the Theme Panel with the 'Skins' step added
	 */
	function orto_skins_theme_panel_steps( $steps ) {
		if ( ORTO_ALLOW_SKINS ) {
			$steps = orto_array_merge( array( 'skins' => wp_kses_data( __( 'Select a skin for your website.', 'orto' ) ) ), $steps );
		}
		return $steps;
	}
}

if ( ! function_exists( 'orto_skins_theme_panel_tabs' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_tabs', 'orto_skins_theme_panel_tabs' );
	/**
	 * Add a tab link 'Skins' to the Theme Panel
	 * 
	 * @hooked 'trx_addons_filter_theme_panel_tabs'
	 * 
	 * @param array $tabs  The list of tabs in the Theme Panel
	 * 
	 * @return array  The list of tabs in the Theme Panel with the 'Skins' tab added
	 */
	function orto_skins_theme_panel_tabs( $tabs ) {
		if ( ORTO_ALLOW_SKINS ) {
			orto_array_insert_after( $tabs, 'general', array( 'skins' => esc_html__( 'Skins', 'orto' ) ) );
		}
		return $tabs;
	}
}

if ( ! function_exists( 'orto_skins_theme_panel_section' ) ) {
	add_action( 'trx_addons_action_theme_panel_section', 'orto_skins_theme_panel_section', 10, 2);
	/**
	 * Display the section 'Skins' in the Theme Panel
	 * 
	 * @hooked 'trx_addons_action_theme_panel_section'
	 * 
	 * @param string $tab_id  The ID of the tab in the Theme Panel
	 * @param array $theme_info  The information about the theme
	 */
	function orto_skins_theme_panel_section( $tab_id, $theme_info ) {

		if ( 'skins' !== $tab_id ) return;

		$theme_activated = trx_addons_is_theme_activated();
		$skins = $theme_activated ? orto_storage_get( 'skins' ) : false;

		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>"
			class="trx_addons_tabs_section trx_addons_section_mode_thumbs">

			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);

			if ( $theme_activated ) {
				?>
				<div class="trx_addons_theme_panel_section_content trx_addons_theme_panel_skins_selector">

					<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>

					<h1 class="trx_addons_theme_panel_section_title">
						<?php esc_html_e( 'Choose a Skin', 'orto' ); ?>
					</h1>

					<?php do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_section_description">
						<p><?php echo wp_kses_data( __( 'Select the desired style of your website. Some skins may require you to install additional plugins.', 'orto' ) ); ?></p>
					</div>

					<div class="trx_addons_theme_panel_section_toolbar">
						<div class="trx_addons_theme_panel_section_filters">
							<form class="trx_addons_theme_panel_section_filters_form">
								<input class="trx_addons_theme_panel_section_filters_search" type="text" placeholder="<?php esc_attr_e( 'Search for skin', 'orto' ); ?>" value="" />
							</form>
							<?php
							$cats = orto_skins_theme_panel_section_filters( $skins );
							if ( is_array( $cats ) && count( $cats ) > 2 ) {
								?>
								<ul class="trx_addons_theme_panel_section_filters_list">
									<?php
									foreach( $cats as $k => $cat ) {
										?>
										<li class="trx_addons_theme_panel_section_filters_list_item<?php
												if ( $k == 0 ) { echo ' filter_active'; }
											?>"
											data-filter="<?php echo esc_attr( $cat ); ?>"
										>
											<a href="#" role="button"><?php echo esc_html( in_array( $cat, array( 'ai' ) ) ? strtoupper( $cat ) : ucfirst( $cat ) ); ?></a>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
							}
							?>
						</div>
						<?php
						// View mode buttons: thumbs | list
						if ( apply_filters( 'orto_filter_skins_view_mode', true ) ) {
							?>
							<div class="trx_addons_theme_panel_section_view_mode">
								<span class="trx_addons_theme_panel_section_view_mode_thumbs" data-mode="thumbs" title="<?php esc_attr_e( 'Large thumbnails', 'orto' ); ?>"></span>
								<span class="trx_addons_theme_panel_section_view_mode_list" data-mode="list" title="<?php esc_attr_e( 'List with details', 'orto' ); ?>"></span>
							</div>
							<?php
						}
						?>
					</div>

					<?php do_action('trx_addons_action_theme_panel_before_list_items', $tab_id, $theme_info); ?>
					
					<div class="trx_addons_theme_panel_skins_list trx_addons_image_block_wrap">
						<?php
						if ( is_array( $skins ) ) {
							// Time to show new skins at the start of the list
							$time_new = strtotime( apply_filters( 'orto_filter_time_to_show_new_skins', '-2 weeks' ) );
							// Sort skins by slug
							uksort( $skins, function( $a, $b ) use ( $skins, $time_new ) {
								$rez = apply_filters( 'orto_filter_skins_sorted', true )
										? strcmp( $a, $b )
										: -1;
								// Move an active skin to the top of the list
								if ( $a == ORTO_SKIN_NAME ) $rez = -1;
								else if ( $b == ORTO_SKIN_NAME ) $rez = 1;
								// Move the skin 'Default' to the top of the list (after the active skin)
								else if ( $a == 'default' ) $rez = -1;
								else if ( $b == 'default' ) $rez = 1;
								// Move installed skins to the top of the list (after skin 'Default')
								else if ( ! empty( $skins[ $a ]['installed'] ) && ! empty( $skins[ $b ]['installed'] ) ) $rez = strcmp( $a, $b );
								else if ( ! empty( $skins[ $a ]['installed'] ) ) $rez = -1;
								else if ( ! empty( $skins[ $b ]['installed'] ) ) $rez = 1;
								// Move new skins to the top of the list (after installed skins)
								else if ( ! empty( $skins[ $a ]['uploaded'] ) && strtotime( $skins[ $a ]['uploaded'] ) > $time_new && ! empty( $skins[ $b ]['uploaded'] ) && strtotime( $skins[ $b ]['uploaded'] ) > $time_new ) $rez = strcmp( $a, $b );
								else if ( ! empty( $skins[ $a ]['uploaded'] ) && strtotime( $skins[ $a ]['uploaded'] ) > $time_new ) $rez = -1;
								else if ( ! empty( $skins[ $b ]['uploaded'] ) && strtotime( $skins[ $b ]['uploaded'] ) > $time_new ) $rez = 1;
								// Move updated skins to the top of the list (after new skins)
								else if ( ! empty( $skins[ $a ]['updated'] ) && strtotime( $skins[ $a ]['updated'] ) > $time_new && ! empty( $skins[ $b ]['updated'] ) && strtotime( $skins[ $b ]['updated'] ) > $time_new ) $rez = strcmp( $a, $b );
								else if ( ! empty( $skins[ $a ]['updated'] ) && strtotime( $skins[ $a ]['updated'] ) > $time_new ) $rez = -1;
								else if ( ! empty( $skins[ $b ]['updated'] ) && strtotime( $skins[ $b ]['updated'] ) > $time_new ) $rez = 1;
								return $rez;
							} );
							foreach ( $skins as $skin => $data ) {
								$skin_classes = array();
								if ( ORTO_SKIN_NAME == $skin ) {
									$skin_classes[] = 'skin_active';
								}
								if ( ! empty( $data['installed'] ) ) {
									$skin_classes[] = 'skin_installed';
								} else if ( ! empty( $data['buy_url'] ) ) {
									$skin_classes[] = 'skin_buy';
								} else {
									$skin_classes[] = 'skin_free';
								}
								if ( ! empty( $data['updated'] ) && ( empty( $data['uploaded'] ) || $data['updated'] > $data['uploaded'] ) && strtotime( $data['updated'] ) > $time_new ) {
									$skin_classes[] = 'skin_updated';
								} else if ( empty( $data['installed'] ) && ! empty( $data['uploaded'] ) && strtotime( $data['uploaded'] ) > $time_new ) {
									$skin_classes[] = 'skin_new';
								}
								// 'trx_addons_image_block' is a inline-block element and spaces around it are not allowed
								?><div class="trx_addons_image_block <?php echo esc_attr( join( ' ', $skin_classes ) ); ?>"<?php
									if ( ! empty( $data['category'] ) ) {
										?> data-filter-value="<?php echo esc_attr( $data['category'] ); ?>"<?php
									}
									?> data-search-value="<?php
										if ( ! empty( $data['title'] ) ) {
											echo esc_attr( strtolower( $data['title'] ) );
										} else {
											echo esc_attr( $skin );
										}
										if ( ! empty( $data['keywords'] ) ) {
											echo ( ! empty( $data['title'] ) ? ', ' : '' ) . esc_attr( strtolower( $data['keywords'] ) );
										}
									?>"<?php
								?>>
									<div class="trx_addons_image_block_inner" tabindex="0">
										<div class="trx_addons_image_block_image
										 	<?php 
											$theme_slug  = get_template();
											// Skin image
											$img = ! empty( $data['installed'] )
													? orto_skins_get_file_url( 'skin.jpg', $skin )
													: trailingslashit( orto_storage_get( 'theme_upgrade_url' ) ) . 'skins/' . urlencode( apply_filters( 'orto_filter_original_theme_slug', $theme_slug ) ) . '/' . urlencode( $skin ) . '/skin.jpg';
											if ( ! empty( $img ) ) {
												echo orto_add_inline_css_class( 'background-image: url(' . esc_url( $img ) . ');' );
											}				 	
										 	?>">
										 	<?php
											// Link to demo site
											if ( ! empty( $data['demo_url'] ) ) {
												?>
												<a href="<?php echo esc_url( $data['demo_url'] ); ?>"
													class="trx_addons_image_block_link trx_addons_image_block_link_view_demo"
													<?php echo orto_external_links_target( true ); ?>
													title="<?php esc_attr_e( 'Live Preview', 'orto' ); ?>"
												>
													<span class="trx_addons_image_block_link_caption">
														<?php
														esc_html_e( 'Live Preview', 'orto' );
														?>
													</span>
												</a>
												<?php
											}
											// Labels
											if ( ! empty( $data['updated'] ) && ( empty( $data['uploaded'] ) || $data['updated'] > $data['uploaded'] ) && strtotime( $data['updated'] ) > $time_new ) {
												?><span class="skin_label"><?php esc_html_e( 'Updated', 'orto' ); ?></span><?php
											} else if ( ! empty( $data['installed'] ) && strtotime( $data['installed'] ) > $time_new ) {
												?><span class="skin_label"><?php esc_html_e( 'Downloaded', 'orto' ); ?></span><?php
											} else if ( ! empty( $data['uploaded'] ) && strtotime( $data['uploaded'] ) > $time_new ) {
												?><span class="skin_label"><?php esc_html_e( 'New', 'orto' ); ?></span><?php
											}
											?>
									 	</div>
									 	<div class="trx_addons_image_block_footer">
											<?php
											// Links to choose skin, update, download, purchase
											if ( ! empty( $data['installed'] ) ) {
												// Active skin
												if ( ORTO_SKIN_NAME == $skin ) {
													?>
													<span class="trx_addons_image_block_link trx_addons_image_block_link_active">
														<?php
														esc_html_e( 'Active', 'orto' );
														?>
													</span>
													<?php
												} else {
													// Button 'Delete'
													?>
													<a href="#" role="button" tabindex="0"
														class="trx_addons_image_block_link trx_addons_image_block_link_delete trx_addons_image_block_link_delete_skin trx_addons_button trx_addons_button_small trx_addons_button_fail"
														data-skin="<?php echo esc_attr( $skin ); ?>"
													>
														<span data-tooltip-text="<?php
															esc_html_e( 'Delete', 'orto' );
														?>"></span>
														<span class="trx_addons_image_block_link_caption"><?php
															esc_html_e( 'Delete', 'orto' );
														?></span>
													</a>
													<?php
													// Button 'Activate'
													?>
													<a href="#" role="button" tabindex="0"
														class="trx_addons_image_block_link trx_addons_image_block_link_activate trx_addons_image_block_link_choose_skin trx_addons_button trx_addons_button_small trx_addons_button_accent trx_addons_image_block_icon_hidden"
														data-skin="<?php echo esc_attr( $skin ); ?>">
															<?php
															esc_html_e( 'Activate', 'orto' );
															?>
													</a>
													<?php
												}
												// Button 'Update'
												if ( version_compare( $data['installed'], $data['version'], '<' ) ) {
													?>
													<a href="#" role="button"
														class="trx_addons_image_block_link trx_addons_image_block_link_update trx_addons_image_block_link_update_skin trx_addons_button trx_addons_button_small trx_addons_button_warning trx_addons_image_block_icon_hidden"
														data-skin="<?php echo esc_attr( $skin ); ?>">
															<?php
															// Translators: Add new version of the skin to the string
															echo esc_html( sprintf( __( 'Update to v.%s', 'orto' ), $data['version'] ) );
															?>
													</a>
													<?php
												}

											} else if ( ! empty( $data['buy_url'] ) ) {
												// Button 'Purchase'
												?>
												<a href="#" role="button" tabindex="0"
													class="trx_addons_image_block_link trx_addons_image_block_link_download trx_addons_image_block_link_buy_skin trx_addons_button trx_addons_button_small trx_addons_button_success trx_addons_image_block_icon_hidden"
													data-skin="<?php echo esc_attr( $skin ); ?>"
													data-buy="<?php echo esc_url( $data['buy_url'] ); ?>">
														<?php
														esc_html_e( 'Purchase', 'orto' );
														?>
												</a>
												<?php

											} else {
												// Button 'Download'
												?>
												<a href="#" role="button" tabindex="0"
													class="trx_addons_image_block_link trx_addons_image_block_link_download trx_addons_image_block_link_download_skin trx_addons_button trx_addons_button_small trx_addons_image_block_icon_hidden"
													data-skin="<?php echo esc_attr( $skin ); ?>">
														<?php
														esc_html_e( 'Download', 'orto' );
														?>
												</a>
												<?php
											}
											// Skin title
											if ( ! empty( $data['title'] ) ) {
												?>
												<h5 class="trx_addons_image_block_title">
													<?php
													echo esc_html( $data['title'] );
													?>
												</h5>
												<?php
											}
											// Skin version
											if ( ! empty( $data['installed'] ) ) {
												?>
												<div class="trx_addons_image_block_description">
													<?php
													echo esc_html( sprintf( __( 'Version %s', 'orto' ), $data['installed'] ) );
													?>
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</div><?php // No spaces allowed after this <div>, because it is an inline-block element
							}
						}
						?>
					</div>

					<?php do_action('trx_addons_action_theme_panel_after_list_items', $tab_id, $theme_info); ?>

				</div>
				<?php
				do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);
			} else {
				?>
				<div class="<?php
					if ( orto_exists_trx_addons() ) {
						echo 'trx_addons_info_box trx_addons_info_box_warning';
					} else {
						echo 'error';
					}
				?>"><p>
					<?php esc_html_e( 'Activate your theme in order to be able to change skins.', 'orto' ); ?>
				</p></div>
				<?php
			}

			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}
}


if ( ! function_exists( 'orto_skins_about_enqueue_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'orto_skins_about_enqueue_scripts' );
	/**
	 * Load a page-specific scripts and styles for the 'Skins' section in the Theme Panel
	 * 
	 * @hooked 'admin_enqueue_scripts'
	 */
	function orto_skins_about_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && ( false !== strpos($screen->id, '_page_trx_addons_theme_panel') || in_array( $screen->id, array( 'update-core', 'update-core-network' ) ) ) ) {
			wp_enqueue_style( 'orto-skins-admin', orto_get_file_url( 'skins/skins-admin.css' ), array(), null );
			wp_enqueue_script( 'orto-skins-admin', orto_get_file_url( 'skins/skins-admin.js' ), array( 'jquery' ), null, true );
		}
	}
}

if ( ! function_exists( 'orto_skins_localize_script' ) ) {
	add_filter( 'orto_filter_localize_script_admin', 'orto_skins_localize_script' );
	/**
	 * Add a page-specific vars to the localize array for the 'Skins' section in the Theme Panel
	 * 
	 * @hooked 'orto_filter_localize_script_admin'
	 * 
	 * @param array $arr  The array of localized strings
	 * 
	 * @return array  The array of localized strings with the 'Skins' section strings added
	 */
	function orto_skins_localize_script( $arr ) {

		// Switch an active skin
		$arr['msg_switch_skin_caption']           = esc_html__( "Attention!", 'orto' );
		$arr['msg_switch_skin']                   = apply_filters( 'orto_filter_msg_switch_skin',
			'<p>'
			. esc_html__( "Some skins require installation of additional plugins.", 'orto' )
			. '</p><p>'
			. esc_html__( "After selecting a new skin, your theme settings will be changed.", 'orto' )
			. '</p>'
		);
		$arr['msg_switch_skin_success']           = esc_html__( 'A new skin is selected. The page will be reloaded.', 'orto' );
		$arr['msg_switch_skin_success_caption']   = esc_html__( 'Skin is changed!', 'orto' );

		// Delete a skin
		$arr['msg_delete_skin_caption']           = esc_html__( "Delete skin", 'orto' );
		$arr['msg_delete_skin']                   = apply_filters( 'orto_filter_msg_delete_skin',
			'<p>'
			. esc_html__( "Attention! This skin will be deleted from the 'skins' folder inside your theme folder.", 'orto' )
			. '</p>'
		);
		$arr['msg_delete_skin_success']           = esc_html__( 'Specified skin is deleted. The page will be reloaded.', 'orto' );
		$arr['msg_delete_skin_success_caption']   = esc_html__( 'Skin is deleted!', 'orto' );
		$arr['msg_delete_skin_error_caption']     = esc_html__( 'Skin delete error!', 'orto' );

		// Download a new skin
		$arr['msg_download_skin_caption']         = esc_html__( "Download skin", 'orto' );
		$arr['msg_download_skin']                 = apply_filters( 'orto_filter_msg_download_skin',
			'<p>'
			. esc_html__( "The new skin will be installed in the 'skins' folder inside your theme folder.", 'orto' )
			. '</p><p>'
			. esc_html__( "Attention! Do not forget to activate the new skin after installation.", 'orto' )
			. '</p>'
		);
		$arr['msg_download_skin_success']         = esc_html__( 'A new skin is installed. The page will be reloaded.', 'orto' );
		$arr['msg_download_skin_success_caption'] = esc_html__( 'Skin is installed!', 'orto' );
		$arr['msg_download_skin_error_caption']   = esc_html__( 'Skin download error!', 'orto' );

		// Buy a new skin
		$arr['msg_buy_skin_caption']              = esc_html__( "Download purchased skin", 'orto' );
		$arr['msg_buy_skin']                      = apply_filters( 'orto_filter_msg_buy_skin',
			'<p>'
			. esc_html__( "1. Follow the link below and purchase the selected skin. After payment you will receive a purchase code.", 'orto' )
			. '</p><p>'
			. '<a href="#" role="button"' . orto_external_links_target( true ) . '>' . esc_html__( "Purchase the selected skin.", 'orto' ) . '</a>'
			. '</p><p>'
			. esc_html__( "2. Enter the purchase code of the selected skin in the field below and press the button 'Apply'.", 'orto' )
			. '</p><p>'
			. esc_html__( "3. The new skin will be installed to the folder 'skins' inside your theme folder.", 'orto' )
			. '</p><p>'
			. esc_html__( "Attention! Do not forget to activate the new skin after installation.", 'orto' )
			. '</p>'
		);
		$arr['msg_buy_skin_placeholder']          = esc_html__( 'Enter the purchase code of the skin.', 'orto' );
		$arr['msg_buy_skin_success']              = esc_html__( 'A new skin is installed. The page will be reloaded.', 'orto' );
		$arr['msg_buy_skin_success_caption']      = esc_html__( 'Skin is installed!', 'orto' );
		$arr['msg_buy_skin_error_caption']        = esc_html__( 'Skin download error!', 'orto' );

		// Update an installed skin
		$arr['msg_update_skin_caption']           = esc_html__( "Update skin", 'orto' );
		$arr['msg_update_skin']                   = apply_filters( 'orto_filter_msg_update_skin',
			'<p>'
			. esc_html__( "Attention! The new version of the skin will be installed in the same folder instead the current version!", 'orto' )
			. '</p><p>'
			. esc_html__( "If you made any changes in the files from the folder of the selected skin - they will be lost.", 'orto' )
			. '</p><p>'
			. esc_html__( "If you do not have the latest version of the theme installed, be sure to update the theme before updating the skin to avoid errors.", 'orto' )
			. '</p>'
		);
		$arr['msg_update_skin_success']           = esc_html__( 'The skin is updated. The page will be reloaded.', 'orto' );
		$arr['msg_update_skin_success_caption']   = esc_html__( 'Skin is updated!', 'orto' );
		$arr['msg_update_skin_error_caption']     = esc_html__( 'Skin update error!', 'orto' );
		$arr['msg_update_skins_result']           = esc_html__( 'Selected skins are updated.', 'orto' );
		$arr['msg_update_skins_error']            = esc_html__( 'Not all selected skins have been updated.', 'orto' );

		// Hide notice about skins
		$arr['msg_hide_skins_notice_forever']         = esc_html__( "We regularly add new skins with fresh designs and features. Hide this if you're happy with your current setup – just note that you won’t be notified about new skins in the future.", 'orto' );
		$arr['msg_hide_skins_notice_forever_caption'] = esc_html__( "Are you sure?", 'orto' );
		$arr['msg_hide_skins_notice_forever_ok']      = esc_html__( "Never show again", 'orto' );
		$arr['msg_hide_skins_notice_forever_cancel']  = esc_html__( "Keep showing updates", 'orto' );

		return $arr;
	}
}


if ( ! function_exists( 'orto_skins_ajax_switch_skin' ) ) {
	add_action( 'wp_ajax_orto_switch_skin', 'orto_skins_ajax_switch_skin' );
	/**
	 * AJAX handler: switch the current skin
	 * 
	 * @hooked 'wp_ajax_orto_switch_skin'
	 */
	function orto_skins_ajax_switch_skin() {

		orto_verify_nonce();

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			orto_forbidden( esc_html__( 'Sorry, you are not allowed to switch skins.', 'orto' ) );
		}

		$response = array( 'error' => '' );

		$skin  = orto_sanitize_slug( orto_get_value_gp( 'skin' ) );
		$skins = orto_storage_get( 'skins' );

		if ( empty( $skin ) || ! isset( $skins[ $skin ] ) || empty( $skins[ $skin ]['installed'] ) ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Can not switch to the skin %s', 'orto' ), $skin );

		} elseif ( ORTO_SKIN_NAME == $skin ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Skin %s is already active', 'orto' ), $skin );

		} else {
			// Get current theme slug
			$theme_slug = get_stylesheet();
			// Get previously saved options for new skin
			$skin_mods = get_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), false );
			if ( ! $skin_mods ) {
				// First activation of the skin - get options from the file
				if ( file_exists( ORTO_THEME_DIR . 'skins/skins-options.php' ) ) {
					require_once ORTO_THEME_DIR . 'skins/skins-options.php';
					if ( isset( $skins_options[ $skin ]['options'] ) ) {
						$skin_mods = apply_filters(
										'orto_filter_skin_options_restore_from_file',
										orto_unserialize( $skins_options[ $skin ]['options'], true )
										);
					}
				}
			}
			if ( empty( $skin_mods ) ) {
				$response['success'] = esc_html__( 'A new skin is selected, but options of the new skin are not found!', 'orto' );
			}
			// Save current options
			update_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, ORTO_SKIN_NAME ), apply_filters( 'orto_filter_skin_options_store', get_theme_mods() ) );
			// Replace theme mods with options from new skin
			if ( ! empty( $skin_mods ) ) {
				orto_options_update( apply_filters( 'orto_filter_skin_options_restore', $skin_mods ) );
			}
			// Replace current skin
			update_option( sprintf( 'theme_skin_%s', $theme_slug ), $skin );
			// Clear current skin from visitor's storage
			if ( ORTO_REMEMBER_SKIN ) {
				orto_set_cookie( 'skin_current', '' );
			}
			// Set a flag to recreate custom layouts
			update_option('trx_addons_cpt_layouts_created', 0);
			// Set a flag to regenerate styles and scripts on first run
			if ( apply_filters( 'orto_filter_regenerate_merged_files_after_switch_skin', true ) ) {
				orto_set_action_save_options();
			}
			// Clear a list with posts for the importer
			delete_transient( 'trx_addons_installer_posts' );
			// Trigger action
			do_action( 'orto_action_skin_switched', $skin, ORTO_SKIN_NAME );
		}

		orto_ajax_response( $response );
	}
}

if ( ! function_exists( 'orto_skins_clear_saved_shapes_list' ) ) {
	add_action( 'orto_action_skin_switched', 'orto_skins_clear_saved_shapes_list', 10, 2 );
	/**
	 * Remove a saved shapes list after switching the skin
	 * 
	 * @hooked 'orto_action_skin_switched'
	 * 
	 * @param string $skin       The name of the new skin (not used here)
	 * @param string $skin_name  The name of the previous skin (not used here)
	 */
	function orto_skins_clear_saved_shapes_list( $skin = '', $skin_name = '' ) {
		delete_transient( 'trx_addons_shapes' );
	}
}

if ( ! function_exists( 'orto_skins_options_restore_from_file' ) ) {
	add_filter( 'orto_filter_skin_options_restore_from_file', 'orto_skins_options_restore_from_file' );
	/**
	 * Remove all entries with a media from options restored from the file
	 * 
	 * @hooked 'orto_filter_skin_options_restore_from_file'
	 * 
	 * @param array $mods  The options to restore
	 * 
	 * @return array  The options to restore without media entries
	 */
	function orto_skins_options_restore_from_file( $mods ) {
		$options = orto_storage_get( 'options' );
		if ( is_array( $options ) ) {
			foreach( $options as $k => $v ) {
				if ( ! empty( $v['type'] ) && in_array( $v['type'], array( 'image', 'media', 'video', 'audio' ) ) && isset( $mods[ $k ] ) ) {
					unset( $mods[ $k ] );
				}
			}
		}
		return $mods;
	}
}


if ( ! function_exists( 'orto_skins_ajax_delete_skin' ) ) {
	add_action( 'wp_ajax_orto_delete_skin', 'orto_skins_ajax_delete_skin' );
	/**
	 * AJAX handler: delete the specified (not current) skin
	 * 
	 * @hooked 'wp_ajax_orto_delete_skin'
	 */
	function orto_skins_ajax_delete_skin() {

		orto_verify_nonce();

		$response = array( 'error' => '' );

		if ( ! current_user_can( 'update_themes' ) ) {
			$response['error'] = esc_html__( 'Sorry, you are not allowed to delete skins.', 'orto' );

		} else {
			$skin            = orto_sanitize_slug( orto_get_value_gp( 'skin' ) );
			$skins_file      = orto_get_file_dir( 'skins/skins.json' );
			$skins_installed = json_decode( orto_fgc( $skins_file ), true );

			$dest = ORTO_THEME_DIR . 'skins'; // Used instead orto_get_folder_dir( 'skins' ) to prevent install skin to the child-theme

			if ( empty( $skin ) || ! isset( $skins_installed[ $skin ] ) ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'Can not delete the skin "%s"', 'orto' ), $skin );

			} else if ( empty( $skins_installed[ $skin ] ) ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'Skin "%s" is not installed', 'orto' ), $skin );

			} else if ( orto_skins_get_current_skin_name() == $skin ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'Can not delete the active skin "%s"', 'orto' ), $skin );

			} else if ( ! is_dir( "{$dest}/{$skin}" ) ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'A skin folder "%s" is not exists', 'orto' ), $skin );

			} else {
				// Delete a skin folder
				orto_unlink( "{$dest}/{$skin}" );
				// Remove a skin from json
				unset( $skins_installed[ $skin ] );
				orto_fpc( $skins_file, json_encode( $skins_installed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) );
				// Remove a stored list to reload it while next site visit occurs
				delete_transient( 'orto_list_skins' );

			}
		}

		orto_ajax_response( $response );
	}
}


if ( ! function_exists( 'orto_skins_ajax_download_skin' ) ) {
	add_action( 'wp_ajax_orto_download_skin', 'orto_skins_ajax_download_skin' );
	add_action( 'wp_ajax_orto_buy_skin', 'orto_skins_ajax_download_skin' );
	add_action( 'wp_ajax_orto_update_skin', 'orto_skins_ajax_download_skin' );
	/**
	 * AJAX handler: download a new skin or update the installed skin
	 * 
	 * @hooked 'wp_ajax_orto_download_skin'
	 * @hooked 'wp_ajax_orto_buy_skin'
	 * @hooked 'wp_ajax_orto_update_skin'
	 */
	function orto_skins_ajax_download_skin() {

		orto_verify_nonce();

		$response = array( 'error' => '' );

		if ( ! current_user_can( 'update_themes' ) ) {
			$response['error'] = esc_html__( 'Sorry, you are not allowed to download/update skins.', 'orto' );

		} else {
			$action = current_action() == 'wp_ajax_orto_download_skin'
							? 'download'
							: ( current_action() == 'wp_ajax_orto_buy_skin'
								? 'buy'
								: 'update' );

			$key    = orto_get_theme_activation_code();

			$skin   = orto_sanitize_slug( orto_get_value_gp( 'skin' ) );
			$code   = 'update' == $action
							? get_option( sprintf( 'purchase_code_%s_%s', get_template(), $skin ), '' )
							: orto_get_value_gp( 'code' );

			$skins  = orto_storage_get( 'skins' );

			if ( empty( $key ) ) {
				$response['error'] = esc_html__( 'Theme is not activated!', 'orto' );

			} else if ( empty( $skin ) || ! isset( $skins[ $skin ] ) ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'Can not download the skin %s', 'orto' ), $skin );

			} else if ( ! empty( $skins[ $skin ]['installed'] ) && 'update' != $action ) {
				// Translators: Add the skin's name to the message
				$response['error'] = sprintf( esc_html__( 'Skin %s is already installed', 'orto' ), $skin );

			} else {
				$result = orto_get_upgrade_data( array(
					'action'   => 'download_skin',
					'key'      => $key,
					'skin'     => $skin,
					'skin_key' => $code,
				) );
				if ( substr( $result['data'], 0, 2 ) == 'PK' ) {
					orto_allow_upload_archives();
					$tmp_name = 'tmp-' . rand() . '.zip';
					$tmp      = wp_upload_bits( $tmp_name, null, $result['data'] );
					orto_disallow_upload_archives();
					if ( $tmp['error'] ) {
						$response['error'] = esc_html__( 'Problem with save upgrade file to the folder with uploads', 'orto' );
					} else {
						$response['error'] .= orto_skins_install_skin( $skin, $tmp['file'], $result['info'], $action );
						// Store purchase code to update skins in the future
						if ( ! empty( $code ) && empty( $response['error'] ) ) {
							update_option( sprintf( 'purchase_code_%s_%s', get_template(), $skin ), $code );
						}
					}
				} else {
					$response['error'] = ! empty( $result['error'] )
											? $result['error']
											: esc_html__( 'Package with skin is corrupt', 'orto' );
				}
			}
		}

		orto_ajax_response( $response );
	}
}


if ( ! function_exists( 'orto_skins_install_skin' ) ) {
	/**
	 * Unpack and install the skin
	 * 
	 * @param string $skin  The name of the skin to install
	 * @param string $file  The path to the skin package file
	 * @param array  $info  The skin info array
	 * @param string $action  The action type: 'download', 'buy', or 'update'
	 * 
	 * @return string  An error message if the installation failed, or an empty string if successful
	 */
	function orto_skins_install_skin( $skin, $file, $info, $action ) {
		if ( file_exists( $file ) ) {
			ob_start();
			// Unpack skin
			$dest = ORTO_THEME_DIR . 'skins'; // Used instead orto_get_folder_dir( 'skins' ) to prevent install skin to the child-theme
			if ( ! empty( $dest ) ) {
				orto_unzip_file( $file, $dest );
			}
			// Remove uploaded archive
			unlink( $file );
			$log = ob_get_contents();
			ob_end_clean();
			// Save skin options (if an action is not 'update')
			if ( 'update' != $action && ! empty( $info['skin_options'] ) ) {
				if ( is_string( $info['skin_options'] ) && is_serialized( $info['skin_options'] ) ) {
					$info['skin_options'] = orto_unserialize( stripslashes( $info['skin_options'] ), true );
				}
				if ( is_array( $info['skin_options'] ) ) {
					$theme_slug  = get_stylesheet();
					update_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), $info['skin_options'] );
				}
			}
			// Update skins list
			$skins_file      = orto_get_file_dir( 'skins/skins.json' );
			$skins_installed = json_decode( orto_fgc( $skins_file ), true );
			$skins_available = orto_storage_get( 'skins' );
			if ( isset( $skins_available[ $skin ][ 'installed' ] ) ) {
				unset( $skins_available[ $skin ][ 'installed' ] );
			}
			$skins_installed[ $skin ] = $skins_available[ $skin ];
			orto_fpc( $skins_file, json_encode( $skins_installed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) );
			// Remove a stored list to reload it while next site visit occurs
			delete_transient( 'orto_list_skins' );
			// Set a flag to regenerate styles and scripts on first run if a current skin is updated
			if ( 'update' == $action
				&& orto_skins_get_current_skin_name() == $skin
				&& apply_filters( 'orto_filter_regenerate_merged_files_after_switch_skin', true )
			) {
				orto_set_action_save_options();
			}
			// Trigger action
			do_action( 'orto_action_skin_updated', $skin );
		} else {
			return esc_html__( 'Uploaded file with skin package is not available', 'orto' );
		}
	}
}



//-------------------------------------------------------
//-- Update skins via WordPress update screen
//-------------------------------------------------------

if ( ! function_exists( 'orto_skins_update_list' ) ) {
	add_action('core_upgrade_preamble', 'orto_skins_update_list');
	/**
	 * Add new skins versions to the WordPress update screen
	 * 
	 * @hooked 'core_upgrade_preamble'
	 */
	function orto_skins_update_list() {
		if ( current_user_can( 'update_themes' ) && orto_is_theme_activated() ) {
			$skins  = orto_storage_get( 'skins' );
			$update = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			?>
			<h2>
				<?php esc_html_e( 'Active theme components: Skins', 'orto' ); ?>
			</h2>
			<?php
			if ( $update == 0 ) {
				?>
				<p><?php esc_html_e( 'Skins of the current theme are all up to date.', 'orto' ); ?></p>
				<?php
				return;
			}
			?>
			<p>
				<?php esc_html_e( 'The following skins have new versions available. Check the ones you want to update and then click &#8220;Update Skins&#8221;.', 'orto' ); ?>
			</p>
			<p>
				<?php echo wp_kses_data( __( '<strong>Please Note:</strong> Any customizations you have made to skin files will be lost.', 'orto' ) ); ?>
			</p>
			<div class="upgrade orto_upgrade_skins">
				<p><input id="upgrade-skins" class="button orto_upgrade_skins_button" type="button" value="<?php esc_attr_e( 'Update Skins', 'orto' ); ?>" /></p>
				<table class="widefat updates-table" id="update-skins-table">
					<thead>
					<tr>
						<td class="manage-column check-column"><input type="checkbox" id="skins-select-all" /></td>
						<td class="manage-column"><label for="skins-select-all"><?php esc_html_e( 'Select All', 'orto' ); ?></label></td>
					</tr>
					</thead>
					<tbody class="plugins">
						<?php
						foreach ( $skins as $skin => $data ) {
							if ( empty( $data['installed'] ) || ! version_compare( $data['installed'], $data['version'], '<' ) ) {
								continue;
							}
							$checkbox_id = 'checkbox_' . md5( $skin );
							?>
							<tr>
								<td class="check-column">
									<input type="checkbox" name="checked[]" id="<?php echo esc_attr( $checkbox_id ); ?>" value="<?php echo esc_attr( $skin ); ?>" />
									<label for="<?php echo esc_attr( $checkbox_id ); ?>" class="screen-reader-text">
										<?php
										// Translators: %s: Skin name
										printf( esc_html__( 'Select %s', 'orto' ), $data['title'] );
										?>
									</label>
								</td>
								<td class="plugin-title"><p>
									<img src="<?php echo esc_url( orto_skins_get_file_url( 'skin.jpg', $skin ) ); ?>" width="85" class="updates-table-screenshot" alt="<?php echo esc_attr( $data['title'] ); ?>" />
									<strong><?php echo esc_html( $data['title'] ); ?></strong>
									<?php
									// Translators: 1: skin version, 2: new version
									printf(
										esc_html__( 'You have version %1$s installed. Update to %2$s.', 'orto' ),
										$data['installed'],
										$data['version']
									);
									?>
								</p></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td class="manage-column check-column"><input type="checkbox" id="skins-select-all-2" /></td>
							<td class="manage-column"><label for="skins-select-all-2"><?php esc_html_e( 'Select All', 'orto' ); ?></label></td>
						</tr>
					</tfoot>
				</table>
				<p><input id="upgrade-skins-2" class="button orto_upgrade_skins_button" type="button" value="<?php esc_attr_e( 'Update Skins', 'orto' ); ?>" /></p>
			</div>
			<?php
		}
	}
}


if ( ! function_exists( 'orto_skins_update_counts' ) ) {
	add_filter('wp_get_update_data', 'orto_skins_update_counts', 10, 2);
	/**
	 * Add new skins count to the WordPress updates count
	 * 
	 * @hooked 'wp_get_update_data'
	 * 
	 * @param array $update_data  The array of update data
	 * @param array $titles       The array of titles for the updates
	 * 
	 * @return array  The updated array of update data with skins count added
	 */
	function orto_skins_update_counts($update_data, $titles) {
		if ( current_user_can( 'update_themes' ) ) {
			$skins  = orto_storage_get( 'skins' );
			$update = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			if ( $update > 0 ) {
				$update_data[ 'counts' ][ 'skins' ]  = $update;
				$update_data[ 'counts' ][ 'total' ] += $update;
				// Translators: %d: number of updates available to installed skins
				$titles['skins']                     = sprintf( _n( '%d Skin Update', '%d Skin Updates', $update, 'orto' ), $update );
				$update_data[ 'title' ]              = esc_attr( implode( ', ', $titles ) );
			}
		}
		return $update_data;
	}
}


// One-click import support
//------------------------------------------------------------------------

if ( ! function_exists( 'orto_skins_importer_export' ) ) {
	if ( false && is_admin() ) {
		add_action( 'trx_addons_action_importer_export', 'orto_skins_importer_export', 10, 1 );
	}
	/**
	 * Export a skins options to the file for the one-click importer. Not used now, because the current skin options are only exported
	 * 
	 * @hooked 'trx_addons_action_importer_export'
	 * 
	 * @param object $importer  The importer object
	 */	
	function orto_skins_importer_export( $importer ) {
		$skins  = orto_storage_get( 'skins' );
		$output = '';
		if ( is_array( $skins ) && count( $skins ) > 0 ) {
			$output     = '<?php'
						. "\n//" . esc_html__( 'Skins', 'orto' )
						. "\n\$skins_options = array(";
			$counter    = 0;
			$theme_mods = get_theme_mods();
			$theme_slug = get_stylesheet();
			foreach ( $skins as $skin => $skin_data ) {
				$options = $skin != orto_skins_get_current_skin_name()
								? get_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), false )
								: false;
				if ( false === $options ) {
					$options = $theme_mods;
				}
				$output .= ( $counter++ ? ',' : '' )
						. "\n\t'{$skin}' => array("
						. "\n\t\t'options' => " . "'" . str_replace( array( "\r", "\n" ), array( '\r', '\n' ), serialize( apply_filters( 'orto_filter_export_skin_options', $options, $skin ) ) ) . "'"
						. "\n\t)";
			}
			$output .= "\n);"
					. "\n?>";
		}
		orto_fpc( $importer->export_file_dir( 'skins.txt' ), $output );
	}
}

if ( ! function_exists( 'orto_skins_importer_export_fields' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_export_fields', 'orto_skins_importer_export_fields', 12, 1 );
	}
	/**
	 * Display exported skin options in the fields list
	 * 
	 * @hooked 'trx_addons_action_importer_export_fields'
	 * 
	 * @param object $importer  The importer object
	 */
	function orto_skins_importer_export_fields( $importer ) {
		$importer->show_exporter_fields(
			array(
				'slug'     => 'skins',
				'title'    => esc_html__( 'Skins', 'orto' ),
				'download' => 'skins-options.php',
			)
		);
	}
}

if ( ! function_exists( 'orto_skins_importer_set_archive_name' ) ) {
	add_action( 'after_setup_theme', 'orto_skins_importer_set_archive_name', 1 );
	/**
	 * Set a name for the archive with demo data for the one-click importer
	 * with the current skin name
	 * 
	 * @hooked 'after_setup_theme'
	 */
	function orto_skins_importer_set_archive_name() {
		$GLOBALS['ORTO_STORAGE']['theme_demofiles_archive_name'] = sprintf( 'demo/%s.zip', orto_skins_get_active_skin_name() );
	}
}


// Load file with current skin
//----------------------------------------------------------
$orto_skin_file = orto_skins_get_file_dir( 'skin.php' );
if ( '' != $orto_skin_file ) {
	require_once $orto_skin_file;
}
