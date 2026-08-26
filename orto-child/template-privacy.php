<?php
/**
 * The template to display the Privacy Policy page
 *
 * The content is the array below; the layout is orto_child_legal_document() in
 * inc/legal.php, which the cookie policy shares.
 *
 * Written against the DPDP Act 2023, the SPDI Rules 2011 and the Indian Medical
 * Council (Professional Conduct, Etiquette and Ethics) Regulations 2002 - see
 * the header of inc/legal.php for what each of those contributes and for the
 * standing caveat that this is a careful draft, not legal advice, and needs a
 * lawyer's eye before launch.
 *
 * @package ORTO
 */

/*
Template Name: Privacy Policy
*/

$djo_business = orto_child_get_business();

$djo_contact_line = $djo_business['email']
	? sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $djo_business['email'] ) )
	: esc_html( $djo_business['phone'] );

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>"
		<?php
		post_class( 'post_item_single post_type_page djo_page djo_page_legal' );
		orto_add_seo_itemprops();
		?>
	>
		<?php
		do_action( 'orto_action_before_post_data' );
		orto_add_seo_snippets();
		do_action( 'orto_action_before_post_content' );
		?>

		<div class="post_content entry-content">
			<?php
			orto_child_legal_document(
				array(
					'eyebrow' => __( 'Legal', 'orto' ),
					'title'   => __( 'Privacy Policy', 'orto' ),
					'intro'   => array(
						sprintf(
							/* translators: %s: the clinic's name. */
							__( '%s ("the clinic", "we") treats bone, joint and spine problems in Nanded City, Pune. To do that we have to hold information about you, and some of it is as private as information gets.', 'orto' ),
							$djo_business['name']
						),
						__( 'This policy says plainly what we collect, why, who else ever sees it, how long we keep it and what you can ask us to do with it. It is written for patients rather than for lawyers. If anything here is unclear, ask us at the clinic and we will explain it.', 'orto' ),
					),
					'sections' => array(

						array(
							'title' => __( 'Who is responsible for your information', 'orto' ),
							'text'  => array(
								sprintf(
									/* translators: 1: doctor's name, 2: registration number. */
									__( 'The clinic is run by %1$s (Maharashtra Medical Council Reg. No. %2$s) at Shop No. S-66, Second Floor, Destination Center 1, Nanded City, Sinhgad Road, Pune 411068.', 'orto' ),
									$djo_business['doctor'],
									$djo_business['reg_number']
								),
								__( 'Under the Digital Personal Data Protection Act, 2023 the clinic is the <strong>Data Fiduciary</strong> for the information described here, and you are the <strong>Data Principal</strong>. In plain terms: we decide what is collected and why, and we answer for how it is looked after.', 'orto' ),
							),
						),

						array(
							'title' => __( 'What we collect', 'orto' ),
							'lead'  => __( 'Two quite different sets of information, held for different reasons and for different lengths of time.', 'orto' ),
							'list'  => array(
								array(
									'term' => __( 'Clinical records.', 'orto' ),
									'text' => __( 'Your name, age, sex, contact details, the complaint you came in with, examination findings, X-ray and other images taken at the clinic, diagnoses, prescriptions, procedure and operation notes, and physiotherapy records. Under the IT (SPDI) Rules 2011 this is <strong>sensitive personal data</strong>, and it is handled accordingly.', 'orto' ),
								),
								array(
									'term' => __( 'Enquiries from this website.', 'orto' ),
									'text' => __( 'If you send the appointment form, we receive your name, phone number, email address if you give one, what is troubling you, when suits you, and anything else you write in the message.', 'orto' ),
								),
								array(
									'term' => __( 'Payment records.', 'orto' ),
									'text' => __( 'What was charged and what was paid. The clinic does not store card numbers or bank details.', 'orto' ),
								),
								array(
									'term' => __( 'Website usage.', 'orto' ),
									'text' => __( 'Basic technical information your browser sends, and whatever the cookie policy describes. See the <a href="/cookie-policy/">Cookie Policy</a>.', 'orto' ),
								),
							),
							'after' => array(
								__( 'We do not ask for your Aadhaar number, and you should not send it through the website form.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Why we hold it, and on what basis', 'orto' ),
							'text'  => array(
								__( 'Clinical information is held so that you can be treated safely: so the surgeon can see what was found last time, compare this year\'s X-ray with the last one, and avoid prescribing something you have reacted to before. It is also held because a registered physician is required to keep it.', 'orto' ),
								__( 'Enquiry details are held for one purpose only - to call you back and arrange an appointment. We do not add you to a mailing list, and we do not sell, rent or trade anyone\'s details to anybody, ever.', 'orto' ),
								__( 'Where the law requires your consent, we ask for it and you may withdraw it at any time. Withdrawing consent does not undo treatment already given, and it does not override the record-keeping the clinic is separately obliged to do.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Who else ever sees it', 'orto' ),
							'lead'  => __( 'As few people as possible, and only where there is a reason.', 'orto' ),
							'list'  => array(
								__( 'Clinic staff - the surgeon, the physiotherapist and the reception desk - each seeing only what their work requires.', 'orto' ),
								__( 'Another doctor, hospital, laboratory or imaging centre, where you are being referred on and the information is needed for your care.', 'orto' ),
								__( 'Your insurer or TPA, where you are making a claim and have asked us to support it.', 'orto' ),
								__( 'A court, the police, or a public authority, where the law obliges us to disclose.', 'orto' ),
							),
							'after' => array(
								__( 'That is the whole list. Nobody buys this data from us because it is not for sale.', 'orto' ),
							),
						),

						array(
							'title' => __( 'How long we keep it', 'orto' ),
							'text'  => array(
								__( 'Clinical records are kept for at least <strong>three years from the commencement of treatment</strong>. That period is set by regulation 1.3.1 of the Indian Medical Council (Professional Conduct, Etiquette and Ethics) Regulations, 2002, and failing to keep them is professional misconduct - so it is not a period we can shorten on request.', 'orto' ),
								__( 'Where a record relates to an ongoing course of treatment, an implant, or a matter that is or may become the subject of a legal claim, it is kept for as long as that reason lasts.', 'orto' ),
								__( 'Website enquiries that do not turn into an appointment are deleted once they have been answered and are no longer needed - in practice, within twelve months.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Your rights', 'orto' ),
							'lead'  => __( 'The DPDP Act 2023 gives you the following, and the clinic will not make you justify asking.', 'orto' ),
							'list'  => array(
								array(
									'term' => __( 'A copy of your records.', 'orto' ),
									'text' => __( 'Ask, and we will give you a copy. Regulation 1.3.2 of the 2002 Regulations requires a doctor to release medical records <strong>within 72 hours</strong> of a request by the patient or their authorised attendant, and that is the standard we hold ourselves to.', 'orto' ),
								),
								array(
									'term' => __( 'Correction.', 'orto' ),
									'text' => __( 'If a detail is wrong - a misspelled name, a wrong date of birth - tell us and it will be corrected. A clinical finding cannot be deleted because you disagree with it, but your disagreement can be recorded alongside it.', 'orto' ),
								),
								array(
									'term' => __( 'Erasure.', 'orto' ),
									'text' => __( 'You may ask us to delete your information, and we will, except where we are legally required to keep it for the period described above.', 'orto' ),
								),
								array(
									'term' => __( 'Nomination.', 'orto' ),
									'text' => __( 'You may nominate someone to exercise these rights on your behalf if you die or become unable to act for yourself.', 'orto' ),
								),
								array(
									'term' => __( 'Grievance redressal.', 'orto' ),
									'text' => __( 'You may complain to us first, and to the Data Protection Board of India if we do not put it right.', 'orto' ),
								),
							),
						),

						array(
							'title' => __( 'How it is kept safe', 'orto' ),
							'text'  => array(
								__( 'Paper records are kept in the clinic in locked storage. Digital records and images sit on password-protected systems that only clinic staff can reach, and access is limited to what each person\'s work requires. This website is served over an encrypted (HTTPS) connection.', 'orto' ),
								__( 'No system is perfect, and we would rather say so than claim otherwise. If a breach ever occurred that was likely to affect you, we would tell you and report it as the DPDP Act requires.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Children', 'orto' ),
							'text'  => array(
								__( 'The clinic treats children, and their records are held on the same terms as anyone else\'s. Where the patient is under 18, consent is given by a parent or guardian, and information is shared with them rather than with the child.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Consultations by phone or video', 'orto' ),
							'text'  => array(
								__( 'Where a consultation happens by telephone or video, it is conducted under the Telemedicine Practice Guidelines, 2020. The same record is made as for a visit to the clinic, and it is held on the same terms as everything else described here.', 'orto' ),
								__( 'Please do not send clinical information over social media messages. It is not a secure channel, and we cannot control who else can see it.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Contacting us, and complaining', 'orto' ),
							'text'  => array(
								sprintf(
									/* translators: %s: an email link or a phone number. */
									__( 'For anything in this policy - a copy of your records, a correction, a deletion, or a complaint - contact the clinic on %s, or come to the reception desk.', 'orto' ),
									$djo_contact_line
								),
								__( '<strong>Grievance Officer:</strong> to be appointed. The DPDP Act requires a named contact for data grievances, and this policy will carry that name and their direct contact details before the site goes live. Until then, use the clinic\'s own contact details above and your request will be handled by the surgeon.', 'orto' ),
								__( 'If we do not resolve your grievance, you may escalate it to the Data Protection Board of India.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Changes to this policy', 'orto' ),
							'text'  => array(
								__( 'If this policy changes in a way that affects you, the date at the foot of this page changes with it. It is worth a glance if you have not read it for a while.', 'orto' ),
							),
						),
					),
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
