<?php
add_action( 'template_redirect', 'clear_woocommerce_notices_on_non_checkout_pages' );
function clear_woocommerce_notices_on_non_checkout_pages() {
    if ( ! is_checkout() ) {
        wc_clear_notices();
    }
}
