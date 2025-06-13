<?php

// better separated voice to check quicker what every single value in add_menu_page is

function woodiscpr_plugin_setup_menu() {
    $parent_slug= 'woocommerce';
    $page_title = 'Discount Price WooCommerce Admin Page';
    $menu_title = 'Discount Price';
	$capability = 'manage_options';
	$menu_slug  = 'woo-discount-price';
	$function   = 'woodiscpr_setup_page_display';

	add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
* Include the new Navigation Bar the Admin page.
*/

function add_woo_discount_to_woocommerce_navigation_bar() {

    if ( is_admin() ) {

    if ( function_exists( 'wc_admin_connect_page' ) ) {

        wc_admin_connect_page(
            
                        array(
					        'id'        => 'woo-discount-price',
					        'screen_id' => 'woocommerce_page_woo-discount-price',
                            'title'     => __( 'Discount Price', 'woo-discount-price' ),
           
                            'path'      => add_query_arg(
                                            array(
                                                'page' => 'woo-discount-price',
                                                //'tab'  => 'ordine',
                                            ),
                                            
                                            'admin.php' ),
                            
	        			)
        );
        
    }

    }
}

