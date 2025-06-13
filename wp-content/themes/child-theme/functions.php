<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */
function show_current_user_name() {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        // return 'Hei, ' . esc_html( $current_user->display_name );
        return 'Hei!';
    } else {
        return 'Hei!';
    }
}
add_shortcode('show_username', 'show_current_user_name');


//added by dany
function display_current_category_subcategories() {
    if (!is_tax('product_cat')) return '';

    $current_category = get_queried_object(); // Get current product category
    if (!$current_category) return '';

    $args = array(
        'taxonomy'   => 'product_cat',
        'parent'     => $current_category->term_id, // Only get child categories
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => false
    );

    $subcategories = get_terms($args);
    if (empty($subcategories)) return '';

    $total_categories = count($subcategories);
    $output = '<div class="category-container">';
    $output .= '<div class="dynamic-subcategories">';

    foreach ($subcategories as $index => $subcategory) {
        $subcategory_link = get_term_link($subcategory);
        // Add classes based on index for mobile and desktop
        $hidden_mobile = ($index >= 4) ? 'hidden-mobile' : ''; // Hide after 4th item on mobile
        $hidden_desktop = ($index >= 8) ? 'hidden-desktop' : ''; // Hide after 8th item on desktop
        $output .= '<div class="category-item ' . $hidden_mobile . ' ' . $hidden_desktop . '">';
        $output .= '<a href="' . esc_url($subcategory_link) . '">' . esc_html($subcategory->name) . '</a>';
        $output .= '</div>';
    }

    $output .= '</div>';

//     if ($total_categories > 4) { 
        $output .= '<div class="show-more" data-total-categories="' . $total_categories . '">';
        $output .= '<a href="#" class="show-more-link">Vis alle ' . $total_categories . ' kategorier <span class="arrow">▼</span></a>';
        $output .= '</div>';
//     }

    $output .= '</div>';
    return $output;
}

add_shortcode('current_subcategories', 'display_current_category_subcategories');
// end here (by dany)

use Elementor\WPNotificationsPackage\V110\Notifications as ThemeNotifications;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.3.0' );

if ( ! isset( $content_width ) ) {
    $content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
    /**
     * Set up theme support.
     *
     * @return void
     */
    function hello_elementor_setup() {
        if ( is_admin() ) {
            hello_maybe_update_theme_version_in_db();
        }

        if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
            register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
            register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
        }

        if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
            add_post_type_support( 'page', 'excerpt' );
        }

        if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
            add_theme_support( 'post-thumbnails' );
            add_theme_support( 'automatic-feed-links' );
            add_theme_support( 'title-tag' );
            add_theme_support(
                'html5',
                [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                    'script',
                    'style',
                ]
            );
            add_theme_support(
                'custom-logo',
                [
                    'height'      => 100,
                    'width'       => 350,
                    'flex-height' => true,
                    'flex-width'  => true,
                ]
            );
            add_theme_support( 'align-wide' );
            add_theme_support( 'responsive-embeds' );

            /*
             * Editor Styles
             */
            add_theme_support( 'editor-styles' );
            add_editor_style( 'editor-styles.css' );

            /*
             * WooCommerce.
             */
            if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
                // WooCommerce in general.
                add_theme_support( 'woocommerce' );
                // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
                // zoom.
                add_theme_support( 'wc-product-gallery-zoom' );
                // lightbox.
                add_theme_support( 'wc-product-gallery-lightbox' );
                // swipe.
                add_theme_support( 'wc-product-gallery-slider' );
            }
        }
    }
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );


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





//added by Zeeshan

add_action('wp_enqueue_scripts', 'custom_zindex_script');
function custom_zindex_script() {
    wp_enqueue_script(
        'custom-zindex',
        get_theme_file_uri('/cart.js'),
        array(),
        '1.0.0',
        true
    );
}

//save price sum on cart 

