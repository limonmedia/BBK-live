<?php
/**
 * Billigebarneklaer functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Billigebarneklaer
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function billigebarneklaer_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Billigebarneklaer, use a find and replace
		* to change 'billigebarneklaer' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'billigebarneklaer', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'billigebarneklaer' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'billigebarneklaer_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'billigebarneklaer_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function billigebarneklaer_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'billigebarneklaer_content_width', 640 );
}
add_action( 'after_setup_theme', 'billigebarneklaer_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function billigebarneklaer_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'billigebarneklaer' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'billigebarneklaer' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'billigebarneklaer_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function billigebarneklaer_scripts() {
	wp_enqueue_style( 'billigebarneklaer-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'billigebarneklaer-style', 'rtl', 'replace' );

	wp_enqueue_script( 'billigebarneklaer-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'billigebarneklaer_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
// On-sale label on shop page
// Add sale percentage to shop page
add_action( 'woocommerce_shop_loop_item_title', 'softexpert_show_sale_percentage', 7 );

// Add sale percentage to product page
add_action( 'woocommerce_single_product_summary', 'softexpert_show_sale_percentage', 10 );

function softexpert_show_sale_percentage() {
   global $product;
   if ( ! $product->is_on_sale() ) return;
   if ( $product->is_type( 'simple' ) ) {
      $max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
   } elseif ( $product->is_type( 'variable' ) ) {
      $max_percentage = 0;
      foreach ( $product->get_children() as $child_id ) {
         $variation = wc_get_product( $child_id );
         $price = $variation->get_regular_price();
         $sale = $variation->get_sale_price();
         if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
         if ( $percentage > $max_percentage ) {
            $max_percentage = $percentage;
         }
      }
   }
   if ( $max_percentage > 0 ) echo "<span class='sale-price-off'>-" . round( $max_percentage ) . "%</span>";
}
// Register the shortcode
add_shortcode( 'show_sale_percentage', function() {
    global $post;

    // Get product object safely
    $product = wc_get_product( $post ? $post->ID : 0 );

    if ( ! $product || ! $product->is_on_sale() ) {
        return '';
    }

    $max_percentage = 0;

    if ( $product->is_type( 'simple' ) ) {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();

        if ( $regular_price > 0 && $sale_price > 0 ) {
            $max_percentage = ( ( $regular_price - $sale_price ) / $regular_price ) * 100;
        }
    } elseif ( $product->is_type( 'variable' ) ) {
        foreach ( $product->get_children() as $child_id ) {
            $variation = wc_get_product( $child_id );
            if ( ! $variation ) continue;

            $regular_price = (float) $variation->get_regular_price();
            $sale_price    = (float) $variation->get_sale_price();

            if ( $regular_price > 0 && $sale_price > 0 ) {
                $max_percentage = max( $max_percentage, ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
            }
        }
    }

    return $max_percentage > 0 ? "<span class='sale-price-off-product'>-" . round( $max_percentage ) . "%</span>" : '';
});



// Remove the subcategories from the product loop
remove_filter('woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories');


add_filter( 'woocommerce_subcategory_count_html', '__return_null' );
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

function display_specific_product_variable_attribute() {
    global $product;

    if ( ! $product || ! $product->is_type( 'variable' ) ) {
        return;
    }

    $storrelse_values = [];

    foreach ( $product->get_available_variations() as $variation ) {
        $attribute_slug = $variation['attributes']['attribute_pa_storrelse'] ?? '';

        if ( $attribute_slug && ! isset( $storrelse_values[ $attribute_slug ] ) ) {
            if ( $attribute_value = get_term_by( 'slug', $attribute_slug, 'pa_storrelse' ) ) {
                $storrelse_values[ $attribute_slug ] = $attribute_value->name;
            }
        }
    }

    if ( ! empty( $storrelse_values ) ) {
        natcasesort( $storrelse_values );
        echo '<div class="product-variables">' . esc_html( implode( ', ', $storrelse_values ) ) . '</div>';
    }
}
add_action( 'woocommerce_after_shop_loop_item_title', 'display_specific_product_variable_attribute', 1 );


function custom_remove_price_from_loop() {
    // Remove default price display
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

    // Re-add price after the product link is closed
    add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 12 );
}
add_action( 'init', 'custom_remove_price_from_loop' );

// Remove currency symbol
function codeAstrology_remove_wc_currency_symbols( $currency_symbol, $currency ) {
    $currency_symbol = '';
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'codeAstrology_remove_wc_currency_symbols', 10, 2);


// Remove the result count from WooCommerce
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
// Function to display WooCommerce result count
function custom_woocommerce_result_count_shortcode() {
    ob_start();
    woocommerce_result_count(); // Output the result count
    return ob_get_clean();
}


add_filter('woocommerce_reset_variations_link', '__return_empty_string');

// Enqueue the WordPress media scripts and TinyMCE
function enqueue_wp_media_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('editor');
    wp_enqueue_script('quicktags');
    wp_enqueue_script('wplink');
    wp_enqueue_script('wpdialogs-popup');
    wp_enqueue_script('wpdialogs');
    wp_enqueue_style('wp-jquery-ui-dialog');
    wp_enqueue_script('word-count');
    wp_enqueue_script('post');
    wp_enqueue_script('editor-expand');
    wp_enqueue_script('tinymce');
}
add_action('admin_enqueue_scripts', 'enqueue_wp_media_scripts');

// Add custom fields to product categories and YITH Brands add form
function add_custom_taxonomy_fields($taxonomy) {
    ?>
    <div class="form-field term-group">
        <label for="bottom_text"><?php _e('Bottom Text', 'text-domain'); ?></label>
        <?php wp_editor('', 'bottom_text', array('textarea_name' => 'bottom_text', 'media_buttons' => true, 'textarea_rows' => 5)); ?>
    </div>
    <?php
}
add_action('product_cat_add_form_fields', 'add_custom_taxonomy_fields', 10, 2);
add_action('yith_product_brand_add_form_fields', 'add_custom_taxonomy_fields', 10, 2);

// Edit custom fields for product categories and YITH Brands edit form
function edit_custom_taxonomy_fields($term, $taxonomy) {
    $bottom_text = get_term_meta($term->term_id, 'bottom_text', true);
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="bottom_text"><?php _e('Bottom Text', 'text-domain'); ?></label></th>
        <td>
            <?php wp_editor($bottom_text, 'bottom_text', array('textarea_name' => 'bottom_text', 'media_buttons' => true, 'textarea_rows' => 5)); ?>
        </td>
    </tr>
    <?php
}
add_action('product_cat_edit_form_fields', 'edit_custom_taxonomy_fields', 10, 2);
add_action('yith_product_brand_edit_form_fields', 'edit_custom_taxonomy_fields', 10, 2);

// Save custom fields for product categories and YITH Brands
function save_custom_taxonomy_fields($term_id, $tt_id) {
    if (isset($_POST['bottom_text'])) {
        $bottom_text = wp_kses_post($_POST['bottom_text']);
        update_term_meta($term_id, 'bottom_text', $bottom_text);
    }
}
add_action('created_product_cat', 'save_custom_taxonomy_fields', 10, 2);
add_action('edited_product_cat', 'save_custom_taxonomy_fields', 10, 2);
add_action('created_yith_product_brand', 'save_custom_taxonomy_fields', 10, 2);
add_action('edited_yith_product_brand', 'save_custom_taxonomy_fields', 10, 2);

function display_bottom_text_shortcode($atts) {
    // Check if it's a product category or YITH Brand archive page
    if (is_product_category() || is_tax('yith_product_brand')) {
        $term = get_queried_object();
        $bottom_text = get_term_meta($term->term_id, 'bottom_text', true);

        if ($bottom_text) {
            return '<div class="bottom-text">' . wp_kses_post($bottom_text) . '</div>';
        }
    }
    return '';
}
add_shortcode('bottom_text', 'display_bottom_text_shortcode');
function custom_size_taxonomy() {

    $labels = array(
        'name'              => _x( 'Sizes', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Size', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Sizes', 'textdomain' ),
        'all_items'         => __( 'All Sizes', 'textdomain' ),
        'parent_item'       => __( 'Parent Size', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Size:', 'textdomain' ),
        'edit_item'         => __( 'Edit Size', 'textdomain' ),
        'update_item'       => __( 'Update Size', 'textdomain' ),
        'add_new_item'      => __( 'Add New Size', 'textdomain' ),
        'new_item_name'     => __( 'New Size Name', 'textdomain' ),
        'menu_name'         => __( 'Sizes', 'textdomain' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
		'sort' 				=> true, 
    );

    register_taxonomy( 'size', array( 'product' ), $args );

}
add_action( 'init', 'custom_size_taxonomy', 0 );
// Define the mapping between attribute sizes and taxonomy terms
function get_size_mapping() {
    return array(
    '38' => array('Prematur'),
'44' => array('Prematur'),
'50' => array('Nyfødt / 50'),
'56' => array('1 mnd / 56'),
'62' => array('3 mnd / 62'),
'68' => array('6 mnd / 68'),
'74' => array('9 mnd / 74'),
'80' => array('12 mnd / 80'),
'86' => array('18 mnd / 86'),
'92' => array('2 år / 92'),
'98' => array('3 år / 98'),
'104' => array('4 år / 104'),
'110' => array('5 år / 110'),
'116' => array('6 år / 116'),
'122' => array('7 år / 122'),
'128' => array('8 år / 128'),
'134' => array('9 år / 134'),
'140' => array('10 år / 140'),
'146' => array('11 år / 146'),
'152' => array('12 år / 152'),
'158' => array('13 år / 158'),
'164' => array('14 år / 164'),
'170' => array('15 år / 170'),
'176' => array('16 år / 176'),
'50-56' => array('Nyfødt / 50', '1 mnd / 56'),
'62-68' => array('3 mnd / 62', '6 mnd / 68'),
'74-80' => array('9 mnd / 74', '9 mnd / 74'),
'80-86' => array('12 mnd / 80', '18 mnd / 86'),
'86-92' => array('18 mnd / 86', '2 år / 92'),
'92-98' => array('2 år / 92', '3 år / 98'),
'98-104' => array('3 år / 98', '4 år / 104'),
'104-110' => array('4 år / 104', '5 år / 110'),
'110-116' => array('5 år / 110', '6 år / 116'),
'116-122' => array('6 år / 116', '7 år / 122'),
'122-128' => array('7 år / 122', '8 år / 128'),
'128-134' => array('8 år / 128', '9 år / 134'),
'134-140' => array('9 år / 134', '10 år / 140'),
'140-146' => array('10 år / 140', '11 år / 146'),
'146-152' => array('11 år / 146', '12 år / 152'),
'158-164' => array('13 år / 158', '14 år / 164'),
'164-170' => array('14 år / 164', '15 år / 170'),
'170-176' => array('15 år / 170', '16 år / 176'),
'60' => array('1 mnd / 56', '3 mnd / 62'),
'70' => array('6 mnd / 68', '9 mnd / 74'),
'90' => array('18 mnd / 86', '2 år / 92'),
'100' => array('3 år / 98', '4 år / 104'),
'120' => array('6 år / 116', '7 år / 122'),
'130' => array('8 år / 128', '9 år / 134'),
'150' => array('11 år / 146', '12 år / 152'),
'160' => array('13 år / 158', '14 år / 164'),
'170' => array('15 år / 170'),
'0-1 år' => array('6 mnd / 68', '9 mnd / 74', '12 mnd / 80'),
'1-2 år' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'2-4 år' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'2-5 år' => array('2 år / 92', '3 år / 98', '4 år / 104', '5 år / 110'),
'3-5 år' => array('3 år / 98', '4 år / 104', '5 år / 110'),
'4-6 år' => array('4 år / 104', '5 år / 110', '6 år / 116'),
'4-8 år' => array('4 år / 104', '5 år / 110', '6 år / 116', '7 år / 122', '8 år / 128'),
'5-6 år' => array('5 år / 110', '6 år / 116'),
'5-7 år' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'6-10 år' => array('6 år / 116', '7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140'),
'6-8 år' => array('6 år / 116', '7 år / 122', '8 år / 128'),
'8-10 år' => array('8 år / 128', '9 år / 134', '10 år / 140'),
'10-12 år' => array('10 år / 140', '11 år / 146', '12 år / 152'),
'11-13 år' => array('11 år / 146', '12 år / 152', '13 år / 158'),
'0-3 mnd' => array('Nyfødt / 50', '1 mnd / 56', '3 mnd / 62'),
'3-6 mnd' => array('3 mnd / 62', '6 mnd / 68'),
'6-12 mnd' => array('6 mnd / 68', '9 mnd / 74', '12 mnd / 80'),
'6-9 mnd' => array('6 mnd / 68', '9 mnd / 74'),
'9-12 mnd' => array('9 mnd / 74', '12 mnd / 80'),
'9-18 mnd' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'12-18 mnd' => array('12 mnd / 80', '18 mnd / 86'),
'18-24 mnd' => array('18 mnd / 86', '2 år / 92'),
'24-36 mnd' => array('2 år / 92', '3 år / 98'),
'0-3 år' => array('6 mnd / 68', '9 mnd / 74', '12 mnd / 80', '18 mnd / 86', '2 år / 92', '3 år / 98'),
'3-7 år' => array('3 år / 98', '4 år / 104', '5 år / 110', '6 år / 116', '7 år / 122'),
'7-12 år' => array('7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152'),
'6' => array('Voksen'),
'7' => array('Voksen'),
'8' => array('Voksen'),
'9' => array('Voksen'),
'20' => array('Sko 20'),
'21' => array('Sko 21'),
'22' => array('Sko 22'),
'23' => array('Sko 23'),
'24' => array('Sko 24'),
'25' => array('Sko 25'),
'26' => array('Sko 26'),
'27' => array('Sko 27'),
'28' => array('Sko 28'),
'29' => array('Sko 29'),
'30' => array('Sko 30'),
'31' => array('Sko 31'),
'32' => array('Sko 32'),
'33' => array('Sko 33'),
'34' => array('Sko 34'),
'35' => array('Sko 35'),
'36' => array('Sko 36'),
'37' => array('Sko 37'),
'38' => array('Sko 38'),
'39' => array('Sko 39'),
'13-15' => array('Nyfødt / 50', '1 mnd / 56', '3 mnd / 62'),
'14-16' => array('3 mnd / 62', '6 mnd / 68', '9 mnd / 74'),
'15-16' => array('3 mnd / 62', '6 mnd / 68', '9 mnd / 74'),
'15-18' => array('6 mnd / 68', '9 mnd / 74', '12 mnd / 80'),
'16-17' => array('9 mnd / 74', '12 mnd / 80'),
'16-18' => array('9 mnd / 74', '12 mnd / 80'),
'16-19' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'17-18' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'17-19' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'17-20' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'18-20' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'19-20' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'19-21' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'19-22' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92', '3 år / 98'),
'20-21' => array('18 mnd / 86', '2 år / 92'),
'20-22' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'20-23' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'21-22' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'21-23' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'21-24' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'22-23' => array('2 år / 92', '3 år / 98'),
'22-24' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'23-24' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'23-26' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'24-25' => array('3 år / 98', '4 år / 104'),
'24-26' => array('3 år / 98', '4 år / 104'),
'24-27' => array('3 år / 98', '4 år / 104', '5 år / 110'),
'25-26' => array('3 år / 98', '4 år / 104', '5 år / 110'),
'25-27' => array('3 år / 98', '4 år / 104', '5 år / 110'),
'25-28' => array('3 år / 98', '4 år / 104', '5 år / 110', '6 år / 116'),
'26-27' => array('3 år / 98', '4 år / 104', '5 år / 110'),
'27-28' => array('5 år / 110', '6 år / 116'),
'27-30' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'28-30' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'28-31' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'29-32' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'30-32' => array('5 år / 110', '6 år / 116', '7 år / 122'),
'31-33' => array('5 år / 110', '6 år / 116', '7 år / 122', '8 år / 128'),
'31-34' => array('5 år / 110', '6 år / 116', '7 år / 122', '8 år / 128', '9 år / 134'),
'32-36' => array('6 år / 116', '7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152'),
'33-35' => array('7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152'),
'33-36' => array('7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158'),
'33-37' => array('7 år / 122', '8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158'),
'34-36' => array('8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170'),
'35-36' => array('9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170'),
'35-38' => array('9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'35-39' => array('9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'36-39' => array('10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'37-38' => array('10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'37-39' => array('10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'37-40' => array('10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'38-43' => array('11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'39-40' => array('12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'40-41' => array('12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'40-42' => array('13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'42-43' => array('14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'42-44' => array('15 år / 170', '16 år / 176', 'Voksen'),
'43-44' => array('15 år / 170', '16 år / 176', 'Voksen'),
'43-45' => array('15 år / 170', '16 år / 176', 'Voksen'),
'34-39cm' => array('Prematur', 'Nyfødt / 50', '1 mnd / 56'),
'35-37cm' => array('Prematur', 'Nyfødt / 50', '1 mnd / 56'),
'36-38cm' => array('Nyfødt / 50', '1 mnd / 56'),
'37cm' => array('1 mnd / 56', '3 mnd / 62'),
'39cm' => array('1 mnd / 56', '3 mnd / 62'),
'39-41cm' => array('1 mnd / 56', '3 mnd / 62'),
'40-44cm' => array('1 mnd / 56', '3 mnd / 62', '6 mnd / 68'),
'40-42cm' => array('1 mnd / 56', '3 mnd / 62'),
'40-43cm' => array('1 mnd / 56', '3 mnd / 62', '6 mnd / 68'),
'41cm' => array('1 mnd / 56', '3 mnd / 62'),
'43-45cm' => array('3 mnd / 62', '6 mnd / 68'),
'43cm' => array('3 mnd / 62', '6 mnd / 68'),
'44-46cm' => array('3 mnd / 62', '6 mnd / 68', '9 mnd / 74'),
'44-47cm' => array('3 mnd / 62', '6 mnd / 68', '9 mnd / 74'),
'45-47cm' => array('3 mnd / 62', '6 mnd / 68', '9 mnd / 74'),
'45cm' => array('6 mnd / 68', '9 mnd / 74'),
'46cm' => array('6 mnd / 68', '9 mnd / 74'),
'46-47cm' => array('9 mnd / 74', '12 mnd / 80'),
'46-48cm' => array('9 mnd / 74', '12 mnd / 80'),
'47-49cm' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'47cm' => array('9 mnd / 74', '12 mnd / 80'),
'48cm' => array('9 mnd / 74', '12 mnd / 80'),
'48-49cm' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86'),
'48-50cm' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'48-51cm' => array('9 mnd / 74', '12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'50cm' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'50-51cm' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'50-52cm' => array('12 mnd / 80', '18 mnd / 86', '2 år / 92'),
'51cm' => array('18 mnd / 86', '2 år / 92'),
'51-52cm' => array('18 mnd / 86', '2 år / 92', '3 år / 98'),
'52-53cm' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'52cm' => array('2 år / 92', '3 år / 98', '4 år / 104'),
'53cm' => array('3 år / 98', '4 år / 104'),
'52-54cm' => array('2 år / 92', '3 år / 98', '4 år / 104', '5 år / 110'),
'54 cm' => array('4 år / 104', '5 år / 110', '6 år / 116', '7 år / 122'),
'54-56cm' => array('5 år / 110', '6 år / 116', '7 år / 122', '8 år / 128', '9 år / 134'),
'56cm' => array('8 år / 128', '9 år / 134', '10 år / 140', '11 år / 146', '12 år / 152'),
'56-58cm' => array('10 år / 140', '11 år / 146', '12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'58cm' => array('12 år / 152', '13 år / 158', '14 år / 164', '15 år / 170', '16 år / 176', 'Voksen'),
'L' => array('Voksen'),
'L/XL' => array('Voksen'),
'M' => array('Voksen'),
'M/L' => array('Voksen'),
'S' => array('Voksen'),
'S/M' => array('Voksen'),
'XL' => array('Voksen'),
'XS' => array('Voksen'),
'36' => array('Voksen'),
'38' => array('Voksen'),
'40' => array('Voksen'),
'42' => array('Voksen'),
'44' => array('Voksen'),
'2 år' => array('2 år / 92'),
'3 år' => array('3 år / 98'),
'4 år ' => array('4 år / 104'),
'5 år' => array('5 år / 110'),
'XS (5-6år)' => array('5 år / 110', '6 år / 116'),
'S (7-8 år)' => array('7 år / 122', '8 år / 128'),
'M (10 år)' => array('9 år / 134', '10 år / 140', '11 år / 146'),
'L (12 år)' => array('11 år / 146', '12 år / 152', '13 år / 158'),
'XL (14 år)' => array('13 år / 158', '14 år / 164'),
'Onesize' => array('Onesize'),
    );
}
// Function to update the size taxonomy based on stock status
function update_size_taxonomy_based_on_stock($post_id) {
    // Check if this is a product
    if (get_post_type($post_id) != 'product') {
        return;
    }

    $product = wc_get_product($post_id);

    // Get the size mapping
    $size_mapping = get_size_mapping();

    // Array to keep track of which taxonomy terms to assign
    $terms_to_assign = array();

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();

        foreach ($available_variations as $variation) {
            $variation_id = $variation['variation_id'];
            $variation_product = wc_get_product($variation_id);

            // Get storrelse attribute value
            $storrelse_value = $variation_product->get_attribute('pa_storrelse');

            // If the storrelse attribute is set and the product is in stock
            if ($storrelse_value && $variation_product->is_in_stock()) {
                // Map the storrelse attribute to the corresponding taxonomy term(s)
                if (isset($size_mapping[$storrelse_value])) {
                    // Add all terms that map to this storrelse
                    foreach ($size_mapping[$storrelse_value] as $term) {
                        $terms_to_assign[] = $term;
                    }
                }
            }
        }
    } else if ($product->is_type('simple')) {
        // For simple products, check if the storrelse attribute exists
        $storrelse_value = $product->get_attribute('pa_storrelse');

        if ($storrelse_value && $product->is_in_stock()) {
            if (isset($size_mapping[$storrelse_value])) {
                // Add all terms that map to this storrelse
                foreach ($size_mapping[$storrelse_value] as $term) {
                    $terms_to_assign[] = $term;
                }
            }
        }
    }

    // Assign the mapped taxonomy terms, ensuring no duplicates
    if (!empty($terms_to_assign)) {
        $terms_to_assign = array_unique($terms_to_assign);
        wp_set_object_terms($post_id, $terms_to_assign, 'size');
    } else {
        // If no terms are left (e.g., all out of stock), remove the size taxonomy
        wp_set_object_terms($post_id, array(), 'size');
    }
}

// Hook to update taxonomy when a product is saved or updated
add_action('save_post', 'update_size_taxonomy_based_on_stock');

// Hook to update taxonomy when stock is reduced after an order
add_action('woocommerce_reduce_order_stock', 'update_size_taxonomy_after_purchase');

// Hook to update taxonomy when stock changes manually, via import, or programmatically
add_action('woocommerce_update_product_stock', 'update_size_taxonomy_after_stock_change', 10, 2);
add_action('woocommerce_product_set_stock', 'update_size_taxonomy_after_stock_change', 10, 1);

// Hook to update taxonomy after WP All Import imports products
add_action('pmxi_saved_post', 'update_size_taxonomy_after_import', 10, 3);

// Function to update taxonomy after stock changes
function update_size_taxonomy_after_stock_change($product_id, $new_stock = null) {
    update_size_taxonomy_based_on_stock($product_id);
}

// Function to update taxonomy after stock is reduced (e.g., when an order is placed)
function update_size_taxonomy_after_purchase($order) {
    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
        update_size_taxonomy_based_on_stock($product_id);
    }
}

// Function to update taxonomy after WP All Import import
function update_size_taxonomy_after_import($post_id, $xml_node, $is_update) {
    // Check if this is a product post
    if (get_post_type($post_id) == 'product') {
        update_size_taxonomy_based_on_stock($post_id);
    }
}


function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );

// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
    return __( 'Legg i kurv', 'woocommerce' ); 
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Legg i kurv', 'woocommerce' );
}
// Add Size Chart Shortcode before Add to Cart Button
function add_size_chart_before_add_to_cart() {
    echo do_shortcode('[scfw_product_size_chart]');
}
add_action('woocommerce_before_add_to_cart_button', 'add_size_chart_before_add_to_cart');

//wishlist count
//
if ( defined( 'YITH_WCWL' ) && ! function_exists( 'yith_wcwl_get_items_count' ) ) {
  function yith_wcwl_get_items_count() {
    ob_start();
    ?>
      <a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
        <span class="yith-wcwl-items-count">
          <i class="yith-wcwl-icon fa fa-heart-o"></i>
		  <span class="wish-count"><?php echo esc_html( yith_wcwl_count_all_products() ); ?></span>
        </span>
      </a>
    <?php
    return ob_get_clean();
  }

  add_shortcode( 'yith_wcwl_items_count', 'yith_wcwl_get_items_count' );
}

if ( defined( 'YITH_WCWL' ) && ! function_exists( 'yith_wcwl_ajax_update_count' ) ) {
  function yith_wcwl_ajax_update_count() {
    wp_send_json( array(
      'count' => yith_wcwl_count_all_products()
    ) );
  }

  add_action( 'wp_ajax_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count' );
  add_action( 'wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count' );
}

if ( defined( 'YITH_WCWL' ) && ! function_exists( 'yith_wcwl_enqueue_custom_script' ) ) {
  function yith_wcwl_enqueue_custom_script() {
    wp_add_inline_script(
      'jquery-yith-wcwl',
      "
        jQuery( function( $ ) {
          $( document ).on( 'added_to_wishlist removed_from_wishlist', function() {
            $.get( yith_wcwl_l10n.ajax_url, {
              action: 'yith_wcwl_update_wishlist_count'
            }, function( data ) {
              $('.yith-wcwl-items-count').children('.wish-count').html( data.count );
            } );
          } );
        } );
      "
    );
  }

  add_action( 'wp_enqueue_scripts', 'yith_wcwl_enqueue_custom_script', 20 );
}
// Add custom tab to My Account page after Orders
function custom_account_wishlist_tab( $items ) {
    // Insert 'Wishlist' after 'Orders'
    $new_items = array();

    foreach ( $items as $key => $item ) {
        $new_items[ $key ] = $item;

        // Insert wishlist tab after orders
        if ( 'orders' === $key ) {
            $new_items['wishlist'] = __( 'Wishlist', 'your-text-domain' );
        }
    }

    return $new_items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_account_wishlist_tab' );

function custom_account_wishlist_content() {
    echo do_shortcode('[yith_wcwl_wishlist]');  // YITH Wishlist shortcode
}

add_action( 'woocommerce_account_wishlist_endpoint', 'custom_account_wishlist_content' );

// Register the custom endpoint for the wishlist
function custom_add_wishlist_endpoint() {
    add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'custom_add_wishlist_endpoint' );

// Flush rewrite rules (do this once after you add the endpoint)
function custom_flush_rewrite_rules() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'custom_flush_rewrite_rules' );

// Shortcode to generate alphabet navigation menu
function alphabet_navigation_shortcode() {
    $alphabet = range('A', 'Z'); // Array of letters from A to Z
    $output = '<div class="alphabet-navigation">';
    
    foreach ( $alphabet as $letter ) {
        $output .= '<a href="#' . strtolower($letter) . '" class="alphabet-letter">' . $letter . '</a> ';
    }
    
    $output .= '</div>';
    return $output;
}
add_shortcode('alphabet_navigation', 'alphabet_navigation_shortcode');

// Enqueue JavaScript for smooth scrolling
function alphabet_navigation_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.alphabet-navigation a');
        links.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const target = link.getAttribute('href').substring(1); // Get the letter
                const targetDiv = document.querySelector('[data-heading="' + target + '"]'); // Find the div with matching data-heading

                if (targetDiv) {
                    window.scrollTo({
                        top: targetDiv.offsetTop - 50, // Adjust the offset as needed (e.g., for a fixed header)
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'alphabet_navigation_script');
add_filter('woocommerce_account_menu_items', 'remove_account_links', 99);

function remove_account_links($items) {
    unset($items['dashboard']); // Hides the Control Panel
    unset($items['downloads']); // Hides the Downloads
    return $items;
}

 
add_action( 'woocommerce_product_bulk_edit_start', 'bbloomer_bulk_edit_remove_product_category' );
 
function bbloomer_bulk_edit_remove_product_category() {
   ?>    
   <div class="inline-edit-group">
      <label class="alignleft">
         <span class="title">Delete Cat</span>
         <span class="input-text-wrap">
            <?php wc_product_dropdown_categories( [ 'class' => 'remove_product_cat', 'name' => 'remove_product_cat', 'show_option_none' => 'Select product category to be removed', 'value_field' => 'term_id' ] ); ?>
         </span>
      </label>
   </div>         
   <?php
}
 
add_action( 'woocommerce_product_bulk_edit_save', 'bbloomer_bulk_edit_remove_product_category_save', 9999 );
  
function bbloomer_bulk_edit_remove_product_category_save( $product ) {
   $post_id = $product->get_id();    
   if ( isset( $_REQUEST['remove_product_cat'] ) ) {
      $cat_to_remove = $_REQUEST['remove_product_cat'];
      $categories = $product->get_category_ids();
      if ( ! in_array( $cat_to_remove, $categories ) ) return;
      if ( ( $key = array_search( $cat_to_remove, $categories ) ) !== false ) {
         unset( $categories[$key] );
      }
      $product->set_category_ids( $categories );
      $product->save();
   }
}

// Function to append sales percentage to thumbnails section
function add_sales_percentage_to_thumbnails( $content ) {
    // Check if this is the woosq-popup
    if ( strpos( $content, 'class="thumbnails"' ) !== false ) {
        ob_start();
        softexpert_show_sale_percentage(); // Call your existing function
        $percentage_content = ob_get_clean();
        
        // Append the sale percentage after the thumbnails section
        $content = preg_replace(
            '/(<div class="thumbnails">)/',
            '$1' . $percentage_content,
            $content,
            1
        );
    }
    return $content;
}
add_action('init', 'modify_yith_product_brand_rewrite_rules', 20);
function modify_yith_product_brand_rewrite_rules() {
    // Get the existing post type registration arguments
    $post_type = 'yith_product_brand';
    $post_type_object = get_post_type_object($post_type);

    if ($post_type_object) {
        // Modify the rewrite rules to remove 'with_front'
        $post_type_object->rewrite['with_front'] = false;

        // Re-register the post type with updated rules
        register_post_type($post_type, (array) $post_type_object);
    }
}
// Enqueue custom JavaScript for modifying WooCommerce breadcrumbs
function custom_breadcrumb_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Select the breadcrumb element
            var breadcrumb = $('.woocommerce-breadcrumb');

            // Remove the 'Shop' breadcrumb item if it exists
            breadcrumb.contents().filter(function() {
                return this.nodeType === 3 && $.trim(this.nodeValue) === 'Shop'; // Node type 3 is a text node
            }).each(function() {
                $(this).remove();
            });

            // Find the text 'Merker' and replace it with a clickable link
            breadcrumb.contents().filter(function() {
                return this.nodeType === 3 && $.trim(this.nodeValue) === 'Merker';
            }).each(function() {
                var merkerLink = $('<a>', {
                    text: 'Merker',
                    href: '/merker/',
                    class: 'woocommerce-breadcrumb-link' // Optional: Add a class if needed
                });

                // Replace the text node with the new anchor element
                $(this).replaceWith(merkerLink);
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_breadcrumb_js');
// Step 1: Remove WooCommerce notices on Shop, Checkout, and YITH Brand pages only
function conditional_disable_woocommerce_notices() {
    if ((is_shop() || is_product_category() || is_product_tag() || is_checkout() || is_tax('yith_product_brand')) && function_exists('remove_action')) {
        remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_cart_is_empty', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_account_content', 'woocommerce_output_all_notices', 10);
    }
}
add_action('wp', 'conditional_disable_woocommerce_notices');

// Step 2: Create a shortcode to display WooCommerce notifications with the default wrapper on Shop, Checkout, and YITH Brand pages
function custom_woocommerce_notifications_shortcode() {
    // Check if we are on the shop archive, checkout page, or YITH brand page
    if ((is_shop() || is_product_category() || is_product_tag() || is_checkout() || is_tax('yith_product_brand')) && function_exists('wc_print_notices')) {
        ob_start();
        echo '<div class="woocommerce-notices-wrapper">'; // Add wrapper to maintain default styling
        wc_print_notices();
        echo '</div>';
        return ob_get_clean();
    }
    return ''; // Return empty if shortcode is used elsewhere
}
add_shortcode('woocommerce_notifications', 'custom_woocommerce_notifications_shortcode');
add_filter( 'wc_price_args', 'custom_wc_remove_decimals' );
function custom_wc_remove_decimals( $args ) {
    $args['decimals'] = 0;
    return $args;
}

add_filter( 'aco_shipping_icon', 'aco_custom_shipping_icon', 10, 3 ); 
function aco_custom_shipping_icon( $img_url, $carrier, $shipping_rate ) { 
    if ( $carrier === "postnord" ) {
        $img_url = "https://app.kroconnect.com/images/integ-woocommerce.svg"; 
    }
    return $img_url; 
}
add_filter('wpseo_canonical', 'remove_trailing_slash_from_canonical');
function remove_trailing_slash_from_canonical($canonical) {
    // Check if the canonical URL ends with a trailing slash and remove it
    if (substr($canonical, -1) === '/') {
        $canonical = rtrim($canonical, '/');
    }
    return $canonical;
}

// Add the custom canonical URL filter
add_filter('wpseo_canonical', 'custom_canonical_with_query_pagination');

function custom_canonical_with_query_pagination($canonical) {
    if (isset($_GET['_page'])) {
        $page_number = intval($_GET['_page']);
        $canonical = add_query_arg('_page', $page_number, $canonical);
    }
    return $canonical;
}

// Add custom rel="next" and rel="prev" links
add_action('wp_head', 'custom_rel_next_prev_links', 1);

function custom_rel_next_prev_links() {
    if (isset($_GET['_page'])) {
        global $wp_query;

        $current_page = intval($_GET['_page']);
        $base_url = remove_query_arg('_page');
        $total_pages = $wp_query->max_num_pages; // Total pages available for the query

        // Generate the "next" link only if there's a next page
        if ($current_page < $total_pages) {
            $next_page = $current_page + 1;
            echo '<link rel="next" href="' . esc_url(add_query_arg('_page', $next_page, $base_url)) . '" />' . "\n";
        }

        // Generate the "prev" link only if there's a previous page
        if ($current_page > 1) {
            $prev_page = $current_page - 1;
            echo '<link rel="prev" href="' . esc_url(add_query_arg('_page', $prev_page, $base_url)) . '" />' . "\n";
        }
    }
}
// Disable Yoast SEO next/prev rel links
add_filter('wpseo_next_rel_link', '__return_false');
add_filter('wpseo_prev_rel_link', '__return_false');

// Remove default WooCommerce pagination
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );


add_action('avarda_order_data_received', 'process_avarda_order_data', 10, 2);

function process_avarda_order_data($order_id, $avarda_data) {
    // Debug Avarda data
    error_log(print_r($avarda_data, true));

    // Extract and store MerchantReference
    if (isset($avarda_data['Modules'][0]['ExternalShippingSession']['Modules'])) {
        $modules_json = $avarda_data['Modules'][0]['ExternalShippingSession']['Modules'];
        $modules = json_decode($modules_json, true);

        foreach ($modules['options'] as $option) {
            if (!empty($option['SelectedPickupPoint']) && $option['SelectedPickupPoint']) {
                $merchant_reference = $option['MerchantReference'];

                // Store MerchantReference in WooCommerce order meta
                update_post_meta($order_id, '_merchant_reference', $merchant_reference);
                break;
            }
        }
    }
}
/**
 * Regenerate metadata for old WooCommerce orders.
 */
