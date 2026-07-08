<?php
/**
 * Frontend shortcodes and search filters.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend rendering.
 */
class LimbeNet_Core_Shortcodes {
	/**
	 * Register shortcodes.
	 */
	public function register() {
		add_shortcode( 'limbenet_language_switcher', array( $this, 'language_switcher' ) );
		add_shortcode( 'limbenet_tourism_search', array( $this, 'tourism_search' ) );
		add_shortcode( 'limbenet_featured', array( $this, 'featured' ) );
		add_shortcode( 'limbenet_travel_styles', array( $this, 'travel_styles' ) );
		add_shortcode( 'limbenet_plan_trip', array( $this, 'plan_trip' ) );
		add_shortcode( 'limbenet_ticket_help', array( $this, 'ticket_help' ) );
		add_shortcode( 'limbenet_travel_info', array( $this, 'travel_info' ) );
		add_shortcode( 'limbenet_partner_cta', array( $this, 'partner_cta' ) );
		add_shortcode( 'limbenet_newsletter', array( $this, 'newsletter' ) );
		add_shortcode( 'limbenet_social_links', array( $this, 'social_links' ) );
		add_shortcode( 'limbenet_contact_page', array( $this, 'contact_page' ) );
		add_shortcode( 'limbenet_attraction_details', array( $this, 'attraction_details' ) );
		add_shortcode( 'limbenet_destination_details', array( $this, 'destination_details' ) );
		add_shortcode( 'limbenet_travel_info_details', array( $this, 'travel_info_details' ) );
		add_shortcode( 'limbenet_booking_form', array( $this, 'booking_form' ) );
		add_shortcode( 'limbenet_partner_form', array( $this, 'partner_form' ) );
		add_shortcode( 'limbenet_claim_form', array( $this, 'claim_form' ) );
		add_shortcode( 'limbenet_advertise_form', array( $this, 'advertise_form' ) );
	}

	/**
	 * Filter main archive/search queries from tourism filters.
	 *
	 * @param WP_Query $query Current query.
	 */
	public function filter_main_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$public_types = LimbeNet_Core_Post_Types::public_types();

		if ( $query->is_search() && ! empty( $_GET['lnet_post_types'] ) ) {
			$post_types = $this->sanitize_post_type_list( wp_unslash( $_GET['lnet_post_types'] ) );
			$post_types = array_values( array_intersect( $post_types, $public_types ) );
			if ( $post_types ) {
				$query->set( 'post_type', $post_types );
			}
		}

		if ( ! $query->is_post_type_archive( $public_types ) && ! $query->is_search() ) {
			return;
		}

		if ( ! empty( $_GET['lnet_query'] ) ) {
			$query->set( 's', sanitize_text_field( wp_unslash( $_GET['lnet_query'] ) ) );
		}

		$tax_query = $this->get_filter_tax_query();
		if ( $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}

		$meta_query = $this->get_filter_meta_query();
		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Render language switcher.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function language_switcher( $atts ) {
		$atts = shortcode_atts(
			array(
				'context' => 'header',
			),
			$atts,
			'limbenet_language_switcher'
		);

		$languages = $this->get_language_options();
		if ( empty( $languages ) ) {
			return '';
		}

		$current = null;
		foreach ( $languages as $language ) {
			if ( ! empty( $language['current'] ) ) {
				$current = $language;
				break;
			}
		}

		if ( ! $current ) {
			$current            = reset( $languages );
			$current['current'] = true;
		}

		$context_class = sanitize_html_class( 'is-' . $atts['context'] );
		$output        = '<nav class="lnet-language-switcher is-dropdown ' . esc_attr( $context_class ) . '" aria-label="' . esc_attr__( 'Language selector', 'limbenet-core' ) . '">';
		$output       .= '<details class="lnet-language-menu" data-lnet-language-switcher>';
		$output       .= '<summary class="lnet-language-current" aria-label="' . esc_attr__( 'Select language', 'limbenet-core' ) . '">';
		$output       .= $this->render_language_flag( $current );
		$output       .= '<span class="lnet-language-current-label">' . esc_html( strtoupper( $current['code'] ) ) . '</span>';
		$output       .= '<span class="lnet-language-arrow" aria-hidden="true"></span>';
		$output       .= '</summary>';
		$output       .= '<div class="lnet-language-options" role="menu">';

		foreach ( $languages as $language ) {
			$is_active = ! empty( $language['current'] ) || $language['code'] === $current['code'];
			$item      = $is_active ? 'span' : 'a';
			$class     = 'lnet-language-option' . ( $is_active ? ' is-active' : '' );
			$attrs     = ' class="' . esc_attr( $class ) . '" role="menuitem"';

			if ( $is_active ) {
				$attrs .= ' aria-current="true"';
			} else {
				$attrs .= ' href="' . esc_url( $language['url'] ) . '"';
			}

			$output .= '<' . $item . $attrs . '>';
			$output .= $this->render_language_flag( $language );
			$output .= '<span class="lnet-language-name">' . esc_html( $language['label'] ) . '</span>';
			$output .= '<span class="lnet-language-check" aria-hidden="true"></span>';
			$output .= '</' . $item . '>';
		}

		$output .= '</div></details></nav>';

		return $output;
	}

	/**
	 * Get language switcher options from Polylang or fallback languages.
	 *
	 * @return array
	 */
	private function get_language_options() {
		$url_language = $this->get_current_language_from_url();

		if ( function_exists( 'pll_the_languages' ) ) {
			$items = pll_the_languages(
				array(
					'dropdown'      => 0,
					'show_names'    => 1,
					'hide_if_empty' => 0,
					'raw'           => 1,
					'echo'          => 0,
					'force_home'    => 0,
					'hide_current'  => 0,
					'post_id'       => null,
				)
			);

			if ( is_array( $items ) && ! empty( $items ) ) {
				$languages = array();
				foreach ( $items as $item ) {
					$code = isset( $item['slug'] ) ? sanitize_key( $item['slug'] ) : '';
					if ( ! $code ) {
						continue;
					}

					$languages[] = array(
						'code'    => $code,
						'label'   => ! empty( $item['name'] ) ? wp_strip_all_tags( $item['name'] ) : strtoupper( $code ),
						'url'     => ! empty( $item['url'] ) ? $item['url'] : home_url( '/' . $code . '/' ),
						'flag'    => ! empty( $item['flag'] ) ? $item['flag'] : '',
						'current' => $url_language ? $code === $url_language : ! empty( $item['current_lang'] ),
					);
				}

				if ( ! empty( $languages ) ) {
					return $languages;
				}
			}
		}

		$current = $url_language ? $url_language : substr( get_locale(), 0, 2 );
		$items   = array(
			'en' => __( 'English', 'limbenet-core' ),
			'es' => __( 'Espanol', 'limbenet-core' ),
			'fr' => __( 'Francais', 'limbenet-core' ),
			'it' => __( 'Italiano', 'limbenet-core' ),
		);

		$languages = array();
		foreach ( $items as $code => $label ) {
			$languages[] = array(
				'code'    => $code,
				'label'   => $label,
				'url'     => apply_filters( 'wpml_permalink', home_url( '/' . $code . '/' ), $code ),
				'flag'    => '',
				'current' => $code === $current,
			);
		}

		return $languages;
	}

