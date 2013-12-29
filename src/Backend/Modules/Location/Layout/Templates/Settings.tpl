{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblLocation}</h2>
</div>

{form:settings}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblIndividualMap|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="zoomLevelWidget">{$lblZoomLevel|ucfirst}</label>
				{$ddmZoomLevelWidget} {$ddmZoomLevelWidgetError}
			</p>
		</div>
		<div class="options"{option:!godUser} style="display:none;"{/option:!godUser}>
			<p>
				<label for="widthWidget">{$lblWidth|ucfirst}</label>
				{$txtWidthWidget} {$txtWidthWidgetError}
				<span class="helpTxt">
					{$msgWidthHelp|sprintf:300:800}
				</span>
			</p>
		</div>
		<div class="options"{option:!godUser} style="display:none;"{/option:!godUser}>
			<p>
				<label for="heightWidget">{$lblHeight|ucfirst}</label>
				{$txtHeightWidget} {$txtHeightWidgetError}
				<span class="helpTxt">
					{$msgHeightHelp|sprintf:150}
				</span>
			</p>
		</div>
		<div class="options">
			<p>
				<label for="mapTypeWidget">{$lblMapType|ucfirst}</label>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
