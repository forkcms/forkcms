{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="pageTitle">
		<h2>{$lblLocation|ucfirst}: {$lblAdd}</h2>
	</div>

	<p>
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
	</p>

	<div class="box">
		<div class="heading">
			<h3>{$lblMap|ucfirst}</h3>
		</div>
		<div class="options">
			{option:item.lat}
			{option:item.lng}
				<div id="map" style="height: {$settings.height_widget}px; width: 100%;"></div>
			{/option:item.lat}
			{/option:item.lng}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblContent|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				{$txtText} {$txtTextError}
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblAddress|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="street">{$lblStreet|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtStreet} {$txtStreetError}
			</p>
			<p>
				<label for="number">{$lblNumber|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtNumber} {$txtNumberError}
			</p>
			<p>
				<label for="zip">{$lblZip|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtZip} {$txtZipError}
			</p>
			<p>
				<label for="city">{$lblCity|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtCity} {$txtCityError}
			</p>
			<p>
				<label for="country">{$lblCountry|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$ddmCountry} {$ddmCountryError}
			</p>
		</div>
	</div>

	{option:item.lat}
	{option:item.lng}
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			// create position
			var position = new google.maps.LatLng({$item.lat}, {$item.lng});

			// create boundaries and add position
			var latlngBounds = new google.maps.LatLngBounds(position);

			// set options
			var options =
			{
				// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
				zoom: 15,
				// set default center as first item's location
				center: new google.maps.LatLng({$item.lat}, {$item.lng}),
				// no interface, just the map
				disableDefaultUI: true,
				// no dragging the map around
				draggable: false,
				// no zooming in/out using scrollwheel
				scrollwheel: false,
				// no double click zoom
				disableDoubleClickZoom: true,
				// set map type
				mapTypeId: google.maps.MapTypeId.{$settings.map_type_widget}
			};

			// create map
			var map = new google.maps.Map(document.getElementById('map'), options);

			// add marker
			var marker = new google.maps.Marker(
			{
				position: position,
				map: map,
				title: '{$item.title}'
			});

			// add click event on marker
			google.maps.event.addListener(marker, 'click', function()
			{
				// create infowindow
				new google.maps.InfoWindow({ content: '<h1>{$item.title}</h1>{$item.text}' }).open(map, marker);
			});
		</script>
	{/option:item.lng}
	{/option:item.lat}

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.title}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}