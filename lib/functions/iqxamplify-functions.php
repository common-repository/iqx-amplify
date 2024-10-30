<?php
/**
 * Iqxamplify functions
 */

if ( ! function_exists( 'get_local_file_contents' ) ) {
    /**
     * Get local file contents
     *
     * @since 1.0.0
     * @param $file_path
     * @return string
     */
    function get_local_file_contents( $file_path ) {
        $contents = @file_get_contents( $file_path );
        if ( !$contents ) {
            ob_start();
            @include_once( $file_path );
            $contents = ob_get_clean();
        }

        return $contents;
    }
}

/**
 * Iqxamplify high priority widget
 * @param $name
 */
function iqxamplify_high_priority_widget($name) {
    // Globalize the metaboxes array, this holds all the widgets for wp-admin
    global $wp_meta_boxes;

    // Get the regular dashboard widgets array
    // (which has our new widget already but at the end)
    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

    // Backup and delete our new dashboard widget from the end of the array
    $example_widget_backup = array( $name => $normal_dashboard[$name] );
    unset( $normal_dashboard[$name] );

    // Merge the two arrays together so our widget is at the beginning
    $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );

    // Save the sorted array back into the original metaboxes
    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function iqxamplify_add_dasboard_widgets() {
    $apiKey = get_option( 'iqxamplify_api_key' );

    if (!$apiKey) {
        $name = 'iqxamplify_dashboard_widget_banner';
        wp_add_dashboard_widget($name, 'Iqxamplify for WooCommerce', $name . '_function');
    } else {
        $totalAppsInstalled = get_option( 'iqxamplify_total_apps_installed' );
        if ( !$totalAppsInstalled ) {
            $name = 'iqxamplify_dashboard_widget_install';
            wp_add_dashboard_widget($name, 'Iqxamplify', $name . '_function');
        } else {
            $name = 'iqxamplify_dashboard_widget_connected';
            wp_add_dashboard_widget($name, 'Iqxamplify', $name . '_function');
        }
    }

    if ($name) {
        iqxamplify_high_priority_widget($name);
    }
}

/**
 * Banner dashboard widget
 */
function iqxamplify_dashboard_widget_banner_function() {
    $plugin_url = admin_url( 'admin.php?page=' . IqxamplifyWoo_PageManager_AdminPage::MAIN_ADMIN_URL );
    $iqxamplifyforwc = 'http://iqxcorp.com/?utm_channel=bktdashboard&utm_medium=banner';
    include_once( IQX_AMPLIFY_PLUGIN_DIR . 'inc/widgets/banner.php' );
}

/**
 * Install app dashboard widget
 */
function iqxamplify_dashboard_widget_install_function() {
    $plugin_url = admin_url( 'admin.php?page=' . IqxamplifyWoo_PageManager_AdminPage::MAIN_ADMIN_URL );
    include_once( IQX_AMPLIFY_PLUGIN_DIR . 'inc/widgets/install.php' );
}

/**
 * Connected dashboard widget
 */
function iqxamplify_dashboard_widget_connected_function() {
    $plugin_url = admin_url( 'admin.php?page=' . IqxamplifyWoo_PageManager_AdminPage::MAIN_ADMIN_URL );
    include_once( IQX_AMPLIFY_PLUGIN_DIR . 'inc/widgets/connected.php' );
}
