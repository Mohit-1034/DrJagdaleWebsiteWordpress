<?php
/**
 * The template to display the About Us page
 *
 * All content here is hardcoded rather than stored in the database, so it
 * survives a database wipe or a migration. The facts - qualifications, training
 * and registration - come from the clinic's own signage and printed card and
 * from the doctor's public medical profiles; correct them here and the footer
 * follows, because both read orto_child_get_business().
 *
 * @package ORTO
 */

/*
Template Name: About
*/

$djo_business = orto_child_get_business();

/*
 * Training, in the order it happened. Kept in the template rather than in
 * functions.php because this is the only page that shows it - there is no
 * second reader to keep in step.
 */
$djo_education = array(
	array(
		'year'  => '2002',
		'title' => __( 'M.B.B.S.', 'orto' ),
		'place' => __( 'Dr. V. M. Medical College, Solapur', 'orto' ),
	),
	array(
		'year'  => '2013',
		'title' => __( 'Diploma in Trauma and Orthopaedic Surgery', 'orto' ),
		'place' => __( 'Hardikar Hospital, Pune', 'orto' ),
	),
	array(
		'year'  => '2014',
		'title' => __( 'Diploma in Orthopaedics', 'orto' ),
		'place' => __( 'University of Mumbai', 'orto' ),
	),
);

/*
 * What the clinic promises, as three plain statements rather than adjectives.
 * Each is something a patient could hold the clinic to.
 */
