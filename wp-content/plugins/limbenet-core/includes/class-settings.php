<?php
/**
 * Settings and admin menu.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings.
 */
class LimbeNet_Core_Settings {
	/**
	 * Option key.
	 */
	const OPTION = 'limbenet_settings';

	/**
	 * Register admin menu.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Limbe.Net Tourism', 'limbenet-core' ),
			__( 'Limbe.Net Tourism', 'limbenet-core' ),
			'edit_posts',
			'limbenet-tourism',
			array( $this, 'render_dashboard' ),
			'dashicons-location-alt',
			26
		);

		add_submenu_page(
			'limbenet-tourism',
			__( 'Settings', 'limbenet-core' ),
			__( 'Settings', 'limbenet-core' ),
			'manage_options',
			'limbenet-settings',
			array( $this, 'render_settings' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'limbenet_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array_merge(
			array(
				'default_whatsapp'      => '',
				'default_contact_email' => get_option( 'admin_email' ),
				'google_maps_api_key'   => '',
				'enable_partner_ctas'   => '1',
				'currency'              => 'XAF',
				'affiliate_disclosure'  => __( 'Some links may be partner or affiliate links. Limbe.Net may earn a commission at no extra cost to you.', 'limbenet-core' ),
				'safety_disclaimer'     => __( 'Travel conditions can change. Check official advisories and local guidance before planning or booking.', 'limbenet-core' ),
			),
			array_fill_keys( array_keys( self::social_fields() ), '' )
		);
	}

	/**
	 * Social media settings fields.
	 *
	 * @return array
	 */
	public static function social_fields() {
		return array(
			'social_facebook_url'  => __( 'Facebook URL', 'limbenet-core' ),
			'social_instagram_url' => __( 'Instagram URL', 'limbenet-core' ),
			'social_x_url'         => __( 'X URL', 'limbenet-core' ),
			'social_tiktok_url'    => __( 'TikTok URL', 'limbenet-core' ),
			'social_youtube_url'   => __( 'YouTube URL', 'limbenet-core' ),
			'social_linkedin_url'  => __( 'LinkedIn URL', 'limbenet-core' ),
			'social_whatsapp_url'  => __( 'WhatsApp Channel URL', 'limbenet-core' ),
		);
	}

