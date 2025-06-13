<?php 
// 1. Add custom sorting options (with labels in Norwegian)
add_filter('woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby');
function custom_woocommerce_catalog_orderby($sortby) {
    $sortby = array(); // Clear default options

    $sortby['menu_order']   = 'Mest relevant';
    $sortby['popularity']   = 'Bestselgere';
    $sortby['price']        = 'Pris lav–høy';
    $sortby['price-desc']   = 'Pris høy–lav';
    $sortby['discount']     = 'Høyest rabatt';

    return $sortby;
}

// 2. Adjust query parameters for sorting options
add_action('woocommerce_product_query', 'custom_sorting_logic');
function custom_sorting_logic($query) {
    if (is_admin() || !$query->is_main_query()) return;

    if (isset($_GET['orderby'])) {
        switch ($_GET['orderby']) {
            case 'price':
                $query->set('orderby', 'meta_value_num');
                $query->set('meta_key', '_price');
                $query->set('order', 'ASC');
                break;

            case 'price-desc':
                $query->set('orderby', 'meta_value_num');
                $query->set('meta_key', '_price');
                $query->set('order', 'DESC');
                break;

            case 'discount':
                // Use a meta query to sort by _max_discount_percent (for variable products) or _discount_percent (for simple products)
                $query->set('meta_query', array(
                    'relation' => 'OR',
                    array(
                        'key' => '_max_discount_percent', // For variable products
                        'compare' => 'EXISTS',
                    ),
//                     array(
//                         'key' => '_max_discount_percent',
//                         'compare' => 'NOT EXISTS',
//                         'value' => '0', // Fallback to _discount_percent if _max_discount_percent doesn't exist
//                     ),
                    array(
                        'key' => '_discount_percent', // For simple products
                        'compare' => 'EXISTS',
                    ),
//                     array(
//                         'key' => '_discount_percent',
//                         'compare' => 'NOT EXISTS',
//                         'value' => '0', // Fallback for products with no discount
//                     ),
                ));
                // Sort by both keys, prioritizing _max_discount_percent for variable products
                $query->set('orderby', array(
                    'meta_value_num' => 'DESC',
                ));
                $query->set('meta_key', '_max_discount_percent'); // Primary sort key for variable products
                break;

            case 'menu_order':
                $query->set('orderby', 'menu_order');
                $query->set('order', 'ASC');
                break;

            default:
                break;
        }
    }
}

// 3. Calculate and save discount percentage for simple products and variations
add_action('save_post_product', 'update_discount_percent_meta', 10, 1);
add_action('woocommerce_update_product_variation', 'update_discount_percent_meta', 10, 1);
function update_discount_percent_meta($post_id) {
    if (wp_is_post_revision($post_id)) return;

    $product = wc_get_product($post_id);
    if (!$product) return;

    // Calculate discount for the product or variation
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $discount_percent = 0;

    if (is_numeric($regular_price) && is_numeric($sale_price) && $regular_price > 0 && $sale_price >= 0 && $sale_price < $regular_price) {
        $discount_percent = (($regular_price - $sale_price) / $regular_price) * 100;
    }

    update_post_meta($post_id, '_discount_percent', round($discount_percent));

    // If this is a variation, update the parent product's max discount
    if ($product->is_type('variation')) {
        $parent_id = $product->get_parent_id();
        update_max_discount_for_variable_product($parent_id);
    }
}

// 4. Function to calculate and store the maximum discount for variable products
function update_max_discount_for_variable_product($parent_id) {
    $product = wc_get_product($parent_id);
    if (!$product || !$product->is_type('variable')) return;

    $variations = $product->get_available_variations();
    $max_discount = 0;

    foreach ($variations as $variation) {
        $variation_id = $variation['variation_id'];
        $discount = (float) get_post_meta($variation_id, '_discount_percent', true);
        if ($discount > $max_discount) {
            $max_discount = $discount;
        }
    }

    // Store the maximum discount for the variable product
    update_post_meta($parent_id, '_max_discount_percent', $max_discount);
}

