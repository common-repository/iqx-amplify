/**
 * Tracking action
 */
AppIqxamplify.tracking = function() {
    // Find tracking target like buttons, links to track
    IqxAmpJQuery('.tracking-target').each(function() {
        var event = IqxAmpJQuery(this).data('event');
        var params = IqxAmpJQuery(this).data('event-params');

        if (typeof params == 'undefined' || !params) {
            params = {};
        }

        if (IqxAmpJQuery(this).is('a') && IqxAmpJQuery(this).attr('href') && IqxAmpJQuery(this).attr('href').substring(0, 1) != '#') {
            analytics.trackLink(this, event, params);
            return;
        }

        IqxAmpJQuery(this).click(function() {
            analytics.track(event, params);
        });
    });
};

// Start app
IqxAmpJQuery(window).load(function() {
    AppIqxamplify.tracking();
});