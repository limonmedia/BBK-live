<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Locale_Helper' ) ) :

class Lipscore_Locale_Helper {
    protected static $_available_locales = array('en', 'it', 'no', 'es', 'br', 'ru', 'se', 'cz', 'nl', 'dk', 'ja', 'de', 'fi', 'fr', 'pl');

    public static function shop_locale() {
        $locale_code = get_locale();
        list( $language, $region ) = explode( '_', $locale_code );

        $locale = self::available_locale($language);
        if ( is_null( $locale ) ) {
            $locale = self::available_locale( $region );
        }
        return $locale ? $locale : 'en';
    }

    protected static function available_locale( $language ) {
        $language = strtolower( $language );
        return in_array( $language, self::$_available_locales ) ? $language : null;
    }
}

endif;
