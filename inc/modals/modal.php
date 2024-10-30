<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="remodal iqx-amplify-remodal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
  <div class="remodal-close-wrapper">
    <span data-remodal-action="close" class="remodal-close" aria-label="Close"></span>
  </div>
  <div>
    <div class="iqx-amplify-optin-image-wrapper">
      <img class="iqx-amplify-optin-image" src="<?php _e($popupImage); ?>">
    </div>

    <h2 class="iqx-amplify-modal-title iqx-amplify-discount-title"><?php _e($popupHeadline); ?></h2>
    <h2 class="iqx-amplify-modal-title hide iqx-amplify-subscribed-title">You are now subscribed!</h2>

    <div class="iqx-amplify-optin-wrapper">


      <h2 class="iqx-amplify-modal-success-message hide">You will receive a text message momentarily confirming your subscription.</h2>
      <div class="iqx-amplify-phone-input-wrapper">
        <input style="border-color : <?php _e($popupBorder); ?>;" class="iqx-amplify-phone-input" class="form-control" autocomplete="off" value="" placeholder="(201) 555-0123" type="tel">

        <div class="iqx-amplify-inline-validation-wrapper">
          <i id="iqx-amplify-modal-error-msg" class="fa fa-times red hide"></i>
          <i id="iqx-amplify-modal-valid-msg" class="fa fa-check green hide"></i>
        </div>
        <button style="background-color : <?php _e($popupFill); ?>;border-color : <?php _e($popupBorder); ?>;color : <?php _e($popupColour); ?>;" class="iqx-amplify-subscribe-button">Subscribe</button>
      </div>
      <div class="iqx-amplify-modal-spinner-wrapper hide">
        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
      </div>
      <h2 class="iqx-amplify-modal-thanks-title"><?php _e($popupSubHeadline); ?></h2>
    </div>
  </div>
  <br>
  <div class="iqx-amplify-modal-footer">
    By subscribing, you consent to receive text messages sent by automatic telephone dialing system.
    Please note that contsent to these terms is not a condition of purchase. You can unsubscribe any time.
    Regular carrier charges will apply.
  </div>
</div>
