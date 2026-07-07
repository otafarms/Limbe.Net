<?php
/**
 * Frontend lead forms.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles frontend form submissions.
 */
class LimbeNet_Core_Forms {
	/**
	 * Handle form submission.
	 */
	public function handle_submission() {
		if ( empty( $_POST['limbenet_form_type'] ) ) {
			return;
		}

		if ( empty( $_POST['limbenet_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['limbenet_form_nonce'] ) ), 'limbenet_form_submit' ) ) {
			wp_die( esc_html__( 'The form could not be verified. Please go back and try again.', 'limbenet-core' ) );
		}

		$posted = wp_unslash( $_POST );
		$type   = isset( $posted['limbenet_form_type'] ) ? sanitize_key( $posted['limbenet_form_type'] ) : 'booking_help';

		if ( empty( $posted['consent'] ) ) {
			$this->redirect_with_status( 'consent-required' );
		}

		$data = array(
			'request_type'  => $type,
			'name'          => isset( $posted['name'] ) ? sanitize_text_field( $posted['name'] ) : '',
			'business_name' => isset( $posted['business_name'] ) ? sanitize_text_field( $posted['business_name'] ) : '',
			'email'         => isset( $posted['email'] ) ? sanitize_email( $posted['email'] ) : '',
			'phone'         => isset( $posted['phone'] ) ? sanitize_text_field( $posted['phone'] ) : '',
			'city'          => isset( $posted['city'] ) ? sanitize_text_field( $posted['city'] ) : '',
			'business_type' => isset( $posted['business_type'] ) ? sanitize_text_field( $posted['business_type'] ) : '',
			'message'       => isset( $posted['message'] ) ? sanitize_textarea_field( $posted['message'] ) : '',
			'consent'       => 'yes',
		);

		$title_parts = array_filter( array( self::type_label( $type ), $data['name'], $data['business_name'] ) );
		$post_id     = wp_insert_post(
			array(
				'post_type'    => 'limbenet_request',
				'post_status'  => 'private',
				'post_title'   => implode( ' - ', $title_parts ),
				'post_content' => $this->format_request_content( $data ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$this->redirect_with_status( 'error' );
		}

		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$this->redirect_with_status( 'success' );
	}

	/**
	 * Render a frontend form.
	 *
	 * @param string $type Form type.
	 * @param array  $args Optional heading args.
	 * @return string
	 */
	public static function render_form( $type = 'booking_help', $args = array() ) {
		$type     = sanitize_key( $type );
		$defaults = self::form_defaults( $type );
		$args     = wp_parse_args( $args, $defaults );
		$is_contact = 'contact' === $type;

		ob_start();
		?>
		<div class="lnet-form-wrap">
			<?php self::render_status_notice(); ?>
			<div class="lnet-form-heading">
				<h2><?php echo esc_html( $args['title'] ); ?></h2>
				<p><?php echo esc_html( $args['intro'] ); ?></p>
			</div>
			<form class="lnet-form" method="post">
				<?php wp_nonce_field( 'limbenet_form_submit', 'limbenet_form_nonce' ); ?>
				<input type="hidden" name="limbenet_form_type" value="<?php echo esc_attr( $type ); ?>">

				<div class="lnet-form-grid<?php echo $is_contact ? ' is-contact' : ''; ?>">
					<p>
						<label for="<?php echo esc_attr( $type ); ?>_name"><?php esc_html_e( 'Name', 'limbenet-core' ); ?></label>
						<input id="<?php echo esc_attr( $type ); ?>_name" name="name" type="text" required>
					</p>
					<p>
						<label for="<?php echo esc_attr( $type ); ?>_phone"><?php esc_html_e( 'Phone or WhatsApp', 'limbenet-core' ); ?></label>
						<input id="<?php echo esc_attr( $type ); ?>_phone" name="phone" type="text" required>
					</p>
					<p class="<?php echo $is_contact ? 'is-wide' : ''; ?>">
						<label for="<?php echo esc_attr( $type ); ?>_email"><?php esc_html_e( 'Email', 'limbenet-core' ); ?></label>
						<input id="<?php echo esc_attr( $type ); ?>_email" name="email" type="email" required>
					</p>
					<?php if ( ! $is_contact ) : ?>
						<p>
							<label for="<?php echo esc_attr( $type ); ?>_business_name"><?php esc_html_e( 'Business name', 'limbenet-core' ); ?></label>
							<input id="<?php echo esc_attr( $type ); ?>_business_name" name="business_name" type="text">
						</p>
						<p>
							<label for="<?php echo esc_attr( $type ); ?>_city"><?php esc_html_e( 'City', 'limbenet-core' ); ?></label>
							<input id="<?php echo esc_attr( $type ); ?>_city" name="city" type="text">
						</p>
						<p>
							<label for="<?php echo esc_attr( $type ); ?>_business_type"><?php esc_html_e( 'Business type', 'limbenet-core' ); ?></label>
							<select id="<?php echo esc_attr( $type ); ?>_business_type" name="business_type">
								<option value=""><?php esc_html_e( 'Select a type', 'limbenet-core' ); ?></option>
								<option value="hotel"><?php esc_html_e( 'Hotel', 'limbenet-core' ); ?></option>
								<option value="restaurant"><?php esc_html_e( 'Restaurant', 'limbenet-core' ); ?></option>
								<option value="tour-guide"><?php esc_html_e( 'Tour guide', 'limbenet-core' ); ?></option>
								<option value="transport"><?php esc_html_e( 'Transport', 'limbenet-core' ); ?></option>
								<option value="attraction"><?php esc_html_e( 'Attraction', 'limbenet-core' ); ?></option>
								<option value="event"><?php esc_html_e( 'Event organizer', 'limbenet-core' ); ?></option>
								<option value="photographer"><?php esc_html_e( 'Photographer', 'limbenet-core' ); ?></option>
								<option value="other"><?php esc_html_e( 'Other', 'limbenet-core' ); ?></option>
							</select>
						</p>
					<?php endif; ?>
				</div>

				<p>
					<label for="<?php echo esc_attr( $type ); ?>_message"><?php esc_html_e( 'Message', 'limbenet-core' ); ?></label>
					<textarea id="<?php echo esc_attr( $type ); ?>_message" name="message" rows="5" required></textarea>
				</p>

				<p class="lnet-consent">
					<label>
						<input name="consent" type="checkbox" value="1" required>
						<?php esc_html_e( 'I consent to Limbe.Net storing this request and contacting me about it.', 'limbenet-core' ); ?>
					</label>
				</p>

				<p><button class="lnet-submit" type="submit"><?php echo esc_html( $args['button'] ); ?></button></p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Form defaults by type.
	 *
	 * @param string $type Form type.
	 * @return array
	 */
	private static function form_defaults( $type ) {
		$forms = array(
			'submit_business' => array(
				'title'  => __( 'Submit a business listing', 'limbenet-core' ),
				'intro'  => __( 'Hotels, restaurants, tour guides, attractions, transport providers, photographers, and event organizers can request a listing review.', 'limbenet-core' ),
				'button' => __( 'Submit listing request', 'limbenet-core' ),
			),
			'booking_help'    => array(
				'title'  => __( 'Request booking help', 'limbenet-core' ),
				'intro'  => __( 'Tell us what you want to visit, your dates, and what support you need. We will route requests to verified partners when available.', 'limbenet-core' ),
				'button' => __( 'Request help', 'limbenet-core' ),
			),
			'claim_listing'   => array(
				'title'  => __( 'Claim this listing', 'limbenet-core' ),
				'intro'  => __( 'Business owners can request verification or updates for an existing listing.', 'limbenet-core' ),
				'button' => __( 'Submit claim', 'limbenet-core' ),
			),
			'advertise'       => array(
				'title'  => __( 'Advertise with Limbe.Net', 'limbenet-core' ),
				'intro'  => __( 'Ask about featured partner plans, sponsored content labels, and campaign placements.', 'limbenet-core' ),
				'button' => __( 'Request advertising info', 'limbenet-core' ),
			),
			'contact'         => array(
				'title'  => __( 'Get in touch', 'limbenet-core' ),
				'intro'  => __( 'Send questions, corrections, partnership notes, or travel planning requests to the Limbe.Net team.', 'limbenet-core' ),
				'button' => __( 'Send message', 'limbenet-core' ),
			),
		);

		return isset( $forms[ $type ] ) ? $forms[ $type ] : $forms['booking_help'];
	}

	/**
	 * Get label for a request type.
	 *
	 * @param string $type Request type.
	 * @return string
	 */
	private static function type_label( $type ) {
		$labels = array(
			'submit_business' => __( 'Business listing', 'limbenet-core' ),
			'booking_help'    => __( 'Booking help', 'limbenet-core' ),
			'claim_listing'   => __( 'Claim listing', 'limbenet-core' ),
			'advertise'       => __( 'Advertising inquiry', 'limbenet-core' ),
			'contact'         => __( 'Contact message', 'limbenet-core' ),
		);

		return isset( $labels[ $type ] ) ? $labels[ $type ] : $labels['booking_help'];
	}

	/**
	 * Render status notice.
	 */
	private static function render_status_notice() {
		if ( empty( $_GET['limbenet_form_status'] ) ) {
			return;
		}

		$status = sanitize_key( wp_unslash( $_GET['limbenet_form_status'] ) );
		$class  = 'success' === $status ? 'is-success' : 'is-error';
		$message = __( 'Thanks. Your request has been received.', 'limbenet-core' );

		if ( 'consent-required' === $status ) {
			$message = __( 'Please confirm the consent checkbox before submitting.', 'limbenet-core' );
		} elseif ( 'error' === $status ) {
			$message = __( 'Something went wrong. Please try again.', 'limbenet-core' );
		}

		echo '<div class="lnet-form-notice ' . esc_attr( $class ) . '" role="status">' . esc_html( $message ) . '</div>';
	}

	/**
	 * Format request content for admin review.
	 *
	 * @param array $data Submission data.
	 * @return string
	 */
	private function format_request_content( $data ) {
		$lines = array(
			__( 'Request Type:', 'limbenet-core' ) . ' ' . self::type_label( $data['request_type'] ),
			__( 'Name:', 'limbenet-core' ) . ' ' . $data['name'],
			__( 'Business Name:', 'limbenet-core' ) . ' ' . $data['business_name'],
			__( 'Email:', 'limbenet-core' ) . ' ' . $data['email'],
			__( 'Phone or WhatsApp:', 'limbenet-core' ) . ' ' . $data['phone'],
			__( 'City:', 'limbenet-core' ) . ' ' . $data['city'],
			__( 'Business Type:', 'limbenet-core' ) . ' ' . $data['business_type'],
			'',
			__( 'Message:', 'limbenet-core' ),
			$data['message'],
		);

		return implode( "\n", $lines );
	}

	/**
	 * Redirect after form handling.
	 *
	 * @param string $status Status key.
	 */
	private function redirect_with_status( $status ) {
		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = home_url( '/' );
		}

		$redirect = remove_query_arg( array( 'limbenet_form_status' ), $redirect );
		wp_safe_redirect( add_query_arg( 'limbenet_form_status', sanitize_key( $status ), $redirect ) );
		exit;
	}
}
