<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyCollections.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyCollections
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyCollections')):

    class IqxamplifySDK_Api_IqxamplifyCollections
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {

            // Delete
            add_action('delete_term', array($this, 'delete_product_category_from_iqxamplify'), 10, 4);

            // Create
            add_action('created_term', array($this, 'add_product_category_to_iqxamplify'), 10, 3);

            // Edit
            add_action('edited_term', array($this, 'update_product_category_to_iqxamplify'), 10, 3);
        }

        /**
         * Processes collection queue.
         *
         * Process the collections that are in the queue.
         *
         * @since 1.0.0
         */
        public function process_queue()
        {
            $queue = get_option('_iqxamplify_queue', array());

            // Process products
            if (isset($queue['product_category']) && is_array($queue['product_category'])) {
                foreach (array_slice($queue['product_category'], 0, 225, true) as $key => $collection) {
                    if ('delete' == $collection['action']) {
                        $response = $this->delete_product_category_from_iqxamplify($collection['id'], null, 'product_cat', null);
                    } elseif ('add' == $collection['action']) {
                        $response = $this->add_product_category_to_iqxamplify($collection['id'], null, 'product_cat');
                    } else {
                        $response = $this->update_product_category_to_iqxamplify($collection['id'], null, 'product_cat');
                    }

                    if (!is_wp_error($response) && in_array($response['response']['code'], array('200', '201', '204', '400', '404'))) { // Unset from queue when appropiate
                        unset($queue['products'][ $key ]);
                    }
                }
            }

            update_option('_iqxamplify_queue', $queue);
        }

        /**
         * Add product category to iqxamplify.
         *
         * @since 1.0.0
         *
         * @param $term_id
         * @param $tt_id
         * @param $taxonomy
         *
         * @return bool
         */
        public function add_product_category_to_iqxamplify($term_id, $tt_id, $taxonomy)
        {
            if ($taxonomy != 'product_cat') {
                return false;
            }

            $product_category = $this->get_formatted_product_category($term_id);

            $response = IqxamplifyControl()->api->apiPost('collections/create_update', $product_category['product_category']);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['product_category'][ $term_id ] = array('id' => $term_id, 'action' => 'add');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_woocommerce_term_meta($term_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
                $updated_categories = get_option('_iqxamplify_updated_categories', array());
                $updated_categories[] = $term_id;
                update_option('_iqxamplify_updated_categories', $updated_categories);
            }

            return $response;
        }

        /**
         * Update product category to iqxamplify.
         *
         * @since 1.0.0
         *
         * @param $term_id
         * @param $tt_id
         * @param $taxonomy
         *
         * @return bool
         */
        public function update_product_category_to_iqxamplify($term_id, $tt_id, $taxonomy)
        {
            if ($taxonomy != 'product_cat') {
                return false;
            }

            $product_category = $this->get_formatted_product_category($term_id);

            $response = IqxamplifyControl()->api->apiPost('collections/create_update', $product_category['product_category']);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['product_category'][ $term_id ] = array('id' => $term_id, 'action' => 'update');
                update_option('_iqxamplify_queue', $queue);
            } elseif (in_array($response['response']['code'], array('200', '201'))) {
                update_woocommerce_term_meta($term_id, '_iqxamplify_last_update', time() + (14 * 24 * 60 * 60));
                $updated_categories = get_option('_iqxamplify_updated_categories', array());
                $updated_categories[] = $term_id;
                update_option('_iqxamplify_updated_categories', $updated_categories);
            }

            return $response;
        }

        /**
         * Delete product category from iqxamplify.
         *
         * @since 1.0.0
         *
         * @param $term
         * @param $tt_id
         * @param $taxonomy
         * @param $deleted_term
         *
         * @return bool
         */
        public function delete_product_category_from_iqxamplify($term, $tt_id, $taxonomy, $deleted_term)
        {
            if ($taxonomy != 'product_cat') {
                return false;
            }

            $response = IqxamplifyControl()->api->apiDelete('collections/'.$term);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['product_category'][ $term ] = array('id' => $term, 'action' => 'delete');
                update_option('_iqxamplify_queue', $queue);
            }

            return $response;
        }

        /**
         * Get a listing of product categories.
         *
         * @since 1.0.0
         *
         * @param string|null $fields fields to limit response to
         * @param array       $filter
         *
         * @return array
         */
        public function get_product_categories($fields = null, $filter = array())
        {
            if (isset($filter['limit'])) {
                $filter['number'] = $filter['limit'];
                unset($filter['limit']);
            }

            if (isset($filter['title'])) {
                $filter['search'] = $filter['title'];
                unset($filter['title']);
            }

            $product_categories = array();
            $terms = get_terms('product_cat', array_merge(array('hide_empty' => false, 'fields' => 'ids'), $filter));
            foreach ($terms as $term_id) {
                $product_categories[] = current($this->get_formatted_product_category($term_id, $fields));
            }

            return array('product_categories' => $product_categories);
        }

        /**
         * Get the product category for the given ID.
         *
         * @since 1.0.0
         *
         * @param string      $id     product category term ID
         * @param string|null $fields fields to limit response to
         *
         * @return array
         */
        public function get_formatted_product_category($id, $fields = null)
        {
            $id = absint($id);
            // Validate ID
            if (empty($id)) {
                return false;
            }

            $term = get_term($id, 'product_cat');
            if (is_wp_error($term) || is_null($term)) {
                return false;
            }
            $term_id = intval($term->term_id);

            // Get category image
            $image = '';
            if ($image_id = get_woocommerce_term_meta($term_id, 'thumbnail_id')) {
                $image = wp_get_attachment_url($image_id);
            }
            $product_category = array(
                'ref_id' => $term_id,
                'title' => $term->name,
                'image_url' => $image ? esc_url($image) : '',
                'published_at' => iqxamplify_format_datetime(gmdate('Y-m-d H:i:s')),
            );

            return array('product_category' => $product_category);
        }
    }

endif;
