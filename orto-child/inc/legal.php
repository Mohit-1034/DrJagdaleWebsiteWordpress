<?php
/**
 * The legal pages: the privacy policy and the cookie policy
 *
 * Both are the same document in different words - a hero, a contents nav, a run
 * of numbered sections, and a date - so the layout is written once here and each
 * template passes only its own content. Adding a third policy later (terms,
 * complaints) is a data array and nothing else.
 *
 * WHAT THIS IS WRITTEN AGAINST
 *
 * The clinic is a healthcare provider in Maharashtra, so four things apply:
 *
 *   Digital Personal Data Protection Act, 2023 (DPDP Act). India's data
 *   protection statute. The clinic is a "Data Fiduciary", the patient is a
 *   "Data Principal". It requires notice, consent for a stated purpose, and
 *   gives patients rights of access, correction, erasure, nomination and
 *   grievance redressal. Note the sections are being brought into force in
 *   phases and the compliance deadline runs to 2027 - the policy is written to
 *   the Act as passed rather than waiting for that date.
 *
 *   IT Act 2000 s.43A and the SPDI Rules 2011. Still in force. These classify
 *   medical records and physical or mental health condition as "sensitive
 *   personal data or information", require a published privacy policy, and
 *   forbid keeping the data longer than the purpose requires.
 *
 *   Indian Medical Council (Professional Conduct, Etiquette and Ethics)
 *   Regulations 2002, regulation 1.3.1 - a physician must keep indoor patient
 *   records for three years from the commencement of treatment; 1.3.2 - records
 *   must be released to the patient within 72 hours of a request. Failing
 *   either is professional misconduct. This is why the retention section says
 *   three years and the access section says 72 hours: those numbers are not a
 *   house style, they are the doctor's own obligation.
 *
 *   Telemedicine Practice Guidelines 2020, where a consultation happens by
 *   phone or video.
 *
 * NOT LEGAL ADVICE. This is a careful, plain-language draft written from the
 * public text of those instruments. It has not been reviewed by a lawyer, and
 * it must be before the site goes live - particularly the grievance officer's
 * name, which is a statutory appointment and is left blank on purpose below
 * rather than filled with a guess.
 *
 * @package ORTO
 */

/**
 * The date shown at the foot of the legal pages.
 *
 * Set by hand rather than generated: "last updated" has to mean the date the
 * wording actually changed, not the date the page happened to be rendered.
 */
if ( ! defined( 'ORTO_CHILD_LEGAL_UPDATED' ) ) {
	define( 'ORTO_CHILD_LEGAL_UPDATED', '26 August 2026' );
}

/**
 * The anchor id a section is linked to from the contents nav.
 *
 * Derived from the title rather than written by hand, so a section can never
 * end up in the contents list pointing at an anchor that is not there.
 *
 * @param array $section One section from a legal document array.
 * @return string
 */
if ( ! function_exists( 'orto_child_legal_section_id' ) ) {
	function orto_child_legal_section_id( $section ) {
		if ( ! empty( $section['id'] ) ) {
			return $section['id'];
		}

		return sanitize_title( $section['title'] );
	}
}

/**
 * Render the body of a legal document.
 *
 * @param array $sections Sections, each with a title and any of lead, text,
 *                        list, table, and after (closing paragraphs, printed
 *                        last whatever else the section holds).
 */
