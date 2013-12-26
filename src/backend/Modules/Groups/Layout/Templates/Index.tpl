{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}</h2>

	{option:showGroupsAdd}
	<div class="buttonHolderRight">
		<a class="button icon iconAdd" href="{$var|geturl:'add'}"><span>{$lblAddGroup|ucfirst}</span></a>
	</div>
	{/option:showGroupsAdd}
</div>
<div class="dataGridHolder">
	{option:dataGrid}{$dataGrid}{/option:dataGrid}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}