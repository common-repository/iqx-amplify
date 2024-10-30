<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_user = wp_get_current_user();

?>
<div id="iqxamplify-sync-permission">
    <div class="permission-header">
        <span class="logo">
            <img src="<?php echo plugins_url('lib/img/logo_iqxamplify.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
        </span>
        <span class="connector">
            <img src="<?php echo plugins_url('lib/img/connector.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
        </span>
        <span class="logo">
            <img src="<?php echo plugins_url('lib/img/logo_woocommerce.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
        </span>
    </div>
    <div class="permission-body">
        <h2>You’re about to connect your site with Amplify</h2>
        <p>By clicking "Continue" button, you allow Amplify For WooCommerce to access and modify your store data.</p>

        <h4>This plugin will be able to:</h4>
        <ul>
            <li>Modify products, variants and categories</li>
            <li>Modify customer details and customer groups</li>
            <li>Modify orders, transactions and fulfillments</li>
        </ul>

        <button class="btn btn-continue">Continue</button>
    </div>
</div>
