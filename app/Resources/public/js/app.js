(function ($) {
    "use strict";

    /**
     * From the given blocks, position them in page by replacing
     * existing content.
     */
    function replaceBlocks(blocks) {
        $.each(blocks, function(index, value) {
            var block = $('[data-target=' + index + ']');

            if (!block.length) {
                console.log("Warning, block " + index + " does not exists in page");
            } else if (1 < block.length) {
                console.log("Warning, block " + index + " exists more than once in page");
            }

            var partialDom = $(value);

            block.html(partialDom);
            block.each(function() {
                attach(this);
            });
        });
    }

    /**
     * Attaches various JavaScript behaviours onto the given HTML input
     * that spawns.
     */
    function attach(context) {

        // Ajaxified forms
        $('form.ajax').each(function () {
            $(this).ajaxForm({
                dataType: 'json',
                beforeSerialize: function() {
                    // @todo Cover the form with a loader
                    console.log("implement me");
                },
                error: function(xhr) {
                    // Errors include the fact that the response might not be
                    // JSON, as awaited, case in which we have to follow the
                    // real redirect behind to refresh the page.
                    if (200 == xhr.status) {
                        window.location.reload();
                    }
                    // @todo handle 4xx errors too
                },
                success: function(response) {
                    // @todo Remove loader
                    // Parse response
                    replaceBlocks(response);
                }
            });
        });
    }

    /**
     * On ready handler, attach global menu behaviours then
     * call attach() on the document.
     */
    $(document).ready(function () {

        // Menu behaviour
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

        attach(document);
    });

}(jQuery));
