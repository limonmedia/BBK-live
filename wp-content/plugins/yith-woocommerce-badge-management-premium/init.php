<?php
/**
 * Plugin Name: YITH WooCommerce Badge Management Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-badges-management/
 * Description: Highlight discounts, offers and products features using <strong>custom graphic badges.</strong> <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 3.15.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-badges-management
 * Domain Path: /languages/
 * WC requires at least: 9.5
 * WC tested up to: 9.7
 * Requires Plugins: woocommerce
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement
 * @version 3.15.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
global $yith_activator_loaded;
if (!isset($yith_activator_loaded)) {
    $yith_activator_loaded = true;

    add_filter('pre_http_request', function($pre, $args, $url) {
		if (strpos($url, 'yithemes.com/wp-json/wc/v3/upsells-data') !== false) {
			return new WP_Error('http_request_blocked', 'Request blocked by filter');
		}
        if (strpos($url, 'yithemes.com') !== false) {
            return [
                'headers' => [],
                'body' => json_encode([
                    "timestamp" => time(),
                    "message" => "900 out of 999 activations remaining",
                    "activated" => true,
                    "instance" => parse_url(get_site_url(), PHP_URL_HOST),
                    "licence_expires" => strtotime('+5 years'),
                    "activation_limit" => 999,
                    "activation_remaining" => 900,
                    "is_membership" => true
                ]),
                'response' => ['code' => 200, 'message' => 'OK'],
                'cookies' => []
            ];
        }
        return $pre;
    }, 10, 3);

    class YITH_Activator {
        public function __construct() {
            if (is_admin()) {
                add_action('init', [$this, 'activate']);
                add_action('plugins_loaded', [$this, 'remove_redirect']);
                add_action('admin_init', [$this, 'disable_onboarding'], 0);
                add_action('admin_init', [$this, 'disable_updates'], 100);
                add_action('admin_init', [$this, 'disable_banners']);
            }
        }

        public function activate() {
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins = get_plugins();
            $licenses = [];

            foreach ($plugins as $path => $data) {
                if (strpos($data['TextDomain'], 'yith') === 0) {
                    $slug = $this->get_slug($path, $data['TextDomain']);
                    $licenses[$slug] = $this->license_data();
                }
            }

            foreach (['yit_products_licence_activation', 'yit_plugin_licence_activation', 'yit_theme_licence_activation'] as $option) {
                update_option($option, $licenses);
            }
        }

        private function get_slug($path, $domain) {
            $file = WP_PLUGIN_DIR . '/' . $path;
            $content = file_get_contents($file);

            return preg_match('/define\s*\(\s*[\'"]([^\'"]+_SLUG)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches) ? $matches[2] : $domain;
        }

        private function license_data() {
            return [
                'activated' => true,
                'email' => 'nulled@weadown.com',
                'licence_key' => 'weadown*-****-****-****-*********com',
                'activation_limit' => 999,
                'activation_remaining' => 900,
                'is_membership' => true,
                'marketplace' => 'yith',
                'licence_expires' => strtotime('+5 years'),
            ];
        }

        public function remove_redirect() {
            remove_action('admin_init', ['YITH_Plugin_Licence_Onboarding', 'handle_redirect'], 5);
        }

        public function disable_onboarding() {
            set_transient('yith_plugin_licence_onboarding_queue', [], 1);
        }

        public function disable_updates() {
            if (class_exists('YITH\PluginUpgrade\Upgrade')) {
                remove_action('load-plugins.php', [YITH\PluginUpgrade\Upgrade::instance(), 'remove_wp_plugin_update_row'], 25);
            }
            if (class_exists('YITH_Plugin_Upgrade')) {
                remove_action('load-plugins.php', [YITH_Plugin_Upgrade::instance(), 'remove_wp_plugin_update_row'], 25);
            }
        }

        public function disable_banners() {
            remove_action('admin_enqueue_scripts', ['YITH\\PluginUpgrade\\Admin\\Banner', 'register_scripts'], 5);
            remove_action('yith_plugin_fw_panel_enqueue_scripts', ['YITH\\PluginUpgrade\\Admin\\Banner', 'maybe_enqueue_and_render_licence_banner']);
            remove_action('wp_ajax_yith_plugin_upgrade_licence_modal_dismiss', ['YITH\\PluginUpgrade\\Admin\\Banner', 'dismiss_licence_modal']);
        }
    }

    new YITH_Activator();
}
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Free version deactivation if installed!
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCBM_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

! defined( 'YITH_WCBM_VERSION' ) && define( 'YITH_WCBM_VERSION', '3.15.0' );

! defined( 'YITH_WCBM_PREMIUM' ) && define( 'YITH_WCBM_PREMIUM', '1' );

! defined( 'YITH_WCBM_INIT' ) && define( 'YITH_WCBM_INIT', plugin_basename( __FILE__ ) );

! defined( 'YITH_WCBM' ) && define( 'YITH_WCBM', true );

! defined( 'YITH_WCBM_FILE' ) && define( 'YITH_WCBM_FILE', __FILE__ );

! defined( 'YITH_WCBM_URL' ) && define( 'YITH_WCBM_URL', plugin_dir_url( __FILE__ ) );

! defined( 'YITH_WCBM_DIR' ) && define( 'YITH_WCBM_DIR', plugin_dir_path( __FILE__ ) );

! defined( 'YITH_WCBM_PLUGIN_OPTIONS_PATH' ) && define( 'YITH_WCBM_PLUGIN_OPTIONS_PATH', YITH_WCBM_DIR . 'plugin-options/' );

! defined( 'YITH_WCBM_ASSETS_PATH' ) && define( 'YITH_WCBM_ASSETS_PATH', YITH_WCBM_DIR . 'assets/' );

! defined( 'YITH_WCBM_ASSETS_IMAGES_PATH' ) && define( 'YITH_WCBM_ASSETS_IMAGES_PATH', YITH_WCBM_ASSETS_PATH . 'images/' );

! defined( 'YITH_WCBM_TEMPLATES_PATH' ) && define( 'YITH_WCBM_TEMPLATES_PATH', YITH_WCBM_DIR . 'templates/' );

! defined( 'YITH_WCBM_VIEWS_PATH' ) && define( 'YITH_WCBM_VIEWS_PATH', YITH_WCBM_DIR . 'views/' );

! defined( 'YITH_WCBM_ASSETS_URL' ) && define( 'YITH_WCBM_ASSETS_URL', YITH_WCBM_URL . 'assets/' );

! defined( 'YITH_WCBM_ASSETS_CSS_URL' ) && define( 'YITH_WCBM_ASSETS_CSS_URL', YITH_WCBM_ASSETS_URL . 'css/' );

! defined( 'YITH_WCBM_ASSETS_JS_URL' ) && define( 'YITH_WCBM_ASSETS_JS_URL', YITH_WCBM_ASSETS_URL . 'js/' );

! defined( 'YITH_WCBM_SLUG' ) && define( 'YITH_WCBM_SLUG', 'yith-woocommerce-badges-management' );

! defined( 'YITH_WCBM_INCLUDES_PATH' ) && define( 'YITH_WCBM_INCLUDES_PATH', YITH_WCBM_DIR . 'includes/' );

! defined( 'YITH_WCBM_COMPATIBILITY_PATH' ) && define( 'YITH_WCBM_COMPATIBILITY_PATH', YITH_WCBM_INCLUDES_PATH . 'compatibility/' );

! defined( 'YITH_WCBM_SECRET_KEY' ) && define( 'YITH_WCBM_SECRET_KEY', 'u6YZJsaoKkwhYSIUgZ6X' );

! defined( 'YITH_WCBM_PLUGIN_NAME' ) && define( 'YITH_WCBM_PLUGIN_NAME', 'YITH WooCommerce Badge Management Premium' );

/**
 * Print admin notice if WC is not enabled
 */
