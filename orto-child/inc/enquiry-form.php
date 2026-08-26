<?php
/**
 * The appointment enquiry form and its handler
 *
 * Kept here rather than inside template-contact.php so more than one page can
 * show it. A page template is only loaded when its own page is being rendered,
 * so nothing else could reach the handler or the markup; out here, adding the
 * form to a second page is two function calls and nothing else.
 *
 * The form posts back to whichever page rendered it - the action is
 * get_permalink(), and the handler redirects to get_permalink() as well.
 *
 * ONE FORM PER PAGE. The field ids are fixed (contact_name, contact_email and
 * so on) because their labels point at them. Two of these on one page would
 * give it duplicate ids and labels that focus the wrong field.
 *
 * @package ORTO
 */

if ( ! function_exists( 'orto_child_contact_get_settings' ) ) {
	/**
	 * Contact details shown on the page.
	 *
	 * The phone/email/address/socials/hours come from orto_child_get_business()
	 * in functions.php, which the header and footer read too, so the three can
	 * never drift apart. Only the settings specific to the Contact page are
	 * added on top.
	 *
	 * @return array
	 */
	function orto_child_contact_get_settings() {
		$business = orto_child_get_business();

		return apply_filters(
			'orto_child_contact_settings',
			$business + array(
				/*
				 * Where enquiry emails are sent. Falls back to the WordPress
				 * admin address, because an enquiry that goes nowhere is worse
				 * than one that lands in the wrong inbox - and the clinic has
				 * not published an email address yet.
				 */
				'notify_email' => ! empty( $business['email'] ) ? $business['email'] : get_option( 'admin_email' ),
				// Zoom level of the embedded map (higher = closer in).
				'map_zoom'     => 17,
				// Show the full-width map band at the foot of the page.
				'map_enabled'  => true,
				/*
				 * Business name used to look the clinic up on Google.
				 * Deliberately not get_bloginfo( 'name' ) - the WP site title is
				 * the theme name on a fresh install and would send the map to
				 * the wrong place. When this matches the clinic's Google
				 * Business Profile, the embed also shows its info card.
				 */
				'map_place'    => $business['name'],
			)
		);
	}
}

if ( ! function_exists( 'orto_child_contact_get_map_query' ) ) {
	/**
	 * The location string handed to the map provider.
	 *
	 * Derived from the address in orto_child_contact_get_settings() so there is
	 * one source of truth: change the address and the map follows.
	 *
	 * @return string
	 */
	function orto_child_contact_get_map_query() {
		$settings = orto_child_contact_get_settings();

		$query = $settings['address'];

		if ( ! empty( $settings['map_place'] ) ) {
			$query = $settings['map_place'] . ', ' . $query;
		}

		return apply_filters( 'orto_child_contact_map_query', $query );
	}
}

if ( ! function_exists( 'orto_child_contact_get_map_embed_url' ) ) {
	/**
	 * URL for the map iframe.
	 *
	 * Uses Google Maps' keyless embed endpoint rather than the Embed API, so the
	 * page needs no API key and no billing account - the site works as soon as
	 * the theme is deployed, on any environment. Filter
	 * 'orto_child_contact_map_embed_url' to swap in a keyed Embed API URL
	 * (https://www.google.com/maps/embed/v1/place?key=...) or another provider.
	 *
	 * @return string
	 */
	function orto_child_contact_get_map_embed_url() {
		$settings = orto_child_contact_get_settings();

		$url = add_query_arg(
			array(
				'q'      => rawurlencode( orto_child_contact_get_map_query() ),
				'z'      => (int) $settings['map_zoom'],
				'output' => 'embed',
			),
			'https://maps.google.com/maps'
		);

		return apply_filters( 'orto_child_contact_map_embed_url', $url );
	}
}

if ( ! function_exists( 'orto_child_contact_get_map_directions_url' ) ) {
	/**
	 * "Get directions" link target - opens the location in the visitor's maps app.
	 *
	 * @return string
	 */
	function orto_child_contact_get_map_directions_url() {
		$url = add_query_arg(
			array(
				'api'         => 1,
				'destination' => rawurlencode( orto_child_contact_get_map_query() ),
			),
			'https://www.google.com/maps/dir/'
		);

		return apply_filters( 'orto_child_contact_map_directions_url', $url );
	}
}

