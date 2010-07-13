{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblSearch}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="overviewNumItems">{$lblItemsPerPage|ucfirst}</label>
			{$ddmOverviewNumItems} {$ddmOverviewNumItemsError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblModules|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList p0" id="searchModules">
				{iteration:modules}
					<li>{$modules.chk} <label for="{$modules.id}">{$modules.label}</label> {$modules.txt} {option:modules.txtError}<span class="formError">{$modules.txtError}</span>{/option:modules.txtError}</li>
				{/iteration:modules}
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}