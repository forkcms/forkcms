/**
 * Interaction for the location module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
jsFrontend.location =
{
	map: {},
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

		// build the options
		var options =
		{
			zoom: (jsFrontend.data.get('location.settings' + suffix + '.zoom_level') == 'auto') ? 0 : parseInt(jsFrontend.data.get('location.settings' + suffix + '.zoom_level')),
			center: new google.maps.LatLng(jsFrontend.data.get('location.settings' + suffix + '.center.lat'), jsFrontend.data.get('location.settings' + suffix + '.center.lng')),
			mapTypeId: google.maps.MapTypeId[jsFrontend.data.get('location.settings' + suffix + '.map_type')]
		};
		
		// create map
		jsFrontend.location.map[mapId] = new google.maps.Map(document.getElementById('map' + id), options);
		
		// get the items
		var items = jsFrontend.data.get('location.items' + suffix);

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
		if(jsFrontend.data.get('location.settings' + suffix + '.directions'))
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
				
		// show infowindow on click
		google.maps.event.addListener(marker, 'click', function()
		{
			$markerText = $('#markerText' + marker.locationId);
			
			// apparently JS goes bananas with multiline HTMl, so we grab it from the div, this seems like a good idea for SEO
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

		// request the rout
		jsFrontend.location.directionsService.route(request, function(response, status)
		{
			// did we find a route
			if(status == google.maps.DirectionsStatus.OK)
			{
				// change the map
				jsFrontend.location.directionsDisplay.setMap(jsFrontend.location.map[mapId]);
				
				// render the route
				jsFrontend.location.directionsDisplay.setDirections(response);
			}
			
			// show error
			else $error.show();
		});
	}
}

$(jsFrontend.location.init);