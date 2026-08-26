<?php
$orto_woocommerce_sc = orto_get_theme_option( 'front_page_woocommerce_products' );
if ( ! empty( $orto_woocommerce_sc ) ) {
	?><div class="front_page_section front_page_section_woocommerce<?php
		$orto_scheme = orto_get_theme_option( 'front_page_woocommerce_scheme' );
		if ( ! empty( $orto_scheme ) && ! orto_is_inherit( $orto_scheme ) ) {
			echo ' scheme_' . esc_attr( $orto_scheme );
		}
		echo ' front_page_section_paddings_' . esc_attr( orto_get_theme_option( 'front_page_woocommerce_paddings' ) );
		if ( orto_get_theme_option( 'front_page_woocommerce_stack' ) ) {
			echo ' sc_stack_section_on';
		}
	?>"
			<?php
			$orto_css      = '';
			$orto_bg_image = orto_get_theme_option( 'front_page_woocommerce_bg_image' );
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
		$orto_anchor_icon = orto_get_theme_option( 'front_page_woocommerce_anchor_icon' );
		$orto_anchor_text = orto_get_theme_option( 'front_page_woocommerce_anchor_text' );
		if ( ( ! empty( $orto_anchor_icon ) || ! empty( $orto_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
			echo do_shortcode(
				'[trx_sc_anchor id="front_page_section_woocommerce"'
											. ( ! empty( $orto_anchor_icon ) ? ' icon="' . esc_attr( $orto_anchor_icon ) . '"' : '' )
											. ( ! empty( $orto_anchor_text ) ? ' title="' . esc_attr( $orto_anchor_text ) . '"' : '' )
											. ']'
			);
		}
	?>
		<div class="front_page_section_inner front_page_section_woocommerce_inner
			<?php
			if ( orto_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
				echo ' orto-full-height sc_layouts_flex sc_layouts_columns_middle';
			}
			?>
				"
				<?php
				$orto_css      = '';
				$orto_bg_mask  = orto_get_theme_option( 'front_page_woocommerce_bg_mask' );
				$orto_bg_color_type = orto_get_theme_option( 'front_page_woocommerce_bg_color_type' );
				if ( 'custom' == $orto_bg_color_type ) {
					$orto_bg_color = orto_get_theme_option( 'front_page_woocommerce_bg_color' );
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
			<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
				<?php
				// Content wrap with title and description
				$orto_caption     = orto_get_theme_option( 'front_page_woocommerce_caption' );
				$orto_description = orto_get_theme_option( 'front_page_woocommerce_description' );
				if ( ! empty( $orto_caption ) || ! empty( $orto_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					// Caption
					if ( ! empty( $orto_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $orto_caption ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( $orto_caption, 'orto_kses_content' );
						?>
						</h2>
						<?php
					}

					// Description (text)
					if ( ! empty( $orto_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $orto_description ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( wpautop( $orto_description ), 'orto_kses_content' );
						?>
						</div>
						<?php
					}
				}

				// Content (widgets)
				?>
				<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
					<?php
					if ( 'products' == $orto_woocommerce_sc ) {
						$orto_woocommerce_sc_ids      = orto_get_theme_option( 'front_page_woocommerce_products_per_page' );
						$orto_woocommerce_sc_per_page = count( explode( ',', $orto_woocommerce_sc_ids ) );
					} else {
						$orto_woocommerce_sc_per_page = max( 1, (int) orto_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
					}
					$orto_woocommerce_sc_columns = max( 1, min( $orto_woocommerce_sc_per_page, (int) orto_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
					echo do_shortcode(
						"[{$orto_woocommerce_sc}"
										. ( 'products' == $orto_woocommerce_sc
												? ' ids="' . esc_attr( $orto_woocommerce_sc_ids ) . '"'
												: '' )
										. ( 'product_category' == $orto_woocommerce_sc
												? ' category="' . esc_attr( orto_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
												: '' )
										. ( 'best_selling_products' != $orto_woocommerce_sc
												? ' orderby="' . esc_attr( orto_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
													. ' order="' . esc_attr( orto_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
												: '' )
										. ' per_page="' . esc_attr( $orto_woocommerce_sc_per_page ) . '"'
										. ' columns="' . esc_attr( $orto_woocommerce_sc_columns ) . '"'
						. ']'
					);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
