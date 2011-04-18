{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:themes}
	<div class="pageTitle">
		<h2>{$lblTemplates|ucfirst} {$lblFor} {$ddmTheme}</h2>
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add_template'}" class="button icon iconAdd" title="{$lblAddTemplate|ucfirst}">
				<span>{$lblAddTemplate|ucfirst}</span>
			</a>
		</div>
	</div>

	<div class="datagridHolder">
		{option:datagrid}{$datagrid}{/option:datagrid}
		{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}
	</div>
{/form:themes}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}