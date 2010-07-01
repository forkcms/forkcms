{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>
</div>

<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblFrontend|ucfirst}</h3>
	</div>
	{option:dgFrontend}{$dgFrontend}{/option:dgFrontend}
	{option:!dgFrontend}<p>{$msgLocaleNoItemsAnalyse}</p>{/option:!dgFrontend}
</div>

<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblBackend|ucfirst}</h3>
	</div>
	{option:dgBackend}{$dgBackend}{/option:dgBackend}
	{option:!dgBackend}<p>{$msgLocaleNoItemsAnalyse}</p>{/option:!dgBackend}
</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}