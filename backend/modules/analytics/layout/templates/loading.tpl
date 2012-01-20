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

{option:redirect}<div id="redirect" class="hidden">{$redirect}</div>{/option:redirect}
<div id="redirectGet" class="hidden">{$redirectGet}</div>
<div id="settingsUrl" class="hidden">{$settingsUrl}</div>
<div id="page" class="hidden">{$page}</div>
<div id="identifier" class="hidden">{$identifier}</div>
<div id="statusError" class="hidden">{$msgGetDataError}</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}