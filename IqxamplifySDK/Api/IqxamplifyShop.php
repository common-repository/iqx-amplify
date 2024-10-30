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
if (!class_exists('IqxamplifySDK_Api_IqxamplifyShop')):

    class IqxamplifySDK_Api_IqxamplifyShop
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            add_action('init', array($this, 'init_shop_info'));
        }

        /**
         * Update shop info to iqxamplify.
         *
         * @since 1.0.0
         */
        public function update_shop_info_to_iqxamplify()
        {
            $formattedPrice = null;
            $currency = null;

            if (IqxamplifyControl()->isWooCommerceActive()) {
                $formattedPrice = wc_price(11.11);
                $formattedPrice = str_replace('11.11', '{{amount}}', $formattedPrice);
                $formattedPrice = str_replace('11,11', '{{amount}}', $formattedPrice);
                $currency = get_woocommerce_currency();
            }

            $args = array(
                'absolute_path' => get_site_url(),
                'currency_format' => $formattedPrice,
                'currency' => $currency,
            );

            $response = IqxamplifyControl()->api->apiPut('shops', $args);

            return $response;
        }

        /**
         * Init shop info.
         *
         * @since 1.0.0
         */
        public function init_shop_info()
        {
            // Update shop domain
            if (get_option('iqxamplify_completed_update_shop_info', 0) != 1) {
                $response = $this->update_shop_info_to_iqxamplify();
                if (!is_wp_error($response) && in_array($response['response']['code'], array('200', '201'))) {
                    update_option('iqxamplify_completed_update_shop_info', 1);
                }
            }
        }
    }

endif;
