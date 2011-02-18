{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGeneralSettings|ucfirst}</h2>
</div>

{form:settingsThemes}
	<div class="box">
		<div class="heading">
			<h3>{$lblThemes|ucfirst}</h3>
		</div>
		<div class="content">
			<p>{$msgHelpThemes}</p>
			{$ddmTheme} {$ddmThemeError}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settingsThemes}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}