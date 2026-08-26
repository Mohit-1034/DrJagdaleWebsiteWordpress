<?php
/**
 * Skin Setup
 *
 * @package ORTO
 * @since ORTO 1.76.0
 */


//--------------------------------------------
// SKIN DEFAULTS
//--------------------------------------------

// Return theme's (skin's) default value for the specified parameter
if ( ! function_exists( 'orto_theme_defaults' ) ) {
	function orto_theme_defaults( $name = '', $value = '' ) {
		$defaults = array(
			'page_width'          => 1290,
			'page_boxed_extra'    => 60,
			'page_fullwide_max'   => 1920,
			'page_fullwide_extra' => 60,
			'sidebar_width'       => 370,
			'sidebar_gap'         => 70,
			'grid_gap'            => 30,
		);
		if ( empty( $name ) ) {
			return $defaults;
		} else {
			if ( $value === '' && isset( $defaults[ $name ] ) ) {
				$value = $defaults[ $name ];
			}
			return $value;
		}
	}
}


// Add a custom skin-specific breakpoint for the key 'lg' of Elementor
if ( ! function_exists( 'orto_skin_add_custom_lg_breakpoint_for_elementor' ) ) {
	add_filter( 'orto_filter_elementor_new_lg_breakpoint', 'orto_skin_add_custom_lg_breakpoint_for_elementor' );
	function orto_skin_add_custom_lg_breakpoint_for_elementor( $bp ) {
		// To Do: return a new value for the breakpoint 'lg'
		// For example: $bp = 1280;
		// 				This value is set the 'lg' breakpoint to >= 1280
		//				and 'tablet' breakpoint to <= 1279
		return $bp;
	}
}


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup', 1 );
	function orto_skin_setup() {

		$GLOBALS['ORTO_STORAGE'] = array_merge( $GLOBALS['ORTO_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-ancora',

			'theme_doc_url'       => '//doc.themerex.net/orto/',

			'theme_demofiles_url' => '//demofiles.ancorathemes.com/orto/',

			'theme_rate_url'      => '//themeforest.net/downloads',

			'theme_custom_url'    => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themeinstall',

			'theme_support_url'   => '//themerex.net/support/',

			'theme_download_url'  => '//themeforest.net/user/ancorathemes/portfolio',        // Ancora

			'theme_video_url'     => '//www.youtube.com/channel/UCdIjRh7-lPVHqTTKpaf8PLA',   // Ancora

			'theme_privacy_url'   => '//ancorathemes.com/privacy-policy/',                   // Ancora

			'portfolio_url'       => '//themeforest.net/user/ancorathemes/portfolio',        // Ancora

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


// Add/remove/change Theme Settings
if ( ! function_exists( 'orto_skin_setup_settings' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_settings', 1 );
	function orto_skin_setup_settings() {
		// Example: enable (true) / disable (false) thumbs in the prev/next navigation
		orto_storage_set_array( 'settings', 'thumbs_in_navigation', false );
	}
}



//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_fonts', 1 );
	function orto_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be:
		// - Google fonts (specify name, family and styles)
		// - Adobe fonts (specify name, family and link URL)
		// - uploaded fonts (specify name, family), placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		$load_fonts = array(
			// Google font
			array(
				'name'   => 'Noto Sans',
				'family' => 'sans-serif',
				'link'   => '',
				'styles' => 'ital,wght@0,100..900;1,100..900',
			),
			array(
				'name'   => 'Tenor Sans',
				'family' => 'sans-serif',
				'link'   => '',
				'styles' => 'wght@400',
			),
		);
		orto_storage_set( 'load_fonts', $load_fonts );

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		orto_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		// Settings of the main tags.
		// Default value of 'font-family' may be specified as reference to the array $load_fonts (see above)
		// or as comma-separated string.
		// In the second case (if 'font-family' is specified manually as comma-separated string):
		//    1) Font name with spaces in the parameter 'font-family' will be enclosed in quotes and no spaces after comma!
		//    2) If font-family inherit a value from the 'Main text' - specify 'inherit' as a value
		// example:
		// Correct:   'font-family' => orto_get_load_fonts_family_string( $load_fonts[0] )
		// Correct:   'font-family' => 'inherit'
		// Correct:   'font-family' => 'Roboto,sans-serif'
		// Correct:   'font-family' => '"PT Serif",sans-serif'
		// Incorrect: 'font-family' => 'Roboto, sans-serif'      // A space after a comma is prohibited
		// Incorrect: 'font-family' => 'PT Serif,sans-serif'     // A font family with spaces must be enclosed with quotes

		$font_description = esc_html__( 'Please use only the following ortots: "rem" or "em".', 'orto' )
							. ( is_customize_preview() ? '<br>' . esc_html__( 'Press "Reload preview area" button at the top of this panel after the all font parameters are changed.', 'orto' ) : '' );

		orto_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'            => esc_html__( 'Main text', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'main text', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[0] ), //'Noto Sans,sans-serif',
					'font-size'        => '16px',     // Default value for desktop
					'font-size_laptop' => '',         // Default value for laptop
					'font-size_tablet' => '15px',     	  // Default value for tablet
					'font-size_mobile' => '',     // Default value for mobile
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.625em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
					'margin-top'       => '0em',
					'margin-bottom'    => '1.62em',
					'margin-bottom_tablet' => '1em',
				),
				'post'    => array(
					'title'            => esc_html__( 'Article text', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'article text', 'orto' ) ),
					'font-family'      => 'inherit',	// Example: orto_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'        => '',			// Example: '1.286rem',
					'font-weight'      => 'inherit',	// Example: '400',
					'font-style'       => 'inherit',	// Example: 'normal',
					'line-height'      => '',			// Example: '1.75em',
					'text-decoration'  => 'inherit',	// Example: 'none',
					'text-transform'   => 'inherit',	// Example: 'none',
					'letter-spacing'   => '',			// Example: '',
					'margin-top'       => '',			// Example: '0em',
					'margin-bottom'    => '',			// Example: '1.4em',
				),
				'h1'      => array(
					'title'            => esc_html__( 'Heading 1', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H1', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ), //'"Tenor Sans",sans-serif',
					'font-size'        => '57px',
					'font-size_laptop' => '',
					'font-size_tablet' => '45px',
					'font-size_mobile' => '36px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.1052em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.0211em',
					'margin-top'       => '1.12em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.27em',
				),
				'h2'      => array(
					'title'            => esc_html__( 'Heading 2', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H2', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '47px',
					'font-size_laptop' => '',
					'font-size_tablet' => '36px',
					'font-size_mobile' => '31px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.1276em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.0213em',
					'margin-top'       => '0.93em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.35em',
				),
				'h3'      => array(
					'title'            => esc_html__( 'Heading 3', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H3', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '35px',
					'font-size_laptop' => '',
					'font-size_tablet' => '28px',
					'font-size_mobile' => '26px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.1142em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.02em',
					'margin-top'       => '1.25em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.47em',
				),
				'h4'      => array(
					'title'            => esc_html__( 'Heading 4', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H4', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '28px',
					'font-size_laptop' => '',
					'font-size_tablet' => '22px',
					'font-size_mobile' => '',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.2143em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.02em',
					'margin-top'       => '1.57em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.45em',
				),
				'h5'      => array(
					'title'            => esc_html__( 'Heading 5', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H5', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '23px',
					'font-size_laptop' => '',
					'font-size_tablet' => '20px',
					'font-size_mobile' => '19px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.2174em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.02em',
					'margin-top'       => '1.95em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.53em',
				),
				'h6'      => array(
					'title'            => esc_html__( 'Heading 6', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'tag H6', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '19px',
					'font-size_laptop' => '',
					'font-size_tablet' => '18px',
					'font-size_mobile' => '17px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.2632em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '-0.02em',
					'margin-top'       => '2.3em',
					'margin-top_tablet' => '35px',
					'margin-top_mobile' => '22px',
					'margin-bottom'    => '0.5em',
				),
				'logo'    => array(
					'title'            => esc_html__( 'Logo text', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'text of the logo', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'        => '35px',
					'font-size_tablet' => '28px',
					'font-size_mobile' => '22px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.1142em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
				),
				'button'  => array(
					'title'            => esc_html__( 'Buttons', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'buttons', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'        => '16px',
					'font-size_laptop' => '',
					'font-size_tablet' => '',
					'font-size_mobile' => '15px',
					'font-weight'      => '500',
					'font-style'       => 'normal',
					'line-height'      => '19px',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
					'padding'          => '18px 40px',
					'padding_laptop'   => '',
					'padding_tablet'   => '16px 36px',
					'padding_mobile'   => '14px 32px',
					'border-radius'    => '30px',
					'border-width'     => '0px',
					'border-style'     => '',
					'border-color'     => '',
					'background-color' => '',
					'color'            => '',
					'border-color:hover' => '',
					'background-color:hover' => '',
					'color:hover'      => '',
				),
				'input'   => array(
					'title'            => esc_html__( 'Input fields', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'orto' ) ),
					'font-family'      => 'inherit',
					'font-size'        => '15px',
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.6em',     // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
					'padding'          => '13px 14px',
					'border-radius'    => '10px',
					'border-width'     => '1px',
					'border-style'     => 'solid',
					'border-color'     => '',
					'background-color' => '',
					'color'            => '',
					'border-color:focus' => '',
					'background-color:focus' => '',
					'color:focus'      => '',
				),
				'info'    => array(
					'title'            => esc_html__( 'Post meta', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'orto' ) ),
					'font-family'      => 'inherit',
					'font-size'        => '14px',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'      => '400',
					'font-style'       => 'normal',
					'line-height'      => '1.5em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
					'margin-top'       => '0.4em',
					'margin-bottom'    => '',
					'category-border-radius' => '0px',
				),
				'menu'    => array(
					'title'            => esc_html__( 'Main menu', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'main menu items', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'        => '16px',
					'font-weight'      => '500',
					'font-style'       => 'normal',
					'line-height'      => '1.5em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
				),
				'submenu' => array(
					'title'            => esc_html__( 'Dropdown menu', 'orto' ),
					'description'      => sprintf( $font_description, esc_html__( 'dropdown menu items', 'orto' ) ),
					'font-family'      => orto_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'        => '15px',
					'font-weight'      => '500',
					'font-style'       => 'normal',
					'line-height'      => '1.4em',
					'text-decoration'  => 'none',
					'text-transform'   => 'none',
					'letter-spacing'   => '0px',
					'border-radius'    => '0px',
				),
			)
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_schemes', 1 );
	function orto_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		orto_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'orto' ),
					'description' => esc_html__( 'General colors', 'orto' ),
				),
				'alt'   => array(
					'title'       => esc_html__( 'Alt', 'orto' ),
					'description' => esc_html__( 'Alternative block colors', 'orto' ),
				),
			)
		);

		orto_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background', 'orto' ),
					'description' => esc_html__( 'The background color of this block in the normal state', 'orto' ),
				),
				'bg_color_2'    => array(
					'title'       => esc_html__( 'Background 2', 'orto' ),
					'description' => esc_html__( 'The background color for contrasting blocks within the same group', 'orto' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border', 'orto' ),
					'description' => esc_html__( 'The border color of this block', 'orto' ),
				),
				'title'   => array(
					'title'       => esc_html__( 'Heading', 'orto' ),
					'description' => esc_html__( 'The color of primary text (titles, bold/strong, etc.) inside this block', 'orto' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'orto' ),
					'description' => esc_html__( 'The color of the plain text inside this block', 'orto' ),
				),
				'meta'  => array(
					'title'       => esc_html__( 'Text Meta', 'orto' ),
					'description' => esc_html__( 'The color of secondary text (post meta, post date, counters, categories, tags, etc.) inside this block', 'orto' ),
				),
				'link'   => array(
					'title'       => esc_html__( 'Accent', 'orto' ),
					'description' => esc_html__( 'The color of the links inside this block', 'orto' ),
				),
				'hover'  => array(
					'title'       => esc_html__( 'Hover', 'orto' ),
					'description' => esc_html__( 'The color of the hovered state of links inside this block', 'orto' ),
				),
			)
		);

		// Substitute colors for the function orto_get_scheme_color_name()
		orto_storage_set(
			'scheme_color_substitutes', array(
				'text_dark'      => 'title',
				'text_light'     => 'meta',
				'text_link'      => 'link',
				'text_hover'     => 'hover',

				'alter_bg_color' => 'bg_color_2',

				'extra_bg_color' => 'alt_bg_color',
				'extra_bg_hover' => 'alt_bg_color_2',
				'extra_bd_color' => 'alt_bd_color',
				'extra_dark'     => 'alt_title',
				'extra_text'     => 'alt_text',
				'extra_light'    => 'alt_meta',
				'extra_link'     => 'alt_link',
				'extra_hover'    => 'alt_hover'
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'orto' ),
				'internal' => true,
				'colors'   => array(

					// Main colors
					'bg_color'   => '#F8F8F7',
					'bg_color_2' => '#F6F2EE',
					'bd_color'   => '#E9E2DB',
					'title'      => '#3E4939',
					'text'       => '#7E847B',
					'meta'       => '#ABAFA9',
					'link'       => '#8BAE7E',
					'hover'      => '#7BA16E',

					// Alternative blocks (extra inverse)
					'alt_bg_color'   => '#161716',
					'alt_bg_color_2' => '#232423',
					'alt_bd_color'   => '#2D2B28',
					'alt_title'      => '#FFFEFE',
					'alt_text'       => '#B8BCC4',
					'alt_meta'       => '#A2A6AF',
					'alt_link'       => '#8BAE7E',
					'alt_hover'      => '#7BA16E',

					// Additional (skin-specific) colors.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alt_new_color1'   => '#rrggbb',
				),
			),
		);
		orto_storage_set( 'schemes', $schemes );
		orto_storage_set( 'schemes_original', $schemes );

		// Additional colors for scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		orto_storage_set( 'scheme_colors_add', array(
			'bg_color_0'        => array(
				'color' => 'bg_color',
				'alpha' => 0,
			),
			'bg_color_02'       => array(
				'color' => 'bg_color',
				'alpha' => 0.2,
			),
			'bg_color_05'       => array(
				'color' => 'bg_color',
				'alpha' => 0.5,
			),
			'bg_color_07'       => array(
				'color' => 'bg_color',
				'alpha' => 0.7,
			),
			'bg_color_09'       => array(
				'color' => 'bg_color',
				'alpha' => 0.9,
			),
			'bg_color_2_05'       => array(
				'color' => 'bg_color_2',
				'alpha' => 0.7,
			),			
			'alt_bg_color_05' => array(
				'color' => 'alt_bg_color',
				'alpha' => 0.5,
			),
			'alt_title_08' => array(
				'color' => 'alt_title',
				'alpha' => 0.8,
			),
			'link_07'          => array(
				'color' => 'link',
				'alpha' => 0.7,
			),
			'link_blend'       => array(
				'color'      => 'link',
				'hue'        => 2,
				'saturation' => -5,
				'brightness' => 5,
			),
		) );

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		orto_storage_set( 'schemes_simple', array() );

		// Parameters to set order of schemes in the css. Leave at least one scheme in the array!
		orto_storage_set(
			'schemes_sorted', array(
				'color_scheme',
			)
		);
	}
}

// Activation methods
if ( ! function_exists( 'orto_skin_filter_activation_methods2' ) ) {
    add_filter( 'trx_addons_filter_activation_methods', 'orto_skin_filter_activation_methods2', 11, 1 );
    function orto_skin_filter_activation_methods2( $args ) {
        $args['elements_key'] = true;
        return $args;
    }
}