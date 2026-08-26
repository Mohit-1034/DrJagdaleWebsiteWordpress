<div class="front_page_section front_page_section_contacts<?php
	$orto_scheme = orto_get_theme_option( 'front_page_contacts_scheme' );
	if ( ! empty( $orto_scheme ) && ! orto_is_inherit( $orto_scheme ) ) {
		echo ' scheme_' . esc_attr( $orto_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( orto_get_theme_option( 'front_page_contacts_paddings' ) );
	if ( orto_get_theme_option( 'front_page_contacts_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$orto_css      = '';
		$orto_bg_image = orto_get_theme_option( 'front_page_contacts_bg_image' );
		if ( ! empty( $orto_bg_image ) ) {
			$orto_css .= 'background-image: url(' . esc_url( orto_get_attachment_url( $orto_bg_image ) ) . ');';
		}
		if ( ! empty( $orto_css ) ) {
			echo ' style="' . esc_attr( $orto_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$orto_anchor_icon = orto_get_theme_option( 'front_page_contacts_anchor_icon' );
	$orto_anchor_text = orto_get_theme_option( 'front_page_contacts_anchor_text' );
if ( ( ! empty( $orto_anchor_icon ) || ! empty( $orto_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_contacts"'
									. ( ! empty( $orto_anchor_icon ) ? ' icon="' . esc_attr( $orto_anchor_icon ) . '"' : '' )
									. ( ! empty( $orto_anchor_text ) ? ' title="' . esc_attr( $orto_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_contacts_inner
	<?php
	if ( orto_get_theme_option( 'front_page_contacts_fullheight' ) ) {
		echo ' orto-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$orto_css      = '';
			$orto_bg_mask  = orto_get_theme_option( 'front_page_contacts_bg_mask' );
			$orto_bg_color_type = orto_get_theme_option( 'front_page_contacts_bg_color_type' );
			if ( 'custom' == $orto_bg_color_type ) {
				$orto_bg_color = orto_get_theme_option( 'front_page_contacts_bg_color' );
			} elseif ( 'scheme_bg_color' == $orto_bg_color_type ) {
				$orto_bg_color = orto_get_scheme_color( 'bg_color', $orto_scheme );
			} else {
				$orto_bg_color = '';
			}
			if ( ! empty( $orto_bg_color ) && $orto_bg_mask > 0 ) {
				$orto_css .= 'background-color: ' . esc_attr(
					1 == $orto_bg_mask ? $orto_bg_color : orto_hex2rgba( $orto_bg_color, $orto_bg_mask )
				) . ';';
			}
			if ( ! empty( $orto_css ) ) {
				echo ' style="' . esc_attr( $orto_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_contacts_content_wrap content_wrap">
			<?php

			// Title and description
			$orto_caption     = orto_get_theme_option( 'front_page_contacts_caption' );
			$orto_description = orto_get_theme_option( 'front_page_contacts_description' );
			if ( ! empty( $orto_caption ) || ! empty( $orto_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $orto_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_contacts_caption front_page_block_<?php echo ! empty( $orto_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( $orto_caption, 'orto_kses_content' );
					?>
					</h2>
					<?php
				}

				// Description
				if ( ! empty( $orto_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_contacts_description front_page_block_<?php echo ! empty( $orto_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( wpautop( $orto_description ), 'orto_kses_content' );
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$orto_content = orto_get_theme_option( 'front_page_contacts_content' );
			$orto_layout  = orto_get_theme_option( 'front_page_contacts_layout' );
			if ( 'columns' == $orto_layout && ( ! empty( $orto_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_columns front_page_section_contacts_columns columns_wrap">
					<div class="column-1_3">
				<?php
			}

			if ( ( ! empty( $orto_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_content front_page_section_contacts_content front_page_block_<?php echo ! empty( $orto_content ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( $orto_content, 'orto_kses_content' );
					?>
				</div>
				<?php
			}

			if ( 'columns' == $orto_layout && ( ! empty( $orto_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div><div class="column-2_3">
				<?php
			}

			// Shortcode output
			$orto_sc = orto_get_theme_option( 'front_page_contacts_shortcode' );
			if ( ! empty( $orto_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_contacts_output front_page_block_<?php echo ! empty( $orto_sc ) ? 'filled' : 'empty'; ?>">
					<?php
					orto_show_layout( do_shortcode( $orto_sc ) );
					?>
				</div>
				<?php
			}

			if ( 'columns' == $orto_layout && ( ! empty( $orto_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>

		</div>
	</div>
</div>