//remove reset pass alert 
add_action('wp_head', 'add_lost_password_css');
function add_lost_password_css() {
    if (is_account_page() && (isset($_GET['action']) && $_GET['action'] === 'lostpassword') || strpos($_SERVER['REQUEST_URI'], 'lost-password') !== false) {
        echo '<!-- Debug: Lost Password CSS is running -->';
        ?>
        <style type="text/css">
            .woocommerce-message {
                display: none !important;
            }
        </style>
        <?php
    }
}
//remove breadcrumb when there is one item 
add_filter('woocommerce_get_breadcrumb', 'hide_single_wc_breadcrumb_early', 10, 2);
function hide_single_wc_breadcrumb_early($crumbs, $breadcrumb_obj) {
    $valid_crumbs = array_filter($crumbs, function($crumb) {
        return !empty(trim($crumb[0]));
    });
    
//     if (current_user_can('manage_options')) {
//         echo '<!-- Breadcrumb Count: ' . count($valid_crumbs) . ' -->';
//         echo '<!-- Crumbs: ' . print_r(array_map(function($crumb) { return $crumb[0]; }, $valid_crumbs), true) . ' -->';
//     }
    
    if (count($valid_crumbs) <= 1) {
        return [];
    }
    
    return $crumbs;
}
//replace quick view text with icon 

add_filter('woosq_button_html', 'custom_quick_view_button_icon', 99, 2);
function custom_quick_view_button_icon($output, $product_id) {
    $icon = '<i class="fa-solid fa-basket-shopping"></i>'; 
    $button_class = 'woosq-btn woosq-btn-' . esc_attr($product_id) . ' ' . esc_attr(get_option('woosq_button_class'));
    $output = '<button class="' . $button_class . '" data-id="' . esc_attr($product_id) . '" data-effect="' . esc_attr(get_option('woosq_effect', 'mfp-3d-unfold')) . '">' . $icon . '</button>';
    return $output;
}

// add wishlist icon next to add to cart
add_action('woocommerce_after_add_to_cart_button', 'add_yith_wishlist_button', 10);
function add_yith_wishlist_button() {
    if (class_exists('YITH_WCWL')) {
        echo do_shortcode('[yith_wcwl_add_to_wishlist]');
    }
}
//activate add to cart button wout selecting variant
add_filter('woocommerce_variable_add_to_cart_enabled', '__return_true');

