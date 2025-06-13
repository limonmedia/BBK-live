<?php

namespace KrokedilAvardaDeps\Krokedil\Shipping\Frontend;

use KrokedilAvardaDeps\Krokedil\Shipping\AJAX;
use KrokedilAvardaDeps\Krokedil\Shipping\SessionHandler;
use KrokedilAvardaDeps\Krokedil\Shipping\Interfaces\PickupPointServiceInterface;
/**
 * Class PickupPointSelect
 *
 * Handles the rendering and logic of the pickup point select box on the checkout page.
 */
class PickupPointSelect
{
    /**
     * The PickupPoints service instance.
     *
     * @var PickupPointServiceInterface
     */
    public $pickup_point_service;
    /**
     * Class constructor.
     *
     * @param PickupPointServiceInterface $pickup_point_service The PickupPoint service instance.
     *
     * @return void
     */
    public function __construct($pickup_point_service)
    {
        $this->pickup_point_service = $pickup_point_service;
        $this->register_ajax_requests();
        add_action('woocommerce_after_shipping_rate', array($this, 'render'));
    }
    /**
     * Register the AJAX requests required for the pickup point selection.
     *
     * @return void
     */
    private function register_ajax_requests()
    {
        /**
         * The Ajax service from the pickup point service container.
         *
         * @var AJAX $registry The Ajax service.
         */
        $registry = $this->pickup_point_service->get_container()->get('ajax');
        // Register the AJAX request to set the selected pickup point.
        $registry->add_ajax_event('ks_pp_set_selected_pickup_point', array($this, 'set_selected_pickup_point_ajax'));
    }
    /**
     * Render the pickup point select box.
     *
     * @param \WC_Shipping_Rate $shipping_rate WooCommerce shipping rate instance.
     * @return void
     */
    public function render($shipping_rate)
    {
        // Only if this is the selected shipping rate.
        WC()->session->get('chosen_shipping_methods');
        if (!\in_array($shipping_rate->get_id(), WC()->session->get('chosen_shipping_methods'), \true)) {
            return;
        }
        // Get the pickup points for the shipping method.
        $pickup_points = $this->pickup_point_service->get_pickup_points_from_rate($shipping_rate);
        // If there are no pickup points, return.
        if (empty($pickup_points)) {
            return;
        }
        $rate_id = $shipping_rate->get_id();
        // Get the selected pickup point for the shipping method.
        $selected_pickup_point = $this->pickup_point_service->get_selected_pickup_point_from_rate($shipping_rate);
        // Get the pickup point select box template.
        $template_path = apply_filters('krokedil_shipping_pickup_point_select_template_path', __DIR__ . '/../templates/html-pickup-point-select.php');
        include $template_path;
    }
    /**
     * Ajax callback handler to set the selected pickup point for the shipping method.
     *
     * @return void
     */
    public function set_selected_pickup_point_ajax()
    {
        $pickup_point_id = \filter_var($_POST['pickupPointId'] ?? '', \FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // phpcs:ignore
        $rate_id = \filter_var($_POST['rateId'] ?? '', \FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // phpcs:ignore
        // Ensure we have a pickup point id and a rate id.
        if (empty($pickup_point_id) || empty($rate_id)) {
            wp_send_json_error('Missing pickup point id or rate id.');
        }
        WC()->cart->calculate_shipping();
        $result = $this->set_selected_pickup_point($pickup_point_id, $rate_id);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success();
    }
    /**
     * Set the selected pickup point for the shipping method.
     * Returns a WP_Error if something went wrong. Otherwise true.
     *
     * @param string $pickup_point_id The id of the selected pickup point.
     * @param string $rate_id The id of the shipping rate.
     *
     * @return \WP_Error|bool
     */
    public function set_selected_pickup_point($pickup_point_id, $rate_id)
    {
        /**
         * Get the SessionHandler instance.
         *
         * @var SessionHandler $session_handler
         */
        $session_handler = $this->pickup_point_service->get_container()->get('session-handler');
        // Get the shipping rate and the selected pickup point for the shipping method from the ajax request.
        $shipping_rate = $session_handler->get_shipping_rate($rate_id);
        // If the shipping rate could not be found, return an error.
        if (!$shipping_rate) {
            return new \WP_Error('krokedil_shipping_set_selected_pickup_point', "Could not find a shipping rate with id: {$rate_id}");
        }
        $selected_pickup_point = $this->pickup_point_service->get_pickup_point_from_rate_by_id($shipping_rate, $pickup_point_id);
        // If we could not find a pickup point matching the selected id, return an error.
        if (!$selected_pickup_point) {
            return new \WP_Error('krokedil_shipping_set_selected_pickup_point', "Could not find a pickup point with id: {$pickup_point_id}");
        }
        // Save the selected pickup point to the rate.
        $this->pickup_point_service->save_selected_pickup_point_to_rate($shipping_rate, $selected_pickup_point);
        // Return true if everything went well.
        return \true;
    }
}
