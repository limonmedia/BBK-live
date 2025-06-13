<?php
/**
 * Hide only the AJAX‐injected Notiny popup (the “Adding…” drawer),
 * but allow the normal “Product added to cart” notification on page reload.
 */
function hide_ajax_notiny_via_mousedown() {
    ?>
    <style>
        /* While <body> has .wooac-hide-ajax, any Notiny popup is hidden. */
        body.wooac-hide-ajax .notiny-container {
            display: none !important;
        }
    </style>

    <script>
    (function() {
        // As soon as the user presses down on an Add-to-Cart button,
        // add a class so the AJAX popup is never visible.
        function addHideClass() {
            document.body.classList.add('wooac-hide-ajax');
        }

        // We listen for mousedown (fires before click), to beat WooAC’s JS.
        document.addEventListener('mousedown', function(e) {
            // Common Add-to-Cart selectors:
            //   - .single_add_to_cart_button  (simple/variable product page)
            //   - .add_to_cart_button         (shop/archive “Add to cart” links)
            //   - .woocommerce-variation-add-to-cart .single_add_to_cart_button
            //     (in case your theme wraps variation button differently)
            if (
                e.target.matches('.single_add_to_cart_button') ||
                e.target.matches('.add_to_cart_button') ||
                e.target.closest('.woocommerce-variation-add-to-cart .single_add_to_cart_button')
            ) {
                addHideClass();
            }
        }, true);

        // As a fallback, also listen for click (in case some themes trigger AJAX on click without a mousedown).
        document.addEventListener('click', function(e) {
            if (
                e.target.matches('.single_add_to_cart_button') ||
                e.target.matches('.add_to_cart_button') ||
                e.target.closest('.woocommerce-variation-add-to-cart .single_add_to_cart_button')
            ) {
                addHideClass();
            }
        }, true);
    })();
    </script>
    <?php
}
add_action('wp_head', 'hide_ajax_notiny_via_mousedown', 1);
