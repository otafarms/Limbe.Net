<?php
/**
 * Seed content importer.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Imports starter tourism content.
 */
class LimbeNet_Core_Seed_Importer {
	/**
	 * Register admin page.
	 */
	public function register_menu() {
		add_submenu_page(
			'limbenet-tourism',
			__( 'Seed Importer', 'limbenet-core' ),
			__( 'Seed Importer', 'limbenet-core' ),
			'manage_options',
			'limbenet-seed-importer',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render importer page.
	 */
	public function render_page() {
		$count = isset( $_GET['imported'] ) ? absint( $_GET['imported'] ) : 0;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Limbe.Net Seed Importer', 'limbenet-core' ); ?></h1>
			<?php if ( $count ) : ?>
				<div class="notice notice-success"><p><?php echo esc_html( sprintf( __( 'Seed import completed. %d items were created or updated.', 'limbenet-core' ), $count ) ); ?></p></div>
			<?php endif; ?>
			<p><?php esc_html_e( 'Import starter pages, taxonomies, attractions, destinations, itineraries, partners, deals, and events. Prices stay unverified and safety notices remain visible placeholders.', 'limbenet-core' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'limbenet_import_seed', 'limbenet_seed_nonce' ); ?>
				<input type="hidden" name="action" value="limbenet_import_seed">
				<?php submit_button( __( 'Import Seed Content', 'limbenet-core' ), 'primary' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle import request.
	 */
	public function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import seed content.', 'limbenet-core' ) );
		}

		if ( empty( $_POST['limbenet_seed_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['limbenet_seed_nonce'] ) ), 'limbenet_import_seed' ) ) {
			wp_die( esc_html__( 'The import request could not be verified.', 'limbenet-core' ) );
		}

		$count = $this->import();
		wp_safe_redirect( add_query_arg( 'imported', absint( $count ), admin_url( 'admin.php?page=limbenet-seed-importer' ) ) );
		exit;
	}

	/**
	 * Import content.
	 *
	 * @return int Created/updated count.
	 */
	private function import() {
		$count = 0;
		$count += $this->import_terms();
		$count += $this->import_pages();
		$count += $this->import_destinations();
		$count += $this->import_attractions();
		$count += $this->import_itineraries();
		$count += $this->import_partners();
		$count += $this->import_deals();
		$count += $this->import_events();

		return $count;
	}

	/**
	 * Import taxonomy terms.
	 *
	 * @return int Count.
	 */
	private function import_terms() {
		$count = 0;
		$terms = array(
			'region'          => array( 'South West', 'Littoral', 'Centre', 'South Region', 'West Region', 'East Region', 'North Cameroon' ),
			'city'            => array( 'Limbe', 'Buea', 'Douala', 'Yaounde', 'Kribi', 'Foumban', 'Bamenda', 'Rhumsiki' ),
			'attraction_type' => array( 'Beaches', 'Wildlife & Safari', 'Mountains & Hiking', 'Culture & Heritage', 'Food & Nightlife', 'History', 'Festivals & Events', 'Eco-tourism', 'Family Trips', 'Weekend Trips' ),
			'travel_style'    => array( 'Beaches', 'Wildlife & Safari', 'Mountains & Hiking', 'Culture & Heritage', 'Food & Nightlife', 'History', 'Festivals & Events', 'Eco-tourism', 'Family Trips', 'Weekend Trips' ),
			'partner_type'    => array( 'Hotel', 'Restaurant', 'Tour Guide', 'Transport', 'Attraction', 'Event', 'Photographer', 'Other' ),
			'difficulty'      => array( 'Easy', 'Moderate', 'Challenging' ),
			'budget_range'    => array( 'Budget', 'Mid-range', 'Premium' ),
			'safety_status'   => array( 'Normal travel planning', 'Check current advisory before travel', 'High-risk area: travel only with expert local guidance' ),
		);

		foreach ( $terms as $taxonomy => $names ) {
			foreach ( $names as $name ) {
				$result = $this->ensure_term( $taxonomy, $name );
				if ( $result ) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Import core pages.
	 *
	 * @return int Count.
	 */
	private function import_pages() {
		$count = 0;
		$pages = array(
			array( 'Home', 'home', '' ),
			array( 'Things to Do', 'things-to-do', '[limbenet_travel_styles]' ),
			array( 'Tickets & Tours', 'tickets-tours', '[limbenet_ticket_help expanded="true"][limbenet_booking_form]' ),
			array( 'Travel Info', 'travel-info', '[limbenet_travel_info]' ),
			array( 'Partner Directory', 'partners-directory', '[limbenet_tourism_search post_type="partner" button_label="Filter partners"]' ),
			array( 'Partner With Us', 'partner-with-us', '[limbenet_partner_form]' ),
			array( 'Request Booking Help', 'request-booking-help', '[limbenet_booking_form]' ),
			array( 'Claim Listing', 'claim-listing', '[limbenet_claim_form]' ),
			array( 'Advertise With Limbe.Net', 'advertise-with-limbenet', '[limbenet_advertise_form]' ),
			array( 'Blog / Magazine', 'blog', '' ),
		);

		foreach ( $pages as $page ) {
			$page_id = $this->ensure_page( $page[0], $page[1], $page[2] );
			if ( $page_id ) {
				$count++;
			}

			if ( 'home' === $page[1] ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_id );
			}

			if ( 'blog' === $page[1] ) {
				update_option( 'page_for_posts', $page_id );
			}
		}

		return $count;
	}

	/**
	 * Import destinations.
	 *
	 * @return int Count.
	 */
	private function import_destinations() {
		$destinations = array(
			array( 'Limbe', 'South West', 'Coastal base for beaches, botanic gardens, wildlife education, and Mount Cameroon side trips.' ),
			array( 'Buea', 'South West', 'Mountain gateway with cooler weather, hiking access, and university-town energy.' ),
			array( 'Douala', 'Littoral', 'Cameroon city experiences, restaurants, nightlife, art, markets, and arrival logistics.' ),
			array( 'Yaounde', 'Centre', 'Cultural attractions, museums, day trips, and administrative city experiences.' ),
			array( 'Kribi', 'South Region', 'Beach escapes, coastal food, waterfalls, and relaxed weekend travel.' ),
			array( 'Foumban', 'West Region', 'Royal heritage, craft traditions, architecture, and cultural learning.' ),
			array( 'Bamenda', 'North West', 'Highland culture and scenic travel planning that requires current advisory checks.' ),
			array( 'North Cameroon', 'North Cameroon', 'Desert-edge landscapes, parks, and cultural routes that require careful current planning.' ),
			array( 'West Region', 'West Region', 'Highland towns, palaces, craft heritage, and road-trip itineraries.' ),
			array( 'South Region', 'South Region', 'Coastal, forest, and ecotourism routes around Kribi and beyond.' ),
			array( 'East Region', 'East Region', 'Forest reserves, wildlife conservation, and remote travel planning.' ),
		);

		$count = 0;
		foreach ( $destinations as $index => $destination ) {
			$title  = $destination[0];
			$region = $destination[1];
			$meta   = array(
				'destination_name'         => $title,
				'region'                   => $region,
				'overview'                 => $destination[2],
				'best_for'                 => 'Culture, nature, food, and responsible travel planning.',
				'travel_time_from_douala'  => 'Needs verification.',
				'travel_time_from_yaounde' => 'Needs verification.',
				'safety_notice'            => 'Check current travel advisory before planning this trip.',
				'advisory_level'           => 'check-before-travel',
				'top_attractions'          => 'Needs verification.',
				'where_to_stay'            => 'Use verified partner listings where available.',
				'how_to_get_there'         => 'Confirm current transport options before travel.',
				'last_verified_date'       => 'Needs verification.',
				'featured'                 => $index < 6 ? 'yes' : 'no',
			);

			if ( 'Limbe' === $title ) {
				$meta['featured_image'] = LIMBENET_CORE_URL . 'assets/images/limbe-city-featured.webp';
			}

			$post_id = $this->ensure_post(
				'destination',
				$title,
				$destination[2],
				$meta,
				array(
					'region'        => array( $region ),
					'safety_status' => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import attractions.
	 *
	 * @return int Count.
	 */
	private function import_attractions() {
		$attractions = array(
			array( 'Limbe Botanic Garden', 'Limbe', 'South West', 'Eco-tourism', 'A historic garden and nature stop for slow walks, plant learning, and relaxed Limbe planning.' ),
			array( 'Limbe Wildlife Centre', 'Limbe', 'South West', 'Wildlife & Safari', 'A conservation-focused wildlife education stop that should be planned with current visitor information.' ),
			array( 'Bimbia Slave Trade Site', 'Limbe', 'South West', 'History', 'A sensitive heritage site connected to Cameroon history and remembrance.' ),
			array( 'Mount Cameroon', 'Buea', 'South West', 'Mountains & Hiking', 'A major hiking and volcanic landscape experience requiring guide planning and current safety checks.' ),
			array( 'Kribi Beaches', 'Kribi', 'South Region', 'Beaches', 'Coastal beach experiences around Kribi with food, relaxation, and weekend travel potential.' ),
			array( 'Lobe Falls', 'Kribi', 'South Region', 'Eco-tourism', 'A waterfall and coastal nature stop near Kribi that requires current access and ticket verification.' ),
			array( 'Foumban Royal Palace', 'Foumban', 'West Region', 'Culture & Heritage', 'A cultural heritage attraction connected to royal history, architecture, and craft traditions.' ),
			array( 'Dja Faunal Reserve', 'Somalomo', 'East Region', 'Wildlife & Safari', 'A major forest reserve experience that requires expert local planning and current access verification.' ),
			array( 'Waza National Park', 'Waza', 'North Cameroon', 'Wildlife & Safari', 'A northern park listing that requires current advisory checks and expert local guidance.' ),
			array( 'Korup National Park', 'Mundemba', 'South West', 'Eco-tourism', 'A rainforest and biodiversity destination requiring verified logistics and local guidance.' ),
			array( 'Mefou Primate Sanctuary', 'Yaounde', 'Centre', 'Wildlife & Safari', 'A primate conservation day-trip option near Yaounde with current visitor details to verify.' ),
			array( 'Ebogo Ecotourism Site', 'Ebogo', 'South Region', 'Eco-tourism', 'A river and nature-focused ecotourism stop that should be planned with local guidance.' ),
			array( 'Nkolandom Caves', 'Nkolandom', 'South Region', 'Family Trips', 'A cave and leisure attraction listing for family travel planning with ticket details to verify.' ),
			array( 'Rhumsiki', 'Rhumsiki', 'North Cameroon', 'Culture & Heritage', 'A dramatic northern landscape and cultural route requiring current advisory checks.' ),
			array( 'Douala city experiences', 'Douala', 'Littoral', 'Food & Nightlife', 'Urban food, market, art, music, and nightlife experiences for city-focused visitors.' ),
			array( 'Yaounde cultural attractions', 'Yaounde', 'Centre', 'Culture & Heritage', 'Museums, monuments, food, and cultural stops for visitors spending time in Yaounde.' ),
		);

		$count = 0;
		foreach ( $attractions as $index => $attraction ) {
			$title = $attraction[0];
			$city  = $attraction[1];
			$region = $attraction[2];
			$type  = $attraction[3];
			$desc  = $attraction[4];

			$post_id = $this->ensure_post(
				'attraction',
				$title,
				$desc,
				array(
					'attraction_subtitle'  => $type,
					'region'               => $region,
					'city'                 => $city,
					'attraction_type'      => $type,
					'short_description'    => $desc,
					'full_description'     => $desc . ' Limbe.Net keeps ticket prices unverified until a reliable source or official link is added.',
					'opening_hours'        => 'Needs verification.',
					'best_time_to_visit'   => 'Needs verification.',
					'recommended_duration' => 'Needs verification.',
					'ticket_required'      => 'unknown',
					'ticket_price_range'   => 'Price not yet verified.',
					'safety_notice'        => 'Check current travel advisory before planning this trip.',
					'advisory_level'       => 'check-before-travel',
					'accessibility_notes'  => 'Needs verification.',
					'family_friendly'      => 'yes',
					'nearby_hotels'        => 'See verified partner listings where available.',
					'nearby_restaurants'   => 'See verified partner listings where available.',
					'nearby_attractions'   => 'Needs verification.',
					'how_to_get_there'     => 'Confirm current transport routes, road conditions, and guide requirements before travel.',
					'last_verified_date'   => 'Needs verification.',
					'source_notes'         => 'Add official links where available.',
					'featured'             => $index < 8 ? 'yes' : 'no',
				),
				array(
					'region'          => array( $region ),
					'city'            => array( $city ),
					'attraction_type' => array( $type ),
					'travel_style'    => array( $type ),
					'safety_status'   => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample itineraries.
	 *
	 * @return int Count.
	 */
	private function import_itineraries() {
		$items = array(
			array( 'Three Days in Limbe and Buea', '3 days', 'Limbe', 'Buea' ),
			array( 'Cameroon Coastal Weekend', '2 days', 'Douala', 'Kribi' ),
			array( 'Culture and Wildlife Highlights', '5 days', 'Yaounde', 'Foumban' ),
		);

		$count = 0;
		foreach ( $items as $index => $item ) {
			$post_id = $this->ensure_post(
				'itinerary',
				$item[0],
				'Sample itinerary framework. Replace with verified travel times, partners, and official links before publishing as final guidance.',
				array(
					'duration'                => $item[1],
					'starting_city'           => $item[2],
					'ending_city'             => $item[3],
					'budget_range'            => 'Needs verification.',
					'difficulty'              => 'Moderate',
					'best_for'                => 'Visitors who want structured Cameroon planning.',
					'itinerary_days_repeater' => "Day 1: Needs verification.\nDay 2: Needs verification.\nDay 3: Needs verification.",
					'included_attractions'    => 'Needs verification.',
					'recommended_partners'    => 'Use verified partner listings where available.',
					'safety_notes'            => 'Check current travel advisory before planning this trip.',
					'sponsored_content'       => 'no',
					'featured'                => $index < 3 ? 'yes' : 'no',
				),
				array(
					'travel_style'  => array( 'Weekend Trips' ),
					'difficulty'    => array( 'Moderate' ),
					'budget_range'  => array( 'Mid-range' ),
					'safety_status' => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample partners.
	 *
	 * @return int Count.
	 */
	private function import_partners() {
		$items = array(
			array( 'Sample Verified Tour Guide', 'tour-guide', 'Limbe', 'South West' ),
			array( 'Sample Coastal Transport Partner', 'transport', 'Douala', 'Littoral' ),
			array( 'Sample Heritage Photographer', 'photographer', 'Yaounde', 'Centre' ),
		);

		$count = 0;
		foreach ( $items as $index => $item ) {
			$post_id = $this->ensure_post(
				'partner',
				$item[0],
				'Sample partner listing for layout and workflow testing. Replace with verified business details before public launch.',
				array(
					'business_name'       => $item[0],
					'business_type'       => $item[1],
					'city'                => $item[2],
					'region'              => $item[3],
					'description'         => 'Sample partner listing for layout and workflow testing.',
					'phone'               => '',
					'whatsapp'            => '',
					'email'               => '',
					'website'             => '',
					'booking_url'         => '',
					'price_range'         => 'Needs verification.',
					'verified_partner'    => $index < 1 ? 'yes' : 'no',
					'paid_plan'           => $index < 1 ? 'verified' : 'free',
					'listing_expiry_date' => '',
					'sponsored_content'   => 'no',
					'featured'            => $index < 1 ? 'yes' : 'no',
				),
				array(
					'region'       => array( $item[3] ),
					'city'         => array( $item[2] ),
					'partner_type' => array( ucwords( str_replace( '-', ' ', $item[1] ) ) ),
					'budget_range' => array( 'Mid-range' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample deals.
	 *
	 * @return int Count.
	 */
	private function import_deals() {
		$items = array(
			array( 'Sample Partner Welcome Deal', 'tour', 'Sample Verified Tour Guide' ),
			array( 'Sample Weekend Transport Offer', 'transport', 'Sample Coastal Transport Partner' ),
		);

		$count = 0;
		foreach ( $items as $item ) {
			$post_id = $this->ensure_post(
				'deal',
				$item[0],
				'Sample deal listing. Replace terms, dates, and booking link after partner verification.',
				array(
					'deal_title'        => $item[0],
					'partner'           => $item[2],
					'deal_type'         => $item[1],
					'description'       => 'Sample deal listing. Replace after partner verification.',
					'discount_text'     => 'Discount details not yet verified.',
					'start_date'        => '',
					'end_date'          => '',
					'booking_url'       => '',
					'coupon_code'       => '',
					'terms'             => 'Terms need verification.',
					'sponsored_content' => 'yes',
					'featured'          => 'yes',
				),
				array(
					'budget_range' => array( 'Mid-range' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample events.
	 *
	 * @return int Count.
	 */
	private function import_events() {
		$post_id = $this->ensure_post(
			'event',
			'Sample Cultural Event Listing',
			'Sample event listing. Add verified dates, ticket links, organizer contact, and venue details before launch.',
			array(
				'event_name'        => 'Sample Cultural Event Listing',
				'event_type'        => 'Culture',
				'city'              => 'Limbe',
				'region'            => 'South West',
				'start_date'        => '',
				'end_date'          => '',
				'venue'             => 'Needs verification.',
				'description'       => 'Sample event listing for workflow testing.',
				'ticket_required'   => 'unknown',
				'ticket_url'        => '',
				'organizer_contact' => 'Needs verification.',
				'featured'          => 'yes',
			),
			array(
				'region' => array( 'South West' ),
				'city'   => array( 'Limbe' ),
			)
		);

		return $post_id ? 1 : 0;
	}

	/**
	 * Ensure a term exists.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $name Term name.
	 * @return int Term ID or zero.
	 */
	private function ensure_term( $taxonomy, $name ) {
		$term = term_exists( $name, $taxonomy );
		if ( $term && ! is_wp_error( $term ) ) {
			return (int) $term['term_id'];
		}

		$result = wp_insert_term( $name, $taxonomy );
		if ( is_wp_error( $result ) ) {
			return 0;
		}

		return (int) $result['term_id'];
	}

	/**
	 * Ensure page exists.
	 *
	 * @param string $title Page title.
	 * @param string $slug Page slug.
	 * @param string $content Page content.
	 * @return int Post ID.
	 */
	private function ensure_page( $title, $slug, $content ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page ) {
			wp_update_post(
				array(
					'ID'           => $page->ID,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
			return (int) $page->ID;
		}

		return (int) wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
	}

	/**
	 * Ensure a custom post exists and update fields.
	 *
	 * @param string $post_type Post type.
	 * @param string $title Title.
	 * @param string $content Content.
	 * @param array  $meta Meta values.
	 * @param array  $terms Taxonomy terms.
	 * @return int Post ID.
	 */
	private function ensure_post( $post_type, $title, $content, $meta, $terms = array() ) {
		$existing = get_posts(
			array(
				'post_type'      => $post_type,
				'title'          => $title,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( $existing ) {
			$post_id = (int) $existing[0];
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
		} else {
			$post_id = (int) wp_insert_post(
				array(
					'post_type'    => $post_type,
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_content' => $content,
					'post_excerpt' => wp_trim_words( $content, 28, '' ),
				)
			);
		}

		if ( ! $post_id ) {
			return 0;
		}

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		foreach ( $terms as $taxonomy => $names ) {
			wp_set_object_terms( $post_id, $names, $taxonomy, false );
		}

		return $post_id;
	}
}
