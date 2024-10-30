<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Autoloading support PHP 5.2.
 *
 * @param $className
 */
function iqxamplifyAutoload($className)
{
    $extensions = array('.php', '.class.php', '.inc');
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $className = str_replace('_', DIRECTORY_SEPARATOR, $className);
    foreach ($paths as $path) {
        $filename = $path.DIRECTORY_SEPARATOR.$className;
        foreach ($extensions as $ext) {
            if (is_readable($filename.$ext)) {
                require_once $filename.$ext;
                break;
            }
        }
    }
}

set_include_path(IQX_AMPLIFY_PLUGIN_DIR);
spl_autoload_register('iqxamplifyAutoload');
