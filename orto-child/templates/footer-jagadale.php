<?php
/**
 * The site footer
 *
 * Replaces the parent theme's footer entirely (see orto_child_use_custom_footer()
 * in functions.php). The parent's footer is widget-driven; this one is not, so
 * the footer is part of the deploy rather than something that has to be rebuilt
 * by hand in wp-admin on every environment.
 *
 * Layout: logo, then three columns (about + socials / pages / contact), then a
 * rule and a bottom bar carrying the copyright, the build credit and the legal
 * links. The contact column leads with the registered practice details,
 * followed by the address and the clinic's numbers.
 *
 * @package ORTO
 */

$orto_child_footer_business = orto_child_get_business();
$orto_child_footer_pages    = orto_child_resolve_footer_links( orto_child_get_footer_pages() );
$orto_child_footer_legal    = orto_child_resolve_footer_links( orto_child_get_footer_legal_pages() );

/*
 * Logo, in order of preference:
 *   1. images/logo-light.* - the reversed mark, which is the one that belongs
 *      on this dark footer.
 *   2. images/logo-mark.* - the standard mark, as a stand-in.
 *   3. The logo set in the Customizer, if neither file is bundled.
 *   4. The clinic's name as text, so the footer is never left with a blank space.
 */
$orto_child_footer_logo_url = orto_child_image_url( 'logo-light' );

if ( '' === $orto_child_footer_logo_url ) {
	$orto_child_footer_logo_url = orto_child_image_url( 'logo-mark' );
}

if ( '' === $orto_child_footer_logo_url ) {
	$orto_child_footer_logo_id = get_theme_mod( 'custom_logo' );
	if ( $orto_child_footer_logo_id && is_numeric( $orto_child_footer_logo_id ) ) {
		$orto_child_footer_logo_url = wp_get_attachment_image_url( $orto_child_footer_logo_id, 'full' );
	}
}
?>

