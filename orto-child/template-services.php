<?php
/**
 * The template to display the Services page
 *
 * Same layout as the Speciality page, and the same renderer -
 * orto_child_render_group_page() in inc/nav.php. Only the wording differs.
 *
 * @package ORTO
 */

/*
Template Name: Services
*/

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>"
		<?php
		post_class( 'post_item_single post_type_page djo_page djo_page_services' );
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
			orto_child_render_group_page(
				'services',
				array(
					'eyebrow'     => __( 'Services', 'orto' ),
					'title'       => __( 'Everything the treatment needs, under one roof', 'orto' ),
					'text'        => __( 'Consultation, digital X-ray, surgery and physiotherapy in one clinic. A patient in pain should not spend a week being sent between three addresses to get one answer.', 'orto' ),
					'jump_label'  => __( 'Services on this page', 'orto' ),
					'list_title'  => __( 'What this includes', 'orto' ),
					'cta_eyebrow' => __( 'Book a consultation', 'orto' ),
					'cta_title'   => __( 'One visit, not three appointments.', 'orto' ),
					'cta_text'    => __( 'See the surgeon, have the X-ray taken and read in the same visit, and start physiotherapy next door.', 'orto' ),
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
