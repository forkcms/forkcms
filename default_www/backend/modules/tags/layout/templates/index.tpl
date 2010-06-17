{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblTags|ucfirst}</h2>
</div>

{option:datagrid}
	<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="tagsForm">
		<div class="datagridHolder">
			{$datagrid}
		</div>
	</form>
{/option:datagrid}
{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/foot.tpl'}