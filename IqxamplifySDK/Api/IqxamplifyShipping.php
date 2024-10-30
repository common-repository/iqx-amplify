<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyShop.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyShop
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyShipping')):

    class IqxamplifySDK_Api_IqxamplifyShipping
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
        }

        /**
         * Init shop info.
         *
         * @since 1.0.0
         */
        public function calc_shipping_method($shipping_country, $shipping_state, $shipping_postcode)
        {
            $results = array();
            $package = array();
            $package['destination']['country'] = $shipping_country;
            $package['destination']['state'] = $shipping_state;
            $package['destination']['postcode'] = $shipping_postcode;
            $status_options = get_option('woocommerce_status_options', array());
            $shipping_zone = WC_Shipping_Zones::get_zone_matching_package($package);
            $all_methods = $shipping_zone->get_shipping_methods(true);
            foreach ($all_methods as $method) {
                if ($method->id == 'free_shipping') {
                    if ($method->availability == 'all') {
                        $results[$method->title] = '0';
                    } elseif ($method->availability == 'including') {
                        $fg = 0;
                        foreach ($method->countries as $country) {
                            if ($shipping_country == $country) {
                                $fg = 1;
                            }
                        }
                        if ($fg == 1) {
                            $results[$method->title] = '0';
                        }
                    } else {
                        $fg = 0;
                        foreach ($method->countries as $country) {
                            if ($shipping_country == $country) {
                                $fg = 1;
                            }
                        }
                        if ($fg == 0) {
                            $results[$method->title] = '0';
                        }
                    }
                    break;
                } else {
                    if ($method->availability == 'all') {
                        $total_cost = 0;
                        if ($method->fee != '') {
                            $total_cost = $method->fee;
                        }
                        if ($method->cost != '') {
                            $total_cost = $total_cost + $method->cost;
                        }
                        if ($method->cost_per_order != '') {
                            $total_cost = $total_cost + $method->cost_per_order;
                        }
                        $results[$method->title] = $total_cost;
                    } elseif ($method->availability == 'including') {
                        $fg = 0;
                        foreach ($method->countries as $country) {
                            if ($shipping_country == $country) {
                                $fg = 1;
                            }
                        }
                        if ($fg == 1) {
                            $total_cost = 0;
                            if ($method->fee != '') {
                                $total_cost = $method->fee;
                            }
                            if ($method->cost != '') {
                                $total_cost = $total_cost + $method->cost;
                            }
                            if ($method->cost_per_order != '') {
                                $total_cost = $total_cost + $method->cost_per_order;
                            }
                            $results[$method->title] = $total_cost;
                        }
                    } else {
                        $fg = 0;
                        foreach ($method->countries as $country) {
                            if ($shipping_country == $country) {
                                $fg = 1;
                            }
                        }
                        if ($fg == 0) {
                            $total_cost = 0;
                            if ($method->fee != '') {
                                $total_cost = $method->fee;
                            }
                            if ($method->cost != '') {
                                $total_cost = $total_cost + $method->cost;
                            }
                            if ($method->cost_per_order != '') {
                                $total_cost = $total_cost + $method->cost_per_order;
                            }
                            $results[$method->title] = $total_cost;
                        }
                    }
                }
            }

            return array('message' => $results);
        }
    }

endif;
