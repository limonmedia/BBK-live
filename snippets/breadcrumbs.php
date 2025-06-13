<?php
//removes home 
add_filter( 'woocommerce_breadcrumb_defaults', 'remove_home_from_breadcrumb' );
function remove_home_from_breadcrumb( $defaults ) {
    $defaults['home'] = ''; // Removes "Home /"
    return $defaults;
}

//removes product name
add_filter('woocommerce_breadcrumb_defaults', 'customize_woocommerce_breadcrumbs');
function customize_woocommerce_breadcrumbs($defaults) {
    if (is_product()) {
        add_filter('woocommerce_get_breadcrumb', 'remove_product_name_from_breadcrumb', 20, 2);
    }
    return $defaults;
}

function remove_product_name_from_breadcrumb($crumbs, $breadcrumb) {
    array_pop($crumbs);
    return $crumbs;
}
