{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$lblModules}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'upload_module'}" class="button icon iconImport" title="{$lblUploadModule|ucfirst}">
			<span>{$lblUploadModule|ucfirst}</span>
		</a>
		<a href="http://www.fork-cms.com/extensions" class="button icon iconNext" title="{$lblFindModules|ucfirst}">
			<span>{$lblFindModules|ucfirst}</span>
		</a>
	</div>
</div>

{option:dataGridInstalledModules}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblInstalledModules|ucfirst}</h3>
	</div>
	{$dataGridInstalledModules}
</div>
{/option:dataGridInstalledModules}
{option:!dataGridInstalledModules}<p>{$msgNoModulesInstalled}</p>{/option:!dataGridInstalledModules}

{option:dataGridInstallableModules}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblInstallableModules|ucfirst}</h3>
		</div>
		{$dataGridInstallableModules}
	</div>
{/option:dataGridInstallableModules}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}