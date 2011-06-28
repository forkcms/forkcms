{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLocation|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{option:dataGrid}
	<div class="box">
		<div class="heading">
			<h3>{$lblMap|ucfirst}</h3>
		</div>
		<div class="options">
			{option:items}
				<div id="map" style="height: {$settings.height}px; width: 100%;"></div>

				<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
				<script type="text/javascript">
					// create boundaries
					var latlngBounds = new google.maps.LatLngBounds();

					// set options
					var options =
					{
						// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
						zoom: '{$settings.zoom_level}' == 'auto' ? 0 : {$settings.zoom_level},
						// set default center as first item's location
						center: new google.maps.LatLng({$items.0.lat}, {$items.0.lng}),
						// no interface, just the map
						disableDefaultUI: true,
						// no dragging the map around
						draggable: false,
						// no zooming in/out using scrollwheel
						scrollwheel: false,
						// no double click zoom
						disableDoubleClickZoom: true,
						// set map type
						mapTypeId: google.maps.MapTypeId.{$settings.map_type}
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
					{iteration:items}
						{option:items.lat}{option:items.lng}addMarker({$items.lat}, {$items.lng}, '{$items.title}', '{$items.text}');{/option:items.lat}{/option:items.lng}
					{/iteration:items}

					// set center to the middle of our boundaries
					map.setCenter(latlngBounds.getCenter());

					// set zoom automatically, defined by points (if allowed)
					if('{$settings.zoom_level}' == 'auto') map.fitBounds(latlngBounds);
				</script>
			{/option:items}
		</div>
	</div>

	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}