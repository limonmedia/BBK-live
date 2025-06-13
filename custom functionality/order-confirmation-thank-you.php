<?php 
add_action('template_redirect', 'custom_woocommerce_redirect_to_order_confirmation');
function custom_woocommerce_redirect_to_order_confirmation() {
    if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
        $order_id = absint(get_query_var('order-received'));
        if ($order_id) {
            $redirect_url = get_permalink(get_page_by_path('order-confirmation'));
            $redirect_url = add_query_arg('order_id', $order_id, $redirect_url);
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}

add_shortcode('custom_order_details', 'display_custom_order_details');
function display_custom_order_details($atts) {
    $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
    $output = '';

    if ($order_id) {
        $order = wc_get_order($order_id);

        if ($order) {
            if ($order->get_user_id() === get_current_user_id() || current_user_can('manage_options')) {
                $order_number = $order->get_order_number();
                $payment_method = $order->get_payment_method_title();
                $total = $order->get_formatted_order_total();
                $email = $order->get_billing_email();

                ob_start();
?>
                <div class="thankyou-page">
                    <h2 >KJØPET DITT ER FULLFØRT</h2>
                    <p>En ordrebekreftelse for ordre <?php echo esc_html($order_number); ?> vil bli sendt til <?php echo esc_html($email); ?></p>
					<div class="summary">
                    
                        <svg style="background:#5bc0de; border-radius:50px; padding:15px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 550" fill="#000"><path d="M431.9 213.3c5.3-7.7 3.4-18.3-4.4-23.6s-18.3-3.4-23.6 4.4l-68.9 100c-6.2 9.1-19.4 9.9-26.7 1.6l-13.6-15.3c-6.2-7-16.9-7.6-24-1.4-7 6.2-7.6 16.9-1.4 24l13.6 15.3c21.9 24.7 61.3 22.3 80-4.9l69-100.1z"></path><path d="M431.2 39c-42-52-120.5-52-162.5 0-13.3 16.5-33 26.1-53.8 26.4-66.5 1-114.9 63.3-101.2 128.6 4.4 21-.5 42.8-13.4 59.7C60 306.6 77.2 383.9 136.4 414c18.7 9.5 32.4 26.9 37.4 47.7 15.6 65 86 99.8 146.4 71.4 18.9-8.9 40.7-8.9 59.6 0 60.3 28.4 130.8-6.4 146.4-71.4 5-20.8 18.7-38.2 37.4-47.7 59.2-30.1 76.4-107.4 36.1-160.3-12.8-16.9-17.8-38.7-13.4-59.7 13.6-65.3-34.7-127.6-101.2-128.6-20.8-.4-40.5-10-53.9-26.4zM295.1 60.3c28.5-35.2 81.3-35.2 109.7 0 19.6 24.2 48.7 38.5 79.7 39 44.5.7 78 42.6 68.5 87.7-6.4 30.5.8 62.4 19.6 87.2 27.6 36.3 15.6 89.1-24.5 109.5-27.6 14.1-47.7 39.7-55 70-10.7 44.6-58.5 67.7-98.9 48.6-28.1-13.2-60.5-13.2-88.5 0-40.4 19-88.2-4-98.9-48.6-7.3-30.3-27.3-56-55-70-40.1-20.4-52.1-73.2-24.5-109.5 18.8-24.8 26-56.6 19.6-87.2-9.4-45.1 24-87.1 68.5-87.7 31.1-.5 60.2-14.8 79.7-39z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    
                    <h3>Takk for handelen!</h3>
                    <p>Betalingen er gjennomført.</p>
                    <table>
                        <tr><td><strong>Betalingstype</strong></td><td style="text-align:right;"><?php echo esc_html($payment_method); ?></td></tr>
                        <tr><td><strong>Beløp</strong></td><td style="text-align:right;"><?php echo $order->get_formatted_order_total(); ?></td></tr>
                        <tr><td><strong>Ordrenummer</strong></td><td style="text-align:right;"><?php echo esc_html($order_number); ?></td></tr>
                    </table>
						</div>
                </div>
<?php
                $output = ob_get_clean();
            } else {
                $output = '<p>You do not have permission to view this order.</p>';
            }
        } else {
            $output = '<p>Invalid order ID.</p>';
        }
    } else {
        $output = '<p>No order ID provided.</p>';
    }

    return $output;
}

// add_action('template_redirect', 'custom_woocommerce_redirect_to_order_confirmation');
// function custom_woocommerce_redirect_to_order_confirmation() {
//     if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
//         $order_id = absint(get_query_var('order-received'));
//         if ($order_id) {
            
//             $redirect_url = get_permalink(get_page_by_path('order-confirmation'));
           
//             $redirect_url = add_query_arg('order_id', $order_id, $redirect_url);
//             wp_safe_redirect($redirect_url);
//             exit;
//         }
//     }
// }


// add_shortcode('custom_order_details', 'display_custom_order_details');
// function display_custom_order_details($atts) {
//     $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
//     $output = '';

//     if ($order_id) {
//         $order = wc_get_order($order_id);

//         if ($order) {
            
//             if ($order->get_user_id() === get_current_user_id() || current_user_can('manage_options')) {
//                 ob_start();
// 				    woocommerce_order_details_table($order_id);
//                 $output = ob_get_clean();
//             } else {
//                 $output = '<p>You do not have permission to view this order.</p>';
//             }
//         } else {
//             $output = '<p>Invalid order ID.</p>';
//         }
//     } else {
//         $output = '<p>No order ID provided.</p>';
//     }

//     return $output;
// }
