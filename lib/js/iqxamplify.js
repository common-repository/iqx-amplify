jQuery(document).ready(function($) {
    // Listen post message from iframe register
    window.onmessage = function(event) {
      // alert(event.data.name);
        if (typeof event.data.success !== 'undefined') {
            if (event.data.success && event.data.api_key) {
                var redirectLink = '';
                // if (event.data.app) {
                //     if (typeof eval('iqxamplify_menu_url_' + event.data.app) !== 'undefined') {
                //         redirectLink = eval('iqxamplify_menu_url_' + event.data.app);
                //     }
                // } else {
                //     if (typeof iqxamplify_menu_url !== 'undefined') {
                //         redirectLink = iqxamplify_menu_url;
                //     }
                // }

                if (typeof iqxamplify_menu_url !== 'undefined') {
                    redirectLink = iqxamplify_menu_url;
                }

                if (redirectLink) {
                    window.location.href = redirectLink + '&iqxamplify_api_key=' + event.data.api_key;
                } else {
                    window.location.reload();
                }
            } else if (!event.data.success && event.data.message) {
                if (event.data.message) {
                    alert(event.data.message);
                }

                window.location.reload();
            }
        }
    };

    if (typeof iqxamplify_popup_btn != 'undefined' || typeof iqxamplify_more_apps != 'undefined') {
        var curUrl = window.location.href,
            topIqxamplifyMenu = $('#toplevel_page_iqxamplify_menu');

        topIqxamplifyMenu.find('.wp-submenu-wrap').hide();
        if (curUrl.indexOf('bk_') == -1 && curUrl.indexOf('iqxamplify_') == -1) {

            if (typeof iqxamplify_popup_btn != 'undefined' && iqxamplify_popup_btn) {
                topIqxamplifyMenu.find('.wp-menu-name').append('<div class="iqx-dashboard-cta"></div>');
                topIqxamplifyMenu.addClass('tracking-target').data('event', 'wc_sidebarbutton_clickhere');
            } else {
                topIqxamplifyMenu.find('.wp-menu-name').append('<span class="iqx-more-apps-count">' + iqxamplify_more_apps + '</span>');
            }
        }
    }

    var enterEmailFormContainer = $('.iqx-enter-email-form'),
        registrationFormContainer = $('.iqx-registration-form-block'),
        enterPasswordForm = $('.iqx-enter-password-form');

    $('#iqx-email').on('keyup',function(eve){
        var key = eve.keyCode || eve.which ;
        if (key == 13) {
            eve.preventDefault();
            enterEmailFormContainer.find('.iqx-btn-email-check').click();
        }
    });

    enterEmailFormContainer.find('.iqx-btn-email-check').on('click', function(e) {
        var emailError = $('.email-error');
        var email = enterEmailFormContainer.find('#iqx-email').val();
        emailError.html('');
        if (!email) {
            emailError.html('This field is required.');
            emailError.show();
            return false;
        }
        $.post(iqxamp_vars.verify_user, {email: email, checkPassword: true}, function(response) {
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
    });

    var registrationForm = registrationFormContainer.find('.registration-form');
    var submitBtn = registrationForm.find('#iqx-btn-submit');

    registrationForm.find('input').on('keyup', function() {
       $(this).next('.iqx-error-message').hide();
    });

    submitBtn.on('click', function(e) {
        e.preventDefault();
        $(this).prop('disabled', true);
        var shouldStop = false;
        registrationForm.find('input').each(function(index) {
            if ($(this).val() == '') {
                $(this).next('.iqx-error-message').show();
                submitBtn.prop('disabled', false);
                if (!shouldStop) {
                    $(this).focus();
                }
                shouldStop = true;
            }
        });
        if (shouldStop) {
            return false;
        }
        $('.indicator').show();
        $.ajax({
            url: iqxamp_vars.add_user_and_shop,
            method: 'POST',
            data: registrationForm.serialize(),
            success: function (data) {
                if (typeof data.success != 'undefined' && data.success) {
                    window.location.href = iqxamplify_menu_url + '&iqxamplify_api_key=' + data.data.api_key;
                }
            }
        });

        return false;
    });

    var loginForm = $('.iqx-registration-group').find('.login-form');
    var loginBtn = loginForm.find('#iqx-btn-login');
    loginBtn.on('click', function(e) {
        e.preventDefault();
        $(this).prop('disabled', true);
        var errorMessage = loginForm.find('.iqx-login-error');

        $.get(iqxamp_vars.get_shop_api, {
            email: loginForm.find('.user-email').html(),
            password: loginForm.find('#login_pass').val(),
            absolutePath: loginForm.find('input[name="path"]').val()
        }, function(response) {
            if (typeof response.success != 'undefined' && response.success) {
                window.location.href = iqxamplify_menu_url + '&iqxamplify_api_key=' + response.api;
            } else if (typeof response.success != 'undefined' && !response.success) {
                loginBtn.prop('disabled', false);
                errorMessage.html(response.message);
                errorMessage.show();
            }
        });


        return false;
    });

    var accessKeyForm = $('.access-key-form');
    var accessBtn = accessKeyForm.find('#iqx-btn-access');

    accessBtn.on('click', function(e) {
        e.preventDefault();
        var key = accessKeyForm.find('#access_key').val();
        if (!key) {
            alert('Please enter access key');
            return false;
        }
        $.get(iqxamp_vars.verify_api + key, {platform: 'woocommerce'}, function(response) {
            if (typeof response.status != 'undefined' && response.status) {
                window.location.replace(accessKeyForm.attr('action') + '&iqxamplify_api_key=' + key);
            } else {
                alert('Invalid api key');
            }
        });

        return false;
    });
});
