{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSearch|ucfirst}: {$lblSynonyms}</h2>

	{option:showSearchAddSynonym}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_synonym'}" class="button icon iconAdd" title="{$lblAddSynonym|ucfirst}">
			<span>{$lblAddSynonym|ucfirst}</span>
		</a>
	</div>
	{/option:showSearchAddSynonym}
</div>

{option:dataGrid}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblSynonyms|ucfirst}</h3>
	</div>
	{$dataGrid}
</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoSynonyms|sprintf:{$var|geturl:'add_synonym'}}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}