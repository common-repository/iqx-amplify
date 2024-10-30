<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Snippet_DefaultSnippet.
 *
 * Add default snippet
 *
 * @class		IqxamplifySDK_Snippet_DefaultSnippet
 * @version		1.0.0
 * @author		Iqxamplify
 * @since		1.0.0
 */
if (!class_exists('IqxamplifySDK_Snippet_DefaultSnippet')):

    class IqxamplifySDK_Snippet_DefaultSnippet
    {
        /**
         * @since 1.0.0
         *
         * @var IqxamplifySDK_Snippet_SnippetManager
         */
        protected $snippetManager;

        /**
         * @since 1.0.0
         *
         * @var IqxamplifySDK_Config_IqxamplifyConfig
         */
        protected $iqxamplifyConfig;

        /**
         * Constructor.
         *
         * @since 1.0.0
         *
         * @param $snippetManager
         * @param $iqxamplifyConfig
         */
        public function __construct($snippetManager, $iqxamplifyConfig)
        {
            $this->snippetManager = $snippetManager;
            $this->iqxamplifyConfig = $iqxamplifyConfig;
        }

        /**
         * Start.
         *
         * @since 1.0.0
         */
        public function start()
        {
            // Add default snippet
            $couponBoxSnippet = dirname(__FILE__).'/Templates/CouponBoxSnippet.html';

            $this->snippetManager->addSnippet($couponBoxSnippet);
            $this->snippetManager->addVars(array(
                '{{ bk_script_path }}' => $this->getScriptPath($this->iqxamplifyConfig->getPlatform()),
                '{{ bk_shop_api_key }}' => $this->getShopApiKey(),
            ));
        }

        /**
         * Get shop api key.
         *
         * @since 1.0.0
         *
         * @return string
         */
        protected function getShopApiKey()
        {
            return strtolower($this->iqxamplifyConfig->getApiKey());
        }

        /**
         * Get script path.
         *
         * @since 1.0.0
         *
         * @param $platform
         *
         * @return string
         */
        protected function getScriptPath($platform)
        {
            // return $this->iqxamplifyConfig->getCdnPath() . '/dist/js/front/loader/' . $platform . '.js';
        }
    }

endif;
