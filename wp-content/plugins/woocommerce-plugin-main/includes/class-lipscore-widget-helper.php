<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Widget_Helper' ) ) :

class Lipscore_Widget_Helper {
    public function product_attrs( $productData ) {
        $attrs = array(
            'ls-product-name'   => $productData['name'],
            'ls-brand'          => $productData['brand'],
            'ls-sku'            => implode( ';', $productData['sku_values'] ),
            'ls-product-id'     => $productData['internal_id'],
            'ls-image-url'      => $productData['image_url'],
            'ls-price'          => $productData['price'],
            'ls-price-currency' => $productData['currency'],
            'ls-category'       => $productData['category'],
            'ls-gtin'           => $productData['gtin'],
            'ls-product-url'    => $productData['url'],
            'ls-variant-id'     => $productData['variant_id'],
            'ls-variant-name'   => $productData['variant_name']
        );
        return static::to_string( $attrs );
    }

    public function richsnippet_product_attrs( $productData ) {
        $attrs = array(
            'ls-description'  => $productData['description'],
            'ls-availability' => $productData['availability']
        );
        return static::to_string( $attrs );
    }

    protected function to_string( $attrs ) {
        $strAttrs = array();
        foreach ($attrs as $attr => $value) {
            $value = htmlspecialchars( $value );
            $strAttrs[] = "$attr=\"$value\"";
        }
        return implode( ' ', $strAttrs );
    }
}

endif;
