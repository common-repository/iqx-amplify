<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IqxamplifyClasses_IqxamplifyControl' ) ) :

    class IqxamplifyClasses_IqxamplifyControl {
        /**
         * The single instance of the class
         */
        protected static $_instance = null;

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         */
        public function __construct()
        {
          $apiKey = sanitize_text_field( get_option( 'iqxamplify_api_key' ) );

            //API
            $this->api = new IqxamplifySDK_Api_IqxamplifyApi( $apiKey );

            if ( $apiKey ) {
                // Shop
                $this->shop = new IqxamplifySDK_Api_IqxamplifyShop();

                if ($this->isWooCommerceActive()) {
                    // Product
                    $this->products = new IqxamplifySDK_Api_IqxamplifyProducts();

                    // Product Category
                    $this->collections = new IqxamplifySDK_Api_IqxamplifyCollections();

                    // Collect
                    $this->collects = new IqxamplifySDK_Api_IqxamplifyCollects();

                    // Order
                    $this->orders = new IqxamplifySDK_Api_IqxamplifyOrders();

                    // Cart
                    $this->cart = new IqxamplifySDK_Api_IqxamplifyCart();

                    // Customer
                    $this->customers = new IqxamplifySDK_Api_IqxamplifyCustomers();

                    // White List Pages
                    $this->whiteList = new IqxamplifySDK_Api_IqxamplifyThankYou();

                    // shipping
                    $this->shipping = new IqxamplifySDK_Api_IqxamplifyShipping();
                }
            }
        }

        /**
         * Check if WooCommerce is active
         * @return bool
         */
        public function isWooCommerceActive()
        {
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                if ( ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
                    return false;
                }
            }

            return true;
        }
    }

endif;

if ( ! function_exists( 'IqxamplifyControl' ) ) {

    function IqxamplifyControl() {
        return IqxamplifyClasses_IqxamplifyControl::instance();
    }

    // Init snippet
    $snippetControl = new IqxamplifyClasses_IqxamplifySnippetControl();

}