// 5. One-time script to update existing products and variations (run once, then remove)
// function update_all_product_discounts() {
//     // Update simple and variable products
//     $products = get_posts([
//         'post_type' => 'product',
//         'posts_per_page' => -1,
//         'post_status' => 'publish',
//     ]);
//     foreach ($products as $product) {
//         $product_obj = wc_get_product($product->ID);
//         if ($product_obj->is_type('variable')) {
//             $variations = $product_obj->get_available_variations();
//             foreach ($variations as $variation) {
//                 update_discount_percent_meta($variation['variation_id']);
//             }
//             // Update the max discount for the variable product
//             update_max_discount_for_variable_product($product->ID);
//         } else {
//             update_discount_percent_meta($product->ID);
//         }
//     }
// }
// add_action('init', 'update_all_product_discounts');

// 6. Custom dropdown for sorting (same as before)
function modify_wc_ordering() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            function initializeCustomDropdown() {
                const $orderingForm = $('.woocommerce-ordering');
                const $select = $orderingForm.find('select.orderby');

                if ($select.length && !$orderingForm.hasClass('custom-dropdown-initialized')) {
                    $select.hide();
                    const $customDropdown = $('<div class="custom-orderby-dropdown"></div>');
                    const $trigger = $('<div class="custom-orderby-trigger">' + $select.find('option:selected').text() + '</div>');
                    const $menu = $('<ul class="custom-orderby-menu"></ul>');

                    $select.find('option').each(function() {
                        const $item = $('<li class="custom-orderby-item"></li>')
                            .text($(this).text())
                            .data('value', $(this).val())
                            .addClass($(this).is(':selected') ? 'selected' : '');
                        $menu.append($item);
                    });

                    $customDropdown.append($trigger).append($menu);
                    $orderingForm.append($customDropdown);
                    $orderingForm.addClass('custom-dropdown-initialized');

                    $trigger.on('click', function() {
                        $customDropdown.toggleClass('open');
                        $menu.toggleClass('is-open');
                    });

                    $menu.on('click', '.custom-orderby-item', function() {
                        const $item = $(this);
                        $trigger.text($item.text());
                        $select.val($item.data('value')).trigger('change');
                        $menu.find('.custom-orderby-item').removeClass('selected');
                        $item.addClass('selected');
                        $customDropdown.removeClass('open');
                        $menu.removeClass('is-open');
                    });

                    $(document).on('click', function(e) {
                        if (!$customDropdown.is(e.target) && $customDropdown.has(e.target).length === 0 && $customDropdown.hasClass('open')) {
                            $customDropdown.removeClass('open');
                            $menu.removeClass('is-open');
                        }
                    });
                }
            }

            initializeCustomDropdown();
            $(document).on('wc_fragments_refreshed wc_fragments_loaded', initializeCustomDropdown);
        });
    </script>

    <style>
        .custom-orderby-dropdown {
            position: relative;
            display: inline-block;
/*             width: 175px; */
        }

        .custom-orderby-trigger {
            padding: 5px;
            color: #000;
        }
        .custom-orderby-trigger:after {
            padding-left: 4px;
            content: '\f107';
            font-family: "Font Awesome 6 Free";
        }

        .custom-orderby-menu {
            box-shadow: rgba(0, 0, 0, 0.25) 0px 0px 6px;
            display: none;
            position: absolute;
            top: 100%;
/*             left: 0; */
			right:0;
            width: 100%;
            background: #fff;
            border-radius: 5px;
            list-style: none;
            margin: 0;
IE: none;
            padding: 0;
            z-index: 1000;
            transition: opacity 0.2s ease, transform 0.2s ease;
            opacity: 0;
            transform: translateY(-10px);
        }

        .custom-orderby-menu.is-open {
            display: block;
            opacity: 1;
            transform: translateY(0);
			width:175px;
        }

        .custom-orderby-item {
            padding: 10px 5px;
            cursor: pointer;
            color: #000;
            margin: 0 20px;
            font-weight: normal;
        }
        .custom-orderby-item.selected {
            font-weight: bold;
        }

        .custom-orderby-item.selected:before {
            content: '\f00c';
            font-family: "Font Awesome 6 Free";
            padding-right: 10%;
        }
        .custom-orderby-item:before {
            content: '';
            padding-right: 24%;
        }

        .woocommerce-ordering select.orderby {
            display: none;
        }
    </style>
    <?php
}
add_action('wp_footer', 'modify_wc_ordering');