<?php
/**
 * The home page
 *
 * Every section here is written in PHP rather than built in a page builder, so
 * the home page deploys with the theme and cannot be lost with the database.
 * The order is the order a patient needs it in:
 *
 *   1. Hero          - who this is, where it is, and how to be seen today.
 *   2. Trust strip   - the four facts that answer "is this the right place".
 *   3. Speciality    - "which part of me hurts", the way people actually arrive.
 *   4. About         - the surgeon, briefly, with the credentials that matter.
 *   5. Conditions    - "what is it called", for the visitor who arrives with a
 *                     diagnosis rather than a symptom.
 *   6. Services      - what the clinic can do about it, all under one roof.
 *   7. Symptoms      - when it is worth coming in at all, and when to go
 *                     straight to a hospital instead.
 *   8. Reviews       - other patients saying it, which carries further than we can.
 *   9. CTA           - book.
 *
 * @package ORTO
 */

$djo_business   = orto_child_get_business();
$djo_groups     = orto_child_get_nav_groups();
$djo_speciality = orto_child_page_url( 'speciality' );
$djo_services   = orto_child_page_url( 'services' );
$djo_contact    = orto_child_page_url( 'contact-us' );

get_header();
?>

<div class="djo_home">

	<?php /* ---------------------------------------------------------------
	 * 1. Hero
	 *
	 * The photograph is two clinicians reading an X-ray, which is the honest
	 * picture of what happens here - not a stock waiting room. It sits behind
	 * a scrim rather than beside the text, because the header stripe floats
	 * over this band and needs something dark to sit on.
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_hero djo_band djo_band_dark">
		<?php $djo_hero_image = orto_child_image_url( 'hero-xray-review' ); ?>
		<?php if ( $djo_hero_image ) { ?>
			<div class="djo_hero_media" aria-hidden="true">
				<img src="<?php echo esc_url( $djo_hero_image ); ?>" alt="" fetchpriority="high" decoding="async">
			</div>
		<?php } ?>

		<?php
		/*
		 * The spine watermark. A decorative vertebral column set behind the
		 * copy and bleeding off the top and bottom of the band, which is what
		 * gives the navy something to be rather than a flat rectangle.
		 *
		 * It is a background-image on an empty element rather than inline SVG:
		 * nothing needs to reach inside it, and this way it costs the document
		 * one div instead of fourteen groups of paths.
		 */
		?>
		<span class="djo_spine djo_spine_hero" aria-hidden="true"></span>

		<div class="content_wrap">
			<div class="djo_hero_body">
				<span class="djo_eyebrow djo_eyebrow_light"><?php echo esc_html( $djo_business['tagline'] ); ?></span>

				<h1 class="djo_hero_title">
					<?php esc_html_e( 'Orthopaedic and fracture care in Nanded City, Pune', 'orto' ); ?>
				</h1>

				<p class="djo_hero_text">
					<?php
					printf(
						/* translators: 1: doctor's name, 2: qualifications. */
						esc_html__( '%1$s, %2$s - consulting orthopaedic and trauma surgeon. Consultation, digital X-ray and physiotherapy under one roof, so most visits are settled in a single appointment rather than three.', 'orto' ),
						esc_html( $djo_business['doctor'] ),
						esc_html( $djo_business['doctor_creds'] )
					);
					?>
				</p>

				<div class="djo_hero_actions">
					<a class="djo_button djo_button_solid" href="<?php echo esc_url( $djo_contact ); ?>">
						<?php esc_html_e( 'Book an Appointment', 'orto' ); ?>
					</a>
					<?php if ( ! empty( $djo_business['phone'] ) ) { ?>
						<a class="djo_button djo_button_ghost" href="tel:<?php echo esc_attr( $djo_business['phone_link'] ); ?>">
							<?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo esc_html( $djo_business['phone'] ); ?>
						</a>
					<?php } ?>
				</div>

				<?php
				/*
				 * The Google rating, stated where it is useful rather than in a
				 * widget at the bottom of the page. Written out in code from the
				 * clinic's profile - see orto_child_get_business().
				 */
				?>
				<p class="djo_hero_rating">
					<?php echo orto_child_stars( 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="djo_hero_rating_text">
						<?php
						printf(
							/* translators: 1: rating out of five, 2: number of reviews. */
							esc_html__( '%1$s from %2$s Google reviews', 'orto' ),
							'<strong>' . esc_html( $djo_business['rating'] ) . '</strong>',
							esc_html( $djo_business['rating_count'] )
						);
						?>
					</span>
				</p>
			</div>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 2. Trust strip
	 *
	 * Four facts, lifted clear of the hero so they read as the answer to "is
	 * this the right place" before the visitor has to scroll for it.
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_trust">
		<div class="content_wrap">
			<ul class="djo_trust_grid">
				<li class="djo_trust_item">
					<span class="djo_trust_value"><?php echo esc_html( $djo_business['experience'] ); ?>+</span>
					<span class="djo_trust_label"><?php esc_html_e( 'Years treating bone and joint injury', 'orto' ); ?></span>
				</li>
				<li class="djo_trust_item">
					<span class="djo_trust_value"><?php esc_html_e( 'On site', 'orto' ); ?></span>
					<span class="djo_trust_label"><?php esc_html_e( 'Digital X-ray, reported the same visit', 'orto' ); ?></span>
				</li>
				<li class="djo_trust_item">
					<span class="djo_trust_value"><?php esc_html_e( 'In house', 'orto' ); ?></span>
					<span class="djo_trust_label"><?php esc_html_e( 'Physiotherapy and rehabilitation', 'orto' ); ?></span>
				</li>
				<li class="djo_trust_item">
					<span class="djo_trust_value"><?php echo '&#8377;' . esc_html( $djo_business['consult_fee'] ); ?></span>
					<span class="djo_trust_label"><?php esc_html_e( 'Consultation, seven days a week', 'orto' ); ?></span>
				</li>
			</ul>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 3. Speciality
	 *
	 * By the part of the body, because that is what a patient can name. The
	 * clinical vocabulary comes later, on the Speciality page itself.
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_light djo_section_speciality">
		<div class="content_wrap">
			<?php
			orto_child_section_head(
				array(
					'eyebrow' => __( 'Speciality', 'orto' ),
					'title'   => __( 'Start with where it hurts', 'orto' ),
					'text'    => __( 'Most people arrive knowing the joint, not the diagnosis. Pick the one that fits and we will do the naming.', 'orto' ),
				)
			);
			?>

			<ul class="djo_cards djo_cards_4">
				<?php foreach ( $djo_groups['speciality'] as $djo_entry ) { ?>
					<li class="djo_card">
						<a class="djo_card_link" href="<?php echo esc_url( $djo_speciality . '#' . $djo_entry['slug'] ); ?>">
							<span class="djo_card_icon" aria-hidden="true">
								<?php echo orto_child_menu_icon( $djo_entry['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<span class="djo_card_title"><?php echo esc_html( $djo_entry['title'] ); ?></span>
							<span class="djo_card_meta">
								<?php
								printf(
									/* translators: %d: number of conditions treated. */
									esc_html( _n( '%d condition treated', '%d conditions treated', count( $djo_entry['items'] ), 'orto' ) ),
									count( $djo_entry['items'] )
								);
								?>
							</span>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 4. About the surgeon
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_dark djo_about_band">
		<span class="djo_spine djo_spine_about" aria-hidden="true"></span>

		<div class="content_wrap">
			<div class="djo_split">

				<div class="djo_split_media">
					<?php $djo_about_image = orto_child_image_url( 'clinic-signage' ); ?>
					<?php if ( $djo_about_image ) { ?>
						<img src="<?php echo esc_url( $djo_about_image ); ?>" alt="<?php echo esc_attr( $djo_business['name'] ); ?>" loading="lazy" decoding="async">
					<?php } ?>
				</div>

				<div class="djo_split_body">
					<span class="djo_eyebrow"><?php esc_html_e( 'About the clinic', 'orto' ); ?></span>
					<h2 class="djo_split_title"><?php echo esc_html( $djo_business['doctor'] ); ?></h2>
					<p class="djo_split_creds">
						<?php echo esc_html( $djo_business['doctor_creds'] . ' &middot; ' . $djo_business['doctor_role'] ); ?>
					</p>

					<p>
						<?php
						printf(
							/* translators: %s: number of years in practice. */
							esc_html__( 'Dr. Ganesh Jagadale has spent %s years in orthopaedics and trauma - an MBBS from Dr. V. M. Medical College, Solapur, a Diploma in Trauma and Orthopaedic Surgery from Hardikar Hospital, Pune, and a Diploma in Orthopaedics from the University of Mumbai.', 'orto' ),
							esc_html( $djo_business['experience'] )
						);
						?>
					</p>
					<p>
						<?php esc_html_e( 'The clinic in Nanded City was built around a simple idea: a patient in pain should not spend a week being sent between a consulting room, a scan centre and a physiotherapist. Consultation, digital X-ray and physiotherapy are all here, and the plan is explained in plain language before anything begins.', 'orto' ); ?>
					</p>

					<ul class="djo_split_facts">
						<li>
							<?php echo orto_child_icon( 'badge' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php
							printf(
								/* translators: %s: medical council registration number. */
								esc_html__( 'Maharashtra Medical Council Reg. No. %s', 'orto' ),
								esc_html( $djo_business['reg_number'] )
							);
							?>
						</li>
						<li>
							<?php echo orto_child_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php esc_html_e( 'Member, Indian Medical Association (IMA)', 'orto' ); ?>
						</li>
						<li>
							<?php echo orto_child_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php esc_html_e( 'Consulting seven days a week, 11:00 AM to 8:00 PM', 'orto' ); ?>
						</li>
					</ul>

					<p class="djo_split_actions">
						<a class="djo_button djo_button_outline" href="<?php echo esc_url( orto_child_page_url( 'about-us' ) ); ?>">
							<?php esc_html_e( 'More about the clinic', 'orto' ); ?>
						</a>
					</p>
				</div>

			</div>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 5. Orthopaedic conditions
	 *
	 * The Speciality cards above answer "where does it hurt". This answers
	 * "what is it called" - for the visitor who has already been given a name
	 * for it somewhere else and is checking whether this clinic treats it.
	 *
	 * The rail below the prose is one row of eight: icon, a dot on a rule, and
	 * a short label. The rule is drawn once across the whole row rather than
	 * per item, so the dots read as stops on one line instead of eight
	 * unrelated ticks.
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_light djo_conditions">
		<div class="content_wrap">

			<div class="djo_conditions_intro">
				<div class="djo_conditions_copy">
					<span class="djo_eyebrow"><?php esc_html_e( 'Orthopaedic conditions', 'orto' ); ?></span>
					<h2 class="djo_conditions_title">
						<?php
						printf(
							/* translators: %s: the emphasised part of the heading. */
							wp_kses_post( __( 'Causes of %s that our clinic treats', 'orto' ) ),
							'<strong>' . esc_html__( 'bone, joint &amp; spine pain', 'orto' ) . '</strong>'
						);
						?>
					</h2>
					<p>
						<?php esc_html_e( 'Many conditions end in the same complaint - pain, stiffness, weakness or numbness - in a joint, along the spine, or spreading into an arm or a leg. The complaint rarely tells you which one it is.', 'orto' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Finding the root cause is what makes a treatment plan work rather than merely pass the time. With digital X-ray in the clinic, the diagnosis and the plan are usually settled in the same visit.', 'orto' ); ?>
					</p>
				</div>

				<?php $djo_conditions_image = orto_child_image_url( 'conditions' ); ?>
				<?php if ( $djo_conditions_image ) { ?>
					<div class="djo_conditions_media">
						<img src="<?php echo esc_url( $djo_conditions_image ); ?>" alt="<?php esc_attr_e( 'An illustration of the lumbar spine, with the painful segment highlighted', 'orto' ); ?>" loading="lazy" decoding="async">
					</div>
				<?php } ?>
			</div>

			<ul class="djo_rail">
				<?php foreach ( orto_child_get_conditions() as $djo_condition ) { ?>
					<li class="djo_rail_item">
						<span class="djo_rail_icon" aria-hidden="true">
							<?php echo orto_child_menu_icon( $djo_condition['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span class="djo_rail_dot" aria-hidden="true"></span>
						<span class="djo_rail_label"><?php echo wp_kses_post( $djo_condition['label'] ); ?></span>
					</li>
				<?php } ?>
			</ul>

			<p class="djo_conditions_more">
				<a class="djo_button djo_button_outline" href="<?php echo esc_url( $djo_speciality ); ?>">
					<?php esc_html_e( 'See all conditions we treat', 'orto' ); ?>
				</a>
			</p>

		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 6. Services
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_dark djo_section_services">
		<div class="content_wrap">
			<?php
			orto_child_section_head(
				array(
					'eyebrow' => __( 'Services', 'orto' ),
					'title'   => __( 'Everything the treatment needs, in one place', 'orto' ),
					'text'    => __( 'From setting a fracture to replacing a joint - and the imaging and rehabilitation on either side of it.', 'orto' ),
				)
			);
			?>

			<ul class="djo_services_grid">
				<?php foreach ( $djo_groups['services'] as $djo_entry ) { ?>
					<li class="djo_service">
						<span class="djo_service_icon" aria-hidden="true">
							<?php echo orto_child_menu_icon( $djo_entry['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h3 class="djo_service_title">
							<a href="<?php echo esc_url( $djo_services . '#' . $djo_entry['slug'] ); ?>"><?php echo esc_html( $djo_entry['title'] ); ?></a>
						</h3>
						<p class="djo_service_text"><?php echo esc_html( $djo_entry['text'] ); ?></p>
					</li>
				<?php } ?>
			</ul>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 7. Signs and symptoms
	 *
	 * The section that turns "it will probably settle" into a decision. Written
	 * as things a visitor can check against themselves this evening rather than
	 * as a list of diagnoses, and numbered so the list can be counted at a
	 * glance instead of read as a wall.
	 *
	 * The red-flag note at the foot is deliberately not a call to action for
	 * this clinic. Cauda equina and an open fracture are emergencies, and a
	 * page that answered them with "book an appointment" would be doing the
	 * reader harm to win a booking.
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_light djo_symptoms">
		<div class="content_wrap">
			<?php
			orto_child_section_head(
				array(
					'eyebrow' => __( 'Signs and symptoms', 'orto' ),
					'title'   => __( 'When it is worth having it looked at', 'orto' ),
					'text'    => __( 'Most aches settle on their own. These are the ones that usually do not - if any of them sounds like you, it is worth a consultation.', 'orto' ),
				)
			);
			?>

			<div class="djo_symptoms_layout">

				<?php $djo_symptoms_image = orto_child_image_url( 'symptoms' ); ?>
				<?php if ( $djo_symptoms_image ) { ?>
					<div class="djo_symptoms_media">
						<img src="<?php echo esc_url( $djo_symptoms_image ); ?>" alt="<?php esc_attr_e( 'A patient holding a painful shoulder', 'orto' ); ?>" loading="lazy" decoding="async">
					</div>
				<?php } ?>

				<div class="djo_symptoms_body">
					<ol class="djo_symptoms_list">
						<?php foreach ( orto_child_get_symptoms() as $djo_symptom_index => $djo_symptom ) { ?>
							<li class="djo_symptoms_item">
								<span class="djo_symptoms_num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $djo_symptom_index + 1 ) ); ?></span>
								<span class="djo_symptoms_text"><?php echo esc_html( $djo_symptom ); ?></span>
							</li>
						<?php } ?>
					</ol>

					<p class="djo_symptoms_note">
						<span class="djo_symptoms_note_icon" aria-hidden="true"><?php echo orto_child_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span>
							<strong><?php esc_html_e( 'Do not wait for an appointment if:', 'orto' ); ?></strong>
							<?php esc_html_e( 'a bone is visibly out of shape or through the skin, a limb is cold or has lost sensation, or you have lost control of your bladder or bowels. Go to your nearest hospital.', 'orto' ); ?>
						</span>
					</p>

					<p class="djo_symptoms_actions">
						<a class="djo_button djo_button_solid" href="<?php echo esc_url( $djo_contact ); ?>">
							<?php esc_html_e( 'Book a consultation', 'orto' ); ?>
						</a>
						<?php if ( ! empty( $djo_business['phone'] ) ) { ?>
							<a class="djo_symptoms_phone" href="tel:<?php echo esc_attr( $djo_business['phone_link'] ); ?>">
								<?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo esc_html( $djo_business['phone'] ); ?>
							</a>
						<?php } ?>
					</p>
				</div>

			</div>
		</div>
	</section>


	<?php /* ---------------------------------------------------------------
	 * 8. Reviews
	 * ------------------------------------------------------------------- */ ?>
	<section class="djo_band djo_band_dark djo_reviews">
		<div class="content_wrap">
			<?php
			orto_child_section_head(
				array(
					'eyebrow' => __( 'Patient reviews', 'orto' ),
					'title'   => __( 'What patients say', 'orto' ),
					'text'    => sprintf(
						/* translators: 1: rating out of five, 2: number of reviews. */
						esc_html__( 'Rated %1$s from %2$s reviews on Google.', 'orto' ),
						esc_html( $djo_business['rating'] ),
						esc_html( $djo_business['rating_count'] )
					),
				)
			);
			?>

			<ul class="djo_reviews_grid">
				<?php foreach ( orto_child_get_reviews() as $djo_review ) { ?>
					<li class="djo_review">
						<?php echo orto_child_stars( $djo_review['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<blockquote class="djo_review_text"><p><?php echo esc_html( $djo_review['text'] ); ?></p></blockquote>
						<p class="djo_review_by">
							<span class="djo_review_name"><?php echo esc_html( $djo_review['name'] ); ?></span>
							<span class="djo_review_for"><?php echo esc_html( $djo_review['for'] ); ?></span>
						</p>
					</li>
				<?php } ?>
			</ul>
		</div>
	</section>


	<?php orto_child_cta_band(); ?>

</div>

<?php
get_footer();