function regenerate_order_metadata() {
    // Fetch all completed orders (modify status or limit as needed).
    $orders = wc_get_orders([
        'status' => 'completed', // Change to desired statuses if needed.
        'limit'  => -1,          // Get all orders, adjust if needed.
    ]);

    foreach ($orders as $order) {
        $order_id = $order->get_id();

        // Fetch JSON data associated with this order (example key, adjust as per your setup).
        $json_data = get_post_meta($order_id, '_avarda_json_data', true);

        if ($json_data) {
            $data = json_decode($json_data, true);

            // Check if the JSON contains the required MerchantReference data.
            if (isset($data['Modules'][0]['ExternalShippingSession']['Modules'])) {
                $modules_json = $data['Modules'][0]['ExternalShippingSession']['Modules'];
                $modules = json_decode($modules_json, true);

                foreach ($modules['options'] as $option) {
                    if (!empty($option['SelectedPickupPoint']) && $option['SelectedPickupPoint']) {
                        $merchant_reference = $option['MerchantReference'];

                        // Store MerchantReference as order metadata.
                        update_post_meta($order_id, '_merchant_reference', $merchant_reference);

                        // Log success (optional, for debugging purposes).
                        error_log("Order ID: $order_id - Merchant Reference: $merchant_reference");

                        // Break after finding the first selected pickup point.
                        break;
                    }
                }
            }
        }
    }

    // Log completion (optional).
    error_log('Metadata regeneration complete!');
}
// Hook to admin_init temporarily to trigger the function.
add_action('admin_init', 'regenerate_order_metadata');

