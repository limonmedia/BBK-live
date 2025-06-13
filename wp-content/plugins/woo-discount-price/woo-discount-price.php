<?php
/*
 * Plugin Name: Discount and regular price cart and checkout page display WooCommerce
 * Plugin URI:  https://wpnetwork.it/shop/woocommerce-discount-price-premium/
 * Description: display the regular and discounted price in cart and checkout page
 * Version:     1.4.0
 * Contributors: wpnetworkit, cristianozanca
 * Author:      wpnetworkit
 * Author URI:  https://wpnetwork.it
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-discount-price
 * Domain Path: /languages
 * WC requires at least: 7.0
 * WC tested up to: 8.7
*/


/* TODO  check network activation */

function woo_discount_price_textdomain() {
	load_plugin_textdomain( 'woo-discount-price', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'woo_discount_price_textdomain' );

if ( ! class_exists( 'woo_discount_price' ) ) :
{
	class woo_discount_price
	{

		public function __construct() {

			if ( ! defined( 'ABSPATH' ) ) {
				exit; // Exit if accessed directly
			}



			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/custom_template.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/display_cart.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/display_total.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/menu_setup.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/setup_page_display.php';

			include_once plugin_dir_path( __FILE__ ) . 'inc/notice_recensione.php';

			

			function woodiscpr_error_notice() {
				if(! is_plugin_active( 'woocommerce/woocommerce.php')) {

					?>
					<div class="notice error is-dismissible">
						<p><?php _e( 'Please install or activate WooCommerce Plugin, it is required for WooCommerce Discount Price Plugin to work ', 'woo-discount-price' ); ?></p>
					</div>
					<?php
				}


			}

			function check_wc_version(){
				if ( function_exists( 'WC' ) && ( version_compare( WC()->version, '3.0', "<" ) )) {
					?>
					<div class="notice error is-dismissible">
						<p><?php _e('WooCommerce version detected: '. WC()->version.' please update to 3.0 to activate to use Discount Price Plugin','woo-discount-price' ); ?></p>
					</div>
					<?php
				}
			}

			function set_up_woodiscpr_options(){
				update_option('woodiscpr_you_save', 1);
			}

			function set_up_taxexcl_options(){
				update_option('woodiscpr_taxexcl', 0);
			}



			load_plugin_textdomain( 'woo-discount-price' );

			add_action( 'admin_notices', 'check_wc_version');
			add_action( 'admin_notices', 'woodiscpr_error_notice' );
			add_filter( 'woocommerce_cart_item_price', 'woodiscpr_change_cart_table_price_display', 30, 3 );
			add_action( 'woocommerce_cart_totals_after_order_total', 'woodiscpr_wc_discount_total_30', 99);
			add_action( 'woocommerce_review_order_after_order_total', 'woodiscpr_wc_discount_total_30', 99);
			add_filter( 'woocommerce_locate_template', 'woodiscpr_custom_woocommerce_locate_template', 10, 3 );
			add_action( 'admin_menu', 'woodiscpr_plugin_setup_menu');
			add_action('admin_menu', 'add_woo_discount_to_woocommerce_navigation_bar');
			add_action( 'admin_enqueue_scripts', array($this, 'register_woo_discount_price_styles_and_scripts'));
			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );
			
			register_activation_hook( __FILE__, 'set_up_woodiscpr_options' );
			register_activation_hook( __FILE__, 'set_up_taxexcl_options' );

		}

		function register_woo_discount_price_styles_and_scripts($hook)
        	{

                $current_screen = get_current_screen();

            if (strpos($current_screen->base, 'woo-discount-price') === false) {
                    return;
                
            } else {

                    wp_enqueue_style('boot_css', plugins_url('assets/css/woo_disc_pr.css', __FILE__));
									
									
					}
			}


	}

}



//Creates a new instance
new woo_discount_price;

endif;










