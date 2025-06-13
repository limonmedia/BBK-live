<?php
add_filter( 'wpcfb_progress_bar_attrs', function ( $attrs, $remaining ) {
	$price = wp_strip_all_tags( wc_price( $remaining ) );
	$attrs['remaining'] = 'Kjøp for ' . $price . ' mer og få gratis frakt';
	
	return $attrs;
}, 99, 2 );
add_filter( 'woocommerce_currency_symbol', function( $symbol, $currency ) {
    return ',-';
}, 10, 2 );