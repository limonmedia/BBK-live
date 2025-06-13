<?php
add_action('wp_footer', 'reopen_elementor_side_cart_script');
function reopen_elementor_side_cart_script() {
    if (is_cart() || is_checkout() || isset($_GET['removed_item']) || isset($_GET['added_to_cart'])) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var urlParams = new URLSearchParams(window.location.search);
                var isRemoved = urlParams.has('removed_item') && urlParams.get('removed_item') === '1';
                var isAdded = urlParams.has('added_to_cart');

                if (isRemoved) {
                    setTimeout(function() {
                        var $cartToggle = $('.elementor-menu-cart__toggle .elementor-button');
                        if ($cartToggle.length) {
                            $cartToggle.trigger('click');
                        } else {
                            console.log('Menu Cart toggle not found.');
                        }
                    }, 500); 
                }
            });
        </script>
        <?php
    }
}

// // add_action('wp_footer', 'reopen_elementor_side_cart_script');
// function reopen_elementor_side_cart_script() {
//     if (is_cart() || is_checkout() || isset($_GET['removed_item'])) {
//         ?>
//         <script type="text/javascript">
//             jQuery(document).ready(function($) {
//                 if (window.location.search.includes('removed_item=')) {
//                     setTimeout(function() {
//                         var $cartToggle = $('.elementor-menu-cart__toggle .elementor-button');
//                         if ($cartToggle.length) {
//                             $cartToggle.trigger('click');
//                         } else {
//                             console.log('Menu Cart toggle not found.');
//                         }
//                     }, 500); // Delay to ensure cart is fully loaded
//                 }
//             });
//         </script>
//         <?php
//     }
// }