add_action('wp_footer', 'custom_add_to_cart_script', 20); // Higher priority to run after WooCommerce scripts
function custom_add_to_cart_script() {
    if (is_product()) {
        ?>
        <script>
        (function($) {
            function enableAddToCartButton() {
                var $variationForm = $('form.variations_form');
                if ($variationForm.length) {
                    $variationForm.find('.woocommerce-variation-add-to-cart').removeClass('woocommerce-variation-add-to-cart-disabled');
                    $variationForm.find('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed wc-variation-is-unavailable');
                }
            }

            $(document).ready(function() {
                setTimeout(enableAddToCartButton, 100); 

                enableAddToCartButton();
                $('form.variations_form').on('woocommerce_variation_select_change', function() {
                    enableAddToCartButton();
                });

                $('form.variations_form').on('woocommerce_variation_has_changed', function() {
                    enableAddToCartButton();
                });

                $('.single_add_to_cart_button').on('click', function(e) {
                    if ($('form.variations_form').length) {
                        var variationSelected = $('input.variation_id').val();
                        if (!variationSelected || variationSelected === '0') {
                            e.preventDefault();
                            alert('Foreta produktvalg før du legger dette produktet i handlekurven din.');
                            return false;
                        }
                    }
                });
            });
        })(jQuery);
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_add_to_cart_script', 20); 


//show all products on campaign page
add_action('init', function() {
    add_rewrite_rule(
        'campaign/([^/]+)/?$',
        'index.php?pagename=campaign-category-template&category_slug=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function($vars) {
    $vars[] = 'category_slug';
    return $vars;
});
add_action('wp_footer', function() {
    $category_slug = get_query_var('category_slug');
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Log the category slug
            console.log('Category Slug: <?php echo esc_js($category_slug); ?>');
			

            // Remove active class from all buttons
            document.querySelectorAll('.campaign-cat-btn').forEach(function(button) {
                button.classList.remove('active');
            });

            // Add active class to the matching button
            const activeButton = document.getElementById('button-<?php echo esc_js($category_slug); ?>');
            if (activeButton) {
                activeButton.classList.add('active');
                console.log('Active button found:', activeButton);
            } else {
                console.log('No active button found for ID: button-<?php echo esc_js($category_slug); ?>');
            }
        });
    </script>
    <?php
});
// add_filter('yith_wcan_filter_url', function($url) {
//     $category_slug = get_query_var('category_slug');
//     if ($category_slug) {
//         // Ensure URL stays on /campaign/ and includes category filter
//         $url = home_url("/campaign/{$category_slug}/");
//         $params = array(
//             'yith_wcan' => '1',
//             'product_cat' => $category_slug,
//             'query_type_kategori' => 'and',
//         );
//         // Merge existing query params (e.g., min_price) with category filter
//         $url = add_query_arg(array_merge($_GET, $params), $url);
//     }
//     return $url;
// });
// add_action('template_redirect', function() {
//     $category_slug = get_query_var('category_slug');
//     if ($category_slug && !isset($_GET['yith_wcan'])) {
//         $url = home_url("/campaign/{$category_slug}/");
//         $params = array(
//             'yith_wcan' => '1',
//             'product_cat' => $category_slug,
//             'query_type_kategori' => 'and',
//         );
//         wp_redirect(add_query_arg($params, $url));
//         exit;
//     }
// });
// function campaign_yith_filters_shortcode($atts) {
//     $category_slug = get_query_var('category_slug');
//     $atts = shortcode_atts(array(
//         'slug' => 'default-preset',
//     ), $atts);

//     if ($category_slug) {
//         // Set minimal context for YITH
//         wc_set_loop_prop('is_filtered', true); // Indicate filtering is active
        
//         // Let URL parameters handle the category filter
//         $output = do_shortcode('[yith_wcan_filters slug="' . esc_attr($atts['slug']) . '"]');
//         wc_reset_loop(); // Clean up
//         return $output;
//     }
//     return ''; // No filters on /campaign/ main page
// }
// add_shortcode('campaign_yith_filters', 'campaign_yith_filters_shortcode');


function campaign_yith_filters_shortcode($atts) {
    $category_slug = get_query_var('category_slug');
	
    $atts = shortcode_atts(array(
        'slug' => 'default-preset',
    ), $atts);

    if ($category_slug) {
        // Simulate WooCommerce category context
        global $wp_query, $woocommerce;
        $original_query = $wp_query;
        $wp_query = new WP_Query(array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ),
            ),
        ));
        $wp_query->is_tax = true;
        $wp_query->set('product_cat', $category_slug);
        wc_set_loop_prop('is_filtered', true); // Tell YITH filters are active

        $output = do_shortcode('[yith_wcan_filters slug="' . esc_attr($atts['slug']) . '"]');

         // Reset query to avoid affecting the rest of the page
         $wp_query = $original_query;
         wc_reset_loop();
         return $output;
     }
     return ''; // No filters on /campaign/ main page
 }
 add_shortcode('campaign_yith_filters', 'campaign_yith_filters_shortcode');


