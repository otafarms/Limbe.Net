<?php
/**
 * Custom post type registration.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers tourism post types.
 */
class LimbeNet_Core_Post_Types {
	/**
	 * Public tourism post types.
	 *
	 * @return array
	 */
	public static function public_types() {
		return array( 'attraction', 'destination', 'travel_info', 'itinerary', 'partner', 'deal', 'event' );
	}

	/**
	 * All post types created by the plugin.
	 *
	 * @return array
	 */
	public static function all_types() {
		return array_merge( self::public_types(), array( 'limbenet_request' ) );
	}

	/**
	 * Register post types.
	 */
	public function register() {
		register_post_type(
			'attraction',
			$this->public_args(
				__( 'Attractions', 'limbenet-core' ),
				__( 'Attraction', 'limbenet-core' ),
				'attractions',
				'dashicons-palmtree'
			)
		);

		register_post_type(
			'destination',
			$this->public_args(
				__( 'Destinations', 'limbenet-core' ),
				__( 'Destination', 'limbenet-core' ),
				'places-to-go',
				'dashicons-location'
			)
		);

		register_post_type(
			'travel_info',
			$this->travel_info_args()
		);

		register_post_type(
			'itinerary',
			$this->public_args(
				__( 'Trip Ideas', 'limbenet-core' ),
				__( 'Itinerary', 'limbenet-core' ),
				'trip-ideas',
				'dashicons-list-view'
			)
		);

		register_post_type(
			'partner',
			$this->public_args(
				__( 'Partners', 'limbenet-core' ),
				__( 'Partner', 'limbenet-core' ),
				'partners',
				'dashicons-businessperson'
			)
		);

		register_post_type(
			'deal',
			$this->public_args(
				__( 'Deals', 'limbenet-core' ),
				__( 'Deal', 'limbenet-core' ),
				'deals',
				'dashicons-tickets-alt'
			)
		);

		register_post_type(
			'event',
			$this->public_args(
				__( 'Events', 'limbenet-core' ),
				__( 'Event', 'limbenet-core' ),
				'events',
				'dashicons-calendar-alt'
			)
		);

		register_post_type(
			'limbenet_request',
			array(
				'labels'              => $this->labels( __( 'Booking Requests', 'limbenet-core' ), __( 'Booking Request', 'limbenet-core' ) ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'limbenet-tourism',
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'menu_icon'           => 'dashicons-email-alt2',
				'supports'            => array( 'title', 'editor' ),
			)
		);
	}

	/**
	 * Build public post type args.
	 *
	 * @param string $plural  Plural label.
	 * @param string $singular Singular label.
	 * @param string $slug    Rewrite/archive slug.
	 * @param string $icon    Dashicon name.
	 * @return array
	 */
	private function public_args( $plural, $singular, $slug, $icon ) {
		return array(
			'labels'             => $this->labels( $plural, $singular ),
			'public'             => true,
			'has_archive'        => $slug,
			'rewrite'            => array(
				'slug'       => $slug,
				'with_front' => false,
			),
			'menu_icon'          => $icon,
			'show_in_menu'       => 'limbenet-tourism',
			'show_in_rest'       => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'query_var'          => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'delete_with_user'   => false,
			'publicly_queryable' => true,
		);
	}

	/**
	 * Build travel info post type args.
	 *
	 * Uses the /travel-info/{slug}/ permalink while leaving the /travel-info/
	 * page available as the editable hub.
	 *
	 * @return array
	 */
	private function travel_info_args() {
		$args = $this->public_args(
			__( 'Travel Info', 'limbenet-core' ),
			__( 'Travel Info Page', 'limbenet-core' ),
			'travel-info',
			'dashicons-info-outline'
		);

		$args['has_archive'] = false;
		$args['supports']    = array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' );

		return $args;
	}

	/**
	 * Build labels.
	 *
	 * @param string $plural Plural label.
	 * @param string $singular Singular label.
	 * @return array
	 */
	private function labels( $plural, $singular ) {
		return array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new_item'          => sprintf(
				/* translators: %s: post type singular label. */
				__( 'Add New %s', 'limbenet-core' ),
				$singular
			),
			'edit_item'             => sprintf(
				/* translators: %s: post type singular label. */
				__( 'Edit %s', 'limbenet-core' ),
				$singular
			),
			'new_item'              => sprintf(
				/* translators: %s: post type singular label. */
				__( 'New %s', 'limbenet-core' ),
				$singular
			),
			'view_item'             => sprintf(
				/* translators: %s: post type singular label. */
				__( 'View %s', 'limbenet-core' ),
				$singular
			),
			'search_items'          => sprintf(
				/* translators: %s: post type plural label. */
				__( 'Search %s', 'limbenet-core' ),
				$plural
			),
			'not_found'             => sprintf(
				/* translators: %s: post type plural label. */
				__( 'No %s found.', 'limbenet-core' ),
				strtolower( $plural )
			),
			'all_items'             => $plural,
			'archives'              => $plural,
			'attributes'            => sprintf(
				/* translators: %s: post type singular label. */
				__( '%s Attributes', 'limbenet-core' ),
				$singular
			),
			'insert_into_item'      => sprintf(
				/* translators: %s: post type singular label. */
				__( 'Insert into %s', 'limbenet-core' ),
				strtolower( $singular )
			),
			'uploaded_to_this_item' => sprintf(
				/* translators: %s: post type singular label. */
				__( 'Uploaded to this %s', 'limbenet-core' ),
				strtolower( $singular )
			),
		);
	}
}
