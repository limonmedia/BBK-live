<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Settings' ) ) :

class Lipscore_Settings {
    const DEFAULT_API_KEY            = '8d11d10169159ba3da361b61';
    const DEFAULT_LOCALE             = 'en';
    const DEFAULT_ORDER_STATUS       = 'wc-completed';
    const DEFAULT_COUPON_CODE        = '';
    const DEFAULT_COUPON_DESCRIPTION = '';
    const DEFAULT_GTIN               = '';
    const DEFAULT_ID                 = '';
    const DEFAULT_DISPLAY_RATINGS    = 'yes';
    const DEFAULT_DISPLAY_REVIEWS    = 'yes';
    const DEFAULT_DISPLAY_QUESTIONS  = 'no';

    public static function api_key() {
        return get_option( 'lipscore_api_key', self::DEFAULT_API_KEY );
    }

    public static function secret() {
        return get_option( 'lipscore_secret', self::DEFAULT_API_KEY );
    }

    public static function locale() {
        return get_option( 'lipscore_locale', self::DEFAULT_LOCALE );
    }

    public static function id_attr() {
        return get_option( 'lipscore_id', self::DEFAULT_ID );
    }

    public static function gtin_attr() {
        return get_option( 'lipscore_gtin', self::DEFAULT_GTIN );
    }

    public static function order_status() {
        return get_option( 'lipscore_order_status', self::DEFAULT_ORDER_STATUS );
    }

    public static function coupon_code() {
        return get_option( 'lipscore_coupon_code', self::DEFAULT_COUPON_CODE );
    }

    public static function coupon_description() {
        return get_option( 'lipscore_coupon_description', self::DEFAULT_COUPON_DESCRIPTION );
    }

    public static function is_default_api_key() {
        return self::DEFAULT_API_KEY == self::api_key();
    }

    public static function is_valid_api_key() {
        return self::api_key() && !self::is_default_api_key();
    }

    public static function is_ratings_displayed() {
        return get_option( 'lipscore_disaplay_ratings', self::DEFAULT_DISPLAY_RATINGS ) == 'yes';
    }

    public static function is_reviews_displayed() {
        return get_option( 'lipscore_disaplay_reviews', self::DEFAULT_DISPLAY_REVIEWS ) == 'yes';
    }

    public static function is_questions_displayed() {
        return get_option( 'lipscore_disaplay_questions', self::DEFAULT_DISPLAY_QUESTIONS ) == 'yes';
    }
}

endif;
