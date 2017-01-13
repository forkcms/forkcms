/**
 * Interaction for the location module
 */
jsFrontend.location =
{
    map: {},
    mapFullUrl: null,
    directionService: null,
    directionsDisplay: null,
    panorama: {},

    // init, something like a constructor
    init: function()
    {
        if($('*[data-role=fork-map-container][data-map-id]').length > 0)
        {
          $('*[data-role=fork-map-container][data-map-id]').each(function()
            {
                google.maps.event.addDomListener(
                    window,
                    'load',
                    jsFrontend.location.initMap($(this).data('map-id'))
                );
            });
        }
    },

    // init the map
    initMap: function(id)
    {
        // define some variables we will need
        var suffix = (id == 'general') ? '' : '_' + id;
        var mapId = id;
        var mapStyle = (jsFrontend.data.get('Location.settings' + suffix + '.map_style'));

          // define coordinates
        var coordinates = new google.maps.LatLng(
            jsFrontend.data.get('Location.settings' + suffix + '.center.lat'),
            jsFrontend.data.get('Location.settings' + suffix + '.center.lng')
        );

        // build the options
        var options =
        {
            zoom: (jsFrontend.data.get('Location.settings' + suffix + '.zoom_level') == 'auto') ? 0 : parseInt(jsFrontend.data.get('Location.settings' + suffix + '.zoom_level')),
            center: coordinates,
            mapTypeId: google.maps.MapTypeId[jsFrontend.data.get('Location.settings' + suffix + '.map_type')],
            styles: MAPS_CONFIG[mapStyle]
        };

        // create map
        jsFrontend.location.map[mapId] = new google.maps.Map(
            $('*[data-role=fork-map-container][data-map-id=' + id + ']')[0],
            options
        );

        // we want a streetview
        if(jsFrontend.data.get('Location.settings' + suffix + '.map_type') == 'STREET_VIEW')
        {
            // get street view data from map
            jsFrontend.location.panorama[mapId] = jsFrontend.location.map[mapId].getStreetView();

            // define position
            jsFrontend.location.panorama[mapId].setPosition(coordinates);

            // define heading (horizontal °) and pitch (vertical °)
            jsFrontend.location.panorama[mapId].setPov({
                heading: 200,
                pitch: 8
            });

            // show panorama
            jsFrontend.location.panorama[mapId].setVisible(true);
        }

        // get the items
        var items = jsFrontend.data.get('Location.items' + suffix);

        // any items
        if(items.length > 0)
        {
            // loop items
            for(var i in items)
            {
                // add markers
                jsFrontend.location.addMarker(mapId, items[i].id, items[i].lat, items[i].lng, items[i].title);
            }
        }

        // are directions enabled?
        if(jsFrontend.data.get('Location.settings' + suffix + '.directions'))
        {
            // create direction variables if needed
            if(jsFrontend.location.directionsService == null) jsFrontend.location.directionsService = new google.maps.DirectionsService();
            if(jsFrontend.location.directionsDisplay == null) jsFrontend.location.directionsDisplay = new google.maps.DirectionsRenderer();

            // bind events
            $('form[data-role=fork-directions-form][data-map-id=' + id + ']').on('submit', function(e)
            {
                e.preventDefault();
                jsFrontend.location.setRoute(id, mapId, items[0]);
            });
        }

        if($('a[data-role=fork-map-url][data-map-id=' + id + ']').length > 0) {
            jsFrontend.location.mapFullUrl = $('a[data-role=fork-map-url][data-map-id=' + id + ']').attr('href');
        }
    },

    // add a marker
    addMarker: function(mapId, id, lat, lng, title)
    {
        // add the marker
        var marker = new google.maps.Marker(
            {
                position: new google.maps.LatLng(lat, lng),
                map: jsFrontend.location.map[mapId],
                title: title,
                locationId: id
            }
        );

        // show info window on click
        google.maps.event.addListener(marker, 'click', function()
        {
            $markerText = $('*[data-role=fork-marker-text-container][data-map-id=' + marker.locationId + ']');

            // apparently JS goes bananas with multi line HTMl, so we grab it from the div, this seems like a good idea for SEO
            if($markerText.length > 0) text = $markerText.html();

            var content = '<h1>' + title + '</h1>';
            if(typeof text != 'undefined') content += text;

            new google.maps.InfoWindow(
                {
                    content: content
                }
            ).open(jsFrontend.location.map[mapId], marker);
        });
    },

    // calculate the route
    setRoute: function(id, mapId, item)
    {
        $error = $('*[data-role=fork-directions-error][data-map-id=' + id + ']');
        $search = $('*[data-role=fork-directions-start][data-map-id=' + id + ']');

        // validate
        if($search.val() == '') $error.show();
        else $error.hide();

        // build the position
        var position = new google.maps.LatLng(item.lat, item.lng);

        // build request
        var request =
        {
            origin: $search.val(),
            destination: position,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };

        // request the route
        jsFrontend.location.directionsService.route(request, function(response, status)
        {
            // did we find a route
            if(status == google.maps.DirectionsStatus.OK)
            {
                // change the map
                jsFrontend.location.directionsDisplay.setMap(jsFrontend.location.map[mapId]);

                // render the route
                jsFrontend.location.directionsDisplay.setDirections(response);

                // change the link
                if (jsFrontend.location.mapFullUrl != null) {
                    // get "a"-link element
                    var $item = $('a[data-role=fork-map-url][data-map-id=' + id + ']');

                    // d = directions
                    var href = jsFrontend.location.mapFullUrl + '&f=d&saddr=' + $search.val() + '&daddr=' + position;

                    // update href
                    $item.attr('href', href);
                }
            }

            // show error
            else $error.show();
        });
    }
};

$(jsFrontend.location.init);