if ( ! function_exists( 'orto_child_legal_render' ) ) {
	function orto_child_legal_render( $sections ) {
		$allowed = array(
			'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
			'strong' => array(),
			'em'     => array(),
			'br'     => array(),
		);

		$number = 0;

		foreach ( $sections as $section ) {
			$number++;
			?>
			<section class="djo_legal_section" id="<?php echo esc_attr( orto_child_legal_section_id( $section ) ); ?>">
				<h2 class="djo_legal_section_title">
					<span class="djo_legal_number"><?php echo esc_html( sprintf( '%02d', $number ) ); ?></span>
					<?php echo esc_html( $section['title'] ); ?>
				</h2>

				<?php if ( ! empty( $section['lead'] ) ) { ?>
					<p class="djo_legal_lead"><?php echo wp_kses( $section['lead'], $allowed ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $section['text'] ) ) { ?>
					<?php foreach ( $section['text'] as $paragraph ) { ?>
						<p><?php echo wp_kses( $paragraph, $allowed ); ?></p>
					<?php } ?>
				<?php } ?>

				<?php if ( ! empty( $section['list'] ) ) { ?>
					<ul class="djo_legal_list">
						<?php foreach ( $section['list'] as $item ) { ?>
							<li>
								<?php if ( is_array( $item ) ) { ?>
									<strong><?php echo esc_html( $item['term'] ); ?></strong>
									<?php echo wp_kses( $item['text'], $allowed ); ?>
								<?php } else { ?>
									<?php echo wp_kses( $item, $allowed ); ?>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>

				<?php if ( ! empty( $section['table'] ) ) { ?>
					<?php
					/*
					 * The cookie table. A table because it genuinely is one -
					 * four facts about each cookie that only make sense read
					 * across - and because that is the form a regulator and a
					 * reader both expect to find it in.
					 */
					?>
					<div class="djo_legal_table_wrap">
						<table class="djo_legal_table">
							<thead>
								<tr>
									<?php foreach ( $section['table']['head'] as $orto_child_legal_th ) { ?>
										<th scope="col"><?php echo esc_html( $orto_child_legal_th ); ?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $section['table']['rows'] as $orto_child_legal_tr ) { ?>
									<tr>
										<?php foreach ( $orto_child_legal_tr as $orto_child_legal_i => $orto_child_legal_td ) { ?>
											<?php if ( 0 === $orto_child_legal_i ) { ?>
												<th scope="row"><?php echo esc_html( $orto_child_legal_td ); ?></th>
											<?php } else { ?>
												<td><?php echo esc_html( $orto_child_legal_td ); ?></td>
											<?php } ?>
										<?php } ?>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				<?php } ?>
				<?php if ( ! empty( $section['after'] ) ) { ?>
					<?php
					/*
					 * Closing paragraphs, printed after everything else in the
					 * section. The renderer emits its parts in a fixed order -
					 * lead, text, list, table - which is right nearly every time
					 * and wrong for the sentence that comments on the list or the
					 * table it has to follow. An explicit slot is clearer than
					 * making the order depend on the order of the keys in the
					 * array, which is invisible at the call site.
					 */
					?>
					<?php foreach ( $section['after'] as $paragraph ) { ?>
						<p><?php echo wp_kses( $paragraph, $allowed ); ?></p>
					<?php } ?>
				<?php } ?>

			</section>
			<?php
		}
	}
}

/**
 * Render a whole legal page: hero, contents nav, body, and the date.
 *
 * @param array $args {
 *     @type string $eyebrow  Small label above the title.
 *     @type string $title    Page heading.
 *     @type array  $intro    Lead paragraphs, plain text.
 *     @type array  $sections Document sections.
 * }
 */
if ( ! function_exists( 'orto_child_legal_document' ) ) {
	function orto_child_legal_document( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'  => __( 'Legal', 'orto' ),
				'title'    => '',
				'intro'    => array(),
				'sections' => array(),
			)
		);
		?>
		<section class="djo_band djo_band_light djo_legal">
			<div class="content_wrap">
				<div class="djo_legal_layout">

					<?php
					/*
					 * The contents nav. Sticky on a wide screen, because these
					 * pages run long and a reader who wants section 9 should not
					 * have to scroll back to the top to find it again.
					 */
					?>
					<nav class="djo_legal_toc" aria-label="<?php esc_attr_e( 'On this page', 'orto' ); ?>">
						<h2 class="djo_legal_toc_title"><?php esc_html_e( 'On this page', 'orto' ); ?></h2>
						<ol class="djo_legal_toc_list">
							<?php foreach ( $args['sections'] as $orto_child_legal_section ) { ?>
								<li>
									<a href="#<?php echo esc_attr( orto_child_legal_section_id( $orto_child_legal_section ) ); ?>">
										<?php echo esc_html( $orto_child_legal_section['title'] ); ?>
									</a>
								</li>
							<?php } ?>
						</ol>
					</nav>

					<div class="djo_legal_body">
						<header class="djo_legal_head">
							<span class="djo_eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
							<h1 class="djo_legal_title"><?php echo esc_html( $args['title'] ); ?></h1>
							<?php foreach ( $args['intro'] as $orto_child_legal_p ) { ?>
								<p class="djo_legal_intro"><?php echo esc_html( $orto_child_legal_p ); ?></p>
							<?php } ?>
						</header>

						<?php orto_child_legal_render( $args['sections'] ); ?>

						<p class="djo_legal_updated">
							<?php
							printf(
								/* translators: %s: the date this policy was last changed. */
								esc_html__( 'Last updated: %s', 'orto' ),
								esc_html( ORTO_CHILD_LEGAL_UPDATED )
							);
							?>
						</p>
					</div>

				</div>
			</div>
		</section>
		<?php
	}
}