// function campaign_products_shortcode($atts) {
//     $category_slug = get_query_var('category_slug');
//     if (!$category_slug) {
//         return do_shortcode('[products limit="12" columns="4"]');
//     }
//     return do_shortcode('[products category="' . esc_attr($category_slug) . '" limit="12" columns="4"]');
// }
// add_shortcode('campaign_products', 'campaign_products_shortcode');
function campaign_products_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'limit'   => '18',
        'columns' => '6',
    ), $atts, 'campaign_products');

    // Get the category slug from query var
    $category_slug = get_query_var('category_slug');

    // Sanitize attributes
    $limit = absint($atts['limit']);
    $columns = absint($atts['columns']);

    // Preserve the original query
    global $wp_query, $post;
    $original_query = $wp_query;
    $original_post = $post;

    // Reset WooCommerce loop
    wc_reset_loop();

    // Set up query arguments
    $query_args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'posts_per_page'      => $limit,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'meta_query'          => array(),
        'tax_query'           => array('relation' => 'AND'),
    );

    // If a category slug is provided, add it to the query
    if ($category_slug) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category_slug,
            'operator' => 'IN',
        );
    }

    // Simulate a product category archive context if a category is specified
    
	if ($category_slug) {
        $term = get_term_by('slug', $category_slug, 'product_cat');
		
        if ($term) {
            $wp_query->is_tax = true;
            $wp_query->is_archive = true;
            $wp_query->queried_object = $term;
            $wp_query->queried_object_id = $term->term_id;
            $wp_query->set('product_cat', $category_slug);

            // Handle pagination
            $paged = max(1, get_query_var('paged') ? get_query_var('paged') : (isset($_GET['product-page']) ? absint($_GET['product-page']) : 1));
            $query_args['paged'] = $paged;
        }
    } else {
        // If no category, treat as a shop archive
        $wp_query->is_post_type_archive = true;
        $wp_query->is_archive = true;
        $wp_query->queried_object = get_post_type_object('product');
        $wp_query->queried_object_id = 0;

        $paged = max(1, get_query_var('paged') ? get_query_var('paged') : (isset($_GET['product-page']) ? absint($_GET['product-page']) : 1));
        $query_args['paged'] = $paged;
    }

    // Set WooCommerce loop properties
    wc_set_loop_prop('columns', $columns);
    wc_set_loop_prop('is_paginated', true); // Enable pagination
    wc_set_loop_prop('total', 0); // Will be set by the query
    wc_set_loop_prop('per_page', $limit);
    wc_set_loop_prop('current_page', isset($query_args['paged']) ? $query_args['paged'] : 1);

    // Run the query
    $products_query = new WP_Query($query_args);

    // Update loop properties with query results
    wc_set_loop_prop('total', $products_query->found_posts);
    wc_set_loop_prop('total_pages', $products_query->max_num_pages);

    // Start output buffering
    ob_start();

    // Add .woocommerce wrapper
    echo '<div class="woocommerce">';

    // Trigger WooCommerce before main content hook (for sorting, result count, etc.)
    do_action('woocommerce_before_main_content');

    // Trigger before shop loop hook (for result count, sorting, etc.)
    do_action('woocommerce_before_shop_loop');

    // Start the product loop
    woocommerce_product_loop_start();

    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            global $product;

            // Ensure the product is visible
            if (empty($product) || !$product->is_visible()) {
                continue;
            }

            // Render the product
            wc_get_template_part('content', 'product');
        }
    } else {
        // No products found
        do_action('woocommerce_no_products_found');
    }

    // End the product loop
    woocommerce_product_loop_end();

    // Trigger after shop loop hook (for pagination)
    do_action('woocommerce_after_shop_loop');

    // Trigger after main content hook
    do_action('woocommerce_after_main_content');

    echo '</div>'; // End .woocommerce wrapper

    // Get the output
    $output = ob_get_clean();

    // Reset the query and loop
    $wp_query = $original_query;
    $post = $original_post;
    wp_reset_postdata();
    wc_reset_loop();

    // Add elementor-grid class to the <ul class="products"> for Elementor compatibility
    $output = str_replace('class="products', 'class="products campaign-listing', $output);

    return $output;
}
add_shortcode('campaign_products', 'campaign_products_shortcode');
function campaign_category_info_shortcode($atts) {
    // Get the category slug from query var
    $category_slug = get_query_var('category_slug');

    // If no category slug, return an empty string or a fallback message
    if (!$category_slug) {
        return '<p>No category specified.</p>';
    }

    // Get the category term object by slug
    $term = get_term_by('slug', $category_slug, 'product_cat');

    // If the term doesn't exist, return an error message
    if (!$term || is_wp_error($term)) {
        return '<p>Category not found.</p>';
    }

    // Get the category title and description
    $category_title = $term->name;
//     $category_description = $term->description;

    // Start output buffering
    ob_start();

    // Output the category title with a custom class for styling
    echo '<p class="campaign-category-title">' . esc_html($category_title) . '</p>';

    // Output the category description if it exists
    if (!empty($category_description)) {
        echo '<div class="campaign-category-description">' . wpautop($category_description) . '</div>';
    }

    return ob_get_clean();
}
add_shortcode('campaign_category_info', 'campaign_category_info_shortcode');
add_filter('pre_get_document_title', 'set_campaign_page_title', 9999);
function set_campaign_page_title($title) {
    // Check if we're on the /campaign/{{category-slug}}/ page
    if (is_page('campaign-category-template') || get_query_var('category_slug')) {
        $category_slug = get_query_var('category_slug');
        if ($category_slug) {
            $term = get_term_by('slug', $category_slug, 'product_cat');
            if ($term && !is_wp_error($term)) {
                $category_title = $term->name;
                return esc_html($category_title) . ' | ' . get_bloginfo('name');
            }
        }
    }
    return $title;
}
add_action('wp_footer', function() {
    // Only apply on wishlist page (adjust condition based on your setup)
    if (is_page('wishlist') || is_page('ønskeliste') || (function_exists('yith_wcwl_is_wishlist_page') && yith_wcwl_is_wishlist_page())) {
        ?>
        <style>
			th.product-price{
				font-size:14px;
			}
			
           td.product-price {
                display: flex;
			   align-items: baseline;

            }
           .product-price ins {
                order: 1;
                text-decoration: none; 
			   padding-right:10px;
            }
            .product-price del {
                order: 2; 
            } 
        </style>
        <?php
    }
});
//set cat style
add_filter( 'woocommerce_get_price_html', function( $price_html, $product ) {

    if ( is_product_category() || is_shop() ) {

        $price_html = '<span class="price category-price">' . $price_html . '</span>';
    }
    return $price_html;
}, 10, 2 );



