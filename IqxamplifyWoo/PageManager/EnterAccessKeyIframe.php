<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_user = wp_get_current_user();

?>
<iframe id="iqxamplify-iframe" width="100%" style="height: 100vh;" src="<?php echo $iframeUrl; ?>">
    Your browser does not support iframe. <a href="<?php menu_page_url('iqxamplify_menu_fallback', true); ?>">Click here to try directly.</a>
</iframe>
