<?php
add_action('wp_footer', function () {
    ?>
    <style>
		.elementor-menu-cart__products{
			margin-bottom:auto;
		}
		.final-total{
			padding-top:20px !important;
/* 			margin-top:auto; */
			border-top:1px solid #d5d8dc;
		}
        .custom-mini-cart-line {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center;
			
            font-size: 14px;
            padding: 6px 0;
            color: #797979;
            font-weight: normal;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100%;
        }
        .custom-mini-cart-line strong {
			padding-top:10px;
            font-weight: 700;
            color: #000;
        }
        .custom-mini-cart-line span {
            font-weight: normal;
        }
        /* Ensure visibility on mobile */
        @media (max-width: 767px) {
            .elementor-menu-cart__main .custom-mini-cart-line {
                display: flex !important;
                font-size: 13px !important;
                padding: 4px 0 !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }
    </style>
    <script>
        function waitForMiniCartAndRun(callback) {
            let attempts = 0;
            const maxAttempts = 50; // 10 seconds
            const checkExist = setInterval(() => {
                const sideCart = document.querySelector(".elementor-menu-cart__container, .elementor-menu-cart__main, .woocommerce-mini-cart");
                if (sideCart && sideCart.querySelectorAll(".elementor-menu-cart__product").length > 0) {
                    clearInterval(checkExist);
//                     console.log(`Side cart detected after ${attempts} attempts`);
                    callback();
                } else {
                    attempts++;
//                     console.log(`Side cart not found, attempt ${attempts}`);
                    if (attempts >= maxAttempts) {
                        clearInterval(checkExist);
//                         console.log(`Stopped retrying: Side cart not found after ${maxAttempts} attempts`);
//                         alert('Mini-cart not found. Please refresh the page.');
                    }
                }
            }, 200);
        }

        function formatCurrency(amount) {
            // Match DOM format: "99kr"
            return `${Math.round(amount)}<span class="woocommerce-Price-currencySymbol">kr</span>`;
        }

        function injectSavedAmount() {
            const sideCart = document.querySelector(".elementor-menu-cart__main");
            if (!sideCart) {
//                 console.log('Side cart container not found');
                return;
            }

            const cartProducts = sideCart.querySelectorAll(".elementor-menu-cart__product");
            let totalSaved = 0;
            let totalOriginal = 0;

//             console.log(`Found products: ${cartProducts.length}`);

            cartProducts.forEach((product, index) => {
                const originalPriceEl = product.querySelector("del .woocommerce-Price-amount bdi");
                const salePriceEl = product.querySelector("ins .woocommerce-Price-amount bdi") || product.querySelector(".woocommerce-Price-amount bdi");
                const qtySelect = product.querySelector(".mini-cart-qty");

                const quantity = qtySelect ? parseInt(qtySelect.value) || 1 : 1;
                const originalPrice = originalPriceEl ? parseFloat(originalPriceEl.textContent.replace(/[^\d,.]/g, '').replace(',', '.')) : 0;
                const currentPrice = salePriceEl ? parseFloat(salePriceEl.textContent.replace(/[^\d,.]/g, '').replace(',', '.')) : originalPrice;

                const savedAmountSingle = (originalPrice && originalPrice > currentPrice) ? originalPrice - currentPrice : 0;
                const savedAmountTotal = savedAmountSingle * quantity;

                totalSaved += savedAmountTotal;
                totalOriginal += (originalPrice || currentPrice) * quantity;

//                 console.log(`Product ${index}: Original=${originalPrice}, Sale=${currentPrice}, Qty=${quantity}, Saved=${savedAmountTotal}`);
            });

            const subtotalEl = sideCart.querySelector('.elementor-menu-cart__subtotal .woocommerce-Price-amount');
            const parent = subtotalEl?.closest('.elementor-menu-cart__subtotal');
            const container = parent?.parentElement;

            if (!subtotalEl || !parent || !container) {
//                 console.log('Subtotal element or parent not found');
                return;
            }

            // Remove existing custom lines
            container.querySelectorAll('.custom-mini-cart-line.saved-amount, .custom-mini-cart-line.final-total').forEach(el => el.remove());

            const finalTotal = totalOriginal - totalSaved;

            // Add "Totalt å betale"
            const totalLine = document.createElement('div');
            totalLine.className = 'custom-mini-cart-line final-total';
            totalLine.innerHTML = `<strong>Totalt å betale:</strong><strong>${formatCurrency(finalTotal)},-</strong>`;

            // Add "Du sparer"
            const savedLine = document.createElement('div');
            savedLine.className = 'custom-mini-cart-line saved-amount';
            savedLine.innerHTML = `Du sparer:<span>${formatCurrency(totalSaved)},-</span>`;

            // Insert lines
            if (totalSaved > 0) {
                parent.insertAdjacentElement('afterend', savedLine);
            }
            parent.insertAdjacentElement('afterend', totalLine);

//             console.log('Total and savings lines appended');
        }

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            waitForMiniCartAndRun(injectSavedAmount);

            // MutationObserver for dynamic cart changes
            const observer = new MutationObserver(() => {
//                 console.log('Cart DOM changed');
                waitForMiniCartAndRun(injectSavedAmount);
            });
            observer.observe(document.querySelector('.elementor-menu-cart__main') || document.body, { childList: true, subtree: true });
        });

        // WooCommerce cart events
        jQuery(document.body).on('updated_wc_div updated_cart_totals wc_fragments_refreshed added_to_cart removed_from_cart', (event) => {
//             console.log(`Cart event: ${event.type}`);
            waitForMiniCartAndRun(injectSavedAmount);
        });

        // Quantity change handler
        jQuery(document).on('change', '.mini-cart-qty', function () {
            const quantity = jQuery(this).val();
            const cart_item_key = jQuery(this).data('cart-key');

            jQuery.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                action: 'update_cart_quantity',
                quantity: quantity,
                cart_item_key: cart_item_key
            }, (response) => {
                if (response.success && response.data.fragments) {
                    jQuery.each(response.data.fragments, (selector, content) => {
                        jQuery(selector).html(content);
                    });
                    waitForMiniCartAndRun(injectSavedAmount);
                } else {
//                     console.log('Quantity update failed:', response);
                    alert('Failed to update quantity. Please try again.');
                }
            });
        });
    </script>
    <?php
});
