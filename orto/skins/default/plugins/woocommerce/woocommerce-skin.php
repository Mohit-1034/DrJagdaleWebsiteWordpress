<?php
/* WooCommerce skin-specific functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 3 - add/remove Theme Options elements

if ( ! function_exists( 'orto_woocommerce_skin_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'orto_woocommerce_skin_theme_setup3', 3 );
	function orto_woocommerce_skin_theme_setup3() {
		if ( orto_exists_woocommerce() ) {
			// Panel 'Shop' with skin-specific options
			orto_storage_set_array_after( 'options', 'shop_single', orto_options_get_list_cpt_options_body( 'shop', esc_html__( 'Product', 'orto' ), 'single' ) );
			// Hide 'shop_mode'
			orto_storage_set_array2( 'options', 'shop_mode', 'type', 'hidden' );
			// Hide 'single_product_gallery_thumbs'
			orto_storage_set_array2( 'options', 'single_product_gallery_thumbs', 'type', 'hidden' );
			// Hide 'shop_buttons'
			orto_storage_set_array2( 'options', 'shop_hover', 'std', 'none' );
			orto_storage_set_array2( 'options', 'shop_hover', 'type', 'hidden' );
			// Number of related products by default
			orto_storage_set_array2( 'options', 'related_posts_shop', 'std', 4);
			orto_storage_set_array2( 'options', 'related_columns_shop', 'std', 4);
		}
	}
}


// Remove\Register Action\filters
if ( ! function_exists( 'orto_woocommerce_skin_woocommerce_remove_action' ) ) {
	add_action( 'init', 'orto_woocommerce_skin_woocommerce_remove_action', 11 );
	function orto_woocommerce_skin_woocommerce_remove_action() {
		if ( orto_exists_woocommerce() ) {
			add_filter( 'orto_filter_woocommerce_sale_flash', 'orto_change_woocommerce_sale_flash', 10, 3 );
		}
	}
}


// Show/Hide product's tags before the title
if ( ! function_exists( 'orto_woocommerce_skin_show_title' ) ) {
	add_filter( 'orto_filter_show_woocommerce_title', 'orto_woocommerce_skin_show_title' );
	function orto_woocommerce_skin_show_title() {
		return false;
	}
}


// Add label "UP TO"
if ( ! function_exists( 'orto_change_woocommerce_sale_flash' ) ) {
	function orto_change_woocommerce_sale_flash($new_sale, $percent, $product) {
		if( 'variable' === $product->get_type() ){
			$new_sale = '<span class="onsale"><span class="onsale_up">'. esc_html__('Up to', 'orto') .'</span> - '. esc_html( $percent ) . '%</span>';
		}
		return $new_sale;
	}
}

// Image width for thumbnails gallery
if ( ! function_exists( 'orto_filter_woocommerce_skin_theme_support' ) ) {
	add_filter( 'orto_filter_woocommerce_theme_support', 'orto_filter_woocommerce_skin_theme_support' );
	function orto_filter_woocommerce_skin_theme_support( $arr ) {
		$arr['gallery_thumbnail_image_width'] = 300;
		return $arr;
	}
}