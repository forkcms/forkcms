/**
 * Interaction for the location module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
jsFrontend.location =
{
	map: {},
	mapFullUrl: null,
	directionService: null,
	directionsDisplay: null,

	// init, something like a constructor
	init: function()
	{
		if($('.parseMap').length > 0)
		{
			$('.parseMap').each(function()
			{
				var id = $(this).attr('id').replace('map', '');
				google.maps.event.addDomListener(window, 'load', jsFrontend.location.initMap(id));
			});
		}
	},

	// init the map
	initMap: function(id)
	{
		// define some variables we will need
		var suffix = (id == '') ? '' : '_' + id;
		var mapId = (id == '') ? 'general' : id;
		var mapStyle = (jsFrontend.data.get('Location.settings' + suffix + '.map_style'));


        // MAPS STYLE CONFIG
        MAPS_CONFIG = {
            standard: [],
            custom: [],
            // GRAY STYLE
            gray: [
                // BASIC
                {
                    "stylers": [
                        {hue: "#B9B9B9"},
                        {saturation: -100}
                    ]
                },
                // Lanscape
                {
                    "featureType": "landscape",
                    "stylers": [
                        {
                            "color": "#E5E5E5"
                        }
                    ]
                },
                // Water
                {
                    "featureType": "water",
                    "stylers": [
                        {
                            "visibility": "on"
                        },
                        {
                            "color": "#DCDCDC"
                        }
                    ]
                },
                // Transit
                {
                    "featureType": "transit",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#B9B9B9"
                        }
                    ]
                },
                // Road hight way
                {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#E2E2E2"
                        }
                    ]
                },
                // Road hight way control access
                {
                    "featureType": "road.highway.controlled_access",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#ACACAC"
                        }
                    ]
                },
                // Road arterial
                {
                    "featureType": "road.arterial",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#ffffff"
                        }
                    ]
                },
                // Road local
                {
                    "featureType": "road.local",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#F6F6F6"
                        }
                    ]
                },
                // Point global
                {
                    "featureType": "poi",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#DDDDDD"
                        }
                    ]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#D3D3D3"
                        }
                    ]
                },
                {
                    "featureType": "poi.attraction",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#DBDBDB"
                        }
                    ]
                },
                {
                    "featureType": "poi.business",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#DBDBDB"
                        }
                    ]
                },
                {
                    "featureType": "poi.place_of_worship",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#DBDBDB"
                        }
                    ]
                },
            ],
            // Blue STYLE
            blue: [
                // BASIC
                {
                    "stylers": [
                        {hue: "#4C95B2"},
                        {saturation: -100}
                    ]
                },
                // Lanscape
                {
                    "featureType": "landscape",
                    "stylers": [
                        {
                            "color": "#DEE7EB"
                        }
                    ]
                },
                // Water
                {
                    "featureType": "water",
                    "stylers": [
                        {
                            "visibility": "on"
                        },
                        {
                            "color": "#BBE7F9"
                        }
                    ]
                },
                // Transit
                {
                    "featureType": "transit",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#86CEEB"
                        }
                    ]
                },
                // Road hight way
                {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#C2EDFF"
                        }
                    ]
                },
                // Road hight way control access
                {
                    "featureType": "road.highway.controlled_access",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#4DCDFF"
                        }
                    ]
                },
                // Road arterial
                {
                    "featureType": "road.arterial",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#ffffff"
                        }
                    ]
                },
                // Road local
                {
                    "featureType": "road.local",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#F3F7F9"
                        }
                    ]
                },
                // Point global
                {
                    "featureType": "poi",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#C6E5F1"
                        }
                    ]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#B7DDEC"
                        }
                    ]
                },
                {
                    "featureType": "poi.attraction",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#D5DDE0"
                        }
                    ]
                },
                {
                    "featureType": "poi.business",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#D5DDE0"
                        }
                    ]
                },
                {
                    "featureType": "poi.place_of_worship",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#D5DDE0"
                        }
                    ]
                },
            ]
        };

		// build the options
		var options =
		{
			zoom: (jsFrontend.data.get('Location.settings' + suffix + '.zoom_level') == 'auto') ? 0 : parseInt(jsFrontend.data.get('Location.settings' + suffix + '.zoom_level')),
			center: new google.maps.LatLng(jsFrontend.data.get('Location.settings' + suffix + '.center.lat'), jsFrontend.data.get('Location.settings' + suffix + '.center.lng')),
			mapTypeId: google.maps.MapTypeId[jsFrontend.data.get('Location.settings' + suffix + '.map_type')],
			styles: MAPS_CONFIG[mapStyle]
		};

		// create map
		jsFrontend.location.map[mapId] = new google.maps.Map(document.getElementById('map' + id), options);

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
			$('#locationSearch' + id + ' form').on('submit', function(e)
			{
				// prevent default
				e.preventDefault();

				// calculate & display the route
				jsFrontend.location.setRoute(id, mapId, items[0]);
			});
		}

		if($('#map-full-url-' + id).length > 0) {
			jsFrontend.location.mapFullUrl = $('#map-full-url-' + id).attr('href');
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
			$markerText = $('#markerText' + marker.locationId);

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
		$error = $('#locationSearchError' + id);
		$search = $('#locationSearchAddress' + id);

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
					var $item = $('#map-full-url-' + id);

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
