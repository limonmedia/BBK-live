<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Lipscore_Admin_Settings_Tab' ) ) :

class Lipscore_Admin_Settings_Tab {
    public function init( $settings_tabs ) {
        $settings_tabs['settings_tab_lipscore'] = __( 'Lipscore', 'woocommerce-settings-tab-lipscore' );
        return $settings_tabs;
    }

    public function update_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    public function tab_content() {
        echo "<div class=\"lipscore-settings\">";
        WC_Admin_Settings::output_fields( $this->get_settings() );
        echo "</div>";
    }

    public function add_assets() {
        wp_enqueue_style('lipscore-admin-styles', Lipscore::assets_url( 'css/admin.css' ), array(), Lipscore::VERSION );
        wp_enqueue_script(
            'lipscore-js', Lipscore::assets_url( 'js/admin.js' ), array( 'jquery' ), Lipscore::VERSION, true
        );
    }

    protected function get_settings() {
        $settings = array();

        $settings['general_title'] = $this->general_title();
        $settings['api_key']       = $this->api_key_setting();
        $settings['secret']        = $this->secret_setting();
        $settings['locale']        = $this->locale_setting();
        $settings['general_end']   = $this->general_section_end();

        $settings['product_attrs_title']       = $this->product_attrs_title();
        $settings['id']                        = $this->id_setting();
        $settings['gtin']                      = $this->gtin_setting();
        $settings['product_attrs_section_end'] = $this->product_attrs_section_end();

        $settings['product_bundles_title']       = $this->product_bundles_title();
        $settings['bundle']                    = $this->bundle_setting();
        $settings['product_bundles_section_end'] = $this->product_bundles_section_end();

        $settings['emails_title'] = $this->emails_title();
        $settings['order_status'] = $this->order_status_setting();
        $settings['emails_end']   = $this->emails_section_end();

        $settings['coupons_title']      = $this->coupons_title();
        $settings['coupon_code']        = $this->coupon_code_setting();
        $settings['coupon_description'] = $this->coupon_description_setting();

        $settings['coupons_end']   = $this->coupons_section_end();

        $settings['appearance_title']   = $this->appearance_title();
        $settings['disaplay_ratings']   = $this->disaplay_ratings_setting();
        $settings['disaplay_reviews']   = $this->disaplay_reviews_setting();
        $settings['disaplay_questions'] = $this->disaplay_questions_setting();
        $settings['appearance_end']     = $this->appearance_section_end();

        return apply_filters( 'wc_settings_tab_lipscore_settings', $settings );
    }

    protected function general_title() {
        return array(
            'name'     => __( 'General settings', 'woocommerce-settings-tab-lipscore' ),
            'type'     => 'title',
            'desc'     => 'Advanced settings are available on your <a href="https://members.lipscore.com/">Lipscore Dashboard</a>',
            'id'       => 'lipscore_general_title'
        );
    }

