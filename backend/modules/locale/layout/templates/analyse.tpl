{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>

	{option:showLocaleExportAnalyse}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'export_analyse'}&amp;language={$language}" class="button icon iconExport"><span>{$lblExport|ucfirst}</span></a>
	</div>
	{/option:showLocaleExportAnalyse}
</div>

{option:dgFrontend}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblFrontend|ucfirst}</h3>
	</div>
	{$dgFrontend}
</div>
{/option:dgFrontend}

{option:!dgFrontend}
<h3>{$lblFrontend|ucfirst}</h3>
<p>{$msgNoItemsAnalyse}</p>
{/option:!dgFrontend}


{option:dgBackend}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblBackend|ucfirst}</h3>
	</div>
	{$dgBackend}
</div>
{/option:dgBackend}

{option:!dgBackend}
<h3>{$lblBackend|ucfirst}</h3>
<p>{$msgNoItemsAnalyse}</p>
{/option:!dgBackend}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}