{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPartners|ucfirst}</h2>

	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}&id={$widgetId}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{option:dgPartners}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>Partners</h3>
		</div>
		{$dgPartners}
	</div>
{/option:dgPartners}

{option:noItems}<p>{$msgNoItems}</p>{/option:noItems}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}