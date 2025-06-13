<?php

add_action('wp_enqueue_scripts', 'custom_minicart_ajax_remove_script');
function custom_minicart_ajax_remove_script() {
    wp_enqueue_script('jquery');

    $inline_js = "
        jQuery(document).ready(function($) {
            $(document).on('click', '.remove_from_cart_button', function(e) {
                e.preventDefault();

                var \$this = $(this);
                var cart_item_key = \$this.data('cart_item_key');

                $.ajax({
                    url: minicart_ajax_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_minicart_item',
                        nonce: minicart_ajax_obj.nonce,
                        cart_item_key: cart_item_key
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update minicart content
                            $('.widget_shopping_cart_content').html(response.data.minicart);

                            // Update cart count and total if needed
                            if (response.data.cart_count === 0) {
                                $('.cart-count').text('0');
                                $('.widget_shopping_cart_content').html('<p class=\"woocommerce-mini-cart__empty-message\">No products in the cart.</p>');
                            } else {
                                $('.cart-count').text(response.data.cart_count);
                            }
                            $('.cart-total').html(response.data.cart_total);

                            // Trigger WooCommerce events for compatibility
                            $(document.body).trigger('wc_fragment_refresh');
                            $(document.body).trigger('removed_from_cart', [response, cart_item_key]);
                        } else {
                            console.log('Error: ' + response.data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX error: ' + error);
                    }
                });
            });
        });
    ";

    wp_add_inline_script('jquery', $inline_js);

    wp_localize_script('jquery', 'minicart_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('remove_minicart_item_nonce')
    ));
}

// AJAX handler to remove item from minicart
add_action('wp_ajax_remove_minicart_item', 'custom_remove_minicart_item');
add_action('wp_ajax_nopriv_remove_minicart_item', 'custom_remove_minicart_item');
function custom_remove_minicart_item() {
    check_ajax_referer('remove_minicart_item_nonce', 'nonce');

    if (isset($_POST['cart_item_key'])) {
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        if (WC()->cart->remove_cart_item($cart_item_key)) {
            // Get updated minicart content
            ob_start();
            woocommerce_mini_cart();
            $minicart_content = ob_get_clean();

            wp_send_json_success(array(
                'minicart' => $minicart_content,
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total()
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to remove item.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid cart item key.'));
    }

    wp_die();
}
