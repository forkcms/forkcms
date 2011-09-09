{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$lblModules}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'module_install'}" class="button icon iconAdd" title="{$lblUploadModule|ucfirst}">
			<span>{$lblUploadModule|ucfirst}</span>
		</a>
	</div>
</div>
{option:dataGrid}
<div class="dataGridHolder">
	{$dataGrid}
</div>
{/option:dataGrid}
{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}