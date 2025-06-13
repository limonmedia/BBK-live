<?php

// Reorder My Account menu and add Wishlist and Betaling og faktura
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_order' );
function custom_my_account_menu_order( $items ) {
    $new_order = array(
        'dashboard'       => __( 'Dashboard', 'woocommerce' ),
//         'wishlist'        => __( 'Ønskeliste', 'woocommerce' ),
        'orders'          => __( 'Orders', 'woocommerce' ),
        'betaling-faktura' => __( 'Betaling og faktura', 'woocommerce' ),
        'edit-address'    => __( 'Addresses', 'woocommerce' ),
        'edit-account'    => __( 'Account Details', 'woocommerce' ),
        'customer-logout' => __( 'Logout', 'woocommerce' ),
    );
    return $new_order;
}

// Register the Wishlist endpoint
add_action( 'init', 'custom_add_wishlist_endpoint' );
function custom_add_wishlist_endpoint() {
    add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
}

// Register the Betaling og faktura endpoint
add_action( 'init', 'custom_add_betaling_faktura_endpoint' );
function custom_add_betaling_faktura_endpoint() {
    add_rewrite_endpoint( 'betaling-faktura', EP_ROOT | EP_PAGES );
}

// Recognize Wishlist endpoint
add_filter( 'woocommerce_get_query_vars', 'custom_wishlist_query_var' );
function custom_wishlist_query_var( $vars ) {
    $vars['wishlist'] = 'wishlist';
    return $vars;
}

// Recognize Betaling og faktura endpoint
add_filter( 'woocommerce_get_query_vars', 'custom_betaling_faktura_query_var' );
function custom_betaling_faktura_query_var( $vars ) {
    $vars['betaling-faktura'] = 'betaling-faktura';
    return $vars;
}

// Treat endpoints like account tabs
add_filter( 'woocommerce_account_endpoint_type', 'custom_endpoint_type', 10, 2 );
function custom_endpoint_type( $type, $endpoint ) {
    if ( in_array( $endpoint, ['wishlist', 'betaling-faktura'] ) ) {
        return 'page';
    }
    return $type;
}

// Show Wishlist content
add_action( 'woocommerce_account_wishlist_endpoint', 'custom_wishlist_content' );
function custom_wishlist_content() {
    echo do_shortcode( '[yith_wcwl_wishlist]' );
}

// Show Betaling og faktura content
add_action( 'woocommerce_account_betaling-faktura_endpoint', 'custom_betaling_faktura_content' );
function custom_betaling_faktura_content() {
    ?>
    <div id="pay-frame"></div>
    <script>
      (function (e, t, n, a, o, i) {
        e[a] =
          e[a] ||
          function () {
            (e[a].q = e[a].q || []).push(arguments);
          };
        i = t.createElement(n);
        i.async = 1;
        i.type = 'module';
        i.src = o + '?ts=' + Date.now();
        t.body.prepend(i);
      })(
        window,
        document,
        'script',
        'avardaPayFrameInit',
        'https://pay-frame.avarda.com/cdn/pay-frame.js'
      );
      window.avardaPayFrameInit({
        rootNode: document.getElementById('pay-frame'),
        siteKey: '729a2000-ec1c-4caa-9231-0348ffad4a99',
        language: 'no',
      });
		
		// Dynamically change language after loading
    setTimeout(() => {
      if (window.avardaPayFrameInstance?.changeLanguage) {
        window.avardaPayFrameInstance.changeLanguage('no');
      }
    }, 2000);
    </script>
    <?php
}

// Load Dashicons (for front-end)
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'dashicons' );
});

// Style the Wishlist and Betaling og faktura menu items with icons
add_action( 'wp_head', 'custom_account_menu_icon_style' );
function custom_account_menu_icon_style() {
    ?>
    <style>
        .woocommerce-MyAccount-navigation-link--wishlist a::before {
            font-family: 'Dashicons';
            content: '\f487';
            margin-right: 8px;
            vertical-align: middle;
            font-size: 16px;
            color: #000;
        }
        .woocommerce-MyAccount-navigation-link--betaling-faktura a::before {
            font-family: 'Dashicons';
            content: '\f508'; /* Icon for money/credit card */
            margin-right: 8px;
            vertical-align: middle;
            font-size: 16px;
            color: #000;
        }
    </style>
    <?php
}
