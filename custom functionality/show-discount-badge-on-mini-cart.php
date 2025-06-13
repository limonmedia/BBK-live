<?php
add_filter( 'woocommerce_cart_item_name', 'add_discount_shortcode_above_product_name', 10, 3 );
function add_discount_shortcode_above_product_name( $product_name, $cart_item, $cart_item_key ) {
    if ( is_ajax() || wp_doing_ajax() ) {
        global $post, $product;

        // Set the product for shortcode context
        $product = $cart_item['data'];

        // Call the existing [discount_percentage] shortcode
        $label = do_shortcode('[discount_percentage]');

        // Return label + product name
        return $label . $product_name;
    }

    return $product_name;
}
