{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_start.tpl}

<div class="pageTitle">
	<h2>{$lblRecentlyEdited|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

<div class="datagridHolder {option:!datagrid}datagridHolderNoDatagrid{/option:!datagrid}">
	{option:datagrid}{$datagrid}{/option:datagrid}
	{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}
</div>

{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}