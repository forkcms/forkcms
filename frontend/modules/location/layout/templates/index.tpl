{*
	variables that are available:
	- {$locationItems}: contains data about all locations
	- {$locationSettings}: contains this module's settings
*}

{option:locationItems}
	<div id="map" style="height: {$locationSettings.height}px; width: {$locationSettings.width}px;"></div>

	{* Store item text in a div because JS goes bananas with multiline HTML *}
	{iteration:locationItems}
		<div id="markerText{$locationItems.id}" style="display:none;">
			<p>{$locationItems.street} {$locationItems.number}</p>
			<p>{$locationItems.zip} {$locationItems.city}</p>
		</div>
	{/iteration:locationItems}

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
	var map;

	function initialize()
	{
		var myOptions =
		{
			zoom: '{$locationSettings.zoom_level}' == 'auto' ? 0 : {$locationSettings.zoom_level},
			center: new google.maps.LatLng({$locationSettings.center.lat}, {$locationSettings.center.lng}),
			mapTypeId: google.maps.MapTypeId.{$locationSettings.map_type}
		};

		map = new google.maps.Map(document.getElementById('map'), myOptions);

		{iteration:locationItems}
			{option:locationItems.lat}
			{option:locationItems.lng}
			addMarker({$locationItems.lat}, {$locationItems.lng}, '{$locationItems.title}', $('#markerText{$locationItems.id}').html());
			{/option:locationItems.lat}
			{/option:locationItems.lng}
		{/iteration:locationItems}

    }

	// function to add markers to the map
	function addMarker(lat, lng, title, text)
	{
		// create position
		position = new google.maps.LatLng(lat, lng);

		var marker = new google.maps.Marker(
		{
			position: position,
			map: map,
			title: title
		});

		google.maps.event.addListener(marker, 'click', function()
		{
			new google.maps.InfoWindow({ content: '<h1>'+ title +'</h1>' + text }).open(map, marker);
		});
	}

    google.maps.event.addDomListener(window, 'load', initialize)
	</script>
{/option:locationItems}