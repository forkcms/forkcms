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
	<div class="box">
		<div class="heading">
			<h3>{$lblMap|ucfirst}</h3>
		</div>
		<div class="options">
			{option:items}
				<div id="map" style="height: {$settings.height}px; width: 100%;">
				</div>
			{/option:items}
		</div>
	</div>

	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!dataGrid}

<script type="text/javascript">
	var mapOptions = {
		zoom: '{$settings.zoom_level}',
		type: '{$settings.map_type}',
		center: {
			lat: {$items.0.lat},
			lng: {$items.0.lng}
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
					text: '{$items.text|stripnewlines}'
				});
			{/option:items.lng}
		{/option:items.lat}
	{/iteration:items}
</script>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}