    protected function api_key_setting() {
        $description = '';
        if ( Lipscore_Settings::is_default_api_key() ) {
            $description = __( '<span class="notice notice-warning notice-alt">Your Lipscore installation is set up using a Demo Account. Please sign up with your own account on <a href="http://lipscore.com/" target="_blank">www.lipscore.com</a> to get access to all available features.', 'woocommerce-settings-tab-lipscore</span>' );
        }

        return array(
            'name'      => __( 'API Key', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'text',
            'desc'      => $description,
            'id'        => 'lipscore_api_key',
            'default'   => Lipscore_Settings::DEFAULT_API_KEY,
        );
    }

    protected function secret_setting() {
        return array(
            'name'      => __( 'Secret API Key', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'text',
            'id'        => 'lipscore_secret',
            'class'     => 'js-ls-no-autocomplete-field'
        );
    }

    protected function locale_setting() {
        return array(
            'name'      => __( 'Interface Locale', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'select',
            'id'        => 'lipscore_locale',
            'default'   => Lipscore_Settings::DEFAULT_LOCALE,
            'options'   => array(
                'cz'   => __( 'Czech', 'woocommerce-settings-tab-lipscore' ),
                'dk'   => __( 'Danish', 'woocommerce-settings-tab-lipscore' ),
                'nl'   => __( 'Dutch', 'woocommerce-settings-tab-lipscore' ),
                'en'   => __( 'English', 'woocommerce-settings-tab-lipscore' ),
                'et'   => __('Estonian', 'woocommerce-settings-tab-lipscore' ),
                'fi'   => __( 'Finnish', 'woocommerce-settings-tab-lipscore' ),
                'fr'   => __( 'French', 'woocommerce-settings-tab-lipscore' ),
                'de'   => __( 'German', 'woocommerce-settings-tab-lipscore' ),
                'it'   => __( 'Italian', 'woocommerce-settings-tab-lipscore' ),
                'ja'   => __( 'Japanese', 'woocommerce-settings-tab-lipscore' ),
                'lv'   => __( 'Latvian', 'woocommerce-settings-tab-lipscore' ),
                'no'   => __( 'Norwegian', 'woocommerce-settings-tab-lipscore' ),
                'br'   => __( 'Portuguese (Brazil)', 'woocommerce-settings-tab-lipscore' ),
                'ru'   => __( 'Russian', 'woocommerce-settings-tab-lipscore' ),
                'sk'   => __( 'Slovak', 'woocommerce-settings-tab-lipscore' ),
                'es'   => __( 'Spanish', 'woocommerce-settings-tab-lipscore' ),
                'se'   => __( 'Swedish', 'woocommerce-settings-tab-lipscore' ),
                'pl'   => __( 'Polish', 'woocommerce-settings-tab-lipscore' ),
            )
        );
    }

    protected function general_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_general_section_end'
        );
    }

    protected function product_attrs_title() {
        return array(
            'name' => __( 'Product Attributes', 'woocommerce-settings-tab-lipscore' ),
            'type' => 'title',
            'id'   => 'lipscore_product_attrs_title'
        );
    }

    protected function product_bundles_title() {
        return array(
            'name' => __( 'Product Bundles', 'woocommerce-settings-tab-lipscore' ),
            'type' => 'title',
            'id'   => 'lipscore_product_bundles_title'
        );
    }

    protected function bundle_setting() {
        $options = array( '' => __( '&mdash; Select &mdash;', 'woocommerce-settings-tab-lipscore' ) );

        $options['one'] = __('One item', 'woocommerce-settings-tab-lipscore' );
        $options['all'] = __('All products separatly', 'woocommerce-settings-tab-lipscore' );

        return array(
            'name'    => __( 'Create invitations for', 'woocommerce-settings-tab-lipscore' ),
            'type'    => 'select',
            'id'      => 'lipscore_bundle_invitations',
            'default' => '',
            'options' => $options
        );
    }

    protected function id_setting() {
        $options = array( '' => __( '&mdash; Select &mdash;', 'woocommerce-settings-tab-lipscore' ) );

        $attributes = wc_get_attribute_taxonomies();
        $options['product_id'] = __('ID', 'woocommerce-settings-tab-lipscore' );
        $options['sku'] = __('SKU', 'woocommerce-settings-tab-lipscore' );

        foreach ( $attributes as $attribute ) {
            $options[$attribute->attribute_name] = $attribute->attribute_label;
        }

        global $wpdb;
        $post_meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM {$wpdb->postmeta} ORDER BY meta_key");

        foreach ( $post_meta_keys as $post_meta_key ) {
            $options[$post_meta_key] = __( $post_meta_key, 'woocommerce' );
        }

        return array(
            'name'    => __( 'ID attribute', 'woocommerce-settings-tab-lipscore' ),
            'type'    => 'select',
            'id'      => 'lipscore_id',
            'default' => '',
            'options' => $options
        );
    }


    protected function gtin_setting() {
        $options = array( '' => __( '&mdash; Select &mdash;', 'woocommerce-settings-tab-lipscore' ) );

        $options['sku'] = __('SKU', 'woocommerce-settings-tab-lipscore' );
        $options['product_id'] = __('ID', 'woocommerce-settings-tab-lipscore' );

        $attributes = wc_get_attribute_taxonomies();
        foreach ( $attributes as $attribute ) {
            $options[$attribute->attribute_name] = $attribute->attribute_label;
        }

        global $wpdb;
        $post_meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM {$wpdb->postmeta} ORDER BY meta_key");
        
        foreach ( $post_meta_keys as $post_meta_key ) {
            $options[$post_meta_key] = __( $post_meta_key, 'woocommerce' );
        }

        return array(
            'name'    => __( 'GTIN attribute', 'woocommerce-settings-tab-lipscore' ),
            'type'    => 'select',
            'id'      => 'lipscore_gtin',
            'default' => '',
            'options' => $options
        );
    }

    protected function product_attrs_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_product_attrs_section_end'
        );
    }

    protected function product_bundles_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_product_bundles_section_end'
        );
    }

    protected function emails_title() {
        return array(
            'name'     => __( 'Review Request Emails', 'woocommerce-settings-tab-lipscore' ),
            'type'     => 'title',
            'desc'     => 'The single most important feature to get ratings and reviews is to send existing customers Review Request Emails after the customer has received the product.<br/>Please choose which order status that triggers these emails (previews can be seen in your <a href="https://members.lipscore.com/">Lipscore Dashboard</a>)',
            'id'       => 'lipscore_emails_title'
        );
    }

    protected function order_status_setting() {
        $statuses = wc_get_order_statuses();

        return array(
            'name'      => __( 'Order status', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'select',
            'desc'      => 'Create Lipscore review invitation emails when orders reach this status',
            'id'        => 'lipscore_order_status',
            'default'   => Lipscore_Settings::DEFAULT_ORDER_STATUS,
            'options'   => $statuses
        );
    }

    protected function emails_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_emails_section_end'
        );
    }

    protected function coupons_title() {
        return array(
            'name'     => __( 'Coupons', 'woocommerce-settings-tab-lipscore' ),
            'type'     => 'title',
            'desc'     => 'Coupons are a great way to give customers an incentive to write a review. Create a coupon code in the "Coupons" section and paste it here along with a description. The code will be emailed to your customers after their review has been submitted.',
            'id'       => 'lipscore_coupons_title'
        );
    }

    protected function coupon_code_setting() {
        return array(
            'name'      => __( 'Coupon code', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'text',
            'id'        => 'lipscore_coupon_code',
            'default'   => Lipscore_Settings::DEFAULT_COUPON_CODE
        );
    }

    protected function coupon_description_setting() {
        return array(
            'name'      => __( 'Coupon description', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'textarea',
            'id'        => 'lipscore_coupon_description',
            'default'   => Lipscore_Settings::DEFAULT_COUPON_DESCRIPTION
        );
    }

    protected function coupons_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_coupons_section_end'
        );
    }

		protected function appearance_title() {
        return array(
            'name'     => __( 'Appearance', 'woocommerce-settings-tab-lipscore' ),
            'type'     => 'title',
            'id'       => 'lipscore_appearance_title'
        );
    }

		protected function disaplay_ratings_setting() {
        return array(
            'name'      => __( 'Display ratings', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'checkbox',
            'id'        => 'lipscore_disaplay_ratings',
            'default'   => Lipscore_Settings::DEFAULT_DISPLAY_RATINGS
        );
    }

    protected function disaplay_reviews_setting() {
        return array(
            'name'      => __( 'Display reviews', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'checkbox',
            'id'        => 'lipscore_disaplay_reviews',
            'default'   => Lipscore_Settings::DEFAULT_DISPLAY_REVIEWS
        );
    }

    protected function disaplay_questions_setting() {
        return array(
            'name'      => __( 'Display Q&A', 'woocommerce-settings-tab-lipscore' ),
            'type'      => 'checkbox',
            'id'        => 'lipscore_disaplay_questions',
            'default'   => Lipscore_Settings::DEFAULT_DISPLAY_QUESTIONS
        );
    }

    protected function appearance_section_end() {
        return array(
            'type' => 'sectionend',
            'id'   => 'lipscore_coupons_section_end'
        );
    }
}

endif;
