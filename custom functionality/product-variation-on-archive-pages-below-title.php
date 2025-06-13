<?php 

add_action( 'woocommerce_shop_loop_item_title', 'show_product_variations_below_title', 11 );

function show_product_variations_below_title() {
    global $product;

    if ( $product->is_type( 'variable' ) ) {
        $available_variations = $product->get_available_variations();
        $attributes_display = [];

        foreach ( $available_variations as $variation ) {
            foreach ( $variation['attributes'] as $attr_slug => $term_slug ) {
                if ( ! empty( $term_slug ) ) {
                    $taxonomy = str_replace( 'attribute_', '', $attr_slug );
                    $term = get_term_by( 'slug', $term_slug, $taxonomy );
                    if ( $term && ! in_array( $term->name, $attributes_display ) ) {
                        $attributes_display[] = $term->name;
                    }
                }
            }
        }

        // Sort alphabetically
        sort( $attributes_display, SORT_NATURAL | SORT_FLAG_CASE );

        if ( ! empty( $attributes_display ) ) {
            echo '<div class="product-variations" style="font-size: 10px; margin-top: 5px;">' . implode( ', ', $attributes_display ) . '</div>';
        }
    }
}
////BACKUP BELOW
// add_action( 'woocommerce_shop_loop_item_title', 'show_product_variations_below_title', 11 );

// function show_product_variations_below_title() {
//     global $product;

//     if ( $product->is_type( 'variable' ) ) {
//         $available_variations = $product->get_available_variations();
//         $variations_output   = '';

//         foreach ( $available_variations as $variation ) {
//             // Skip any variation that is out of stock
//             if ( isset( $variation['is_in_stock'] ) && ! $variation['is_in_stock'] ) {
//                 continue;
//             }

//             $variation_attributes = $variation['attributes'];

//             foreach ( $variation_attributes as $attribute_value ) {
//                 if ( ! empty( $attribute_value ) ) {
//                     $variations_output .= esc_html( $attribute_value ) . ', ';
//                 }
//             }
//         }

//         // Remove the trailing comma + space, if any
//         $variations_output = rtrim( $variations_output, ', ' );

//         if ( ! empty( $variations_output ) ) {
//             echo '<div class="product-variations" style="font-size:10px; margin-top:5px;">' 
//                  . $variations_output . 
//                  '</div>';
//         }
//     }
// }