<footer class="footer_wrap footer_jagadale">
	<div class="content_wrap">

		<div class="djo_footer_top">

			<a class="djo_footer_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( $orto_child_footer_logo_url ) { ?>
					<img src="<?php echo esc_url( $orto_child_footer_logo_url ); ?>" alt="<?php echo esc_attr( $orto_child_footer_business['name'] ); ?>">
				<?php } ?>
				<span class="djo_footer_logo_text">
					<span class="djo_footer_logo_name"><?php echo esc_html( $orto_child_footer_business['name'] ); ?></span>
					<span class="djo_footer_logo_tagline"><?php echo esc_html( $orto_child_footer_business['tagline'] ); ?></span>
				</span>
			</a>

			<div class="djo_footer_columns">

				<div class="djo_footer_col djo_footer_col_about">
					<h3 class="djo_footer_title"><?php esc_html_e( 'About Us', 'orto' ); ?></h3>
					<p class="djo_footer_about_text">
						<?php
						printf(
							/* translators: 1: doctor's name, 2: qualifications. */
							esc_html__( 'An orthopaedic and fracture clinic in Nanded City, Pune, led by %1$s, %2$s - with digital X-ray and physiotherapy under the same roof, so most visits are settled in a single appointment.', 'orto' ),
							esc_html( $orto_child_footer_business['doctor'] ),
							esc_html( $orto_child_footer_business['doctor_creds'] )
						);
						?>
					</p>

					<div class="djo_footer_socials">
						<?php if ( ! empty( $orto_child_footer_business['facebook_url'] ) ) { ?>
							<a class="djo_footer_social" href="<?php echo esc_url( $orto_child_footer_business['facebook_url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Facebook', 'orto' ); ?>">
								<?php echo orto_child_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php } ?>
						<?php if ( ! empty( $orto_child_footer_business['instagram_url'] ) ) { ?>
							<a class="djo_footer_social" href="<?php echo esc_url( $orto_child_footer_business['instagram_url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Instagram', 'orto' ); ?>">
								<?php echo orto_child_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php } ?>
						<?php if ( ! empty( $orto_child_footer_business['whatsapp_link'] ) ) { ?>
							<a class="djo_footer_social" href="<?php echo esc_url( $orto_child_footer_business['whatsapp_link'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'WhatsApp', 'orto' ); ?>">
								<?php echo orto_child_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php } ?>
					</div>
				</div>

				<?php if ( ! empty( $orto_child_footer_pages ) ) { ?>
					<nav class="djo_footer_col djo_footer_col_pages" aria-label="<?php esc_attr_e( 'Footer', 'orto' ); ?>">
						<h3 class="djo_footer_title"><?php esc_html_e( 'Pages', 'orto' ); ?></h3>
						<ul class="djo_footer_menu">
							<?php foreach ( $orto_child_footer_pages as $orto_child_footer_link ) { ?>
								<li class="djo_footer_menu_item">
									<a href="<?php echo esc_url( $orto_child_footer_link['url'] ); ?>"><?php echo esc_html( $orto_child_footer_link['title'] ); ?></a>
								</li>
							<?php } ?>
						</ul>
					</nav>
				<?php } ?>

				<div class="djo_footer_col djo_footer_col_contact">
					<h3 class="djo_footer_title"><?php esc_html_e( 'Contact', 'orto' ); ?></h3>

					<?php
					/*
					 * Registered practice details, at the head of this column.
					 * Set on their own lines so each fact stays scannable rather
					 * than running together as one long line of small print.
					 */
					?>
					<div class="djo_footer_legal_block">
						<span class="djo_footer_legal_name"><?php echo esc_html( $orto_child_footer_business['doctor'] ); ?></span>
						<span class="djo_footer_legal_line"><?php echo esc_html( $orto_child_footer_business['doctor_creds'] . ' - ' . $orto_child_footer_business['doctor_role'] ); ?></span>
						<span class="djo_footer_legal_line">
							<?php
							printf(
								/* translators: %s: medical council registration number */
								esc_html__( 'Reg. No. %s', 'orto' ),
								esc_html( $orto_child_footer_business['reg_number'] )
							);
							?>
						</span>
					</div>

					<ul class="djo_footer_contacts">
						<li class="djo_footer_contact">
							<span class="djo_footer_contact_icon" aria-hidden="true"><?php echo orto_child_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="djo_footer_contact_text"><?php echo esc_html( $orto_child_footer_business['address'] ); ?></span>
						</li>
						<?php if ( ! empty( $orto_child_footer_business['phone'] ) ) { ?>
							<li class="djo_footer_contact">
								<span class="djo_footer_contact_icon" aria-hidden="true"><?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="djo_footer_contact_text">
									<a href="tel:<?php echo esc_attr( $orto_child_footer_business['phone_link'] ); ?>"><?php echo esc_html( $orto_child_footer_business['phone'] ); ?></a>
									<?php if ( ! empty( $orto_child_footer_business['phone_alt'] ) ) { ?>
										<span class="djo_footer_contact_sep">/</span>
										<a href="tel:<?php echo esc_attr( $orto_child_footer_business['phone_alt_link'] ); ?>"><?php echo esc_html( $orto_child_footer_business['phone_alt'] ); ?></a>
									<?php } ?>
								</span>
							</li>
						<?php } ?>
						<?php if ( ! empty( $orto_child_footer_business['email'] ) ) { ?>
							<li class="djo_footer_contact">
								<span class="djo_footer_contact_icon" aria-hidden="true"><?php echo orto_child_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="djo_footer_contact_text">
									<a href="mailto:<?php echo esc_attr( $orto_child_footer_business['email'] ); ?>"><?php echo esc_html( $orto_child_footer_business['email'] ); ?></a>
								</span>
							</li>
						<?php } ?>
						<li class="djo_footer_contact">
							<span class="djo_footer_contact_icon" aria-hidden="true"><?php echo orto_child_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="djo_footer_contact_text">
								<?php foreach ( $orto_child_footer_business['hours'] as $orto_child_footer_hour ) { ?>
									<span class="djo_footer_hours_line"><?php echo esc_html( $orto_child_footer_hour['label'] . ': ' . $orto_child_footer_hour['value'] ); ?></span>
								<?php } ?>
							</span>
						</li>
					</ul>
				</div>

			</div>

		</div>

		<div class="djo_footer_bottom">

			<div class="djo_footer_bottom_left">
				<p class="djo_footer_copyright">
					<?php
					printf(
						/* translators: 1: current year, 2: clinic name */
						esc_html__( 'Copyright &copy; %1$s %2$s. All rights reserved.', 'orto' ),
						esc_html( wp_date( 'Y' ) ),
						esc_html( $orto_child_footer_business['name'] )
					);
					?>
				</p>

				<?php
				/*
				 * Agency credit. Uses orto-child/images/blocsys.svg or .png when
				 * that file is bundled, and falls back to a text wordmark when it
				 * is not - so the credit is never missing and the mark is never
				 * hotlinked from blocsys.com.
				 */
				$orto_child_footer_credit_url = '';

				foreach ( array( 'blocsys.svg', 'blocsys.png', 'blocsys.webp' ) as $orto_child_footer_credit_file ) {
					if ( file_exists( get_stylesheet_directory() . '/images/' . $orto_child_footer_credit_file ) ) {
						$orto_child_footer_credit_url = get_stylesheet_directory_uri() . '/images/' . $orto_child_footer_credit_file;
						break;
					}
				}
				?>
				<p class="djo_footer_credit">
					<span class="djo_footer_credit_label"><?php esc_html_e( 'Built by', 'orto' ); ?></span>
					<a class="djo_footer_credit_link" href="https://blocsys.com/" target="_blank" rel="noopener">
						<?php if ( $orto_child_footer_credit_url ) { ?>
							<img src="<?php echo esc_url( $orto_child_footer_credit_url ); ?>" alt="Blocsys" width="64" height="64" loading="lazy" decoding="async">
						<?php } ?>
						<span class="djo_footer_credit_name">Blocsys</span>
					</a>
				</p>
			</div>

			<?php if ( ! empty( $orto_child_footer_legal ) ) { ?>
				<ul class="djo_footer_bottom_links">
					<?php foreach ( $orto_child_footer_legal as $orto_child_footer_link ) { ?>
						<li><a href="<?php echo esc_url( $orto_child_footer_link['url'] ); ?>"><?php echo esc_html( $orto_child_footer_link['title'] ); ?></a></li>
					<?php } ?>
				</ul>
			<?php } ?>

		</div>

	</div>
</footer>
