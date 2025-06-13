<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Product_Helper' ) ) :

class Lipscore_Product_Helper {
    /**
     * @param \WC_Product $product
     * @return array
     */
    public function product_data( $product ) {
        $parent_product_id = (int) $product->get_parent_id();
        $product_name = $product->get_title();
        $variant_name = '';
        $variant_id = '';

        if ( $parent_product_id > 0 ) {
            $parent = wc_get_product( $parent_product_id );
            $product_id = $this->product_id($parent);
            $variant_name = $product->get_name();
            $variant_id = $product->get_id();
        } else {
            $product_id = $this->product_id($product);
        }

        return array(
            'name'         => $product_name,
            'brand'        => $this->product_brand( $product ),
            'sku_values'   => [$product->get_sku()],
            'internal_id'  => $product_id,
            'url'          => get_permalink($product->get_id()),
            'image_url'    => $this->image_url( $product ),
            'price'        => $product->get_price(),
            'currency'     => get_woocommerce_currency(),
            'category'     => $this->product_category( $product ),
            'gtin'         => $this->product_gtin( $product ),
            'variant_name' => $variant_name,
            'variant_id'   => $variant_id
        );
    }


    public function richsnippet_product_data( $product ) {
        return array(
            'description'  => get_the_excerpt($product->get_id()),
            'availability' => $this->availability( $product )
        );
    }

    protected function product_category( $product ) {
        $terms = get_the_terms( $product->get_id(), 'product_cat' );

        if ( is_wp_error( $terms ) || empty( $terms ) || !is_array( $terms ) ) {
            return '';
        } else {
            $terms_names = array_map(
                function( $term ) {
                    return $term->name;
                },
                $terms
            );

            return implode( ', ', $terms_names );
        }
    }

    protected function product_brand( $product ) {
        $terms = get_the_terms( $product->get_id(), 'product_brand' );

        if ( is_wp_error( $terms ) || empty( $terms ) || !is_array( $terms ) ) {
            return '';
        } else {
            $terms_names = array_map(
                function( $term ) {
                    return $term->name;
                },
                $terms
            );

            return implode( ', ', $terms_names );
        }
    }

    /**
     * @param \WC_Product $product
     * @return mixed|string
     */
    protected function product_id( $product ) {
        $id_attr = Lipscore_Settings::id_attr();
        if (!$id_attr) {
            return '';
        }

        if ($id_attr == 'sku') {
            return $product->get_sku();
        }

        $id = $product->get_attribute($id_attr);
        if (!$id) {
            $id = get_post_meta($product->get_id(), $id_attr, true);
        }
        if (!$id) {
            $id = $product->get_id();
        }

        return $id;
    }

    protected function product_gtin( $product ) {
        $gtin_attr = Lipscore_Settings::gtin_attr();
        if (!$gtin_attr) {
            return '';
        }

        if ($gtin_attr == 'sku') {
            return $product->get_sku();
        }

        $gtin = $product->get_attribute($gtin_attr);
        if (!$gtin) {
            $gtin = get_post_meta($product->get_id(), $gtin_attr, true);
        }

        return $gtin;
    }

    protected function image_url ( $product ) {
        return wp_get_attachment_url( $product->get_image_id() );
    }

    /**
     * @param \WC_Product $product
     * @return string
     */
    protected function availability( $product ) {
        return $product->get_stock_quantity();
    }
}

endif;
