<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyCollects.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyCollects
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyCollects')):

    class IqxamplifySDK_Api_IqxamplifyCollects
    {
        /**
         * Processes collect queue.
         *
         * Process the collects that are in the queue.
         *
         * @since 1.0.0
         */
        public function process_queue()
        {
            $queue = get_option('_iqxamplify_queue', array());

            // Process products
            if (isset($queue['collect']) && is_array($queue['collect'])) {
                foreach (array_slice($queue['collect'], 0, 225, true) as $key => $collect) {
                    if ('update' == $collect['action']) {
                        $response = $this->update_collect($collect['product_id'], $collect['collection_id']);
                    }

                    if (isset($response) && !is_wp_error($response) && in_array($response['response']['code'], array('200', '201', '204', '400', '404'))) { // Unset from queue when appropiate
                        unset($queue['collect'][$key]);
                    }
                }
            }

            update_option('_iqxamplify_queue', $queue);
        }

        /**
         * Sync collect by collection id.
         *
         * @since 1.0.0
         *
         * @param $collection_id
         */
        public function sync_collect_by_collection_id($collection_id)
        {
            $product_offset = 0;
            while (true) {
                $product_ids = iqxamplify_get_product_ids_in_collection($collection_id, array(
                    'posts_per_page' => 250,
                    'offset' => $product_offset * 250,
                ));

                if (!$product_ids) {
                    break;
                }

                foreach ($product_ids as $product_id) {
                    $this->update_collect($product_id, $collection_id);
                }

                ++$product_offset;
            }
        }

        /**
         * Update collect.
         *
         * @since 1.0.0
         *
         * @param $product_id
         * @param $collection_id
         *
         * @return mixed
         */
        public function update_collect($product_id, $collection_id)
        {
            $collect_data = $this->get_formatted_collect($product_id, $collection_id);

            $response = IqxamplifyControl()->api->apiPost('collects', $collect_data['collect']);

            if (is_wp_error($response) || in_array($response['response']['code'], array('401', '500', '503'))) {
                $queue = get_option('_iqxamplify_queue', array());
                $queue['collect'][$collect_data['id']] = array('id' => $collect_data['id'], 'action' => 'update');
                update_option('_iqxamplify_queue', $queue);
            }

            return $response;
        }

        /**
         * Get formatted collect.
         *
         * @since 1.0.0
         *
         * @param $product_id
         * @param $collection_id
         *
         * @return array
         */
        public function get_formatted_collect($product_id, $collection_id)
        {
            return array(
                'collect' => array(
                    'ref_id' => $this->generate_collect_id($product_id, $collection_id),
                    'product_ref_id' => $product_id,
                    'collection_ref_id' => $collection_id,
                ),
            );
        }

        /**
         * Generate collect id by using cantor pairing function.
         *
         * @since 1.0.0
         *
         * @param $product_id
         * @param $collection_id
         *
         * @return float
         */
        public function generate_collect_id($product_id, $collection_id)
        {
            return (($product_id + $collection_id) * ($product_id + $collection_id + 1)) / 2 + $collection_id;
        }

        /**
         * Get collects by collection id.
         *
         * @since 1.0.0
         *
         * @param $collection_id
         *
         * @return array
         */
        public function get_collects_by_collection_id($collection_id)
        {
            $product_ids = iqxamplify_get_product_ids_in_collection($collection_id);

            $collects = array();
            foreach ($product_ids as $product_id) {
                $collect = $this->get_formatted_collect($product_id, $collection_id);
                if (!isset($collect['collect'])) {
                    continue;
                }
                $collects[] = $collect['collect'];
            }

            return array('collects' => $collects);
        }
    }

endif;
