<?php
// // add_filter('login_with_vipps_woo_login_redirect', 'custom_vipps_login_redirect', 10, 3);
// add_filter('login_with_vipps_woo_login_redirect', 'custom_vipps_login_redirect', 10, 3);

// function custom_vipps_login_redirect($link, $user, $session) {
    
//     $link = home_url('/my-account/');
  
    
//     return $link; 
// }
// do_action('continue_with_vipps_before_login_redirect', $user, $session);


add_action('continue_with_vipps_before_login_redirect', 'custom_vipps_login_redirect', 10, 2);

function custom_vipps_login_redirect($user, $session) {
    // Redirect all users (even admin) to My Account page
    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
}