add_filter( 'woocommerce_get_breadcrumb', 'bbloomer_single_product_edit_prod_name_breadcrumbs', 9999, 2 );
 
function bbloomer_single_product_edit_prod_name_breadcrumbs( $crumbs, $breadcrumb ) {
    
   if ( is_product() ) {
      global $product;
      $index = count( $crumbs ) - 1; // product name is always last item
      $value = $crumbs[$index];
      $crumbs[$index][0] = null;
   }
    
   return $crumbs;
}
function modify_price_history_shortcode() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".wc-price-history-shortcode").forEach(function (el) {
            el.textContent = el.textContent.replace(/,\d+$/, ",-");
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'modify_price_history_shortcode');

function add_sale_percentage_before_product_title() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        function insertSalePercentage() {
            let popup = document.getElementById("woosq-popup");
            if (popup) {
                let title = popup.querySelector("h1.product_title.entry-title");
                if (title && !popup.querySelector(".woosq-sale-percentage")) {
                    let saleDiv = document.createElement("div");
                    saleDiv.className = "woosq-sale-percentage";
                    saleDiv.innerHTML = `<?php echo do_shortcode('[show_sale_percentage]'); ?>`;
                    title.insertAdjacentElement("beforebegin", saleDiv); // 👈 Now added before H1
                }
            }
        }

        // Observe changes in the popup for dynamic loading
        let observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === "childList") {
                    insertSalePercentage();
                }
            });
        });

        let popupContainer = document.getElementById("woosq-popup");
        if (popupContainer) {
            observer.observe(popupContainer, { childList: true, subtree: true });
        }

        // Run once in case the popup is already open
        insertSalePercentage();
    });
    </script>
    <?php
}
add_action('wp_footer', 'add_sale_percentage_before_product_title');
function display_material_description() {
    global $product;

    if (!$product || !is_a($product, 'WC_Product')) {
        return ''; // Prevent errors if not on a product page
    }

    $material_description = get_post_meta($product->get_id(), 'material_description', true);

    if (!empty($material_description)) {
        return '<div class="product-material-description">' . esc_html($material_description) . '</div>';
    }

    return ''; // Return empty if no material description is set
}
add_shortcode('show_material_description', 'display_material_description');

