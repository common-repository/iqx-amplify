<?php
/**
Plugin Name: Amplify For WooCommerce
Plugin URI:  https://iqxcorp.com
Description: Amplify is a direct text marketing and sales system that allows you to instantly sell your eCommerce store products directly inside your customer's text applications. Amplify is the only technology of its kind, enabling your customers to experience their entire purchase journey via text.
Version:     1.8.0
Author:      iQX Corp.
Author URI:  https://iqxcorp.com
*/
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

define('IQX_AMPLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IQX_AMPLIFY_PLUGIN_DIRNAME', __FILE__);
define('IQX_AMPLIFY_INITIAL_SYNC_TIME', 1445878800); // 2015-10-27

require_once IQX_AMPLIFY_PLUGIN_DIR.'lib/functions/iqxamplify-functions.php';
require_once IQX_AMPLIFY_PLUGIN_DIR.'config.php';
require_once IQX_AMPLIFY_PLUGIN_DIR.'iqx_autoload.php';

if (!defined('IQX_AMPLIFY_ENVIRONMENT')) {
    define('IQX_AMPLIFY_ENVIRONMENT', 'prod');
}

if (!class_exists('IqxamplifyWooCommerce')) :

    class IqxamplifyWooCommerce
    {
        /**
         * Plugin version.
         *
         * @since 1.0.0
         *
         * @var string Plugin version number
         */
        public $version = '1.2.4';

        /**
         * Plugin file.
         *
         * @since 1.0.0
         *
         * @var string Plugin file path
         */
        public $file = __FILE__;

        /**
         * @var IqxamplifyWoo_PageManager_AdminPage;
         */
        protected $adminPage;

        /**
         * @var IqxamplifySDK_Config_IqxamplifyConfig
         */
        protected $iqxamplifyConfig;

        /**
         * @var IqxamplifySDK_Snippet_SnippetManager
         */
        protected $snippetManager;

        /**
         * The single instance of the class.
         */
        protected static $_instance = null;

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Constructor.
         */
        public function __construct()
        {
            // Enqueue scripts
            add_action('admin_enqueue_scripts', array($this, 'iqxamplify_register_script'));

            // Enqueue styles
            add_action('admin_enqueue_scripts', array($this, 'iqxamplify_enqueue_style'));

            // Iqxamplify Control
            $this->iqxamplifyControl = new IqxamplifyClasses_IqxamplifyControl();

            // Initialize plugin parts
            $this->init();

            // Check if WooCommerce is active
            if (!$this->iqxamplifyControl->isWooCommerceActive()) {
                add_action('admin_notices', array($this, 'requireWooCommerce'));

                return false;
            }

            // Plugin hooks
            $this->hooks();

            // Register ajax
            $this->registerAjax();

            do_action('iqxamplify_loaded');
        }

        /**
         * Init.
         */
        protected function init()
        {
            $apiKey = sanitize_text_field(get_option('iqxamplify_api_key'));

            // Init Env.
            $this->iqxamplifyConfig = new IqxamplifySDK_Config_IqxamplifyConfig();
            $this->iqxamplifyConfig->setApiKey($apiKey);
            $this->iqxamplifyConfig->setPlatform('woocommerce');

            // Snippet Manager.
            $this->snippetManager = new IqxamplifySDK_Snippet_SnippetManager();

            if (is_admin()) {
                /*
                 * Admin settings class
                 */
                $this->adminPage = new IqxamplifyWoo_PageManager_AdminPage($this->iqxamplifyConfig);
            }

            // Init cart token
            $this->iqxamplifyControl->api->checkAndAddCartToken();
        }

        /**
         * Enqueue and localize js.
         */
        public function iqxamplify_register_script()
        {
            // Enqueue script
            wp_register_script('iqx_reg_script', plugins_url('lib/js/iqxamplify.js', __FILE__), array('jquery'), null, false);
            wp_register_script('bk_app_script', plugins_url('lib/js/app.js', __FILE__), array('jquery'), null, false);
            wp_enqueue_script('iqx_reg_script');
            wp_enqueue_script('bk_app_script');

            // Tracking script
            // Set environment equals to local for debug
            if (IQX_AMPLIFY_ENVIRONMENT == 'prod') {
                wp_register_script('bk_tracking_script', plugins_url('lib/js/tracking.js', __FILE__), array('bk_app_script'), null, false);
                wp_enqueue_script('bk_tracking_script');
            }

            wp_localize_script('iqx_reg_script', 'bk_reg_vars', array(
                    'bk_ajax_url' => admin_url('admin-ajax.php'),
                )
            );

            // Inject variables to javascript
            wp_localize_script('bk_app_script', 'bk_js_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_url' => menu_page_url('iqxamplify_menu', false),
                )
            );
        }

        /**
         * Enqueue style.
         */
        public function iqxamplify_enqueue_style()
        {
            wp_register_style('bk_style', plugins_url('lib/css/iqxamplify.css', __FILE__), array(), '20160411', 'all');

            wp_enqueue_style('bk_style');
        }

        /**
         * Hooks.
         */
        protected function hooks()
        {
            // Notice set up
            if (!$this->iqxamplifyConfig->getApiKey()) {
                add_action('admin_notices', array($this, 'notice_set_up'));
            }
            add_action('wp_dashboard_setup', 'iqxamplify_add_dasboard_widgets');

            // Add the plugin page Settings and Docs links
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'pluginLinks'));

            // Plugin updates
            add_action('admin_init', array($this, 'checkVersion'), 2);

            // Plugin activation
            add_action('activated_plugin', array($this, 'pluginActivation'));

            // Helper functions
            add_action('plugins_loaded', array($this, 'loadHelperFunctions'));

            // Register plugin deactive hook
            register_deactivation_hook(__FILE__, array($this, 'pluginDeactivation'));
        }

        /**
         * Ajax.
         */
        protected function registerAjax()
        {
            if (is_admin()) {
                add_action('wp_ajax_accept_sync_permission', array($this, 'acceptSyncPermission'));
            }
        }

        /**
         * Load helper functions.
         */
        public function loadHelperFunctions()
        {
            /**
             * Helper functions.
             */
            require_once plugin_dir_path(__FILE__).'/IqxamplifySDK/Helper/iqxamplify-helper-functions.php';

            /*
             * Iqxamplify CRONTABs
             */
            if ($this->iqxamplifyConfig->getApiKey()) {
                require_once plugin_dir_path(__FILE__).'/IqxamplifySDK/Helper/iqxamplify-cron-functions.php';
            }
        }

        /**
         * Plugin links.
         *
         * @param $links
         *
         * @return array
         */
        public function pluginLinks($links)
        {
            $moreLinks = array();
            $moreLinks['settings'] = '<a href="'.admin_url('admin.php?page=iqxamplify_menu').'">'.__('Settings', 'iqxamplify').'</a>';

            return array_merge($moreLinks, $links);
        }

        /**
         * Check version.
         */
        public function checkVersion()
        {
            if (!get_option('iqxamplify_api_key')) {
                return false;
            }

            /*
             * Version specific plugin updates
             */
            // Update version number if its not the same
            if ($this->version != get_option('iqxamplify_woocommerce_version')) {
                update_option('iqxamplify_woocommerce_version', $this->version);

                $this->iqxamplifyControl->api->sendTrackingEvent(array(
                    'event' => 'Amplify WC Plugin Version',
                    'iqxamplify_wc_plugin_version' => $this->version,
                ));
            }
        }

        /**
         * Plugin activation.
         *
         * @param $plugin
         */
        public function pluginActivation($plugin)
        {
            $this->iqxamplifyControl->api->listenPluginActivation(array(
                'iqxamplify_wc_app_active' => 1,
            ));

            if ($plugin == plugin_basename(__FILE__)) {
                exit(wp_redirect(admin_url('admin.php?page='.$this->adminPage->getMainAdminUrl())));
            }
        }

        /**
         * Plugin deactivation.
         */
        public function pluginDeactivation()
        {
            $this->iqxamplifyControl->api->listenPluginDeactivation(array(
                'iqxamplify_wc_app_active' => 2,
            ));

            // delete_option( 'iqxamplify_api_key' );

            wp_clear_scheduled_hook('iqxamplify_initial_product_sync');
            wp_clear_scheduled_hook('iqxamplify_initial_orders_sync');
            wp_clear_scheduled_hook('iqxamplify_initial_collections_sync');
            wp_clear_scheduled_hook('iqxamplify_initial_collects_sync');
            wp_clear_scheduled_hook('iqxamplify_initial_customers_sync');
            wp_clear_scheduled_hook('iqxamplify_initial_sync_collects_by_collections');

            delete_option('iqxamplify_completed_initial_product_sync');
            delete_option('iqxamplify_completed_initial_order_sync');
            delete_option('iqxamplify_completed_initial_collection_sync');
            delete_option('iqxamplify_completed_initial_collects_sync');
            delete_option('iqxamplify_completed_initial_customer_sync');
            delete_option('iqxamplify_current_sync_time');
        }

        /**
         * Plugin uninstall.
         */
        public function pluginUninstall()
        {
            global $wpdb;

            $this->iqxamplifyControl->api->listenPluginUninstall(array(
                'iqxamplify_wc_app_active' => 0,
            ));

            // Delete options.
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%iqxamplify\_%';");
        }

        /**
         * Require woocommerce warning.
         */
        public function requireWooCommerce()
        {
            if (!$this->isInCurrentScreen(array($this->adminPage->getMainAdminUrl()))) {
                $wooCommercePluginUrl = get_site_url().'/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce';
                include_once IQX_AMPLIFY_PLUGIN_DIR.'inc/notices/require_wc.php';
            }
        }

        /**
         * Notice set up iqxamplify.
         */
        public function notice_set_up()
        {
            if (!$this->isInCurrentScreen(array($this->adminPage->getMainAdminUrl()))) {
                $plugin_url = admin_url('admin.php?page='.$this->adminPage->getMainAdminUrl());
                $learn_more_url = 'http://iqxcorp.com/?utm_channel=bamplifytdashboard&utm_medium=htmlbar';
                include_once IQX_AMPLIFY_PLUGIN_DIR.'inc/notices/set_up.php';
            }
        }

        /**
         * Is in current screen.
         *
         * @param array $pages
         *
         * @return bool
         */
        public function isInCurrentScreen($pages = array())
        {
            $screen = get_current_screen();

            if (in_array($screen->parent_base, $pages)) {
                return true;
            }

            return false;
        }

        /**
         * Accept sync permission callback.
         */
        public function acceptSyncPermission()
        {
            update_option('iqxamplify_sync_permission', true);

            wp_send_json(array(
                'success' => true,
            ));
            wp_die();
        }
    }

endif;

if (!function_exists('IqxamplifyWooCommerce')) {
    function IqxamplifyWooCommerce()
    {
        return IqxamplifyWooCommerce::instance();
    }
}

IqxamplifyWooCommerce();
