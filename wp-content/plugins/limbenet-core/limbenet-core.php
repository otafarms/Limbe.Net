<?php
/**
 * Plugin Name: Limbe.Net Core
 * Plugin URI: https://limbe.net/
 * Description: Tourism directory, booking leads, partner listings, seed content, and frontend components for Limbe.Net.
 * Version: 1.0.2
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Limbe.Net
 * Author URI: https://limbe.net/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: limbenet-core
 * Domain Path: /languages
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LIMBENET_CORE_VERSION', '1.0.2' );
define( 'LIMBENET_CORE_FILE', __FILE__ );
define( 'LIMBENET_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIMBENET_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once LIMBENET_CORE_PATH . 'includes/class-core.php';
require_once LIMBENET_CORE_PATH . 'includes/class-post-types.php';
require_once LIMBENET_CORE_PATH . 'includes/class-taxonomies.php';
require_once LIMBENET_CORE_PATH . 'includes/class-meta-boxes.php';
require_once LIMBENET_CORE_PATH . 'includes/class-settings.php';
require_once LIMBENET_CORE_PATH . 'includes/class-forms.php';
require_once LIMBENET_CORE_PATH . 'includes/class-shortcodes.php';
require_once LIMBENET_CORE_PATH . 'includes/class-schema.php';
require_once LIMBENET_CORE_PATH . 'includes/class-seed-importer.php';

/**
 * Bootstrap plugin.
 *
 * @return LimbeNet_Core
 */
function limbenet_core() {
	return LimbeNet_Core::instance();
}
add_action( 'plugins_loaded', 'limbenet_core' );

register_activation_hook( __FILE__, array( 'LimbeNet_Core', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LimbeNet_Core', 'deactivate' ) );
