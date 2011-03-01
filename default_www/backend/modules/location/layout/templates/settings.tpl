{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblLocation}</h2>
</div>

{form:settings}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblGroupMap|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="zoom_level">{$lblZoomLevel|ucfirst}</label>
				{$ddmZoomLevel} {$ddmZoomLevelError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="width">{$lblWidth|ucfirst}</label>
				{$txtWidth} {$txtWidthError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="height">{$lblHeight|ucfirst}</label>
				{$txtHeight} {$txtHeightError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="map_type">{$lblMapType|ucfirst}</label>
				{$ddmMapType} {$ddmMapTypeError}
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblIndividualMap|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="zoom_level_widget">{$lblZoomLevel|ucfirst}</label>
				{$ddmZoomLevelWidget} {$ddmZoomLevelWidgetError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="width_widget">{$lblWidth|ucfirst}</label>
				{$txtWidthWidget} {$txtWidthWidgetError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="height_widget">{$lblHeight|ucfirst}</label>
				{$txtHeightWidget} {$txtHeightWidgetError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="map_type_widget">{$lblMapType|ucfirst}</label>
				{$ddmMapTypeWidget} {$ddmMapTypeWidgetError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}