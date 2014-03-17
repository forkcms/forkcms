{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPartnerWidgets|ucfirst}</h2>

	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_widget'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{option:dgWidgets}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblPartnerWidgets|ucfirst}</h3>
		</div>
		{$dgWidgets}
	</div>
{/option:dgWidgets}

{option:noItems}<p>{$msgNoItems}</p>{/option:noItems}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}