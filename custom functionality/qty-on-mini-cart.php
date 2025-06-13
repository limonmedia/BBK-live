<?php

add_action( 'wp_footer', function() {
    ?>
    <style>
		.woo-variation-swatches.wvs-behavior-hide .variable-items-wrapper .variable-item.disabled {
			display: none !important
		}
		span.haji {
			display: none !important
		}
		#woosq-popup .single-product .variations {
			display: block !important
		}
		.woocommerce-variation .woocommerce-variation-description {
			display: none !important
		}
		.berocket_better_labels.berocket_better_labels_image {
			z-index: 1
		}
		div[data-id="cfcf029"]{
			margin-bottom: -10px !important
		}
		.wpgb-result-count {
			color: #000;
			font-weight: 500
		}
		
		.woosq-sidebar .woosq-product > .product > div {
			max-height: 360px
		}

		.woosq-product .thumbnails img {
			max-height: 360px
		}

		.woosq-sidebar .variatform.cart {
			margin-bottom: unset;
		}
		
		.woosq-sidebar h1.product_title.entry-title {
			text-align: left
		}


		@media screen and (max-width: 1023px) {
			.woosq-product .thumbnails img {
				max-height: 250px;
				margin: 0;
			}
		}
		
		.elementor-75199 .elementor-element.elementor-element-2154bc04 {
			padding: 0 6% 12% 6% !important
		}

		@media screen and (max-width: 1023px) {
			.woosq-product .thumbnails img {
				max-height: 250px;
				margin: 0;
			}
			
			.elementor-75199 .elementor-element.elementor-element-2154bc04 {
				padding: 6% 6% 12% 6% !important
			}
		}
		
		.dialog-widget-content {
			z-index: 999999999 !important;
		}
		
		
		
		.wpgb-apply {
			background-color: #36A9E1 !important;
			padding: 12px 0 !important;
			text-align: center !important;
		}
		
		
        .mini-cart-qty-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    margin-top: 2px; 
    gap: 12px; 
}
		

.mini-cart-stock {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #198754;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap; 
	
}

.mini-cart-stock svg {
    width: 18px;
    height: 18px;
    fill: #198754;
}
		.mini-cart-stock {
  color: black;
}

.mini-cart-stock svg {
  fill: #198754; /* re-apply the green color to SVG to override inherited text color */
  color: inherit; /* optional if the SVG uses 'fill' */
}


.mini-cart-qty {
    padding: 6px 10px;
	 border: none !important;
    border-radius: 6px;
    background: #f6f6f6;
    font-size: 14px;
	color:rgb(39, 39, 39);
    min-width: 85px;
	    right: 0;
	bottom:15%;
    position: absolute;
    flex-shrink: 0; 
		 transition: box-shadow 0.2s ease;
}
.mini-cart-qty:focus {
  border: 1px solid #0d6efd; 
  outline: none;
  box-shadow: 0 0 0 2px #0d6efd; 
	 
}
@media (max-width: 768px) {
		.mini-cart-qty {
		    right: 2%;
			width:10% !important;
    min-width: 60px !important;
		}
		}
     
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            jQuery(document).on('change', '.mini-cart-qty', function () {
                var quantity = jQuery(this).val();
                var cart_item_key = jQuery(this).data('cart-key');

                jQuery.post('<?php echo admin_url( "admin-ajax.php" ); ?>', {
                    action: 'update_cart_quantity',
                    quantity: quantity,
                    cart_item_key: cart_item_key
                }, function (response) {
                    if (response.success && response.data.fragments) {
                        jQuery.each(response.data.fragments, function (selector, content) {
                            jQuery(selector).html(content);
                        });
                    }
                });
            });
        });
    </script>
    <?php
});

// === Add Quantity Dropdown in Mini Cart Items ===
add_filter( 'woocommerce_widget_cart_item_quantity', function( $html, $cart_item, $cart_item_key ) {
    $product = $cart_item['data'];
    $max_qty = $product->get_stock_quantity();
    $current_qty = $cart_item['quantity'];

    if ( $product->is_sold_individually() || ! $product->managing_stock() ) {
        return $html;
    }
 $default_quantity_html = $html;
    $select = '<select class="mini-cart-qty" data-cart-key="' . esc_attr( $cart_item_key ) . '">';
    for ( $i = 1; $i <= $max_qty; $i++ ) {
        $selected = $i == $current_qty ? 'selected' : '';
        $select .= "<option value='{$i}' {$selected}>{$i}</option>";
    }
    $select .= '</select>';

    $stock_icon = '<svg class="custom-archive-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 16" width="18" height="18" fill="#198754" aria-hidden="true">
  <path d="M15.4.5H2.6C1.43.5.48 1.53.48 2.79v3.05h.708v10.7h15.6V5.84h.708V2.79c0-1.26-.953-2.29-2.12-2.29zm0 14.5H2.6V5.86h12.8V15zm.708-10.7h-14.2V2.78c0-.419.317-.762.708-.762h12.8c.391 0 .708.341.708.762V4.3z"></path>
</svg>';



	return $html . '<div class="mini-cart-qty-wrapper">
                    
                    ' . $select . '
                </div>';


}, 10, 3 );

// === Handle Ajax Cart Quantity Update ===
if ( ! function_exists( 'update_cart_quantity' ) ) {
    add_action( 'wp_ajax_update_cart_quantity', 'update_cart_quantity' );
    add_action( 'wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity' );

    function update_cart_quantity() {
        $cart_item_key = sanitize_text_field( $_POST['cart_item_key'] ?? '' );
        $quantity = intval( $_POST['quantity'] ?? 1 );

        foreach ( WC()->cart->get_cart() as $key => $item ) {
            if ( $key === $cart_item_key ) {
                WC()->cart->set_quantity( $cart_item_key, $quantity, true );
                break;
            }
        }

        WC()->cart->calculate_totals();
        WC()->cart->maybe_set_cart_cookies();

        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        wp_send_json_success( [
            'fragments' => [ 'div.widget_shopping_cart_content' => $mini_cart ]
        ] );
    }
}
