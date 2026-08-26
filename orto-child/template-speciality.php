<?php
/**
 * The template to display the Speciality page
 *
 * The layout - hero, contents nav, one section per entry, call to action - is
 * shared with the Services page and lives in orto_child_render_group_page()
 * in inc/nav.php. Only the wording differs, so only the wording is here.
 *
 * The sections carry the anchor ids the mega menu links to, so an item in the
 * Speciality panel lands the visitor on the right part of this page rather
 * than at the top of it.
 *
 * @package ORTO
 */

/*
Template Name: Speciality
*/

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>"
		<?php
		post_class( 'post_item_single post_type_page djo_page djo_page_speciality' );
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
				'speciality',
				array(
					'eyebrow'     => __( 'Speciality', 'orto' ),
					'title'       => __( 'Where it hurts, and what we do about it', 'orto' ),
					'text'        => __( 'Orthopaedic care organised by joint, because that is how pain announces itself. Every one of these is assessed in the consulting room, imaged on site if it needs to be, and treated without reaching for surgery first.', 'orto' ),
					'jump_label'  => __( 'Specialities on this page', 'orto' ),
					'list_title'  => __( 'Conditions treated', 'orto' ),
					'cta_eyebrow' => __( 'Still not sure?', 'orto' ),
					'cta_title'   => __( 'Describe the pain. We will work out the name for it.', 'orto' ),
					'cta_text'    => __( 'A consultation, an X-ray on site if it is needed, and a plan you understand before you leave.', 'orto' ),
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
