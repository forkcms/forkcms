/**
 * Interaction for the location module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
jsBackend.location =
{
	bounds: null,
	map: null,

	// init, something like a constructor
	init: function()
	{
		if(typeof markers != 'undefined' && typeof mapOptions != 'undefined') jsBackend.location.showMap();
		
		// the panning save option
		$('#saveLiveData').bind('click', jsBackend.location.saveLiveData);
	},

	addMarker: function(map, bounds, object)
	{
		position = new google.maps.LatLng(object.lat, object.lng);
		bounds.extend(position);

		// add marker
		var marker = new google.maps.Marker(
		{
			position: position,
			map: map,
			title: object.title
		});

		// add click event on marker
		google.maps.event.addListener(marker, 'click', function()
		{
			// create infowindow
			new google.maps.InfoWindow({ content: '<h1>'+ object.title +'</h1>' + object.text }).open(map, marker);
		});
	},
	
	// save the live data
	saveLiveData: function(e)
	{
		e.preventDefault();

		var mapZoom = jsBackend.location.map.getZoom();
		var mapType = jsBackend.location.map.getMapTypeId();
		var mapCenter = jsBackend.location.map.getCenter();
		var centerLat = mapCenter.lat();
		var centerLng = mapCenter.lng();
		
		$.ajax(
		{
			data:
			{
				fork: { module: 'location', action: 'save_live_location' },
				zoom: mapZoom,
				type: mapType,
				centerLat: centerLat,
				centerLng: centerLng
			},
			success: function(json, textStatus)
			{
				
			}
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
			zoom: mapOptions.zoom,
			center: new google.maps.LatLng(mapOptions.center.lat, mapOptions.center.lng),
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