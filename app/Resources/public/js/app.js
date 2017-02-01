(function ($) {
    "use strict";

    $(document).ready(function () {

        var menuOpened = false;
        var menuToggle = $('#menu-toggle:not(.done)');
        var menu = $('#menu-panel');

        menuToggle.addClass('done');
        menuToggle.on("click", function (event) {
            event.stopPropagation();
            event.preventDefault();

            if (menuOpened) {
                menu.hide();
                menuOpened = false;
            } else {
                menu.show();
                menuOpened = true;
            }
        });
    });

}(jQuery));
