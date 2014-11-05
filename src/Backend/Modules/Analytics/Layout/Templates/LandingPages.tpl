{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}


<div class="pageTitle">
	<h2>{$lblLandingPages|ucfirst}</h2>

	{option:showAnalyticsAddLandingPage}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_landing_page'}" class="button icon iconAdd" title="{$lblAddLandingPage|ucfirst}">
			<span>{$lblAddLandingPage|ucfirst}</span>
		</a>
	</div>
	{/option:showAnalyticsAddLandingPage}
</div>

<div class="box">
	{include:{$BACKEND_MODULE_PATH}/Layout/Templates/Period.tpl}

	<div class="options content">
		<form action="{$var|geturl:'mass_landing_page_action'}" method="get" class="forkForms submitWithLink" id="landing_pages">
			<div class="dataGridHolder">
				{option:dgPages}
					{$dgPages}
				{/option:dgPages}

				{option:!dgPages}
					<table class="dataGrid">
						<tr>
							<td>{$msgNoLandingPages}</td>
						</tr>
					</table>
				{/option:!dgPages}
			</div>
		</form>
	</div>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