// Add a new column to the WooCommerce product admin list
add_filter('manage_edit-product_columns', 'custom_product_quantity_column');
function custom_product_quantity_column($columns) {
    // Add 'Total Stock' column after the first three columns
    $columns = array_slice($columns, 0, 5, true) + array('total_stock' => __('Quantity', 'woocommerce')) + array_slice($columns, 5, count($columns) - 3, true);
    return $columns;
}

// Make the column sortable
add_filter('manage_edit-product_sortable_columns', 'custom_product_quantity_column_sortable');
function custom_product_quantity_column_sortable($columns) {
    $columns['total_stock'] = 'total_stock'; // Sortable column key
    return $columns;
}

// Populate the custom column with total stock quantity and update meta for sorting
add_action('manage_product_posts_custom_column', 'custom_product_quantity_column_content', 10, 2);
function custom_product_quantity_column_content($column, $post_id) {
    if ($column === 'total_stock') {
        $product = wc_get_product($post_id);
        $total_stock = 0;

        if ($product) {
            if ($product->is_type('variable')) {
                $variations = $product->get_children();
                foreach ($variations as $variation_id) {
                    $variation = wc_get_product($variation_id);
                    if ($variation) {
                        $stock = $variation->get_stock_quantity();
                        if ($stock !== null) {
                            $total_stock += $stock;
                        }
                    }
                }
            } else {
                $total_stock = $product->get_stock_quantity();
            }

            // Store this value for sorting
            update_post_meta($post_id, '_total_stock', $total_stock);

            echo ($total_stock !== null) ? $total_stock : __('N/A', 'woocommerce');
        }
    }
}

