<?php
/**
 * The template to display the main menu
 *
 * Overrides orto/skins/default/templates/header-navi.php. The theme resolves
 * this part through get_template_part(), which checks the child theme first, so
 * this file wins without touching the parent.
 *
 * Why override rather than restyle the parent's markup: the header is a
 * floating stripe - logo, menu and the clinic's phone number inside one rounded
 * bar - and the parent's four nested column divs have no slot for the tools and
 * impose a 1/4 + 3/4 split the design does not use. The markup below is the
 * same three regions with none of that.
 *
 * The Speciality and Services items open full-width mega panels rather than
 * ordinary dropdowns. Those panels are rendered here, once, as siblings of the
 * bar - not inside the <nav>, because the parent theme's menu CSS gives every
 * descendant of a menu item absolute positioning and a fixed width, and a panel
 * that has to span the whole window cannot live inside a 220px column. They are
 * shown by js/mega-menu.js, and by :hover alone if that script never runs.
 *
 * Classes kept deliberately, because other code depends on them:
 *   .top_panel_navi                - js/header.js pins this on scroll
 *   .sc_layouts_menu_mobile_button - the theme's own script opens the mobile menu from it
 *   .menu_main_nav_area            - the theme's menu styling and dropdown script
 *
 * @package ORTO
 */

$orto_child_header_business = orto_child_get_business();
$orto_child_header_groups   = orto_child_get_nav_groups();
?>
<div class="top_panel_navi sc_layouts_row sc_layouts_row_type_compact djo_bar">
	<div class="djo_bar_inner">

		<div class="djo_bar_logo">
			<?php get_template_part( apply_filters( 'orto_filter_get_template_part', 'templates/header-logo' ) ); ?>
		</div>

		<?php
		$orto_child_menu_main = orto_get_nav_menu( 'menu_main' );

		// Show any menu if no menu is selected in the location 'menu_main'.
		if ( orto_get_theme_setting( 'autoselect_menu' ) && empty( $orto_child_menu_main ) ) {
			$orto_child_menu_main = orto_get_nav_menu();
		}

		orto_show_layout(
			$orto_child_menu_main,
			'<nav class="menu_main_nav_area sc_layouts_menu sc_layouts_menu_default sc_layouts_hide_on_mobile djo_bar_menu"'
				. ( orto_is_on( orto_get_theme_option( 'seo_snippets' ) ) ? ' itemscope="itemscope" itemtype="' . esc_attr( orto_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"' : '' )
				. '>',
			'</nav>'
		);
		?>

		<div class="djo_bar_tools">

			<?php
			/*
			 * The phone number is the tool that matters on a clinic site - on a
			 * phone it dials, which is what someone in pain at this point in the
			 * page actually wants. It is a labelled button rather than a bare
			 * icon, because a number people are meant to call should be legible
			 * without being tapped first.
			 */
			?>
			<?php if ( ! empty( $orto_child_header_business['phone'] ) ) { ?>
				<a class="djo_bar_phone" href="tel:<?php echo esc_attr( $orto_child_header_business['phone_link'] ); ?>">
					<span class="djo_bar_phone_icon" aria-hidden="true"><?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="djo_bar_phone_body">
						<span class="djo_bar_phone_label"><?php esc_html_e( 'Call the clinic', 'orto' ); ?></span>
						<span class="djo_bar_phone_number"><?php echo esc_html( $orto_child_header_business['phone'] ); ?></span>
					</span>
				</a>
			<?php } ?>

			<a class="djo_button djo_button_solid djo_bar_cta" href="<?php echo esc_url( orto_child_page_url( 'contact-us' ) ); ?>">
				<?php esc_html_e( 'Book Appointment', 'orto' ); ?>
			</a>

			<div class="sc_layouts_iconed_text sc_layouts_menu_mobile_button djo_bar_burger">
				<a class="sc_layouts_item_link sc_layouts_iconed_text_link" href="#" role="button" aria-label="<?php esc_attr_e( 'Open the menu', 'orto' ); ?>">
					<span class="sc_layouts_item_icon sc_layouts_iconed_text_icon trx_addons_icon-menu"></span>
				</a>
			</div>

		</div>

	</div>

	<?php
	/*
	 * The mega panels.
	 *
	 * Speciality is laid out as columns of conditions under a pill heading, the
	 * way a patient scans for the part of them that hurts. Services is a two-up
	 * grid of what the clinic does. Both are the reference layouts from the
	 * brief's menu screenshots.
	 *
	 * Every heading is a real link to its section on the group's page, so the
	 * panel is navigable with a keyboard and useful without JavaScript.
	 */
	?>
	<?php foreach ( array( 'speciality', 'services' ) as $orto_child_mega_group ) { ?>
		<?php
		if ( empty( $orto_child_header_groups[ $orto_child_mega_group ] ) ) {
			continue;
		}

		$orto_child_mega_url = orto_child_page_url( $orto_child_mega_group );
		?>
		<div class="djo_mega djo_mega_<?php echo esc_attr( $orto_child_mega_group ); ?>"
			id="djo_mega_<?php echo esc_attr( $orto_child_mega_group ); ?>"
			data-djo-mega="<?php echo esc_attr( $orto_child_mega_group ); ?>"
			hidden>
			<div class="djo_mega_inner">
				<div class="djo_mega_grid">
					<?php foreach ( $orto_child_header_groups[ $orto_child_mega_group ] as $orto_child_mega_entry ) { ?>
						<div class="djo_mega_col">
							<a class="djo_mega_head" href="<?php echo esc_url( $orto_child_mega_url . '#' . $orto_child_mega_entry['slug'] ); ?>">
								<span class="djo_mega_head_icon" aria-hidden="true">
									<?php echo orto_child_menu_icon( $orto_child_mega_entry['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
								<span class="djo_mega_head_pill"><?php echo esc_html( $orto_child_mega_entry['title'] ); ?></span>
							</a>
							<ul class="djo_mega_list">
								<?php foreach ( $orto_child_mega_entry['items'] as $orto_child_mega_item ) { ?>
									<li><a href="<?php echo esc_url( $orto_child_mega_url . '#' . $orto_child_mega_entry['slug'] ); ?>"><?php echo esc_html( $orto_child_mega_item ); ?></a></li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>

				<div class="djo_mega_foot">
					<p class="djo_mega_foot_text">
						<?php
						if ( 'speciality' === $orto_child_mega_group ) {
							esc_html_e( 'Not sure which one fits? Describe the pain and we will work it out at the consultation.', 'orto' );
						} else {
							esc_html_e( 'Digital X-ray and physiotherapy are on site, so most visits are settled in one appointment.', 'orto' );
						}
						?>
					</p>
					<a class="djo_mega_foot_link" href="<?php echo esc_url( $orto_child_mega_url ); ?>">
						<?php
						/* translators: %s: the section name, e.g. Speciality. */
						printf( esc_html__( 'View all %s', 'orto' ), esc_html( 'speciality' === $orto_child_mega_group ? __( 'specialities', 'orto' ) : __( 'services', 'orto' ) ) );
						?>
						<?php echo orto_child_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
			</div>
		</div>
	<?php } ?>

</div>
