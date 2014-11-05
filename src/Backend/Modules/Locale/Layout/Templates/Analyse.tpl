{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>

	{option:showLocaleExportAnalyse}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'ExportAnalyse'}&amp;language={$language}" class="button icon iconExport"><span>{$lblExport|ucfirst}</span></a>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
