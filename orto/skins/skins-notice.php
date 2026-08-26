<?php
/**
 * The template to display Admin notices
 *
 * @package ORTO
 * @since ORTO 1.0.64
 */

$orto_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$orto_skins_args = get_query_var( 'orto_skins_notice_args' );
?>
<div class="orto_admin_notice orto_skins_notice notice notice-info is-dismissible" data-notice="skins">
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
		<?php esc_html_e( 'New skins are available', 'orto' ); ?>
	</h3>
	<?php

	// Description
	$orto_total      = $orto_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$orto_skins_msg  = $orto_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $orto_total, 'orto' ), $orto_total ) . '</strong>'
							: '';
	$orto_total      = $orto_skins_args['free'];
	$orto_skins_msg .= $orto_total > 0
							? ( ! empty( $orto_skins_msg ) ? ' ' . esc_html__( 'and', 'orto' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $orto_total, 'orto' ), $orto_total ) . '</strong>'
							: '';
	$orto_total      = $orto_skins_args['pay'];
	$orto_skins_msg .= $orto_skins_args['pay'] > 0
							? ( ! empty( $orto_skins_msg ) ? ' ' . esc_html__( 'and', 'orto' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $orto_total, 'orto' ), $orto_total ) . '</strong>'
							: '';
	?>
	<div class="orto_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'orto' ), $orto_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="orto_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $orto_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			esc_html_e( 'Go to Skins manager', 'orto' );
			?>
		</a>
		<?php
		// Dismiss notice for 7 days
		?>
		<a href="#" role="button" class="button button-secondary orto_notice_button_dismiss" data-notice="skins"><i class="dashicons dashicons-no-alt"></i> 
			<?php
			esc_html_e( 'Dismiss', 'orto' );
			?>
		</a>
		<?php
		// Hide notice forever
		?>
		<a href="#" role="button" class="button button-secondary orto_notice_button_hide" data-notice="skins"><i class="dashicons dashicons-no-alt"></i> 
			<?php
			esc_html_e( 'Never show again', 'orto' );
			?>
		</a>
	</div>
</div>
