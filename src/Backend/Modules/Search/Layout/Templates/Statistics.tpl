{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSearch|ucfirst}: {$lblStatistics}</h2>
</div>

{option:dataGrid}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblStatistics|ucfirst}</h3>
	</div>
	{$dataGrid}
</div>
{/option:dataGrid}

{option:!dataGrid}<p>{$msgNoStatistics}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}