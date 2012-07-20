{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLoading|ucfirst}</h2>
</div>

<div id="longLoader">
	<div id="messaging">
		<div class="formMessage loadingMessage">
			<p>{$msgLoadingData}</p>
		</div>
	</div>
</div>
<div id="statusError" class="hidden">{$msgGetDataError}</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}