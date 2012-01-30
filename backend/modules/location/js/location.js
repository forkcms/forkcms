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
	zoomLevel: null,
	type: null,
	center: null,
	centerLat: null,
	centerLng: null,
	height: null,
	width: null,
	mapId: null,

	init: function()
	{
		if(typeof markers != 'undefined' && typeof mapOptions != 'undefined') 
		{
			jsBackend.location.showMap();

			// add listeners for the zoom level and terrain
			google.maps.event.addListener(jsBackend.location.map, 'maptypeid_changed', jsBackend.location.setDropdownTerrain);
			google.maps.event.addListener(jsBackend.location.map, 'zoom_changed', jsBackend.location.setDropdownZoom);
			
			// if the zoom level or map type changes in the dropdown, the map needs to change
			$('#zoomLevel').bind('change', jsBackend.location.setMapZoom);
			$('#mapType').bind('change', jsBackend.location.setMapTerrain);
			
			// the panning save option
			$('#saveLiveData').bind('click', function(e)
			{
				e.preventDefault();
				
				// save the live map data
				jsBackend.location.getMapData();
				jsBackend.location.saveLiveData();
			});
		}
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
			new google.maps.InfoWindow(
			{ 
				content: '<h1>'+ object.title +'</h1>' + object.text 
			}).open(map, marker);
		});
	},
	
	getMapData: function()
	{
		// get the live data
		jsBackend.location.zoomLevel = jsBackend.location.map.getZoom();
		jsBackend.location.type = jsBackend.location.map.getMapTypeId();
		jsBackend.location.center = jsBackend.location.map.getCenter();
		jsBackend.location.centerLat = jsBackend.location.center.lat();
		jsBackend.location.centerLng = jsBackend.location.center.lng();
		jsBackend.location.mapId = parseInt($('#mapId').val());
		
		// @todo int validation
		jsBackend.location.height = parseInt($('#height').val());
		jsBackend.location.width = parseInt($('#width').val());
	},
	
	saveLiveData: function()
	{
		$.ajax(
		{
			data:
			{
				fork: { module: 'location', action: 'save_live_location' },
				zoom: jsBackend.location.zoomLevel,
				type: jsBackend.location.type,
				centerLat: jsBackend.location.centerLat,
				centerLng: jsBackend.location.centerLng,
				height: jsBackend.location.height,
				width: jsBackend.location.width,
				id: jsBackend.location.mapId
			},
			success: function(json, textStatus)
			{
				// reload the page on success
				// @todo clean message
				if(json.code == 200) location.reload(true);
			}
		});
	},
	
	// this will set the terrain type of the map to the dropdown
	setDropdownTerrain: function()
	{
		jsBackend.location.getMapData();
		$('#mapType').val(jsBackend.location.type.toUpperCase());
	},
	
	// this will set the zoom level of the map to the dropdown
	setDropdownZoom: function()
	{
		jsBackend.location.getMapData();
		$('#zoomLevel').val(jsBackend.location.zoomLevel);
	},
	
	// this will set the terrain type of the map to the dropdown
	setMapTerrain: function()
	{
		jsBackend.location.type = $('#mapType').val();
		jsBackend.location.map.setMapTypeId(jsBackend.location.type.toLowerCase());
	},
	
	// this will set the zoom level of the map to the dropdown
	setMapZoom: function()
	{
		jsBackend.location.zoomLevel = $('#zoomLevel').val();
		jsBackend.location.map.setZoom(parseInt(jsBackend.location.zoomLevel));
	},

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
		for(var i in markers)
		{
			jsBackend.location.addMarker(
				jsBackend.location.map, jsBackend.location.bounds, markers[i]
			);
		}

		// set zoom automatically, defined by points (if allowed)
		if(mapOptions.zoom == 'auto') 
		{
			jsBackend.location.map.fitBounds(jsBackend.location.bounds);
		}
	}
}

$(jsBackend.location.init);