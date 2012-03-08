{*
	variables that are available:
	- {$widgetLocationItem}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItem}

{option:widgetLocationSettings.directions}
	<aside class="locationSearch">
		<form method="get" action="">
			<input type="text" id="locationSearchAddress" class="inputText" name="locationSearchAddress" />
			<input type="button" name="locationSearchRequest" onclick="setRoute()" class="inputSubmit" id="locationSearchRequest" value="{$lblShowDirections|ucfirst}" />
		</form>
	</aside>
{/option:widgetLocationSettings.directions}

<div id="mapWidget" style="height: {$widgetLocationSettings.height}px; width: {$widgetLocationSettings.width}px;"></div>

{option:widgetLocationSettings.full_url}
	<p><a href="{$widgetLocationSettings.maps_url}" title="{$lblViewLargeMap}">{$lblViewLargeMap|ucfirst}</a></p>
{/option:widgetLocationSettings.full_url}

<div id="widgetLocationItemText" style="display: none;">
	<p>{$widgetLocationItem.street} {$widgetLocationItem.number}</p>
	<p>{$widgetLocationItem.zip} {$widgetLocationItem.city}</p>
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var position = new google.maps.LatLng({$widgetLocationItem.lat}, {$widgetLocationItem.lng});
	var latlngBounds = new google.maps.LatLngBounds(position);

	{* Optional direction calculation *}
	{option:widgetLocationSettings.directions}
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	{/option:widgetLocationSettings.directions}

	function initialize()
	{
		// set options
		var options =
		{
			center: new google.maps.LatLng({$widgetLocationSettings.center.lat}, {$widgetLocationSettings.center.lng}),
			mapTypeId: google.maps.MapTypeId.{$widgetLocationSettings.map_type}
		};

		// create map
		var mapWidget = new google.maps.Map(document.getElementById('mapWidget'), options);

		var markers = [];
		{option:widgetLocationItem.lat}
			{option:widgetLocationItem.lng}
				markers.push(
				{
					lat: {$widgetLocationItem.lat},
					lng: {$widgetLocationItem.lng},
					title: '{$widgetLocationItem.title}',
					text: '<p>{$widgetLocationItem.street} {$widgetLocationItem.number}</p><p>{$widgetLocationItem.zip} {$widgetLocationItem.city}</p>',
					dragable: true
				});
			{/option:widgetLocationItem.lng}
		{/option:widgetLocationItem.lat}

		// loop the markers
		for(var i in markers)
		{
			var marker = new google.maps.Marker(
			{
				position: position,
				map: mapWidget,
				title: '<h1>{$widgetLocationItem.title}</h1>'
			});
		}

		// set zoom automatically, defined by points (if allowed)
		if('{$widgetLocationSettings.zoom_level}' == 'auto') mapWidget.fitBounds(latlngBounds);
		else mapWidget.setZoom(parseInt('{$widgetLocationSettings.zoom_level}'));

		{* Optional direction calculation *}
		{option:widgetLocationSettings.directions}
			directionsDisplay = new google.maps.DirectionsRenderer();
			directionsDisplay.setMap(mapWidget);

			// show the search form
			$('.locationSearch').show();

			// calculate the route on enter
			$('#locationSearchAddress').keypress(function(e)
			{
				if(e.which == 13)
				{
					e.preventDefault();
					setRoute();
				}
			});

		{/option:widgetLocationSettings.directions}

		// add marker
		var marker = new google.maps.Marker(
		{
			position: position,
			map: mapWidget,
			title: '{$widgetLocationItem.title}'
		});

		// add click event on marker
		google.maps.event.addListener(marker, 'click', function()
		{
			// create infowindow
			new google.maps.InfoWindow({ content: '<h1>{$widgetLocationItem.title}</h1>' + $('#widgetLocationItemText').html() }).open(mapWidget, marker);
		});
	}

	{* Optional direction calculation *}
	{option:widgetLocationSettings.directions}
	function setRoute()
	{
		var request =
		{
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
{/option:widgetLocationItem}