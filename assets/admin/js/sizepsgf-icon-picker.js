(function (window, document, $, undefined) {
    "use strict";

    /*
    * sticky custom menu icon
    */
    // Define the array of icons
    const fontAwesomeIcons = [
        "fa-solid fa-house",
        "fa-solid fa-user",
        "fa-solid fa-users",
        "fa-solid fa-envelope",
        "fa-solid fa-phone",
        "fa-solid fa-mobile",
        "fa-solid fa-map-marker-alt",
        "fa-solid fa-globe",
        "fa-solid fa-heart",
        "fa-solid fa-star",
        "fa-solid fa-thumbs-up",
        "fa-solid fa-thumbs-down",
        "fa-solid fa-camera",
        "fa-solid fa-video",
        "fa-solid fa-music",
        "fa-solid fa-bell",
        "fa-solid fa-flag",
        "fa-solid fa-shopping-cart",
        "fa-solid fa-credit-card",
        "fa-solid fa-money-bill",
        "fa-solid fa-dollar-sign",
        "fa-solid fa-euro-sign",
        "fa-solid fa-pound-sign",
        "fa-solid fa-yen-sign",
        "fa-solid fa-wallet",
        "fa-solid fa-gift",
        "fa-solid fa-bolt",
        "fa-solid fa-cog",
        "fa-solid fa-wrench",
        "fa-solid fa-key",
        "fa-solid fa-lock",
        "fa-solid fa-unlock",
        "fa-solid fa-clock",
        "fa-solid fa-calendar",
        "fa-solid fa-file",
        "fa-solid fa-file-alt",
        "fa-solid fa-file-pdf",
        "fa-solid fa-file-word",
        "fa-solid fa-file-excel",
        "fa-solid fa-folder",
        "fa-solid fa-folder-open",
        "fa-solid fa-trash",
        "fa-solid fa-edit",
        "fa-solid fa-save",
        "fa-solid fa-print",
        "fa-solid fa-search",
        "fa-solid fa-filter",
        "fa-solid fa-sort",
        "fa-solid fa-arrow-up",
        "fa-solid fa-arrow-down",
        "fa-solid fa-arrow-left",
        "fa-solid fa-arrow-right",
        "fa-solid fa-angle-up",
        "fa-solid fa-angle-down",
        "fa-solid fa-angle-left",
        "fa-solid fa-angle-right",
        "fa-solid fa-chevron-up",
        "fa-solid fa-chevron-down",
        "fa-solid fa-chevron-left",
        "fa-solid fa-chevron-right",
        "fa-solid fa-hand-point-up",
        "fa-solid fa-hand-point-down",
        "fa-solid fa-hand-point-left",
        "fa-solid fa-hand-point-right",
        "fa-solid fa-question-circle",
        "fa-solid fa-info-circle",
        "fa-solid fa-exclamation-circle",
        "fa-solid fa-check-circle",
        "fa-solid fa-times-circle",
        "fa-solid fa-plus-circle",
        "fa-solid fa-minus-circle",
        "fa-solid fa-check",
        "fa-solid fa-times",
        "fa-solid fa-plus",
        "fa-solid fa-minus",
        "fa-solid fa-bars",
        "fa-solid fa-ellipsis-h",
        "fa-solid fa-ellipsis-v",
        "fa-solid fa-home",
        "fa-solid fa-blog",
        "fa-solid fa-rss",
        "fa-solid fa-share",
        "fa-solid fa-share-alt",
        "fa-solid fa-comment",
        "fa-solid fa-comments",
        "fa-solid fa-smile",
        "fa-solid fa-frown",
        "fa-solid fa-meh",
        "fa-solid fa-quote-left",
        "fa-solid fa-quote-right",
        "fa-solid fa-lightbulb",
        "fa-solid fa-paint-brush",
        "fa-solid fa-laptop",
        "fa-solid fa-desktop",
        "fa-solid fa-tablet",
        "fa-solid fa-tv",
        "fa-solid fa-code",
        "fa-solid fa-database",
        "fa-solid fa-server",
        "fa-solid fa-terminal",
        "fa-solid fa-cloud",
        "fa-solid fa-cloud-upload-alt",
        "fa-solid fa-cloud-download-alt",
        "fa-solid fa-download",
        "fa-solid fa-upload",
        "fa-solid fa-play",
        "fa-solid fa-pause",
        "fa-solid fa-stop",
        "fa-solid fa-forward",
        "fa-solid fa-backward",
        "fa-solid fa-fast-forward",
        "fa-solid fa-fast-backward",
        "fa-solid fa-volume-up",
        "fa-solid fa-volume-down",
        "fa-solid fa-volume-mute",
        "fa-solid fa-microphone",
        "fa-solid fa-microphone-slash",
        "fa-solid fa-headphones",
        "fa-solid fa-random",
        "fa-solid fa-repeat",
        "fa-solid fa-sync",
        "fa-solid fa-spinner",
        "fa-solid fa-paperclip",
        "fa-solid fa-book",
        "fa-solid fa-bookmark",
        "fa-solid fa-tags",
        "fa-solid fa-newspaper",
        "fa-solid fa-chart-bar",
        "fa-solid fa-chart-pie",
        "fa-solid fa-chart-line",
        "fa-solid fa-user-circle",
        "fa-solid fa-user-check",
        "fa-solid fa-user-clock",
        "fa-solid fa-user-cog",
        "fa-solid fa-user-edit",
        "fa-solid fa-user-lock",
        "fa-solid fa-user-minus",
        "fa-solid fa-user-plus",
        "fa-solid fa-user-shield",
        "fa-solid fa-user-times",
        "fa-solid fa-glasses",
        "fa-solid fa-binoculars",
        "fa-solid fa-map",
        "fa-solid fa-map-marker",
        "fa-solid fa-map-pin",
        "fa-solid fa-map-signs",
        "fa-solid fa-plane",
        "fa-solid fa-train",
        "fa-solid fa-bus",
        "fa-solid fa-car",
        "fa-solid fa-bicycle",
        "fa-solid fa-ship",
        "fa-solid fa-umbrella",
        "fa-solid fa-shopping-bag",
        "fa-solid fa-shopping-basket",
        "fa-solid fa-tree",
        "fa-solid fa-leaf",
        "fa-solid fa-recycle",
        "fa-solid fa-truck",
        "fa-solid fa-truck-moving",
        "fa-solid fa-truck-loading",
        "fa-solid fa-tools",
        "fa-solid fa-hammer",
        "fa-solid fa-wrench",
        "fa-solid fa-user-secret",
        "fa-solid fa-user-md",
        "fa-solid fa-briefcase",
        "fa-solid fa-hard-hat",
        "fa-solid fa-building",
        "fa-solid fa-landmark",
        "fa-solid fa-gavel",
        "fa-solid fa-handshake",
        "fa-solid fa-balance-scale",
        "fa-solid fa-business-time",
        "fa-solid fa-comment-dollar",
        "fa-solid fa-chart-pie",
        "fa-solid fa-hand-holding-usd",
        "fa-solid fa-donate",
        "fa-solid fa-piggy-bank",
        "fa-solid fa-wallet",
        "fa-solid fa-ruler",
        "fa-solid fa-ruler-horizontal",
        "fa-solid fa-ruler-vertical",
        "fa-solid fa-ruler-combined"
    ];
    

    // Function to generate the icon list HTML
    function generateIconList() {
        let content = '<div class="icon-list">';
        fontAwesomeIcons.forEach(function(icon) {
            content += '<span><i class="' + icon + '"></i></span>';
        });
        content += '</div>';
        return content;
    }

    $(document).ready(function() {

        $(document).on("click", ".sizepsgf-popup-icon-input", function() {
            $(this).parent().toggleClass('active');
        
            // Check if .icon-list already exists to avoid duplicating it
            if (!$(this).next('.icon-list').length) {
                $(this).after(generateIconList());
            }
        });

        $(document).on("click", ".icon-list span i", function() {
            var className = $(this).attr("class");
            var icon_field = $(this).closest('.active').find('.sizepsgf-popup-icon-input');
            icon_field.val(className);
            $(this).closest('.active').removeClass('active');
        });
    });

})(window, document, jQuery);