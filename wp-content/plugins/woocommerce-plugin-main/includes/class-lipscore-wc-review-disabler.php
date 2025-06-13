<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_WC_Review_Disabler' ) ) :

class Lipscore_WC_Review_Disabler {

    public function remove_metaboxes() {
        remove_meta_box( 'commentsdiv' , 'product' , 'normal' );
    }

    public function remove_dashboard_reviews() {
        remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
    }

    public function disable_product_comments( $open, $post_id ) {
        $post_type = get_post_type( $post_id );

    	if ($post_type == 'product') {
    		$open = false;
    	}

    	return $open;
    }
}

endif;
