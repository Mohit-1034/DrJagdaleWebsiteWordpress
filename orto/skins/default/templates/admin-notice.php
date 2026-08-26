<?php
/**
 * The template to display Admin notices
 *
 * @package ORTO
 * @since ORTO 1.0.1
 */

$orto_theme_slug = get_template();
$orto_theme_obj  = wp_get_theme( $orto_theme_slug );
?>
<div class="orto_admin_notice orto_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$orto_theme_img = orto_get_file_url( 'screenshot.jpg' );
	if ( '' != $orto_theme_img ) {
		?>
		<div class="orto_notice_image"><img src="<?php echo esc_url( $orto_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'orto' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="orto_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'orto' ),
				$orto_theme_obj->get( 'Name' ) . ( ORTO_THEME_FREE ? ' ' . __( 'Free', 'orto' ) : '' ),
				$orto_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="orto_notice_text">
		<p class="orto_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $orto_theme_obj->description ) );
			?>
		</p>
		<p class="orto_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'orto' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="orto_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=orto_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'orto' );
			?>
		</a>
	</div>
</div>
