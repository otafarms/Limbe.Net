<?php
/**
 * Core plugin bootstrap.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
final class LimbeNet_Core {
	/**
	 * Singleton instance.
	 *
	 * @var LimbeNet_Core|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return LimbeNet_Core
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_textdomain();

		$post_types    = new LimbeNet_Core_Post_Types();
		$taxonomies    = new LimbeNet_Core_Taxonomies();
		$meta_boxes    = new LimbeNet_Core_Meta_Boxes();
		$settings      = new LimbeNet_Core_Settings();
		$forms         = new LimbeNet_Core_Forms();
		$shortcodes    = new LimbeNet_Core_Shortcodes();
		$schema        = new LimbeNet_Core_Schema();
		$seed_importer = new LimbeNet_Core_Seed_Importer();

		add_action( 'init', array( $post_types, 'register' ) );
		add_action( 'init', array( $taxonomies, 'register' ) );
		add_action( 'init', array( $meta_boxes, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $meta_boxes, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $meta_boxes, 'save_meta' ) );
		add_action( 'admin_menu', array( $settings, 'register_menu' ), 5 );
		add_action( 'admin_init', array( $settings, 'register_settings' ) );
		add_action( 'admin_menu', array( $seed_importer, 'register_menu' ), 20 );
		add_action( 'admin_post_limbenet_import_seed', array( $seed_importer, 'handle_import' ) );
		add_action( 'init', array( $forms, 'handle_submission' ) );
		add_action( 'init', array( $shortcodes, 'register' ) );
		add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 20 );
		add_action( 'pre_get_posts', array( $shortcodes, 'filter_main_query' ) );
		add_action( 'wp_head', array( $schema, 'print_schema' ), 30 );
		add_action( 'wp_head', array( $schema, 'print_meta_description' ), 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_cookie_consent' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );
	}

	/**
	 * Load plugin translations.
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'limbenet-core', false, dirname( plugin_basename( LIMBENET_CORE_FILE ) ) . '/languages' );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_public_assets() {
		wp_enqueue_style(
			'limbenet-core-frontend',
			LIMBENET_CORE_URL . 'assets/css/frontend.css',
			array(),
			LIMBENET_CORE_VERSION
		);

		wp_enqueue_script(
			'limbenet-core-frontend',
			LIMBENET_CORE_URL . 'assets/js/frontend.js',
			array(),
			LIMBENET_CORE_VERSION,
			true
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( false === strpos( $hook, 'limbenet' ) && false === strpos( $hook, 'post.php' ) && false === strpos( $hook, 'post-new.php' ) ) {
			return;
		}

		wp_enqueue_style(
			'limbenet-core-admin',
			LIMBENET_CORE_URL . 'assets/css/frontend.css',
			array(),
			LIMBENET_CORE_VERSION
		);
	}

	/**
	 * Declare compatibility with WooCommerce features where available.
	 */
	public function declare_woocommerce_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', LIMBENET_CORE_FILE, true );
		}
	}

	/**
	 * Render the cookie consent interface.
	 */
	public function render_cookie_consent() {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$cookie_policy_url = home_url( '/cookie-policy/' );
		?>
		<div class="lnet-cookie-consent" data-lnet-cookie-consent hidden>
			<div class="lnet-cookie-backdrop" data-lnet-cookie-backdrop hidden></div>

			<section class="lnet-cookie-modal" data-lnet-cookie-modal hidden aria-labelledby="lnet-cookie-title" role="dialog" aria-modal="true">
				<div class="lnet-cookie-dialog">
					<button class="lnet-cookie-close" type="button" data-lnet-cookie-close aria-label="<?php esc_attr_e( 'Close cookie preferences', 'limbenet-core' ); ?>">x</button>
					<div class="lnet-cookie-modal-intro">
						<h2 id="lnet-cookie-title"><?php esc_html_e( 'Cookie Preferences', 'limbenet-core' ); ?></h2>
						<p>
							<?php esc_html_e( 'This website uses cookies and similar technologies to operate the site, remember choices, measure performance, and improve Limbe.Net.', 'limbenet-core' ); ?>
							<a href="<?php echo esc_url( $cookie_policy_url ); ?>"><?php esc_html_e( 'Learn more in our Cookie Policy.', 'limbenet-core' ); ?></a>
						</p>
					</div>

					<div class="lnet-cookie-categories">
						<label class="lnet-cookie-category is-required">
							<span class="lnet-cookie-category-icon" aria-hidden="true">!</span>
							<span>
								<strong><?php esc_html_e( 'Strictly necessary', 'limbenet-core' ); ?></strong>
								<small><?php esc_html_e( 'Required for core site functions such as navigation, security, forms, and storing your cookie choice.', 'limbenet-core' ); ?></small>
							</span>
							<span class="lnet-cookie-switch">
								<input type="checkbox" checked disabled data-lnet-cookie-category="necessary">
								<span aria-hidden="true"></span>
							</span>
						</label>

						<label class="lnet-cookie-category">
							<span class="lnet-cookie-category-icon" aria-hidden="true">A</span>
							<span>
								<strong><?php esc_html_e( 'Analytics and performance', 'limbenet-core' ); ?></strong>
								<small><?php esc_html_e( 'Helps us understand page visits, search behavior, and site performance so we can improve travel content.', 'limbenet-core' ); ?></small>
							</span>
							<span class="lnet-cookie-switch">
								<input type="checkbox" data-lnet-cookie-category="analytics">
								<span aria-hidden="true"></span>
							</span>
						</label>

						<label class="lnet-cookie-category">
							<span class="lnet-cookie-category-icon" aria-hidden="true">P</span>
							<span>
								<strong><?php esc_html_e( 'Preferences and functionality', 'limbenet-core' ); ?></strong>
								<small><?php esc_html_e( 'Remembers choices such as language, display preferences, and optional site features.', 'limbenet-core' ); ?></small>
							</span>
							<span class="lnet-cookie-switch">
								<input type="checkbox" data-lnet-cookie-category="preferences">
								<span aria-hidden="true"></span>
							</span>
						</label>

						<label class="lnet-cookie-category">
							<span class="lnet-cookie-category-icon" aria-hidden="true">M</span>
							<span>
								<strong><?php esc_html_e( 'Marketing and embedded media', 'limbenet-core' ); ?></strong>
								<small><?php esc_html_e( 'Allows optional social, video, map, advertising, and partner features that may set third-party cookies.', 'limbenet-core' ); ?></small>
							</span>
							<span class="lnet-cookie-switch">
								<input type="checkbox" data-lnet-cookie-category="marketing">
								<span aria-hidden="true"></span>
							</span>
						</label>
					</div>

					<div class="lnet-cookie-actions">
						<button class="lnet-cookie-button is-outline" type="button" data-lnet-cookie-allow-all><?php esc_html_e( 'Allow All', 'limbenet-core' ); ?></button>
						<button class="lnet-cookie-button is-muted" type="button" data-lnet-cookie-decline><?php esc_html_e( 'Decline', 'limbenet-core' ); ?></button>
						<button class="lnet-cookie-button is-primary" type="button" data-lnet-cookie-save><?php esc_html_e( 'Accept Selected', 'limbenet-core' ); ?></button>
					</div>
				</div>
			</section>

			<section class="lnet-cookie-banner" data-lnet-cookie-banner role="region" aria-label="<?php esc_attr_e( 'Cookie notice', 'limbenet-core' ); ?>">
				<p>
					<?php esc_html_e( 'This website uses cookies to keep Limbe.Net working and to improve your travel planning experience.', 'limbenet-core' ); ?>
					<a href="<?php echo esc_url( $cookie_policy_url ); ?>"><?php esc_html_e( 'Learn more about our Cookie Policy.', 'limbenet-core' ); ?></a>
				</p>
				<div class="lnet-cookie-banner-actions">
					<button class="lnet-cookie-link-button" type="button" data-lnet-cookie-manage><?php esc_html_e( 'Manage Cookies', 'limbenet-core' ); ?></button>
					<button class="lnet-cookie-button is-muted" type="button" data-lnet-cookie-decline><?php esc_html_e( 'Decline', 'limbenet-core' ); ?></button>
					<button class="lnet-cookie-button is-accent" type="button" data-lnet-cookie-allow-all><?php esc_html_e( 'Allow All', 'limbenet-core' ); ?></button>
					<button class="lnet-cookie-dismiss" type="button" data-lnet-cookie-dismiss aria-label="<?php esc_attr_e( 'Dismiss cookie notice', 'limbenet-core' ); ?>">x</button>
				</div>
			</section>
		</div>
		<?php
	}

	/**
	 * Flush rewrite rules once after plugin updates that add routes.
	 */
	public function maybe_flush_rewrite_rules() {
		if ( get_option( 'limbenet_core_rewrite_version' ) === LIMBENET_CORE_VERSION ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( 'limbenet_core_rewrite_version', LIMBENET_CORE_VERSION );
	}

	/**
	 * Plugin activation.
	 */
	public static function activate() {
		$post_types = new LimbeNet_Core_Post_Types();
		$taxonomies = new LimbeNet_Core_Taxonomies();

		$post_types->register();
		$taxonomies->register();
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
