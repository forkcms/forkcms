/**
 * Interaction for the location module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
jsFrontend.location =
{
	map: null,
		
	// init, something like a constructor
	init: function()
	{
		if($('#map').length > 0) 
		{
			google.maps.event.addDomListener(window, 'load', jsFrontend.location.initMap);
		}
	}, 
	
	initMap: function()
	{
		// build the options
		var options = 
		{
			zoom: (jsFrontend.data.get('location.settings.zoom_level') == 'auto') ? 0 : jsFrontend.data.get('location.settings.zoom_level'),
			center: new google.maps.LatLng(jsFrontend.data.get('location.settings.center.lat'), jsFrontend.data.get('location.settings.center.lng')),
			mapTypeId: google.maps.MapTypeId[jsFrontend.data.get('location.settings.map_type')]
		};

		// create map
		jsFrontend.location.map = new google.maps.Map(document.getElementById('map'), options);
		
		var items = jsFrontend.data.get('location.items');

		if(items.length > 0)
		{
			for(var i in items)
			{
				jsFrontend.location.addMarker(items[i].lat, items[i].lng, items[i].title, items[i].text);
			}
		}
	},
	
	addMarker: function(lat, lng, title, text)
	{
		// add the marker
		var marker = new google.maps.Marker(
			{
				position: new google.maps.LatLng(lat, lng),
				map: jsFrontend.location.map,
				title: title
			}
		);
		
		// show infowindow on click
		google.maps.event.addListener(marker, 'click', function() 
		{
			new google.maps.InfoWindow(
				{
					content: '<h1>' + title + '</h1>' + text
				}
			).open(jsFrontend.location.map, marker);
		});
	}
}

$(jsFrontend.location.init);