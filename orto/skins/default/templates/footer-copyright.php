<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package ORTO
 * @since ORTO 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
				<?php
					$orto_copyright = orto_get_theme_option( 'copyright' );
					if ( ! empty( $orto_copyright ) ) {
						// Replace {{Y}} or {Y} with the current year
						$orto_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $orto_copyright );
						// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
						$orto_copyright = orto_prepare_macros( $orto_copyright );
						// Display copyright
						echo wp_kses( nl2br( $orto_copyright ), 'orto_kses_content' );
					}
				?>
			</div>
		</div>
	</div>
</div>