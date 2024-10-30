(function($) {
  var modal = $('.iqx-amplify-remodal').remodal({ closeOnOutsideClick : false}),
    telInput = $(".iqx-amplify-phone-input"),
    errorMsg = $("#iqx-amplify-modal-error-msg"),
    validMsg = $("#iqx-amplify-modal-valid-msg"),
    subscriptionBtn = $(".iqx-amplify-subscribe-button"),
    phoneInput =  $(".iqx-amplify-phone-input-wrapper"),
    subscribedTitle =  $(".iqx-amplify-subscribed-title"),
    discountTitle =  $(".iqx-amplify-discount-title"),
    successMessage =  $(".iqx-amplify-modal-success-message"),
    spinner =  $(".iqx-amplify-modal-spinner-wrapper, .iqx-amplify-modal-spinner-no-image-wrapper");

    modal.open();

    telInput.intlTelInput({
      utilsScript: iqx_amplify_exit_params.baseUrl + "../../lib/js/utils.js"
    });

    telInput.intlTelInput("setNumber", iqx_amplify_exit_params.phoneNumber);

    var reset = function() {
      telInput.removeClass("error");
      errorMsg.addClass("hide");
      validMsg.addClass("hide");
    };

    // on blur: validate
    telInput.keyup(function() {
      reset();
      if ($.trim(telInput.val())) {
        if (telInput.intlTelInput("isValidNumber")) {
          validMsg.removeClass("hide");
        } else {
          telInput.addClass("error");
          errorMsg.removeClass("hide");
        }
      }
    });

    var viewUrl = iqx_amplify_exit_params.blogUrl + '?iqxamplify_send_action=capture-view&userId=' + iqx_amplify_exit_params.userId +'&type=modal';

    $.get(viewUrl, function(){});

    subscriptionBtn.click(function(){
      if (telInput.intlTelInput("isValidNumber")) {
        spinner.removeClass("hide");
        phoneInput.addClass("hide");
        subscriptionBtn.addClass("hide");

        var url = iqx_amplify_exit_params.blogUrl + "?iqxamplify_send_action=white-list-number&number=" +$('.iqx-amplify-phone-input').val().replace(/\D+/g, '') + '&userId=' + iqx_amplify_exit_params.userId;


        $.get(url, function(response) {
            if (typeof response.success != 'undefined') {
                if (response.success) {
                    $('.iqx-registration-group').find('.login-form').find('.user-email').html(email);
                    enterPasswordForm.find('.name').html(response.data.firstName);
                    enterPasswordForm.show();
                    $('#overflow').find('.inner').addClass('next-enter-pwd');
                    $('.btn-signin-account').click(function() {
                        $('#overflow').find('.inner').removeClass('next-enter-pwd');
                    });
                } else if (!response.success && typeof response.redirect != 'undefined' && !response.redirect) {
                    emailError.html(response.message);
                    emailError.show();
                } else {
                    registrationFormContainer.find('#bk_email').val(email);
                    registrationFormContainer.find('#bk_email').prop('readonly', true);
                    registrationFormContainer.show();
                    $('#overflow').find('.inner').addClass('next-enter-email');
                    $('.login-here').click(function() {
                        $('#overflow').find('.inner').removeClass('next-enter-email');
                    });
                }
            }
        });

        setTimeout(function(){
          spinner.addClass("hide");
          discountTitle.addClass("hide");
          subscribedTitle.removeClass("hide");
          successMessage.removeClass("hide");
        },3000);
      }
    });

})(jQuery);