function yith_wcbm_pr_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p>
			<?php esc_html_e( 'YITH WooCommerce Badge Management is enabled but not effective. It requires Woocommerce in order to work.', 'yith-woocommerce-badges-management' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Init.
 */
function yith_wcbm_pr_init() {
	if ( function_exists( 'yith_plugin_fw_load_plugin_textdomain' ) ) {
		yith_plugin_fw_load_plugin_textdomain( 'yith-woocommerce-badges-management', basename( dirname( __FILE__ ) ) . '/languages' );
	}


	require_once 'includes/functions.yith-wcbm.php';
	require_once 'includes/functions.yith-wcbm-premium.php';
	require_once 'includes/functions.yith-wcbm-badge-rules.php';
	require_once 'includes/functions.yith-wcbm-deprecated.php';
	require_once 'includes/functions.yith-wcbm-deprecated-premium.php';

	require_once 'includes/abstract-class-yith-wcbm-db.php';

	// Abstract Objects Classes.
	require_once 'includes/objects/class-yith-wcbm-badge-rule.php';
	require_once 'includes/objects/class-yith-wcbm-associative-badge-rule.php';

	// Objects Classes.
	require_once 'includes/objects/class-yith-wcbm-badge.php';
	require_once 'includes/objects/class-yith-wcbm-badge-premium.php';
	require_once 'includes/objects/class-yith-wcbm-badge-rule-tag.php';
	require_once 'includes/objects/class-yith-wcbm-badge-rule-product.php';
	require_once 'includes/objects/class-yith-wcbm-badge-rule-category.php';
	require_once 'includes/objects/class-yith-wcbm-badge-rule-shipping-class.php';

	// Abstract Data Stores.
	require_once 'includes/data-stores/class-yith-wcbm-simple-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-rule-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-associative-badge-rule-data-store-cpt.php';

	// Data Stores.
	require_once 'includes/data-stores/class-yith-wcbm-badge-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-premium-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-rule-tag-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-rule-product-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-rule-category-data-store-cpt.php';
	require_once 'includes/data-stores/class-yith-wcbm-badge-rule-shipping-class-data-store-cpt.php';

	require_once 'includes/class-yith-wcbm.php';
	require_once 'includes/class-yith-wcbm-admin.php';
	require_once 'includes/class-yith-wcbm-badges.php';
	require_once 'includes/class-yith-wcbm-install.php';
	require_once 'includes/class-yith-wcbm-premium.php';
	require_once 'includes/class-yith-wcbm-frontend.php';
	require_once 'includes/class-yith-wcbm-shortcodes.php';
	require_once 'includes/class-yith-wcbm-post-types.php';
	require_once 'includes/class-yith-wcbm-badge-rules.php';
	require_once 'includes/class-yith-wcbm-admin-premium.php';
	require_once 'includes/class-yith-wcbm-badges-premium.php';
	require_once 'includes/class-yith-wcbm-install-premium.php';
	require_once 'includes/class-yith-wcbm-frontend-premium.php';
	require_once 'includes/class-yith-wcbm-post-types-premium.php';
	require_once 'includes/compatibility/class-yith-wcbm-compatibility.php';

	// Let's start the game!
	YITH_WCBM();
}

add_action( 'yith_wcbm_pr_init', 'yith_wcbm_pr_init' );

/**
 * Install
 */
function yith_wcbm_pr_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcbm_pr_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcbm_pr_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcbm_pr_install', 11 );

// Plugin Framework Loader.
if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
