<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!defined('IQX_AMPLIFY_PATH')) {
    define( 'IQX_AMPLIFY_PATH', 'https://api.iqxamplify.com' );
}
if (!defined('IQX_AMPLIFY_LOGIN')) {
    define( 'IQX_AMPLIFY_LOGIN', 'https://woo-app.iqxamplify.com' );
}

define('IQX_AMPLIFY_CDN', 'https://cdn.iqxamplify.com');
define('IQX_AMPLFY_VERSION', '1.0.0');
define('IQX_AMPLFY_INSERTJS', plugin_dir_url(__FILE__).'lib/js');
define('IQX_AMPLFY_INSERTCSS', plugin_dir_url(__FILE__).'lib/css');
define('IQX_AMPLFY_INC_DIR', plugin_dir_path(__FILE__).'inc');
