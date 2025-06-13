<?php

// Enqueue inline JavaScript for AJAX coupon application, notices, and dynamic total update
add_action('wp_footer', 'add_custom_coupon_script_and_notices');
function add_custom_coupon_script_and_notices() {
    if (is_checkout()) {
        // Get current cart totals for JavaScript
        $subtotal = WC()->cart->get_subtotal() * 1.25; // Mimic shortcode's VAT adjustment
        $discount_total = WC()->cart->get_discount_total();
        $total = $subtotal - $discount_total; // Correct total after discount
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Apply coupon via AJAX
                $('.discount-code-container .activate-btn').on('click', function(e) {
                    e.preventDefault();
                    var $container = $(this).closest('.discount-code-container');
                    var couponCode = $container.find('.discount-input').val().trim();
                    if (!couponCode) {
                        alert('Vennligst skriv inn en rabattkode.');
                        return;
                    }
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'apply_custom_coupon',
                            coupon_code: couponCode,
                            nonce: '<?php echo wp_create_nonce('apply-coupon'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.data.message);
                                // Update Totalpris dynamically
                                $.ajax({
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    type: 'POST',
                                    data: {
                                        action: 'get_updated_cart_totals'
                                    },
                                    success: function(totals) {
                                        if (totals.success) {
                                            $('.summary-row.total .woocommerce-Price-amount').html(totals.data.total_html);
                                        }
                                        location.reload(); // Reload to ensure all totals are consistent
                                    }
                                });
                            } else {
                                alert(response.data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Feil ved bruk av rabattkode. Prøv igjen.');
                            console.log('AJAX Error:', xhr.responseText);
                        }
                    });
                });

                // Initial Totalpris correction on page load
                var currentTotal = '<?php echo wc_price($total); ?>';
                $('.summary-row.total .woocommerce-Price-amount').html(currentTotal);
            });
        </script>
        <div class="woocommerce-notices-wrapper">
            <?php wc_print_notices(); ?>
        </div>
        <?php
    }
}

// AJAX handler for applying coupons
add_action('wp_ajax_apply_custom_coupon', 'apply_custom_coupon_callback');
add_action('wp_ajax_nopriv_apply_custom_coupon', 'apply_custom_coupon_callback');
function apply_custom_coupon_callback() {
    check_ajax_referer('apply-coupon', 'nonce');
    $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field($_POST['coupon_code']) : '';

    if (empty($coupon_code)) {
        wp_send_json_error(['message' => __('Ingen rabattkode angitt.')]);
        wp_die();
    }

    if (WC()->cart) {
        $result = WC()->cart->apply_coupon($coupon_code);
        if ($result) {
            WC()->cart->calculate_totals(); // Ensure totals are updated
            wp_send_json_success(['message' => __('Rabattkode brukt!')]);
        } else {
            $notices = wc_get_notices('error');
            $error_message = !empty($notices) ? end($notices)['notice'] : __('Ugyldig rabattkode eller kupongen kan ikke brukes.');
            wp_send_json_error(['message' => $error_message]);
        }
    } else {
        wp_send_json_error(['message' => __('Feil: Handlekurv ikke initialisert.')]);
    }
    wp_die();
}

// AJAX handler to get updated cart totals
add_action('wp_ajax_get_updated_cart_totals', 'get_updated_cart_totals_callback');
add_action('wp_ajax_nopriv_get_updated_cart_totals', 'get_updated_cart_totals_callback');
function get_updated_cart_totals_callback() {
    if (WC()->cart) {
        $subtotal = WC()->cart->get_subtotal() * 1.25; // Mimic shortcode's VAT adjustment
        $discount_total = WC()->cart->get_discount_total();
        $total = $subtotal - $discount_total; // Correct total
        wp_send_json_success([
            'total_html' => wc_price($total)
        ]);
    } else {
        wp_send_json_error(['message' => __('Feil: Handlekurv ikke initialisert.')]);
    }
    wp_die();
}

// Ensure cart totals are recalculated
add_action('woocommerce_cart_calculate_totals', 'ensure_discount_applied_in_total', 20);
function ensure_discount_applied_in_total($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    $cart->calculate_totals();
}