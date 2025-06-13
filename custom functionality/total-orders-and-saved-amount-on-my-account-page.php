<?php
function display_customer_order_totals() {
    if ( ! class_exists( 'WooCommerce' ) || ! is_user_logged_in() ) {
        return '';
    }

    $user_id = get_current_user_id();
    $customer_orders = wc_get_orders([
        'customer_id' => $user_id,
        'status'      => ['completed', 'processing'],
        'limit'       => -1,
    ]);

    $total_used = 0;
    $total_saved = 0;

    foreach ( $customer_orders as $order ) {
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( ! $product ) continue;

            $quantity      = $item->get_quantity();
            $paid_price    = $item->get_total() + $item->get_total_tax(); // actual paid
            $regular_price = $product->get_regular_price();

            if ( ! $regular_price ) continue;

            $total_used  += $paid_price;
            $total_saved += max(0, ($regular_price * $quantity) - $paid_price);
        }
    }

    function format_nok($amount) {
        return number_format($amount, 0, '', ' ') . ' NOK';
    }

    ob_start(); ?>
    <div class="cart-totals">
        <p class="price-heading">Du har handlet for</p>
        <h2 class="sale-price"><?php echo format_nok($total_used); ?></h2>

        <p class="price-heading">Du har spart</p>
        <h2 class="total-saved"><?php echo format_nok($total_saved); ?></h2>

        <p style="margin-top: 10px; margin-bottom: 0px; font-weight: 600; color: #2375B9; font-family:Barlow;">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">Vis ordrehistorikk>></a>
        </p>
    </div>

    <style>
        .cart-totals { font-family:Barlow; max-width: 400px; }
        .price-heading { margin: 0; font-size: 16px; font-weight: 500; color: #242424; }
        .sale-price { margin-top: 0; color: #070707; font-size: 26px !important; }
        .total-saved { margin-top: 0; color: #d1080f; font-size: 40px !important; }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode( 'order_totals', 'display_customer_order_totals' );
