<?php
/**
 * Polylang integration.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Makes Limbe.Net content translatable with Polylang.
 */
class LimbeNet_Core_Polylang_Integration {
	/**
	 * Get public content types that should have translations.
	 *
	 * @return array
	 */
	private function translatable_post_types() {
		return LimbeNet_Core_Post_Types::public_types();
	}

	/**
	 * Get taxonomy keys that should have translations.
	 *
	 * @return array
	 */
	private function translatable_taxonomies() {
		return array(
			'region',
			'city',
			'attraction_type',
			'travel_style',
			'partner_type',
			'difficulty',
			'budget_range',
			'safety_status',
		);
	}

	/**
	 * Tell Polylang that Limbe.Net public post types are translatable.
	 *
	 * @param array $post_types Registered post types.
	 * @param bool  $is_settings Whether the filter is being used on Polylang settings.
	 * @return array
	 */
	public function register_post_types( $post_types, $is_settings = false ) {
		foreach ( $this->translatable_post_types() as $post_type ) {
			$post_types[ $post_type ] = $post_type;
		}

		return $post_types;
	}

	/**
	 * Tell Polylang that Limbe.Net public taxonomies are translatable.
	 *
	 * @param array $taxonomies Registered taxonomies.
	 * @param bool  $is_settings Whether the filter is being used on Polylang settings.
	 * @return array
	 */
	public function register_taxonomies( $taxonomies, $is_settings = false ) {
		foreach ( $this->translatable_taxonomies() as $taxonomy ) {
			$taxonomies[ $taxonomy ] = $taxonomy;
		}

		return $taxonomies;
	}

	/**
	 * Copy Limbe.Net custom fields into newly created translations.
	 *
	 * @param array  $metas Meta keys.
	 * @param bool   $sync Whether Polylang is synchronizing posts.
	 * @param int    $from Source post ID.
	 * @param int    $to Target post ID.
	 * @param string $lang Target language.
	 * @return array
	 */
	public function copy_post_metas( $metas, $sync = false, $from = 0, $to = 0, $lang = '' ) {
		$fields = LimbeNet_Core_Meta_Boxes::fields();

		foreach ( $fields as $post_fields ) {
			foreach ( array_keys( $post_fields ) as $key ) {
				$metas[] = $key;
			}
		}

		return array_values( array_unique( $metas ) );
	}

	/**
	 * Copy Limbe.Net taxonomy assignments into newly created translations.
	 *
	 * @param array  $taxonomies Taxonomy keys.
	 * @param bool   $sync Whether Polylang is synchronizing posts.
	 * @param int    $from Source post ID.
	 * @param int    $to Target post ID.
	 * @param string $lang Target language.
	 * @return array
	 */
	public function copy_taxonomies( $taxonomies, $sync = false, $from = 0, $to = 0, $lang = '' ) {
		return array_values( array_unique( array_merge( $taxonomies, $this->translatable_taxonomies() ) ) );
	}

	/**
	 * Register configurable text settings in Polylang string translations.
	 */
	public function register_strings() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$settings = LimbeNet_Core_Settings::get_settings();
		$strings  = array(
			'Contact Address'      => array( 'value' => $settings['contact_address'], 'multiline' => true ),
			'Business Hours'       => array( 'value' => $settings['contact_business_hours'], 'multiline' => true ),
			'Affiliate Disclosure' => array( 'value' => $settings['affiliate_disclosure'], 'multiline' => true ),
			'Safety Disclaimer'    => array( 'value' => $settings['safety_disclaimer'], 'multiline' => true ),
		);

		foreach ( $strings as $name => $string ) {
			if ( '' === trim( (string) $string['value'] ) ) {
				continue;
			}

			pll_register_string( 'Limbe.Net ' . $name, $string['value'], 'Limbe.Net Tourism', $string['multiline'] );
		}
	}

	/**
	 * Translate a configured text string when Polylang is active.
	 *
	 * @param string $text Original text.
	 * @return string
	 */
	public static function translate_string( $text ) {
		if ( function_exists( 'pll__' ) && '' !== trim( (string) $text ) ) {
			return pll__( $text );
		}

		return $text;
	}
}
