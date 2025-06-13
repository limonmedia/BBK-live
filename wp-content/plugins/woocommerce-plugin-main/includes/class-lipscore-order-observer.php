<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Order_Observer' ) ) :

class Lipscore_Order_Observer {
    public function status_update( $order_id, $old_status, $new_status ) {
        $old_status = $this->normalize_status( $old_status );
        $new_status = $this->normalize_status( $new_status );

        if ( $old_status != $new_status && $this->is_reminder_required( $new_status ) ) {
            $order = new WC_Order( $order_id );
            if ( $order ) {
                $this->send_order( $order );
            }
        }
    }

    public function create( $order_id, $posted ) {
        $order = new WC_Order( $order_id );
        if ( ! $order ) {
            return;
        }

        $status = $this->normalize_status( $order->get_status() );
        if ( $this->is_reminder_required( $status ) ) {
            $this->send_order( $order );
        }
    }

    protected function is_reminder_required( $order_status ) {
        $reminderable_status = $this->normalize_status( Lipscore_Settings::order_status() );

        $is_valid_api_key    = Lipscore_Settings::is_valid_api_key();
        $is_valid_status     = ( $order_status == $reminderable_status);

        return $is_valid_api_key && $is_valid_status;
    }

    protected function normalize_status( $order_status ) {
        return str_replace( 'wc-', '',  $order_status);
    }

    protected function send_order ( $order ) {
        $reminder = new Lipscore_Order_Reminder();
        $reminder->send_order( $order );
    }
}

endif;
