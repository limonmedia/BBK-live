<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Template' ) ) :

class Lipscore_Template {
    protected $args;
    protected $file;

    public $product_helper;
    public $widget_helper;

    public function __get( $name ) {
        return $this->args[$name];
    }

    public function __construct( $file, $args = array() ) {
        $this->file = $file;
        $this->args = $args;

        $this->product_helper = new Lipscore_Product_Helper();
        $this->widget_helper  = new Lipscore_Widget_Helper();
    }

    public function __isset( $name ) {
        return isset( $this->args[$name] );
    }

    public function render() {
        $file = Lipscore::dir( "templates/$this->file" . '.php' );
        if ( file_exists( $file ) ) {
            include( $file );
        }
    }
}

endif;