// Modify the query to sort by total_stock meta
add_action('pre_get_posts', 'custom_product_sort_by_total_stock');

function custom_product_sort_by_total_stock($query) {
    // Ensure we are in the admin area and sorting products
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'product') {
        // Check if we're sorting by the 'total_stock' column
        if ('total_stock' === $query->get('orderby')) {
            // Set the query to sort by the custom meta field '_total_stock'
            $query->set('meta_key', '_total_stock');
            $query->set('orderby', 'meta_value_num'); // Ensure it sorts numerically
        }
    }
}


add_action( 'woocommerce_product_bulk_edit_start', 'bbloomer_custom_field_bulk_edit_input' );
          
function bbloomer_custom_field_bulk_edit_input() {
    ?>
    <div class="inline-edit-group">
        <label class="alignleft">
            <span class="title"><?php _e( 'Visible in Feed', 'woocommerce' ); ?></span>
            <span class="input-text-wrap">
                <select name="wpfoof-exclude-product" class="text">
                    <option value="no_change" selected><?php _e( 'No Change', 'woocommerce' ); ?></option>
                    <option value="0"><?php _e( 'Yes', 'woocommerce' ); ?></option>
                    <option value="1"><?php _e( 'No', 'woocommerce' ); ?></option>
                </select>
            </span>
        </label>
    </div>
    <?php
}

