<?php
/**
 * Taxonomy registration.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers tourism taxonomies.
 */
class LimbeNet_Core_Taxonomies {
	/**
	 * Register taxonomies.
	 */
	public function register() {
		$this->register_taxonomy( 'region', __( 'Regions', 'limbenet-core' ), __( 'Region', 'limbenet-core' ), array( 'attraction', 'destination', 'itinerary', 'partner', 'deal', 'event' ), 'regions' );
		$this->register_taxonomy( 'city', __( 'Cities', 'limbenet-core' ), __( 'City', 'limbenet-core' ), array( 'attraction', 'destination', 'partner', 'event' ), 'cities' );
		$this->register_taxonomy( 'attraction_type', __( 'Attraction Types', 'limbenet-core' ), __( 'Attraction Type', 'limbenet-core' ), array( 'attraction' ), 'attraction-type' );
		$this->register_taxonomy( 'travel_style', __( 'Travel Styles', 'limbenet-core' ), __( 'Travel Style', 'limbenet-core' ), array( 'attraction', 'destination', 'itinerary' ), 'travel-style' );
		$this->register_taxonomy( 'partner_type', __( 'Partner Types', 'limbenet-core' ), __( 'Partner Type', 'limbenet-core' ), array( 'partner' ), 'partner-type' );
		$this->register_taxonomy( 'difficulty', __( 'Difficulties', 'limbenet-core' ), __( 'Difficulty', 'limbenet-core' ), array( 'attraction', 'itinerary' ), 'difficulty' );
		$this->register_taxonomy( 'budget_range', __( 'Budget Ranges', 'limbenet-core' ), __( 'Budget Range', 'limbenet-core' ), array( 'itinerary', 'partner', 'deal' ), 'budget-range' );
		$this->register_taxonomy( 'safety_status', __( 'Safety Statuses', 'limbenet-core' ), __( 'Safety Status', 'limbenet-core' ), array( 'attraction', 'destination', 'itinerary' ), 'safety-status' );
	}

	/**
	 * Register a single taxonomy.
	 *
	 * @param string $taxonomy Taxonomy key.
	 * @param string $plural Plural label.
	 * @param string $singular Singular label.
	 * @param array  $post_types Assigned post types.
	 * @param string $slug Rewrite slug.
	 */
	private function register_taxonomy( $taxonomy, $plural, $singular, $post_types, $slug ) {
		register_taxonomy(
			$taxonomy,
			$post_types,
			array(
				'labels'            => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'search_items'      => sprintf(
						/* translators: %s: taxonomy plural label. */
						__( 'Search %s', 'limbenet-core' ),
						$plural
					),
					'all_items'         => sprintf(
						/* translators: %s: taxonomy plural label. */
						__( 'All %s', 'limbenet-core' ),
						$plural
					),
					'edit_item'         => sprintf(
						/* translators: %s: taxonomy singular label. */
						__( 'Edit %s', 'limbenet-core' ),
						$singular
					),
					'update_item'       => sprintf(
						/* translators: %s: taxonomy singular label. */
						__( 'Update %s', 'limbenet-core' ),
						$singular
					),
					'add_new_item'      => sprintf(
						/* translators: %s: taxonomy singular label. */
						__( 'Add New %s', 'limbenet-core' ),
						$singular
					),
					'new_item_name'     => sprintf(
						/* translators: %s: taxonomy singular label. */
						__( 'New %s Name', 'limbenet-core' ),
						$singular
					),
					'menu_name'         => $singular,
					'not_found'         => __( 'No terms found.', 'limbenet-core' ),
					'back_to_items'     => sprintf(
						/* translators: %s: taxonomy plural label. */
						__( 'Back to %s', 'limbenet-core' ),
						$plural
					),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => $slug,
					'with_front' => false,
				),
			)
		);
	}
}
