<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Config_IqxamplifyConfig.
 *
 * Config iqxamplify plugin
 *
 * @class		IqxamplifySDK_Config_IqxamplifyConfig
 * @version		1.0.0
 * @author		Iqxamplify
 * @since		1.0.0
 */

if (!class_exists('IqxamplifySDK_Config_IqxamplifyConfig')):

    class IqxamplifySDK_Config_IqxamplifyConfig
    {
        /**
         * Iqxamplify format date.
         *
         * @since 1.0.0
         */
        const IQX_AMPLIFY_FORMAT_DATE = 'Y-m-d\TH:i:s\Z';

        /**
         * Type webhook.
         *
         * @since 1.0.0
         */
        const SOURCE_TYPE_WEBHOOK = 'webhook';

        /**
         * Iqxamplify path.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $path;

        /**
         * Iqxamplify cdn path.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $cdnPath;

        /**
         * Iqxamplify cdn path.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $loginPath;

        /**
         * Default config.
         *
         * @var array
         *
         * @since 1.0.0
         */
        protected $defaultConfig = array(
            'IQX_AMPLIFY_PROFILE_URL' => 'user/profile',
            'IQX_AMPLIFY_GET_APPS_DATA_API' => 'get-apps-data-by-key/',
            'IQX_AMPLIFY_CHECK_STATUS_KEY_API' => 'check-key-exists/',
            'IQX_AMPLIFY_SIGN_IN_URL' => 'sign-in?platform=',
            'IQX_AMPLIFY_LOG_IN_URL' => '#/login-api/',
            'IQX_AMPLIFY_SIGN_IN_URL_IFRAME' => '#/onboarding/login?utm_channel=appstore&utm_medium=woolisting&utm_term=admindashboard&iframe=true&domain=%s&platform=%s&email=%s&owner_full_name=%s&business_name=%s&site_url=%s&app=%s&terms_of_service=%s',
            'IQX_AMPLIFY_CHECK_API_KEY_BY_SHOP' => 'check-key-by-shop?domain=%s',
            'IQX_AMPLIFY_CONTACT_URL' => 'https://iqxamplify.com/contact',
            'IQX_AMPLIFY_PLUGIN_REVIEW_URL' => 'https://wordpress.org/support/plugin/iqxamplify-for-woocommerce/reviews/',
        );

        /**
         * @since 1.0.0
         *
         * @var string
         */
        protected $apiKey;

        /**
         * @since 1.0.0
         *
         * @var string
         */
        protected $env;

        /**
         * @since 1.0.0
         *
         * @var string
         */
        protected $platform;

        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->path = IQX_AMPLIFY_PATH;
            $this->cdnPath = IQX_AMPLIFY_CDN;
            $this->loginPath = IQX_AMPLIFY_LOGIN;
        }

        /**
         * Set environment.
         *
         * @since 1.0.0
         *
         * @param $env
         *
         * @return $this
         *
         * @throws Exception
         */
        public function setEnv($env)
        {
            $this->env = $env;

            return $this;
        }

        /**
         * Get environment.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getEnv()
        {
            return $this->env;
        }

        /**
         * Get api key.
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
         * Set api key.
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
         * Get platform.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getPlatform()
        {
            return $this->platform;
        }

        /**
         * Set platform.
         *
         * @since 1.0.0
         *
         * @param $platform
         *
         * @return $this
         */
        public function setPlatform($platform)
        {
            $this->platform = $platform;

            return $this;
        }

        /**
         * Get config by name.
         *
         * @since 1.0.0
         *
         * @param $name
         *
         * @return string|bool
         */
        public function getConfigByName($name)
        {
            // If config not found.
            if (!isset($this->defaultConfig[ $name ])) {
                return false;
            }

            return $this->defaultConfig[ $name ];
        }

        /**
         * Get profile url.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getProfileUrl()
        {
            return $this->path.$this->getConfigByName('IQX_AMPLIFY_PROFILE_URL');
        }

        /**
         * Get sign in url.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getSignInUrl()
        {
            $apiKey = $this->getApiKey();

            return $this->loginPath.'/'.$this->getConfigByName('IQX_AMPLIFY_LOG_IN_URL').$apiKey;
        }

        /**
         * Get sign in url iframe.
         *
         * @since 2.2.6
         *
         * @param null $appCode
         *
         * @return string
         */
        public function getSignInUrlIframe($appCode = null)
        {
            $currentUser = wp_get_current_user();
            $siteTitle = get_bloginfo('name');
            $siteUrl = get_site_url();
            $termsOfService = get_site_url();

            if (wc_get_page_id('terms') > 0) {
                $termsOfService = esc_url(wc_get_page_permalink('terms'));
            }

            return $this->loginPath.'/'.sprintf(
                $this->getConfigByName('IQX_AMPLIFY_SIGN_IN_URL_IFRAME'),
                self::iqxamplifyGetShopDomain(), $this->getPlatform(),
                $currentUser->user_email,
                $currentUser->display_name,
                $siteTitle,
                $siteUrl,
                $appCode,
                $termsOfService
            );
        }

        /**
         * Get contact url.
         *
         * @since 2.2.9
         *
         * @return string
         */
        public function getContactUrl()
        {
            return $this->getConfigByName('IQX_AMPLIFY_CONTACT_URL');
        }

        /**
         * Get review url.
         *
         * @since 2.2.9
         *
         * @return string
         */
        public function getReviewUrl()
        {
            return $this->getConfigByName('IQX_AMPLIFY_PLUGIN_REVIEW_URL');
        }

        /**
         * Get admin panel url.
         *
         * @since 2.2.9
         *
         * @return string
         */
        public function getAdminPanelUrl()
        {
            return $this->path.'/'.$this->getConfigByName('IQX_AMPLIFY_SIGN_IN_URL').$this->getPlatform();
        }

        /**
         * Get apps data by domain from iqxamplify.
         *
         * @since 1.0.0
         *
         * @param null  $appCode
         * @param array $installedApps
         *
         * @return array
         */
        public function getAppsDataByApiKey($appCode = null, $installedApps = array())
        {
            $platform = $this->getPlatform();
            $apiKey = $this->getApiKey();
            $installedApps = implode(',', $installedApps);

            $data = self::curlGet($this->path.'/'.$this->getConfigByName('IQX_AMPLIFY_GET_APPS_DATA_API')
                .$apiKey.'?platform='.$platform.'&app_code='.$appCode.'&installed-app='.$installedApps);

            $data = json_decode($data, true); // decode & convert to array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }

            return array();
        }

        /**
         * Is api key exists?
         *
         * @since 1.0.0
         *
         * @param string
         *
         * @return bool
         */
        public function isApiKeyExists($apiKey)
        {
            $platform = $this->getPlatform();

            $data = self::curlGet($this->path.'/'.$this->getConfigByName('IQX_AMPLIFY_CHECK_STATUS_KEY_API').$apiKey.'?platform='.$platform.'&domain='.self::iqxamplifyGetShopDomain());

            $data = json_decode($data); // decode
            if (json_last_error() === JSON_ERROR_NONE) {
                return (isset($data->status)) ? $data->status : false;
            }

            return false;
        }

        /**
         * Check api key by shop.
         *
         * @since 2.2.6
         *
         * @param string
         *
         * @return bool
         */
        public function checkApiKeyByShop()
        {
            $data = $this->iqxamplifySendGet($this->path.'/'.sprintf($this->getConfigByName('IQX_AMPLIFY_CHECK_API_KEY_BY_SHOP'), self::iqxamplifyGetShopDomain()));

            if (!is_wp_error($data)) {
                $data = json_decode($data);
            }

            return (isset($data->api_key)) ? $data->api_key : false;
        }

        /**
         * Get path.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getPath()
        {
            return $this->path;
        }

        /**
         * Set path.
         *
         * @since 1.0.0
         *
         * @param string $path
         */
        public function setPath($path)
        {
            $this->path = $path;
        }

        /**
         * Get cnd path.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function getCdnPath()
        {
            return $this->cdnPath;
        }

        /**
         * Set cdn path.
         *
         * @since 1.0.0
         *
         * @param string $cdnPath
         */
        public function setCdnPath($cdnPath)
        {
            $this->cdnPath = $cdnPath;
        }

        /**
         * Send get request to iqxamplify.
         *
         * @since 1.0.0
         *
         * @param $url
         * @param array $args
         * @param array $headers
         *
         * @return mixed
         */
        public function iqxamplifySendGet($url, $args = array(), $headers = array())
        {
            $apiResponse = wp_remote_get($url, array(
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'body' => $args,
                    'cookies' => array(),
                )
            );

            if (is_wp_error($apiResponse)) {
                return $apiResponse;
            } else {
                $response = $apiResponse['body'];

                return $response;
            }
        }

        /**
         * Get shop domain.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public static function iqxamplifyGetShopDomain()
        {
            $siteUrl = get_site_url();
            $urlParsed = parse_url($siteUrl);
            $host = isset($urlParsed['host']) ? $urlParsed['host'] : '';

            return $host;
        }

        /**
         * Curl get.
         *
         * @param $url
         * @param bool $decodeMode
         *
         * @return mixed
         */
        public static function curlGet($url, $decodeMode = false)
        {
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $url);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

            if ($decodeMode) {
                $data = json_decode(curl_exec($curlSession), true);
            } else {
                $data = curl_exec($curlSession);
            }
            curl_close($curlSession);

            return $data;
        }
    }

endif;
