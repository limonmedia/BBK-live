<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Config' ) ) :

class Lipscore_Config {
    const ENV_PRODUCTION  = 'production';
    const ENV_DEVELOPMENT = 'development';

    public function __construct() {
        $env = getenv( 'LIPSCORE_ENV' ) ? getenv( 'LIPSCORE_ENV' ) : self::ENV_PRODUCTION;
        Lipscore::include_file( 'config/' . $env );
    }

    public function assets_url() {
        return LIPSCORE_ASSETS_URL;
    }

    public function api_url() {
        return LIPSCORE_API_URL;
    }
}

endif;
