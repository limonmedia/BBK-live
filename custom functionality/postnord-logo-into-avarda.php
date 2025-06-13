<?php
add_filter( 'aco_shipping_icon', 'aco_custom_shipping_icon', 10, 3 );
function aco_custom_shipping_icon( $img_url, $carrier, $shipping_rate ) {

    // check if carrier is PostNord
    if ( stripos( $carrier, 'PostNord' ) !== false ) {
        $img_url = AVARDA_CHECKOUT_URL . '/assets/images/shipping/icon-postnord.svg';
    }
    return $img_url;

}