{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_start.tpl}


<div class="pageTitle">
	<h2>
		{option:dgDrafts}{$lblDrafts|ucfirst}{/option:dgDrafts}
		{option:!dgDrafts}{$lblRecentlyEdited|ucfirst}{/option:!dgDrafts}
	</h2>

    {option:showPagesAdd}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
    {/option:showPagesAdd}
</div>

{option:dgDrafts}
	<div class="dataGridHolder {option:!dgDrafts}dataGridHolderNoDataGrid{/option:!dgDrafts}">
		{$dgDrafts}
	</div>

	<div class="pageTitle">
		<h2>
			{$lblRecentlyEdited|ucfirst}
		</h2>

		{option:showPagesAdd}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}" class="button icon iconAdd">
				<span>{$lblAdd|ucfirst}</span>
			</a>
		</div>
		{/option:showPagesAdd}
	</div>

{/option:dgDrafts}


<div class="dataGridHolder {option:!dgRecentlyEdited}dataGridHolderNoDataGrid{/option:!dgRecentlyEdited}">
	{option:dgRecentlyEdited}{$dgRecentlyEdited}{/option:dgRecentlyEdited}
	{option:!dgRecentlyEdited}<p>{$msgNoItems}</p>{/option:!dgRecentlyEdited}
</div>

{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}