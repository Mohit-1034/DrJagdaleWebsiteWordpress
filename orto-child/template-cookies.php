<?php
/**
 * The template to display the Cookie Policy page
 *
 * Layout: orto_child_legal_document() in inc/legal.php, shared with the privacy
 * policy. Content is the array below.
 *
 * The cookie table describes what this site sets TODAY. That is a short list,
 * because the site runs no analytics, no advertising pixels and no embedded
 * video - and a cookie policy that lists trackers a site does not have is worse
 * than none, because nobody can then tell which entries are real.
 *
 * If analytics or a chat widget is ever added, this table must be updated in
 * the same commit, and a consent banner will be needed before those cookies are
 * allowed to load.
 *
 * @package ORTO
 */

/*
Template Name: Cookie Policy
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
					'title'   => __( 'Cookie Policy', 'orto' ),
					'intro'   => array(
						__( 'A cookie is a small file a website asks your browser to keep. Most of the internet uses them to follow people around; this site uses almost none, and this page says exactly which and why.', 'orto' ),
						__( 'It is a short page because it is an honest one. If that changes - if the clinic ever adds analytics or a booking widget - this page changes on the same day.', 'orto' ),
					),
					'sections' => array(

						array(
							'title' => __( 'The short version', 'orto' ),
							'text'  => array(
								__( 'This website sets <strong>no advertising cookies, no analytics cookies and no social media trackers</strong>. Nothing here follows you to another website. There is no consent banner because there is nothing to ask you to consent to.', 'orto' ),
								__( 'The only cookies set are the ones WordPress needs to run the site, and they appear only if you leave a comment or log in to the administration area. An ordinary visitor reading these pages is not given a cookie at all.', 'orto' ),
							),
						),

						array(
							'title' => __( 'What is actually set', 'orto' ),
							'lead'  => __( 'The complete list, as at the date at the foot of this page.', 'orto' ),
							'table' => array(
								'head' => array(
									__( 'Cookie', 'orto' ),
									__( 'Set when', 'orto' ),
									__( 'What it does', 'orto' ),
									__( 'How long', 'orto' ),
								),
								'rows' => array(
									array(
										'wordpress_logged_in_*',
										__( 'You log in to wp-admin', 'orto' ),
										__( 'Keeps a staff member signed in. Never set for a patient reading the site.', 'orto' ),
										__( 'Until you log out', 'orto' ),
									),
									array(
										'wp-settings-*',
										__( 'You log in to wp-admin', 'orto' ),
										__( 'Remembers an administrator\'s own screen preferences.', 'orto' ),
										__( '1 year', 'orto' ),
									),
									array(
										'comment_author_*',
										__( 'You leave a comment on a post', 'orto' ),
										__( 'Fills your name and email in again next time so you do not retype them.', 'orto' ),
										__( '1 year', 'orto' ),
									),
								),
							),
							'after' => array(
								__( 'All three are "strictly necessary" - they make a function you asked for work, and they are not used to build any picture of you.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Google Maps on the Contact page', 'orto' ),
							'text'  => array(
								__( 'The Contact page shows a map of where the clinic is. That map comes from Google, and loading it means your browser contacts Google\'s servers - which lets Google see your IP address and may let it set cookies of its own, under its own policy rather than ours.', 'orto' ),
								__( 'The map is loaded lazily, so nothing is requested from Google until you actually scroll down to it. If you would rather not contact Google at all, do not scroll to the foot of the Contact page - the clinic\'s full address is written out above it in text, and the phone numbers are on every page.', 'orto' ),
							),
						),

						array(
							'title' => __( 'What this site does not do', 'orto' ),
							'list'  => array(
								__( 'No Google Analytics, and no analytics of any other kind.', 'orto' ),
								__( 'No Facebook, Instagram or advertising pixels.', 'orto' ),
								__( 'No embedded video, and no chat widget.', 'orto' ),
								__( 'No profiling, no behavioural advertising, and nothing sold or shared with a data broker.', 'orto' ),
							),
							'after' => array(
								__( 'The links to the clinic\'s Facebook, Instagram and WhatsApp are ordinary links. Nothing is loaded from those services unless you click one, and then you are on their site under their terms.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Turning cookies off', 'orto' ),
							'text'  => array(
								__( 'Every browser lets you block or delete cookies - usually under Settings, then Privacy. Because this site sets none for an ordinary visitor, blocking them will not stop any part of it working for you. The only people affected are clinic staff signing in to update the site.', 'orto' ),
							),
						),

						array(
							'title' => __( 'Questions', 'orto' ),
							'text'  => array(
								sprintf(
									/* translators: %s: an email link or a phone number. */
									__( 'Ask the clinic on %s. How the clinic handles your health information is a separate and much longer story, and it is told in the <a href="/privacy-policy/">Privacy Policy</a>.', 'orto' ),
									$djo_contact_line
								),
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
