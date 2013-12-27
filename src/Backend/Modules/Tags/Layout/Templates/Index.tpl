{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTags|ucfirst}</h2>
</div>

{option:dataGrid}
	<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="tagsForm">
		<div class="dataGridHolder">
			{$dataGrid}
		</div>
	</form>
{/option:dataGrid}
{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassDelete}</p>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}