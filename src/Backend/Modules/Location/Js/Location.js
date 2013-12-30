/**
 * Interaction for the location module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
jsBackend.location =
{
	// base values
	bounds: null, center: null, centerLat: null, centerLng: null, height: null,  
	map: null, mapId: null, showDirections: false, showLink: false, showOverview: true,
	type: null, width: null, zoomLevel: null,

	init: function()
	{
		// only show a map when there are options and markers given
		if(typeof markers != 'undefined' && typeof mapOptions != 'undefined') 
		{
			jsBackend.location.showMap();

			// add listeners for the zoom level and terrain
			google.maps.event.addListener(jsBackend.location.map, 'maptypeid_changed', jsBackend.location.setDropdownTerrain);
			google.maps.event.addListener(jsBackend.location.map, 'zoom_changed', jsBackend.location.setDropdownZoom);

			// if the zoom level or map type changes in the dropdown, the map needs to change
			$('#zoomLevel').bind('change', function() { jsBackend.location.setMapZoom($('#zoomLevel').val()); });
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
		
		if(typeof object.dragable != 'undefined' && object.dragable)
		{
			marker.setDraggable(true);
			
			// add event listener
			google.maps.event.addListener(marker, 'dragend', function()
			{
				jsBackend.location.updateMarker(marker);
			});
		}
		
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

		// get the form data
		jsBackend.location.mapId = parseInt($('#mapId').val());
		jsBackend.location.height = parseInt($('#height').val());
		jsBackend.location.width = parseInt($('#width').val());

		jsBackend.location.showDirections = ($('#directions').attr('checked') == 'checked');
		jsBackend.location.showLink = ($('#fullUrl').attr('checked') == 'checked');
		jsBackend.location.showOverview = ($('#markerOverview').attr('checked') == 'checked');
	},
	
	// this will refresh the page and display a certain message
	refreshPage: function(message)
	{
		var currLocation = window.location;
		var reloadLocation = (currLocation.search.indexOf('?') >= 0) ? '&' : '?';
		reloadLocation = currLocation + reloadLocation + 'report=' + message;

		// cleanly redirect so we can display a message
		window.location = reloadLocation;
	},
	
	saveLiveData: function()
	{
		$.ajax(
		{
			data:
			{
				fork: { module: 'Location', action: 'SaveLiveLocation' },
				zoom: jsBackend.location.zoomLevel,
				type: jsBackend.location.type,
				centerLat: jsBackend.location.centerLat,
				centerLng: jsBackend.location.centerLng,
				height: jsBackend.location.height,
				width: jsBackend.location.width,
				id: jsBackend.location.mapId,
				link: jsBackend.location.showLink,
				directions: jsBackend.location.showDirections,
				showOverview: jsBackend.location.showOverview
			},
			success: function(json, textStatus)
			{
				// reload the page on success
				if(json.code == 200)
				{
					// no redirect given, refresh the page
					if(typeof $('input#redirect').val() == 'undefined')
					{
						jsBackend.location.refreshPage('map-saved');
					}
					
					$('input#redirect').val('edit');
					$('form#edit').submit();
				}
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
	setMapZoom: function(zoomlevel)
	{
		jsBackend.location.zoomLevel = zoomlevel;

		// set zoom automatically, defined by points (if allowed)
		if(zoomlevel == 'auto') jsBackend.location.map.fitBounds(jsBackend.location.bounds);
		else jsBackend.location.map.setZoom(parseInt(zoomlevel));
	},

	showMap: function()
	{
		// create boundaries
		jsBackend.location.bounds = new google.maps.LatLngBounds();

		// set options
		var options =
		{
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

		jsBackend.location.setMapZoom(mapOptions.zoom);
	},
	
	// this will re-set the position of a marker
	updateMarker: function(marker)
	{
		jsBackend.location.getMapData();
		
		var lat = marker.getPosition().lat();
		var lng = marker.getPosition().lng();
		
		$.ajax(
		{
			data:
			{
				fork: { module: 'Location', action: 'UpdateMarker' },
				id: jsBackend.location.mapId,
				lat: lat,
				lng: lng
			},
			success: function(json, textStatus)
			{
				// reload the page on success
				if(json.code == 200) jsBackend.location.saveLiveData();
			}
		});
	}
}

$(jsBackend.location.init);
