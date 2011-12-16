/**
 * Interaction for the location module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.location =
{
	bounds: null,
	map: null,

	// init, something like a constructor
	init: function()
	{
		if(typeof markers != 'undefined' && typeof mapOptions != 'undefined') jsBackend.location.showMap();
	},

	addMarker: function(map, bounds, object)
	{
		// create position
		position = new google.maps.LatLng(object.lat, object.lng);

		// add to boundaries
		bounds.extend(position);

		// add marker
		var marker = new google.maps.Marker(
		{
			// set position
			position: position,
			// add to map
			map: map,
			// set title
			title: object.title
		});

		// add click event on marker
		google.maps.event.addListener(marker, 'click', function()
		{
			// create infowindow
			new google.maps.InfoWindow({ content: '<h1>'+ object.title +'</h1>' + object.text }).open(map, marker);
		});
	},

	// init, something like a constructor
	showMap: function()
	{
		// create boundaries
		jsBackend.location.bounds = new google.maps.LatLngBounds();

		// set options
		var options =
		{
			// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
			zoom: (mapOptions.zoom == 'auto') ? 0 : mapOptions.zoom,

			// set default center as first item's location
			center: new google.maps.LatLng(mapOptions.center.lat, mapOptions.center.lng),

			// no interface, just the map
			disableDefaultUI: true,

			// no dragging the map around
			draggable: false,

			// no zooming in/out using scrollwheel
			scrollwheel: false,

			// no double click zoom
			disableDoubleClickZoom: true,

			// set map type
			mapTypeId: eval('google.maps.MapTypeId.' + mapOptions.type)
		};

		// create map
		jsBackend.location.map = new google.maps.Map(document.getElementById('map'), options);

		// loop the markers
		for(var i in markers) jsBackend.location.addMarker(jsBackend.location.map, jsBackend.location.bounds, markers[i])

		// set center to the middle of our boundaries
		jsBackend.location.map.setCenter(jsBackend.location.bounds.getCenter());

		// set zoom automatically, defined by points (if allowed)
		if(mapOptions.zoom == 'auto') jsBackend.location.map.fitBounds(jsBackend.location.bounds);
	}
}

$(jsBackend.location.init);