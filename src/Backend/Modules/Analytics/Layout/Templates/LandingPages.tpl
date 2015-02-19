{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12">
		<h2>
			{$lblLandingPages|ucfirst}
		</h2>
		<div class="btn-toolbar pull-right">
			<div class="btn-group" role="group">
				{option:showAnalyticsAddLandingPage}
				<a href="{$var|geturl:'add_landing_page'}" class="btn btn-primary" title="{$lblAddLandingPage|ucfirst}">
					<span class="glyphicon glyphicon-plus"></span>&nbsp;
					{$lblAddLandingPage|ucfirst}
				</a>
				{/option:showAnalyticsAddLandingPage}
			</div>
		</div>
	</div>
</div>
<div class="row fork-module-content">
	<div class="col-md-12">
		{include:{$BACKEND_MODULE_PATH}/Layout/Templates/Period.tpl}
	</div>
</div>
<div class="row fork-module-content">
	<div class="col-md-12">
		<form action="{$var|geturl:'mass_landing_page_action'}" method="get" class="forkForms submitWithLink" id="landing_pages">
			{option:dgPages}
			{$dgPages}
			{/option:dgPages}
			{option:!dgPages}
			<p>{$msgNoLandingPages}</p>
			{/option:!dgPages}
		</form>
	</div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
