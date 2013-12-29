{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}


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
	{$dataGrid}
</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoSynonyms|sprintf:{$var|geturl:'add_synonym'}}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
