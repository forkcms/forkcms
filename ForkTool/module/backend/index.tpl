{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>
		{$lbl|ucfirst}
	</h2>
	<div class="buttonHolderRight">
		{option:filterCategory}<a href="{$var|geturl:'add':null:'&category={$filterCategory.id}'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">{/option:filterCategory}
		{option:!filterCategory}<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">{/option:!filterCategory}
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}