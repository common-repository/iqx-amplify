<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifyWoo_PageManager_AdminPage.
 *
 * @class		IqxamplifyWoo_PageManager_AdminPage
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifyWoo_PageManager_AdminPage')):

    class IqxamplifyWoo_PageManager_AdminPage extends IqxamplifyClasses_BaseAdminPage
    {
        const MAIN_ADMIN_URL = 'iqxamplify_menu';

        /**
         * Main admin url.
         *
         * @var string
         */
        public $mainAdminUrl;

        /**
         * Constructor.
         *
         * @param IqxamplifySDK_Config_IqxamplifyConfig $iqxamplifyConfig
         *
         * @since 1.0.0
         */
        public function __construct($iqxamplifyConfig)
        {
            parent::__construct($iqxamplifyConfig);

            $this->setMainAdminUrl(self::MAIN_ADMIN_URL);
            $this->setIncludePath(plugin_dir_path(__FILE__));
        }

        /**
         * Sidebar tab.
         *
         * @since 1.0.0
         */
        public function sidebarTab()
        {
            // Add to admin_menu
            add_menu_page(__('Amplify Menu Page'), __('Amplify'), 'edit_theme_options', $this->getMainAdminUrl(), array($this, 'addBlocksIframe'), plugins_url('lib/img/icon_menu.png', IQX_AMPLIFY_PLUGIN_DIRNAME));

            if (!get_option('iqxamplify_api_key')) {
                add_submenu_page( $this->getMainAdminUrl(), __('Settings'), __('Settings'), 'edit_theme_options', 'iqx_access_key', array( $this, 'editAccessKey') );
                add_submenu_page( null, __('Setting'), __('Setting'), 'edit_themes', 'iqx_registration', array( $this, 'getRegistrationForm') );
                add_action('admin_enqueue_scripts', function () {
                    wp_localize_script('iqx_reg_script', 'iqxamplify_menu_url', menu_page_url('iqx_registration', false));
                    wp_localize_script('iqx_reg_script', 'iqxamplify_popup_btn', '1');
                });
            } else {
                add_action('admin_enqueue_scripts', function () {
                    wp_localize_script('iqx_reg_script', 'iqxamplify_menu_url', '#');
                    $more_apps = get_option('iqxamplify_total_more_apps');
                    if ($more_apps) {
                        wp_localize_script('iqx_reg_script', 'iqxamplify_more_apps', $more_apps);
                    }
                });
            }

            if (get_option('iqxamplify_api_key')) {
                add_submenu_page($this->getMainAdminUrl(), __('Settings'), __('Settings'), 'edit_theme_options', 'iqxamplify_menu_fallback', array($this, 'editAccessKey'));
            }
        }

        /**
         * {@inheritdoc}
         */
        public function setMainAdminUrl($mainAdminUrl)
        {
            $this->mainAdminUrl = $mainAdminUrl;
        }

        /**
         * {@inheritdoc}
         */
        public function getMainAdminUrl()
        {
            return $this->mainAdminUrl;
        }

        /**
         * {@inheritdoc}
         */
        public function setIncludePath($includePath)
        {
            $this->includePath = $includePath;
        }

        /**
         * {@inheritdoc}
         */
        public function getIncludePath()
        {
            return $this->includePath;
        }

        /**
         * Get settings Tab.
         *
         * @since 1.0.0
         *
         * @return mixed|void
         */
        public function getSettingsTab()
        {
            $settings = apply_filters('woocommerce_iqxamplify_settings', array(
                array(
                    'title' => __('Access Key', 'iqxamplify'),
                    'type' => 'title',
                    'desc' => sprintf(__("Enter your Access Key below to connect your store with Amplify, you can <a href='%s' target='_blank'>get your access key</a> from your Amplify's account page.", 'iqxamplify'), $this->iqxamplifyConfig->getSignInUrl()),
                    'id' => 'iqxamplify_access_key',
                ),
                array(
                    'title' => __('Access Key', 'iqxamplify'),
                    'desc' => '',
                    'id' => 'iqxamplify_api_key',
                    'default' => '',
                    'type' => 'text',
                    'autoload' => false,
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'iqxamplify_end',
                ),
            ));

            return $settings;
        }

        /**
         * Settings Tab.
         *
         * @param $tabs
         *
         * @since 1.0.0
         *
         * @return mixed
         */
        public function settingsTab($tabs)
        {
            $tabs['iqxamplify'] = 'Amplify';

            return $tabs;
        }

        /**
         * Settings Page Action.
         *
         * @since 1.0.0
         */
        public function settingsPageAction()
        {
            \WC_Admin_Settings::output_fields($this->getSettingsTab());
        }

        /**
         * Update options action.
         *
         * @since 1.0.0
         */
        public function updateOptionsAction()
        {
            $apiKey = wp_filter_nohtml_kses($_REQUEST['iqxamplify_api_key']);
            $verified = $this->iqxamplifyConfig->isApiKeyExists($apiKey);

            if ($verified) {
                // Set api key
                $this->iqxamplifyConfig->setApiKey($apiKey);
                update_option('iqxamplify_send_whitelist_email', 'false');
                update_option('iqxamplify_show_whitelist_popup', 'false');
                // Save fields
                \WC_Admin_Settings::save_fields($this->getSettingsTab());
            } else {
                \WC_Admin_Settings::add_error(__('Oops! we can\'t connect to Amplify\'s service with your Access Key, please make sure your access key is corrected.', 'iqxamplify'));
            }
        }

        /**
         * Sign-up complete html.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function signUpCompleteHtml()
        {
            if (!headers_sent()) {
                wp_redirect(menu_page_url($this->getMainAdminUrl(), false));
            } else {
                $url = menu_page_url($this->getMainAdminUrl(), false);
                if ($url) {
                    include_once plugin_dir_path(__FILE__).'SignUpComplete.php';
                }
            }

            exit;
        }
    }

endif;
