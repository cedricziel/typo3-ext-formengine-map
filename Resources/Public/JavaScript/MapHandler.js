/**
 * Module: TYPO3/CMS/FormengineMap/MapHandler
 */
define(['jquery', 'jquery/autocomplete'], function ($) {
    var apiUrl = TYPO3.settings.ajaxUrls['tx_formenginemap_address_geocode_handler'];
    var MapHandler = {};

    /**
     * Initialize the whole thing.
     */
    MapHandler.init = function () {
        $('.cz-map').each(function () {
            var $mapEl = $(this);
            var $realField = $mapEl.find('input[type=hidden]');
            var mode = $mapEl.data('mode');

            if (mode === 'google_maps') {
                MapHandler.initEmbeddedMap($mapEl);
            } else if (mode === 'google_static') {
                MapHandler.initStaticMap($mapEl);
            }

            $mapEl.find('a.remove-location').click(function (e) {
                e.preventDefault();

                if (mode === 'google_static') {
                    $mapEl.find('.map-preview').remove();
                }
                $realField.val('');
                $mapEl.find('details').remove();
            });
        });
    };

    /**
     * Uses a more privacy-oriented approach, where a static map
     * is shown.
     */
    MapHandler.initStaticMap = function ($el) {
        var $realField = $el.find('input[type=hidden]');
        $el.find('input[type=text]').autocomplete({
            containerClass: 'autocomplete-results',
            serviceUrl: apiUrl,
            minLength: 2,
            onSelect: function (item, ui) {
                $realField.val(JSON.stringify(item.data));
            },
            noSuggestionNotice: '<div class="autocomplete-info">No results</div>',
            formatResult: function (suggestion, value) {

                return $('<div>')
                    .append(
                        $('<a class="autocomplete-suggestion-link" href="#">'
                            + suggestion.data.formatted_address +
                            '</a></div>').attr({
                            'data-label': suggestion.label,
                            'data-id': suggestion.place_id
                        }))
                    .html();
            },
            transformResult: function (response) {

                var results = JSON.parse(response);

                return {
                    suggestions: $.map(results.results, function (result) {

                        return {
                            "id": result.place_id,
                            "label": result.formatted_address,
                            "value": result.formatted_address,
                            "data": result
                        };
                    })
                }
            }
        });
    };

    /**
     * Uses an embedded Google Places finder.
     */
    MapHandler.initEmbeddedMap = function ($el) {
        var $formElement = $el;
        var mapContainer = $formElement.find('.map').get(0);
        var input = $formElement.find('input.controls').get(0);
        var apiKey = $formElement.data('api-key');
        var scriptUrlWithKey = 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' + apiKey;
        var $realField = $el.find('input[type=hidden]');
        var currentValue = $el.data('current-value');

        require([scriptUrlWithKey], function () {
            var map = new google.maps.Map(mapContainer, {
                center: {lat: -33.8688, lng: 151.2195},
                zoom: 13
            });

            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map
            });
            marker.addListener('click', function () {
                infowindow.open(map, marker);
            });

            if (currentValue.hasOwnProperty('place_id')) {

                if (!currentValue.geometry) {
                    return;
                }

                if (currentValue.geometry.viewport) {
                    map.fitBounds(currentValue.geometry.viewport);
                } else {
                    map.setCenter(currentValue.geometry.location);
                    map.setZoom(17);
                }

                marker.setPlace({
                    placeId: currentValue.place_id,
                    location: currentValue.geometry.location
                });
                marker.setVisible(true);

                map.setCenter(currentValue.geometry.location);
                map.setZoom(17);

                infowindow.setContent(
                    '<div><strong>' + currentValue.name + '</strong><br>' +
                    'Place ID: ' + currentValue.place_id + '<br>' +
                    currentValue.formatted_address
                );

                infowindow.open(map, marker);
            }

            autocomplete.addListener('place_changed', function () {
                infowindow.close();
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                // Set the position of the marker using the place ID and location.
                marker.setPlace({
                    placeId: place.place_id,
                    location: place.geometry.location
                });
                marker.setVisible(true);

                infowindow.setContent(
                    '<div><strong>' + place.name + '</strong><br>' +
                    'Place ID: ' + place.place_id + '<br>' +
                    place.formatted_address
                );

                infowindow.open(map, marker);

                $realField.val(JSON.stringify(place));
            });
        });
    };

    MapHandler.init();
});
