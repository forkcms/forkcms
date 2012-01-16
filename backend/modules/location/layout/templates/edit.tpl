{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLocation|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
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
					<div id="map" style="height: {$settings.height_widget}px; width: 100%;">
					</div>
				{/option:item.lat}
			{/option:item.lng}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>
				<label for="text">{$lblContent|ucfirst}</label>
			</h3>
		</div>
		<div class="optionsRTE">
			{$txtText} {$txtTextError}
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

	<div class="fullwidthOptions">
		{option:showLocationDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.title}}
			</p>
		</div>
		{/option:showLocationDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

<script type="text/javascript">
	var mapOptions = {
		zoom: 15,
		type: '{$settings.map_type}',
		center: {
			lat: {$item.lat},
			lng: {$item.lng}
		}
	};
	var markers = [];
	{option:item.lat}
		{option:item.lng}
			markers.push({
				lat: {$item.lat},
				lng: {$item.lng},
				title: '{$item.title}',
				text: '{$item.text|stripnewlines}'
			});
		{/option:item.lng}
	{/option:item.lat}
</script>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}