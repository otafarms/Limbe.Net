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
