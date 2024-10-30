<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php

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
  $popupImage = "https://iqxstatic.s3-us-west-1.amazonaws.com/POP-UP-image.png";
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

wp_head();

?>
</head>
<!-- ControllerAs syntax -->
<!-- Main controller with serveral data used in Inspinia theme on diferent view -->
<!-- <body ng-controller="MainCtrl as main"> -->
<body>
  <div class="page-loader hide">
  </div>
  <div class="container">
    <div class="row header-row">
      <div class="col-xs-12">
        <h2 class=""><?php _e($blogName); ?> eCommerce Store</h2>
      </div>

    </div>
    <div class="row">
      <div class="iqx-amplify-modal-spinner-wrapper hide">
        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="iqx-amplify-optin-image-wrapper">
          <img class="iqx-amplify-optin-image" src="<?php _e($popupImage); ?>">
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <h2 class="subscribe-now"><?php _e($popupHeadline); ?></h2>
        <h2 class="thanks-for-subscribing hide">Thanks for subscribing to our store!</h2>
        <div class="row">
          <div class="col-lg-12 z-index-0">

            <div class="wrapper">
              <input style="border-color : <?php _e($popupBorder); ?>;" class="iqx-amplify-phone-input" class="form-control" autocomplete="off" value="" placeholder="(201) 555-0123" type="tel">
              <div class="iqx-amplify-inline-validation-wrapper">
                <i id="iqx-amplify-modal-error-msg" class="fa fa-times red hide"></i>
                <i id="iqx-amplify-modal-valid-msg" class="fa fa-check green hide"></i>
              </div>
              <button style="background-color : <?php _e($popupFill); ?>;border-color : <?php _e($popupBorder); ?>;color : <?php _e($popupColour); ?>;" class="iqx-amplify-subscribe-button">Subscribe</button>
            </div>


          </div>
        </div>
      </div>
      <div class="iqx-amplify-modal-footer">
        By subscribing, you consent to receive text messages sent by automatic telephone dialing system.
        Please note that contsent to these terms is not a condition of purchase. You can unsubscribe any time.
        Regular carrier charges will apply.
      </div>

    </div>
    <div  role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
      <div>
        <h2 class="iqx-amplify-modal-title hide iqx-amplify-subscribed-title">You are now subscribed!</h2>

        <div class="iqx-amplify-optin-wrapper">


          <h2 class="iqx-amplify-modal-success-message hide">You will receive a text message momentarily confirming your subscription.</h2>
          <div class="iqx-amplify-phone-input-wrapper">

          </div>
        </div>
      </div>
      <br>

    </div>
  </div>
</body>
</html>
