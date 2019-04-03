;window.tradingTracker = window.tradingTracker || {};
window.tradingTracker.stickyFooter = (function(jQuery) {

    "use strict";

    // Expands height of section to create sticky footer
    var expandSection = function() {
        var mainContent = jQuery(".main-content");

        // Makes section default height to work out if content is too small or big
        mainContent.height("auto");

        // Calculates the default height of the content
        var defaultHeight = mainContent.outerHeight(true) + jQuery(".footer").outerHeight(true);

        var windowHeight = jQuery(window).height();

        // Checks if default height of content is shorter than screen height
        if (defaultHeight < windowHeight) {

            // Section is extended to fill the difference
            mainContent.height(windowHeight - defaultHeight + mainContent.height());
        }
    };

    jQuery(window).on("load orientationchange resize", expandSection);

    return {
        expandSection: expandSection,
    };

}(jQuery));
