<?php
/**
 * SEO schema helpers.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prints lightweight structured data.
 */
class LimbeNet_Core_Schema {
	/**
	 * Print JSON-LD schema.
	 */
	public function print_schema() {
		$graph = array();

		if ( is_singular( 'attraction' ) ) {
			$graph[] = $this->attraction_schema( get_the_ID() );
			$graph[] = $this->faq_schema( get_the_ID() );
		} elseif ( is_singular( 'partner' ) ) {
			$graph[] = $this->partner_schema( get_the_ID() );
		} elseif ( is_singular( 'event' ) ) {
			$graph[] = $this->event_schema( get_the_ID() );
		}

		if ( is_singular( LimbeNet_Core_Post_Types::public_types() ) ) {
			$graph[] = $this->breadcrumb_schema( get_the_ID() );
		}

		$graph = array_filter( $graph );
		if ( ! $graph ) {
			return;
		}

		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}

	/**
	 * Print fallback meta descriptions when major SEO plugins are absent.
	 */
	public function print_meta_description() {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		$description = '';

		if ( is_singular( LimbeNet_Core_Post_Types::public_types() ) ) {
			$post_id = get_the_ID();
			foreach ( array( 'short_description', 'overview', 'description', 'discount_text' ) as $key ) {
				$value = get_post_meta( $post_id, $key, true );
				if ( $value ) {
					$description = $value;
					break;
				}
			}

			if ( ! $description ) {
				$description = get_the_excerpt( $post_id );
			}
		} elseif ( is_post_type_archive( 'attraction' ) ) {
			$description = __( 'Browse Cameroon attractions with safety notices, ticket guidance, maps, and booking help from Limbe.Net.', 'limbenet-core' );
		} elseif ( is_post_type_archive( 'destination' ) ) {
			$description = __( 'Explore Cameroon destinations including Limbe, Buea, Douala, Yaounde, Kribi, Foumban, and more.', 'limbenet-core' );
		} elseif ( is_post_type_archive( 'deal' ) ) {
			$description = __( 'Find partner deals for Cameroon hotels, tours, transport, attractions, and travel experiences.', 'limbenet-core' );
		}

		$description = wp_trim_words( wp_strip_all_tags( $description ), 28, '' );
		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		}
	}

	/**
	 * TouristAttraction schema.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function attraction_schema( $post_id ) {
		$schema = array(
			'@type'       => 'TouristAttraction',
			'@id'         => get_permalink( $post_id ) . '#tourist-attraction',
			'name'        => get_the_title( $post_id ),
			'description' => $this->description( $post_id ),
			'url'         => get_permalink( $post_id ),
		);

		$image = get_the_post_thumbnail_url( $post_id, 'full' );
		if ( $image ) {
			$schema['image'] = $image;
		}

		$address = get_post_meta( $post_id, 'address', true );
		if ( $address ) {
			$schema['address'] = array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $address,
				'addressCountry'  => 'CM',
				'addressLocality' => get_post_meta( $post_id, 'city', true ),
				'addressRegion'   => get_post_meta( $post_id, 'region', true ),
			);
		}

		$latitude  = get_post_meta( $post_id, 'latitude', true );
		$longitude = get_post_meta( $post_id, 'longitude', true );
		if ( $latitude && $longitude ) {
			$schema['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => $latitude,
				'longitude' => $longitude,
			);
		}

		$schema['additionalProperty'] = array(
			array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Safety status', 'limbenet-core' ),
				'value' => get_post_meta( $post_id, 'advisory_level', true ) ?: 'check-before-travel',
			),
			array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Last verified', 'limbenet-core' ),
				'value' => get_post_meta( $post_id, 'last_verified_date', true ) ?: __( 'Needs verification', 'limbenet-core' ),
			),
		);

		return $schema;
	}

	/**
	 * LocalBusiness schema.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function partner_schema( $post_id ) {
		$schema = array(
			'@type'       => 'LocalBusiness',
			'@id'         => get_permalink( $post_id ) . '#local-business',
			'name'        => get_post_meta( $post_id, 'business_name', true ) ?: get_the_title( $post_id ),
			'description' => $this->description( $post_id ),
			'url'         => get_permalink( $post_id ),
			'address'     => array(
				'@type'           => 'PostalAddress',
				'addressCountry'  => 'CM',
				'addressLocality' => get_post_meta( $post_id, 'city', true ),
				'addressRegion'   => get_post_meta( $post_id, 'region', true ),
			),
		);

		$phone = get_post_meta( $post_id, 'phone', true );
		if ( $phone ) {
			$schema['telephone'] = $phone;
		}

		$website = get_post_meta( $post_id, 'website', true );
		if ( $website ) {
			$schema['sameAs'] = array( $website );
		}

		return $schema;
	}

	/**
	 * Event schema.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function event_schema( $post_id ) {
		$schema = array(
			'@type'       => 'Event',
			'@id'         => get_permalink( $post_id ) . '#event',
			'name'        => get_post_meta( $post_id, 'event_name', true ) ?: get_the_title( $post_id ),
			'description' => $this->description( $post_id ),
			'url'         => get_permalink( $post_id ),
			'eventStatus' => 'https://schema.org/EventScheduled',
			'location'    => array(
				'@type'   => 'Place',
				'name'    => get_post_meta( $post_id, 'venue', true ),
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressCountry'  => 'CM',
					'addressLocality' => get_post_meta( $post_id, 'city', true ),
					'addressRegion'   => get_post_meta( $post_id, 'region', true ),
				),
			),
		);

		$start = get_post_meta( $post_id, 'start_date', true );
		$end   = get_post_meta( $post_id, 'end_date', true );

		if ( $start ) {
			$schema['startDate'] = $start;
		}

		if ( $end ) {
			$schema['endDate'] = $end;
		}

		return $schema;
	}

	/**
	 * FAQ schema for attraction pages.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function faq_schema( $post_id ) {
		return array(
			'@type'      => 'FAQPage',
			'@id'        => get_permalink( $post_id ) . '#faq',
			'mainEntity' => array(
				array(
					'@type'          => 'Question',
					'name'           => __( 'Do I need a ticket?', 'limbenet-core' ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => get_post_meta( $post_id, 'ticket_required', true ) ? get_post_meta( $post_id, 'ticket_required', true ) : __( 'Ticket requirement not yet verified.', 'limbenet-core' ),
					),
				),
				array(
					'@type'          => 'Question',
					'name'           => __( 'How current is this information?', 'limbenet-core' ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => get_post_meta( $post_id, 'last_verified_date', true ) ?: __( 'Needs verification.', 'limbenet-core' ),
					),
				),
			),
		);
	}

	/**
	 * Breadcrumb schema.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function breadcrumb_schema( $post_id ) {
		$post_type = get_post_type( $post_id );
		$type_obj  = get_post_type_object( $post_type );
		$archive   = get_post_type_archive_link( $post_type );

		$items = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Home', 'limbenet-core' ),
				'item'     => home_url( '/' ),
			),
		);

		if ( $archive && $type_obj ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $type_obj->labels->name,
				'item'     => $archive,
			);
		}

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => count( $items ) + 1,
			'name'     => get_the_title( $post_id ),
			'item'     => get_permalink( $post_id ),
		);

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => get_permalink( $post_id ) . '#breadcrumb',
			'itemListElement' => $items,
		);
	}

	/**
	 * Get description.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function description( $post_id ) {
		foreach ( array( 'short_description', 'overview', 'description', 'full_description' ) as $key ) {
			$value = get_post_meta( $post_id, $key, true );
			if ( $value ) {
				return wp_trim_words( wp_strip_all_tags( $value ), 32, '' );
			}
		}

		return wp_trim_words( get_the_excerpt( $post_id ), 32, '' );
	}
}
