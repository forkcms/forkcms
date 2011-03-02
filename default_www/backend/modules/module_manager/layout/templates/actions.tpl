{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}
<div class="pageTitle">
	<h2>{$lblActionsForModule|sprintf:{$item.name}|ucfirst}</h2>
	<div class="buttonHolderRight">
			<a class="button icon iconAdd" href="{$var|geturl:'add_action'}&amp;module={$item.name}"><span>{$lblAddAction}</span></a>
		</div>
</div>
{option:datagrid}
	<div class="datagridHolder">
		
		{$datagrid}
	</div>
{/option:datagrid}

{option:!datagrid}
	<p>{$msgNoActions|sprintf:{$var|geturl:'add_action'}}</p>
{/option:!datagrid}


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
