<?php

function display_user_valid_coupons_shortcode() {
    if (!is_user_logged_in()) {
        return '';
    }

    $user = wp_get_current_user();
    $user_email = $user->user_email;
    $user_id = $user->ID;

    // Get hidden coupons for this user
    $hidden_coupons = get_user_meta($user_id, 'hidden_coupons', true);
    if (!is_array($hidden_coupons)) {
        $hidden_coupons = [];
    }

    $args = array(
        'post_type'      => 'shop_coupon',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $coupons = get_posts($args);
    $valid_coupons = [];

    foreach ($coupons as $coupon_post) {
        $coupon = new WC_Coupon($coupon_post->post_title);

        // Skip if user has hidden this coupon
        if (in_array($coupon->get_code(), $hidden_coupons)) {
            continue;
        }

        $restricted_emails = $coupon->get_email_restrictions();
        $usage_limit_per_user = $coupon->get_usage_limit_per_user();

        $is_user_allowed = false;

        if (!empty($restricted_emails)) {
            if (in_array($user_email, $restricted_emails)) {
                $is_user_allowed = true;
            }
        } else {
            continue;
        }

        if ($is_user_allowed && $usage_limit_per_user > 0) {
            $user_usage_count = $coupon->get_data_store()->get_usage_by_user_id($coupon->get_id(), $user_id);
            if ($user_usage_count >= $usage_limit_per_user) {
                $is_user_allowed = false;
            }
        }

        if ($is_user_allowed) {
            $discounts = new WC_Discounts(WC()->cart);
            $result = $discounts->is_coupon_valid($coupon);

            if (!is_wp_error($result)) {
                $valid_coupons[] = $coupon;
            }
        }
    }

    $output = '';
    if (!empty($valid_coupons)) {
        $output .= '<div style="display: flex; flex-wrap: nowrap; gap: 20px; margin-top: 10px; justify-content: flex-start;">';

        $counter = 0;

        foreach ($valid_coupons as $coupon) {
            $amount = $coupon->get_amount();
            $discount_type = $coupon->get_discount_type();
            $description = $coupon->get_description();
            $coupon_id = 'coupon-code-' . $counter;
            $coupon_code = $coupon->get_code();

            $output .= '<div class="coupon-card" data-coupon="' . esc_attr($coupon_code) . '" id="card-' . esc_attr($coupon_id) . '" style="width: 200px; background: white; border: 1px solid #ddd; padding: 5px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); box-sizing: border-box;">';
            $output .= '<div style="background: #eae8ed; color: #d20a11; padding: 10px; border-radius: 8px; text-align: center;">';

            if ($discount_type === 'percent') {
                $output .= '<div style="font-size: 50px; font-family:Barlow; font-weight: bold; color: #d20a11;">-' . esc_html($amount) . '%</div>';
            } elseif ($discount_type === 'fixed_cart') {
                $output .= '<div style="font-size: 50px; font-family:Barlow; font-weight: bold; color: #d20a11;">' . wc_price($amount) . '</div>';
                $output .= '<div style="font-size: 20px; font-family:Barlow;">off your cart*</div>';
            } elseif ($discount_type === 'fixed_product') {
                $output .= '<div style="font-size: 50px; font-family:Barlow; font-weight: bold; color: #d20a11;">' . wc_price($amount) . '</div>';
                $output .= '<div style="font-size: 20px; font-family:Barlow;">off per item*</div>';
            }

            $output .= '</div>';
            $output .= '<div style="padding: 15px; color: #333;">';
            $output .= '<p style="font-size: 8px; text-align:center; color: #666; font-family:Barlow;">';

            if ($description) {
                $output .= esc_html($description) . '<br>';
            } else {
                if ($discount_type === 'percent') {
                    $output .= 'Vi har gitt deg en rabattkode på '.esc_html($amount) . '% i vår nettbutikk.<br>';
                } elseif ($discount_type === 'fixed_cart') {
                    $output .= wc_price($amount) . ' off your cart*<br>';
                } elseif ($discount_type === 'fixed_product') {
                    $output .= wc_price($amount) . ' off per item*<br>';
                }
            }

            $output .= '</p>';
            $output .= '<div style="text-align: center;">';
            $output .= '<button onclick="showCouponCode(\'' . esc_js($coupon_id) . '\', \'' . esc_js($coupon_code) . '\')" style="font-size: 10px; background: white; color: black; border: 1px solid black; font-family:Barlow; padding: 5px 5px; border-radius: 25px; cursor: pointer; width: 50%;">AKTIVER</button>';
            $output .= '</div>';
            
//             $output .= '<p style="font-size: 10px; font-family:Barlow; background: white; color: #d20a11; margin-top:10; text-align: center; width: 100%;">Copy the code it will disappear within 1 minute</p>';
            $output .= '</div>';

            $output .= '<div style="margin-top: 20px; text-align: center;">';
            $output .= '<div id="' . esc_attr($coupon_id) . '" style="display: none; background: #d20a11; font-size: 12px; color: #fff; padding: 5px 0px; border-radius: 5px; margin-bottom: 10px; font-family:Barlow;">';
            $output .= '<strong>' . esc_html($coupon_code) . '</strong>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            $counter++;
        }

        $output .= '</div>';

        $output .= '<script>
        function showCouponCode(couponId, couponCode) {
            var couponBox = document.getElementById(couponId);
            if (couponBox) {
                couponBox.style.display = "block";
                setTimeout(function() {
                    couponBox.style.display = "none";
                    var card = document.querySelector("[data-coupon=\'" + couponCode + "\']");
                    if (card) card.style.display = "none";
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                   
                    xhr.send("action=hide_user_coupon&coupon_code=" + encodeURIComponent(couponCode));
                }, 60000);// 1 minute = 60000 ms
            } else {
                console.log("Coupon box not found for ID: " + couponId);
            }
        }
		// Hide Elementor heading div if coupons are available
        document.addEventListener("DOMContentLoaded", function() {
            var couponContainer = document.querySelector(".coupon-card");
            var elementorHeading = document.querySelector(".elementor-element-f487a4e");
            if (couponContainer && elementorHeading) {
                elementorHeading.style.display = "none";
            }
			});
        </script>';
    }

    return $output;
}
add_shortcode('display_user_coupons', 'display_user_valid_coupons_shortcode');

// AJAX: Save hidden coupon code in user meta
add_action('wp_ajax_hide_user_coupon', function () {
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    $user_id = get_current_user_id();
    $coupon_code = sanitize_text_field($_POST['coupon_code'] ?? '');

    if (!$coupon_code) {
        wp_send_json_error('No coupon code');
    }

    $hidden = get_user_meta($user_id, 'hidden_coupons', true);
    if (!is_array($hidden)) {
        $hidden = [];
    }

    if (!in_array($coupon_code, $hidden)) {
        $hidden[] = $coupon_code;
        update_user_meta($user_id, 'hidden_coupons', $hidden);
    }

    wp_send_json_success('Hidden');
});
