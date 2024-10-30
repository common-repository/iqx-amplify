<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_user = wp_get_current_user();

?>
<div class="choose-apps-content">
<?php if (isset($hasAccessKey) && $hasAccessKey) {
    ?>
    <?php if (isset($iqxamplifyAppsData['your_apps']) && count($iqxamplifyAppsData['your_apps']) > 0) {
        ?>

        <div id="is-installed-header">
            <span class="logo">
                <img class="iqx-logo" src="<?php echo plugins_url('lib/img/iQXAmplifyBlack.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                     alt="">
            </span>

            <h1>Inteligence Multiplied</h1>
        </div>


        <ul class="more-apps-container is-installed">
            <h3>Installed apps </h3>
            <?php foreach ($iqxamplifyAppsData['your_apps'] as $app) {
            ?>
                <li>
                    <div class="install-apps-container">
                        <a href="<?php echo $app['install_url']; ?>" target="_blank">
                            <img
                                src="<?php echo $app['banner']; ?>"
                                alt="<?php echo $app['name']; ?>">
                            <span><?php echo $app['name']; ?></span>
                        </a>

                        <div class="apps-status">
                            <?php if ($app['is_trial_expired']): ?>
                                <a href="<?php echo $app['install_url']; ?>" target="_blank" class="iqx-button iqx-btn-warning free-trial-expired-status">Free Trial Expired</a>
                            <?php elseif ($app['is_expired']): ?>
                                <a href="<?php echo $app['install_url']; ?>" target="_blank" class="iqx-button iqx-btn-warning free-trial-status">App Expired</a>
                            <?php elseif ($app['is_free_trial']): ?>
                                <a href="<?php echo $app['install_url']; ?>" target="_blank" class="iqx-button iqx-btn-main free-trial-status">In Free Trial</a>
                            <?php endif; ?>
                        </div>

                        <div class="button-apps">
                            <a href="<?php echo $app['install_url']; ?>" class="apps-manager"
                               target="_blank"><?php echo $app['is_expired'] ? 'Upgrade' : 'Manage'; ?></a>

                            <a href="javascript: void(0);" class="iqxamplify-remove-app notice-dismiss"
                               data-url="<?php echo $app['uninstall_url']; ?>" data-name="<?php echo $app['name']; ?>"></a>
                            <br>
                        </div>
                    </div>
                </li>
            <?php
        } ?>
        </ul>

        <div id="header-review-request">
            <img src="<?php echo plugins_url('lib/img/icon_love.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
            <p>We will love you forever if you
                <a target="_blank" href="<?php echo $this->iqxamplifyConfig->getReviewUrl(); ?>">leave a review here</a> of the Amplify plugin.</p>
        </div>
        <div style="clear: both;"></div>
        <br>
    <?php
    } else {
        ?>
      <div id="is-installed-header" class="iqx-logo-header">
          <span class="logo">
              <img class="iqx-logo" src="<?php echo plugins_url('lib/img/iQXAmplifyPlain.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                   alt="">
          </span>

      </div>

        <div class="more-app-page clearfix">
            <h1>Your eCommerce store has successfully integrated!</h1>
            <h2>
                Get started with 100 free credits!
            </h2>

            <a target="_blank" href="<?php echo $this->iqxamplifyConfig->getSignInUrl(); ?>">
              <div class="button-wrapper free-form" style="font-family: &quot;Droid Sans&quot;, sans-serif;color: rgb(255, 255, 255) !important;z-index: 1;right: auto;bottom: auto;line-height: 32px;font-size: 14px;display: block;margin-top: 43px;">
                <span class="btn btn-primary purchase-button button pull-left" style="font-size: 17px;line-height: 34px;height: 33px;background-color: rgb(241, 89, 42); border-color: rgb(241, 89, 42); border-width: 0px; border-radius: 2px;"><span class="editor" contenteditable="false" style="color: white;">Click here to access your account</span></span>
              </div>
            </a>
        </div>
    <?php
    } ?>

    <?php if (isset($iqxamplifyAppsData['more_apps']) && count($iqxamplifyAppsData['more_apps']) > 0) {
        ?>
        <h3 class="app-installed-heading">More apps to boost your revenue</h3>

        <div class="more-apps-wrap">
            <ul class="more-apps-container">
                <?php foreach ($iqxamplifyAppsData['more_apps'] as $app) {
            ?>
                    <li>
                        <div class="analytic-mark" style="width: 312px;">
                            <?php if ($app['featured'] && empty($iqxamplifyAppsData['your_apps'])): ?>
                                <div class="icon-featured">
                                    <img src="<?php echo plugins_url('lib/img/icon_featured.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
                                </div>
                            <?php endif; ?>

                            <div class="appcard">
                                <div class="apps-inner">
                                    <a target="_blank" href="<?php echo $app['install_url']; ?>" class="appcard-overlay"></a>
                                    <div class="apps-content-wrapper">
                                        <div class="apps-content" style="background-image: url('<?php echo $app['img']; ?>')">
                                            <ul>
                                                <li class="apps-banner"></li>
                                                <li class="apps-name apps-rating">
                                                    <span class="name-app"><?php echo $app['name']; ?></span>
                                                    <span data-review-type="star" class="appcard-rating-star-halves"></span>
                                                </li>
                                                <li class="apps-price"><?php echo $app['price']; ?></li>
                                                <li class="apps-rating-description">
                                                    <span data-review-type="star" class="appcard-rating-star-halves"></span>
                                                </li>
                                                <li class="apps-description">
                                                    <?php echo $app['short_description']; ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="button-apps clearfix">
                                <div style="margin-top: 10px;">
                                    <div style="float: left;">
                                        <a href="<?php echo $app['info_url'].'?utm_channel=cs&utm_medium=woobackend&utm_term=bktplugindashboard'; ?>" target="_blank">More info</a>
                                    </div>

                                    <div style="float: right;">
                                        <button type="button" class="button button-primary" onClick="window.open('<?php echo $app['install_url'].'&utm_channel=cs&utm_medium=woobackend&utm_term=bktplugindashboard'; ?>', '_blank');">
                                            <?php if ($app['is_expired']): ?>
                                                Get App
                                            <?php else: ?>
                                                Try it for FREE
                                            <?php endif; ?>
                                        </button>
                                    </div>

                                    <div style="clear: both;"></div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php
        } ?>
            </ul>
        </div>

        <div style="clear: both;"></div>

        <br />
    <?php
    } ?>

    <?php if (isset($iqxamplifyAppsData['your_apps']) && count($iqxamplifyAppsData['your_apps']) == 0) {
        ?>
        <!-- Reason choose apps -->
        <div id="more-app-feature">
            <div class="container">
                <div class="reason-choose-app">
                    <div class="reason-choose-app-container">
                        <div class="no-risk">
                            <div class="choose-apps-img"></div>
                            <div class="reason-choose-apps-content">
                                <h3>No Risk</h3>
                                <p>Free trial for all apps. Cancel anytime you want.</p>
                            </div>
                        </div>
                    </div>
                    <div class="reason-choose-app-container">
                        <div class="increase">
                            <div class="choose-apps-img"></div>
                            <div class="reason-choose-apps-content">
                                <h3>10% - 30%</h3>
                                <p>The average conversion rate that customers achieve with our apps.</p>
                            </div>
                        </div>
                    </div>
                    <div class="reason-choose-app-container">
                        <div class="awesome-support">
                            <div class="choose-apps-img"></div>
                            <div class="reason-choose-apps-content">
                                <h3>Awesome Support</h3>
                                <p>Free lifetime support. Your success is our priority.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Feedback Slider -->
        <div id="customer-feedback">
            <h3>Amplify powers 100,000+ businesses </br>
                to sell over 90 million dollars worth of products </h3>

            <div class="customer-feedback-container">
                <div class="customer_feedback_slider">

                    <input type="radio" name="slider" title="slide1" checked="checked"
                           class="customer_feedback_slider__nav"/>
                    <input type="radio" name="slider" title="slide2" class="customer_feedback_slider__nav"/>
                    <input type="radio" name="slider" title="slide3" class="customer_feedback_slider__nav"/>
                    <input type="radio" name="slider" title="slide4" class="customer_feedback_slider__nav"/>
                    <input type="radio" name="slider" title="slide4" class="customer_feedback_slider__nav"/>
                    <input type="radio" name="slider" title="slide4" class="customer_feedback_slider__nav"/>


                    <div class="customer_feedback_content">
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop1.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">Great App!! exactly what we wanted and does better job than
                                expected!</p>

                            <h2 class="slider_shopname">Zuliwholesale </h2>
                            <a href="http://www.zuliwholesale.in/" class="slider_shoplink">zuliwholesale.in </a>
                        </div>
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop2.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">Very easy to install and use...and the free version already gives me so
                                many fantastic features!</p>

                            <h2 class="slider_shopname">Moda Africana Collection-o</h2>
                            <a href="https://shop.modafricana.com/" class="slider_shoplink">shop.modafricana.com</a>
                        </div>
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop3.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">This is exactly what we need for our shopping platform. Prominent
                                discount coupon display yet professional looking.</p>

                            <h2 class="slider_shopname">You By Pontus </h2>
                            <a href="http://www.pontusfolio.com/" class="slider_shoplink">pontusfolio.com</a>
                        </div>
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop4.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">I loved the app because it did exactly what I wanted it to which was to
                                encourage followers. The majority of my customers utilize the discount on their purchases
                                and I steadily experienced growth in the amount of Followers particularly on Instagram.</p>

                            <h2 class="slider_shopname">Shop Total Chicness </h2>
                            <a href="http://www.shoptotalchicness.com/" class="slider_shoplink">shoptotalchicness.com</a>
                        </div>
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop5.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">Easy to install and very easy to use.. love the exit pop up, we use it
                                as an incentive with a discount code which has increased our sales significantly. Very
                                pleased we decided to try this app... well worth the investment. Thanks Better Coupon
                                Box!</p>

                            <h2 class="slider_shopname">Skikey </h2>
                            <a href="http://usa.skikey.com/" class="slider_shoplink">usa.skikey.com</a>
                        </div>
                        <div class="slider_contents">
                            <img src="<?php echo plugins_url('lib/img/shop-logo/shop6.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>"
                                 alt="">

                            <p class="slider_review">It works amazing! Very easy to install and take your store connection
                                to another level by grabbing customers attention with a special discount pop up! Thanks</p>

                            <h2 class="slider_shopname">1130 Worldwide</h2>
                            <a href="https://www.1130worldwide.com/" class="slider_shoplink">1130worldwide.com</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="try-this-cta">
                <button class="button button-primary button-hero btn-try-this">Great, Let's Try It</button>
            </div>
        </div>
    <?php
    } ?>

<?php
} ?>

