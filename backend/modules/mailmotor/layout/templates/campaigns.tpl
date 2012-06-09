{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblCampaigns|ucfirst}</h2>

	{option:showMailmotorAddCampaign}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_campaign'}" class="button icon iconFolderAdd" title="{$lblAddCampaign|ucfirst}">
			<span>{$lblAddCampaign|ucfirst}</span>
		</a>
	</div>
	{/option:showMailmotorAddCampaign}
</div>

<form action="{$var|geturl:'mass_campaign_action'}" method="get" class="forkForms submitWithLink" id="campaigns">
	{option:dataGrid}
		<div class="dataGridHolder">
			{$dataGrid}
		</div>
	{/option:dataGrid}
	{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}
</form>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}