	/**
	 * Get merged settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $settings ) ? $settings : array(), self::defaults() );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Raw settings.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$input = is_array( $input ) ? $input : array();

		$settings = array(
			'default_whatsapp'      => isset( $input['default_whatsapp'] ) ? sanitize_text_field( $input['default_whatsapp'] ) : '',
			'default_contact_email' => isset( $input['default_contact_email'] ) ? sanitize_email( $input['default_contact_email'] ) : '',
			'google_maps_api_key'   => isset( $input['google_maps_api_key'] ) ? sanitize_text_field( $input['google_maps_api_key'] ) : '',
			'enable_partner_ctas'   => empty( $input['enable_partner_ctas'] ) ? '0' : '1',
			'currency'              => isset( $input['currency'] ) && in_array( $input['currency'], array( 'XAF', 'USD', 'EUR' ), true ) ? $input['currency'] : 'XAF',
			'affiliate_disclosure'  => isset( $input['affiliate_disclosure'] ) ? sanitize_textarea_field( $input['affiliate_disclosure'] ) : '',
			'safety_disclaimer'     => isset( $input['safety_disclaimer'] ) ? sanitize_textarea_field( $input['safety_disclaimer'] ) : '',
		);

		foreach ( array_keys( self::social_fields() ) as $key ) {
			$settings[ $key ] = isset( $input[ $key ] ) ? esc_url_raw( $input[ $key ] ) : '';
		}

		return $settings;
	}

	/**
	 * Render admin dashboard.
	 */
	public function render_dashboard() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Limbe.Net Tourism', 'limbenet-core' ); ?></h1>
			<p><?php esc_html_e( 'Manage Cameroon tourism content, partners, deals, booking requests, and safety information from one workspace.', 'limbenet-core' ); ?></p>
			<div class="lnet-admin-dashboard">
				<p><strong><?php esc_html_e( 'Positioning reminder:', 'limbenet-core' ); ?></strong> <?php esc_html_e( 'Limbe.Net is an independent Cameroon tourism guide, not an official government portal.', 'limbenet-core' ); ?></p>
				<p>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=limbenet-seed-importer' ) ); ?>"><?php esc_html_e( 'Import Seed Content', 'limbenet-core' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=limbenet-settings' ) ); ?>"><?php esc_html_e( 'Open Settings', 'limbenet-core' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=limbenet_request' ) ); ?>"><?php esc_html_e( 'View Booking Requests', 'limbenet-core' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render settings page.
	 */
	public function render_settings() {
		$settings = self::get_settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Limbe.Net Settings', 'limbenet-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'limbenet_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="limbenet_default_whatsapp"><?php esc_html_e( 'Default WhatsApp Number', 'limbenet-core' ); ?></label></th>
						<td><input class="regular-text" id="limbenet_default_whatsapp" name="<?php echo esc_attr( self::OPTION ); ?>[default_whatsapp]" type="text" value="<?php echo esc_attr( $settings['default_whatsapp'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="limbenet_default_contact_email"><?php esc_html_e( 'Default Contact Email', 'limbenet-core' ); ?></label></th>
						<td><input class="regular-text" id="limbenet_default_contact_email" name="<?php echo esc_attr( self::OPTION ); ?>[default_contact_email]" type="email" value="<?php echo esc_attr( $settings['default_contact_email'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="limbenet_google_maps_api_key"><?php esc_html_e( 'Google Maps API Key Placeholder', 'limbenet-core' ); ?></label></th>
						<td><input class="regular-text" id="limbenet_google_maps_api_key" name="<?php echo esc_attr( self::OPTION ); ?>[google_maps_api_key]" type="text" value="<?php echo esc_attr( $settings['google_maps_api_key'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Social Media Links', 'limbenet-core' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'These links power the footer and contact-page social icon placeholders across all Limbe.Net themes.', 'limbenet-core' ); ?></p>
							<?php foreach ( self::social_fields() as $key => $label ) : ?>
								<p>
									<label for="limbenet_<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
									<input class="regular-text" id="limbenet_<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( self::OPTION ); ?>[<?php echo esc_attr( $key ); ?>]" type="url" value="<?php echo esc_attr( $settings[ $key ] ); ?>" placeholder="https://">
								</p>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Partner Monetization CTAs', 'limbenet-core' ); ?></th>
						<td><label><input name="<?php echo esc_attr( self::OPTION ); ?>[enable_partner_ctas]" type="checkbox" value="1" <?php checked( '1', $settings['enable_partner_ctas'] ); ?>> <?php esc_html_e( 'Enable partner, claim listing, and advertise CTAs.', 'limbenet-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="limbenet_currency"><?php esc_html_e( 'Currency', 'limbenet-core' ); ?></label></th>
						<td>
							<select id="limbenet_currency" name="<?php echo esc_attr( self::OPTION ); ?>[currency]">
								<option value="XAF" <?php selected( $settings['currency'], 'XAF' ); ?>><?php esc_html_e( 'XAF', 'limbenet-core' ); ?></option>
								<option value="USD" <?php selected( $settings['currency'], 'USD' ); ?>><?php esc_html_e( 'USD', 'limbenet-core' ); ?></option>
								<option value="EUR" <?php selected( $settings['currency'], 'EUR' ); ?>><?php esc_html_e( 'EUR', 'limbenet-core' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="limbenet_affiliate_disclosure"><?php esc_html_e( 'Affiliate Disclosure Text', 'limbenet-core' ); ?></label></th>
						<td><textarea class="large-text" rows="4" id="limbenet_affiliate_disclosure" name="<?php echo esc_attr( self::OPTION ); ?>[affiliate_disclosure]"><?php echo esc_textarea( $settings['affiliate_disclosure'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="limbenet_safety_disclaimer"><?php esc_html_e( 'Safety Disclaimer Text', 'limbenet-core' ); ?></label></th>
						<td><textarea class="large-text" rows="4" id="limbenet_safety_disclaimer" name="<?php echo esc_attr( self::OPTION ); ?>[safety_disclaimer]"><?php echo esc_textarea( $settings['safety_disclaimer'] ); ?></textarea></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
