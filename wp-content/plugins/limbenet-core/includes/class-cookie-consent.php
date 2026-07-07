<?php
/**
 * Cookie consent banner.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a lightweight cookie consent widget.
 */
class LimbeNet_Core_Cookie_Consent {
	/**
	 * Register frontend hooks.
	 */
	public function register() {
		add_action( 'wp_footer', array( $this, 'render' ), 60 );
	}

	/**
	 * Render consent widget.
	 */
	public function render() {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$settings = LimbeNet_Core_Settings::get_settings();
		if ( '1' !== $settings['enable_cookie_consent'] ) {
			return;
		}

		$privacy_url = get_privacy_policy_url();
		if ( ! $privacy_url ) {
			$privacy_url = $this->page_url( 'privacy-policy' );
		}

		$cookie_url = $this->page_url( 'cookie-policy' );
		?>
		<div class="lnet-cookie-widget" data-consent-version="<?php echo esc_attr( $settings['cookie_consent_version'] ); ?>" hidden>
			<div class="lnet-cookie-panel" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Cookie preferences', 'limbenet-core' ); ?>" tabindex="-1">
				<div class="lnet-cookie-copy">
					<h2><?php esc_html_e( 'Cookie preferences', 'limbenet-core' ); ?></h2>
					<p><?php echo esc_html( $settings['cookie_banner_text'] ); ?></p>
					<p class="lnet-cookie-links">
						<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'limbenet-core' ); ?></a>
						<a href="<?php echo esc_url( $cookie_url ); ?>"><?php esc_html_e( 'Cookie Policy', 'limbenet-core' ); ?></a>
					</p>
				</div>

				<div class="lnet-cookie-preferences" hidden>
					<label>
						<input type="checkbox" checked disabled>
						<span><?php esc_html_e( 'Essential cookies are always on for security, forms, and site preferences.', 'limbenet-core' ); ?></span>
					</label>
					<label>
						<input type="checkbox" data-lnet-cookie-choice="analytics">
						<span><?php esc_html_e( 'Analytics cookies help us understand which travel pages are useful.', 'limbenet-core' ); ?></span>
					</label>
					<label>
						<input type="checkbox" data-lnet-cookie-choice="marketing">
						<span><?php esc_html_e( 'Marketing cookies may support partner offers and campaign measurement.', 'limbenet-core' ); ?></span>
					</label>
				</div>

				<div class="lnet-cookie-actions">
					<button type="button" class="lnet-button" data-lnet-cookie-action="accept"><?php esc_html_e( 'Accept all', 'limbenet-core' ); ?></button>
					<button type="button" class="lnet-button-outline-dark" data-lnet-cookie-action="reject"><?php esc_html_e( 'Reject optional', 'limbenet-core' ); ?></button>
					<button type="button" class="lnet-button-outline-dark" data-lnet-cookie-action="manage"><?php esc_html_e( 'Manage choices', 'limbenet-core' ); ?></button>
					<button type="button" class="lnet-button" data-lnet-cookie-action="save" hidden><?php esc_html_e( 'Save choices', 'limbenet-core' ); ?></button>
				</div>
			</div>
		</div>
		<button type="button" class="lnet-cookie-settings-button" data-lnet-cookie-action="open-settings" hidden><?php esc_html_e( 'Cookie settings', 'limbenet-core' ); ?></button>
		<?php
	}

	/**
	 * Get a page URL by slug.
	 *
	 * @param string $slug Page slug.
	 * @return string
	 */
	private function page_url( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return get_permalink( $page );
		}

		return home_url( '/' . trim( $slug, '/' ) . '/' );
	}
}
