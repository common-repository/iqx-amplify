<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Class IqxamplifySDK_Api_IqxamplifyShop.
 *
 * @class		IqxamplifySDK_Api_IqxamplifyShop
 * @version		1.0.0
 * @author		Iqxamplify
 */
if (!class_exists('IqxamplifySDK_Api_IqxamplifyThankYou')):

    class IqxamplifySDK_Api_IqxamplifyThankYou
    {
        /**
         * Constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            add_action('woocommerce_thankyou', array($this, 'init_thank_you'));
        }

        /**
         * Init modal whitelisting.
         *
         * @since 1.0.0
         */
        public function init_thank_you($order_id)
        {
            global $woocommerce;

            $order = new WC_Order($order_id);

            $user_id = $order->get_customer_id();

            $user_white_listed = wp_filter_nohtml_kses(get_user_meta($user_id, '_iqxamplify_user_white_listed', true));
            $send_whitelist_email = wp_filter_nohtml_kses(get_option('iqxamplify_send_whitelist_email'));
            $show_whitelist_popup = wp_filter_nohtml_kses(get_option('iqxamplify_show_whitelist_popup'));

            if (!$user_white_listed) {
                if ($send_whitelist_email == 'true') {

                    $userUuid = get_user_meta( $user_id, 'iqxamplify_uuid' , true );

                    if ( empty( $userUuid ) ) {
                        $userUuid = iqxamplify_generate_uuid();
                        update_user_meta( $user_id, 'iqxamplify_uuid', $userUuid );
                    }

                    $userUrl = get_site_url();

                    $userUrl .= '?iqxamplify_send_action=white-list&id='.$user_id.'&uuid='.$userUuid;

                    $mailer = $woocommerce->mailer();

                    $billing_first_name = wp_filter_nohtml_kses(get_user_meta($user_id, 'billing_first_name', true));

                    $billing_email = wp_filter_nohtml_kses(get_user_meta($user_id, 'billing_email', true));

                    $messageBody = '<p>Hi '.$billing_first_name.', thank you for your recent purchase. We really appreciate your ';
                    $messageBody .= "business! Make sure you don't miss out on special offers, discounts and more by subscribing to our instant text messages. </p>";

                    $messageBody .= '<p><a class="link" href="'.$userUrl.'">Subscribe</a></p>';

                    $messageBody .= "<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p style='FONT-SIZE: 10px;'>".get_bloginfo('name', 'display').' will not share your information, your mobile number is secure and will remain private.By subscribing, you consent to ';
                    $messageBody .= ' receive text messages sent by an automatic telephone dialing system. Please note that consent to these terms is not a condition of purchase. You can unsubcribe at any time. Regular carrier charges will apply. ';

                  // Buffer
                  ob_start();

                    do_action('woocommerce_email_header', 'Thanks for your purchase!');

                    echo $messageBody;

                    do_action('woocommerce_email_footer');

                  // Get contents
                  $message = ob_get_clean();

                  // Cliente email, email subject and message.
                  $mailer->send($billing_email, 'Thanks for your purchase!', $message);
                }

                if ($show_whitelist_popup == 'true') {
                    $hasPopup = true;
                    $billing_phone_number = wp_filter_nohtml_kses(get_user_meta($user_id, 'billing_phone', true));
                    $plugin_dir = plugin_dir_url(__FILE__);

                    if (!empty(get_option('iqxamplify_popup_headline'))) {
                      $popupHeadline = wp_filter_nohtml_kses(get_option('iqxamplify_popup_headline'));
                    } else {
                      $popupHeadline = "Subscribe now for instant offers and specials:";
                    }

                    if (!empty(get_option('iqxamplify_popup_sub_headline'))) {
                      $popupSubHeadline = wp_filter_nohtml_kses(get_option('iqxamplify_popup_sub_headline'));
                    } else {
                      $popupSubHeadline = "Thanks for visiting our store!";
                    }

                    if (!empty(get_option('iqxamplify_popup_image'))) {
                      $popupImage = esc_url(get_option('iqxamplify_popup_image'));
                    } else {
                      $hasPopup = false;
                      // $popupImage = "https://iqxstatic.s3-us-west-1.amazonaws.com/POP-UP-image.png";
                    }

                    if (!empty(get_option('iqxamplify_popup_fill'))) {
                      $popupFill = wp_filter_nohtml_kses(get_option('iqxamplify_popup_fill'));
                    } else {
                      $popupFill = "#0D54C2";
                    }

                    if (!empty(get_option('iqxamplify_popup_border'))) {
                      $popupBorder = wp_filter_nohtml_kses(get_option('iqxamplify_popup_border'));
                    } else {
                      $popupBorder = "#95989A";
                    }

                    if (!empty(get_option('iqxamplify_popup_colour'))) {
                      $popupColour = wp_filter_nohtml_kses(get_option('iqxamplify_popup_colour'));
                    } else {
                      $popupColour = "#fff";
                    }

                    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
                    wp_enqueue_style('iqx_amplify_remodal_style',  IQX_AMPLFY_INSERTCSS.'/remodal.css');
                    wp_enqueue_style('iqx_amplify_remodal_theme_style',  IQX_AMPLFY_INSERTCSS.'/remodal-default-theme.css');
                    wp_enqueue_style('iqx_amplify_intl_tel_style',  IQX_AMPLFY_INSERTCSS.'/intlTelInput.css');
                    wp_enqueue_style('iqx_amplify_style',  IQX_AMPLFY_INSERTCSS.'/styles.css');

                    wp_enqueue_script('jquery');
                    wp_enqueue_script('iqx_amplify_remodal_script',  IQX_AMPLFY_INSERTJS.'/remodal.js');
                    wp_enqueue_script('iqx_amplify_intl_tel_script',  IQX_AMPLFY_INSERTJS.'/intlTelInput.js');

                    wp_enqueue_script('iqx_amplify_exit_script',  IQX_AMPLFY_INSERTJS.'/iqxAmplifyExit.js', true, IQX_AMPLFY_VERSION);

                    $params = array(
                      'baseUrl' => plugin_dir_url(__FILE__),
                      'iqxUrl' => IQX_AMPLIFY_PATH.'/rest-api/v1/verify_number',
                      'userId' => get_current_user_id(),
                      'blogUrl' => get_site_url(),
                      'phoneNumber' => $billing_phone_number,
                    );

                    wp_localize_script('iqx_amplify_exit_script', 'iqx_amplify_exit_params', $params);

                    if ($hasPopup == true) {

                      include_once IQX_AMPLFY_INC_DIR.'/modals/modal.php';

                    } else {
                      include_once IQX_AMPLFY_INC_DIR.'/modals/no_image_modal.php';
                    }

                }
            }
        }

        /**
         * Init modal whitelisting.
         *
         * @since 1.0.0
         */
        public function white_list_page($user_id, $userUuid)
        {
            $user_id = intval($user_id);
            $user_white_listed = wp_filter_nohtml_kses(get_user_meta($user_id, '_iqxamplify_user_white_listed', true));
            $userSavedUuid = wp_filter_nohtml_kses(get_user_meta($user_id, 'iqxamplify_uuid', true));

            if (empty($userUuid) || $userSavedUuid != $userUuid) {
              include_once IQX_AMPLFY_INC_DIR.'/pages/error.php';
              die;
            }

          if (!$user_white_listed) {

            function iqx_remove_all_styles()
            {
                global $wp_styles;
                $wp_styles->queue = array();

                wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
                wp_enqueue_style('bootstrap',  IQX_AMPLFY_INSERTCSS.'/bootstrap.min.css');
                wp_enqueue_style('iqx_amplify_intl_tel_style',  IQX_AMPLFY_INSERTCSS.'/intlTelInput.css');
                wp_enqueue_style('iqx_amplify_style',  IQX_AMPLFY_INSERTCSS.'/pageStyles.css');
            }

            add_action('wp_print_styles', 'iqx_remove_all_styles', 99);

            $billing_phone_number = wp_filter_nohtml_kses(get_user_meta($user_id, 'billing_phone', true));
            $plugin_dir = plugin_dir_url(__FILE__);
            $blogName = get_bloginfo('name', 'display');

            wp_enqueue_script('jquery');
            wp_enqueue_script('iqx_amplify_remodal_script',  IQX_AMPLFY_INSERTJS.'/remodal.js');
            wp_enqueue_script('iqx_amplify_intl_tel_script',  IQX_AMPLFY_INSERTJS.'/intlTelInput.js');

            wp_enqueue_script('iqx_amplify_exit_script',  IQX_AMPLFY_INSERTJS.'/iqxAmplifyPageExit.js', true, IQX_AMPLFY_VERSION);

            $params = array(
              'baseUrl' => plugin_dir_url(__FILE__),
              'iqxUrl' => IQX_AMPLIFY_PATH.'/rest-api/v1/verify_number',
              'userId' => $user_id,
              'blogUrl' => get_site_url(),
              'phoneNumber' => $billing_phone_number,
            );

            wp_localize_script('iqx_amplify_exit_script', 'iqx_amplify_exit_params', $params);

            include_once IQX_AMPLFY_INC_DIR.'/pages/white-list.php';

          }
        }
    }

endif;
