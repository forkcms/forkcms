{*
	variables that are available:
	- {$locationItems}: contains data about all locations
	- {$locationSettings}: contains this module's settings
*}

{option:locationItems}
	<div id="map" style="height: {$locationSettings.height}px; width: {$locationSettings.width}px;"></div>

	{* Store item text in a div because JS goes bananas with multiline HTML *}
	{iteration:locationItems}
		<div id="markerText{$locationItems.id}" style="display:none;">{$locationItems.text}</div>
	{/iteration:locationItems}

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		function initialize() {

			// create boundaries
			var latlngBounds = new google.maps.LatLngBounds();

			// set options
			var options =
			{
				// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
				zoom: '{$locationSettings.zoom_level}' == 'auto' ? 0 : {$locationSettings.zoom_level},
				// set default center as first item's location
				center: new google.maps.LatLng({$locationItems.0.lat}, {$locationItems.0.lng}),
				// no interface, just the map
				disableDefaultUI: true,
				// no dragging the map around
				draggable: false,
				// no zooming in/out using scrollwheel
				scrollwheel: false,
				// no double click zoom
				disableDoubleClickZoom: true,
				// set map type
				mapTypeId: google.maps.MapTypeId.{$locationSettings.map_type}
			};

			// create map
			var map = new google.maps.Map(document.getElementById('map'), options);

			// function to add markers to the map
			function addMarker(lat, lng, title, text)
			{
				// create position
				position = new google.maps.LatLng(lat, lng);

				// add to boundaries
				latlngBounds.extend(position);

				// add marker
				var marker = new google.maps.Marker(
				{
					// set position
					position: position,
					// add to map
					map: map,
					// set title
					title: title
				});

				// add click event on marker
				google.maps.event.addListener(marker, 'click', function()
				{
					// create infowindow
					new google.maps.InfoWindow({ content: '<h1>'+ title +'</h1>' + text }).open(map, marker);
				});
			}

			// loop items and add to map
			{iteration:locationItems}
				{option:locationItems.lat}{option:locationItems.lng}addMarker({$locationItems.lat}, {$locationItems.lng}, '{$locationItems.title}', $('#markerText' + {$locationItems.id}).html());{/option:locationItems.lat}{/option:locationItems.lng}
			{/iteration:locationItems}

			// set center to the middle of our boundaries
			map.setCenter(latlngBounds.getCenter());

			// set zoom automatically, defined by points (if allowed)
			if('{$locationSettings.zoom_level}' == 'auto') map.fitBounds(latlngBounds);
		}

		google.maps.event.addDomListener(window, 'load', initialize)
	</script>
{/option:locationItems}