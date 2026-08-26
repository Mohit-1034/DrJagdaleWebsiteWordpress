<?php
/**
 * The navigation tree: what the clinic treats, and what it does about it
 *
 * Four things read these arrays - the desktop mega menu, the mobile menu, the
 * Speciality page and the Services page - so a treatment is written down once
 * and appears in all four. Adding one is a matter of adding an entry here.
 *
 * Two groups, and the difference between them is deliberate:
 *
 *   'speciality' - the part of the body that hurts. This is how a patient
 *                  arrives: "my knee has been bad for a year". Entries carry a
 *                  list of the conditions treated at that joint.
 *
 *   'services'   - what the clinic does. This is how a referrer or a returning
 *                  patient arrives: "I need an X-ray", "I was told I need a
 *                  replacement". Entries carry a list of what that service
 *                  includes.
 *
 * The service list is the clinic's own, taken from its signage:
 * fractures and dislocations, orthopaedic conditions and diseases, joint
 * replacement, arthroscopy, spine surgery, digital X-ray and physiotherapy.
 *
 * 'icon' is a file name in images/icons/. 'slug' is the anchor a menu item
 * links to on the group's page, so it must be unique within its group.
 *
 * @package ORTO
 */

if ( ! function_exists( 'orto_child_get_nav_group' ) ) {
	/**
	 * One navigation group.
	 *
	 * @param string $group 'speciality' or 'services'.
	 * @return array List of entries, or an empty array for an unknown group.
	 */
	function orto_child_get_nav_group( $group ) {
		$groups = orto_child_get_nav_groups();

		return isset( $groups[ $group ] ) ? $groups[ $group ] : array();
	}
}

