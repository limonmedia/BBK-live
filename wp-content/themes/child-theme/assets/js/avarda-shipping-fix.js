jQuery(document).ready(function($) {
    // Ensure Avarda widget is present
    if ($('#aco-iframe').length || $('avarda-shipping-module-form').length) {
        // Function to update WooCommerce cart and refresh Avarda
        function updateAvardaShipping(shippingMethod) {
            console.log('Pushing shipping method to Avarda:', shippingMethod);

            $.ajax({
                type: 'POST',
                url: custom_ajax_params.ajax_url,
                data: {
                    action: 'update_cart_with_shipping',
                    shipping_method: shippingMethod
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Cart updated:', response);

                        // Refresh Avarda widget
                        if (typeof AvardaCheckout !== 'undefined' && response.purchase_id) {
                            $('#aco-iframe').html('<div id="checkout-form"><avarda-checkout-custom-element></avarda-checkout-custom-element></div>');
                            AvardaCheckout.init({ paymentId: response.purchase_id });
                            console.log('Avarda widget refreshed with purchase ID:', response.purchase_id);
                        } else {
                            console.log('AvardaCheckout not available or no purchase_id');
                        }

                        // Update total outside widget (if applicable)
                        if (response.summary && response.summary.total) {
                            $('.order-summary .summary-row.total div:last-child').html(response.summary.total);
                        }
                    } else {
                        console.log('Update failed:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', error);
                }
            });
        }

        // Option 1: Avarda shippingMethodChanged event
        if (typeof AvardaCheckout !== 'undefined') {
            AvardaCheckout.on('shippingMethodChanged', function(data) {
                var shippingMethod = data.shippingMethodId || data.selectedShippingOption;
                if (shippingMethod) {
                    updateAvardaShipping(shippingMethod);
                }
            });

            // Log initialization for debugging
            AvardaCheckout.on('initialized', function() {
                console.log('Avarda Checkout initialized');
            });
        }

        // Option 2: DOM listener for shipping method change
        var lastShippingMethod = null;
        $('body').on('change', 'avarda-shipping-module-form input[type="radio"]', function() {
            var shippingMethod = $(this).val();
            if (shippingMethod && shippingMethod !== lastShippingMethod) {
                lastShippingMethod = shippingMethod;
                updateAvardaShipping(shippingMethod);
            }
        });

        // Option 3: Fallback polling (remove if Option 1 or 2 works)
        setInterval(function() {
            var $selected = $('avarda-shipping-module-form input[type="radio"]:checked');
            if ($selected.length) {
                var shippingMethod = $selected.val();
                if (shippingMethod && shippingMethod !== lastShippingMethod) {
                    lastShippingMethod = shippingMethod;
                    updateAvardaShipping(shippingMethod);
                }
            }
        }, 1000);
    } else {
        console.log('Avarda widget not detected on this page');
    }
});
