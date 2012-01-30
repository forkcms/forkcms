{*
	variables that are available:
	- {$widgetLocationItemsItems}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItems}
<div id="mapWidget" style="height: {$widgetLocationSettings.height}px; width: {$widgetLocationSettings.width}px;"></div>

<div id="widgetLocationItemText" style="display: none;">
	<p>{$widgetLocationItems.street} {$widgetLocationItems.number}</p>
	<p>{$widgetLocationItems.zip} {$widgetLocationItems.city}</p>
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var position = new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng});
	var latlngBounds = new google.maps.LatLngBounds(position);

	var options =
	{
		zoom: '{$widgetLocationSettings.zoom_level}' == 'auto' ? 0 : {$widgetLocationSettings.zoom_level},
		center: new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng}),
		mapTypeId: google.maps.MapTypeId.{$widgetLocationSettings.map_type}
	};

	// create map
	var mapWidget = new google.maps.Map(document.getElementById('mapWidget'), options);

	// set zoom automatically, defined by points (if allowed)
	if('{$widgetLocationSettings.zoom_level}' == 'auto') mapWidget.fitBounds(latlngBounds);

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
		new google.maps.InfoWindow({ content: '<h1>{$widgetLocationItems.title}</h1>' + $('#widgetLocationItemText').html() }).open(mapWidget, marker);
	});
</script>
{/option:widgetLocationItems}