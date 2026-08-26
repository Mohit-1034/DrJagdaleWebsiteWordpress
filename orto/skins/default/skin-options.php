<?php
/**
 * Skin Options
 *
 * @package ORTO
 * @since ORTO 1.76.0
 */


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc. or overriden values from the post/page meta)

if ( ! function_exists( 'orto_create_theme_options' ) ) {

	function orto_create_theme_options() {

		// Message about options override.
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = esc_html__( 'Attention! Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages. If you changed such parameter and nothing happened on the page, this option may be overridden in the corresponding section or in the Page Options of this page. These options are marked with an asterisk (*) in the title.', 'orto' );

		// Color schemes number: if < 2 - hide fields with selectors
		$hide_schemes = count( orto_storage_get( 'schemes' ) ) < 2;

		$trx_addons_present = function_exists( 'orto_exists_trx_addons' ) ? orto_exists_trx_addons() : defined( 'TRX_ADDONS_VERSION' );
		if ( $trx_addons_present && ! function_exists( 'orto_exists_trx_addons' ) ) {
			$trx_addons_plugin_path = orto_get_file_dir( 'plugins/trx_addons/trx_addons.php' );
			if ( ! empty( $trx_addons_plugin_path ) ) {
				require_once $trx_addons_plugin_path;
			}
			trx_addons_set_admin_message(
				esc_html__( 'The new skin version may not be fully compatible with your current theme version. Please update the theme or temporarily revert to the previous skin version.', 'orto' )
				. '<br><br>'
				. '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '" class="trx_addons_button trx_addons_button_small trx_addons_button_accent">'
					. esc_html__( 'Go to Dashboard - Updates', 'orto' )
				. '</a>'
				. '|'
				. esc_html__( 'Theme Update Required', 'orto' ),
				'error'
			);
		}

		orto_storage_set(

			'options', array(

				// 'Logo & Site Identity'
				//---------------------------------------------
				'title_tagline'                 => array(
					'title'    => esc_html__( 'Logo & Site Identity', 'orto' ),
					'desc'     => '',
					'priority' => 10,
					'icon'     => 'icon-home-2',
					'type'     => 'section',
				),
				'logo_info'                     => array(
					'title'    => esc_html__( 'Logo Settings', 'orto' ),
					'desc'     => '',
					'priority' => 20,
					'qsetup'   => esc_html__( 'General', 'orto' ),
					'type'     => 'info',
				),
				'logo_text'                     => array(
					'title'    => esc_html__( 'Use Site Name as Logo', 'orto' ),
					'desc'     => wp_kses_data( __( 'Use the site title and tagline as a text logo if no image is selected', 'orto' ) ),
					'priority' => 30,
					'std'      => 1,
					'qsetup'   => esc_html__( 'General', 'orto' ),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'switch',
				),
				'logo_zoom'                     => array(
					'title'      => esc_html__( 'Logo zoom', 'orto' ),
					'desc'       => wp_kses_data( __( 'Zoom the logo (set 1 to leave original size). For this parameter to affect images, their max-height should be specified in "em" instead of "px" during header creation. In this case, maximum logo size depends on the actual size of the picture.', 'orto' ) ),
					'std'        => 1,
					'min'        => 0.2,
					'max'        => 2,
					'step'       => 0.1,
					'refresh'    => false,
					'show_value' => true,
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'slider',
				),
				'logo_retina_enabled'           => array(
					'title'    => esc_html__( 'Allow retina display logo', 'orto' ),
					'desc'     => wp_kses_data( __( 'Show fields to select logo images for Retina display', 'orto' ) ),
					'priority' => 40,
					'refresh'  => false,
					'std'      => 0,
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'switch',
				),
				// Parameter 'logo' was replaced with standard WordPress 'custom_logo'
				'logo_retina'                   => array(
					'title'      => esc_html__( 'Logo for Retina', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'orto' ) ),
					'priority'   => 70,
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'image',
				),
				'logo_secondary'                   => array(
					'title' => esc_html__( 'Secondary Logo', 'orto' ),
					'desc'  => wp_kses_data( __( 'Select or upload a secondary logo, which is used primarily for dark backgrounds', 'orto' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_secondary_retina'            => array(
					'title'      => esc_html__( 'Secondary Logo on Retina', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select or upload a secondary logo for retina displays. If empty, the logo from the field above will be used', 'orto' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'image',
				),


				// 'General settings'
				//---------------------------------------------
				'general'                       => array(
					'title'    => esc_html__( 'General', 'orto' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 20,
					'icon'     => 'icon-settings',
					'demo'     => true,
					'type'     => 'section',
				),
				'general_layout_info'           => array(
					'title'  => esc_html__( 'Layout', 'orto' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'orto' ),
					'demo'   => true,
					'type'   => 'info',
				),
				'body_style'                    => array(
					'title'    => esc_html__( 'Body style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'orto' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'refresh'  => false,
					'std'      => 'wide',
					'options'  => orto_get_list_body_styles( false, true ),
					'qsetup'   => esc_html__( 'General', 'orto' ),
					'demo'     => true,
					'type'     => 'choice',
				),
				'page_width'                    => array(
					'title'      => esc_html__( 'Page width', 'orto' ),
					'desc'       => wp_kses_data( __( 'Total width of the site content and sidebar (in pixels). If empty - use default width', 'orto' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed', 'wide' ),
					),
					'std'        => orto_theme_defaults( 'page_width' ),
					'min'        => 1000,
					'max'        => 1600,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_width',          // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ORTO_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'page_boxed_extra'             => array(
					'title'      => esc_html__( 'Boxed page extra spaces', 'orto' ),
					'desc'       => wp_kses_data( __( 'Width of the extra side space on boxed pages', 'orto' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'std'        => orto_theme_defaults( 'page_boxed_extra' ),
					'min'        => 0,
					'max'        => 150,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_boxed_extra',   // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ORTO_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'boxed_bg_image'                => array(
					'title'      => esc_html__( 'Boxed bg image', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select or upload image for the background of the boxed content', 'orto' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'std'        => '',
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'type'       => 'image',
				),
				'remove_margins'                => array(
					'title'    => esc_html__( 'Page margins', 'orto' ),
					'desc'     => wp_kses_data( __( 'Add margins above and below the content area', 'orto' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'refresh'  => false,
					'std'      => 0,
					'options'  => orto_get_list_remove_margins(),
					'type'     => 'choice',
				),

				'general_sidebar_info'          => array(
					'title' => esc_html__( 'Sidebar', 'orto' ),
					'desc'  => '',
					'demo'  => true,
					'type'  => 'info',
				),
				'sidebar_position'              => array(
					'title'    => esc_html__( 'Sidebar position', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar', 'orto' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'qsetup'   => esc_html__( 'General', 'orto' ),
					'demo'      => true,
					'type'     => 'choice',
				),
				'sidebar_type'              => array(
					'title'    => esc_html__( 'Sidebar style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style'                 => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets'               => array(
					'title'      => esc_html__( 'Sidebar widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_widgets_single'
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type'     => array( 'default')
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'type'       => 'select',
				),
				'sidebar_width'                 => array(
					'title'      => esc_html__( 'Sidebar width', 'orto' ),
					'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'orto' ) ),
					'std'        => orto_theme_defaults( 'sidebar_width' ),
					'min'        => 150,
					'max'        => 500,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_width', // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ORTO_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'sidebar_gap'                   => array(
					'title'      => esc_html__( 'Sidebar gap', 'orto' ),
					'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'orto' ) ),
					'std'        => orto_theme_defaults( 'sidebar_gap' ),
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_gap',  // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ORTO_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'sidebar_proportional'          => array(
					'title'      => esc_html__( 'Sidebar proportional', 'orto' ),
					'desc'       => wp_kses_data( __( 'Change the width of the sidebar and gap proportionally when the window is resized, or leave the width of the sidebar constant', 'orto' ) ),
					'refresh'    => false,
					'customizer' => 'sidebar_proportional',  // SASS variable's name to preview changes 'on fly'
					'std'        => 1,
					'type'       => 'switch',
				),
				'expand_content'                => array(
					'title'    => esc_html__( 'Content width', 'orto' ),
					'desc'     => wp_kses_data( __( 'Content width if the sidebar is hidden', 'orto' ) ),
					'refresh'  => false,
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'expand_content_single'
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'options'  => orto_get_list_expand_content(),
					'std'      => 'expand',
					'type'     => 'choice',
				),

				'general_misc_info'             => array(
					'title' => esc_html__( 'Miscellaneous', 'orto' ),
					'desc'  => '',
					'pro_only'  => ORTO_THEME_FREE,
					'type'  => 'info',
				),
				'seo_snippets'                  => array(
					'title' => esc_html__( 'SEO snippets', 'orto' ),
					'desc'  => wp_kses_data( __( 'Add structured data markup to the single posts and pages', 'orto' ) ),
					'std'   => 0,
					'pro_only'  => ORTO_THEME_FREE,
					'type'  => 'switch',
				),
				'privacy_text' => array(
					"title" => esc_html__("Text with Privacy Policy link", 'orto'),
					"desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'orto') ),
					"std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'orto'), 'orto_kses_content' ),
					"type"  => "textarea"
				),



				// 'Header'
				//---------------------------------------------
				'header'                        => array(
					'title'    => esc_html__( 'Header', 'orto' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 30,
					'icon'     => 'icon-header',
					'type'     => 'section',
				),

				'header_style_info'             => array(
					'title' => esc_html__( 'Header style', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type'                   => array(
					'title'    => esc_html__( 'Header style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'orto' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'header_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'orto' ),
					),
					'dependency' => array(
						'header_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'select',
				),
				'header_position'               => array(
					'title'    => esc_html__( 'Header position', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select site header position', 'orto' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'orto' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),



				// 'Footer'
				//---------------------------------------------
				'footer'                        => array(
					'title'    => esc_html__( 'Footer', 'orto' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 50,
					'icon'     => 'icon-footer',
					'type'     => 'section',
				),
				'footer_type'                   => array(
					'title'    => esc_html__( 'Footer style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'orto' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'footer_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'orto' ),
					),
					'dependency' => array(
						'footer_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets'                => array(
					'title'      => esc_html__( 'Footer widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'orto' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns'                => array(
					'title'      => esc_html__( 'Footer columns', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'orto' ),
					),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'footer_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => orto_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				'copyright'                     => array(
					'title'      => esc_html__( 'Copyright', 'orto' ),
					'desc'       => wp_kses_data( __( 'Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'orto' ) ),
					'translate'  => true,
					'std'        => esc_html__( 'Copyright &copy; {Y}. All rights reserved.', 'orto' ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'refresh'    => false,
					'type'       => 'textarea',
				),



				// 'Blog'
				//---------------------------------------------
				'blog'                          => array(
					'title'    => esc_html__( 'Blog', 'orto' ),
					'desc'     => wp_kses_data( __( 'Options of the the blog archive', 'orto' ) ),
					'priority' => 70,
					'icon'     => 'icon-blog',
					'type'     => 'panel',
				),


				// Blog - Posts page
				//---------------------------------------------
				'blog_general'                  => array(
					'title' => esc_html__( 'Posts page', 'orto' ),
					'desc'  => wp_kses_data( __( 'Style and components of the blog archive', 'orto' ) ),
					'icon'  => 'icon-posts-page',
					'type'  => 'section',
				),
				'blog_general_info'             => array(
					'title'  => esc_html__( 'Posts page settings', 'orto' ),
					'desc'   => wp_kses_data( __( 'Customize the blog archive: post layout, header style, sidebar style and position, etc.', 'orto' ) ),
					'qsetup' => esc_html__( 'General', 'orto' ),
					'type'   => 'info',
				),
				'body_style_blog'               => array(
					'title'    => esc_html__( 'Body style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content on the blog archive pages', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_body_styles( true, true ),
					'type'     => 'choice',
				),
				'blog_style'                    => array(
					'title'      => esc_html__( 'Blog style', 'orto' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						// New format: '@editor/property-name'
						'@editor/template' => array( 'blog.php' ),
					),
					'std'        => 'classic_1',
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'excerpt_length'                => array(
					'title'      => esc_html__( 'Excerpt length', 'orto' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'std'        => 25,
					'type'       => 'text',
				),
				'blog_columns'                  => array(
					'title'   => esc_html__( 'Blog columns', 'orto' ),
					'desc'    => wp_kses_data( __( 'How many columns should be used in the blog archive (from 1 to 3)?', 'orto' ) ),
					'std'     => 1,
					'options' => orto_get_list_range( 1, 3 ),
					'type'    => 'hidden',      // This options is available and must be overriden only for some modes (for example, 'shop')
				),
				'post_type'                     => array(
					'title'      => esc_html__( 'Post type', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select post type to show in the blog archive', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'linked'     => 'parent_cat',
					'refresh'    => false,
					'hidden'     => true,
					'std'        => 'post',
					'options'    => array(),
					'type'       => 'select',
				),
				'parent_cat'                    => array(
					'title'      => esc_html__( 'Category to show', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select category to show in the blog archive', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'refresh'    => false,
					'hidden'     => true,
					'std'        => '0',
					'options'    => array(),
					'type'       => 'select',
				),
				'posts_per_page'                => array(
					'title'      => esc_html__( 'Posts per page', 'orto' ),
					'desc'       => wp_kses_data( __( 'How many posts will be displayed on this page', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => '',
					'type'       => 'text',
				),
				'blog_pagination'               => array(
					'title'      => esc_html__( 'Pagination style', 'orto' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'std'        => 'pages',
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'options'    => orto_get_list_blog_paginations(),
					'type'       => 'choice',
				),
				'blog_pagination_border_radius'                => array(
					'title'      => esc_html__( 'Pagination Border Radius', 'orto' ),
					'std'        => '0px',
					'std_laptop' => '',
					'std_tablet' => '',
					'std_mobile' => '',
					'responsive' => true,
					'css'        => 'blog-pagination-border-radius',
					'dependency' => array(
						'blog_pagination' => array( 'pages' ),
					),
					'type'       => 'text',
				),
				'blog_animation'                => array(
					'title'      => esc_html__( 'Post animation', 'orto' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'select',
				),
				'disable_animation_on_mobile'   => array(
					'title'      => esc_html__( 'Disable animation on mobile', 'orto' ),
					'desc'       => wp_kses_data( __( 'Disable any posts animation on mobile devices', 'orto' ) ),
					'std'        => 0,
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'switch',
				),
				'blog_header_info'              => array(
					'title' => esc_html__( 'Header', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_blog'              => array(
					'title'    => esc_html__( 'Header style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_header_footer_types( true ),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_blog'             => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						'header_type_blog' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_blog'          => array(
					'title'    => esc_html__( 'Header position', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),

				'blog_sidebar_info'             => array(
					'title' => esc_html__( 'Sidebar', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_blog'         => array(
					'title'   => esc_html__( 'Sidebar position', 'orto' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'orto' ) ),
					'std'     => 'right',
					'options' => array(),
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'type'    => 'choice',
				),
				'sidebar_type_blog'           => array(
					'title'    => esc_html__( 'Sidebar style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style_blog'            => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_blog'          => array(
					'title'      => esc_html__( 'Sidebar widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'orto' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'type'       => 'select',
				),
				'expand_content_blog'           => array(
					'title'   => esc_html__( 'Content width', 'orto' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'orto' ) ),
					'refresh' => false,
					'std'     => 'expand',
					'options' => orto_get_list_expand_content( true ),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'choice',
				),

				'blog_advanced_info'            => array(
					'title' => esc_html__( 'Advanced settings', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'no_image'                      => array(
					'title' => esc_html__( 'Image placeholder', 'orto' ),
					'desc'  => wp_kses_data( __( "Select or upload a placeholder image for posts without a featured image. Placeholder is used exclusively on the blog stream page (and not on single post pages), and only in those styles, where omitting a featured image would be inappropriate.", 'orto' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'meta_parts'                    => array(
					'title'      => esc_html__( 'Post meta', 'orto' ),
					'desc'       => wp_kses_data( __( "If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'orto' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'orto' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=0|likes=0|comments=1|author=0|share=0|edit=0',
					'options'    => orto_get_list_meta_parts(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'checklist',
				),
				'time_diff_before'              => array(
					'title' => esc_html__( 'Easy readable date format', 'orto' ),
					'desc'  => wp_kses_data( __( "For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'orto' ) ),
					'std'   => 5,
					'type'  => 'text',
				),
				'use_blog_archive_pages'        => array(
					'title'      => esc_html__( 'Use "Blog Archive" page settings on the post list', 'orto' ),
					'desc'       => wp_kses_data( __( 'Apply options and content of pages created with the template "Blog Archive" for some type of posts and / or taxonomy when viewing feeds of posts of this type and taxonomy.', 'orto' ) ),
					'std'        => 0,
					'type'       => 'switch',
				),
				'global_border_radius'   => array(
					'title'      => esc_html__( 'Global Border Radius', 'orto' ),
					'desc'       => wp_kses_data( __( "Applies a border radius to images in the blog feed, the featured image of single posts, and other elements such as the social sharing bar, quotations, and the author box", 'orto' ) ),
					'std'        => '0px',
					'std_laptop' => '',
					'std_tablet' => '',
					'std_mobile' => '',
					'responsive' => true,
					'css'        => 'global-border-radius',
					'type'       => 'text',
				),
				'global_border_radius_small'   => array(
					'title'      => esc_html__( 'Global Border Radius - Small', 'orto' ),
					'desc'       => wp_kses_data( __( "Applies a border radius to elements smaller in size, such as post tags, drop caps, form notifications, post slider navigation, etc", 'orto' ) ),
					'std'        => '0px',
					'std_laptop' => '',
					'std_tablet' => '',
					'std_mobile' => '',
					'responsive' => true,
					'css'        => 'global-border-radius-small',
					'type'       => 'text',
				),


				// Blog - Single posts
				//---------------------------------------------
				'blog_single'                   => array(
					'title' => esc_html__( 'Single posts', 'orto' ),
					'desc'  => wp_kses_data( __( 'Settings of the single post', 'orto' ) ),
					'icon'  => 'icon-single-post',
					'type'  => 'section',
				),

				'blog_single_info'       => array(
					'title' => esc_html__( 'Single posts', 'orto' ),
					'desc'   => wp_kses_data( __( 'Customize the single post: content  layout, header and footer styles, sidebar position, meta elements, etc.', 'orto' ) ),
					'type'  => 'info',
				),

				'blog_single_body_info'  => array(
					'title' => esc_html__( 'Body', 'orto' ),
					'desc'   => '',
					'type'  => 'info',
				),
				'body_style_single'               => array(
					'title'    => esc_html__( 'Body style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content on the single posts', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_body_styles( true, true ),
					'type'     => 'choice',
				),

				'blog_single_header_info'       => array(
					'title' => esc_html__( 'Header', 'orto' ),
					'desc'   => '',
					'type'  => 'info',
				),
				'header_type_single'            => array(
					'title'    => esc_html__( 'Header style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_header_footer_types( true ),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_single'           => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						'header_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_single'        => array(
					'title'    => esc_html__( 'Header position', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),

				'blog_single_sidebar_info'      => array(
					'title' => esc_html__( 'Sidebar', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_single'       => array(
					'title'   => esc_html__( 'Sidebar position', 'orto' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar on the single posts', 'orto' ) ),
					'std'     => 'hide',
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'options' => array(),
					'type'    => 'choice',
				),
				'sidebar_type_single'           => array(
					'title'    => esc_html__( 'Sidebar style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style_single'            => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_single'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on the single posts', 'orto' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_single'         => array(
					'title'   => esc_html__( 'Content width', 'orto' ),
					'desc'    => wp_kses_data( __( 'Content width on the single posts if the sidebar is hidden. Attention! "Narrow" width is only available for posts. For all other post types (Team, Services, etc.), it is equivalent to "Normal"', 'orto' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'refresh' => false,
					'std'     => 'normal',
					'options' => orto_get_list_expand_content( true, true ),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'choice',
				),

				'blog_single_title_info'        => array(
					'title' => esc_html__( 'Featured image and title', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'single_style'                  => array(
					'title'      => esc_html__( 'Single style', 'orto' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'std'        => 'style-1',
					'qsetup'     => esc_html__( 'General', 'orto' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'show_post_meta'                => array(
					'title' => esc_html__( 'Show post meta', 'orto' ),
					'desc'  => wp_kses_data( __( "Display block with post's meta: date, categories, counters, etc.", 'orto' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'meta_parts_single'             => array(
					'title'      => esc_html__( 'Post meta', 'orto' ),
					'desc'       => wp_kses_data( __( 'Meta parts for single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'orto' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'orto' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'author=1|categories=1|date=1|modified=0|views=0|likes=1|share=1|comments=1|edit=0',
					'options'    => orto_get_list_meta_parts(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'checklist',
				),
				'social_links_border_radius'    => array(
					'title'      => esc_html__( 'Social Links Border Radius', 'orto' ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'std'        => '50%',
					'std_laptop' => '',
					'std_tablet' => '',
					'std_mobile' => '',
					'responsive' => true,
					'css'        => 'social-links-border-radius',
					'type'       => 'text',
				),
				'show_author_info'              => array(
					'title' => esc_html__( 'Show author info', 'orto' ),
					'desc'  => wp_kses_data( __( "Display block with information about post's author", 'orto' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'profile_image_border_radius'   => array(
					'title'      => esc_html__( 'Profile Image Border Radius', 'orto' ),
					'desc'       => wp_kses_data( __( "Adjusts the border radius for author and commenter avatars", 'orto' ) ),
					'std'        => '50%',
					'std_laptop' => '',
					'std_tablet' => '',
					'std_mobile' => '',
					'responsive' => true,
					'css'        => 'profile-image-border-radius',
					'type'       => 'text',
				),

				'blog_single_related_info'      => array(
					'title' => esc_html__( 'Related posts', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'show_related_posts'            => array(
					'title'    => esc_html__( 'Show related posts', 'orto' ),
					'desc'     => wp_kses_data( __( "Show 'Related posts' section on single post pages", 'orto' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'std'      => 1,
					'type'     => 'switch',
				),
				'related_posts'                 => array(
					'title'      => esc_html__( 'Related posts', 'orto' ),
					'desc'       => wp_kses_data( __( 'How many related posts should be displayed in the single post?', 'orto' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 9,
					'show_value' => true,
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'slider',
				),
				'related_columns'               => array(
					'title'      => esc_html__( 'Related columns', 'orto' ),
					'desc'       => wp_kses_data( __( 'How many columns should be used to output related posts on the single post page?', 'orto' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'orto' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 3,
					'show_value' => true,
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'slider',
				),

				'posts_navigation_info'      => array(
					'title' => esc_html__( 'Post navigation', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'posts_navigation'           => array(
					'title'   => esc_html__( 'Show post navigation', 'orto' ),
					'desc'    => wp_kses_data( __( "Display post navigation on single post pages or load the next post automatically after the content of the current article.", 'orto' ) ),
					'std'     => 'links',
					'options' => array(
						'none'   => esc_html__('None', 'orto'),
						'links'  => esc_html__('Prev/Next links', 'orto'),
					),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'radio',
				),


				// 404 page
				//---------------------------------------------
				'page_404_section' => array(
					'title' => esc_html__( 'Page 404', 'orto' ),
					'desc'  => wp_kses_data( __( 'Settings of the page 404', 'orto' ) ),
					'icon'  => 'icon-padlock',
					'type'  => 'section',
				),

				'page_404_info'    => array(
					'title' => esc_html__( 'Page 404', 'orto' ),
					'desc'   => wp_kses_data( __( 'Customize the page 404.', 'orto' ) ),
					'type'  => 'info',
				),
				'redirect_404_page' => array(
					"title" => esc_html__('Page 404', 'orto'),
					"desc" => wp_kses_data( __("Select a page to redirect to in case of a 404 error (requested URL not found). If no page is selected - the default page of your theme will be used.", 'orto') ),
					"std" => "none",
					"options" => array(),
					"type" => "select"
				),

				'header_type_404'  => array(
					'title'    => esc_html__( 'Header style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_header_footer_types( true ),
					'type'     => 'radio',
					'dependency' => array(
						'redirect_404_page' => array( 'none' ),
					),
				),
				'header_style_404' => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						'redirect_404_page' => array( 'none' ),
						'header_type_404' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),


				'blog_end'                      => array(
					'type' => 'panel_end',
				),



				// 'Colors'
				//---------------------------------------------
				'panel_colors'                  => array(
					'title'    => esc_html__( 'Colors', 'orto' ),
					'desc'     => '',
					'priority' => 300,
					'icon'     => 'icon-customizer',
					'demo'     => true,
					'type'     => 'section',
				),

				'color_scheme_editor_info'      => array(
					'title' => esc_html__( 'Color scheme editor', 'orto' ),
					'desc'  => wp_kses_data( __( 'Customize the colors for your site. Warning. When creating pages in Elementor, you can find these colors in Global Colors. When you use them on pages, you will be able to automatically change the desired colors throughout the site when you edit the color scheme.', 'orto' ) ),
					'demo'  => true,
					'type'  => 'info',
				),
				'scheme_storage'                => array(
					'title'       => '',
					'desc'        => '',
					'std'         => '$orto_get_scheme_storage',
					'refresh'     => false,
					'colorpicker' => 'spectrum',
					'alpha'	      => apply_filters( 'orto_filter_colorpicker_allow_alpha', false, 'color_scheme' ),
					'demo'        => true,
					'type'        => 'scheme_editor',
				),

				'color_schemes_info'            => array(
					'title'  => esc_html__( 'Color scheme assignment', 'orto' ),
					'desc'   => wp_kses_data( __( 'Color schemes for various parts of the site. "Inherit" means that this block uses the main color scheme from the first parameter - Site Color Scheme.', 'orto' ) ),
					'hidden' => $hide_schemes,
					'demo'   => true,
					'type'   => 'info',
				),
				'color_scheme'                  => array(
					'title'    => esc_html__( 'Site Color Scheme', 'orto' ),
					'desc'     => '',
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),

				// Internal options.
				// Attention! Don't change any options in the section below!
				// Huge priority is used to call render this elements after all options!
				'reset_options'                 => array(
					'title'    => '',
					'desc'     => '',
					'std'      => '0',
					'priority' => 10000,
					'type'     => 'hidden',
				),

				'last_option'                   => array(     // Need to manually call action to include Tiny MCE scripts
					'title' => '',
					'desc'  => '',
					'std'   => 1,
					'demo'  => true,
					'type'  => 'hidden',
				),

			)
		);


		// Add parameters for "Category", "Tag", "Author", "Search" to Theme Options
		orto_storage_set_array_before( 'options', 'blog_single', orto_options_get_list_blog_options( 'category', esc_html__( 'Category', 'orto' ), 'icon-category' ) );
		orto_storage_set_array_before( 'options', 'blog_single', orto_options_get_list_blog_options( 'tag', esc_html__( 'Tag', 'orto' ), 'icon-tag-1' ) );
		orto_storage_set_array_before( 'options', 'blog_single', orto_options_get_list_blog_options( 'author', esc_html__( 'Author', 'orto' ), 'icon-resume' ) );
		orto_storage_set_array_before( 'options', 'blog_single', orto_options_get_list_blog_options( 'search', esc_html__( 'Search', 'orto' ), 'icon-search-1' ) );


		// Prepare panel 'Fonts'
		// -------------------------------------------------------------
		$fonts = array(

			// 'Fonts'
			//---------------------------------------------
			'fonts'             => array(
				'title'    => esc_html__( 'Typography', 'orto' ),
				'desc'     => '',
				'priority' => 200,
				'icon'     => 'icon-font',
				'demo'     => true,
				'type'     => 'panel',
			),

			// Fonts - Load_fonts
			'load_fonts_font_section' => array(
				'title' => esc_html__( 'Load fonts', 'orto' ),
				'desc'  => wp_kses_data( __( 'Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'orto' ) ),
				'demo'  => true,
				'type'  => 'section',
			),
			'load_fonts_info'   => array(
				'title' => esc_html__( 'Load fonts', 'orto' ),
				'desc'  => is_customize_preview() ? wp_kses_data( __( 'Press "Reload preview area" button at the top of this panel after the all font parameters are changed.', 'orto' ) ) : '',
				'demo'  => true,
				'type'  => 'info',
			),
			'load_fonts_subset' => array(
				'title'   => esc_html__( 'Google fonts subsets', 'orto' ),
				'desc'    => wp_kses_data( __( 'Specify a comma separated list of subsets to be loaded from Google fonts.', 'orto' ) )
						. wp_kses_data( __( 'Permitted subsets include: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'orto' ) ),
				'class'   => 'orto_column-1_4 orto_new_row',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$orto_get_load_fonts_subset',
				'type'    => 'text',
			),
		);

		for ( $i = 1; $i <= orto_get_theme_setting( 'max_load_fonts' ); $i++ ) {
			if ( orto_get_value_gp( 'page' ) != 'theme_options' ) {
				$fonts[ "load_fonts-{$i}-info" ] = array(
					// Translators: Add font's number - 'Font 1', 'Font 2', etc
					'title' => esc_html( sprintf( __( 'Font %s', 'orto' ), $i ) ),
					'desc'  => '',
					'demo'  => true,
					'type'  => 'info',
				);
			}
			$fonts[ "load_fonts-{$i}-name" ]   = array(
				'title'   => esc_html__( 'Font name', 'orto' ),
				'desc'    => '',
				'class'   => 'orto_column-1_4 orto_new_row',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$orto_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-family" ] = array(
				'title'   => esc_html__( 'Fallback fonts', 'orto' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'A comma-separated list of fallback fonts. Used if the font specified in the previous field is not available. Last in the list, specify the name of the font family: serif, sans-serif, monospace, cursive.', 'orto' ) )
								. '<br>'
								. wp_kses_data( __( 'For example: Arial, Helvetica, sans-serif', 'orto' ) )
							: '',
				'class'   => 'orto_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$orto_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-link" ] = array(
				'title'   => esc_html__( 'Font URL', 'orto' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font URL used only for Adobe fonts. This is URL of the stylesheet for the project with a fonts collection from the site adobe.com', 'orto' ) )
							: '',
				'class'   => 'orto_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$orto_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-styles" ] = array(
				'title'   => esc_html__( 'Font styles', 'orto' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font styles used only for Google fonts. This is a list of the font weight and style options for Google fonts CSS API v2.', 'orto' ) )
								. '<br>'
								. wp_kses_data( __( 'For example, to load normal, normal italic, bold and bold italic fonts, please specify: ital,wght@0:400;0,700;1,400;1,700', 'orto' ) )
								. '<br>'
								. wp_kses_data( __( 'Attention! Each weight and style option increases download size! Specify only those weight and style options that you plan on using.', 'orto' ) )
							: '',
				'class'   => 'orto_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$orto_get_load_fonts_option',
				'type'    => 'text',
			);
		}
		$fonts['load_fonts_end'] = array(
			'demo' => true,
			'type' => 'section_end',
		);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = orto_get_theme_fonts();
		foreach ( $theme_fonts as $tag => $v ) {
			$fonts[ "{$tag}_font_section" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'orto' ), $tag ) ),
/*
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								// Translators: Add tag's name to make description
								: wp_kses_data( sprintf( __( 'Font settings for the "%s" tag.', 'orto' ), $tag ) ),
*/
				'demo'  => true,
				'type'  => 'section',
			);
			$fonts[ "{$tag}_font_info" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'orto' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								: '',
				'demo'  => true,
				'type'  => 'info',
			);
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				// Skip responsive values
				if ( strpos( $css_prop, '_' ) !== false ) {
					continue;
				}
				// Skip property 'text-decoration' for the main text
				if ( 'text-decoration' == $css_prop && 'p' == $tag ) {
					continue;
				}

				$options    = '';
				$type       = 'text';
				$load_order = 1;
				$title      = ucfirst( str_replace( '-', ' ', $css_prop ) );
				if ( 'font-family' == $css_prop ) {
					$type       = 'select';
					$options    = array();
					$load_order = 2;        // Load this option's value after all options are loaded (use option 'load_fonts' to build fonts list)
				} elseif ( 'font-weight' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'orto' ),
						'100'     => esc_html__( '100 (Thin)', 'orto' ),
						'200'     => esc_html__( '200 (Extra-Light)', 'orto' ),
						'300'     => esc_html__( '300 (Light)', 'orto' ),
						'400'     => esc_html__( '400 (Regular)', 'orto' ),
						'500'     => esc_html__( '500 (Medium)', 'orto' ),
						'600'     => esc_html__( '600 (Semi-bold)', 'orto' ),
						'700'     => esc_html__( '700 (Bold)', 'orto' ),
						'800'     => esc_html__( '800 (Extra-bold)', 'orto' ),
						'900'     => esc_html__( '900 (Black)', 'orto' ),
					);
				} elseif ( 'font-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'orto' ),
						'normal'  => esc_html__( 'Normal', 'orto' ),
						'italic'  => esc_html__( 'Italic', 'orto' ),
						'oblique' => esc_html__( 'Oblique', 'orto' ),
					);
				} elseif ( 'text-decoration' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'      => esc_html__( 'Inherit', 'orto' ),
						'none'         => esc_html__( 'None', 'orto' ),
						'underline'    => esc_html__( 'Underline', 'orto' ),
						'overline'     => esc_html__( 'Overline', 'orto' ),
						'line-through' => esc_html__( 'Line-through', 'orto' ),
					);
				} elseif ( 'text-transform' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'    => esc_html__( 'Inherit', 'orto' ),
						'none'       => esc_html__( 'None', 'orto' ),
						'uppercase'  => esc_html__( 'Uppercase', 'orto' ),
						'lowercase'  => esc_html__( 'Lowercase', 'orto' ),
						'capitalize' => esc_html__( 'Capitalize', 'orto' ),
					);
				} elseif ( 'border-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'orto' ),
						'none'    => esc_html__( 'None', 'orto' ),
						'solid'   => esc_html__( 'Solid', 'orto' ),
						'double'  => esc_html__( 'Double', 'orto' ),
						'dotted'  => esc_html__( 'Dotted', 'orto' ),
						'dashed'  => esc_html__( 'Dashed', 'orto' ),
						'groove'  => esc_html__( 'Groove', 'orto' ),
						'ridge'   => esc_html__( 'Ridge', 'orto' ),
						'inset'   => esc_html__( 'Inset', 'orto' ),
						'outset'  => esc_html__( 'Outset', 'orto' ),
					);
				} elseif ( strpos( $css_prop, 'color') !== false ) {
					$type = 'color';
				}
				$fonts[ "{$tag}_{$css_prop}" ] = array(
					'title'      => $title,
					'desc'       => '',
					'refresh'    => false,
					'demo'       => true,
					'compact'    => true,
					'load_order' => $load_order,
					'std'        => '$orto_get_theme_fonts_option',
					'type'       => $type,
				);
				if ( is_array( $options ) ) {
					$fonts[ "{$tag}_{$css_prop}" ]['options'] = $options;
				}
				if ( $type == 'text' ) {
					$fonts[ "{$tag}_{$css_prop}" ]['responsive'] = true;
				}
				if ( $type == 'color' ) {
					$fonts[ "{$tag}_{$css_prop}" ]['colorpicker'] = apply_filters( 'orto_filter_colorpicker_type', 'wp' );	// wp | spectrum
					$fonts[ "{$tag}_{$css_prop}" ]['alpha'] = apply_filters( 'orto_filter_colorpicker_allow_alpha', false, 'typography' );
					$fonts[ "{$tag}_{$css_prop}" ]['globals'] = apply_filters( 'orto_filter_colorpicker_allow_globals', false, 'typography' );
				}
			}

			$fonts[ "{$tag}_section_end" ] = array(
				'demo' => true,
				'type' => 'section_end',
			);
		}

		$fonts['fonts_end'] = array(
			'demo' => true,
			'type' => 'panel_end',
		);

		// Add fonts parameters to Theme Options
		orto_storage_set_array_before( 'options', 'panel_colors', $fonts );

		// Add option 'logo' if WP version < 4.5
		// or 'custom_logo' if current page is not 'Customize'
		// ------------------------------------------------------
		if ( ! function_exists( 'the_custom_logo' ) || ! orto_check_url( 'customize.php' ) ) {
			orto_storage_set_array_before(
				'options', 'logo_retina', function_exists( 'the_custom_logo' ) ? 'custom_logo' : 'logo', array(
					'title'    => esc_html__( 'Logo', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select or upload the site logo', 'orto' ) ),
					'priority' => 60,
					'std'      => '',
					'qsetup'   => esc_html__( 'General', 'orto' ),
					'type'     => 'image',
				)
			);
		}

	}
}


// Common parameters for some blog modes: categories, tags, archives, author posts, search, etc.
//------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'orto_options_get_list_blog_options' ) ) {
	function orto_options_get_list_blog_options( $mode, $title = '', $icon = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $mode );
		}
		return apply_filters( 'orto_filter_get_list_blog_options', array(
				"blog_general_{$mode}"           => array(
					'title' => $title,
					// Translators: Add mode name to the description
					'desc'  => wp_kses_data( sprintf( __( "Style and components of the %s posts page", 'orto' ), $title ) ),
					'icon'  => $icon,
					'type'  => 'section',
				),
				"blog_general_info_{$mode}"      => array(
					// Translators: Add mode name to the title
					'title'  => wp_kses_data( sprintf( __( "%s posts page", 'orto' ), $title ) ),
					// Translators: Add mode name to the description
					'desc'   => wp_kses_data( sprintf( __( 'Customize %s page: post layout, header and footer styles, sidebar position and widgets, etc.', 'orto' ), $title ) ),
					'type'   => 'info',
				),
				"body_style_{$mode}"             => array(
					'title'    => esc_html__( 'Body style', 'orto' ),
					'desc'     => wp_kses_data( sprintf( __( 'Select width of the body content on the %s page', 'orto' ), $title ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_body_styles( true, true ),
					'type'     => 'choice',
				),
				"blog_style_{$mode}"             => array(
					'title'      => esc_html__( 'Blog style', 'orto' ),
					'desc'       => '',
					'std'        => 'classic_1',
					'options'    => array(),
					'type'       => 'choice',
				),
				"excerpt_length_{$mode}"         => array(
					'title'      => esc_html__( 'Excerpt length', 'orto' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'orto' ) ),
					'std'        => 25,
					'type'       => 'text',
				),
				"meta_parts_{$mode}"             => array(
					'title'      => esc_html__( 'Post meta', 'orto' ),
					'desc'       => wp_kses_data( __( "Set up post meta parts to show in the blog archive. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'orto' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'orto' ) ),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=0|likes=0|comments=1|author=0|share=0|edit=0',
					'options'    => orto_get_list_meta_parts(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'checklist',
				),
				"blog_pagination_{$mode}"        => array(
					'title'      => esc_html__( 'Pagination style', 'orto' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'orto' ) ),
					'std'        => 'pages',
					'options'    => orto_get_list_blog_paginations( true ),
					'type'       => 'choice',
				),
				"blog_animation_{$mode}"         => array(
					'title'      => esc_html__( 'Post animation', 'orto' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'orto' ) ),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'select',
				),

				"blog_header_info_{$mode}"       => array(
					'title' => esc_html__( 'Header', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"header_type_{$mode}"            => array(
					'title'    => esc_html__( 'Header style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_header_footer_types( true ),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),
				"header_style_{$mode}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				"header_position_{$mode}"        => array(
					'title'    => esc_html__( 'Header position', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => 'radio',
				),

				"blog_sidebar_info_{$mode}"      => array(
					'title' => esc_html__( 'Sidebar', 'orto' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"sidebar_position_{$mode}"       => array(
					'title'   => esc_html__( 'Sidebar position', 'orto' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'orto' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_type_{$mode}"           => array(
					'title'    => esc_html__( 'Sidebar style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => orto_get_list_header_footer_types(),
					'pro_only' => ORTO_THEME_FREE,
					'type'     => ! orto_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_{$mode}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'orto' ), 'orto_kses_content' ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_{$mode}"        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'orto' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_{$mode}"         => array(
					'title'   => esc_html__( 'Content width', 'orto' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'orto' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => orto_get_list_expand_content( true ),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'choice',
				),
			), $mode, $title
		);
	}
}


// Common parameters for CPT
//------------------------------------------------------------------------------------------------------------

// Returns a list of options that can be overridden for CPT
if ( ! function_exists( 'orto_options_get_list_cpt_options' ) ) {
	function orto_options_get_list_cpt_options( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'orto_filter_get_list_cpt_options',
								array_merge(
									orto_options_get_list_cpt_options_body( $cpt, $title ),              // Body style options for both: a posts list and a single post
									orto_options_get_list_cpt_options_header( $cpt, $title, 'list' ),    // Header options for the posts list
									orto_options_get_list_cpt_options_header( $cpt, $title, 'single' ),  // Header options for the single post
									orto_options_get_list_cpt_options_sidebar( $cpt, $title, 'list' ),   // Sidebar options for the posts list
									orto_options_get_list_cpt_options_sidebar( $cpt, $title, 'single' ), // Sidebar options for the single post
									orto_options_get_list_cpt_options_footer( $cpt, $title ),            // Footer options for both: a posts list and a single post
									orto_options_get_list_cpt_options_widgets( $cpt, $title )            // Widgets options for both: a posts list and a single post
								),
								$cpt,
								$title
							);
	}
}


// Returns a text description suffix for CPT
if ( ! function_exists( 'orto_options_get_cpt_description_suffix' ) ) {
	function orto_options_get_cpt_description_suffix( $title, $mode ) {
		return $mode == 'both'
					// Translators: Add CPT name to the description
					? sprintf( __( 'the %s list and single posts', 'orto' ), $title )
					: ( $mode == 'list'
						// Translators: Add CPT name to the description
						? sprintf( __( 'the %s list', 'orto' ), $title )
						// Translators: Add CPT name to the description
						: sprintf( __( 'Single %s posts', 'orto' ), $title )
						);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Content'
if ( ! function_exists( 'orto_options_get_list_cpt_options_body' ) ) {
	function orto_options_get_list_cpt_options_body( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = orto_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "orto_filter_get_list_cpt_options_body{$suffix}", array(
				"content_info{$suffix}_{$cpt}"           => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Body style on %s', 'orto' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s', 'orto' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"body_style{$suffix}_{$cpt}"             => array(
					'title'    => esc_html__( 'Body style', 'orto' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'orto' ) ),
					'std'      => 'inherit',
					'options'  => orto_get_list_body_styles( true, true ),
					'type'     => 'choice',
				),
				"boxed_bg_image{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Boxed bg image', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select or upload image for the background of the boxed content', 'orto' ) ),
					'dependency' => array(
						"body_style{$suffix}_{$cpt}" => array( 'boxed' ),
					),
					'std'        => 'inherit',
					'type'       => 'image',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Header'
if ( ! function_exists( 'orto_options_get_list_cpt_options_header' ) ) {
	function orto_options_get_list_cpt_options_header( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = orto_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "orto_filter_get_list_cpt_options_header{$suffix}", array(
				"header_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Header on %s', 'orto' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up header parameters to display %s', 'orto' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"header_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Header style', 'orto' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'     => 'inherit',
					'options' => orto_get_list_header_footer_types( true ),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'radio',
				),
				"header_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select custom layout to display the site header on the %s pages', 'orto' ), $title ) ),
					'dependency' => array(
						"header_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'select',
				),
				"header_position{$suffix}_{$cpt}"        => array(
					'title'   => esc_html__( 'Header position', 'orto' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select position to display the site header on the %s pages', 'orto' ), $title ) ),
					'std'     => 'inherit',
					'options' => array(),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'radio',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Sidebar'
if ( ! function_exists( 'orto_options_get_list_cpt_options_sidebar' ) ) {
	function orto_options_get_list_cpt_options_sidebar( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = orto_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "orto_filter_get_list_cpt_options_sidebar{$suffix}", array_merge(
				array(
					"sidebar_info{$suffix}_{$cpt}"           => array(
						// Translators: Add CPT name to the description
						'title' => wp_kses_data( sprintf( __( 'Sidebar on %s', 'orto' ), $suffix2 ) ),
						// Translators: Add CPT name to the description
						'desc'  => wp_kses_data( sprintf( __( 'Set up sidebar parameters to display %s', 'orto' ), $suffix2 ) ),
						'type'  => 'info',
					),
					"sidebar_position{$suffix}_{$cpt}"       => array(
						'title'   => esc_html__( 'Sidebar position', 'orto' ),
						'desc'    => wp_kses_data( __( 'Select sidebar position', 'orto' ) ),
						'std'     => 'hide',
						'options' => array(),
						'type'    => 'choice',
					),
					"sidebar_type{$suffix}_{$cpt}"           => array(
						'title'    => esc_html__( 'Sidebar style', 'orto' ),
						'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'std'      => 'default',
						'options'  => orto_get_list_header_footer_types( true ),
						'pro_only' => ORTO_THEME_FREE,
						'type'     => ! orto_exists_trx_addons() ? 'hidden' : 'radio',
					),
					"sidebar_style{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Select custom layout', 'orto' ),
						'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'orto' ), 'orto_kses_content' ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'custom' ),
						),
						'std'        => '',
						'options'    => array(),
						'type'       => 'select',
					),
					"sidebar_widgets{$suffix}_{$cpt}"        => array(
						'title'      => esc_html__( 'Sidebar widgets', 'orto' ),
						'desc'       => wp_kses_data( __( 'Select set of widgets to display in the sidebar', 'orto' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'default' ),
						),
						'std'        => 'hide',
						'options'    => array(),
						'type'       => 'select',
					),
				),
				$mode == 'single' ? array() : array(
					"sidebar_width{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Sidebar width', 'orto' ),
						'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'orto' ) ),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 500,
						'step'       => 10,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => ORTO_THEME_FREE,
						'type'       => 'slider',
					),
					"sidebar_gap{$suffix}_{$cpt}"            => array(
						'title'      => esc_html__( 'Sidebar gap', 'orto' ),
						'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'orto' ) ),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => ORTO_THEME_FREE,
						'type'       => 'slider',
					),
					"sidebar_proportional{$suffix}_{$cpt}"    => array(
						'title'      => esc_html__( 'Sidebar proportional', 'orto' ),
						'desc'       => wp_kses_data( __( 'Change the width of the sidebar and gap proportionally when the window is resized, or leave the width of the sidebar constant', 'orto' ) ),
						'refresh'    => false,
						'std'        => 1,
						'type'       => 'switch',
					),
				),
				array(
					"expand_content{$suffix}_{$cpt}"          => array(
						'title'   => esc_html__( 'Content width', 'orto' ),
						'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'orto' ) ),
						'refresh' => false,
						'std'     => 'inherit',
						'options' => orto_get_list_expand_content( true ),
						'pro_only'=> ORTO_THEME_FREE,
						'type'    => 'choice',
					),
				)
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Footer'
if ( ! function_exists( 'orto_options_get_list_cpt_options_footer' ) ) {
	function orto_options_get_list_cpt_options_footer( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = orto_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "orto_filter_get_list_cpt_options_footer{$suffix}", array(
				"footer_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Footer on %s', 'orto' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up footer parameters to display %s', 'orto' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"footer_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Footer style', 'orto' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'orto' ) ),
					'std'     => 'inherit',
					'options' => orto_get_list_header_footer_types( true ),
					'pro_only'=> ORTO_THEME_FREE,
					'type'    => 'radio',
				),
				"footer_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select custom layout to display the site footer', 'orto' ) ),
					'std'        => 'inherit',
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'options'    => array(),
					'pro_only'   => ORTO_THEME_FREE,
					'type'       => 'select',
				),
				"footer_widgets{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer widgets', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'orto' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"footer_columns{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer columns', 'orto' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'orto' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}"    => array( 'default' ),
						"footer_widgets{$suffix}_{$cpt}" => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => orto_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Additional Widget Areas'
if ( ! function_exists( 'orto_options_get_list_cpt_options_widgets' ) ) {
	function orto_options_get_list_cpt_options_widgets( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		return apply_filters( "orto_filter_get_list_cpt_options_widgets{$suffix}", array(), $cpt, $title );
	}
}


// Return lists with choises when its need in the admin mode
if ( ! function_exists( 'orto_options_get_list_choises' ) ) {
	add_filter( 'orto_filter_options_get_list_choises', 'orto_options_get_list_choises', 10, 2 );
	function orto_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ( strpos( $id, 'header_style' ) === 0 ) {
				$list = orto_get_list_header_styles( strpos( $id, 'header_style_' ) === 0 );
			} elseif ( strpos( $id, 'header_position' ) === 0 ) {
				$list = orto_get_list_header_positions( strpos( $id, 'header_position_' ) === 0 );
			} elseif ( strpos( $id, '_scheme' ) > 0 ) {
				$list = orto_get_list_schemes( 'color_scheme' != $id );
			} else if ( strpos( $id, 'sidebar_style' ) === 0 ) {
				$list = orto_get_list_sidebar_styles( strpos( $id, 'sidebar_style_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_widgets' ) === 0 ) {
				$list = orto_get_list_sidebars( 'sidebar_widgets_single' != $id && ( strpos( $id, 'sidebar_widgets_' ) === 0 || strpos( $id, 'sidebar_widgets_single_' ) === 0 ), true );
			} elseif ( strpos( $id, 'sidebar_position' ) === 0 ) {
				$list = orto_get_list_sidebars_positions( strpos( $id, 'sidebar_position_' ) === 0 );
			} elseif ( strpos( $id, 'footer_style' ) === 0 ) {
				$list = orto_get_list_footer_styles( strpos( $id, 'footer_style_' ) === 0 );
			} elseif ( strpos( $id, 'footer_widgets' ) === 0 ) {
				$list = orto_get_list_sidebars( strpos( $id, 'footer_widgets_' ) === 0, true );
			} elseif ( strpos( $id, 'blog_style' ) === 0 ) {
				$list = orto_get_list_blog_styles( strpos( $id, 'blog_style_' ) === 0 );
			} elseif ( strpos( $id, 'single_style' ) === 0 ) {
				$list = orto_get_list_single_styles( strpos( $id, 'single_style_' ) === 0 );
			} elseif ( strpos( $id, 'post_type' ) === 0 ) {
				$list = orto_get_list_posts_types();
			} elseif ( strpos( $id, 'parent_cat' ) === 0 ) {
				$list = orto_array_merge( array( 0 => orto_get_not_selected_text( esc_html__( 'Select category', 'orto' ) ) ), orto_get_list_categories() );
			} elseif ( strpos( $id, 'blog_animation' ) === 0 ) {
				$list = orto_get_list_animations_in( strpos( $id, 'blog_animation_' ) === 0 );
			} elseif ( 'color_scheme_editor' == $id ) {
				$list = orto_get_list_schemes();
			} elseif ( strpos( $id, '_font-family' ) > 0 ) {
				$list = orto_get_list_load_fonts( true );
			} elseif ( 'redirect_404_page' == $id ) {
				$list = orto_get_list_pages();
			}
		}
		return $list;
	}
}


//--------------------------------------------
// THUMBS
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup_thumbs' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_thumbs', 1 );
	function orto_skin_setup_thumbs() {
		orto_storage_set(
			'theme_thumbs', apply_filters(
				'orto_filter_add_thumb_sizes', array(
					// Height is fixed
					'orto-thumb-huge'        => array(
						'size'  => array( 1290, 725, true ),
						'title' => esc_html__( 'Huge image', 'orto' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Height is fixed
					'orto-thumb-big'         => array(
						'size'  => array( 924, 520, true ),
						'title' => esc_html__( 'Large image', 'orto' ),
						'subst' => 'trx_addons-thumb-big',
					),
					// Height is fixed
					'orto-thumb-med'         => array(
						'size'  => array( 410, 230, true ),
						'title' => esc_html__( 'Medium image', 'orto' ),
						'subst' => 'trx_addons-thumb-medium',
					),
					// Small square image (for avatars in comments, etc.)
					'orto-thumb-tiny'        => array(
						'size'  => array( 90, 90, true ),
						'title' => esc_html__( 'Small square avatar', 'orto' ),
						'subst' => 'trx_addons-thumb-tiny',
					),
					// Height is proportional (only downscale, not crop)
					'orto-thumb-masonry-big' => array(
						'size'  => array( 924, 0, false ), // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'orto' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),
					// Height is proportional (only downscale, not crop)
					'orto-thumb-masonry'     => array(
						'size'  => array( 410, 0, false ), // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'orto' ),
						'subst' => 'trx_addons-thumb-masonry',
					),
				)
			)
		);
	}
}


//--------------------------------------------
// BLOG STYLES
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup_blog_styles' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_blog_styles', 1 );
	function orto_skin_setup_blog_styles() {
		$blog_styles = array(
			'classic' => array(
				'title'   => esc_html__( 'Classic', 'orto' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 1, 2, 3 ),
				'styles'  => 'classic',
				'icon'    => "images/theme-options/blog-style/classic-%d.png",
			),
		);
		orto_storage_set( 'blog_styles', apply_filters( 'orto_filter_add_blog_styles', $blog_styles ) );
	}
}


//--------------------------------------------
// SINGLE STYLES
//--------------------------------------------
if ( ! function_exists( 'orto_skin_setup_single_styles' ) ) {
	add_action( 'after_setup_theme', 'orto_skin_setup_single_styles', 1 );
	function orto_skin_setup_single_styles() {
		orto_storage_set(
			'single_styles', apply_filters(
				'orto_filter_add_single_styles', array(
					'style-1' => array(
						'title'       => esc_html__( 'Style 1', 'orto' ),
						'description' => esc_html__( 'Boxed image, the title and meta are inside the content area, the title and meta are above the image', 'orto' ),
						'styles'      => 'style-1',
						'icon'        => "images/theme-options/single-style/style-6.png",
					),
					'style-2' => array(
						'title'       => esc_html__( 'Style 2', 'orto' ),
						'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are over the image', 'orto' ),
						'styles'      => 'style-2',
						'icon'        => "images/theme-options/single-style/style-1.png",
					),
				)
			)
		);
	}
}