{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12 text-center">
		<span class="h2">{$lblLoading|ucfirst}</span>
	</div>
</div>
<div class="row fork-module-content">
	<div class="col-md-12">
		<div id="longLoader" class="text-info text-center">
			<p>{$msgLoadingData}</p>
			<p class="fork-loader lg"></p>
		</div>
		<div id="statusError" class="alert alert-danger" style="display: none;">
			<p>{$msgGetDataError}</p>
		</div>
	</div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
