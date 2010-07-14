{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblSearch|ucfirst}: {$lblSynonyms}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_synonym'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{option:datagrid}
<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblSynonyms|ucfirst}</h3>
	</div>
	{$datagrid}
</div>
{/option:datagrid}

{option:!datagrid}<p>{$msgNoSynonyms|sprintf:{$var|geturl:'add'}}</p>{/option:!datagrid}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}