// Add a Debug Log Viewer page in WP Admin
add_action( 'admin_menu', function() {
    add_menu_page(
        'Debug Log Viewer',          // Page title
        'Debug Log',                 // Menu title
        'manage_options',            // Capability (admin only)
        'debug-log-viewer',          // Menu slug
        'debug_log_viewer_page',     // Callback function
        'dashicons-admin-tools',     // Icon
        99                           // Menu position
    );
});
add_action( 'wp_head', function() {
    echo '<meta charset="UTF-8">';
});
	
function debug_log_viewer_page() {
    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have permission to access this page.' );
    }

    $log_file = WP_CONTENT_DIR . '/debug.log';
    echo '<div class="wrap">';
    echo '<h1>Debug Log Viewer</h1>';

    if ( file_exists( $log_file ) ) {
        $log_content = file_get_contents( $log_file );
        if ( $log_content === false ) {
            echo '<p>Error: Unable to read the debug log file.</p>';
        } else if ( empty( $log_content ) ) {
            echo '<p>The debug log is empty.</p>';
        } else {
            echo '<p>Showing the latest debug log entries:</p>';
            echo '<pre style="background: #f5f5f5; padding: 15px; max-height: 600px; overflow-y: auto; white-space: pre-wrap;">';
            echo esc_html( $log_content );
            echo '</pre>';
        }
    } else {
        echo '<p>No debug.log file found in <code>' . esc_html( WP_CONTENT_DIR ) . '</code>. Ensure <code>WP_DEBUG_LOG</code> is enabled in <code>wp-config.php</code> and that logs have been generated.</p>';
    }

    echo '</div>';
}
	
	
//end debug code

