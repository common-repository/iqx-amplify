<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_user = wp_get_current_user();

?>
<script>
    window.location.href = '<?php echo $url; ?>';
</script>
