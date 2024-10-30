<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class IqxamplifyClasses_BaseAdminPage.
 *
 * @class		IqxamplifyClasses_BaseAdminPage
 * @version		1.0.0
 * @author		Iqxamplify
 */
if ( ! class_exists( 'IqxamplifyClasses_BaseAdminPage' ) ):

    abstract class IqxamplifyClasses_BaseAdminPage
    {
        /**
         * @since 1.0.0
         * @var string
         */
        protected $mainAdminUrl;

        /**
         * @since 1.0.0
         * @var string
         */
        protected $includePath;

        /**
         * @since 1.0.0
         * @var IqxamplifySDK_Config_IqxamplifyConfig
         */
        protected $iqxamplifyConfig;

        /**
         * Constructor
         *
         * @param IqxamplifySDK_Config_IqxamplifyConfig $iqxamplifyConfig
         * @since 1.0.0
         */
        public function __construct( $iqxamplifyConfig )
        {
            $this->hooks();

            $this->iqxamplifyConfig = $iqxamplifyConfig;

            add_action( 'admin_enqueue_scripts', array( $this, 'registerJSVars' ) );
        }

        /**
         * Setup Hooks
         *
         * @since 1.0.0
         */
        public function hooks()
        {
            add_action( 'admin_menu', array( $this, 'sidebarTab' ), 200 );
        }

        /**
         * Sidebar tab
         *
         * @since 1.0.0
         */
        public abstract function sidebarTab();

        /**
         * Set main admin url
         *
         * @since 1.0.0
         * @param $mainAdminUrl
         */
        public abstract function setMainAdminUrl($mainAdminUrl);

        /**
         * Get main admin url
         *
         * @since 1.0.0
         */
        public abstract function getMainAdminUrl();

        /**
         * Set include path
         *
         * @since 1.0.0
         * @param $includePath
         */
        public abstract function setIncludePath($includePath);

        /**
         * Get include path
         *
         * @since 1.0.0
         */
        public abstract function getIncludePath();

        /**
         * Get app code
         *
         * @since 1.0.0
         */
        public function getAppCode() {
            return null;
        }

        /**
         * Get registration form
         * @since 1.0.0
         */
        public function getRegistrationForm()
        {
            $apiKey = isset( $_GET['iqxamplify_api_key'] ) ? $_GET['iqxamplify_api_key'] : null;
            if ( $apiKey ) {
                $apiKey     = wp_filter_nohtml_kses($apiKey);
                $verified   = $this->iqxamplifyConfig->isApiKeyExists( $apiKey );
                if ($verified) {
                    update_option( 'iqxamplify_api_key', $apiKey );
                    $this->iqxamplifyConfig->setApiKey( $apiKey );
                    echo $this->signUpCompleteHtml();
                    exit;
                } else {
                    // Check exist shop
                    $checkApiKey = $this->iqxamplifyConfig->checkApiKeyByShop();
                    if ( $checkApiKey ) {
                        echo '<h3>This shop is already registered with another account. <a href="' . menu_page_url( $this->getMainAdminUrl(), false ) . '">Click here</a>
                        to input again or <a target="_blank" href="' . $this->iqxamplifyConfig->getContactUrl() . '">contact us</a></h3>';
                        exit;
                    }

                    echo '<h3>Invalid api key. <a href="' . menu_page_url( $this->getMainAdminUrl(), false ) . '">Click here</a>
                    to input again or <a target="_blank" href="' . $this->iqxamplifyConfig->getContactUrl() . '">contact us</a></h3>';
                    exit;
                }
            } elseif ( get_option( 'iqxamplify_api_key' ) ) {
                echo $this->signUpCompleteHtml();
                exit;
            }

            $siteUrl = get_site_url();
            $urlParsed = parse_url( $siteUrl );
            $host = isset($urlParsed['host']) ? $urlParsed['host'] :  '';
            ?>
            <div class="iqx-registration-group">
                <div id="overflow">
                    <div class="inner">
                        <div class="iqx-enter-email-form">
                            <h4>Enter your email to get start</h4>
                            <input class="iqx-email" id="iqx-email" type="email" placeholder="JohnDoe@domain.com" required autofocus focus>
                            <button class="iqx-btn-email-check" type="button">Next </button>
                            <div class="indicator email-error" style="display: none">Please wait...</div>
                        </div>
                        <div class="iqx-enter-password-form" >
                            <h3>Welcome <span class="name"></span>, Please enter your password</h3>
                            <form class="form-horizontal login-form" role="form">
                                <input type="hidden" name="path" value="<?php echo get_site_url(); ?>">
                                <div class="form-group">
                                    <span class="user-email">email-test@emailtest.com</span>
                                    <a class="forgot-password" href="<?php echo $this->iqxamplifyConfig->getPath() . '/resetting/request'; ?>" target="_blank">Forgot your password?</a>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" id="login_pass" value="" placeholder="Password" class="form-control" required />
                                    <span class="iqx-login-error" style="display: none;">Error</span>
                                </div>
                                <input type="submit" class="btn btn-material-blue-600" id="iqx-btn-login" value="Sign in" />
                            </form>
                            <a class="btn-signin-account" href="<?php menu_page_url( 'iqx_registration', true ) ?>">Sign in with a different account</a>
                        </div>
                        <div class="iqx-registration-form-block" >
                            <h3>Welcome to Amplify!</h3>
                            <p>Now please enter your shop info</p>
                            <form class="form-horizontal registration-form" role="form">
                                <p>Already have a Amplify account? <a class="login-here" href="<?php menu_page_url( 'iqx_registration', true ) ?>">Log in here</a></p>
                                <input type="hidden" name="platform" value="woocommerce">
                                <input type="hidden" name="domain" value="<?php echo $host; ?>">
                                <div class="form-group">
                                    <label for="bk_email" class="sr-only">Email *</label>
                                    <input type="email" name="email" id="bk_email" value="" placeholder="Your Email" class="form-control" required />
                                </div>

                                <div class="form-group">
                                    <label for="bk_pass" class="sr-only">Password *</label>
                                    <input type="password" name="password" id="bk_pass" value="" placeholder="Password" class="form-control" required />
                                    <p class="iqx-error-message" style="display: none">Please enter password</p>
                                </div>

                                <div class="form-group">
                                    <label for="bk_name" class="sr-only">Name *</label>
                                    <input type="text" name="name" id="bk_name" value="" placeholder="Your Full name" class="form-control" required />
                                    <p class="iqx-error-message" style="display: none">Please your name</p>
                                </div>

                                <div class="form-group">
                                    <label for="bk_business_name" class="sr-only">Business Name *</label>
                                    <input type="text" name="business_name" id="bk_business_name" value="" placeholder="My Pink Boutique" class="form-control" required />
                                    <p class="iqx-error-message" style="display: none">Please enter business name</p>
                                </div>

                                <input type="submit" class="btn btn-primary" id="iqx-btn-submit" value="Complete" />
                                <p>By clicking this button, you agree to Amplify's
                                    <a href="https://iqxamplify.com/terms-of-service" target="_blank">Terms of Services</a> &
                                    <a href="https://iqxamplify.com/privacy-policy" target="_blank">Privacy Policy</a></p>
                            </form>

                            <div class="indicator" style="display: none">Please wait...</div>
                            <div class="alert result-message"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Add blocks to setting tab using iframe.
         *
         * @since 1.0.0
         */
        public function addBlocksIframe()
        {
            if (!IqxamplifyControl()->isWooCommerceActive()) {
                $this->renderRequireWCPage();
            } else {
                $this->addSomeBlocks();
            }
        }

        /**
         * Add blocks to setting tab iframe fallback.
         *
         * @since 1.0.0
         */
        public function addBlocksFallback()
        {
            $this->addSomeBlocks(false);
        }

        /**
         * Add blocks to setting tab.
         *
         * @since 1.0.0
         * @param bool $useIframe
         */
        public function addSomeBlocks($useIframe = true)
        {
            $optionApiKey = sanitize_text_field( get_option( 'iqxamplify_api_key' ) );

            if ( !$optionApiKey ) {

                if ($useIframe) {
                    $iframeUrl = $this->iqxamplifyConfig->getSignInUrlIframe( $this->getAppCode() );
                    include( $this->getIncludePath() . 'EnterAccessKeyIframe.php' );
                } else {
                    include( $this->getIncludePath() . 'EnterAccessKey.php' );
                }
            } else {

                if (isset($_GET['iqxamplify_api_key'])) {
                  $apiKey = sanitize_text_field( $_GET['iqxamplify_api_key'] );
                  update_option( 'iqxamplify_api_key',  $apiKey);
                  $this->iqxamplifyConfig->setApiKey( $apiKey );
                  $hasAccessKey = true;
                } else {

                  $hasAccessKey = ( $this->iqxamplifyConfig->getApiKey() ) ? true : false;

                }

                include( $this->getIncludePath() . 'BlocksView.php' );
            }
        }

        /**
         * Edit access key.
         *
         * @since 1.0.0
         */
        public function editAccessKey($useIframe = true)
        {
            $optionApiKey = sanitize_text_field( get_option( 'iqxamplify_api_key' ) );

            include( $this->getIncludePath() . 'EnterAccessKey.php' );
        }

        /**
         * Register JS Vars
         *
         * @since 1.0.0
         */
        public function registerJSVars()
        {
            $data = array(
                'verify_user'       => $this->iqxamplifyConfig->getPath() . '/verify-user-by-email',
                'add_user_and_shop' => $this->iqxamplifyConfig->getPath() . '/add-new-user-and-shop',
                'get_shop_api' => $this->iqxamplifyConfig->getPath() . '/get-shop-apikey/woocommerce',
                'verify_api' => $this->iqxamplifyConfig->getPath() . '/check-key-exists/',
            );
            wp_localize_script( 'iqx_reg_script', 'iqxamp_vars', $data );
        }

        /**
         * Sign-up complete html
         * @since 1.0.0
         * @return string
         */
        public function signUpCompleteHtml()
        {
            return
                '<div id="congrat-content"><h3 class="congrat-heading">Congrats!</h3>
                <p class="congrat-subheading">Your store has been successfully connected with Amplify.</p>
                <a href="' . menu_page_url( $this->getMainAdminUrl(), false ) . '" class="btn btn-material-blue-600 iqx-continue">Great! Get started now</a></div>';
        }

        /**
         * Render require woocommerce page
         */
        public function renderRequireWCPage()
        {
            $wooCommercePluginUrl = get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce';
            include_once( IQX_AMPLIFY_PLUGIN_DIR . 'inc/pages/require_wc.php' );
        }
    }

endif;
