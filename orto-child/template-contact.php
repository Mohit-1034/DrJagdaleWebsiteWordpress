<?php
/**
 * The template to display the Contact Us page
 *
 * @package ORTO
 */

/*
Template Name: Contact
*/

/**
 * All content on this page (intro text, contact details, opening times and the
 * form fields) is hardcoded rather than stored in the database, so it survives
 * a database wipe or a migration.
 *
 * The enquiry form itself is not here. It, its POST handler and the business
 * details it reads live in inc/enquiry-form.php, so a second page can show the
 * same form rather than a copy of it that drifts.
 */

if ( ! function_exists( 'orto_child_contact_icon' ) ) {
	/**
	 * Inline SVG icons. Thin wrapper over the site-wide orto_child_icon() in
	 * functions.php, which the header and footer share.
	 *
	 * @param string $name Icon slug.
	 * @return string Safe SVG markup (or an empty string for an unknown slug).
	 */
	function orto_child_contact_icon( $name ) {
		return orto_child_icon( $name );
	}
}

/*
 * Handles a submission and redirects, so a refresh cannot re-send the enquiry.
 * Has to run before get_header() - nothing may have been printed yet.
 */
$orto_child_contact_status   = orto_child_enquiry_form_boot();
$orto_child_contact_settings = orto_child_contact_get_settings();

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>"
		<?php
		post_class( 'post_item_single post_type_page contact_page' );
		orto_add_seo_itemprops();
		?>
	>

		<?php
		do_action( 'orto_action_before_post_data' );

		orto_add_seo_snippets();

		do_action( 'orto_action_before_post_content' );
		?>

		<div class="post_content entry-content">

			<div class="contact_layout">

				<aside class="contact_details">

					<span class="djo_eyebrow contact_eyebrow"><?php esc_html_e( 'Get in touch', 'orto' ); ?></span>

					<div class="contact_intro">
						<p>
							<?php esc_html_e( 'Call the clinic to book, or send the form and we will call you back. Walk-in consultations are seen the same day wherever the list allows, and digital X-ray and physiotherapy are both on site - so most visits are settled in one appointment.', 'orto' ); ?>
						</p>
					</div>

					<ul class="contact_details_list">
						<?php if ( ! empty( $orto_child_contact_settings['phone'] ) ) { ?>
							<li class="contact_details_item">
								<span class="contact_details_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_details_body">
									<span class="contact_details_label"><?php esc_html_e( 'Clinic', 'orto' ); ?></span>
									<a class="contact_details_value" href="tel:<?php echo esc_attr( $orto_child_contact_settings['phone_link'] ); ?>"><?php echo esc_html( $orto_child_contact_settings['phone'] ); ?></a>
									<?php if ( ! empty( $orto_child_contact_settings['phone_alt'] ) ) { ?>
										<a class="contact_details_value" href="tel:<?php echo esc_attr( $orto_child_contact_settings['phone_alt_link'] ); ?>"><?php echo esc_html( $orto_child_contact_settings['phone_alt'] ); ?></a>
									<?php } ?>
								</span>
							</li>
						<?php } ?>

						<?php if ( ! empty( $orto_child_contact_settings['phone_physio'] ) ) { ?>
							<li class="contact_details_item">
								<span class="contact_details_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_details_body">
									<span class="contact_details_label"><?php esc_html_e( 'Physiotherapy', 'orto' ); ?></span>
									<a class="contact_details_value" href="tel:<?php echo esc_attr( $orto_child_contact_settings['phone_physio_link'] ); ?>"><?php echo esc_html( $orto_child_contact_settings['phone_physio'] ); ?></a>
								</span>
							</li>
						<?php } ?>

						<?php if ( ! empty( $orto_child_contact_settings['email'] ) ) { ?>
							<li class="contact_details_item">
								<span class="contact_details_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_details_body">
									<span class="contact_details_label"><?php esc_html_e( 'Email', 'orto' ); ?></span>
									<a class="contact_details_value" href="mailto:<?php echo esc_attr( $orto_child_contact_settings['email'] ); ?>"><?php echo esc_html( $orto_child_contact_settings['email'] ); ?></a>
								</span>
							</li>
						<?php } ?>

						<li class="contact_details_item">
							<span class="contact_details_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="contact_details_body">
								<span class="contact_details_label"><?php esc_html_e( 'Address', 'orto' ); ?></span>
								<span class="contact_details_value"><?php echo esc_html( $orto_child_contact_settings['address'] ); ?></span>
							</span>
						</li>
					</ul>

					<div class="contact_hours">
						<h2 class="contact_hours_title">
							<span class="contact_hours_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php esc_html_e( 'Consulting Hours', 'orto' ); ?>
						</h2>
						<ul class="contact_hours_list">
							<?php foreach ( $orto_child_contact_settings['hours'] as $orto_child_contact_hour ) { ?>
								<li class="contact_hours_item<?php echo ( 'Closed' === $orto_child_contact_hour['value'] ) ? ' contact_hours_item_closed' : ''; ?>">
									<span class="contact_hours_label"><?php echo esc_html( $orto_child_contact_hour['label'] ); ?></span>
									<span class="contact_hours_value"><?php echo esc_html( $orto_child_contact_hour['value'] ); ?></span>
								</li>
							<?php } ?>
						</ul>
						<?php if ( ! empty( $orto_child_contact_settings['consult_fee'] ) ) { ?>
							<p class="contact_hours_fee">
								<?php
								printf(
									/* translators: %s: consultation fee in rupees. */
									esc_html__( 'Consultation fee: %s', 'orto' ),
									'&#8377;' . esc_html( $orto_child_contact_settings['consult_fee'] )
								);
								?>
							</p>
						<?php } ?>
					</div>

					<div class="contact_social">
						<?php if ( ! empty( $orto_child_contact_settings['whatsapp_link'] ) ) { ?>
							<a class="contact_social_link" href="<?php echo esc_url( $orto_child_contact_settings['whatsapp_link'] ); ?>" target="_blank" rel="noopener">
								<span class="contact_social_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_social_text"><?php esc_html_e( 'WhatsApp', 'orto' ); ?></span>
							</a>
						<?php } ?>
						<?php if ( ! empty( $orto_child_contact_settings['facebook_url'] ) ) { ?>
							<a class="contact_social_link" href="<?php echo esc_url( $orto_child_contact_settings['facebook_url'] ); ?>" target="_blank" rel="noopener">
								<span class="contact_social_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_social_text"><?php esc_html_e( 'Facebook', 'orto' ); ?></span>
							</a>
						<?php } ?>
						<?php if ( ! empty( $orto_child_contact_settings['instagram_url'] ) ) { ?>
							<a class="contact_social_link" href="<?php echo esc_url( $orto_child_contact_settings['instagram_url'] ); ?>" target="_blank" rel="noopener">
								<span class="contact_social_icon" aria-hidden="true"><?php echo orto_child_contact_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact_social_text"><?php esc_html_e( 'Instagram', 'orto' ); ?></span>
							</a>
						<?php } ?>
					</div>

				</aside>

				<div class="contact_form_wrap">

					<?php
					// Notices and fields both live in inc/enquiry-form.php, so a
					// second page renders exactly this form rather than a copy.
					orto_child_enquiry_form( array( 'status' => $orto_child_contact_status ) );
					?>

				</div>

			</div>

			<?php if ( ! empty( $orto_child_contact_settings['map_enabled'] ) ) { ?>
				<?php
				/*
				 * Full-bleed map band. 'alignfull' is the theme's own container
				 * breakout class (width:100vw plus negative side margins), so the
				 * band sits edge-to-edge the same way a full-width block does,
				 * in boxed and wide layouts alike.
				 *
				 * The iframe is lazy-loaded, so Google is not contacted until the
				 * band is actually scrolled into view.
				 */
				?>
				<section class="contact_map alignfull" aria-label="<?php esc_attr_e( 'Find us', 'orto' ); ?>">
					<div class="contact_map_frame">
						<iframe
							src="<?php echo esc_url( orto_child_contact_get_map_embed_url() ); ?>"
							title="<?php echo esc_attr( sprintf( /* translators: %s: street address */ __( 'Map showing %s', 'orto' ), $orto_child_contact_settings['address'] ) ); ?>"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							allowfullscreen></iframe>
					</div>
					<p class="contact_map_directions">
						<a href="<?php echo esc_url( orto_child_contact_get_map_directions_url() ); ?>" class="djo_button djo_button_solid" target="_blank" rel="noopener">
							<?php esc_html_e( 'Get Directions', 'orto' ); ?>
						</a>
					</p>
				</section>
			<?php } ?>

		</div>

		<?php
		do_action( 'orto_action_after_post_content' );

		do_action( 'orto_action_after_post_data' );
		?>

	</article>
	<?php
}

get_footer();