add_action( 'woocommerce_product_bulk_edit_save', 'bbloomer_custom_field_bulk_edit_save' );

function bbloomer_custom_field_bulk_edit_save( $product ) {
    $post_id = $product->get_id();    
    if ( isset( $_REQUEST['wpfoof-exclude-product'] ) ) {
        $exclude_product = $_REQUEST['wpfoof-exclude-product'];
        
        // If "No Change" is selected, don't update the custom field
        if ( $exclude_product !== 'no_change' ) {
            update_post_meta( $post_id, 'wpfoof-exclude-product', wc_clean( $exclude_product ) );
        }
    }
}

// Add a dropdown filter for Feed status
add_action( 'restrict_manage_posts', 'wpwoof_filter_by_feed_status' );
function wpwoof_filter_by_feed_status() {
	global $typenow;
	if ( 'product' === $typenow ) {
		$selected = isset( $_GET['feed_status'] ) ? $_GET['feed_status'] : '';
		echo '<select name="feed_status" id="feed_status" class="postform">';
		echo '<option value="">Feed Status</option>';
		echo '<option value="Yes"' . selected( $selected, 'Yes', false ) . '>Yes</option>';
		echo '<option value="No"' . selected( $selected, 'No', false ) . '>No</option>';
		echo '</select>';
	}
}

