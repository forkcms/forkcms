{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLocation|ucfirst}</h2>

	{option:showLocationAdd}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showLocationAdd}
</div>

{option:dataGrid}
	<table width="100%">
		<tr>
			<td id="leftColumn">
				<div class="box">
					<div class="heading">
						<h3>{$lblMap|ucfirst}</h3>
					</div>

					{* Map *}
					<div class="options">
						{option:items}
							<div id="map" style="height: {$settings.height}px; width: {$settings.width}px;">
							</div>
						{/option:items}
					</div>
				</div>
			</td>

			{form:settings}
			<td id="rightColumn" style="width: 300px; padding-left: 10px;">
				<div class="box">
					<div class="heading">
						<h3>{$lblSettings|ucfirst}</h3>
					</div>

					{* Zoom level *}
					<div class="options">
						<p>
							<label for="zoomLevel">{$lblZoomLevel|ucfirst}</label>
							{$ddmZoomLevel} {$ddmZoomLevelError}
						</p>
					</div>

					{* Map width *}
					<div class="options"{option:!godUser} style="display:none;"{/option:!godUser}>
						<p>
							<label for="width">{$lblWidth|ucfirst}</label>
							{$txtWidth} {$txtWidthError}
							<span class="helpTxt">
								{$msgWidthHelp|sprintf:300:800}
							</span>
						</p>
					</div>

					{* Map height *}
					<div class="options"{option:!godUser} style="display:none;"{/option:!godUser}>
						<p>
							<label for="height">{$lblHeight|ucfirst}</label>
							{$txtHeight} {$txtHeightError}
							<span class="helpTxt">
								{$msgHeightHelp|sprintf:150}
							</span>
						</p>
					</div>

					{* Map type *}
					<div class="options">
						<p>
							<label for="mapType">{$lblMapType|ucfirst}</label>
							{$ddmMapType} {$ddmMapTypeError}
						</p>
					</div>

					{* Save button *}
					<div class="options">
						<div class="buttonHolderRight">
							<a href="#" id="saveLiveData" class="submitButton button inputButton button mainButton">
								<span>{$lblSave|ucfirst}</span>
							</a>
						</div>
					</div>
				</div>
			</td>
			{/form:settings}
		</tr>
	</table>

	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!dataGrid}

<script type="text/javascript">
	var mapOptions = {
		zoom: '{$settings.zoom_level}' == 'auto' ? 0 : {$settings.zoom_level},
		type: '{$settings.map_type}',
		center: {
			lat: {$settings.center.lat},
			lng: {$settings.center.lng}
		}
	};
	var markers = [];
	{iteration:items}
		{option:items.lat}
			{option:items.lng}
				markers.push({
					lat: {$items.lat},
					lng: {$items.lng},
					title: '{$items.title}',
					text: '<p>{$items.street} {$items.number}</p><p>{$items.zip} {$items.city}</p>'
				});
			{/option:items.lng}
		{/option:items.lat}
	{/iteration:items}
</script>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}