{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}


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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
