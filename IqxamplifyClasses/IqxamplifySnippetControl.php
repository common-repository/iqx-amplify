<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IqxamplifyClasses_IqxamplifySnippetControl' ) ):

    class IqxamplifyClasses_IqxamplifySnippetControl {
        /**
         * Constructor
         */
        public function __construct()
        {
            $apiKey = sanitize_text_field( get_option( 'iqxamplify_api_key' ) );

            // Init Env.
            $this->iqxamplifyConfig = new IqxamplifySDK_Config_IqxamplifyConfig();
            $this->iqxamplifyConfig->setApiKey( $apiKey );
            $this->iqxamplifyConfig->setPlatform( 'woocommerce' );

            // Snippet Manager.
            $this->snippetManager = new IqxamplifySDK_Snippet_SnippetManager();

            // Create default snippet.
            $defaultSnippet = new IqxamplifySDK_Snippet_DefaultSnippet( $this->snippetManager, $this->iqxamplifyConfig );
            $defaultSnippet->start();

            $this->includeIqxamplifyNotifier();

            $this->hooks();
        }

        /**
         * Hooks
         */
        protected function hooks()
        {
            // Add scripts
            add_action( 'wp_footer', array( $this, 'addScripts' ) );

            // Init rollbar
            add_action( 'init', array( $this, 'iqxamplifyPhpNotifier' ) );
            add_action( 'wp_head', array( $this, 'iqxamplifyJsNotifier' ) );
        }

        /**
         * Add scripts
         * @return string
         */
        public function addScripts()
        {
            if ( ! $this->iqxamplifyConfig->getApiKey() ) {
                return;
            }
            echo $this->snippetManager->getAllSnippetContent();
        }

        /**
         * Include iqxamplify notifier
         * @since 1.0.0
         */
        public function includeIqxamplifyNotifier()
        {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . '/IqxamplifySDK/Helper/rollbar.php';
        }

        /**
         * Js notifier
         * @since 1.0.0
         */
        public function iqxamplifyJsNotifier()
        {
            if ( ! $this->shouldInitIqxamplifyNotifier() ) {
                return;
            }

            $str_client_access_token = 'eb7ad257c4e049c6b43d66cfa620b025';
            echo
                '<script>
                var _rollbarConfig = {
                    accessToken: ' . $str_client_access_token . ',
                    captureUncaught: true,
                    payload: {
                        environment: ' . IQX_AMPLIFY_ENVIRONMENT . '
                    }
                };
                !function(r){function o(e){if(t[e])return t[e].exports;var n=t[e]={exports:{},id:e,loaded:!1};return r[e].call(n.exports,n,n.exports,o),n.loaded=!0,n.exports}var t={};return o.m=r,o.c=t,o.p="",o(0)}([function(r,o,t){"use strict";var e=t(1).Rollbar,n=t(2);_rollbarConfig.rollbarJsUrl=_rollbarConfig.rollbarJsUrl||"https://d37gvrvc0wt4s1.cloudfront.net/js/v1.8/rollbar.min.js";var a=e.init(window,_rollbarConfig),i=n(a,_rollbarConfig);a.loadFull(window,document,!_rollbarConfig.async,_rollbarConfig,i)},function(r,o){"use strict";function t(r){return function(){try{return r.apply(this,arguments)}catch(o){try{console.error("[Rollbar]: Internal error",o)}catch(t){}}}}function e(r,o,t){window._rollbarWrappedError&&(t[4]||(t[4]=window._rollbarWrappedError),t[5]||(t[5]=window._rollbarWrappedError._rollbarContext),window._rollbarWrappedError=null),r.uncaughtError.apply(r,t),o&&o.apply(window,t)}function n(r){var o=function(){var o=Array.prototype.slice.call(arguments,0);e(r,r._rollbarOldOnError,o)};return o.belongsToShim=!0,o}function a(r){this.shimId=++s,this.notifier=null,this.parentShim=r,this._rollbarOldOnError=null}function i(r){var o=a;return t(function(){if(this.notifier)return this.notifier[r].apply(this.notifier,arguments);var t=this,e="scope"===r;e&&(t=new o(this));var n=Array.prototype.slice.call(arguments,0),a={shim:t,method:r,args:n,ts:new Date};return window._rollbarShimQueue.push(a),e?t:void 0})}function l(r,o){if(o.hasOwnProperty&&o.hasOwnProperty("addEventListener")){var t=o.addEventListener;o.addEventListener=function(o,e,n){t.call(this,o,r.wrap(e),n)};var e=o.removeEventListener;o.removeEventListener=function(r,o,t){e.call(this,r,o&&o._wrapped?o._wrapped:o,t)}}}var s=0;a.init=function(r,o){var e=o.globalAlias||"Rollbar";if("object"==typeof r[e])return r[e];r._rollbarShimQueue=[],r._rollbarWrappedError=null,o=o||{};var i=new a;return t(function(){if(i.configure(o),o.captureUncaught){i._rollbarOldOnError=r.onerror,r.onerror=n(i);var t,a,s="EventTarget,Window,Node,ApplicationCache,AudioTrackList,ChannelMergerNode,CryptoOperation,EventSource,FileReader,HTMLUnknownElement,IDBDatabase,IDBRequest,IDBTransaction,KeyOperation,MediaController,MessagePort,ModalWindow,Notification,SVGElementInstance,Screen,TextTrack,TextTrackCue,TextTrackList,WebSocket,WebSocketWorker,Worker,XMLHttpRequest,XMLHttpRequestEventTarget,XMLHttpRequestUpload".split(",");for(t=0;t<s.length;++t)a=s[t],r[a]&&r[a].prototype&&l(i,r[a].prototype)}return r[e]=i,i})()},a.prototype.loadFull=function(r,o,e,n,a){var i=function(){var o;if(void 0===r._rollbarPayloadQueue){var t,e,n,i;for(o=new Error("rollbar.js did not load");t=r._rollbarShimQueue.shift();)for(n=t.args,i=0;i<n.length;++i)if(e=n[i],"function"==typeof e){e(o);break}}"function"==typeof a&&a(o)},l=!1,s=o.createElement("script"),u=o.getElementsByTagName("script")[0],p=u.parentNode;s.crossOrigin="",s.src=n.rollbarJsUrl,s.async=!e,s.onload=s.onreadystatechange=t(function(){if(!(l||this.readyState&&"loaded"!==this.readyState&&"complete"!==this.readyState)){s.onload=s.onreadystatechange=null;try{p.removeChild(s)}catch(r){}l=!0,i()}}),p.insertBefore(s,u)},a.prototype.wrap=function(r,o){try{var t;if(t="function"==typeof o?o:function(){return o||{}},"function"!=typeof r)return r;if(r._isWrap)return r;if(!r._wrapped){r._wrapped=function(){try{return r.apply(this,arguments)}catch(o){throw o._rollbarContext=t()||{},o._rollbarContext._wrappedSource=r.toString(),window._rollbarWrappedError=o,o}},r._wrapped._isWrap=!0;for(var e in r)r.hasOwnProperty(e)&&(r._wrapped[e]=r[e])}return r._wrapped}catch(n){return r}};for(var u="log,debug,info,warn,warning,error,critical,global,configure,scope,uncaughtError".split(","),p=0;p<u.length;++p)a.prototype[u[p]]=i(u[p]);r.exports={Rollbar:a,_rollbarWindowOnError:e}},function(r,o){"use strict";r.exports=function(r,o){return function(t){if(!t&&!window._rollbarInitialized){var e=window.RollbarNotifier,n=o||{},a=n.globalAlias||"Rollbar",i=window.Rollbar.init(n,r);i._processShimQueue(window._rollbarShimQueue||[]),window[a]=i,window._rollbarInitialized=!0,e.processPayloads()}}}}]);

            </script>';
        }

        /**
         * Php notifier
         * @since 1.0.0
         */
        public function iqxamplifyPhpNotifier()
        {
            if ( ! $this->shouldInitIqxamplifyNotifier() ) {
                return;
            }

            $config = array(
                'access_token' => esc_attr(trim('eb3eb41a89e444dfa61d777056ebb86e')),
                'environment' => IQX_AMPLIFY_ENVIRONMENT,
                'root' => plugin_dir_path( dirname( __FILE__ ) ),
                'max_errno' => esc_attr(trim(16384))
            );

            // installs global error and exception handlers
            Rollbar::init($config);
        }

        /**
         * Should init iqxamplify notifier
         *
         * @since 1.0.0
         * @return bool
         */
        public function shouldInitIqxamplifyNotifier()
        {
            if ( IQX_AMPLIFY_ENVIRONMENT == 'local' ) {
                return false;
            }

            $current_uri = $_SERVER["REQUEST_URI"];

            if ( $current_uri == '/wp-admin/' ) {
                return true;
            }

            if ( strpos( $current_uri, 'iqxamplify') !== false ) {
                return true;
            }

            if ( strpos( $current_uri, 'wp-admin/index.php') !== false ) {
                return true;
            }

            if ( strpos( $current_uri, 'wp-admin/plugin.php') !== false ) {
                return true;
            }

            return false;
        }
    }

endif;
