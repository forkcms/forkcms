{*
	variables that are available:
	- {$widgetLocationItems}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItems}
{option:widgetLocationSettings.directions}
<input type="text" id="locationSearchAddress" name="locationSearchAddress" /> <input type="button" name="locationSearchRequest" onclick="setRoute()" id="locationSearchRequest" value="{$lblShowDirections|ucfirst}" />
{/option:widgetLocationSettings.directions}
<div id="mapWidget" style="height: {$widgetLocationSettings.height}px; width: {$widgetLocationSettings.width}px;"></div>
{option:widgetLocationSettings.full_url}
	<p><a href="{$widgetLocationSettings.maps_url}" title="{$lblViewLargeMap}">{$lblViewLargeMap|ucfirst}</a></p>
{/option:widgetLocationSettings.full_url}

<div id="widgetLocationItemText" style="display: none;">
	<p>{$widgetLocationItems.street} {$widgetLocationItems.number}</p>
	<p>{$widgetLocationItems.zip} {$widgetLocationItems.city}</p>
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var position = new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng});
	var latlngBounds = new google.maps.LatLngBounds(position);

	{* Optional direction calculation *}
	{option:widgetLocationSettings.directions}
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	{/option:widgetLocationSettings.directions}

	function initialize()
	{
		var options =
		{
			zoom: '{$widgetLocationSettings.zoom_level}' == 'auto' ? 0 : {$widgetLocationSettings.zoom_level},
			center: new google.maps.LatLng({$widgetLocationItems.lat}, {$widgetLocationItems.lng}),
			mapTypeId: google.maps.MapTypeId.{$widgetLocationSettings.map_type}
		};

		// create map
		var mapWidget = new google.maps.Map(document.getElementById('mapWidget'), options);

		{* Optional direction calculation *}
		{option:widgetLocationSettings.directions}
			directionsDisplay = new google.maps.DirectionsRenderer();
			directionsDisplay.setMap(mapWidget);
		{/option:widgetLocationSettings.directions}

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
	}

	{* Optional direction calculation *}
	{option:widgetLocationSettings.directions}
	function setRoute()
	{
		var request = {
			origin: $('#locationSearchAddress').val(),
			destination: position,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};

		directionsService.route(request, function(response, status)
		{
			if(status == google.maps.DirectionsStatus.OK)
			{
				directionsDisplay.setDirections(response);
			}
		});
	}
	{/option:widgetLocationSettings.directions}

	google.maps.event.addDomListener(window, 'load', initialize)
</script>
{/option:widgetLocationItems}