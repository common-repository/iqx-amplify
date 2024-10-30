<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyApi.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyApi
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyApi')):

    class IqxamplifySDK_Api_IqxamplifyApi
    {
        const CART_TOKEN_LIFE_TIME = 31536000;

        const METHOD_GET = 'GET';

        const METHOD_POST = 'POST';

        const METHOD_DELETE = 'DELETE';

        const METHOD_PUT = 'PUT';

        const IQX_AMPLIFY_SOURCE = 'webhook';

        public $iqxamplifySourceSync = 'sync';

        /**
         * Iqxamplify Api Key.
         *
         * @since 1.0.0
         *
         * @var string
         */
        protected $apiKey;

        /**
         * Iqxamplify Api URL.
         *
         * @since 1.0.0
         *
         * @var string
         */
        protected $apiUrl;

        /**
         * @since 1.0.0
         *
         * @var
         */
        protected $client;

        /**
         * Constructor.
         *
         * @since 1.0.0
         *
         * @param $apiKey
         */
        public function __construct($apiKey)
        {
            $this->apiUrl = IQX_AMPLIFY_PATH.'/rest-api/v1/';
            $this->apiKey = $apiKey;
            add_action('init', array($this, 'listenCommand'));
        }

        /**
         * Set Api Key.
         *
         * @since 1.0.0
         *
         * @param $apiKey
         *
         * @return $this
         */
        public function setApiKey($apiKey)
        {
            $this->apiKey = $apiKey;

            return $this;
        }

        /**
         * Get Api Key.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getApiKey()
        {
            return $this->apiKey;
        }

        /**
         * Set api url.
         *
         * @since 1.0.0
         *
         * @param $url
         *
         * @return $this
         */
        public function setApiUrl($url)
        {
            $this->apiUrl = $url;

            return $this;
        }

        /**
         * Get api url.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getApiUrl()
        {
            return $this->apiUrl;
        }

        /**
         * Set http client.
         *
         * @since 1.0.0
         *
         * @param $client
         *
         * @return $this
         */
        public function setHttpClient($client)
        {
            $this->client = $client;

            return $this;
        }

        /**
         * Get http client.
         *
         * @since 1.0.0
         *
         * @return mixed
         */
        public function getHttpClient()
        {
            return $this->client;
        }

        /**
         * Listen command.
         *
         * @since 1.0.0
         * Listen to requests from Iqxamplify
         */
        public function listenCommand()
        {
            if (!isset($_GET['iqxamplify_send_action'])) {
                return;
            }

            $command = wp_filter_nohtml_kses($_GET['iqxamplify_send_action']);

            if ($command != 'white-list-number' && $command != 'white-list' && $command != 'capture-view' && $command != 'clicked_product_link') {
                if (!isset($_GET['access_token'])) {
                    wp_send_json_error(array('message' => 'Access token not found'));
                    die;
                }

                $access_token = wp_filter_nohtml_kses($_GET['access_token']);

                if ($access_token != $this->apiKey) {
                    wp_send_json_error(array('message' => 'Invalid access token'));
                    die;
                }
            }

            error_reporting(0);

            switch ($command) {
                case 'set-whitelist-email':
                    $value = wp_filter_nohtml_kses($_GET['value']);
                    update_option('iqxamplify_send_whitelist_email', $value);
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'set-whitelist-popup':
                    $value = wp_filter_nohtml_kses($_GET['value']);
                    update_option('iqxamplify_show_whitelist_popup', $value);
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'set-popup-details':
                    $popupHeadline = wp_filter_nohtml_kses($_GET['popup_headline']);
                    $popupSubHeadline = wp_filter_nohtml_kses($_GET['popup_sub_headline']);
                    $popupFill = wp_filter_nohtml_kses($_GET['popup_fill']);
                    $popupBorder = wp_filter_nohtml_kses($_GET['popup_border']);
                    $popupColour = wp_filter_nohtml_kses($_GET['popup_colour']);
                    $popupImage = esc_url($_GET['popup_image']);
                    update_option('iqxamplify_popup_headline', $popupHeadline);
                    update_option('iqxamplify_popup_sub_headline', $popupSubHeadline);
                    update_option('iqxamplify_popup_fill', $popupFill);
                    update_option('iqxamplify_popup_border', $popupBorder);
                    update_option('iqxamplify_popup_colour', $popupColour);
                    update_option('iqxamplify_popup_image', $popupImage);
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'sync-products':
                    // Reset complete syncing flag to wake it up
                    update_option('iqxamplify_completed_initial_product_sync', 0);
                    delete_option('iqxamplify_current_sync_time', time());
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'sync-collections':
                    if (1 == get_option('iqxamplify_completed_initial_collection_sync', 0)) {
                        // Reset complete syncing flag to wake it up
                        update_option('iqxamplify_completed_initial_collection_sync', 0);
                        delete_option('iqxamplify_current_sync_time', time());
                    }
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'sync-orders':
                    if (1 == get_option('iqxamplify_completed_initial_order_sync', 0)) {
                        // Reset complete syncing flag to wake it up
                        update_option('iqxamplify_completed_initial_order_sync', 0);
                        delete_option('iqxamplify_current_sync_time', time());
                    }
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'sync-collects':
                    if (1 == get_option('iqxamplify_completed_initial_order_sync', 0)) {
                        // Reset complete syncing flag to wake it up
                        update_option('iqxamplify_completed_initial_order_sync', 0);
                        delete_option('iqxamplify_current_sync_time', time());
                    }
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'sync-customers':
                    if (1 == get_option('iqxamplify_completed_initial_customer_sync', 0)) {
                        // Reset complete syncing flag to wake it up
                        update_option('iqxamplify_completed_initial_customer_sync', 0);
                        delete_option('iqxamplify_current_sync_time', time());
                    }
                    wp_send_json(array('success' => true));
                    die;
                    break;

                case 'get-customers':
                    $params = $_GET;
                    $params = $this->cleanUpParams($params);
                    $response = IqxamplifyControl()->customers->get_customers(null, null, $params);


                    $customers = array();
                    $customers[] = $response['customers'];
                    // for ($i = 0; $i < count($customers[0]); $i++) {
                    //
                    //     $customer = $customers[0][$i]['customer'];
                    //     $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);
                    //
                    //     $apiResponse = wp_remote_post($this->apiUrl.'customers/create_update.json', array(
                    //           'method' => self::METHOD_POST,
                    //           'timeout' => 45,
                    //           'redirection' => 5,
                    //           'httpversion' => '1.0',
                    //           'blocking' => true,
                    //           'headers' => $headers,
                    //           'body' => json_encode($customer),
                    //           'cookies' => array(),
                    //       )
                    //   );
                    // }
                    wp_send_json_success($response['customers']);
                    die;
                    break;

                case 'white-list-customer':
                    if (isset($_GET['id'])) {
                        $user_id = intval($_GET['id']);
                        $response = IqxamplifyControl()->customers->white_list_customer($user_id);
                        echo $response;
                    } else {
                        wp_send_json_error(array('message' => 'User not found'));
                        die;
                    }
                    die;
                    break;

                case 'white-list':
                    if (isset($_GET['id'])) {
                        $user_id = intval($_GET['id']);
                        $userUuid = wp_filter_nohtml_kses($_GET['uuid']);
                        $response = IqxamplifyControl()->whiteList->white_list_page($user_id, $userUuid);
                        echo $response;
                    } else {
                        wp_send_json_error(array('message' => 'User not found'));
                        die;
                    }

                    die;
                    break;

                case 'get-products':
                    $params = $_GET;
                    $params = $this->cleanUpParams($params);
                    $response = IqxamplifyControl()->products->get_products(null, null, $params);


                    $products = array();
                    $products[] = $response['products'];
                    // for ($i = 0; $i < count($products[0]); $i++) {
                    //
                    //     $product = $products[0][$i]['product'];
                    //     $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);
                    //
                    //     $apiResponse = wp_remote_post($this->apiUrl.'products/create_update.json', array(
                    //           'method' => self::METHOD_POST,
                    //           'timeout' => 45,
                    //           'redirection' => 5,
                    //           'httpversion' => '1.0',
                    //           'blocking' => true,
                    //           'headers' => $headers,
                    //           'body' => json_encode($product),
                    //           'cookies' => array(),
                    //       )
                    //   );
                    // }
                    wp_send_json_success($response['products']);
                    die;
                    break;

                case 'get-orders':
                    $params = $_GET;
                    $params = $this->cleanUpParams($params);
                    $response = IqxamplifyControl()->orders->get_orders();


                    $orders = array();
                    $orders[] = $response['orders'];
                    // s $key => $customer
                    // for ($i = 0; $i < count($orders[0]); $i++) {
                    //
                    //     $order = $orders[0][$i]['order'];
                    //     $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);
                    //
                    //     $apiResponse = wp_remote_post($this->apiUrl.'orders/create_update.json', array(
                    //           'method' => self::METHOD_POST,
                    //           'timeout' => 45,
                    //           'redirection' => 5,
                    //           'httpversion' => '1.0',
                    //           'blocking' => true,
                    //           'headers' => $headers,
                    //           'body' => json_encode($order),
                    //           'cookies' => array(),
                    //       )
                    //   );
                    // }
                    wp_send_json_success($response['orders']);
                    die;
                    break;

                case 'get-shipping-class':

                    if (!isset($_GET['shipping_country'])) {
                        wp_send_json_error(array('message' => 'country not found'));
                        die;
                    }

                    if (!isset($_GET['shipping_state'])) {
                        wp_send_json_error(array('message' => 'state not found'));
                        die;
                    }

                    if (!isset($_GET['shipping_postcode'])) {
                        wp_send_json_error(array('message' => 'Post code not found'));
                        die;
                    }

                    $shipping_country = wp_filter_nohtml_kses($_GET['shipping_country']);
                    $shipping_state = wp_filter_nohtml_kses($_GET['shipping_state']);
                    $shipping_postcode = wp_filter_nohtml_kses($_GET['shipping_postcode']);

                    $response = IqxamplifyControl()->shipping->calc_shipping_method($shipping_country, $shipping_state, $shipping_postcode);

                    wp_send_json_success($response['message']);
                    die;
                    break;

                case 'get-product':
                    if (!isset($_GET['id'])) {
                        wp_send_json_error(array('message' => 'Id not found'));
                        die;
                    }
                    $product = IqxamplifyControl()->products->get_formatted_product((int) $_GET['id']);
                    if (!$product) {
                        wp_send_json_error(array('Product not found'));
                        die;
                    }

                    wp_send_json_success($product['product']);
                    die;
                    break;

                case 'create_order':
                    if (!isset($_GET['product_ref_id'])) {
                        wp_send_json_error(array('message' => 'product Id not found'));
                        die;
                    }

                    if (!isset($_GET['profile_ref_id'])) {
                        wp_send_json_error(array('message' => 'profile Id not found'));
                        die;
                    }

                    if (!isset($_GET['total'])) {
                        wp_send_json_error(array('message' => 'total not found'));
                        die;
                    }

                    $product_ref_id = intval($_GET['product_ref_id']);
                    $profile_ref_id = intval($_GET['profile_ref_id']);
                    $first_name = wp_filter_nohtml_kses($_GET['first_name']);
                    $last_name = wp_filter_nohtml_kses($_GET['last_name']);
                    $shipping_address_1 = wp_filter_nohtml_kses($_GET['shipping_address_1']);
                    $shipping_address_2 = wp_filter_nohtml_kses($_GET['shipping_address_2']);
                    $shipping_state = wp_filter_nohtml_kses($_GET['shipping_state']);
                    $shipping_city = wp_filter_nohtml_kses($_GET['shipping_city']);
                    $shipping_zip = wp_filter_nohtml_kses($_GET['shipping_zip']);
                    $shipping_country = wp_filter_nohtml_kses($_GET['shipping_country']);
                    $quantity = intval($_GET['quantity']);
                    $total = floatval($_GET['total']);
                    $tax_rate = floatval($_GET['tax_rate']);
                    $shipping_rate = floatval($_GET['shipping_rate']);
                    $type = wp_filter_nohtml_kses($_GET['type']);
                    $variation_id = intval($_GET['variation_id']);
                    $response = IqxamplifyControl()->orders->create_order($product_ref_id, $profile_ref_id, $quantity, $shipping_address_1, $shipping_address_2, $shipping_country, $shipping_state, $shipping_city, $shipping_zip, $total, $tax_rate, $shipping_rate, $first_name, $last_name, $type, $variation_id);
                    if (!$response) {
                        wp_send_json_error(array('Order Not Created'));
                        die;
                    }

                    wp_send_json_success($response['order']);
                    die;
                    break;

                  case 'create_customer':

                      $phone = wp_filter_nohtml_kses($_GET['phone']);
                      $email = wp_filter_nohtml_kses($_GET['email']);
                      $first_name = wp_filter_nohtml_kses($_GET['first_name']);
                      $last_name = wp_filter_nohtml_kses($_GET['last_name']);
                      $shipping_address_1 = wp_filter_nohtml_kses($_GET['shipping_address_1']);
                      $shipping_address_2 = wp_filter_nohtml_kses($_GET['shipping_address_2']);
                      $shipping_state = wp_filter_nohtml_kses($_GET['shipping_state']);
                      $shipping_city = wp_filter_nohtml_kses($_GET['shipping_city']);
                      $shipping_zip = wp_filter_nohtml_kses($_GET['shipping_zip']);
                      $shipping_country = wp_filter_nohtml_kses($_GET['shipping_country']);

                      $response = IqxamplifyControl()->customers->create_customer($email, $first_name, $last_name, $shipping_address_1, $shipping_address_2, $shipping_country, $shipping_state, $shipping_city, $shipping_zip, $phone);
                      if (!$response) {
                          wp_send_json_error(array('Customer Not Created'));
                          die;
                      }

                      wp_send_json_success($response['customer']);
                      die;
                      break;

                  case 'create_refund':
                      if (!isset($_GET['order_id'])) {
                          wp_send_json_error(array('message' => 'Id not found'));
                          die;
                      }

                      if (!isset($_GET['type'])) {
                          wp_send_json_error(array('message' => 'type not found'));
                          die;
                      }

                      if (!isset($_GET['total'])) {
                          wp_send_json_error(array('message' => 'total not found'));
                          die;
                      }
                      $order_id = intval($_GET['order_id']);
                      $type = wp_filter_nohtml_kses($_GET['type']);
                      $total = floatval($_GET['total']);
                      $response = IqxamplifyControl()->orders->create_refund($order_id, $type, $total);
                      if (!$response) {
                          wp_send_json_error(array('Refund Not Created'));
                          die;
                      }

                      wp_send_json_success($response);
                      die;
                      break;

                case 'get-collections':
                    $params = $_GET;
                    $params = $this->cleanUpParams($params);
                    $response = IqxamplifyControl()->collections->get_product_categories(null, $params);

                    wp_send_json_success($response['product_categories']);
                    die;
                    break;

                case 'update-site-url':
                    IqxamplifyControl()->shop->update_shop_info_to_iqxamplify();
                    wp_send_json_success();
                    die;
                    break;

                case 'white-list-number':
                    if (!isset($_GET['number'])) {
                        wp_send_json_error(array('message' => 'number not found'));
                        die;
                    }

                    if (!isset($_GET['userId'])) {
                        wp_send_json_error(array('message' => 'user not found'));
                        die;
                    }

                    $number = $_GET['number'];
                    $userId = $_GET['userId'];

                    $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

                    $apiResponse = wp_remote_post($this->apiUrl.'white-list-number?number='.$number.'&userId='.$userId, array(
                            'method' => self::METHOD_GET,
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => $headers,
                            'body' => json_encode(array()),
                            'cookies' => array(),
                        )
                    );

                    if (is_wp_error($apiResponse)) {
                        return $apiResponse;
                    } else {
                        $response['response'] = $apiResponse['response'];
                        $response['body'] = $apiResponse['body'];
                        echo  json_decode($apiResponse['body'], true);
                    }
                    die;
                    break;

                  case 'capture-view':
                      if (!isset($_GET['type'])) {
                          wp_send_json_error(array('message' => 'type not found'));
                          die;
                      }

                      if (!isset($_GET['userId'])) {
                          wp_send_json_error(array('message' => 'user not found'));
                          die;
                      }

                      $type = wp_filter_nohtml_kses($_GET['type']);
                      $userId = $_GET['userId'];

                      $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

                      $apiResponse = wp_remote_post($this->apiUrl.'capture-view?type='.$type.'&userId='.$userId, array(
                              'method' => self::METHOD_GET,
                              'timeout' => 45,
                              'redirection' => 5,
                              'httpversion' => '1.0',
                              'blocking' => true,
                              'headers' => $headers,
                              'body' => json_encode(array()),
                              'cookies' => array(),
                          )
                      );

                      if (is_wp_error($apiResponse)) {
                          return $apiResponse;
                      } else {
                          $response['response'] = $apiResponse['response'];
                          $response['body'] = $apiResponse['body'];
                          echo  json_decode($apiResponse['body'], true);
                      }
                      die;
                      break;

                    case 'clicked_product_link':
                        if (!isset($_GET['url'])) {
                            wp_send_json_error(array('message' => 'url not found'));
                            die;
                        }

                        if (!isset($_GET['userId'])) {
                            wp_send_json_error(array('message' => 'user not found'));
                            die;
                        }

                        $url = esc_url($_GET['url']);
                        $userId = $_GET['userId'];

                        $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

                        $apiResponse = wp_remote_post($this->apiUrl.'clicked_product_link?url='.$url.'&userId='.$userId, array(
                                'method' => self::METHOD_GET,
                                'timeout' => 45,
                                'redirection' => 5,
                                'httpversion' => '1.0',
                                'blocking' => true,
                                'headers' => $headers,
                                'body' => json_encode(array()),
                                'cookies' => array(),
                            )
                        );

                        header("Location: " . $url);
                        die;
                        break;

                default:
                    wp_send_json_error(array('message' => 'Action not found'));
                    die;
                    break;
            }
        }

        /**
         * Clean up params.
         *
         * @param array $params
         *
         * @return array
         */
        public function cleanUpParams($params = array())
        {
            if (isset($params['iqxamplify_send_action'])) {
                unset($params['iqxamplify_send_action']);
            }
            if (isset($params['access_token'])) {
                unset($params['access_token']);
            }

            return $params;
        }

        /**
         * API Get.
         *
         * @since 1.0.0
         *
         * @param $apiMethod
         * @param array $args
         *
         * @return mixed
         */
        public function apiGet($apiMethod, $args = array())
        {
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

            $apiResponse = wp_remote_get($this->apiUrl.$apiMethod.'.json', array(
                    'timeout' => 5,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'body' => json_encode($args),
                    'cookies' => array(),
                )
            );

            if (is_wp_error($apiResponse)) {
                return $apiResponse;
            } else {
                $response['response'] = $apiResponse['response'];
                $response['body'] = $apiResponse['body'];

                return $response;
            }
        }

        /**
         * API Post.
         *
         * @since 1.0.0
         *
         * @param $apiMethod
         * @param array  $args
         * @param string $source
         *
         * @return mixed
         */
        public function apiPost($apiMethod, $args = array(), $source = null)
        {
            if (!$source) {
                $source = self::IQX_AMPLIFY_SOURCE;
            }

            $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => $source);

            $apiResponse = wp_remote_post($this->apiUrl.$apiMethod.'.json', array(
                    'method' => self::METHOD_POST,
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'body' => json_encode($args),
                    'cookies' => array(),
                )
            );

            if (is_wp_error($apiResponse)) {
                return $apiResponse;
            } else {
                $response['response'] = $apiResponse['response'];
                $response['body'] = $apiResponse['body'];

                return $response;
            }
        }

        /**
         * API Put.
         *
         * @since 1.0.0
         *
         * @param $apiMethod
         * @param array $args
         *
         * @return mixed
         */
        public function apiPut($apiMethod, $args = array())
        {
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

            $apiResponse = wp_remote_post($this->apiUrl.$apiMethod.'.json', array(
                    'method' => self::METHOD_PUT,
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'body' => json_encode($args),
                    'cookies' => array(),
                )
            );

            if (is_wp_error($apiResponse)) {
                return $apiResponse;
            } else {
                $response['response'] = $apiResponse['response'];
                $response['body'] = $apiResponse['body'];

                return $response;
            }
        }

        /**
         * API Delete.
         *
         * @since 1.0.0
         *
         * @param $apiMethod
         * @param array $args
         *
         * @return mixed
         */
        public function apiDelete($apiMethod, $args = array())
        {
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'Basic: '.$this->apiKey, 'X-Iqxamplify-Source' => self::IQX_AMPLIFY_SOURCE);

            $apiResponse = wp_remote_post($this->apiUrl.$apiMethod.'.json', array(
                    'method' => self::METHOD_DELETE,
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'body' => json_encode($args),
                    'cookies' => array(),
                )
            );

            if (is_wp_error($apiResponse)) {
                return $apiResponse;
            } else {
                $response['response'] = $apiResponse['response'];
                $response['body'] = $apiResponse['body'];

                return $response;
            }
        }

        /**
         * Check and add cart token.
         *
         * @since 1.0.0
         */
        public function checkAndAddCartToken()
        {
            if (!isset($_COOKIE['iqxamplify-cart-token'])) {
                setcookie('iqxamplify-cart-token', uniqid($this->apiKey, true), time() + self::CART_TOKEN_LIFE_TIME, '/');
            }
        }

        /**
         * Listen plugin deactivation.
         *
         * @since 1.0.0
         *
         * @param array $args
         */
        public function listenPluginDeactivation($args = array())
        {
            $this->apiPost('woocommerce/plugin_deactivation', $args);
        }

        /**
         * Listen plugin uninstall.
         *
         * @since 1.0.0
         *
         * @param array $args
         */
        public function listenPluginUninstall($args = array())
        {
            $this->apiPost('woocommerce/plugin_uninstall', $args);
        }

        /**
         * Listen plugin activation.
         *
         * @since 1.0.0
         *
         * @param array $args
         */
        public function listenPluginActivation($args = array())
        {
            $this->apiPost('woocommerce/plugin_activation', $args);
        }

        /**
         * Send tracking event.
         *
         * @since 1.0.0
         *
         * @param array $args
         */
        public function sendTrackingEvent($args = array())
        {
            $this->apiPost('shops/track', $args);
        }
    }

endif;
