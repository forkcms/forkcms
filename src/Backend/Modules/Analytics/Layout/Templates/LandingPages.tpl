{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

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
	{include:{$BACKEND_MODULE_PATH}/layout/templates/period.tpl}

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

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}