// Modify the query to filter products by feed status
add_filter( 'pre_get_posts', 'wpwoof_filter_by_feed_status_query' );
function wpwoof_filter_by_feed_status_query( $query ) {
	if ( is_admin() && $query->is_main_query() && isset( $_GET['feed_status'] ) && $_GET['feed_status'] !== '' ) {
		$feed_status = $_GET['feed_status'];

		// Initialize the meta_query array
		$meta_query = (array) $query->get( 'meta_query' );

		// Modify the meta_query based on feed status
		if ( 'Yes' === $feed_status ) {
			// Filter for products where wpfoof-exclude-product is NOT '1' (included in the feed)
			$meta_query[] = array(
				'key'     => 'wpfoof-exclude-product',
				'value'   => '1',
				'compare' => '!=' // Products that are NOT excluded from the feed (value is NOT '1')
			);
		} elseif ( 'No' === $feed_status ) {
			// Filter for products where wpfoof-exclude-product is '1' (excluded from the feed)
			$meta_query[] = array(
				'key'     => 'wpfoof-exclude-product',
				'value'   => '1',
				'compare' => '=' // Products that are excluded from the feed (value is '1')
			);
		}

		// Set the meta_query back to the query
		$query->set( 'meta_query', $meta_query );
	}
}
//secound image
add_action('woocommerce_before_shop_loop_item_title', 'wcsuccess_display_secondary_product_image', 10);

