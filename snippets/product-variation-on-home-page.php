<?php
// Display Product Variations Below Title on Home Page Carousel
add_action( 'wp_footer', 'show_product_variations_in_carousel' );
function show_product_variations_in_carousel() {
    // Apply this only on the homepage
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.uc_image_carousel_content_inside').forEach(function (productContainer) {
                const productId = productContainer.querySelector('.woosq-btn')?.getAttribute('data-id');
                
                if (productId) {
                    const data = new FormData();
                    data.append('action', 'fetch_product_variations');
                    data.append('product_id', productId);

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: data
                    })
                    .then(response => response.text())
                    .then(variationsOutput => {
                        if (variationsOutput) {
                            const variationsElement = document.createElement('div');
                            variationsElement.className = 'product-variations';
                            variationsElement.style = 'font-size: 10px; margin-top: 1px;';
                            variationsElement.innerHTML = variationsOutput;

                            // Insert after the product title
                            const titleElement = productContainer.querySelector('.uc_post_title');
                            if (titleElement) {
                                titleElement.insertAdjacentElement('afterend', variationsElement);
                            }
                        }
                    });
                }
            });
        });
    </script>
    <?php
}

// AJAX Handler to Fetch Variations (only in‐stock)
add_action( 'wp_ajax_fetch_product_variations', 'fetch_product_variations' );
add_action( 'wp_ajax_nopriv_fetch_product_variations', 'fetch_product_variations' );

function fetch_product_variations() {
    if ( isset( $_POST['product_id'] ) ) {
        $product_id = intval( $_POST['product_id'] );
        $product    = wc_get_product( $product_id );

        if ( $product && $product->is_type( 'variable' ) ) {
            $available_variations = $product->get_available_variations();

            // Sort by attribute string (case‐insensitive)
            usort( $available_variations, function ( $a, $b ) {
                $attr_a = implode( ', ', array_values( $a['attributes'] ) );
                $attr_b = implode( ', ', array_values( $b['attributes'] ) );
                return strcasecmp( $attr_a, $attr_b );
            } );

            $variations_output = '';

            foreach ( $available_variations as $variation ) {
                // Skip if out of stock
                if ( isset( $variation['is_in_stock'] ) && ! $variation['is_in_stock'] ) {
                    continue;
                }

                $variation_attributes = $variation['attributes'];
                $attributes_output    = implode( ', ', array_values( $variation_attributes ) );

                if ( ! empty( $attributes_output ) ) {
                    $variations_output .= '<span>' . esc_html( $attributes_output ) . '</span>, ';
                }
            }

            $variations_output = rtrim( $variations_output, ', ' );

            if ( ! empty( $variations_output ) ) {
                echo $variations_output;
            }
        }
    }
    wp_die();
}
