<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyCustomers.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyCustomers
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyCustomers')):

    class IqxamplifySDK_Api_IqxamplifyCustomers
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            // User register
            add_action('user_register', array($this, 'iqxamplify_add_customer'), 10, 2);
            // User Update
            add_action('profile_update', array($this, 'iqxamplify_update_customer'), 10, 2);
            // User Delete
            add_action('delete_user', array($this, 'iqxamplify_delete_customer'), 10, 2);

            add_action('woocommerce_thankyou', array($this, 'iqxamplify_add_customer_from_order'));
        }

        /**
         * Processes customer queue.
         *
         * Process the customers that are in the queue.
         *
         * @since 1.0.0
         */
        public function process_queue()
        {
            $queue = get_option('_iqxamplify_queue', array());

            // Process products
            if (isset($queue['customers']) && is_array($queue['customers'])) {
                foreach (array_slice($queue['customers'], 0, 225, true) as $key => $customer) {
                    if ('delete' == $customer['action']) {
                        $response = $this->iqxamplify_delete_customer($customer['id'], null);
                    } elseif ('add' == $customer['action']) {
                        $response = $this->iqxamplify_add_customer($customer['id'], null);
                    } else {
                        $response = $this->iqxamplify_update_customer($customer['id'], null);
                    }

                    if (!is_wp_error($response) && in_array($response['response']['code'], array('200', '201', '204', '400', '404'))) { // Unset from queue when appropiate
                        unset($queue['customers'][ $key ]);
                    }
                }
            }

            update_option('_iqxamplify_queue', $queue);
        }

        /**
         * Add customer.
         *
         * @since 1.0.0
         *
         * @param $user_id
         * @param $old_user_data
         * @param null $source
         *
         * @return mixed
         */
        public function iqxamplify_add_customer($user_id, $old_user_data = null, $source = null)
        {
            $user_data = $this->get_customer($user_id);
            $response = IqxamplifyControl()->api->apiPost('customers/create_update', $user_data['customer'], $source);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['customers'][$user_id] = array('id' => $user_id, 'action' => 'add');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_user_meta($user_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
            }

            return $response;
        }

        /**
         * Add customer.
         *
         * @since 1.0.0
         *
         * @param $user_id
         * @param $old_user_data
         * @param null $source
         *
         * @return mixed
         */
        public function iqxamplify_add_customer_from_order($order_id, $source = null)
        {
            $order = new WC_Order($order_id);
            $user_id = $order->get_customer_id();
            $user_data = $this->get_customer($user_id);
            $response = IqxamplifyControl()->api->apiPost('customers/create_update', $user_data['customer'], $source);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['customers'][$user_id] = array('id' => $user_id, 'action' => 'add');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_user_meta($user_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
            }

            return $response;
        }

        /**
         * Update customer.
         *
         * @since 1.0.0
         *
         * @param $user_id
         * @param $old_user_data
         *
         * @return mixed
         */
        public function iqxamplify_update_customer($user_id, $old_user_data = null)
        {
            $user_data = $this->get_customer($user_id);
            $response = IqxamplifyControl()->api->apiPost('customers/create_update', $user_data['customer']);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['customers'][$user_id] = array('id' => $user_id, 'action' => 'update');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_user_meta($user_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
            }

            return $response;
        }

        /**
         * Delete customer.
         *
         * @since 1.0.0
         *
         * @param $user_id
         *
         * @return mixed
         */
        public function iqxamplify_delete_customer($user_id, $reassign = null)
        {
            $response = IqxamplifyControl()->api->apiDelete('customers/'.$user_id);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['customers'][$user_id] = array('id' => $user_id, 'action' => 'delete');
                update_option('_iqxamplify_queue', $queue);
            }

            return $response;
        }

        /**
         * Get all customers.
         *
         * @since 1.0.0
         *
         * @param array $fields
         * @param array $filter
         * @param int   $page
         *
         * @return array
         */
        public function get_customers($fields = null, $filter = array(), $page = 1)
        {
            $query = iqxamplify_query_customers($filter);
            $customers = array();
            foreach ($query->get_results() as $user_id) {
                $customers[] = $this->get_customer($user_id, $fields);
            }

            return array('customers' => $customers);
        }

        /**
         * Get the customer for the given ID.
         *
         * @since 1.0.0
         *
         * @param int   $id     the customer ID
         * @param array $fields
         *
         * @return array
         */
        public function get_customer($id, $fields = null)
        {
            global $wpdb;

            $customer = new WP_User($id);

            $customer_data = array(
                'ref_id' => intval($customer->ID),
                'email' => sanitize_email($customer->user_email),
                'first_name' => wp_filter_nohtml_kses($customer->first_name),
                'last_name' => wp_filter_nohtml_kses($customer->last_name),
                'signed_up_at' => wp_filter_nohtml_kses(iqxamplify_format_datetime($customer->user_registered, true)),
                'address1' => wp_filter_nohtml_kses($customer->billing_address_1),
                'address2' => wp_filter_nohtml_kses($customer->billing_address_2),
                'city' => wp_filter_nohtml_kses($customer->billing_city),
                'company' => wp_filter_nohtml_kses($customer->billing_company),
                'province' => wp_filter_nohtml_kses($customer->billing_state),
                'zip' => wp_filter_nohtml_kses($customer->billing_postcode),
                'country' => wp_filter_nohtml_kses($customer->billing_country),
                'phone' => wp_filter_nohtml_kses($customer->billing_phone),
                'orders_count' => intval(wc_get_customer_order_count($customer->ID)),
                'total_spent' => (float) wc_format_decimal(wc_get_customer_total_spent($customer->ID), 2),
            );

            return array('customer' => $customer_data);
        }

        /**
         * Create a customer.
         *
         * @since 1.0.0
         *
         * @param int   $id     the customer ID
         * @param array $fields
         *
         * @return array
         */
        public function create_customer($email, $first_name, $last_name, $shipping_address_1, $shipping_address_2, $shipping_country, $shipping_state, $shipping_city, $shipping_zip, $phone)
        {
            global $wpdb, $woocommerce;

            $password = wp_generate_password( 8, false );

            $username_unique = wp_generate_password( 4, false );

            $username = $first_name . '_' . $username_unique . '_' . $last_name;

            $user_id = wc_create_new_customer( $email, $username, $password );

            update_user_meta( $user_id, "first_name", $first_name );
            update_user_meta( $user_id, "last_name", $last_name );
            update_user_meta( $user_id, "display_name ", $first_name );

            update_user_meta( $user_id, "billing_first_name", $first_name );
            update_user_meta( $user_id, "billing_last_name", $last_name );
            update_user_meta( $user_id, "billing_address_1", $shipping_address_1 );
            update_user_meta( $user_id, "billing_address_2", $shipping_address_2 );
            update_user_meta( $user_id, "billing_country", $shipping_country );
            update_user_meta( $user_id, "billing_last_name", $shipping_state );
            update_user_meta( $user_id, "billing_state", $shipping_city );
            update_user_meta( $user_id, "billing_postcode", $shipping_zip );
            update_user_meta( $user_id, "billing_phone", $phone );

            update_user_meta( $user_id, "shipping_first_name", $first_name );
            update_user_meta( $user_id, "shipping_last_name", $last_name );
            update_user_meta( $user_id, "shipping_address_1", $shipping_address_1 );
            update_user_meta( $user_id, "shipping_address_2", $shipping_address_2 );
            update_user_meta( $user_id, "shipping_country", $shipping_country );
            update_user_meta( $user_id, "billing_last_name", $shipping_state );
            update_user_meta( $user_id, "shipping_state", $shipping_city );
            update_user_meta( $user_id, "shipping_postcode", $shipping_zip );

            $user_data = get_user_by('ID', $user_id);

            $username = $user_data->user_login;
            $key = get_password_reset_key( $user_data );

            $resetUrl = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($username), 'login');

            $mailer = $woocommerce->mailer();

            $messageBody = '<p>Hi '.$first_name.', thank you for your recent purchase through Amplify. We really appreciate your ';
            $messageBody .= "business! </p>";

            $messageBody .= "<p>Your username is " . $username . " </p>";

            $messageBody .= "<p>To setup a new password for your account, please visit the following address. </p>";

            $messageBody .= '<p><a class="link" href="'. $resetUrl.'">Click here to set your password</a></p>';

            // Buffer
            ob_start();

            do_action('woocommerce_email_header', 'Activate your account for ' . get_bloginfo('name', 'display'), $email);

            echo $messageBody;

            do_action('woocommerce_email_footer');

            // Get contents
            $message = ob_get_clean();

            // Cliente email, email subject and message.
            $mailer->send($email, 'Activate your account for ' . get_bloginfo('name', 'display'), $message);


            $customer = new WP_User($user_id);

            $customer_data = array(
                'ref_id' => intval($customer->ID),
                'email' => sanitize_email($customer->user_email),
                'first_name' => wp_filter_nohtml_kses($customer->first_name),
                'last_name' => wp_filter_nohtml_kses($customer->last_name),
                'signed_up_at' => wp_filter_nohtml_kses(iqxamplify_format_datetime($customer->user_registered, true)),
                'address1' => wp_filter_nohtml_kses($customer->billing_address_1),
                'address2' => wp_filter_nohtml_kses($customer->billing_address_2),
                'city' => wp_filter_nohtml_kses($customer->billing_city),
                'company' => wp_filter_nohtml_kses($customer->billing_company),
                'province' => wp_filter_nohtml_kses($customer->billing_state),
                'zip' => wp_filter_nohtml_kses($customer->billing_postcode),
                'country' => wp_filter_nohtml_kses($customer->billing_country),
                'phone' => wp_filter_nohtml_kses($customer->billing_phone),
                'orders_count' => intval(wc_get_customer_order_count($customer->ID)),
                'total_spent' => (float) wc_format_decimal(wc_get_customer_total_spent($customer->ID), 2),
            );

            return array('customer' => $customer_data);
        }

        /**
         * Get the customer for the given ID.
         *
         * @since 1.0.0
         *
         * @param int   $id     the customer ID
         * @param array $fields
         *
         * @return array
         */
        public function white_list_customer($id)
        {
            update_user_meta($id, '_iqxamplify_user_white_listed', true);

            return array('white_listed' => true);
        }
    }

endif;