if ( ! function_exists( 'orto_child_get_nav_groups' ) ) {
	/**
	 * Both navigation groups.
	 *
	 * @return array
	 */
	function orto_child_get_nav_groups() {
		return apply_filters(
			'orto_child_nav_groups',
			array(

				/* ------------------------------------------------------------
				 * Speciality - by joint
				 * ---------------------------------------------------------- */
				'speciality' => array(

					array(
						'slug'  => 'knee-pain',
						'title' => __( 'Knee Pain Treatment', 'orto' ),
						'icon'  => 'knee',
						'text'  => __( 'From the first twinge on the stairs to a knee that will no longer take your weight - assessed on the day, imaged on site, and treated without jumping straight to surgery.', 'orto' ),
						'items' => array(
							__( 'Osteoarthritis of the knee', 'orto' ),
							__( 'Ligament and ACL injuries', 'orto' ),
							__( 'Meniscus tears', 'orto' ),
							__( 'Total and partial knee replacement', 'orto' ),
							__( 'Knee arthroscopy', 'orto' ),
							__( 'Knee braces for osteoarthritis', 'orto' ),
						),
					),

					array(
						'slug'  => 'shoulder-pain',
						'title' => __( 'Shoulder Pain Treatment', 'orto' ),
						'icon'  => 'shoulder',
						'text'  => __( 'Shoulders stiffen quietly and then all at once. Early assessment is what keeps a frozen shoulder from costing you a year of movement.', 'orto' ),
						'items' => array(
							__( 'Frozen shoulder', 'orto' ),
							__( 'Rotator cuff injuries', 'orto' ),
							__( 'Shoulder dislocation and instability', 'orto' ),
							__( 'Shoulder arthroscopy', 'orto' ),
							__( 'Shoulder impingement', 'orto' ),
						),
					),

					array(
						'slug'  => 'hand-wrist-pain',
						'title' => __( 'Hand & Wrist Pain', 'orto' ),
						'icon'  => 'hand',
						'text'  => __( 'The hand is where a small problem becomes a daily one. Numbness, a locking finger or a wrist that aches at a keyboard all have straightforward answers.', 'orto' ),
						'items' => array(
							__( 'Carpal tunnel syndrome', 'orto' ),
							__( 'Trigger finger', 'orto' ),
							__( 'Wrist fractures', 'orto' ),
							__( 'Ganglion cysts', 'orto' ),
							__( 'Hand pain treatment', 'orto' ),
						),
					),

					array(
						'slug'  => 'hip-pain',
						'title' => __( 'Hip Pain Treatment', 'orto' ),
						'icon'  => 'hip',
						'text'  => __( 'Hip pain in an older patient is often a fracture and always urgent. In a younger one it is usually arthritis or impingement - and both are treatable.', 'orto' ),
						'items' => array(
							__( 'Hip osteoarthritis', 'orto' ),
							__( 'Total hip replacement', 'orto' ),
							__( 'Hip fractures in the elderly', 'orto' ),
							__( 'Avascular necrosis', 'orto' ),
							__( 'Joint dislocation', 'orto' ),
						),
					),

					array(
						'slug'  => 'foot-ankle-pain',
						'title' => __( 'Foot & Ankle Pain', 'orto' ),
						'icon'  => 'foot',
						'text'  => __( 'Feet carry everything else. Heel pain, a sprain that never quite settled, or a diabetic foot that needs watching - all seen here.', 'orto' ),
						'items' => array(
							__( 'Ankle sprains and ligament injury', 'orto' ),
							__( 'Plantar fasciitis and heel pain', 'orto' ),
							__( 'Ankle and foot fractures', 'orto' ),
							__( 'Flat foot and gait problems', 'orto' ),
							__( 'Diabetic foot care', 'orto' ),
						),
					),

					array(
						'slug'  => 'elbow-pain',
						'title' => __( 'Elbow Pain Treatment', 'orto' ),
						'icon'  => 'elbow',
						'text'  => __( 'Tennis elbow rarely comes from tennis. It comes from work - and it responds well to the right combination of rest, therapy and injection.', 'orto' ),
						'items' => array(
							__( 'Tennis elbow', 'orto' ),
							__( 'Golfer\'s elbow', 'orto' ),
							__( 'Elbow fractures and dislocation', 'orto' ),
							__( 'Cubital tunnel syndrome', 'orto' ),
						),
					),

					array(
						'slug'  => 'back-neck-pain',
						'title' => __( 'Back & Neck Pain', 'orto' ),
						'icon'  => 'back',
						'text'  => __( 'Most back pain is mechanical and gets better. The job of the first consultation is to tell that apart from the small share that will not.', 'orto' ),
						'items' => array(
							__( 'Lower back pain (lumbago)', 'orto' ),
							__( 'Cervical spondylosis', 'orto' ),
							__( 'Slipped disc and sciatica', 'orto' ),
							__( 'Neck pain treatment', 'orto' ),
							__( 'Spinal therapy', 'orto' ),
						),
					),

					array(
						'slug'  => 'bone-health',
						'title' => __( 'Bone Health & Osteoporosis', 'orto' ),
						'icon'  => 'joint',
						'text'  => __( 'Thinning bone gives no warning until something breaks. Screening finds it while it is still a prescription rather than a fracture.', 'orto' ),
						'items' => array(
							__( 'Osteoporosis screening', 'orto' ),
							__( 'Bone mineral density assessment', 'orto' ),
							__( 'Vitamin D and calcium management', 'orto' ),
							__( 'Fracture prevention in the elderly', 'orto' ),
						),
					),
				),

				/* ------------------------------------------------------------
				 * Services - what the clinic does
				 * ---------------------------------------------------------- */
				'services'   => array(

					array(
						'slug'  => 'fracture-trauma',
						'title' => __( 'Fracture & Trauma Care', 'orto' ),
						'icon'  => 'fracture',
						'text'  => __( 'Fractures and dislocations in adults and children, from the first X-ray to the last follow-up - all in one place, on the same day.', 'orto' ),
						'items' => array(
							__( 'Closed reduction and casting', 'orto' ),
							__( 'Fracture fixation surgery', 'orto' ),
							__( 'Joint dislocation reduction', 'orto' ),
							__( 'Paediatric fractures', 'orto' ),
							__( 'Sports injuries', 'orto' ),
						),
					),

					array(
						'slug'  => 'joint-replacement',
						'title' => __( 'Joint Replacement Surgery', 'orto' ),
						'icon'  => 'joint-replacement',
						'text'  => __( 'Hip and knee replacement for arthritis that no longer answers to therapy - recommended only once the simpler options have genuinely been tried.', 'orto' ),
						'items' => array(
							__( 'Total knee replacement', 'orto' ),
							__( 'Partial knee replacement', 'orto' ),
							__( 'Total hip replacement', 'orto' ),
							__( 'Revision joint surgery', 'orto' ),
							__( 'Pre- and post-operative rehabilitation', 'orto' ),
						),
					),

					array(
						'slug'  => 'arthroscopy',
						'title' => __( 'Arthroscopic Surgery', 'orto' ),
						'icon'  => 'arthroscopy',
						'text'  => __( 'Keyhole surgery through two small openings instead of one long incision - less to recover from, and a faster return to work.', 'orto' ),
						'items' => array(
							__( 'Knee arthroscopy', 'orto' ),
							__( 'Shoulder arthroscopy', 'orto' ),
							__( 'ACL and ligament reconstruction', 'orto' ),
							__( 'Meniscus repair', 'orto' ),
						),
					),

					array(
						'slug'  => 'spine-surgery',
						'title' => __( 'Spine Surgery', 'orto' ),
						'icon'  => 'spine',
						'text'  => __( 'For the small share of back problems that will not settle on their own - assessed carefully, and operated on only when it is the right answer.', 'orto' ),
						'items' => array(
							__( 'Slipped disc surgery', 'orto' ),
							__( 'Spinal decompression', 'orto' ),
							__( 'Spinal fusion', 'orto' ),
							__( 'Sciatica management', 'orto' ),
						),
					),

					array(
						'slug'  => 'digital-xray',
						'title' => __( 'Digital X-Ray Centre', 'orto' ),
						'icon'  => 'xray',
						'text'  => __( 'A digital X-ray suite inside the clinic. Your image is on the screen during the same consultation - no second appointment, no trip to a separate lab.', 'orto' ),
						'items' => array(
							__( 'Digital X-ray, on site', 'orto' ),
							__( 'Same-visit reporting', 'orto' ),
							__( 'Bone mineral density screening', 'orto' ),
							__( 'Pre-operative imaging', 'orto' ),
						),
					),

					array(
						'slug'  => 'physiotherapy',
						'title' => __( 'Physiotherapy & Rehabilitation', 'orto' ),
						'icon'  => 'physiotherapy',
						'text'  => __( 'An in-house physiotherapy department, so the plan made in the consulting room is the plan carried out next door.', 'orto' ),
						'items' => array(
							__( 'Post-operative rehabilitation', 'orto' ),
							__( 'Sports injury rehabilitation', 'orto' ),
							__( 'Electrotherapy and manual therapy', 'orto' ),
							__( 'Posture and gait correction', 'orto' ),
						),
					),

					array(
						'slug'  => 'spinal-therapy',
						'title' => __( 'Spinal Therapy', 'orto' ),
						'icon'  => 'spinal-therapy',
						'text'  => __( 'Non-surgical treatment for disc and nerve pain: traction, targeted exercise and, where it helps, injection.', 'orto' ),
						'items' => array(
							__( 'Spinal traction', 'orto' ),
							__( 'Core strengthening programmes', 'orto' ),
							__( 'Epidural and facet joint injections', 'orto' ),
							__( 'Cervical and lumbar therapy', 'orto' ),
						),
					),

					array(
						'slug'  => 'braces-supports',
						'title' => __( 'Braces & Orthopaedic Supports', 'orto' ),
						'icon'  => 'strength',
						'text'  => __( 'Fitted at the clinic rather than bought off a shelf, because a brace that does not fit is a brace nobody wears.', 'orto' ),
						'items' => array(
							__( 'Knee braces for osteoarthritis', 'orto' ),
							__( 'Cervical collars and lumbar belts', 'orto' ),
							__( 'Walking aids and splints', 'orto' ),
							__( 'Post-operative supports', 'orto' ),
						),
					),
				),
			)
		);
	}
}