	/**
	 * Infer the active language from the current URL prefix.
	 *
	 * @return string
	 */
	private function get_current_language_from_url() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH );

		if ( ! is_string( $path ) ) {
			return '';
		}

		$path     = trim( $path, '/' );
		$segments = $path ? explode( '/', $path ) : array();
		$language = isset( $segments[0] ) ? sanitize_key( $segments[0] ) : '';

		return in_array( $language, array( 'en', 'es', 'fr', 'it' ), true ) ? $language : '';
	}

	/**
	 * Render a flag for a language option.
	 *
	 * @param array $language Language option.
	 * @return string
	 */
	private function render_language_flag( $language ) {
		$code = ! empty( $language['code'] ) ? sanitize_html_class( $language['code'] ) : 'en';

		if ( ! empty( $language['flag'] ) ) {
			if ( filter_var( $language['flag'], FILTER_VALIDATE_URL ) ) {
				return '<span class="lnet-lang-flag is-image is-' . esc_attr( $code ) . '" aria-hidden="true"><img src="' . esc_url( $language['flag'] ) . '" alt="" loading="lazy" decoding="async"></span>';
			}

			$allowed = array(
				'img' => array(
					'alt'      => true,
					'class'    => true,
					'height'   => true,
					'loading'  => true,
					'src'      => true,
					'srcset'   => true,
					'width'    => true,
					'decoding' => true,
				),
			);

			return '<span class="lnet-lang-flag is-image is-' . esc_attr( $code ) . '" aria-hidden="true">' . wp_kses( $language['flag'], $allowed ) . '</span>';
		}

		return '<span class="lnet-lang-flag is-' . esc_attr( $code ) . '" aria-hidden="true"></span>';
	}

	/**
	 * Render configurable social media links with placeholders.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function social_links( $atts ) {
		$atts = shortcode_atts(
			array(
				'context' => 'footer',
			),
			$atts,
			'limbenet_social_links'
		);

		$context = sanitize_key( $atts['context'] );
		if ( ! in_array( $context, array( 'footer', 'contact' ), true ) ) {
			$context = 'footer';
		}

		$settings = LimbeNet_Core_Settings::get_settings();
		$output   = '';
		$links    = array();

		foreach ( $this->social_platforms() as $platform ) {
			$url = isset( $settings[ $platform['setting'] ] ) ? LimbeNet_Core_Settings::sanitize_social_url( $settings[ $platform['setting'] ], $platform['setting'] ) : '';
			if ( ! $url ) {
				continue;
			}

			$platform['url'] = $url;
			$links[]         = $platform;
		}

		if ( ! $links ) {
			return '';
		}

		if ( 'contact' === $context ) {
			$output .= '<section class="lnet-social-section is-contact">';
			$output .= '<div class="lnet-social-heading"><p class="lnet-kicker">' . esc_html__( 'Stay connected', 'limbenet-core' ) . '</p>';
			$output .= '<h2>' . esc_html__( 'Connect with Limbe.Net', 'limbenet-core' ) . '</h2></div>';
		}

		$output .= '<nav class="lnet-social-links is-' . esc_attr( $context ) . '" aria-label="' . esc_attr__( 'Social media links', 'limbenet-core' ) . '">';

		foreach ( $links as $platform ) {
			$mark       = '<span class="lnet-social-mark" aria-hidden="true">' . esc_html( $platform['mark'] ) . '</span>';
			$class_name = 'lnet-social-link is-' . $platform['slug'];
			$output    .= '<a class="' . esc_attr( $class_name ) . '" href="' . esc_url( $platform['url'] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $platform['label'] ) . '">' . $mark . '<span class="screen-reader-text">' . esc_html( $platform['label'] ) . '</span></a>';
		}

		$output .= '</nav>';

		if ( 'contact' === $context ) {
			$output .= '</section>';
		}

		return $output;
	}

	/**
	 * Render Contact Us page.
	 *
	 * @return string
	 */
	public function contact_page() {
		$settings = LimbeNet_Core_Settings::get_settings();
		$email    = sanitize_email( $settings['default_contact_email'] );
		$phone    = trim( ! empty( $settings['contact_phone'] ) ? $settings['contact_phone'] : $settings['default_whatsapp'] );
		$address  = trim( LimbeNet_Core_Polylang_Integration::translate_string( $settings['contact_address'] ) );
		$hours    = trim( LimbeNet_Core_Polylang_Integration::translate_string( $settings['contact_business_hours'] ) );
		$map_url  = ! empty( $settings['contact_map_url'] ) ? esc_url( $settings['contact_map_url'] ) : '';

		$output  = '<section class="lnet-contact-page">';
		$output .= '<div class="lnet-contact-layout">';
		$output .= LimbeNet_Core_Forms::render_form( 'contact' );
		$output .= '<aside class="lnet-contact-side" aria-label="' . esc_attr__( 'Contact information', 'limbenet-core' ) . '">';
		$output .= '<section class="lnet-contact-card"><h3>' . esc_html__( 'Contact information', 'limbenet-core' ) . '</h3><div class="lnet-contact-info-grid">';

		if ( $phone ) {
			$phone_href = preg_replace( '/[^0-9+]/', '', $phone );
			$output    .= '<div class="lnet-contact-info-item"><span aria-hidden="true">P</span><div><strong>' . esc_html__( 'Phone', 'limbenet-core' ) . '</strong>';
			$output    .= $phone_href ? '<a href="' . esc_url( 'tel:' . $phone_href ) . '">' . esc_html( $phone ) . '</a>' : '<p>' . esc_html( $phone ) . '</p>';
			$output    .= '</div></div>';
		}

		if ( $email ) {
			$output .= '<div class="lnet-contact-info-item"><span aria-hidden="true">E</span><div><strong>' . esc_html__( 'Email', 'limbenet-core' ) . '</strong><a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a></div></div>';
		}

		if ( $address ) {
			$output .= '<div class="lnet-contact-info-item"><span aria-hidden="true">A</span><div><strong>' . esc_html__( 'Address', 'limbenet-core' ) . '</strong><p>' . nl2br( esc_html( $address ) ) . '</p></div></div>';
		}

		$output .= '</div></section>';

		if ( $hours ) {
			$output .= '<section class="lnet-contact-card"><h3>' . esc_html__( 'Business hours', 'limbenet-core' ) . '</h3><div class="lnet-contact-hours">';
			foreach ( preg_split( '/\r\n|\r|\n/', $hours ) as $line ) {
				$line = trim( $line );
				if ( $line ) {
					$output .= '<span>' . esc_html( $line ) . '</span>';
				}
			}
			$output .= '</div></section>';
		}

		$output .= $this->social_links( array( 'context' => 'contact' ) );
		$output .= '</aside></div>';

		if ( $map_url ) {
			$output .= '<div class="lnet-contact-map" data-lnet-cookie-embed="marketing">';
			$output .= '<div class="lnet-cookie-embed-placeholder" data-lnet-cookie-placeholder>';
			$output .= '<h3>' . esc_html__( 'Map preview', 'limbenet-core' ) . '</h3>';
			$output .= '<p>' . esc_html__( 'Accept marketing and embedded media cookies to load the interactive map.', 'limbenet-core' ) . '</p>';
			$output .= '<button class="lnet-cookie-policy-button" type="button" data-lnet-cookie-open>' . esc_html__( 'Manage cookie preferences', 'limbenet-core' ) . '</button>';
			$output .= '</div>';
			$output .= '<iframe data-src="' . esc_url( $map_url ) . '" title="' . esc_attr__( 'Limbe.Net map location', 'limbenet-core' ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" hidden></iframe>';
			$output .= '</div>';
		}

		$output .= '</section>';

		return $output;
	}

	/**
	 * Render tourism search form and optional results.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function tourism_search( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_type'    => 'attraction,destination,partner,itinerary',
				'compact'      => 'false',
				'placeholder'  => __( 'Where do you want to go?', 'limbenet-core' ),
				'button_label' => __( 'Search', 'limbenet-core' ),
			),
			$atts,
			'limbenet_tourism_search'
		);

		$post_types = $this->sanitize_post_type_list( $atts['post_type'] );
		$compact    = filter_var( $atts['compact'], FILTER_VALIDATE_BOOLEAN );
		$action     = $this->current_url_without_filters();
		$query      = $this->query_arg( 'lnet_query' );
		$output     = '<div class="lnet-search-panel' . ( $compact ? ' is-compact' : '' ) . '">';
		$output    .= '<form class="lnet-search-form" action="' . esc_url( $action ) . '" method="get" role="search">';
		$output    .= '<input type="hidden" name="lnet_post_types" value="' . esc_attr( implode( ',', $post_types ) ) . '">';
		$output    .= '<div class="lnet-search-primary">';
		$output    .= '<label class="screen-reader-text" for="lnet_query">' . esc_html( $atts['placeholder'] ) . '</label>';
		$output    .= '<input id="lnet_query" name="lnet_query" type="search" value="' . esc_attr( $query ) . '" placeholder="' . esc_attr( $atts['placeholder'] ) . '">';
		$output    .= '<button type="submit">' . esc_html( $atts['button_label'] ) . '</button>';
		$output    .= '</div>';

		if ( ! $compact ) {
			$family_friendly = $this->query_arg( 'lnet_family_friendly' );
			$ticket_required = $this->query_arg( 'lnet_ticket_required' );

			$output .= '<div class="lnet-filter-grid">';
			$output .= $this->taxonomy_select( 'region', 'lnet_region', __( 'Region', 'limbenet-core' ) );
			$output .= $this->taxonomy_select( 'city', 'lnet_city', __( 'City', 'limbenet-core' ) );
			$output .= $this->taxonomy_select( 'attraction_type', 'lnet_attraction_type', __( 'Attraction type', 'limbenet-core' ) );
			$output .= $this->taxonomy_select( 'travel_style', 'lnet_travel_style', __( 'Travel style', 'limbenet-core' ) );
			$output .= $this->taxonomy_select( 'budget_range', 'lnet_budget', __( 'Budget', 'limbenet-core' ) );
			$output .= $this->taxonomy_select( 'safety_status', 'lnet_safety_status', __( 'Safety status', 'limbenet-core' ) );
			$output .= '<label><span>' . esc_html__( 'Family friendly', 'limbenet-core' ) . '</span><select name="lnet_family_friendly">';
			$output .= '<option value="">' . esc_html__( 'Any', 'limbenet-core' ) . '</option>';
			$output .= '<option value="yes" ' . selected( $family_friendly, 'yes', false ) . '>' . esc_html__( 'Yes', 'limbenet-core' ) . '</option>';
			$output .= '<option value="no" ' . selected( $family_friendly, 'no', false ) . '>' . esc_html__( 'No', 'limbenet-core' ) . '</option>';
			$output .= '</select></label>';
			$output .= '<label><span>' . esc_html__( 'Ticket required', 'limbenet-core' ) . '</span><select name="lnet_ticket_required">';
			$output .= '<option value="">' . esc_html__( 'Any', 'limbenet-core' ) . '</option>';
			$output .= '<option value="yes" ' . selected( $ticket_required, 'yes', false ) . '>' . esc_html__( 'Yes', 'limbenet-core' ) . '</option>';
			$output .= '<option value="no" ' . selected( $ticket_required, 'no', false ) . '>' . esc_html__( 'No', 'limbenet-core' ) . '</option>';
			$output .= '<option value="unknown" ' . selected( $ticket_required, 'unknown', false ) . '>' . esc_html__( 'Unknown', 'limbenet-core' ) . '</option>';
			$output .= '</select></label></div>';
		}

		$output .= '</form></div>';

		if ( ! is_post_type_archive() && ! empty( $_GET['lnet_query'] ) ) {
			$output .= $this->render_search_results( $post_types );
		}

		return $this->compact_html( $output );
	}

	/**
	 * Render featured content grid.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function featured( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'  => 'attraction',
				'limit' => 6,
			),
			$atts,
			'limbenet_featured'
		);

		$type  = sanitize_key( $atts['type'] );
		$limit = max( 1, absint( $atts['limit'] ) );

		if ( ! in_array( $type, LimbeNet_Core_Post_Types::public_types(), true ) ) {
			return '';
		}

		$query = new WP_Query(
			array(
				'post_type'      => $type,
				'posts_per_page' => $limit,
				'no_found_rows'  => true,
				'meta_query'     => array(
					array(
						'key'   => 'featured',
						'value' => 'yes',
					),
				),
			)
		);

		if ( ! $query->have_posts() ) {
			$query = new WP_Query(
				array(
					'post_type'      => $type,
					'posts_per_page' => $limit,
					'no_found_rows'  => true,
				)
			);
		}

		return $this->render_query_grid( $query, __( 'No listings found yet. Import seed content or add listings in WordPress admin.', 'limbenet-core' ) );
	}

	/**
	 * Render travel styles.
	 *
	 * @return string
	 */
	public function travel_styles() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'travel_style',
				'hide_empty' => false,
				'number'     => 10,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			$terms = array(
				(object) array( 'name' => __( 'Beaches', 'limbenet-core' ), 'slug' => 'beaches' ),
				(object) array( 'name' => __( 'Wildlife & Safari', 'limbenet-core' ), 'slug' => 'wildlife-safari' ),
				(object) array( 'name' => __( 'Mountains & Hiking', 'limbenet-core' ), 'slug' => 'mountains-hiking' ),
				(object) array( 'name' => __( 'Culture & Heritage', 'limbenet-core' ), 'slug' => 'culture-heritage' ),
				(object) array( 'name' => __( 'Food & Nightlife', 'limbenet-core' ), 'slug' => 'food-nightlife' ),
				(object) array( 'name' => __( 'Eco-tourism', 'limbenet-core' ), 'slug' => 'eco-tourism' ),
			);
		}

		$output = '<div class="lnet-style-grid">';
		foreach ( $terms as $term ) {
			$url = add_query_arg( 'lnet_travel_style', $term->slug, get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) );
			$output .= '<a class="lnet-style-card" href="' . esc_url( $url ) . '"><span aria-hidden="true"></span><strong>' . esc_html( $term->name ) . '</strong></a>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render trip planning cards.
	 *
	 * @return string
	 */
	public function plan_trip() {
		$items = $this->travel_info_items();

		$output = '<div class="lnet-info-grid">';
		foreach ( $items as $item ) {
			$output .= '<a class="lnet-info-card" href="' . esc_url( $item['url'] ) . '"><strong>' . esc_html( $item['label'] ) . '</strong><span>' . esc_html__( 'Read planning notes', 'limbenet-core' ) . '</span></a>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render ticket help box.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function ticket_help( $atts ) {
		$atts = shortcode_atts(
			array(
				'expanded' => 'false',
			),
			$atts,
			'limbenet_ticket_help'
		);

		$output  = '<section class="lnet-ticket-help">';
		$output .= '<p class="lnet-kicker">' . esc_html__( 'Tickets & tours', 'limbenet-core' ) . '</p>';
		$output .= '<h2>' . esc_html__( 'Get ticket and tour help without invented prices', 'limbenet-core' ) . '</h2>';
		$output .= '<p>' . esc_html__( 'Limbe.Net supports online ticket links, onsite purchase notes, attraction contact details, verified partner booking links, and price verification notices.', 'limbenet-core' ) . '</p>';
		$output .= '<ul>';
		$output .= '<li>' . esc_html__( 'Tickets available online', 'limbenet-core' ) . '</li>';
		$output .= '<li>' . esc_html__( 'Buy onsite', 'limbenet-core' ) . '</li>';
		$output .= '<li>' . esc_html__( 'Contact attraction', 'limbenet-core' ) . '</li>';
		$output .= '<li>' . esc_html__( 'Book with verified partner', 'limbenet-core' ) . '</li>';
		$output .= '<li>' . esc_html__( 'Price not yet verified', 'limbenet-core' ) . '</li>';
		$output .= '</ul>';
		$output .= '<a class="lnet-button" href="' . esc_url( home_url( '/request-booking-help/' ) ) . '">' . esc_html__( 'Request booking help', 'limbenet-core' ) . '</a>';
		$output .= '</section>';

		if ( filter_var( $atts['expanded'], FILTER_VALIDATE_BOOLEAN ) ) {
			$output .= '<div class="lnet-disclosure">' . esc_html( LimbeNet_Core_Polylang_Integration::translate_string( LimbeNet_Core_Settings::get_settings()['affiliate_disclosure'] ) ) . '</div>';
		}

		return $output;
	}

	/**
	 * Render travel info hub.
	 *
	 * @return string
	 */
	public function travel_info() {
		$settings = LimbeNet_Core_Settings::get_settings();
		$output   = '<div class="lnet-hub">';
		$output  .= '<p class="lnet-disclosure">' . esc_html( LimbeNet_Core_Polylang_Integration::translate_string( $settings['safety_disclaimer'] ) ) . '</p>';
		$query    = $this->travel_info_query();

		if ( $query->have_posts() ) {
			$output .= $this->render_query_grid( $query, __( 'No travel info pages found yet. Import seed content or add travel info pages in WordPress admin.', 'limbenet-core' ) );
		} else {
			$output .= $this->plan_trip();
		}

		$output  .= '</div>';

		return $output;
	}

	/**
	 * Get travel info posts ordered for hub/widget display.
	 *
	 * @return WP_Query
	 */
	private function travel_info_query() {
		return new WP_Query(
			array(
				'post_type'      => 'travel_info',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * Get travel info links for compact widgets.
	 *
	 * @return array
	 */
	private function travel_info_items() {
		$query = $this->travel_info_query();
		$items = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$items[] = array(
				'label' => get_the_title(),
				'url'   => get_permalink(),
			);
		}
		wp_reset_postdata();

		if ( $items ) {
			return $items;
		}

		$labels = array(
			'visa-evisa'                => __( 'Visa & eVisa', 'limbenet-core' ),
			'airports'                  => __( 'Airports', 'limbenet-core' ),
			'getting-around'            => __( 'Getting around', 'limbenet-core' ),
			'money-payments'            => __( 'Money & payments', 'limbenet-core' ),
			'sim-cards-internet'        => __( 'SIM cards & internet', 'limbenet-core' ),
			'safety-travel-advisories'  => __( 'Safety & travel advisories', 'limbenet-core' ),
			'health-packing'            => __( 'Health & packing', 'limbenet-core' ),
			'best-time-to-visit'        => __( 'Best time to visit', 'limbenet-core' ),
			'responsible-travel'        => __( 'Responsible travel', 'limbenet-core' ),
		);

		foreach ( $labels as $slug => $label ) {
			$items[] = array(
				'label' => $label,
				'url'   => home_url( '/travel-info/' . $slug . '/' ),
			);
		}

		return $items;
	}

	/**
	 * Render partner CTA.
	 *
	 * @return string
	 */
	public function partner_cta() {
		$settings = LimbeNet_Core_Settings::get_settings();
		if ( '1' !== $settings['enable_partner_ctas'] ) {
			return '';
		}

		$output  = '<div class="lnet-partner-cta">';
		$output .= '<div><p class="lnet-kicker">' . esc_html__( 'Partner with Limbe.Net', 'limbenet-core' ) . '</p>';
		$output .= '<h2>' . esc_html__( 'List your hotel, restaurant, tour, or transport service', 'limbenet-core' ) . '</h2>';
		$output .= '<p>' . esc_html__( 'Partner plans are monetization-ready: Free, Verified, Featured, and Premium listings with clear sponsored labels where required.', 'limbenet-core' ) . '</p></div>';
		$output .= '<div class="lnet-plan-stack">';
		foreach ( array( __( 'Free', 'limbenet-core' ), __( 'Verified', 'limbenet-core' ), __( 'Featured', 'limbenet-core' ), __( 'Premium', 'limbenet-core' ) ) as $plan ) {
			$output .= '<span>' . esc_html( $plan ) . '</span>';
		}
		$output .= '</div>';
		$output .= '<a class="lnet-button" href="' . esc_url( home_url( '/partner-with-us/' ) ) . '">' . esc_html__( 'Submit a business listing', 'limbenet-core' ) . '</a>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render newsletter signup UI.
	 *
	 * @return string
	 */
	public function newsletter() {
		$output  = '<section class="lnet-newsletter">';
		$output .= '<div><p class="lnet-kicker">' . esc_html__( 'Travel updates', 'limbenet-core' ) . '</p><h2>' . esc_html__( 'Get new Cameroon travel guides and partner deals', 'limbenet-core' ) . '</h2></div>';
		$output .= '<form class="lnet-newsletter-form" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
		$output .= '<label class="screen-reader-text" for="lnet_newsletter_email">' . esc_html__( 'Email address', 'limbenet-core' ) . '</label>';
		$output .= '<input id="lnet_newsletter_email" type="email" name="newsletter_email" placeholder="' . esc_attr__( 'Email address', 'limbenet-core' ) . '">';
		$output .= '<button type="submit">' . esc_html__( 'Sign up', 'limbenet-core' ) . '</button>';
		$output .= '</form></section>';

		return $output;
	}

	/**
	 * Render attraction detail layout.
	 *
	 * @return string
	 */
	public function attraction_details() {
		$post_id = get_the_ID();
		if ( ! $post_id || 'attraction' !== get_post_type( $post_id ) ) {
			return '';
		}

		$meta  = $this->post_meta_map( $post_id );
		$city  = $this->meta_or_term( $post_id, 'city', 'city' );
		$region = $this->meta_or_term( $post_id, 'region', 'region' );

		ob_start();
		?>
		<div class="lnet-detail-layout">
			<aside class="lnet-quick-facts">
				<h2><?php esc_html_e( 'Quick facts', 'limbenet-core' ); ?></h2>
				<?php $this->fact( __( 'City', 'limbenet-core' ), $city ); ?>
				<?php $this->fact( __( 'Region', 'limbenet-core' ), $region ); ?>
				<?php $this->fact( __( 'Type', 'limbenet-core' ), $meta['attraction_type'] ); ?>
				<?php $this->fact( __( 'Recommended duration', 'limbenet-core' ), $meta['recommended_duration'] ); ?>
				<?php $this->fact( __( 'Family friendly', 'limbenet-core' ), $this->yes_no_label( $meta['family_friendly'] ) ); ?>
				<?php $this->fact( __( 'Last verified', 'limbenet-core' ), $meta['last_verified_date'] ?: __( 'Needs verification', 'limbenet-core' ) ); ?>
			</aside>

			<div class="lnet-detail-main">
				<?php echo $this->safety_notice( $meta['advisory_level'], $meta['safety_notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'Why visit', 'limbenet-core' ); ?></h2>
					<?php echo $this->paragraphs( $meta['full_description'] ?: $meta['short_description'] ?: get_the_excerpt( $post_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>

				<?php echo $this->ticket_card( $meta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'How to get there', 'limbenet-core' ); ?></h2>
					<?php echo $this->paragraphs( $meta['how_to_get_there'] ?: __( 'Local transport details need verification. Contact the attraction or a verified partner before planning.', 'limbenet-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'Best time to visit', 'limbenet-core' ); ?></h2>
					<?php echo $this->paragraphs( $meta['best_time_to_visit'] ?: __( 'Best-time guidance has not yet been verified for this listing.', 'limbenet-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>

				<?php echo $this->map_placeholder( $meta['latitude'], $meta['longitude'], get_the_title( $post_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $this->gallery( $meta['gallery_images'], get_the_title( $post_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'Nearby planning', 'limbenet-core' ); ?></h2>
					<div class="lnet-nearby-grid">
						<?php echo $this->list_card( __( 'Nearby hotels', 'limbenet-core' ), $meta['nearby_hotels'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo $this->list_card( __( 'Nearby restaurants', 'limbenet-core' ), $meta['nearby_restaurants'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo $this->list_card( __( 'Nearby attractions', 'limbenet-core' ), $meta['nearby_attractions'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</section>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'FAQ', 'limbenet-core' ); ?></h2>
					<details>
						<summary><?php esc_html_e( 'Do I need a ticket?', 'limbenet-core' ); ?></summary>
						<p><?php echo esc_html( $this->ticket_required_text( $meta['ticket_required'] ) ); ?></p>
					</details>
					<details>
						<summary><?php esc_html_e( 'How current is this information?', 'limbenet-core' ); ?></summary>
						<p><?php echo esc_html( $meta['last_verified_date'] ? sprintf( __( 'Last verified: %s', 'limbenet-core' ), $meta['last_verified_date'] ) : __( 'This listing needs verification before travel planning.', 'limbenet-core' ) ); ?></p>
					</details>
				</section>

				<section class="lnet-cta-row" aria-label="<?php esc_attr_e( 'Attraction calls to action', 'limbenet-core' ); ?>">
					<a class="lnet-button" href="<?php echo esc_url( home_url( '/request-booking-help/' ) ); ?>"><?php esc_html_e( 'Request booking help', 'limbenet-core' ); ?></a>
					<a class="lnet-button-outline-dark" href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php esc_html_e( 'List your business near this attraction', 'limbenet-core' ); ?></a>
				</section>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render destination detail layout.
	 *
	 * @return string
	 */
	public function destination_details() {
		$post_id = get_the_ID();
		if ( ! $post_id || 'destination' !== get_post_type( $post_id ) ) {
			return '';
		}

		$meta = $this->post_meta_map( $post_id );

		ob_start();
		?>
		<div class="lnet-detail-layout">
			<aside class="lnet-quick-facts">
				<h2><?php esc_html_e( 'Destination facts', 'limbenet-core' ); ?></h2>
				<?php $this->fact( __( 'Region', 'limbenet-core' ), $meta['region'] ); ?>
				<?php $this->fact( __( 'Best for', 'limbenet-core' ), $meta['best_for'] ); ?>
				<?php $this->fact( __( 'From Douala', 'limbenet-core' ), $meta['travel_time_from_douala'] ); ?>
				<?php $this->fact( __( 'From Yaounde', 'limbenet-core' ), $meta['travel_time_from_yaounde'] ); ?>
				<?php $this->fact( __( 'Last verified', 'limbenet-core' ), $meta['last_verified_date'] ?: __( 'Needs verification', 'limbenet-core' ) ); ?>
			</aside>
			<div class="lnet-detail-main">
				<?php echo $this->safety_notice( $meta['advisory_level'], $meta['safety_notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<section class="lnet-detail-section"><h2><?php esc_html_e( 'Overview', 'limbenet-core' ); ?></h2><?php echo $this->paragraphs( $meta['overview'] ?: get_the_excerpt( $post_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section>
				<section class="lnet-detail-section"><h2><?php esc_html_e( 'Top attractions', 'limbenet-core' ); ?></h2><?php echo $this->list_from_text( $meta['top_attractions'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section>
				<section class="lnet-detail-section"><h2><?php esc_html_e( 'Where to stay', 'limbenet-core' ); ?></h2><?php echo $this->paragraphs( $meta['where_to_stay'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section>
				<section class="lnet-detail-section"><h2><?php esc_html_e( 'How to get there', 'limbenet-core' ); ?></h2><?php echo $this->paragraphs( $meta['how_to_get_there'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section>
				<?php echo $this->map_placeholder_from_string( $meta['map_coordinates'], get_the_title( $post_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render travel info detail layout.
	 *
	 * @return string
	 */
	public function travel_info_details() {
		$post_id = get_the_ID();
		if ( ! $post_id || 'travel_info' !== get_post_type( $post_id ) ) {
			return '';
		}

		$meta = $this->post_meta_map( $post_id );

		ob_start();
		?>
		<div class="lnet-detail-layout is-travel-info">
			<aside class="lnet-quick-facts">
				<h2><?php esc_html_e( 'Planning facts', 'limbenet-core' ); ?></h2>
				<?php $this->fact( __( 'Topic', 'limbenet-core' ), get_the_title( $post_id ) ); ?>
				<?php $this->fact( __( 'Focus', 'limbenet-core' ), $meta['travel_info_subtitle'] ); ?>
				<?php $this->fact( __( 'Last verified', 'limbenet-core' ), $meta['last_verified_date'] ?: __( 'Needs verification', 'limbenet-core' ) ); ?>
			</aside>

			<div class="lnet-detail-main">
				<?php echo $this->travel_info_hero( $post_id, $meta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $this->safety_notice( $meta['advisory_level'], $meta['safety_notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'Key planning notes', 'limbenet-core' ); ?></h2>
					<?php echo $this->list_from_text( $meta['key_points'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>

				<section class="lnet-detail-section">
					<h2><?php esc_html_e( 'Detailed guidance', 'limbenet-core' ); ?></h2>
					<?php echo $this->paragraphs( $meta['details'] ?: $meta['summary'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>

				<?php echo $this->source_links( $meta['official_links'], $meta['source_notes'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render booking form.
	 *
	 * @return string
	 */
	public function booking_form() {
		return LimbeNet_Core_Forms::render_form( 'booking_help' );
	}

	/**
	 * Render partner form.
	 *
	 * @return string
	 */
	public function partner_form() {
		return LimbeNet_Core_Forms::render_form( 'submit_business' );
	}

	/**
	 * Render claim form.
	 *
	 * @return string
	 */
	public function claim_form() {
		return LimbeNet_Core_Forms::render_form( 'claim_listing' );
	}

	/**
	 * Render advertise form.
	 *
	 * @return string
	 */
	public function advertise_form() {
		return LimbeNet_Core_Forms::render_form( 'advertise' );
	}

	/**
	 * Render a query grid.
	 *
	 * @param WP_Query $query Query.
	 * @param string   $empty Empty message.
	 * @return string
	 */
	private function render_query_grid( $query, $empty ) {
		if ( ! $query->have_posts() ) {
			return '<p class="lnet-empty">' . esc_html( $empty ) . '</p>';
		}

		$output = '<div class="lnet-card-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$output .= $this->render_card( get_the_ID() );
		}
		wp_reset_postdata();
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render search results under standalone search form.
	 *
	 * @param array $post_types Post types.
	 * @return string
	 */
	private function render_search_results( $post_types ) {
		$args = array(
			'post_type'      => $post_types,
			'posts_per_page' => 12,
			's'              => $this->query_arg( 'lnet_query' ),
			'tax_query'      => $this->get_filter_tax_query(),
			'meta_query'     => $this->get_filter_meta_query(),
		);

		$query = new WP_Query( $args );
		return '<div class="lnet-search-results">' . $this->render_query_grid( $query, __( 'No tourism results found. Try a broader destination, style, or safety filter.', 'limbenet-core' ) ) . '</div>';
	}

	/**
	 * Remove shortcode template whitespace that WordPress can convert into line breaks.
	 *
	 * @param string $html Markup.
	 * @return string
	 */
	private function compact_html( $html ) {
		$html = preg_replace( '/<br\s*\/?>/i', '', $html );
		$html = preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	/**
	 * Social platform metadata used by the social links shortcode.
	 *
	 * @return array
	 */
	private function social_platforms() {
		return array(
			array(
				'slug'    => 'facebook',
				'setting' => 'social_facebook_url',
				'label'   => __( 'Facebook', 'limbenet-core' ),
				'mark'    => 'f',
			),
			array(
				'slug'    => 'instagram',
				'setting' => 'social_instagram_url',
				'label'   => __( 'Instagram', 'limbenet-core' ),
				'mark'    => 'IG',
			),
			array(
				'slug'    => 'x',
				'setting' => 'social_x_url',
				'label'   => __( 'X', 'limbenet-core' ),
				'mark'    => 'X',
			),
			array(
				'slug'    => 'tiktok',
				'setting' => 'social_tiktok_url',
				'label'   => __( 'TikTok', 'limbenet-core' ),
				'mark'    => 'TT',
			),
			array(
				'slug'    => 'youtube',
				'setting' => 'social_youtube_url',
				'label'   => __( 'YouTube', 'limbenet-core' ),
				'mark'    => 'YT',
			),
			array(
				'slug'    => 'linkedin',
				'setting' => 'social_linkedin_url',
				'label'   => __( 'LinkedIn', 'limbenet-core' ),
				'mark'    => 'in',
			),
			array(
				'slug'    => 'whatsapp',
				'setting' => 'social_whatsapp_url',
				'label'   => __( 'WhatsApp', 'limbenet-core' ),
				'mark'    => 'WA',
			),
		);
	}

	/**
	 * Render a card.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function render_card( $post_id ) {
		$type      = get_post_type( $post_id );
		$type_obj  = get_post_type_object( $type );
		$label     = $type_obj ? $type_obj->labels->singular_name : __( 'Listing', 'limbenet-core' );
		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$excerpt   = $this->card_excerpt( $post_id );
		$city      = $this->meta_or_term( $post_id, 'city', 'city' );
		$region    = $this->meta_or_term( $post_id, 'region', 'region' );

		$output  = '<article class="lnet-card lnet-card-' . esc_attr( $type ) . '">';
		$output .= '<a class="lnet-card-image" href="' . esc_url( $permalink ) . '">' . $this->card_image( $post_id, $title ) . '</a>';
		$output .= '<div class="lnet-card-body">';
		$output .= '<div class="lnet-card-eyebrow"><span>' . esc_html( $label ) . '</span>';
		if ( $city || $region ) {
			$output .= '<span>' . esc_html( trim( $city . ( $city && $region ? ', ' : '' ) . $region ) ) . '</span>';
		}
		$output .= '</div>';
		$output .= '<h3><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h3>';
		if ( $excerpt ) {
			$output .= '<p>' . esc_html( $excerpt ) . '</p>';
		}
		$output .= $this->card_badges( $post_id );
		$output .= '</div></article>';

		return $output;
	}

	/**
	 * Render card image.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $title Title.
	 * @return string
	 */
	private function card_image( $post_id, $title ) {
		$type = get_post_type( $post_id );

		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail(
				$post_id,
				'limbenet-card',
				array(
					'loading' => 'lazy',
					'alt'     => $title,
				)
			);
		}

		$url = get_post_meta( $post_id, 'hero_image', true );
		if ( ! $url ) {
			$url = get_post_meta( $post_id, 'featured_image', true );
		}
		if ( ! $url ) {
			$url = get_post_meta( $post_id, 'logo', true );
		}

		if ( $url ) {
			return '<img loading="lazy" src="' . esc_url( $url ) . '" alt="' . esc_attr( $title ) . '">';
		}

		$default_image = $this->default_card_image( $type, $title );
		if ( $default_image ) {
			return $default_image;
		}

		return '<span class="lnet-card-placeholder" aria-hidden="true"></span>';
	}

	/**
	 * Render a default card thumbnail for featured homepage widgets.
	 *
	 * @param string $type Post type.
	 * @param string $title Card title.
	 * @return string
	 */
	private function default_card_image( $type, $title ) {
		$filename = $this->default_card_image_filename( $type, $title );
		if ( ! $filename ) {
			return '';
		}

		$locations = array(
			array(
				'path' => trailingslashit( get_stylesheet_directory() ) . 'assets/images/' . $filename,
				'url'  => trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/' . $filename,
			),
			array(
				'path' => trailingslashit( get_template_directory() ) . 'assets/images/' . $filename,
				'url'  => trailingslashit( get_template_directory_uri() ) . 'assets/images/' . $filename,
			),
			array(
				'path' => LIMBENET_CORE_PATH . 'assets/images/' . $filename,
				'url'  => LIMBENET_CORE_URL . 'assets/images/' . $filename,
			),
		);

		foreach ( $locations as $location ) {
			if ( file_exists( $location['path'] ) ) {
				return '<img loading="lazy" src="' . esc_url( $location['url'] ) . '" alt="' . esc_attr( $title ) . '">';
			}
		}

		return '';
	}

	/**
	 * Get the default card thumbnail filename for a post type and title.
	 *
	 * @param string $type Post type.
	 * @param string $title Card title.
	 * @return string
	 */
	private function default_card_image_filename( $type, $title ) {
		$slug = sanitize_title( $title );

		if ( 'destination' === $type && in_array( $slug, array( 'limbe', 'limbe-city' ), true ) ) {
			return 'limbe-city-featured.webp';
		}

		$images = array(
			'destination' => 'home-featured-destinations.webp',
			'attraction'  => 'home-popular-attractions.webp',
			'travel_info' => 'travel-info-default.webp',
			'itinerary'   => 'home-latest-travel-guides.webp',
			'deal'        => 'home-featured-deals.webp',
		);

		return isset( $images[ $type ] ) ? $images[ $type ] : '';
	}

	/**
	 * Get card excerpt.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function card_excerpt( $post_id ) {
		foreach ( array( 'short_description', 'overview', 'summary', 'description', 'discount_text', 'best_for' ) as $key ) {
			$value = get_post_meta( $post_id, $key, true );
			if ( $value ) {
				return wp_trim_words( $value, 24 );
			}
		}

		return wp_trim_words( get_the_excerpt( $post_id ), 24 );
	}

	/**
	 * Render badges for card.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function card_badges( $post_id ) {
		$badges = array();

		if ( 'yes' === get_post_meta( $post_id, 'verified_partner', true ) ) {
			$badges[] = __( 'Verified partner', 'limbenet-core' );
		}

		if ( 'yes' === get_post_meta( $post_id, 'featured', true ) ) {
			$badges[] = __( 'Featured', 'limbenet-core' );
		}

		if ( 'yes' === get_post_meta( $post_id, 'sponsored_content', true ) ) {
			$badges[] = __( 'Sponsored', 'limbenet-core' );
		}

		$advisory = get_post_meta( $post_id, 'advisory_level', true );
		if ( $advisory ) {
			$badges[] = $this->advisory_label( $advisory );
		}

		if ( ! $badges ) {
			return '';
		}

		$output = '<div class="lnet-badge-row">';
		foreach ( $badges as $badge ) {
			$output .= '<span>' . esc_html( $badge ) . '</span>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render taxonomy select.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $name Field name.
	 * @param string $label Label.
	 * @return string
	 */
	private function taxonomy_select( $taxonomy, $name, $label ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		$current = $this->query_arg( $name );
		$output  = '<label><span>' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '">';
		$output .= '<option value="">' . esc_html__( 'Any', 'limbenet-core' ) . '</option>';

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$output .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $current, $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
			}
		}

		$output .= '</select></label>';

		return $output;
	}

	/**
	 * Build tax query from GET filters.
	 *
	 * @return array
	 */
	private function get_filter_tax_query() {
		$map = array(
			'lnet_region'          => 'region',
			'lnet_city'            => 'city',
			'lnet_attraction_type' => 'attraction_type',
			'lnet_travel_style'    => 'travel_style',
			'lnet_budget'          => 'budget_range',
			'lnet_safety_status'   => 'safety_status',
		);

		$tax_query = array();
		foreach ( $map as $query_key => $taxonomy ) {
			$value = $this->query_arg( $query_key );
			if ( $value ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $value,
				);
			}
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		return $tax_query;
	}

	/**
	 * Build meta query from GET filters.
	 *
	 * @return array
	 */
	private function get_filter_meta_query() {
		$meta_query = array();

		foreach ( array( 'lnet_family_friendly' => 'family_friendly', 'lnet_ticket_required' => 'ticket_required' ) as $query_key => $meta_key ) {
			$value = $this->query_arg( $query_key );
			if ( $value ) {
				$meta_query[] = array(
					'key'   => $meta_key,
					'value' => $value,
				);
			}
		}

		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		return $meta_query;
	}

	/**
	 * Get sanitized query arg.
	 *
	 * @param string $key Query key.
	 * @return string
	 */
	private function query_arg( $key ) {
		if ( empty( $_GET[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
	}

	/**
	 * Get current URL without search filter args.
	 *
	 * @return string
	 */
	private function current_url_without_filters() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$path        = strtok( $request_uri, '?' );

		return home_url( $path ? $path : '/' );
	}

	/**
	 * Sanitize a comma-separated post type list.
	 *
	 * @param string $value Raw value.
	 * @return array
	 */
	private function sanitize_post_type_list( $value ) {
		$items = is_array( $value ) ? $value : explode( ',', (string) $value );
		$items = array_map( 'sanitize_key', $items );
		$items = array_filter( $items );

		return array_values( array_unique( $items ) );
	}

	/**
	 * Build post meta map.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	private function post_meta_map( $post_id ) {
		$fields = LimbeNet_Core_Meta_Boxes::fields();
		$type   = get_post_type( $post_id );
		$map    = array();

		if ( empty( $fields[ $type ] ) ) {
			return $map;
		}

		foreach ( $fields[ $type ] as $key => $field ) {
			$map[ $key ] = get_post_meta( $post_id, $key, true );
		}

		return $map;
	}

	/**
	 * Get meta or taxonomy value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Meta key.
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	private function meta_or_term( $post_id, $meta_key, $taxonomy ) {
		$meta = get_post_meta( $post_id, $meta_key, true );
		if ( $meta ) {
			return $meta;
		}

		$terms = get_the_terms( $post_id, $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		return $terms[0]->name;
	}

	/**
	 * Print quick fact row.
	 *
	 * @param string $label Label.
	 * @param string $value Value.
	 */
	private function fact( $label, $value ) {
		if ( '' === trim( (string) $value ) ) {
			$value = __( 'Needs verification', 'limbenet-core' );
		}

		echo '<div class="lnet-fact"><span>' . esc_html( $label ) . '</span><strong>' . esc_html( $value ) . '</strong></div>';
	}

	/**
	 * Render safety notice.
	 *
	 * @param string $level Advisory level.
	 * @param string $notice Notice.
	 * @return string
	 */
	private function safety_notice( $level, $notice ) {
		$level  = $level ?: 'check-before-travel';
		$notice = $notice ?: __( 'Check current travel advisory before planning this trip.', 'limbenet-core' );

		return '<section class="lnet-safety-box is-' . esc_attr( $level ) . '"><h2>' . esc_html( $this->advisory_label( $level ) ) . '</h2><p>' . esc_html( $notice ) . '</p></section>';
	}

	/**
	 * Render ticket card.
	 *
	 * @param array $meta Meta map.
	 * @return string
	 */
	private function ticket_card( $meta ) {
		$price = ! empty( $meta['ticket_price_range'] ) ? $meta['ticket_price_range'] : __( 'Price not yet verified.', 'limbenet-core' );

		$output  = '<section class="lnet-ticket-card">';
		$output .= '<h2>' . esc_html__( 'Ticket information', 'limbenet-core' ) . '</h2>';
		$output .= '<div class="lnet-ticket-grid">';
		$output .= '<div><span>' . esc_html__( 'Ticket status', 'limbenet-core' ) . '</span><strong>' . esc_html( $this->ticket_required_text( isset( $meta['ticket_required'] ) ? $meta['ticket_required'] : '' ) ) . '</strong></div>';
		$output .= '<div><span>' . esc_html__( 'Price', 'limbenet-core' ) . '</span><strong>' . esc_html( $price ) . '</strong></div>';
		$output .= '<div><span>' . esc_html__( 'Last verified', 'limbenet-core' ) . '</span><strong>' . esc_html( ! empty( $meta['last_verified_date'] ) ? $meta['last_verified_date'] : __( 'Needs verification', 'limbenet-core' ) ) . '</strong></div>';
		$output .= '</div><div class="lnet-ticket-actions">';

		if ( ! empty( $meta['official_ticket_url'] ) ) {
			$output .= '<a class="lnet-button" href="' . esc_url( $meta['official_ticket_url'] ) . '">' . esc_html__( 'Tickets available online', 'limbenet-core' ) . '</a>';
		}

		if ( ! empty( $meta['partner_booking_url'] ) ) {
			$output .= '<a class="lnet-button-outline-dark" href="' . esc_url( $meta['partner_booking_url'] ) . '">' . esc_html__( 'Book with verified partner', 'limbenet-core' ) . '</a>';
		}

		if ( ! empty( $meta['booking_whatsapp'] ) ) {
			$output .= '<a class="lnet-button-outline-dark" href="' . esc_url( 'https://wa.me/' . preg_replace( '/\D+/', '', $meta['booking_whatsapp'] ) ) . '">' . esc_html__( 'Contact attraction', 'limbenet-core' ) . '</a>';
		}

		$output .= '<span>' . esc_html__( 'Buy onsite information should be verified before travel.', 'limbenet-core' ) . '</span>';
		$output .= '</div></section>';

		return $output;
	}

	/**
	 * Render a map placeholder.
	 *
	 * @param string $latitude Latitude.
	 * @param string $longitude Longitude.
	 * @param string $title Title.
	 * @return string
	 */
	private function map_placeholder( $latitude, $longitude, $title ) {
		$output  = '<section class="lnet-map-box"><h2>' . esc_html__( 'Map', 'limbenet-core' ) . '</h2>';
		if ( $latitude && $longitude ) {
			$query  = $latitude . ',' . $longitude;
			$output .= '<p>' . esc_html( $query ) . '</p>';
			$output .= '<a href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query ) ) . '">' . esc_html__( 'Open map', 'limbenet-core' ) . '</a>';
		} else {
			$output .= '<p>' . esc_html__( 'Map coordinates pending verification.', 'limbenet-core' ) . '</p>';
		}
		$output .= '</section>';

		return $output;
	}

	/**
	 * Render map from string coordinates.
	 *
	 * @param string $coordinates Coordinates.
	 * @param string $title Title.
	 * @return string
	 */
	private function map_placeholder_from_string( $coordinates, $title ) {
		if ( false !== strpos( $coordinates, ',' ) ) {
			$parts = array_map( 'trim', explode( ',', $coordinates, 2 ) );
			return $this->map_placeholder( $parts[0], $parts[1], $title );
		}

		return $this->map_placeholder( '', '', $title );
	}

	/**
	 * Render gallery.
	 *
	 * @param string $images Comma-separated image IDs/URLs.
	 * @param string $title Title.
	 * @return string
	 */
	private function gallery( $images, $title ) {
		if ( ! $images ) {
			return '';
		}

		$items = array_filter( array_map( 'trim', explode( ',', $images ) ) );
		if ( ! $items ) {
			return '';
		}

		$output = '<section class="lnet-gallery"><h2>' . esc_html__( 'Gallery', 'limbenet-core' ) . '</h2><div>';
		foreach ( $items as $item ) {
			if ( is_numeric( $item ) ) {
				$output .= wp_get_attachment_image( absint( $item ), 'medium_large', false, array( 'loading' => 'lazy' ) );
			} else {
				$output .= '<img loading="lazy" src="' . esc_url( $item ) . '" alt="' . esc_attr( $title ) . '">';
			}
		}
		$output .= '</div></section>';

		return $output;
	}

	/**
	 * Render travel info hero image from meta when no WP featured image exists.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $meta Meta map.
	 * @return string
	 */
	private function travel_info_hero( $post_id, $meta ) {
		if ( has_post_thumbnail( $post_id ) || empty( $meta['featured_image'] ) ) {
			return '';
		}

		return '<figure class="lnet-detail-hero"><img loading="eager" src="' . esc_url( $meta['featured_image'] ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '"></figure>';
	}

	/**
	 * Render official source links.
	 *
	 * @param string $links Source links, one per line.
	 * @param string $notes Source notes.
	 * @return string
	 */
	private function source_links( $links, $notes ) {
		$items = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $links ) ) );
		if ( ! $items && ! $notes ) {
			return '';
		}

		$output = '<section class="lnet-detail-section lnet-source-section"><h2>' . esc_html__( 'Sources', 'limbenet-core' ) . '</h2>';

		if ( $items ) {
			$output .= '<ul>';
			foreach ( $items as $item ) {
				$parts = array_map( 'trim', explode( '|', $item, 2 ) );
				$label = $parts[0];
				$url   = isset( $parts[1] ) ? $parts[1] : $parts[0];
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					$output .= '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a></li>';
				} else {
					$output .= '<li>' . esc_html( $item ) . '</li>';
				}
			}
			$output .= '</ul>';
		}

		if ( $notes ) {
			$output .= '<p>' . esc_html( $notes ) . '</p>';
		}

		$output .= '</section>';

		return $output;
	}

	/**
	 * Render a list card.
	 *
	 * @param string $title Title.
	 * @param string $text Text.
	 * @return string
	 */
	private function list_card( $title, $text ) {
		return '<div class="lnet-list-card"><h3>' . esc_html( $title ) . '</h3>' . $this->list_from_text( $text ) . '</div>';
	}

	/**
	 * Render list from line text.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	private function list_from_text( $text ) {
		$items = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) );
		if ( ! $items ) {
			return '<p>' . esc_html__( 'Needs verification.', 'limbenet-core' ) . '</p>';
		}

		$output = '<ul>';
		foreach ( $items as $item ) {
			$output .= '<li>' . esc_html( $item ) . '</li>';
		}
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Convert text to escaped paragraphs.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	private function paragraphs( $text ) {
		$text = trim( (string) $text );
		if ( '' === $text ) {
			return '<p>' . esc_html__( 'Needs verification.', 'limbenet-core' ) . '</p>';
		}

		return wpautop( esc_html( $text ) );
	}

	/**
	 * Get advisory label.
	 *
	 * @param string $level Advisory level.
	 * @return string
	 */
	private function advisory_label( $level ) {
		$labels = array(
			'normal'              => __( 'Normal travel planning', 'limbenet-core' ),
			'check-before-travel' => __( 'Check current advisory before travel', 'limbenet-core' ),
			'high-risk'           => __( 'High-risk area: travel only with expert local guidance', 'limbenet-core' ),
		);

		return isset( $labels[ $level ] ) ? $labels[ $level ] : $labels['check-before-travel'];
	}

	/**
	 * Ticket status text.
	 *
	 * @param string $value Stored value.
	 * @return string
	 */
	private function ticket_required_text( $value ) {
		if ( 'yes' === $value ) {
			return __( 'Ticket required. Check official or partner booking details before travel.', 'limbenet-core' );
		}

		if ( 'no' === $value ) {
			return __( 'No ticket currently marked as required. Verify before travel.', 'limbenet-core' );
		}

		return __( 'Ticket requirement not yet verified.', 'limbenet-core' );
	}

	/**
	 * Yes/no label.
	 *
	 * @param string $value Stored value.
	 * @return string
	 */
	private function yes_no_label( $value ) {
		if ( 'yes' === $value ) {
			return __( 'Yes', 'limbenet-core' );
		}

		if ( 'no' === $value ) {
			return __( 'No', 'limbenet-core' );
		}

		return __( 'Needs verification', 'limbenet-core' );
	}
}