// Ensure necessary scripts load
add_action( 'wp_enqueue_scripts', function() {
    global $post;
	wp_enqueue_script('custom-variations', get_template_directory_uri() . '/assets/js/custom-variations.js', array('jquery'), '1.0', true);
	wp_enqueue_script( 'jquery' );
    $custom_js = "
        jQuery(document).ready(function($) {
            console.log('Site-wide -ar to -år replacement running');
            function fixArToAar() {
                $('body :not(script, style)').contents().each(function() {
                    if (this.nodeType === 3) { // Text nodes only
                        var text = $(this).text();
                        if (text.includes('-ar')) {
                            text = text.replace(/-ar/g, ' år');
                            $(this).replaceWith(text);
                        }
                    }
                });
            }
            fixArToAar(); // Run immediately
            setInterval(fixArToAar, 1000); // Run every second for dynamic content
        });
    ";
    wp_add_inline_script( 'jquery', $custom_js );
    error_log( "Variation fix script enqueued with interval" );
    if ( is_a( $post, 'WP_Post' )) {
        wp_enqueue_script( 'jquery', false, array(), null, true );
        wp_enqueue_script( 'woocommerce' );
        wp_enqueue_script( 'wc-checkout' );
    }
	wp_localize_script( 'custom-variations','custom_ajax_params', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('wp_rest'),
    'rest_url' => rest_url('wc/v3/'),
));
//     wp_localize_script( 'custom-variations','jquery', 'custom_ajax_params', array(
//         'ajax_url' => admin_url( 'admin-ajax.php' ),
// 		'nonce' => wp_create_nonce('wp_rest'),
//         'rest_url' => rest_url('wc/v3/'),
//     ));
}, 20);

//scripts end

// AJAX handler to apply coupon
add_action( 'wp_ajax_apply_coupon', 'custom_apply_coupon' );
add_action( 'wp_ajax_nopriv_apply_coupon', 'custom_apply_coupon' );
function custom_apply_coupon() {
    if ( ! isset( $_POST['coupon_code'] ) ) {
        wp_send_json_error( 'Ingen kupong gitt' );
    }

    $coupon_code = sanitize_text_field( $_POST['coupon_code'] );
    if ( WC()->cart->apply_coupon( $coupon_code ) ) {
        WC()->cart->calculate_totals();
        wp_send_json_success( ['message' => 'Kupong brukt'] );
    } else {
        wp_send_json_error( ['message' => 'Ugyldig kupongkode'] );
    }
	
	exit;
}
//filter added by berkay
add_filter( 'gettext', 'override_woocommerce_coupon_notices', 20, 3 );
function override_woocommerce_coupon_notices( $translated, $original, $domain ) {
	if ( $original === 'Invalid coupon code' ) {
		return 'Ugyldig kupongkode.';
	}
	if ( $original === 'Coupon "%s" does not exist!' ) {
		return 'Kupong "%s" finnes ikke!';
	}
    return $translated;
}

//shipping bar on cart

// add_action('woocommerce_mini_cart_contents', 'add_wpcfb_to_side_cart', 5);

// function add_wpcfb_to_side_cart() {
//     echo '<div class="wpcfb-wrapper">' . do_shortcode('[wpcfb]') . '</div>';
// }
//translation for woocommerce 
function custom_translate_woocommerce_texts($translated_text, $text, $domain) {
    if ($domain === 'woocommerce') {
        switch ($text) {
            case 'Subtotal':
                return 'Produkter';
            case 'Checkout':
                return 'FORTSETT TIL KASSEN';
			case 'ADD TO CART':
                return 'Legg i kurv';
        }
    }
    return $translated_text;
}
add_filter('gettext', 'custom_translate_woocommerce_texts', 10, 3);
//change woocommerce_ordering 

//edit add to cart msg 
add_filter('wc_add_to_cart_message_html', function($message, $products) {
    $product = wc_get_product(array_key_first($products));
    $cart_url = wc_get_cart_url(); // Get the cart URL

    return sprintf(
        '«%s» er lagt til handlekurven din. <a href="%s" class="button wc-forward">Vis handlekurv</a>',
        $product->get_name(),
        $cart_url
    );
}, 10, 2);

