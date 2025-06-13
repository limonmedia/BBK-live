<?php

 


##############################################################

function woo_discount_notice() {

    $user_id = get_current_user_id();

    if ( !get_user_meta( $user_id, 'woo_discount_notice_dismissed' ) && ( get_option('count_load_time_woo_discount') > get_user_meta( $user_id, 'woo_discount_notice_maybe_delay', true)   ) ) 
    

        echo '<div class="notice updated">
               
                <p>
                    
                    <span style="padding: 62px; ">

                    '. get_user_meta( $user_id, 'woo_discount_notice_dismissed', true ) .' 
        
                    '. __( "Enjoying the WooCommerce Discount Price Plugin and you would like to leave a five star review?", "woo-discount-price" ) .'
                                                   
                        
                                <a href="https://wordpress.org/support/plugin/woo-discount-price/reviews/?filter=5" target="_blank" ><span class="dashicons dashicons-external" style="text-decoration: none"></span>   '. __( "Sure!", "woo-discount-price" ).'</a>
                        
                                <span> ||| </span>

                                <a href="'.admin_url().'?woo_discount-maybe"> <span class="dashicons dashicons-clock" style="text-decoration: none"></span> '. __( " Maybe later", "woo-discount-price" ) .'</a>

                                <span> ||| </span>

                                <a href="'.admin_url().'?woo_discount-dismissed" > <span class="dashicons dashicons-yes-alt" style="text-decoration: none"></span> '. __( "I've already done it! ", "woo-discount-price" ).'</a>

                                <span> ||| </span>
                               
                                <a href="'.admin_url().'?woo_discount-dismissed" > <span class="dashicons dashicons-dismiss" style="text-decoration: none"></span> '. __( "No, thanks ", "woo-discount-price" ).'</a>
                    
                    </span>
            
                </p>
            
            </div>';

}





##########################################################
   
      
   
    function woo_discount_notice_maybe() {
    
        $user_id = get_current_user_id();
    
        if ( isset( $_GET['woo_discount-maybe'] ) )

            if (get_user_meta( $user_id, 'woo_discount_notice_maybe_delay') > 0) {

                $count = get_option('count_load_time_woo_discount');

                $delay = $count + 200;

                update_user_meta( $user_id, 'woo_discount_notice_maybe_delay', $delay );

            }

            
    }

    add_action( 'admin_init', 'woo_discount_notice_maybe' );

    
#####################################################

   
    function woo_discount_notice_dismissed() {

        $user_id = get_current_user_id();
        
        if ( isset( $_GET['woo_discount-dismissed'] ) ) {
        
            add_user_meta( $user_id, 'woo_discount_notice_dismissed', 'true', true );

            delete_user_meta( $user_id, 'woo_discount_notice_maybe_delay');

            delete_option('count_load_time_woo_discount');

       }

    }
    
    add_action( 'admin_init', 'woo_discount_notice_dismissed' );


#####################################################


    add_action( 'admin_init', 'count_woo_discount_function');



    function count_woo_discount_function() {

        $user_id = get_current_user_id();
       
       
            if (!get_option('count_load_time_woo_discount') && !get_user_meta( $user_id, 'woo_discount_notice_dismissed' ) ) {

                update_option('count_load_time_woo_discount', 1);

            } elseif (get_option('count_load_time_woo_discount') > 0 && !get_user_meta( $user_id, 'woo_discount_notice_dismissed' ) ) {

                $count = get_option('count_load_time_woo_discount');

                update_option('count_load_time_woo_discount', $count +1);

            }

            if (get_option('count_load_time_woo_discount') > 100) {

            add_action( 'admin_notices', 'woo_discount_notice' );

            }

    }
    

