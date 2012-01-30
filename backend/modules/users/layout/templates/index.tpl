{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}</h2>

	{option:showUsersAdd}
	<div class="buttonHolderRight">
		<a class="button icon iconAdd" href="{$var|geturl:'add'}"><span>{$lblAdd|ucfirst}</span></a>
	</div>
	{/option:showUsersAdd}
</div>
<div class="dataGridHolder">
	{option:dataGrid}{$dataGrid}{/option:dataGrid}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}