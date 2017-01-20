/**
 * Module: TYPO3/CMS/FormengineMap/MapHandler
 */
define(['jquery', 'jquery/autocomplete'], function ($) {
    $(function () {
        $.ajax({
            url: TYPO3.settings.ajaxUrls['cz_maps_geocode_handler'],
            dataType: 'text',
            cache: false,
            data: {
                "address": "Halle (Saale)"
            }
        });
    });

    var url = TYPO3.settings.ajaxUrls['cz_maps_geocode_handler'];
    console.log(url);
    $('.cz-map input[type=text]').autocomplete({
        source: url,
        minLength: 2,
        select: function( event, ui ) {
            log( "Selected: " + ui.item.value + " aka " + ui.item.id );
        }
    });
});
