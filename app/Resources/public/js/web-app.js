(function ($) {
    "use strict";

    // See https://hacks.mozilla.org/2014/03/better-integration-for-open-web-apps-on-android/
    if (window.navigator.mozApps) {
        // We're on a platform that supports the apps API.
        window.navigator.mozApps.getSelf().onsuccess = function() {
            if (this.result) {
                // We're running in an installed web app.
            } else {
                // We're running in an webpage.
                // Perhaps we should offer an install button.
                window.navigator.mozApps.install('/manifest.json')
            }
        };
    }

}(jQuery));
