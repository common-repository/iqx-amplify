<?php
/**
 * Iqxamplify For WooCommerce Uninstall.
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

require_once 'iqxamplify-woocommerce.php';

IqxamplifyWooCommerce()->pluginUninstall();