if ( ! function_exists( 'orto_child_contact_handle_submission' ) ) {
	/**
	 * Process the enquiry form on POST, before any output is sent, so we can
	 * redirect.
	 *
	 * @return string|null 'sent', 'error', or null if this isn't a form submission.
	 */
	function orto_child_contact_handle_submission() {
		if ( empty( $_POST['orto_child_contact_submit'] ) ) {
			return null;
		}

		if ( ! isset( $_POST['orto_child_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['orto_child_contact_nonce'] ) ), 'orto_child_contact_form' ) ) {
			return 'error';
		}

		// Honeypot: real visitors never fill this hidden field in.
		if ( ! empty( $_POST['orto_child_contact_company'] ) ) {
			return 'sent'; // Pretend success so bots don't learn anything.
		}

		$name      = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
		$email     = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
		$phone     = isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '';
		$concern   = isset( $_POST['contact_concern'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_concern'] ) ) : '';
		$preferred = isset( $_POST['contact_preferred'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_preferred'] ) ) : '';
		$message   = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

		/*
		 * A phone number is enough to reply to, and plenty of patients here do
		 * not use email - so either one will do, rather than email being
		 * required. The message is still required: an enquiry with no question
		 * in it cannot be answered.
		 */
		if ( '' === $name || '' === $message ) {
			return 'error';
		}

		if ( '' === $phone && ! is_email( $email ) ) {
			return 'error';
		}

		if ( '' !== $email && ! is_email( $email ) ) {
			return 'error';
		}

		$settings = orto_child_contact_get_settings();

		/* translators: %s: the enquirer's name. */
		$subject = sprintf( __( 'New appointment enquiry from %s', 'orto' ), $name );

		$body  = 'New enquiry from the appointment form on ' . wp_parse_url( home_url(), PHP_URL_HOST ) . "\n\n";
		$body .= "Name: {$name}\n";
		$body .= "Phone: {$phone}\n";
		$body .= "Email: {$email}\n";
		$body .= "Concern: {$concern}\n";
		$body .= "Preferred date/time: {$preferred}\n";
		$body .= "Message:\n{$message}\n";

		$headers = array();

		// Reply-To only when there is an address to reply to.
		if ( is_email( $email ) ) {
			$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
		}

		$sent = wp_mail( $settings['notify_email'], $subject, $body, $headers );

		return $sent ? 'sent' : 'error';
	}
}

if ( ! function_exists( 'orto_child_enquiry_form_boot' ) ) {
	/**
	 * Run the handler and redirect, then hand back the result of the last
	 * submission so the page can show a notice.
	 *
	 * Call this at the top of a template, before get_header(). The
	 * post/redirect/get is the point: without it a refresh re-submits the form,
	 * and the browser's "confirm resubmission" dialog is the last thing anyone
	 * wants after sending an enquiry.
	 *
	 * @return string '', 'sent' or 'error'.
	 */
	function orto_child_enquiry_form_boot() {
		$result = orto_child_contact_handle_submission();

		if ( null !== $result ) {
			wp_safe_redirect( add_query_arg( 'contact', $result, get_permalink() ) );
			exit;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading back our own redirect, and the value is only ever compared against a fixed list below.
		$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';

		return in_array( $status, array( 'sent', 'error' ), true ) ? $status : '';
	}
}

if ( ! function_exists( 'orto_child_enquiry_form' ) ) {
	/**
	 * Print the enquiry form, with the notice for the last submission above it.
	 *
	 * @param array $args {
	 *     @type string $status   Result from orto_child_enquiry_form_boot().
	 *     @type string $title    Heading above the fields. Pass '' when the page
	 *                            already heads the form from outside the panel,
	 *                            and the form takes an aria-label instead so it
	 *                            is still named for assistive technology.
	 *     @type string $subtitle Line under the heading. Pass '' for none.
	 * }
	 */
	function orto_child_enquiry_form( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'status'   => '',
				'title'    => __( 'Request an appointment', 'orto' ),
				'subtitle' => __( 'Tell us what is hurting and when suits you. We will call back to confirm a slot.', 'orto' ),
			)
		);

		$settings = orto_child_contact_get_settings();
		$status   = $args['status'];
		?>

		<?php
		/*
		 * The result of a submission arrives as a query parameter after a
		 * redirect, so the notice is new content on a page the visitor has just
		 * landed on rather than a live update. role="status" makes a screen
		 * reader announce it without stealing focus, and tabindex="-1" lets the
		 * script below move focus to it, which is what a sighted visitor's eye
		 * does automatically.
		 */
		?>
		<?php if ( 'sent' === $status ) { ?>
			<div class="contact_form_notice contact_form_notice_success" id="contact_form_notice" role="status" tabindex="-1">
				<?php esc_html_e( 'Thank you - your enquiry has been sent. The clinic will call you back to confirm an appointment.', 'orto' ); ?>
			</div>
		<?php } elseif ( 'error' === $status ) { ?>
			<div class="contact_form_notice contact_form_notice_error" id="contact_form_notice" role="alert" tabindex="-1">
				<?php
				printf(
					/* translators: 1: opening link tag to the phone number, 2: closing link tag. */
					esc_html__( 'Sorry, something went wrong sending your enquiry. Please check your name, a way to reach you and your message, or %1$scall the clinic%2$s instead.', 'orto' ),
					'<a href="tel:' . esc_attr( $settings['phone_link'] ) . '">',
					'</a>'
				);
				?>
			</div>
		<?php } ?>

		<?php if ( '' !== $status ) { ?>
			<script>(function(){var n=document.getElementById("contact_form_notice");if(n){n.focus();}}());</script>
		<?php } ?>

		<form class="contact_form" method="post" action="<?php echo esc_url( get_permalink() ); ?>"
			<?php if ( '' !== $args['title'] ) { ?>
				aria-labelledby="contact_form_title"
			<?php } else { ?>
				aria-label="<?php esc_attr_e( 'Request an appointment', 'orto' ); ?>"
			<?php } ?>>
			<?php wp_nonce_field( 'orto_child_contact_form', 'orto_child_contact_nonce' ); ?>

			<?php if ( '' !== $args['title'] ) { ?>
				<h2 class="contact_form_title" id="contact_form_title"><?php echo esc_html( $args['title'] ); ?></h2>
			<?php } ?>
			<?php if ( '' !== $args['subtitle'] ) { ?>
				<p class="contact_form_subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php } ?>

			<div class="contact_form_hp" aria-hidden="true">
				<label for="contact_company"><?php esc_html_e( 'Company', 'orto' ); ?></label>
				<input type="text" id="contact_company" name="orto_child_contact_company" tabindex="-1" autocomplete="off">
			</div>

			<div class="contact_form_grid">

				<p class="contact_form_row">
					<label for="contact_name"><?php esc_html_e( 'Name', 'orto' ); ?> <span class="required">*</span></label>
					<input type="text" id="contact_name" name="contact_name" autocomplete="name" required>
				</p>

				<p class="contact_form_row">
					<label for="contact_phone"><?php esc_html_e( 'Phone', 'orto' ); ?> <span class="required">*</span></label>
					<input type="tel" id="contact_phone" name="contact_phone" autocomplete="tel" inputmode="tel" required>
				</p>

				<p class="contact_form_row">
					<label for="contact_email"><?php esc_html_e( 'Email', 'orto' ); ?></label>
					<input type="email" id="contact_email" name="contact_email" autocomplete="email" inputmode="email">
				</p>

				<p class="contact_form_row">
					<label for="contact_preferred"><?php esc_html_e( 'Preferred date/time', 'orto' ); ?></label>
					<input type="text" id="contact_preferred" name="contact_preferred" placeholder="<?php esc_attr_e( 'e.g. Tuesday evening', 'orto' ); ?>">
				</p>

				<p class="contact_form_row contact_form_row_full">
					<label for="contact_concern"><?php esc_html_e( 'What is troubling you?', 'orto' ); ?></label>
					<input type="text" id="contact_concern" name="contact_concern" placeholder="<?php esc_attr_e( 'e.g. Knee pain, fracture follow-up, X-ray', 'orto' ); ?>">
				</p>

				<p class="contact_form_row contact_form_row_full">
					<label for="contact_message"><?php esc_html_e( 'Message', 'orto' ); ?> <span class="required">*</span></label>
					<textarea id="contact_message" name="contact_message" rows="6" required></textarea>
				</p>

			</div>

			<p class="contact_form_submit">
				<button type="submit" name="orto_child_contact_submit" value="1" class="djo_button djo_button_solid contact_form_button">
					<?php esc_html_e( 'Send Enquiry', 'orto' ); ?>
				</button>
			</p>

			<p class="contact_form_note">
				<?php esc_html_e( 'This form is for appointments and general questions only. If this is an emergency, go to your nearest hospital.', 'orto' ); ?>
			</p>
		</form>
		<?php
	}
}
