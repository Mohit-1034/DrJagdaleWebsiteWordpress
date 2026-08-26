<?php
/**
 * The template to show mobile menu (used only header_style == 'default')
 *
 * @package ORTO
 * @since ORTO 1.0
 */
?>
<div class="menu_mobile_overlay"></div>
<div class="menu_mobile menu_mobile_fullscreen">
	<div class="menu_mobile_inner">
		<span class="menu_mobile_close theme_button_close" tabindex="0"><span class="theme_button_close_icon"></span></span>
		<?php
		// Mobile menu
		$orto_menu_mobile = orto_get_nav_menu( 'menu_mobile' );
		if ( empty( $orto_menu_mobile ) ) {
			$orto_menu_mobile = apply_filters( 'orto_filter_get_mobile_menu', '' );
			if ( empty( $orto_menu_mobile ) ) {
				$orto_menu_mobile = orto_get_nav_menu( 'menu_main' );
				if ( empty( $orto_menu_mobile ) ) {
					$orto_menu_mobile = orto_get_nav_menu();
				}
			}
		}
		if ( ! empty( $orto_menu_mobile ) ) {
			// Change attribute 'id' - add prefix 'mobile-' to prevent duplicate id on the page
			$orto_menu_mobile = preg_replace( '/([\s]*id=")/', '${1}mobile-', $orto_menu_mobile );
			// Change main menu classes
			$orto_menu_mobile = str_replace(
				array( 'menu_main',   'sc_layouts_menu_nav', 'sc_layouts_menu ' ),	// , 'sc_layouts_hide_on_mobile', 'hide_on_mobile'
				array( 'menu_mobile', '',                    ' ' ),					// , '',                          ''
				$orto_menu_mobile
			);
			// Wrap menu to the <nav> if not present
			if ( strpos( $orto_menu_mobile, '<nav ' ) !== 0 ) {	// condition !== false is not allowed, because menu can contain inner <nav> elements (in the submenu layouts)
				$orto_menu_mobile = orto_is_on( orto_get_theme_option( 'seo_snippets' ) )
					? sprintf( '<nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="%1$s//schema.org/SiteNavigationElement">%2$s</nav>', esc_attr( orto_get_protocol( true ) ), $orto_menu_mobile )
					: sprintf( '<nav class="menu_mobile_nav_area">%s</nav>', $orto_menu_mobile );
			}
			// Show menu
			orto_show_layout( apply_filters( 'orto_filter_menu_mobile_layout', $orto_menu_mobile ) );
		}
		?>
	</div>
</div>