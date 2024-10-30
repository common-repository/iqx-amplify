<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyOrders.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyOrders
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyOrders')):

    class IqxamplifySDK_Api_IqxamplifyOrders
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            // Save user token on checkout
            add_action('woocommerce_checkout_update_order_meta', array($this, 'order_save_user_token'), 10, 2);

            // Check product stock, if empty update product
            add_action('woocommerce_reduce_order_stock', array($this, 'update_products_in_order'), 10, 1);
        }

        /**
         * Processes order queue.
         *
         * Process the orders that are in the queue.
         *
         * @since 1.0.0
         */
        public function process_queue()
        {
            $queue = get_option('_iqxamplify_queue', array());

            // Process products
            if (isset($queue['orders']) && is_array($queue['orders'])) {
                foreach (array_slice($queue['orders'], 0, 225, true) as $key => $order) {
                    if ('add' == $order['action']) {
                        $response = $this->add_order_to_iqxamplify($order['id']);
                    }

                    if (isset($response) && !is_wp_error($response) && in_array($response['response']['code'], array('200', '201', '204', '400', '404'))) { // Unset from queue when appropiate
                        unset($queue['orders'][ $key ]);
                    }
                }
            }

            update_option('_iqxamplify_queue', $queue);
        }

        /**
         * Save user token.
         *
         * Save the user token from the iqxamplify cookie at checkout.
         * After save it will immediately be deleted. When deleted it will
         * automatically re-generate a new one to track the new purchase flow.
         *
         * @since 1.0.0
         *
         * @param int   $order_id ID of the order that is being processed
         * @param array $posted   List of $_POST values
         */
        public function order_save_user_token($order_id, $posted)
        {
            if (isset($_COOKIE['iqxamplify-cart-token'])) {
                update_post_meta($order_id, '_iqxamplify_cart_token', $_COOKIE['iqxamplify-cart-token']);
                // Delete cart token
                setcookie('iqxamplify-cart-token', '', time() - IqxamplifySDK_Api_IqxamplifyApi::CART_TOKEN_LIFE_TIME + 1, '/');
            }

            $this->add_order_to_iqxamplify($order_id);
        }

        /**
         * Update order.
         *
         * @since 1.0.0
         *
         * @param $order_id
         *
         * @return mixed
         */
        public function create_order($product_ref_id, $profile_ref_id, $quantity, $shipping_address_1, $shipping_address_2, $shipping_country, $shipping_state, $shipping_city, $shipping_zip, $total, $tax_rate, $shipping_rate, $first_name, $last_name, $type, $variation_id)
        {
            global $woocommerce;

            $success = false;

            $productId = $product_ref_id;
            $userId = $profile_ref_id;

            $shipping_address_full = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => '',
                'address_1' => $shipping_address_1,
                'address_2' => $shipping_address_2,
                'city' => $shipping_city,
                'state' => $shipping_state,
                'postcode' => $shipping_zip,
                'country' => $shipping_country,
            );

            $billing_phone_number = wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_phone', true));

            $billing_address_full = array(
                'first_name' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_first_name', true)),
                'last_name' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_last_name', true)),
                'company' => '',
                'address_1' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_address_1', true)),
                'address_2' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_address_2', true)),
                'city' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_city', true)),
                'state' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_state', true)),
                'postcode' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_postcode', true)),
                'country' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_country', true)),
                'phone' => $billing_phone_number,
                'email' => wp_filter_nohtml_kses(get_user_meta($profile_ref_id, 'billing_email', true)),
            );

            if ($type == 'simple') {
                $myProduct = get_product($product_ref_id);
                $order = wc_create_order();
                $order->add_product($myProduct, $quantity);

                $order->set_address($shipping_address_full, 'shipping');
                $order->set_address($billing_address_full, 'billing');

              // $order->calculate_totals(); $total, $tax_rate, $shipping_rate
              $order->set_total($tax_rate, 'tax');
                $order->set_total($shipping_rate, 'shipping');
                $order->set_total($total, 'total');
                $order->add_shipping(new WC_Shipping_Rate('flat_rate_shipping', 'Flat rate shipping', '0', $shipping_rate, 'flat_rate'));
                update_post_meta($order->get_id(), '_customer_user', $profile_ref_id);
                update_post_meta($order->get_id(), '_is_iqx_order', true);
                $order->payment_complete();
                $woocommerce->cart->empty_cart();

                $woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order->get_id());

                return $this->get_formatted_order($order->id);
            } else {
                $myProduct = new WC_Product_Variation($variation_id);
                $order = wc_create_order();
                $order->add_product($myProduct, $quantity);
                $order->set_address($shipping_address_full, 'shipping');
                $order->set_address($billing_address_full, 'billing');

              // $order->calculate_totals(); $total, $tax_rate, $shipping_rate
              $order->set_total($tax_rate, 'tax');
                $order->set_total($shipping_rate, 'shipping');
                $order->set_total($total, 'total');
                $order->add_shipping(new WC_Shipping_Rate('flat_rate_shipping', 'Flat rate shipping', '0', $shipping_rate, 'flat_rate'));
                update_post_meta($order->get_id(), '_customer_user', $profile_ref_id);
                update_post_meta($order->get_id(), '_is_iqx_order', true);
                $order->payment_complete();
                $woocommerce->cart->empty_cart();

                $woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order->get_id());

                return $this->get_formatted_order($order->get_id());
            }
        }

        /**
         * Update order.
         *
         * @since 1.0.0
         *
         * @param $order_id
         *
         * @return mixed
         */
        public function create_refund($order_id, $type, $total)
        {
            global $woocommerce;

            $success = false;

            if ($type == 'full_refund_sent') {
                $refund = wc_create_refund(array('amount' => $total, 'reason' => __('Order Fully Refunded', 'woocommerce'), 'order_id' => $order_id, 'line_items' => array()));

                $woocommerce->mailer()->emails['WC_Email_Customer_Refunded_Order']->trigger_full($order_id, $refund->id);
            } else {
                $refund = wc_create_refund(array('amount' => $total, 'reason' => __('Order Partially Refunded', 'woocommerce'), 'order_id' => $order_id, 'line_items' => array()));

                $woocommerce->mailer()->emails['WC_Email_Customer_Refunded_Order']->trigger_partial($order_id, $refund->id);
            }

            return array(
                'ref_id' => $refund->id,
            );
        }

        /**
         * Update order.
         *
         * @since 1.0.0
         *
         * @param $order_id
         *
         * @return mixed
         */
        public function add_order_to_iqxamplify($order_id)
        {
            $order_data = $this->get_formatted_order($order_id);

            $response = IqxamplifyControl()->api->apiPost('orders/create_update', $order_data['order']);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['orders'][ $order_id ] = array('id' => $order_id, 'action' => 'add');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_post_meta($order_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
            }

            return $response;
        }

        /**
         * Update products.
         *
         * Maybe send a update to Iqxamplify. Check if the product is stock-managed,
         * when it is, a update will be send to Iqxamplify to make sure the product
         * is up to date.
         *
         * @since 1.0.0
         *
         * @param WC_Order $order Order object
         */
        public function update_products_in_order($order)
        {
            foreach ($order->get_items() as $item) {
                if ($item['product_id'] > 0) {
                    $_product = $order->get_product_from_item($item);

                    if ($_product && $_product->exists() && $_product->managing_stock()) {
                        // Sync this product
                        IqxamplifyControl()->products->update_product_to_iqxamplify($_product->id);
                    }
                }
            }
        }

        /**
         * Get all orders.
         *
         * @since 1.0.0
         *
         * @param string $fields
         * @param array  $filter
         * @param string $status
         * @param int    $page
         *
         * @return array
         */
        public function get_orders($fields = null, $filter = array(), $status = null, $page = 1)
        {
            if (!empty($status)) {
                $filter['status'] = $status;
            }
            $filter['page'] = $page;
            $query = iqxamplify_query_orders($filter);
            $orders = array();
            foreach ($query->posts as $order_id) {
                $orders[] = $this->get_formatted_order($order_id, $fields, $filter);
            }

            return array('orders' => $orders);
        }

        /**
         * Get the order for the given ID.
         *
         * @since 1.0.0
         *
         * @param int   $id     the order ID
         * @param array $fields
         * @param array $filter
         *
         * @return array
         */
        public function get_formatted_order($id, $fields = null, $filter = array())
        {
            // Get the decimal precession
            $dp = (isset($filter['dp']) ? intval($filter['dp']) : 2);
            $order = wc_get_order($id);

            if (!$order || !is_object($order)) {
                return array('order' => array());
            }

            $order_data = array(
                'ref_id' => intval($order->get_id()),
                'email' => sanitize_email($order->get_billing_email()),
                'contact_ref_id' => intval($order->get_user_id()),
                'financial_status' => wp_filter_nohtml_kses($order->get_status()),
                'line_items' => array(),
                'cart_token' => wp_filter_nohtml_kses(get_post_meta($id, '_iqxamplify_cart_token', true)),
                'total_price' => wp_filter_nohtml_kses($order->get_total()),
                'total_tax' => wp_filter_nohtml_kses($order->get_total_tax()),
                'total_shipping' => wp_filter_nohtml_kses($order->get_total_shipping()),
                'iqx_order' => wp_filter_nohtml_kses(get_post_meta($id, '_is_iqx_order', true)),
                'subtotal_price' => wp_filter_nohtml_kses($order->get_subtotal()),
                'processed_at' => iqxamplify_format_datetime($order->get_date_created()),
                'items' => array_filter($order->get_items()),
            );

            // Add contact info
            if ($order->get_user_id()) {
                $contact = IqxamplifyControl()->customers->get_customer($order->get_user_id());

                if (isset($contact['customer'])) {
                    $order_data['contact'] = $contact['customer'];
                }
            } else {
                $order_data['contact']['email'] = $order->get_billing_email();
            }

            $order_data['items'][] = $order->get_items();

            // add line items
            foreach ($order->get_items() as $item_id => $item) {
                $product = $order->get_product_from_item($item);
                $product_id = null;
                $product_sku = null;
                // Check if the product exists.
                if (is_object($product)) {
                    $product_sku = $product->get_sku();
                }

                $order_data['line_items'][] = array(
                    'ref_id' => $item_id,
                    'price' => wc_format_decimal($order->get_item_total($item, false, false), $dp),
                    'line_total' => wc_format_decimal($order->get_item_total($item, false, false), $dp),
                    'sku' => $product_sku,
                    'name' => wp_filter_nohtml_kses( $product->get_title()),
                    'product_ref_id' => $product->get_id(),
                    'variant_id' => (null !== $product->get_id()) ? $product->get_id() : null,
                    'type' => "line_item",
                    'fulfillable_quantity' => wc_stock_amount($item['qty']),
                );
            }

            return array('order' => $order_data);
        }
    }

endif;
