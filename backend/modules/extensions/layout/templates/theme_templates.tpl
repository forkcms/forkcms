{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:themes}
	<div class="pageTitle">
		<h2>
			{$lblExtensions|ucfirst}: <label for="theme">{$lblTemplates} {$lblFor}</label> {$ddmTheme}
		</h2>

		{option:showExtensionsAddThemeTemplate}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add_theme_template'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="button icon iconAdd" title="{$lblAddTemplate|ucfirst}">
				<span>{$lblAddTemplate|ucfirst}</span>
			</a>
		</div>
		{/option:showExtensionsAddThemeTemplate}
	</div>

	<div class="dataGridHolder">
		{option:dataGrid}{$dataGrid}{/option:dataGrid}
		{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}
	</div>
{/form:themes}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
