<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Order_Reminder' ) ) :

class Lipscore_Order_Reminder {
    const PARENT_SOURCE_NAME = 'wordpress';
    const PARENT_SOURCE_ID = 'woocomerce';

    protected $products_helper;

    public function __construct() {
        $this->products_helper = new Lipscore_Product_Helper();
    }

    public function send_order( $order ) {
        if ( !$order || !is_a( $order, 'WC_Order' ) ) {
            return;
        }

        $data = array(
            'purchase' => $this->order_data( $order ),
            'products' => $this->products_data( $order )
        );

        $this->send( $data );
    }

    /**
     * @param \WC_Order $order
     * @return array
     */
    public function order_data( $order ) {
        if ( $order->get_shipping_first_name() || $order->get_shipping_last_name() ) {
            $buyer_first_name = $order->get_shipping_first_name();
            $buyer_last_name  = $order->get_shipping_last_name();
        } else {
            $buyer_first_name = $order->get_billing_first_name();
            $buyer_last_name  = $order->get_billing_last_name();
        }

        return array(
            'buyer_email' => $order->get_billing_email(),
            'buyer_name' => sprintf('%s %s', $buyer_first_name, $buyer_last_name),
            'discount_descr' => Lipscore_Settings::coupon_description(),
            'discount_voucher' => Lipscore_Settings::coupon_code(),
            'purchased_at' => (int)strtotime($order->get_date_created()),
            'lang' => Lipscore_Settings::locale(),
            'internal_order_id' => (string)$order->get_id(),
            'internal_customer_id' => $order->get_customer_id(),
            'parent_source_id' => (string)self::PARENT_SOURCE_ID,
            'parent_source_name' => (string)self::PARENT_SOURCE_NAME,
            'source_id' => '',
            'source_name' => (string)get_bloginfo('name'),
        );
    }

    /**
     * @param \WC_Order $order
     * @return array
     */
    public function products_data( $order ) {
        $products_data = array();

        $bundleInviteType = get_option( 'lipscore_bundle_invitations', 'one' );

        $items = $order->get_items();
        
        foreach ($items as $item_id => $item) {
            $product_id = $item->get_product_id();
            $product = $item->get_product();

            $boundleItems = $item->get_meta( '_bundled_items' );
            $boundleParent = is_array($boundleItems) && count($boundleItems) > 0;
            $boundleItem = $item->get_meta( '_bundled_by' );

            //handle special cases first
            if ($boundleParent && $bundleInviteType !== 'one') {
                continue;
            }

            if ($boundleItem && $bundleInviteType !== 'all') {
                continue;
            }

            if (!$product) {
                continue;
            }

            $products_data[ $product_id ] = $this->products_helper->product_data( $product );
        }

        return array_values( $products_data );
    }

    protected function send( $data ) {
        if ( ! Lipscore_Settings::is_valid_api_key() ) {
            return;
        }

        $api_key = Lipscore_Settings::api_key();
        $secret  = Lipscore_Settings::secret();
        $api_url = lipscore()->config->api_url();

        wp_remote_post(
            "$api_url/purchases?api_key=$api_key",
            array(
                'timeout' => 3,
                'body'    => json_encode($data),
                'headers' => array(
                    'Content-Type'    => 'application/json',
                    'X-Authorization' => $secret
                )
            )
        );
    }
}

endif;