function discount_percentage_shortcode() {
    global $product;

    if (!is_a($product, 'WC_Product')) {
        $product = wc_get_product(get_the_ID());
    }

    if (!$product) return '';

    $regular_price = 0;
    $sale_price = 0;

    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        foreach ($variations as $variation) {
            $variation_obj = wc_get_product($variation['variation_id']);
            $var_regular_price = floatval($variation_obj->get_regular_price());
            $var_sale_price = floatval($variation_obj->get_sale_price());

            if ($var_regular_price > 0) {
                $regular_price = max($regular_price, $var_regular_price);
            }
            if ($var_sale_price > 0) {
                $sale_price = max($sale_price, $var_sale_price);
            }
        }
    } else {
        $regular_price = floatval($product->get_regular_price());
        $sale_price = floatval($product->get_sale_price());
    }

    if ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price) {
        $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
        return '<span class="sale-price-off-product">-' . $discount_percentage . '%</span>';
		
    }

    return '';
}

add_shortcode('discount_percentage', 'discount_percentage_shortcode');



///// custom codes ends here ///
/// ends here ///

function hello_maybe_update_theme_version_in_db() {
    $theme_version_option_name = 'hello_theme_version';
    // The theme version saved in the database.
    $hello_theme_db_version = get_option( $theme_version_option_name );

    // If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
    if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
        update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
    }
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
    /**
     * Check whether to display header footer.
     *
     * @return bool
     */
    function hello_elementor_display_header_footer() {
        $hello_elementor_header_footer = true;

        return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
    }
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
    /**
     * Theme Scripts & Styles.
     *
     * @return void
     */
    function hello_elementor_scripts_styles() {
        $min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
            wp_enqueue_style(
                'hello-elementor',
                get_template_directory_uri() . '/style' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }

        if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
            wp_enqueue_style(
                'hello-elementor-theme-style',
                get_template_directory_uri() . '/theme' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }

        if ( hello_elementor_display_header_footer() ) {
            wp_enqueue_style(
                'hello-elementor-header-footer',
                get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
    /**
     * Register Elementor Locations.
     *
     * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
     *
     * @return void
     */
    function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
        if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
            $elementor_theme_manager->register_all_core_location();
        }
    }
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
    /**
     * Set default content width.
     *
     * @return void
     */
    function hello_elementor_content_width() {
        $GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
    }
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
    /**
     * Add description meta tag with excerpt text.
     *
     * @return void
     */
    function hello_elementor_add_description_meta_tag() {
        if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
            return;
        }

        if ( ! is_singular() ) {
            return;
        }

        $post = get_queried_object();
        if ( empty( $post->post_excerpt ) ) {
            return;
        }

        echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
    require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
    // Customizer controls
    function hello_elementor_customizer() {
        if ( ! is_customize_preview() ) {
            return;
        }

        if ( ! hello_elementor_display_header_footer() ) {
            return;
        }

        require get_template_directory() . '/includes/customizer-functions.php';
    }
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
    /**
     * Check whether to display the page title.
     *
     * @param bool $val default value.
     *
     * @return bool
     */
    function hello_elementor_check_hide_title( $val ) {
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            $current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
            if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
                $val = false;
            }
        }
        return $val;
    }
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
    function hello_elementor_body_open() {
        wp_body_open();
    }
}

function hello_elementor_get_theme_notifications(): ThemeNotifications {
    static $notifications = null;

    if ( null === $notifications ) {
        require get_template_directory() . '/vendor/autoload.php';

        $notifications = new ThemeNotifications(
            'hello-elementor',
            HELLO_ELEMENTOR_VERSION,
            'theme'
        );
    }

    return $notifications;
}

hello_elementor_get_theme_notifications();


// Added by Chris
// Function to get live wishlist count
function custom_wishlist_count() {
    $count = 0;
    
    if ( class_exists( 'YITH_WCWL' ) ) {
        $count = YITH_WCWL()->count_products();
    }
    
    wp_send_json_success( $count );
}
add_action( 'wp_ajax_get_wishlist_count', 'custom_wishlist_count' );
add_action( 'wp_ajax_nopriv_get_wishlist_count', 'custom_wishlist_count' );