/* ---------------------------------------------------------------------------
 * The page that renders a group
 *
 * Speciality and Services are the same page with different data in it: a hero,
 * a jump list, one section per entry, and a call to action. Rather than keeping
 * two copies of that layout in two templates and watching them drift, both
 * templates are four lines long and call this.
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'orto_child_render_group_page' ) ) {
	/**
	 * Render a navigation group as a full page.
	 *
	 * @param string $group 'speciality' or 'services'.
	 * @param array  $copy {
	 *     The wording that differs between the two pages.
	 *
	 *     @type string $eyebrow      Small line above the page title.
	 *     @type string $title        The page's h1.
	 *     @type string $text         The standfirst under it.
	 *     @type string $jump_label   aria-label for the contents nav.
	 *     @type string $list_title   Heading above each entry's list.
	 *     @type string $cta_eyebrow  CTA band eyebrow.
	 *     @type string $cta_title    CTA band heading.
	 *     @type string $cta_text     CTA band sentence.
	 * }
	 */
	function orto_child_render_group_page( $group, $copy = array() ) {
		$business = orto_child_get_business();
		$entries  = orto_child_get_nav_group( $group );

		if ( empty( $entries ) ) {
			return;
		}

		$copy = wp_parse_args(
			$copy,
			array(
				'eyebrow'     => '',
				'title'       => '',
				'text'        => '',
				'jump_label'  => __( 'Sections on this page', 'orto' ),
				'list_title'  => __( 'What this includes', 'orto' ),
				'cta_eyebrow' => __( 'Book a consultation', 'orto' ),
				'cta_title'   => __( 'Walk in with pain. Walk out with a plan.', 'orto' ),
				'cta_text'    => __( 'Same-day consultations, digital X-ray on site, and a treatment plan explained before anything begins.', 'orto' ),
			)
		);
		?>

		<section class="djo_page_hero djo_band djo_band_light">
			<div class="content_wrap">
			<?php
			orto_child_section_head(
				array(
					'tag'     => 'h1',
					'eyebrow' => $copy['eyebrow'],
					'title'   => $copy['title'],
					'text'    => $copy['text'],
				)
			);
			?>

			<?php
			/*
			 * A contents list rather than an ordinary intro paragraph. These
			 * pages are long by design - eight entries, each with its own list -
			 * and this is what makes them navigable without scrolling past seven
			 * sections to reach the eighth.
			 */
			?>
			<nav class="djo_jump" aria-label="<?php echo esc_attr( $copy['jump_label'] ); ?>">
				<ul class="djo_jump_list">
					<?php foreach ( $entries as $entry ) { ?>
						<li>
							<a class="djo_jump_link" href="#<?php echo esc_attr( $entry['slug'] ); ?>">
								<?php echo orto_child_menu_icon( $entry['icon'], 'djo_jump_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo esc_html( $entry['title'] ); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</nav>
			</div>
		</section>

		<?php foreach ( $entries as $index => $entry ) { ?>
			<?php
			/*
			 * Alternating full-width bands, light and dark, all the way down the
			 * page. Eight identical white blocks read as one undifferentiated
			 * wall; alternating grounds give the reader a place in the list
			 * without a single rule being drawn.
			 */
			?>
			<section class="djo_entry djo_band <?php echo ( $index % 2 ) ? 'djo_band_dark' : 'djo_band_light'; ?>" id="<?php echo esc_attr( $entry['slug'] ); ?>">
				<div class="content_wrap">
				<div class="djo_entry_inner">

					<div class="djo_entry_head">
						<span class="djo_entry_icon" aria-hidden="true">
							<?php echo orto_child_menu_icon( $entry['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h2 class="djo_entry_title"><?php echo esc_html( $entry['title'] ); ?></h2>
					</div>

					<p class="djo_entry_text"><?php echo esc_html( $entry['text'] ); ?></p>

					<h3 class="djo_entry_subtitle"><?php echo esc_html( $copy['list_title'] ); ?></h3>
					<ul class="djo_entry_list">
						<?php foreach ( $entry['items'] as $item ) { ?>
							<li>
								<span class="djo_entry_tick" aria-hidden="true"><?php echo orto_child_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php echo esc_html( $item ); ?>
							</li>
						<?php } ?>
					</ul>

					<p class="djo_entry_actions">
						<a class="djo_button djo_button_outline" href="<?php echo esc_url( orto_child_page_url( 'contact-us' ) ); ?>">
							<?php esc_html_e( 'Book a consultation', 'orto' ); ?>
						</a>
						<?php if ( ! empty( $business['phone'] ) ) { ?>
							<a class="djo_entry_phone" href="tel:<?php echo esc_attr( $business['phone_link'] ); ?>">
								<?php echo orto_child_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo esc_html( $business['phone'] ); ?>
							</a>
						<?php } ?>
					</p>

				</div>
				</div>
			</section>
		<?php } ?>

		<?php
		orto_child_cta_band(
			array(
				'eyebrow' => $copy['cta_eyebrow'],
				'title'   => $copy['cta_title'],
				'text'    => $copy['cta_text'],
			)
		);
	}
}