$djo_values = array(
	array(
		'icon'  => 'clock',
		'title' => __( 'One visit, not three', 'orto' ),
		'text'  => __( 'Consultation, digital X-ray and physiotherapy are all in this clinic. You are not sent across Pune for an image and back again a week later to be told what it showed.', 'orto' ),
	),
	array(
		'icon'  => 'shield',
		'title' => __( 'Surgery last, not first', 'orto' ),
		'text'  => __( 'Most orthopaedic pain answers to therapy, bracing and time. An operation is recommended when it is genuinely the better option - and the reasoning is explained, not asserted.', 'orto' ),
	),
	array(
		'icon'  => 'badge',
		'title' => __( 'Plain language, every time', 'orto' ),
		'text'  => __( 'You leave knowing what is wrong, what the plan is, how long it should take and what it will cost. If a word needs explaining, it gets explained.', 'orto' ),
	),
);

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>"
		<?php
		post_class( 'post_item_single post_type_page djo_page djo_page_about' );
		orto_add_seo_itemprops();
		?>
	>

		<?php
		do_action( 'orto_action_before_post_data' );

		orto_add_seo_snippets();

		do_action( 'orto_action_before_post_content' );
		?>

		<div class="post_content entry-content">

			<?php /* ------------------------------------------------- Hero */ ?>
			<section class="djo_page_hero">
				<?php
				orto_child_section_head(
					array(
						'tag'     => 'h1',
						'eyebrow' => __( 'About us', 'orto' ),
						'title'   => __( 'A fracture clinic built around one visit', 'orto' ),
						'text'    => __( 'Dr. Jagadale\'s Orthocare & Fracture Clinic has been treating bone and joint injury in Nanded City since 2020 - with the consulting room, the X-ray suite and the physiotherapy department all on the same floor.', 'orto' ),
					)
				);
				?>
			</section>

			<?php /* ------------------------------------------ The surgeon */ ?>
			<section class="djo_section djo_about_band">
				<div class="djo_split">

					<div class="djo_split_media">
						<?php $djo_about_image = orto_child_image_url( 'clinic-entrance' ); ?>
						<?php if ( $djo_about_image ) { ?>
							<img src="<?php echo esc_url( $djo_about_image ); ?>" alt="<?php esc_attr_e( 'The entrance to the clinic in Destination Center 1, Nanded City', 'orto' ); ?>" loading="lazy" decoding="async">
						<?php } ?>
					</div>

					<div class="djo_split_body">
						<span class="djo_eyebrow"><?php esc_html_e( 'The surgeon', 'orto' ); ?></span>
						<h2 class="djo_split_title"><?php echo esc_html( $djo_business['doctor'] ); ?></h2>
						<p class="djo_split_creds">
							<?php echo esc_html( $djo_business['doctor_creds'] . ' &middot; ' . $djo_business['doctor_role'] ); ?>
						</p>

						<p>
							<?php
							printf(
								/* translators: %s: number of years in practice. */
								esc_html__( 'Dr. Ganesh Jagadale has practised orthopaedics for %s years. He trained in medicine at Dr. V. M. Medical College, Solapur, then in trauma and orthopaedic surgery at Hardikar Hospital in Pune, and holds a Diploma in Orthopaedics from the University of Mumbai.', 'orto' ),
								esc_html( $djo_business['experience'] )
							);
							?>
						</p>
						<p>
							<?php esc_html_e( 'He began as Registrar in Orthopaedics at the clinic\'s first practice in Dhayari before opening the Nanded City clinic, and his work covers the full range of the speciality: fractures and dislocations in adults and children, arthroscopic and joint replacement surgery, spine surgery, and the long, unglamorous rehabilitation that decides how well any of it actually turns out.', 'orto' ); ?>
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
								<?php echo orto_child_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php
								printf(
									/* translators: 1: rating out of five, 2: number of reviews. */
									esc_html__( 'Rated %1$s from %2$s Google reviews', 'orto' ),
									esc_html( $djo_business['rating'] ),
									esc_html( $djo_business['rating_count'] )
								);
								?>
							</li>
						</ul>
					</div>

				</div>
			</section>

			<?php /* ----------------------------------------- Education */ ?>
			<section class="djo_section djo_education">
				<?php
				orto_child_section_head(
					array(
						'eyebrow' => __( 'Training', 'orto' ),
						'title'   => __( 'Qualifications', 'orto' ),
					)
				);
				?>

				<ol class="djo_timeline">
					<?php foreach ( $djo_education as $djo_step ) { ?>
						<li class="djo_timeline_item">
							<span class="djo_timeline_year"><?php echo esc_html( $djo_step['year'] ); ?></span>
							<span class="djo_timeline_body">
								<span class="djo_timeline_title"><?php echo esc_html( $djo_step['title'] ); ?></span>
								<span class="djo_timeline_place"><?php echo esc_html( $djo_step['place'] ); ?></span>
							</span>
						</li>
					<?php } ?>
				</ol>
			</section>

			<?php /* -------------------------------------- What we stand for */ ?>
			<section class="djo_section djo_values">
				<?php
				orto_child_section_head(
					array(
						'eyebrow' => __( 'How we work', 'orto' ),
						'title'   => __( 'Three things you can hold us to', 'orto' ),
					)
				);
				?>

				<ul class="djo_values_grid">
					<?php foreach ( $djo_values as $djo_value ) { ?>
						<li class="djo_value">
							<span class="djo_value_icon" aria-hidden="true"><?php echo orto_child_icon( $djo_value['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3 class="djo_value_title"><?php echo esc_html( $djo_value['title'] ); ?></h3>
							<p class="djo_value_text"><?php echo esc_html( $djo_value['text'] ); ?></p>
						</li>
					<?php } ?>
				</ul>
			</section>

			<?php /* ------------------------------------------ The facilities */ ?>
			<section class="djo_section djo_facilities">
				<?php
				orto_child_section_head(
					array(
						'eyebrow' => __( 'The clinic', 'orto' ),
						'title'   => __( 'What is on the floor', 'orto' ),
						'text'    => __( 'Second floor, Destination Center 1 - consulting room, digital X-ray suite and physiotherapy department, with parking below.', 'orto' ),
					)
				);
				?>

				<ul class="djo_facilities_grid">
					<?php
					$djo_facilities = array(
						array( 'image' => 'imaging', 'title' => __( 'Digital X-ray suite', 'orto' ), 'text' => __( 'Images taken and read during the same consultation.', 'orto' ) ),
						array( 'image' => 'physiotherapy', 'title' => __( 'Physiotherapy department', 'orto' ), 'text' => __( 'The rehabilitation plan is carried out next door, not referred out.', 'orto' ) ),
						array( 'image' => 'bone-density', 'title' => __( 'Bone density screening', 'orto' ), 'text' => __( 'Osteoporosis found before it becomes a fracture.', 'orto' ) ),
					);

					foreach ( $djo_facilities as $djo_facility ) {
						$djo_facility_image = orto_child_image_url( $djo_facility['image'] );
						?>
						<li class="djo_facility">
							<?php if ( $djo_facility_image ) { ?>
								<span class="djo_facility_media">
									<img src="<?php echo esc_url( $djo_facility_image ); ?>" alt="" loading="lazy" decoding="async">
								</span>
							<?php } ?>
							<h3 class="djo_facility_title"><?php echo esc_html( $djo_facility['title'] ); ?></h3>
							<p class="djo_facility_text"><?php echo esc_html( $djo_facility['text'] ); ?></p>
						</li>
						<?php
					}
					?>
				</ul>
			</section>

			<?php
			orto_child_cta_band(
				array(
					'eyebrow' => __( 'Visit the clinic', 'orto' ),
					'title'   => __( 'Second floor, Destination Center 1, Nanded City', 'orto' ),
					'text'    => __( 'Open seven days a week, 11:00 AM to 8:00 PM. Call ahead or send an enquiry and we will call you back.', 'orto' ),
				)
			);
			?>

		</div>

		<?php
		do_action( 'orto_action_after_post_content' );

		do_action( 'orto_action_after_post_data' );
		?>

	</article>
	<?php
}

get_footer();
