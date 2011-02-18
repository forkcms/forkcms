{*
	variables that are available:
	- {$widgetLocationItemsItems}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItems}
<div id="mapWidget" style="height: {$widgetLocationSettings.height_widget}px; width: {$widgetLocationSettings.width_widget}px;"></div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	//create position
	var position = new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng});

	// create boundaries and add position
	var latlngBounds = new google.maps.LatLngBounds(position);

	// set options
	var options =
	{
		// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
		zoom: '{$widgetLocationSettings.zoom_level_widget}' == 'auto' ? 0 : {$widgetLocationSettings.zoom_level_widget},
		// set default center as first item's location
		center: new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng}),
		// no interface, just the map
		disableDefaultUI: true,
		// no dragging the map around
		draggable: false,
		// no zooming in/out using scrollwheel
		scrollwheel: false,
		// no double click zoom
		disableDoubleClickZoom: true,
		// set map type
		mapTypeId: google.maps.MapTypeId.{$widgetLocationSettings.map_type_widget}
	};

	// create map
	var mapWidget = new google.maps.Map(document.getElementById('mapWidget'), options);

	// set zoom automatically, defined by points (if allowed)
	if('{$widgetLocationSettings.zoom_level_widget}' == 'auto') mapWidget.fitBounds(latlngBounds);

	// add marker
	var marker = new google.maps.Marker(
	{
		position: position,
		map: mapWidget,
		title: '{$widgetLocationItems.title}'
	});

	// add click event on marker
	google.maps.event.addListener(marker, 'click', function()
	{
		// create infowindow
		new google.maps.InfoWindow({ content: '<h1>{$widgetLocationItems.title}</h1>{$widgetLocationItems.text}' }).open(map, marker);
	});
</script>
{/option:widgetLocationItems}