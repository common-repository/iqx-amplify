<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_user = wp_get_current_user();

?>
<div class="enter-access-key">
    <div class="enter-access-key-text">
        <h3>Enter Access Key</h3>

        <p>If you've already known your Access Key</p>
    </div>
    <div class="enter-access-key-form">
        <form action="<?php menu_page_url('iqxamplify_menu', true); ?>" class="form-horizontal access-key-form" method="get" role="form">
            <div class="form-group">
                <input type="text" name="iqxamplify_api_key" id="access_key" value="" class="form-control" autofocus focus/>
            </div>
            <input type="submit" class="btn btn-primary" id="iqx-btn-access" value="Update" />
        </form>
    </div>
</div>
