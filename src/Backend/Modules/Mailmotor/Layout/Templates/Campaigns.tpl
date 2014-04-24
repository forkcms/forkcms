{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
