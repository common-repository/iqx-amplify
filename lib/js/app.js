var AppIqxamplify = {},
    IqxAmpJQuery = jQuery.noConflict();

/**
 * Init function
 */
AppIqxamplify.init = function() {
    this.uninstall();
    this.scrollChooseApps();
    this.syncPermission();

    this.resizeIframe(IqxAmpJQuery('#iqxamplify-iframe'));

    IqxAmpJQuery(window).on('resize', function() {
        AppIqxamplify.resizeIframe(IqxAmpJQuery('#iqxamplify-iframe'));
    })
};

/**
 * Uninstall app
 */
AppIqxamplify.uninstall = function() {
    IqxAmpJQuery('.iqxamplify-remove-app', '.more-apps-container').on('click', function() {
        var $this = IqxAmpJQuery(this),
            url = $this.data('url'),
            name = $this.data('name'),
            deleteModal = IqxAmpJQuery('#remove-app-modal'),
            btnDeleteModal = IqxAmpJQuery('.button-delete', deleteModal);

        IqxAmpJQuery('.wc_app_name').text(name);
        btnDeleteModal.on('click', function() {
            IqxAmpJQuery.ajax({
                url: url,
                beforeSend: function () {
                    btnDeleteModal.text('Removing...');
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        });

        IqxAmpJQuery('.modal-backdrop, .close-modal, .modal-container').addClass('is-visible');
    });

    IqxAmpJQuery('.button-cancel').on('click', function() {
        IqxAmpJQuery('.modal-backdrop, .close-modal, .modal-container').removeClass('is-visible');
    });
};

/**
 * Scroll to choose apps
 */
AppIqxamplify.scrollChooseApps = function() {
    IqxAmpJQuery('.btn-try-this').on('click', function() {
        IqxAmpJQuery('html, body').animate({
            scrollTop: IqxAmpJQuery('.icon-featured').offset().top,
            easing: 'linear'
        }, 1000);
    });
};

/**
 * Sync permission
 */
AppIqxamplify.syncPermission = function() {
    IqxAmpJQuery('.btn-continue', '#iqxamplify-sync-permission').on('click', function() {
        IqxAmpJQuery(this).attr('disabled', true);

        var data = {
            'action': 'accept_sync_permission'
        };

        IqxAmpJQuery.post(bk_js_object.ajax_url, data, function(response) {
            window.location.href = bk_js_object.plugin_url;
        });
    });
};

/**
 * Resize iframe
 * @param iframe
 */
AppIqxamplify.resizeIframe = function(iframe) {
    iframe.height(IqxAmpJQuery(window).height() - IqxAmpJQuery('#wpadminbar').height());
};

/**
 * Add google tag manager
 */
AppIqxamplify.addGoogleTagManager = function() {
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PM2GT6');
};

// Start app
IqxAmpJQuery(document).ready(function() {
    AppIqxamplify.init();
});

// Add google tag manager
AppIqxamplify.addGoogleTagManager();
