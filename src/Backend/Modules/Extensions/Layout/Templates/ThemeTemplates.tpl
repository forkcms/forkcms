{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:themes}
	<div class="pageTitle">
		<h2>
			{$lblExtensions|ucfirst}: <label for="theme">{$lblTemplates} {$lblFor}</label> {$ddmTheme}
		</h2>

		{option:showExtensionsAddThemeTemplate}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'export_theme_templates'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="button icon iconExport" title="{$lblExport|ucfirst}">
				<span>{$lblExport|ucfirst}</span>
			</a>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
