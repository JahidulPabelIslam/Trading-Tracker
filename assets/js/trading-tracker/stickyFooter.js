window.tt = window.tt || {};
window.tt.stickyFooter = (function (jQuery) {

    "use strict";

    //Expands height of section to create sticky footer
    var expandSection = function() {
        //makes section default height to work out if content is too small or big
        jQuery('main').height("auto");

        //calculates the default height of the content
        var height = jQuery('nav').outerHeight(true) + jQuery('main').outerHeight(true) + jQuery('footer').outerHeight(true);

        //checks if default height of content is shorter than screen height
        if (height < jQuery(window).height()) {

            //section is extended to fill the difference
            jQuery('main').height((jQuery(window).height() - height) + jQuery('main').height());
        }
    };

    jQuery(window).on("load orientationchange resize", expandSection);

    return {
        "expandSection" : expandSection
    };
})(jQuery);