<div id="iqxamplify-extend-footer">
    <div class="group">
        <div class="img">
            <img src="<?php echo plugins_url('lib/img/icon_support.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
        </div>
        <div class="description">
            <h3>Need our support ?</h3>
            <p>Send us an email at support@iqxamplify.com. Our agent will get in touch with you shortly to assist.</p>
        </div>
    </div>

    <div class="group">
        <div class="img">
            <img src="<?php echo plugins_url('lib/img/icon_love.png', IQX_AMPLIFY_PLUGIN_DIRNAME); ?>" alt="">
        </div>
        <div class="description">
            <h3>Love these apps ?</h3>
            <p>Let us know if you love our apps and support by <a target="_blank" href="<?php echo $this->iqxamplifyConfig->getReviewUrl(); ?>">leaving us an honest review here</a>. We will be eternally grateful for having your support :-)</p>
        </div>
    </div>
</div>

<div id="remove-apps">
    <div class="modal-backdrop"></div>
    <div id="remove-app-modal" class="modal close-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Remove <span class="wc_app_name"></span></h3>
            </div>
            <div class="modal-content">
                This action will remove the <b class="wc_app_name"></b> app from your store, do you wish to continue?
            </div>
            <div class="modal-footer">
                <div class="button-group">
                    <button type="button" class="button button-cancel">Cancel</button>
                    <button type="button" class="button button-delete button-warning">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    #message.error {
        background-color: #dd3d36;
        color: #fff;
        text-align: center;
    }

    #message.updated {
        background-color: #7ad03a;
        text-align: center;
        color: #fff;
    }
</style>