// Function to display wishlist counter with AJAX update
function custom_wishlist_counter() {
    ob_start();
    ?>
    <div class="wishlist-counter-wrapper" style="position: relative; display: inline-block;">
        <a href="<?php echo home_url( '/wishlist' ); ?>" class="wishlist-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"></path>
            </svg>
            <span class="wishlist-count">
                0
            </span>
        </a>
    </div>

    <script>
        jQuery(document).ready(function($) {
    // Use the localized AJAX URL
    var ajaxUrl = custom_ajax_params.ajax_url;

    function updateWishlistCount() {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: { action: 'get_wishlist_count' },
            success: function(response) {
                if (response.success) {
                    $('.wishlist-count').text(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.log('Wishlist count update failed: ' + error);
            }
        });
    }

    // Call on page load
    updateWishlistCount();

    // Update only when needed (e.g., on specific events), not polling
    // Example: Update when an item is added/removed from wishlist
    $(document.body).on('added_to_wishlist removed_from_wishlist', function() {
        updateWishlistCount();
    });

    // If polling is still needed, increase interval (e.g., every 30 seconds)
    // setInterval(updateWishlistCount, 30000);
});
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'wishlist_counter', 'custom_wishlist_counter' );

// Added by Chris
// Calculate and display discount badge
function dpp_display_discount_badge() {
    global $product;

    if ( ! $product ) {
        return;
    }

    $discount = 0;

    // Handle variable products
    if ( $product->is_type( 'variable' ) ) {
        $regular_price = (float) $product->get_variation_regular_price( 'max' );
        $sale_price    = (float) $product->get_variation_sale_price( 'max' );
    } else {
        // Handle simple and other product types
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();
    }

    // Calculate discount if both prices are available
    if ( $regular_price > 0 && $sale_price > 0 ) {
        $discount = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
    }

    // Display badge if there is a discount
    if ( $discount > 0 ) {
        echo '<div class="dpp-discount-badge">-' . esc_html( $discount ) . '%</div>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'dpp_display_discount_badge', 10 );
add_action( 'woocommerce_before_single_product_summary', 'dpp_display_discount_badge', 10 ); // Single product


add_filter( 'retrieve_password_message', 'custom_reset_password_email_html', 10, 4 );

function custom_reset_password_email_html( $message, $key, $user_login, $user_data ) {
    $reset_link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );

    $logo_url = 'https://billig-26333.nora-osl.servebolt.cloud/wp-content/uploads/billige-barneklaer-logo-1.png';

    $message = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee;">
        <div style="padding: 30px 40px; text-align: center;">
            <img src="' . esc_url( $logo_url ) . '" alt="Logo" style="max-height: 60px; margin-bottom: 30px;">
            <h2 style="color: #000;">Resetting your password</h2>
            <p style="font-size: 16px;">To reset your password, go to this page:</p>
            <a href="' . esc_url( $reset_link ) . '"
               style="display: inline-block; padding: 12px 25px; border: 2px solid #000;
                      border-radius: 25px; text-decoration: none; color: #000; font-weight: bold; margin-top: 15px;">
               Reset password
            </a>
            <p style="margin-top: 30px; color: #555; font-size: 14px;">
                If you do not want to reset your password, you can ignore this message.<br>
                This link is valid for a limited time.
            </p>
        </div>

        <div style="background-color: #2f0c47; color: #fff; padding: 20px; text-align: center; font-size: 13px;">
            <div style="margin-bottom: 10px;">
                <a href="https://www.instagram.com/" style="margin: 0 10px;">
		<img src="https://cdn-icons-png.flaticon.com/24/174/174855.png" alt="Instagram" style="vertical-align: middle;">
	</a>
	<a href="https://www.facebook.com/" style="margin: 0 10px;">
		<img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook" style="vertical-align: middle;">
	</a>
            </div>
            <p style="margin: 5px 0;">©2024 Your Company Name</p>
            <p style="margin: 5px 0;">Get Inspired | PO Box 6124 TORGÅRD | Trondheim 7435 | Norway</p>
            <a href="#" style="color: #fff; text-decoration: underline;">Privacy Policy</a> |
            <a href="#" style="color: #fff; text-decoration: underline;">Unregister – Click here</a>
        </div>
    </div>';

    return $message;
}

add_filter( 'wp_mail_content_type', function() { return 'text/html'; });

