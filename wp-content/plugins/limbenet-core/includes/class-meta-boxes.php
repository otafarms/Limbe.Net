<?php
/**
 * Meta field registration and admin boxes.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages tourism metadata.
 */
class LimbeNet_Core_Meta_Boxes {
	/**
	 * Get field definitions by post type.
	 *
	 * @return array
	 */
	public static function fields() {
		$yes_no         = array(
			''    => __( 'Select', 'limbenet-core' ),
			'yes' => __( 'Yes', 'limbenet-core' ),
			'no'  => __( 'No', 'limbenet-core' ),
		);
		$yes_no_unknown = array(
			''        => __( 'Select', 'limbenet-core' ),
			'yes'     => __( 'Yes', 'limbenet-core' ),
			'no'      => __( 'No', 'limbenet-core' ),
			'unknown' => __( 'Unknown', 'limbenet-core' ),
		);
		$advisory       = array(
			''                    => __( 'Select', 'limbenet-core' ),
			'normal'              => __( 'Normal travel planning', 'limbenet-core' ),
			'check-before-travel' => __( 'Check current advisory before travel', 'limbenet-core' ),
			'high-risk'           => __( 'High-risk area: travel only with expert local guidance', 'limbenet-core' ),
		);

		return array(
			'attraction'  => array(
				'attraction_subtitle'   => array( 'label' => __( 'Attraction Subtitle', 'limbenet-core' ), 'type' => 'text' ),
				'region'                => array( 'label' => __( 'Region', 'limbenet-core' ), 'type' => 'text' ),
				'city'                  => array( 'label' => __( 'City', 'limbenet-core' ), 'type' => 'text' ),
				'attraction_type'       => array( 'label' => __( 'Attraction Type', 'limbenet-core' ), 'type' => 'text' ),
				'short_description'     => array( 'label' => __( 'Short Description', 'limbenet-core' ), 'type' => 'textarea' ),
				'full_description'      => array( 'label' => __( 'Full Description', 'limbenet-core' ), 'type' => 'textarea' ),
				'hero_image'            => array( 'label' => __( 'Hero Image URL', 'limbenet-core' ), 'type' => 'url' ),
				'gallery_images'        => array( 'label' => __( 'Gallery Image URLs or IDs', 'limbenet-core' ), 'type' => 'textarea', 'description' => __( 'Separate image URLs or attachment IDs with commas.', 'limbenet-core' ) ),
				'latitude'              => array( 'label' => __( 'Latitude', 'limbenet-core' ), 'type' => 'text' ),
				'longitude'             => array( 'label' => __( 'Longitude', 'limbenet-core' ), 'type' => 'text' ),
				'address'               => array( 'label' => __( 'Address', 'limbenet-core' ), 'type' => 'textarea' ),
				'opening_hours'         => array( 'label' => __( 'Opening Hours', 'limbenet-core' ), 'type' => 'textarea' ),
				'best_time_to_visit'    => array( 'label' => __( 'Best Time to Visit', 'limbenet-core' ), 'type' => 'textarea' ),
				'recommended_duration'  => array( 'label' => __( 'Recommended Duration', 'limbenet-core' ), 'type' => 'text' ),
				'ticket_required'       => array( 'label' => __( 'Ticket Required', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no_unknown ),
				'ticket_price_range'    => array( 'label' => __( 'Ticket Price Range', 'limbenet-core' ), 'type' => 'text' ),
				'official_ticket_url'   => array( 'label' => __( 'Official Ticket URL', 'limbenet-core' ), 'type' => 'url' ),
				'partner_booking_url'   => array( 'label' => __( 'Partner Booking URL', 'limbenet-core' ), 'type' => 'url' ),
				'booking_whatsapp'      => array( 'label' => __( 'Booking WhatsApp', 'limbenet-core' ), 'type' => 'text' ),
				'phone_number'          => array( 'label' => __( 'Phone Number', 'limbenet-core' ), 'type' => 'text' ),
				'email'                 => array( 'label' => __( 'Email', 'limbenet-core' ), 'type' => 'email' ),
				'safety_notice'         => array( 'label' => __( 'Safety Notice', 'limbenet-core' ), 'type' => 'textarea' ),
				'advisory_level'        => array( 'label' => __( 'Advisory Level', 'limbenet-core' ), 'type' => 'select', 'choices' => $advisory ),
				'accessibility_notes'   => array( 'label' => __( 'Accessibility Notes', 'limbenet-core' ), 'type' => 'textarea' ),
				'family_friendly'       => array( 'label' => __( 'Family Friendly', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
				'nearby_hotels'         => array( 'label' => __( 'Nearby Hotels', 'limbenet-core' ), 'type' => 'textarea' ),
				'nearby_restaurants'    => array( 'label' => __( 'Nearby Restaurants', 'limbenet-core' ), 'type' => 'textarea' ),
				'nearby_attractions'    => array( 'label' => __( 'Nearby Attractions', 'limbenet-core' ), 'type' => 'textarea' ),
				'how_to_get_there'      => array( 'label' => __( 'How to Get There', 'limbenet-core' ), 'type' => 'textarea' ),
				'last_verified_date'    => array( 'label' => __( 'Last Verified Date', 'limbenet-core' ), 'type' => 'text' ),
				'source_notes'          => array( 'label' => __( 'Source Notes', 'limbenet-core' ), 'type' => 'textarea' ),
				'featured'              => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'destination' => array(
				'destination_name'          => array( 'label' => __( 'Destination Name', 'limbenet-core' ), 'type' => 'text' ),
				'region'                    => array( 'label' => __( 'Region', 'limbenet-core' ), 'type' => 'text' ),
				'overview'                  => array( 'label' => __( 'Overview', 'limbenet-core' ), 'type' => 'textarea' ),
				'best_for'                  => array( 'label' => __( 'Best For', 'limbenet-core' ), 'type' => 'textarea' ),
				'travel_time_from_douala'   => array( 'label' => __( 'Travel Time from Douala', 'limbenet-core' ), 'type' => 'text' ),
				'travel_time_from_yaounde'  => array( 'label' => __( 'Travel Time from Yaounde', 'limbenet-core' ), 'type' => 'text' ),
				'safety_notice'             => array( 'label' => __( 'Safety Notice', 'limbenet-core' ), 'type' => 'textarea' ),
				'advisory_level'            => array( 'label' => __( 'Advisory Level', 'limbenet-core' ), 'type' => 'select', 'choices' => $advisory ),
				'featured_image'            => array( 'label' => __( 'Featured Image URL', 'limbenet-core' ), 'type' => 'url' ),
				'map_coordinates'           => array( 'label' => __( 'Map Coordinates', 'limbenet-core' ), 'type' => 'text' ),
				'top_attractions'           => array( 'label' => __( 'Top Attractions', 'limbenet-core' ), 'type' => 'textarea' ),
				'where_to_stay'             => array( 'label' => __( 'Where to Stay', 'limbenet-core' ), 'type' => 'textarea' ),
				'how_to_get_there'          => array( 'label' => __( 'How to Get There', 'limbenet-core' ), 'type' => 'textarea' ),
				'last_verified_date'        => array( 'label' => __( 'Last Verified Date', 'limbenet-core' ), 'type' => 'text' ),
				'featured'                  => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'travel_info' => array(
				'travel_info_subtitle' => array( 'label' => __( 'Subtitle', 'limbenet-core' ), 'type' => 'text' ),
				'summary'              => array( 'label' => __( 'Summary', 'limbenet-core' ), 'type' => 'textarea' ),
				'featured_image'       => array( 'label' => __( 'Featured Image URL', 'limbenet-core' ), 'type' => 'url' ),
				'key_points'           => array( 'label' => __( 'Key Points', 'limbenet-core' ), 'type' => 'textarea', 'description' => __( 'One planning point per line.', 'limbenet-core' ) ),
				'details'              => array( 'label' => __( 'Detailed Guidance', 'limbenet-core' ), 'type' => 'textarea' ),
				'official_links'       => array( 'label' => __( 'Official Source Links', 'limbenet-core' ), 'type' => 'textarea', 'description' => __( 'One per line in this format: Label | https://example.com', 'limbenet-core' ) ),
				'safety_notice'        => array( 'label' => __( 'Safety Notice', 'limbenet-core' ), 'type' => 'textarea' ),
				'advisory_level'       => array( 'label' => __( 'Advisory Level', 'limbenet-core' ), 'type' => 'select', 'choices' => $advisory ),
				'last_verified_date'   => array( 'label' => __( 'Last Verified Date', 'limbenet-core' ), 'type' => 'text' ),
				'source_notes'         => array( 'label' => __( 'Source Notes', 'limbenet-core' ), 'type' => 'textarea' ),
				'featured'             => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'itinerary'   => array(
				'duration'                  => array( 'label' => __( 'Duration', 'limbenet-core' ), 'type' => 'text' ),
				'starting_city'             => array( 'label' => __( 'Starting City', 'limbenet-core' ), 'type' => 'text' ),
				'ending_city'               => array( 'label' => __( 'Ending City', 'limbenet-core' ), 'type' => 'text' ),
				'budget_range'              => array( 'label' => __( 'Budget Range', 'limbenet-core' ), 'type' => 'text' ),
				'difficulty'                => array( 'label' => __( 'Difficulty', 'limbenet-core' ), 'type' => 'text' ),
				'best_for'                  => array( 'label' => __( 'Best For', 'limbenet-core' ), 'type' => 'textarea' ),
				'itinerary_days_repeater'   => array( 'label' => __( 'Itinerary Days', 'limbenet-core' ), 'type' => 'textarea' ),
				'included_attractions'      => array( 'label' => __( 'Included Attractions', 'limbenet-core' ), 'type' => 'textarea' ),
				'recommended_partners'      => array( 'label' => __( 'Recommended Partners', 'limbenet-core' ), 'type' => 'textarea' ),
				'safety_notes'              => array( 'label' => __( 'Safety Notes', 'limbenet-core' ), 'type' => 'textarea' ),
				'sponsored_content'         => array( 'label' => __( 'Sponsored Content', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
				'featured'                  => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'partner'     => array(
				'business_name'       => array( 'label' => __( 'Business Name', 'limbenet-core' ), 'type' => 'text' ),
				'business_type'       => array( 'label' => __( 'Business Type', 'limbenet-core' ), 'type' => 'select', 'choices' => array(
					''             => __( 'Select', 'limbenet-core' ),
					'hotel'        => __( 'Hotel', 'limbenet-core' ),
					'restaurant'   => __( 'Restaurant', 'limbenet-core' ),
					'tour-guide'   => __( 'Tour Guide', 'limbenet-core' ),
					'transport'    => __( 'Transport', 'limbenet-core' ),
					'attraction'   => __( 'Attraction', 'limbenet-core' ),
					'event'        => __( 'Event', 'limbenet-core' ),
					'photographer' => __( 'Photographer', 'limbenet-core' ),
					'other'        => __( 'Other', 'limbenet-core' ),
				) ),
				'city'                => array( 'label' => __( 'City', 'limbenet-core' ), 'type' => 'text' ),
				'region'              => array( 'label' => __( 'Region', 'limbenet-core' ), 'type' => 'text' ),
				'description'         => array( 'label' => __( 'Description', 'limbenet-core' ), 'type' => 'textarea' ),
				'phone'               => array( 'label' => __( 'Phone', 'limbenet-core' ), 'type' => 'text' ),
				'whatsapp'            => array( 'label' => __( 'WhatsApp', 'limbenet-core' ), 'type' => 'text' ),
				'email'               => array( 'label' => __( 'Email', 'limbenet-core' ), 'type' => 'email' ),
				'website'             => array( 'label' => __( 'Website', 'limbenet-core' ), 'type' => 'url' ),
				'booking_url'         => array( 'label' => __( 'Booking URL', 'limbenet-core' ), 'type' => 'url' ),
				'logo'                => array( 'label' => __( 'Logo URL', 'limbenet-core' ), 'type' => 'url' ),
				'gallery'             => array( 'label' => __( 'Gallery URLs or IDs', 'limbenet-core' ), 'type' => 'textarea' ),
				'price_range'         => array( 'label' => __( 'Price Range', 'limbenet-core' ), 'type' => 'text' ),
				'verified_partner'    => array( 'label' => __( 'Verified Partner', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
				'paid_plan'           => array( 'label' => __( 'Paid Plan', 'limbenet-core' ), 'type' => 'select', 'choices' => array(
					''         => __( 'Select', 'limbenet-core' ),
					'free'     => __( 'Free', 'limbenet-core' ),
					'verified' => __( 'Verified', 'limbenet-core' ),
					'featured' => __( 'Featured', 'limbenet-core' ),
					'premium'  => __( 'Premium', 'limbenet-core' ),
				) ),
				'listing_expiry_date' => array( 'label' => __( 'Listing Expiry Date', 'limbenet-core' ), 'type' => 'date' ),
				'sponsored_content'   => array( 'label' => __( 'Sponsored Content', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
				'featured'            => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'deal'        => array(
				'deal_title'        => array( 'label' => __( 'Deal Title', 'limbenet-core' ), 'type' => 'text' ),
				'partner'           => array( 'label' => __( 'Partner', 'limbenet-core' ), 'type' => 'text' ),
				'deal_type'         => array( 'label' => __( 'Deal Type', 'limbenet-core' ), 'type' => 'text' ),
				'description'       => array( 'label' => __( 'Description', 'limbenet-core' ), 'type' => 'textarea' ),
				'discount_text'     => array( 'label' => __( 'Discount Text', 'limbenet-core' ), 'type' => 'text' ),
				'start_date'        => array( 'label' => __( 'Start Date', 'limbenet-core' ), 'type' => 'date' ),
				'end_date'          => array( 'label' => __( 'End Date', 'limbenet-core' ), 'type' => 'date' ),
				'booking_url'       => array( 'label' => __( 'Booking URL', 'limbenet-core' ), 'type' => 'url' ),
				'coupon_code'       => array( 'label' => __( 'Coupon Code', 'limbenet-core' ), 'type' => 'text' ),
				'terms'             => array( 'label' => __( 'Terms', 'limbenet-core' ), 'type' => 'textarea' ),
				'sponsored_content' => array( 'label' => __( 'Sponsored Content', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
				'featured'          => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
			'event'       => array(
				'event_name'        => array( 'label' => __( 'Event Name', 'limbenet-core' ), 'type' => 'text' ),
				'event_type'        => array( 'label' => __( 'Event Type', 'limbenet-core' ), 'type' => 'text' ),
				'city'              => array( 'label' => __( 'City', 'limbenet-core' ), 'type' => 'text' ),
				'region'            => array( 'label' => __( 'Region', 'limbenet-core' ), 'type' => 'text' ),
				'start_date'        => array( 'label' => __( 'Start Date', 'limbenet-core' ), 'type' => 'date' ),
				'end_date'          => array( 'label' => __( 'End Date', 'limbenet-core' ), 'type' => 'date' ),
				'venue'             => array( 'label' => __( 'Venue', 'limbenet-core' ), 'type' => 'text' ),
				'description'       => array( 'label' => __( 'Description', 'limbenet-core' ), 'type' => 'textarea' ),
				'ticket_required'   => array( 'label' => __( 'Ticket Required', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no_unknown ),
				'ticket_url'        => array( 'label' => __( 'Ticket URL', 'limbenet-core' ), 'type' => 'url' ),
				'organizer_contact' => array( 'label' => __( 'Organizer Contact', 'limbenet-core' ), 'type' => 'textarea' ),
				'featured'          => array( 'label' => __( 'Featured', 'limbenet-core' ), 'type' => 'select', 'choices' => $yes_no ),
			),
		);
	}

	/**
	 * Register post meta.
	 */
	public function register_meta() {
		foreach ( self::fields() as $post_type => $fields ) {
			foreach ( $fields as $key => $field ) {
				register_post_meta(
					$post_type,
					$key,
					array(
						'single'            => true,
						'type'              => 'string',
						'show_in_rest'      => true,
						'sanitize_callback' => array( $this, 'sanitize_meta_value' ),
						'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
							return current_user_can( 'edit_post', $post_id );
						},
					)
				);
			}
		}
	}

	/**
	 * Sanitize a meta value.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public function sanitize_meta_value( $value ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', array_map( 'sanitize_text_field', $value ) );
		}

		return sanitize_textarea_field( (string) $value );
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		foreach ( array_keys( self::fields() ) as $post_type ) {
			add_meta_box(
				'limbenet_details',
				__( 'Limbe.Net Tourism Details', 'limbenet-core' ),
				array( $this, 'render_meta_box' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Current post.
	 */
	public function render_meta_box( $post ) {
		$fields = self::fields();
		if ( empty( $fields[ $post->post_type ] ) ) {
			return;
		}

		wp_nonce_field( 'limbenet_save_meta', 'limbenet_meta_nonce' );

		echo '<div class="lnet-admin-fields">';
		foreach ( $fields[ $post->post_type ] as $key => $field ) {
			$value       = get_post_meta( $post->ID, $key, true );
			$field_id    = 'limbenet_' . $key;
			$field_type  = isset( $field['type'] ) ? $field['type'] : 'text';
			$description = isset( $field['description'] ) ? $field['description'] : '';

			echo '<p class="lnet-admin-field">';
			echo '<label for="' . esc_attr( $field_id ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label>';

			if ( 'textarea' === $field_type ) {
				echo '<textarea class="widefat" rows="4" id="' . esc_attr( $field_id ) . '" name="limbenet_meta[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
			} elseif ( 'select' === $field_type ) {
				echo '<select class="widefat" id="' . esc_attr( $field_id ) . '" name="limbenet_meta[' . esc_attr( $key ) . ']">';
				foreach ( $field['choices'] as $choice_value => $choice_label ) {
					echo '<option value="' . esc_attr( $choice_value ) . '" ' . selected( $value, $choice_value, false ) . '>' . esc_html( $choice_label ) . '</option>';
				}
				echo '</select>';
			} else {
				$input_type = in_array( $field_type, array( 'url', 'email', 'date' ), true ) ? $field_type : 'text';
				echo '<input class="widefat" type="' . esc_attr( $input_type ) . '" id="' . esc_attr( $field_id ) . '" name="limbenet_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
			}

			if ( $description ) {
				echo '<span class="description">' . esc_html( $description ) . '</span>';
			}

			echo '</p>';
		}
		echo '</div>';
	}

	/**
	 * Save meta box fields.
	 *
	 * @param int $post_id Current post ID.
	 */
	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['limbenet_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['limbenet_meta_nonce'] ) ), 'limbenet_save_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		$fields    = self::fields();

		if ( empty( $fields[ $post_type ] ) || empty( $_POST['limbenet_meta'] ) || ! is_array( $_POST['limbenet_meta'] ) ) {
			return;
		}

		$posted = wp_unslash( $_POST['limbenet_meta'] );

		foreach ( $fields[ $post_type ] as $key => $field ) {
			$value = isset( $posted[ $key ] ) ? $posted[ $key ] : '';
			$value = $this->sanitize_field_by_type( $value, $field );
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Sanitize a field by configured type.
	 *
	 * @param mixed $value Raw value.
	 * @param array $field Field config.
	 * @return string
	 */
	private function sanitize_field_by_type( $value, $field ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		$value = is_scalar( $value ) ? (string) $value : '';

		if ( 'url' === $type ) {
			return esc_url_raw( $value );
		}

		if ( 'email' === $type ) {
			return sanitize_email( $value );
		}

		if ( 'textarea' === $type ) {
			return sanitize_textarea_field( $value );
		}

		return sanitize_text_field( $value );
	}
}
