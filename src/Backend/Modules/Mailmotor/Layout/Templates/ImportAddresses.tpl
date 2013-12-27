{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblImportAddresses|ucfirst}</h2>
</div>

{form:import}
	<div class="box">
		<div class="heading">
			<h3>{$lblAddressList|ucfirst}</h3>
		</div>
		<div class="options">
			<p class="p0">
				{$fileCsv} {$fileCsvError}

				{option:showMailmotorImportAddresses}<label for="download">Download <a href="{$var|geturl:'import_addresses'}&amp;example=1">{$lblExampleFile}</a>.</label>{/option:showMailmotorImportAddresses}
			</p>
		</div>
	</div>
	{*
	<div class="box">
		<div class="heading">
			<h3>{$lblLanguage|ucfirst}</h3>
		</div>
		<div class="options">
			{$ddmLanguages} {$ddmLanguagesError}
		</div>
	</div>
	*}
	<div class="box">
		<div class="heading">
			<h3>{$lblGroup|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList">
				{iteration:groups}
					<li>{$groups.rbtGroups} <label for="{$groups.id}">{$groups.label|ucfirst}</label></li>
				{/iteration:groups}
			</ul>
			{option:chkGroupsError}<p class="error">{$chkGroupsError}</p>{/option:chkGroupsError}
		</div>
	</div>

	{option:showMailmotorImportAddresses}
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'import_addresses'}" class="submitButton button inputButton button mainButton"><span>{$lblImportAddresses|ucfirst}</span></a>
		</div>
	</div>
	{/option:showMailmotorImportAddresses}
{/form:import}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}