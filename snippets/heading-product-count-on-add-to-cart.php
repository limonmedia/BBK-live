<?php
function modify_elementor_menu_cart() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            function addCustomElements() {
                if (!$('.custom-cart-header').length) {
                    let counts = getProductAndVariationCounts();
                    let customHeader = $('<div class="custom-cart-header"></div>');
                    let headingText = buildHeadingText(counts);
                    
                    let heading = $('<p class="custom-cart-heading">' + headingText + '</p>');
                    let closeButton = $('.elementor-menu-cart__close-button').first();
                    customHeader.append(heading).append(closeButton);
                    $('.elementor-menu-cart__main').prepend(customHeader);
                }
                $('.elementor-button--checkout .cart-checkout-btn').text('FORTSETT TIL KASSEN');
            }
            
            function getProductAndVariationCounts() {
                const productCount = document.querySelectorAll('.elementor-menu-cart__product').length || 0;
                
                let totalVariations = 0;
                $('.mini-cart-qty').each(function() {
                    totalVariations += parseInt($(this).val()) || 1;
                });
                
                if (totalVariations === 0) {
                    totalVariations = productCount;
                }
                
                return {
                    products: productCount,
                    variations: totalVariations
                };
            }
            
            function buildHeadingText(counts) {
                let headingText = 'Handlekurv';
                
                if (counts.products > 0) {
                    let productText = counts.products === 1 ? 'produkt' : 'produkter';
                  
                    
                    if (counts.products !== counts.variations) {
                        headingText += ` <span class="cart-count">(${counts.variations} ${productText})</span>`;
                    } else {
                        headingText += ` <span class="cart-count">(${counts.products} ${productText})</span>`;
                    }
                }
                
                return headingText;
            }
            
            function updateProductCount() {
                const counts = getProductAndVariationCounts();
                const headingText = buildHeadingText(counts);
                $('.custom-cart-heading').html(headingText);
                
//                 console.log('Product count: ' + counts.products + ', Total variations: ' + counts.variations);
            }
			
            $(document).on('wc_fragments_refreshed wc_fragments_loaded added_to_cart removed_from_cart updated_cart_totals', function(event) {
//                 console.log('Header cart event: ' + event.type);
                addCustomElements();
                updateProductCount();
            });
            
            $(document).on('change', '.mini-cart-qty', function() {
                updateProductCount();
            });
            
            addCustomElements();
            updateProductCount();
        });
    </script>
    <?php
}
add_action('wp_footer', 'modify_elementor_menu_cart');