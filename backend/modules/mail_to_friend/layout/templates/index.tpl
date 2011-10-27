{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>
		{$lblMailToFriend|ucfirst}
	</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'export'}" class="button" title="{$lblExport|ucfirst}"><span>{$lblExport|ucfirst}</span></a>
	</div>
</div>

{option:dataGrid}
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!dataGrid}
	{$msgNoItems}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}