{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleManager|ucfirst}</h2>
</div>


{option:dgNonInstalled}
<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblNonInstalledModules|ucfirst}</h3>
	</div>
	{$dgNonInstalled}
</div>
{/option:dgNonInstalled}

{option:dgInstalledNonActive}
<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblInstalledNonActive|ucfirst}</h3>
	</div>
	{$dgInstalledNonActive}
</div>
{/option:dgInstalledNonActive}

{option:dgInstalledActive}
<div class="datagridHolder">
	<div class="tableHeading">
		<h3>{$lblInstalledActive|ucfirst}</h3>
	</div>
	{$dgInstalledActive}
</div>
{/option:dgInstalledActive}


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}