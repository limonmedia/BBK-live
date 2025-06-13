<?php
add_action('plugins_loaded', function() {
    if (!class_exists('WC_Brands_Admin')) {
        return;
    }

    // Lag en ny metode som erstatter den som gir feil
    remove_all_actions('woocommerce_product_import_inserted_product_object', 10);

    add_filter('woocommerce_product_import_inserted_product_object', function($product, $data) {
        if (!empty($data['brands'])) {
            $raw_brands = $data['brands'];

            // Håndter både array og streng
            $brands = is_array($raw_brands) ? $raw_brands : explode(',', $raw_brands);

            // Fjern mellomrom og tomme elementer
            $brands = array_filter(array_map('trim', $brands));

            // Sett merker (brands) på produktet
            if (!empty($brands)) {
                wp_set_object_terms($product->get_id(), $brands, 'product_brand');
            }
        }

        return $product;
    }, 10, 2);
});