function wcsuccess_display_secondary_product_image() {
    global $product;

    $attachment_ids = $product->get_gallery_image_ids();
    if (!empty($attachment_ids)) {
        // Use the first gallery image as secondary
        $secondary_image_id = $attachment_ids[0];
    } else {
        // If no gallery image, use the main product image as fallback
        $secondary_image_id = $product->get_image_id();
    }

    if ($secondary_image_id) {
        $secondary_image_src = wp_get_attachment_image_src($secondary_image_id, 'woocommerce_thumbnail')[0];
        echo '<img src="' . esc_url($secondary_image_src) . '" class="secondary-image" alt="Secondary product image" />';
    }
}

//Checkout modification
//

 
add_filter( 'woocommerce_get_price_html', 'bbloomer_alter_price_display', 9999, 2 );
 
function bbloomer_alter_price_display( $price_html, $product ) {
   if ( '' === $product->get_price() ) return $price_html;
   if ( get_post_meta( $product->get_id(), '_pc_discount', true ) && get_post_meta( $product->get_id(), '_pc_discount', true ) > 0 ) {
      if ( is_admin() ) {
         return $price_html . ' <code style="white-space: nowrap">-' . get_post_meta( $product->get_id(), '_pc_discount', true ) . '% on top</code>';
      }
      $orig_price = wc_get_price_to_display( $product );
      $price_html = wc_format_sale_price( $orig_price, $orig_price * ( 100 - get_post_meta( $product->get_id(), '_pc_discount', true ) ) / 100 );
   }
   return $price_html;
}
 
add_action( 'woocommerce_before_calculate_totals', 'bbloomer_alter_price_cart', 9999 );
 
function bbloomer_alter_price_cart( $cart ) {
   if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
   if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
   foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
      $product = $cart_item['data'];
      if ( get_post_meta( $product->get_id(), '_pc_discount', true ) && get_post_meta( $product->get_id(), '_pc_discount', true ) > 0 ) {
         $price = $product->get_price();
         $cart_item['data']->set_price( $price * ( 100 - get_post_meta( $product->get_id(), '_pc_discount', true ) ) / 100 );
      }
   }
}
 
add_filter( 'woocommerce_product_is_on_sale', 'bbloomer_is_onsale_if_percentage_discount', 9999, 2 );
 
function bbloomer_is_onsale_if_percentage_discount( $on_sale, $product ) {
   if ( is_admin() ) return $on_sale;
   if ( get_post_meta( $product->get_id(), '_pc_discount', true ) && get_post_meta( $product->get_id(), '_pc_discount', true ) > 0 ) {
      $on_sale = true;
   }
   return $on_sale;
}
  

//checkout modification
//

add_filter( 'woocommerce_cart_item_name', 'bbloomer_product_image_review_order_checkout', 9999, 3 );
  
function bbloomer_product_image_review_order_checkout( $name, $cart_item, $cart_item_key ) {
    if ( ! is_checkout() ) return $name;
    $product = $cart_item['data'];
    $thumbnail = $product->get_image( array( '100', '100' ), array( 'class' => 'alignleft' ) );
    return $thumbnail . $name;
}

function move_checkout_suggestions_inline_script() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var suggestions = document.getElementById("checkout-suggestions");
            var iframe = document.getElementById("aco-iframe");

            if (suggestions && iframe) {
                iframe.parentNode.insertBefore(suggestions, iframe);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'move_checkout_suggestions_inline_script');

 
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );

function custom_admin_bar_failed_orders_count($wp_admin_bar) {
    if (!is_admin() || !current_user_can('manage_woocommerce')) {
        return;
    }

    // Get the count of failed orders
    $failed_orders = wc_get_orders([
        'status' => 'failed',
        'return' => 'ids',
    ]);
    $count = count($failed_orders);

    // Add custom-styled number badge
    $title = 'Failed Orders <span class="custom-failed-orders-badge">' . $count . '</span>';

    // Add menu item to admin bar
    $wp_admin_bar->add_node([
        'id'    => 'failed_orders_count',
        'title' => $title,
        'href'  => admin_url('edit.php?post_type=shop_order&post_status=wc-failed'), // Link to failed orders
        'meta'  => ['class' => 'failed-orders-count'],
    ]);
}
add_action('admin_bar_menu', 'custom_admin_bar_failed_orders_count', 100);

// Inject custom CSS for the badge
function custom_failed_orders_admin_css() {
    echo '<style>
        .custom-failed-orders-badge {
            background-color: #d63638;
            border-radius: 9px;
            color: #fff;
            display: inline-block;
            padding: 1px 7px 1px 6px !important;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.5;
        }
    </style>';
}
add_action('admin_head', 'custom_failed_orders_admin_css');
