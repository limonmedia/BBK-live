<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin Name: Lipscore Ratings and Reviews
 * Plugin URI:  http://lipscore.com/
 * Description: Collecting reviews is difficult. Let the most efficient and flexible plugin in the world do it for you.
 * Version:     0.6.2
 * Author:      Lipscore
 * Author URI:  http://lipscore.com/
 * Donate link: http://lipscore.com/
 * License:     GPLv2
 * Text Domain: lipscore
 * Domain Path: /languages
 *
 * @link http://lipscore.com/
 *
 * @package lipscore
 * @version 0.4.4
 */

/**
 * Copyright (c) 2021 Lipscore (email : support@lipscore.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */


/**
 * Autoloads files with classes when needed
 *
 * @since  0.1.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function lipscore_autoload_classes( $class_name ) {
    $starts_from = 'Lipscore_';

	if ( 0 !== strpos( $class_name, $starts_from ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-', $class_name
	) );

	Lipscore::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'lipscore_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  0.1.0
 */
final class Lipscore {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.1.0
	 */
	const VERSION = '0.6.2';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages
	 *
	 * @var array
	 * @since  0.1.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin
	 *
	 * @var Lipscore
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

    protected $config;
    protected $settings_tab;
    protected $widget_manager;
    protected $initializer;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.1.0
	 * @return Lipscore A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

    $this->config = new Lipscore_Config();
	}

	/**
	 * Add hooks and filters
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
		// Priority needs to be:
		// < 10 for CPT_Core,
		// < 5 for Taxonomy_Core,
		// 0 Widgets because widgets_init runs at init priority 1.

		add_action( 'init', array( $this, 'init' ), 0 );

        $settings_tab = new Lipscore_Admin_Settings_Tab();
        add_filter(
            'woocommerce_settings_tabs_array',
            array( $settings_tab, 'init' ),
            50
        );
        add_action(
            'woocommerce_settings_tabs_settings_tab_lipscore',
            array( $settings_tab, 'tab_content' )
        );
        add_action(
            'woocommerce_update_options_settings_tab_lipscore',
            array( $settings_tab, 'update_settings' )
        );
        add_action(
            'admin_enqueue_scripts',
            array( $settings_tab, 'add_assets' )
        );

        $wc_review_disabler = new Lipscore_WC_Review_Disabler();
        add_filter(
            'comments_open',
            array( $wc_review_disabler, 'disable_product_comments' ),
            1000,
            2
        );
        add_action(
            'add_meta_boxes' ,
            array( $wc_review_disabler, 'remove_metaboxes' ),
            99
        );
        add_action(
            'wp_dashboard_setup',
            array( $wc_review_disabler, 'remove_dashboard_reviews' ),
            50
        );

        $widget_manager = new Lipscore_Widget_Manager();

				if ( Lipscore_Settings::is_ratings_displayed() ) {
					add_action(
							'woocommerce_after_shop_loop_item_title',
							array( $widget_manager, 'add_small_rating' ),
							1
					);
					add_action(
							'woocommerce_single_product_summary',
							array( $widget_manager, 'add_rating' ),
							6
					);
				}

				if ( Lipscore_Settings::is_reviews_displayed() ) {
	        add_action(
	            'woocommerce_product_tabs',
	            array( $widget_manager, 'add_reviews_tab' ),
	            6
	        );
					add_filter(
	            'comments_template',
	            array( $widget_manager, 'show_reviews_instead_comments' ),
	            10,
	            2
	        );
				}

				if ( Lipscore_Settings::is_questions_displayed() ) {
					add_action(
	            'woocommerce_product_tabs',
	            array( $widget_manager, 'add_questions_tab' ),
	            7
	        );
					add_filter(
	            'comments_template',
	            array( $widget_manager, 'show_questions_instead_comments' ),
	            11,
	            2
	        );
				}

        add_action(
            'wp_head',
            array( $widget_manager, 'add_styles' ),
            6
        );
        add_action(
            'wp_enqueue_scripts',
            array( $widget_manager, 'add_scripts' ),
            6
        );

        $initializer = new Lipscore_Initializer();
        add_action(
            'wp_head',
            array( $initializer, 'add_script' )
        );

        $order_observer = new Lipscore_Order_Observer();
        add_action(
            'woocommerce_checkout_order_processed',
            array( $order_observer, 'create' ),
            10,
            2
        );
        add_action(
            'woocommerce_order_status_changed',
            array( $order_observer, 'status_update' ),
            10,
            3
        );

        add_filter(
            'option_lipscore_locale',
            array( $this, 'fix_lipscore_locale_option' ),
            10,
            2
        );

				add_filter(
						'plugin_action_links_' . $this->basename,
						array( __CLASS__, 'plugin_action_links' )
				);

				add_filter(
						'load_textdomain_mofile',
						array( $this, 'load_custom_plugin_translation_file' ),
						10,
						2
				);

				$shortcode_manager = Lipscore_Shortcode_Manager::get_instance();
				add_action( 'init', [ $shortcode_manager, 'register_shortcodes' ] );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=settings_tab_lipscore' ) . '" aria-label="' . esc_attr__( 'View Lipscore settings', 'lipscore' ) . '">' . esc_html__( 'Settings' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();

        $initial_wc_enable_review_rating = get_option('woocommerce_enable_review_rating');
        update_option('lipscore_woocommerce_enable_review_rating', $initial_wc_enable_review_rating);
        update_option('woocommerce_enable_review_rating', 'no');
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function _deactivate() {
        $initial_wc_enable_review_rating = get_option('lipscore_woocommerce_enable_review_rating');
        update_option('woocommerce_enable_review_rating', $initial_wc_enable_review_rating);
    }

	/**
	 * Init hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function init() {
		// bail early if requirements aren't met
		if ( ! $this->check_requirements() ) {
			return;
		}

		// load translated strings for plugin
		load_plugin_textdomain( 'lipscore', false, dirname( $this->basename ) . '/languages/' );
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.1.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		// bail early if pluginmeets requirements
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function deactivate_me() {
		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.1.0
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		// Add detailed messages to $this->activation_errors array
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// compile default message
		$default_message = sprintf(
			__( 'lipscore is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'lipscore' ),
			admin_url( 'plugins.php' )
		);

		// default details to null
		$details = null;

		// add details if any exist
		if ( ! empty( $this->activation_errors ) && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// output errors
		?>
		<div id="message" class="error">
			<p><?php echo $default_message; ?></p>
			<?php echo $details; ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.1.0
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
            case 'config':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  0.1.0
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  0.1.0
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  0.1.0
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

  public static function assets_url( $path = '' ) {
      return static::url( 'assets/' . $path );
  }

  public function fix_lipscore_locale_option( $value, $option) {
      if ( $value == 'auto' ) {
          return Lipscore_Settings::DEFAULT_LOCALE;
      } else {
          return $value;
      }
  }

	function load_custom_plugin_translation_file( $mofile, $domain ) {
	  if ( 'lipscore' === $domain ) {
	    $mofile = self::dir( 'languages/lipscore-' . get_locale() . '.mo' );
	  }
	  return $mofile;
	}
}

/**
 * Grab the Lipscore object and return it.
 * Wrapper for Lipscore::get_instance()
 *
 * @since  0.1.0
 * @return Lipscore  Singleton instance of plugin class.
 */
function lipscore() {
	return Lipscore::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( lipscore(), 'hooks' ) );

register_activation_hook( __FILE__, array( lipscore(), '_activate' ) );
register_deactivation_hook( __FILE__, array( lipscore(), '_deactivate' ) );
